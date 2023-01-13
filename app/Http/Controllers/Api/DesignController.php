<?php

namespace App\Http\Controllers\Api;

use App\Models\Design;
use App\Jobs\UploadImage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignRessource;
use Illuminate\Support\Facades\Storage;

class DesignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => ['required','mimes:jpg,jpeg,png,bmp,gif,webp','max:51200']
        ]);

        $image = $request->file('image');
        $img_path = $image->getPathname();

        $filename = time() . "_" . preg_replace('/\s+/','_',strtolower($image->getClientOriginalName()));

        // move image to tmp folder
        $tmp = $image->storeAs('uploads/original',$filename,'tmp');

        $design = auth()->user()->designs()->create([
            'image' => $filename,
            'disk' => config('site.upload_disk')
        ]);

        // dispatch image manuilation job
        $this->dispatch(new UploadImage($design));

        return new DesignRessource($design);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Design  $design
     * @return \Illuminate\Http\Response
     */
    public function show(Design $design)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Design  $design
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Design $design)
    {
        $this->authorize('update',$design);
        $this->validate($request, [
            "title" => ["required","unique:designs,title,".$design->id],
            "description" => ['required',"string","min:20","max:255"]
        ]);
        $design->update([
            "title" => $request->title,
            "slug" => Str::slug($request->title),
            "description" => $request->description,
            "is_live" => $design->upload_successful ? ($request->is_live ?? false) : false
        ]);

        return new DesignRessource($design);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Design  $design
     * @return \Illuminate\Http\Response
     */
    public function destroy(Design $design)
    {
        $this->authorize('delete',$design);

        foreach(['thumbnail','large','original'] as $size) {
            if(Storage::disk($design->disk)->exists("/uploads/designs/{$size}/".$design->image)) {
                Storage::disk($design->disk)->delete("/uploads/designs/{$size}/".$design->image);
            }
        }

        $design->delete();
    }
}
