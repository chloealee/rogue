<?php

namespace Rogue\Http\Controllers;

use Rogue\Http\Requests\TagsRequest;
use Rogue\Repositories\PostRepository;

class TagsController extends Controller
{
    /**
     * The post service instance.
     *
     * @var Rogue\Repositories\PostRepository
     */
    protected $post;

    /**
     * Create a controller instance.
     *
     * @param PostContract $posts
     * @return void
     */
    public function __construct(PostRepository $post)
    {
        $this->middleware('auth');
        $this->middleware('role:admin,staff');

        $this->post = $post;
    }

    /**
     * Add or soft delete a tag to a post when reviewed.
     *
     * @param Rogue\Http\Requests\TagsRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(TagsRequest $request)
    {
        $tagData = $request->all();
        $taggedPost = $this->post->tag($tagData);

        if (! $taggedPost) {
            response()->json(['error' => 'Tag was not successfully created/deleted.'], 500);
        }

        return;
    }
}