<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    //
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,' . $id],
            'description' => ['required', 'string', 'min:15', 'max:250'],
        ]);
        $design = Design::findOrFail($id);
        $design->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$design->upload_successfully ? false : $request->is_live
        ]);
        return response()->json($design, 200);

    }
}
