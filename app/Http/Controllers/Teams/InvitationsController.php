<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Mail\SendInvitationToJoinTeam;
use App\Models\Team;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class InvitationsController extends Controller
{
    //
    protected $invitations;
    protected $teams;
    protected $user;

    public function __construct(IInvitation $invitations, ITeam $teams, IUser $user)
    {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->user = $user;
    }

    public function invite(Request $request, $teamId)
    {
        //get the team
        $team = $this->teams->find($teamId);
        $this->validate($request, [
            'email' => ['required', 'email']
        ]);
        $user = auth()->user();
        //check if the user owns the team
        if (!$user->isOwnerOfTeam($team)) {
            return response()->json(['email' => 'Your are not the team owner'], 401);
        }
        //check if the email has a pending invitation
        if ($team->hasPendingInvite($request->email)) {
            return response()->json(['email' => 'Email already has a pending invited'], 422);
        }
        //get the recipient
        $recipient = $this->user->findByEmail($request->email);
        // if the recipient does not exist
        if (!$recipient) {
            $this->createInvitation(false, $team, $request->email);
            return response()->json(['message' => 'Invitation sent to user'], 200);
        }
        //check if the team already has the user
        if ($team->hasUser($recipient)) {
            return response()->json(['email' => 'This user seems to be a team member'], 422);
        }
        //send invitation to the use
        $this->createInvitation(true, $team, $request->email);
        return response()->json(['message' => 'Invitation sent to user'], 200);


    }

    public function resend($id)
    {
        //check if the user owns the team
        $invitation = $this->invitations->find($id);
//        if (!auth()->user()->isOwnerOfTeam($invitation->team)) {
//            return response()->json(['email' => 'Your are not the team owner'], 401);
//        }
        $this->authorize('resend', $invitation);
        $recipient = $this->user->findByEmail($invitation->recipient_email);
        Mail::to($recipient->email)->send(new SendInvitationToJoinTeam($invitation, !is_null($recipient)));
        return response()->json(['message' => 'Invitation resent to user'], 200);

    }

    public function respond(Request $request, $id)
    {
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required'],
        ]);
        $token = $request->token;
        $decision = $request->decision;
        $invitation = $this->invitations->find($id);
        // check if the invitation belongs to the user
//        if ($invitation->recipient_email !== auth()->user()->email) {
//            return response()->json(['message' => 'This is not your invitation'], 401);
//
//        }
        $this->authorize('respond', $invitation);
        // check to make sure that the token match
        if ($invitation->token !== $token) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }
        // check if accept
        if ($decision !== 'deny') {
            $this->invitations->addUserToTeam($invitation->team, auth()->id());
        }
        $invitation->delete();
        return response()->json(['message' => 'Successful Join the Team'], 200);

    }

    public function destroy($id)
    {
        $invitation = $this->invitations->find($id);
        $this->authorize('delete', $invitation);
        $invitation->delete();
        return response()->json(['message' => 'Deleted Successful'], 200);
    }

    protected function createInvitation(bool $user_exists, Team $team, string $email)
    {
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => Hash::make(uniqid(microtime()))
        ]);
        Mail::to($email)->send(new SendInvitationToJoinTeam($invitation, $user_exists));
    }
}
