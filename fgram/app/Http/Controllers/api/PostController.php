<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostShowReq;
use App\Http\Requests\ReqPost;
use App\Models\PostAttachments;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function post(ReqPost $request) {
        $user = Auth::user();
        $post = $request->all();


        $post_id = Posts::create([
            'caption' => $post['caption'],
            'user_id' => $user->id,
        ]);

        $paths = [];

        foreach ($request->file('attachments') as $file) {
            // Simpan file ke storage
            $path = $file->store('posts');

            // Simpan informasi file ke database
            PostAttachments::create([
                'post_id' => $post_id->id,
                'storage_path' => $path,
            ]);

            $paths[] = $path;

        return response()->json([ 
            'message' => 'Create post success',
        ]);

        }
    }

    public function delete($id) {
        $post = Posts::find($id);


        if(!$post) {
            return response()->json([
                'message' => 'Post not found',                
            ],404);
        }

        if(Auth::id() !== $post->user_id) {
            return response()->json([
                'message' => 'Forbidden access',
            ],403);
        }

        $attachment = PostAttachments::find($post->id);

        $attachment->delete();
        $post->delete();
        
        return response()->json([
        ],204);
    }

    public function show(PostShowReq $request)
    {
        $page = $request->input('page', 0);
        $size = $request->input('size', 10);

        $page = max(0, $page);
        $size = max(0, $size);


        $posts = Posts::with('post_attachments')->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->skip($page * $size)
            ->take($size)
            ->get();
            
        return response()->json([
            'page' => $page,
            'size' => $size,
            'posts' => $posts
        ]);
    }
}