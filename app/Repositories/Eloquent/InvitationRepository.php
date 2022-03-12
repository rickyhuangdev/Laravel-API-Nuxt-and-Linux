<?php

namespace App\Repositories\Eloquent;


use App\Models\Invitation;
use App\Repositories\Contracts\IInvitation;

class InvitationRepository extends BaseRepository implements IInvitation
{
    public function model()
    {
        return Invitation::class;
    }

    public function addUserToTeam($team, $use_id)
    {
        $team->members()->attach($use_id);
    }

    public function removeUserFromTeam($team, $use_id)
    {
        $team->members()->detach($use_id);
    }
}
