<?php

namespace App\Repositories\Contracts;

interface IInvitation
{
    public function addUserToTeam($team, $use_id);

    public function removeUserFromTeam($team, $use_id);
}
