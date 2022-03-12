<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IInvitation;
use Illuminate\Http\Request;

class InvitationsController extends Controller
{
    //
    protected $invitations;

    public function __construct(IInvitation $invitations)
    {
        $this->invitations = $invitations;
    }

    public function invite(Request $request, $teamId)
    {

    }

    public function resend($id)
    {

    }

    public function respond(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}
