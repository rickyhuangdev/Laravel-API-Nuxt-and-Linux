<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\IUser;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class UserRepository extends BaseRepository implements IUser
{
    public function model()
    {
        return User::class;
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();
        //only designer who has the design
        if ($request->has_designs) {
            $query->has('designs');
        }
        //check available to hire
        if ($request->available_to_hire) {
            $query->where('available_to_hire', true);
        }
        //geographic search
        $lat = $request->latitude;
        $lng = $request->longitude;
        $dist = $request->distance;
        $unit = $request->unit;
        if ($lat && $lng) {
            $point = new Point($lat, $lng);
            $dist = $dist * 1000;
            $unit === 'km' ? $dist *= 1000 : $dist *= 1609.34;
            $query->distanceSphereExcludingSelf('location', $point, $dist);
        }
        //order the result
        if ($request->orderByLatest) {
            $query->latest();
        } else {
            $query->oldest();
        }

        return $query->get();
    }

}
