<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostDestroyRequest;
use App\Http\Requests\PostIndexRequest;
use App\Http\Requests\PostShowRequest;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PostIndexRequest $request): AnonymousResourceCollection
    {
        $per_page = $request->query('per_page') ?: 15;

        $posts = Post::query();

        // Check if is filtered by text
        if ($text = $request->query('text')) {
            $posts->where(function ($query) use ($text) {
                $query->where('title', 'like', '%' . $text . '%')
                    ->orWhere('text', 'like', '%' . $text . '%');
            });
        }

        // Filter by trashed
        if ($request->has('trashed')) {
            switch ($request->query('trashed')) {
                case 'with':
                    $posts->withTrashed();
                    break;
                case 'only':
                    $posts->onlyTrashed();
                    break;
                default:
                    $posts->withTrashed();
            }
        }

        $posts = $posts->paginate((int)$per_page);

        // Include relationship
        if ($request->has('with')) {
            $posts->load($request->query('with'));
        }

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostStoreRequest $request): PostResource
    {
        DB::beginTransaction();

        try {
            $post = new User();
            $post->title = $request->title;
            $post->text = $request->text;
            $post->save();


            DB::commit();
        } catch (Exception $exception) {

            DB::rollBack();
            throw $exception;
        }
        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PostShowRequest $request, Post $post): PostResource
    {
         // Include relationship
         if ($request->query('with')) {
            $post->load($request->query('with'));
        }

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostUpdateRequest $request, Post $post): PostResource
    {
        DB::beginTransaction();

        try {

            $post->update($request->only(['title','text']));


            DB::commit();
        } catch (Exception $exception) {

            DB::rollBack();
            throw $exception;
        }

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PostDestroyRequest $request, Post $post): Response

    {
    $post->delete();

        return response(null, 204);
    }
}
