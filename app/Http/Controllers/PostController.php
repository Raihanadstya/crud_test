<?php

namespace App\Http\Controllers;

use App\Helper\HelperResponse;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class PostController extends Controller
{
    use HelperResponse;

    public function addPost(Request $request)
    {
        $user = Auth::user();
        $array = [
            'title' => 'required',
            'body' => 'required',
        ];
        $validator = Validator::make($request->all(), $array);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors());
        }
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $user->id,
        ]);

        return $this->successResponse(
            $post,
            'post created successfully',
            201
        );
    }
    public function getUserPostbyAuth()
    {
        $user = Auth::user();
        $post = Post::where('user_id', $user->id)->get();

        return $this->successResponse(
            $post,
            'post created successfully',
            201
        );
    }

    public function getUserPostbyId($id)
    {
        $user = User::where('id', $id)->first();
        $post = $user->posts;


        return $this->successResponse(
            $post,
            'post created successfully',
            201
        );
    }

    public function getPost(Request $request)
    {
        $search = $request->query('search');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        $query = Post::query();
        if ($search) {
            $query->where('title', 'LIKE', '%' . $search . '%');
        }

        $posts = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Posts retrieved successfully',
            'data' => $posts->items(),
            'pagination' => [
                'total' => $posts->total(),
                'count' => $posts->count(),
                'per_page' => $posts->perPage(),
                'current_page' => $posts->currentPage(),
                'total_pages' => $posts->lastPage(),
            ],
        ], 200);
    }
}
