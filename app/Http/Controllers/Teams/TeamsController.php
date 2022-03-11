<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ITeam;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    //
    protected $teams;

    public function __construct(ITeam $teams)
    {
        $this->teams = $teams;
    }

    public function index(Request $request)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function store(Request $request, $id)
    {

    }

    public function findById($id)
    {

    }

    public function fetchUserTeams()
    {

    }

    public function destroy($id)
    {

    }

    public function findBySlug($slug)
    {

    }
}
