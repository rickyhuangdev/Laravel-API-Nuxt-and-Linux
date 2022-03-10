<?php

namespace App\Jobs;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\File;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Design
     */
    protected $design;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        //
        $this->design = $design;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $disk = $this->design->disk;
        $filename = $this->design->image;
        $original_file = storage_path() . '/uploads/original/' . $filename;
        try {
            Image::make($original_file)->fit(800, 600, function ($constraint) {
                $constraint->upsize();
            })->save($large = storage_path('uploads/large/' . $filename));
            //thumbnail
            Image::make($original_file)->fit(250, 200, function ($constraint) {
                $constraint->upsize();
            })->save($thumbnail = storage_path('uploads/thumbnail/' . $filename));

            //store images to permanent disk
            if (Storage::disk($disk)->put('uploads/designs/original/' . $filename, fopen($original_file, 'r+'))) {
                unlink($original_file);
            };
            if (Storage::disk($disk)->put('uploads/designs/large/' . $filename, fopen($large, 'r+'))) {
                unlink($large);
            };
            if (Storage::disk($disk)->put('uploads/designs/thumbnail/' . $filename, fopen($thumbnail, 'r+'))) {
                unlink($thumbnail);
            };
            //update database
            $this->design->update([
                'upload_successfully' => true
            ]);
        } catch (\Exception $exception) {
            \Illuminate\Log\Logger::error($exception->getMessage());

        }
    }
}
