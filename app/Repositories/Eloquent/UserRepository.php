<?php

namespace App\Repositories\Eloquent;

use App\Jobs\UploadImage;
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
        if ($request->keywords) {
            $query->where(function ($q) use ($request) {
                $q->where('tagline', 'like', '%' . $request->keywords . '%')
                    ->orWhere('about', 'like', '%' . $request->keywords . '%');
            });
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
        if ($request->orderBy === 'closest') {
            $query->orderByDistanceSphere('location', $point, 'asc');
        } else if ($request->orderBy === 'latest') {
            $query->latest();
        } else {
            $query->oldest();
        }

        return $query->get();
    }

    public function uploadUserImage(Request $request)
    {
        $this->validate($request, [
            'image' => ['required', 'mimes:jpeg,gif,bmp,png', 'max:200']
        ]);
        //get the images
        $image = $request->file('image');
        $filename = $image->hashName();
        // move the image to the temporary
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');
        // create the database record for the design
        $user = auth()->user()->update([
            'image' => $filename,
        ]);
        //dispatch a job to handle the image manipulation
        $this->dispatch(new UploadImage($user));
        return $user;
    }

}
