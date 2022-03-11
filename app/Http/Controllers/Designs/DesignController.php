<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Repositories\Contracts\IDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    //
    protected $designs;

    public function __construct(IDesign $designs)
    {
        $this->designs = $designs;
    }

    public function index()
    {
        $designs = $this->designs->all();
        return DesignResource::collection($designs);
    }

    public function findDesign($id)
    {
        $design = $this->designs->find($id);
        return new DesignResource($design);
    }

    public function update(Request $request, $id)
    {
        $design = $this->designs->find($id);
        $this->authorize('update', $design);
        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,' . $id],
            'description' => ['required', 'string', 'min:15', 'max:250'],
            'tags' => ['required']
        ]);

        $this->designs->update($id, [
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$design->upload_successfully ? false : $request->is_live
        ]);
        //apply the tag
        $this->designs->applyTags($id, $request->tags);
        return new DesignResource($design);

    }

    public function destroy(Request $request, $id)
    {
        $design = $this->designs->find($id);
        $this->authorize('update', $design);
        //delete file
        foreach (['thumbnail', 'large', 'original'] as $size) {
            if (Storage::disk($design->disk)->exists("uploads/designs/${size}/" . $design->image)) {
                Storage::disk($design->disk)->delete("uploads/designs/${size}/" . $design->image);
            }
        }
        $this->designs->delete();

        return response()->json(['message' => 'Record Successful deleted'], 200);
    }
}
