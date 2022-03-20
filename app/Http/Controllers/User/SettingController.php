<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Jobs\UploadUserImage;
use App\Rules\CheckSamePassword;
use App\Rules\MatchOldPassword;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    //
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'tagline' => ['required'],
            'name' => ['required','min:5','max:12'],
            'about' => ['required', 'string', 'min:20'],
            'formatted_address' => ['required'],
            'location.latitude' => ['required', 'between:-90,90'],
            'location.longitude' => ['required', 'between:-180,180'],
        ]);
        $location = new Point($request->location['latitude'], $request->location['longitude']);

        $user->update([
            'tagline' => $request->tagline,
            'name' => $request->name,
            'about' => $request->about,
            'formatted_address' => $request->formatted_address,
            'available_to_hire' => $request->available_to_hire,
            'location' => $location,

        ]);

        return new UserResource($user);

    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => ['required', new MatchOldPassword],
            'password' => ['required', 'confirmed', 'min:6', new CheckSamePassword]
        ]);
        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'password updated'
        ]);
    }

    public function uploadImage(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'image' => 'required|mimes:jpg,jpeg,png|max:300'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();
            // move the image to the temporary
            $image->storeAs('uploads/user/original', $imageName, 'tmp');
            $user->image = $imageName;
            $user->save();
            $this->dispatch(new UploadUserImage($user));
            return response()->json(['success' => true], 200);
        }

    }
}
