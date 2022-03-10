<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    //
    public function upload(Request $request)
    {
        $this->validate($request, [
            'image' => ['required', 'mimes:jpeg,gif,bmp,png', 'max:2048']
        ]);
        //get the images
        $image = $request->file('image');
        $filename = $image->hashName();
        // move the image to the temporary
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');
        // create the database record for the design
        $design = auth()->user()->designs()->create([
            'image' => $filename,
            'disk' => config('site.upload_disk')
        ]);
        //dispatch a job to handle the image manipulation
        $this->dispatch(new UploadImage($design));
        return response()->json($design, 200);
    }
}
