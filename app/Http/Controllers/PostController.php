<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// panggil namespace yang dibutuhkan
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    public function index(){
        $posts = Post::all();
        return response()->json([
            'data' => $posts
        ]);
    }

    public function show($id){
        $post = Post::find($id);

        if ($post){
            return response()->json([
                'post' => $post
            ]);
        }

        return response()->json([
            'message' => 'Post not found'
        ], 404);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $post = new Post;
        $post->title = $request->title;
        $post->content = $request->content;
        $post->user_id = auth()->user()->id;
        $post->save();

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);

    }

    public function update(Request $request, $id){
        // validate incoming request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // find post by id
        $post = Post::find($id);

        // check if post exists
        if(!$post){
            return response()->json(['message' => 'Post not found'], 404);
        }

        // update post
        $post->title = $request->title;
        $post->content = $request->content;
        $post->save();

        // return update post
        return response()->json($post, 200);
    }

    public function destroy($id){
        // find post by id
        $post = Post::find($id);

        // check if post exists
        if(!$post){
            return response()->json(['message' => 'Post not found'], 404);
        }

        // delete post
        $post->delete();

        // return message
        return response()
            ->json(['message' => 'Post deleted successfully'], 200);
    }

}
