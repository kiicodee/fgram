<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{ 
    public function follow(string $username) {
        $user = Auth::user();

        $follower = User::where("username", $username)->first();
        if (!$follower) { 
            return response()->json([
                'message' => 'User not found',
             ],404);
        }

        if($follower->id === $user->id) {
            return response()->json([
                'message'=> 'Your not allowed to follow yourself',
            ],422);
        }

        $existingFollow = Follow::where([
            'follower_id' => $user->id,
            'following_id' => $follower->id,
        ])->first();
        if ($existingFollow) {
            $status = $existingFollow->is_accepted ? 'following' : 'requested';
            return response()->json([
                'message' => 'You are already followed',
                'status' => $status
            ], 422);
        }



        $following = Follow::create([
            'follower_id' => $user->id,
            'following_id' => $follower->id,
            'is_accepted' => false,
        ]);

        return response()->json([
            'message' => 'Follow success',
            'status' => 'requested'
        ],200);

    }
    public function unfollow(string $username) { 
        $user = Auth::user();

        $follower = User::where('username', $username)->first();
        if (!$follower) {
            return response()->json([
                'message'=> 'User not Found',
            ],404);
        }

        $notFollow = Follow::where([
            'follower_id' => $user->id,
            'following_id' => $follower->id,
        ])->first();

        
        if (!$notFollow) {
            return response()->json([
                'message'=> 'You are not following the user',
            ],422);
        } else {
            $notFollow->delete();
            return response()->json([
                'message'=> 'Success unfollow',
            ],200);
        }
    }

    public function following(string $username) {
        
        $follower = User::where('username', $username)->first();
        if (!$follower) {
            return response()->json([
                'message' => 'User not found',
            ]);
        }else {
        $following = Follow::where('follower_id', $follower->id)->get();

        foreach ($following as $follow) {
            return response()->json([
                'following' => $following,
            ]);
            }   
        }
    }
    public function accept(string $username) {

        $username = User::where('username', $username)->first();

        if (!$username) {
            return response()->json([
                'message' => 'User not found',
            ],404);
        }

        $userId  = Auth::user()->id;
        $user = User::where('id', $userId)->first();

        $following = Follow::where([ 
            'follower_id' => $user->id,
            'following_id' => $username->id,
        ])->first();

        if(!$following) {
            return response()->json([
                'message' => 'The user is not following you',
            ],404);
        }

        if($following->is_accepted == false) {

            $following->update([
                'is_accepted' => true,
            ]);
            
            return response()->json([
                'message' => 'Follow request is accepted',
            ]);
        } else if($following->is_accepted == true) {
            return response()->json([
                'message' => 'Follow request is already accepted',
            ]);
        }        
    }

    public function follower(string $username) {
        $following = User::where('username', $username)->first();
        if (!$following) {
            return response()->json([
                'message' => 'User not found',
            ]);
        }else {

        $follower = Follow::where('following_id', $following->id)->get();

        foreach ($follower as $follow) {
            return response()->json([
                'following' => $follower,
            ]);
            }   
        }
    }
}
