<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReqRegister;
use App\Models\Follow;
use App\Models\PostAttachments;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(ReqRegister $request) {
        
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'message' => 'Success register',
            'token'=> $token,
            'user' => $user,
        ],201);
    }
    
    public function login (Request $request) {
        if(Auth::attempt(['username' => $request->username, 'password'=> $request->password])) {
            $auth = Auth::user();
            $token = $auth->createToken('token')->plainTextToken;
            return response()->json([ 
                'message'=> 'Login success',
                'token' => $token,
                'user' => $auth,
            ],200);
        } else {
            return response()->json([ 
                'message'=> 'Wrong username or password',
            ],401);
        }

    }
    public function logout(Request $request) {

        $user = Auth::user();
    
        $token = $request->user()->currentAccessToken();

    if ($token) {
        $token->delete();
    }
    
    return response()->json(['message' => 'Logged out successfully']);
    }


    public function user(Request $request) {
        $user = Auth::user();

        return response()->json([
            'users' => $user,
        ]);
    }

    public function detail (Request $request, string $username) {
        $user = Auth::user();
        
        $detailUser = User::where('username', $username)->first();
        if (!$detailUser) { 
            return response()->json([
                'message' => 'User not found',
            ],404);
        }

        $sameAccount = $detailUser->id == $user->id;
        $followStatus = Follow::where([
            'follower_id' => $user->id,
            'following_id' =>$detailUser->id,
        ])->first();
        

        if (!$followStatus) {
            $statusFollow = 'not following';
        } else {
            // Jika data ada, periksa status is_accept
            $statusFollow = $followStatus->is_accepted ? 'following' : 'requested';
        }

        $post_count = Posts::where('user_id', $detailUser->id)->count();
        $posts = Posts::with('post_attachments')->where('user_id', $user->id)->get();

        $followers_count = Follow::where('following_id', $detailUser->id)->count();
        $following_count = Follow::where('follower_id', $detailUser->id)->count();

        

        if($statusFollow == 'following') {
        return response()->json([
            'id' => $detailUser->id,
            'full_name' => $detailUser->full_name,
            'username' => $detailUser->username,
            'bio' => $detailUser->bio,
            'is_private' => $detailUser->is_private,
            'create_at' => $detailUser->create_at,
            'is_your_account' => $sameAccount,
            'following_status' => $statusFollow,
            'post_count' => $post_count,
            'followers_count' => $followers_count,
            'following_count' => $following_count,
            'post' => $posts,
        ]);
        }else {
            return response()->json([
                'id' => $detailUser->id,
                'full_name' => $detailUser->full_name,
                'username' => $detailUser->username,
                'bio' => $detailUser->bio,
                'is_private' => $detailUser->is_private,
                'create_at' => $detailUser->create_at,
                'is_your_account' => $sameAccount,
                'following_status' => $statusFollow,
                'post_count' => $post_count,
                'followers_count' => $followers_count,
                'following_count' => $following_count,
                'post' => null,
            ]);
        }
    }

}