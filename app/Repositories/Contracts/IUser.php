<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface IUser
{
    public function findByEmail($email);

    public function search(Request $request);

    public function uploadUserImage(Request $request);
}
