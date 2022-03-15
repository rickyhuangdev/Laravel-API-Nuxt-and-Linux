<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Repositories\Contracts\IDesign;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\LatestFirst;
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

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $designs = $this->designs->withCriteria([new LatestFirst(), new IsLive(), new EagerLoad(['user', 'comments'])])->all();
        return DesignResource::collection($designs);
    }

    public function findDesign($id)
    {
        $design = $this->designs->find($id);
        return new DesignResource($design);
    }

    public function update(Request $request, $id): DesignResource
    {
        $design = $this->designs->find($id);;
        $this->authorize('update', $design);
        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,' . $id],
            'description' => ['required', 'string', 'min:15', 'max:250'],
            'tags' => ['required'],
            'team' => ['required_if:assign_to_team,true'],
        ]);

        $this->designs->update($id, [
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$design->upload_successfully ? false : $request->is_live,
            'team_id' => $request->team,
        ]);
        //apply the tag
        $this->designs->applyTags($id, $request->tags);
        return new DesignResource($design);

    }

    public function destroy(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $design = $this->designs->find($id);
        $this->authorize('update', $design);
        //delete file
        foreach (['thumbnail', 'large', 'original'] as $size) {
            if (Storage::disk($design->disk)->exists("uploads/designs/${size}/" . $design->image)) {
                Storage::disk($design->disk)->delete("uploads/designs/${size}/" . $design->image);
            }
        }
        $this->designs->delete($id);

        return response()->json(['message' => 'Record Successful deleted'], 200);
    }

    public function like($id): \Illuminate\Http\JsonResponse
    {
        $this->designs->like($id);
        return response()->json(['message' => 'Successful']);
    }

    public function checkIfUserHasLiked($designId): \Illuminate\Http\JsonResponse
    {
        $isLiked = $this->designs->isLikedByUser($designId);
        return response()->json(['liked' => $isLiked]);
    }


    public function search(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $designs = $this->designs->search($request);
        return DesignResource::collection($designs);
    }

    public function findBySlug($slug): DesignResource
    {
        $design = $this->designs->withCriteria([new IsLive()])->findWhereFirst('slug', $slug);
        return new DesignResource($design);
    }

    public function getForTeam($teamId): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $designs = $this->designs->withCriteria([new IsLive()])->findWhere('team_id', $teamId);
        return DesignResource::collection($designs);
    }

    public function getForUser($userId): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $designs = $this->designs->withCriteria([new IsLive()])->findWhere('user_id', $userId);
        return DesignResource::collection($designs);
    }

    public function userOwnsDesign($id)
    {
            $design = $this->designs->withCriteria(
               [ new ForUser(auth()->id())]
            )->findWhereFirst('id',$id);

            return new DesignResource($design);
    }
}
