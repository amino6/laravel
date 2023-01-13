<?php

namespace App\Jobs;

use Image;
use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $design;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = $this->design->disk;
        $filename = $this->design->image;
        $original_img = storage_path() . '/app/tmp/uploads/original/' . $filename;

        // large image
        $this->resize($original_img, 800, 600)
            ->save($large = storage_path('app/tmp/uploads/large/' . $filename));
        // thumbnail image
        $this->resize($original_img, 250, 200)
            ->save($thumbnail = storage_path('app/tmp/uploads/thumbnail/' . $filename));

        // move images from tmp folder
        if($this->move_img_to($disk,'uploads/designs/original/'.$filename,$original_img)) {
            Storage::disk('tmp')->delete("/uploads/designs/original/".$filename);
        }
        if($this->move_img_to($disk,'uploads/designs/large/'.$filename,$large)) {
            Storage::disk('tmp')->delete("/uploads/designs/large/".$filename);
        }
        if($this->move_img_to($disk,'uploads/designs/thumbnail/'.$filename,$thumbnail)) {
            Storage::disk('tmp')->delete("/uploads/designs/thumbnail/".$filename);
        }

        $this->design->update([
            'upload_successful' => true
        ]);
    }

    private function resize($image, $width, $height = null)
    {
        return Image::make($image)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
    }

    private function move_img_to($disk, $path, $img)
    {
        return Storage::disk($disk)->put($path,fopen($img,"r+"));
    }
}
