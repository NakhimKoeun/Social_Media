<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
class CommentController extends Controller
{
    //list comment for post
    public function show($id){
        $post = Post::find($id);
        //if no post
        if(!$post){
            return response()->json([
                'status'=>'error',
                'message'=>'post not found'
            ],404);
        }
        $comments = $post->comments()->with('user')->get();
        return response()->json([
            'status'=>'succes',
            'data'=>$comments
        ]);
    }
    //add comment to a post
    public function store(Request $request,$id){
        $user = auth()->user();//get current user logged in
        $post = Post::find($id);
        //if no post 
        if(!$post){
            return response()->json([
                'status'=>'error',
                'message'=>'post not found',
            ],404);
        }
        $data = $request->all();
        $data['user_id'] = $user->id;//add user id
        $comment = $post->comments()->create($data);//save comment to db
        return response()->json([
            'status'=>'error',
            'data'=>$comment
        ]);
    }
    //comment update
    public function update(Request $request,$id){
        $user = auth()->user();
        $valid = $this->validate($request,[
            'text'=>'required',
        ]);
        $comment = Comment::find($id);
        //if no comment
        if(!$comment){
            return response()->json([
                'status'=>'error',
                'message'=>'comment not found'
            ],404);
        }
        //if user not the owner comment 
        if($comment->user_id != $user->id){
            return response()->json([
                'status'=>'error',
                'message'=>'you are not the owner this comment'
            ],401);
        }
        $comment->update($valid);
        return response()->json([
            'status'=>'succes',
            'data'=>$comment
        ]);
    }
    public function destroy($id){
        $user = auth()->user();
        $comment = Comment::find($id);
         //if no comment
         if(!$comment){
            return response()->json([
                'status'=>'error',
                'message'=>'comment not found'
            ],404);
        }
        //if user not the owner comment 
        if($comment->user_id != $user->id){
            return response()->json([
                'status'=>'error',
                'message'=>'you are not the owner this comment'
            ],401);
        }
        $comment->delete();
        return response()->json([
            'status'=>'succes',
            'massage'=>'delete complate'
        ]);
    }
}
