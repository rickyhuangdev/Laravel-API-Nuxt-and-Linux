<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadUserImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        //
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $filename = $this->user->image;
        $original_file = storage_path() . '/uploads/user/original/' . $filename;

        try {
            Image::make($original_file)->fit(80, 80, function ($constraint) {
                $constraint->upsize();
            })->save($imagPath = storage_path('uploads/user/original/' . $filename));

           $result =  Storage::disk(config('site.upload_disk'))->put('uploads/user/original/' . $filename, fopen($imagPath, 'r+'));
            if ($result) {
                unlink($imagPath);
            };
        } catch (\Exception $exception) {
            log::error($exception->getMessage());

        }
    }
}
