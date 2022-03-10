<?php

namespace App\Jobs;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;
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
        $original_file = storage_path().'/uploads/original/'.$this->design->image;
        try{
            Image::make($original_file)->fit(800,600,function ($constraint){
                $constraint->aspectRation();
            })->save($large = storage_path('uploads/large/'.$this->design->image));
            //thumbnail
            Image::make($original_file)->fit(250,200,function ($constraint){
                $constraint->aspectRation();
            })->save($large = storage_path('uploads/thumbnail/'.$this->design->image));

        }catch (\Exception $exception){
            \Illuminate\Log\Logger::error($exception->getMessage());

        }
    }
}
