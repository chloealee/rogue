<?php

namespace Rogue\Http\Controllers\Three;

use Rogue\Models\Signup;
use Illuminate\Http\Request;
use Rogue\Http\Controllers\Api\ApiController;
use Rogue\Http\Transformers\Three\SignupTransformer;
use Rogue\Http\Controllers\Traits\TransformsRequests;

class SignupsController extends ApiController
{
    use TransformsRequests;

    /**
     * @var \League\Fractal\TransformerAbstract;
     */
    protected $transformer;

    /**
     * Create a controller instance.
     *
     * @param  PostContract  $posts
     * @return void
     */
    public function __construct()
    {
        $this->transformer = new SignupTransformer;

        $this->middleware('auth.api');
    }

    /**
     * Returns signups.
     * GET /signups
     *
     * @param Request $request
     * @return ]Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->newQuery(Signup::class);

        return $this->paginatedCollection($query, $request);
    }

    /**
     * Returns a specific signup.
     * GET /signups/:id
     *
     * @param Request $request
     * @param Signup $signup
     * @return Illuminate\Http\Response
     */
    public function show(Request $request, Signup $signup)
    {
        return $this->item($signup, 200, [], $this->transformer, $request->query('include'));
    }

    /**
     * Delete a signup.
     * DELETE /signups/:id
     *
     * @param  \Rogue\Models\Signup $signup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Signup $signup)
    {
        $signup->delete();

        return $this->respond('Signup deleted.', 200);
    }
}