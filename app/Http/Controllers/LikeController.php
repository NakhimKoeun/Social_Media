<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\Like;
class LikeController extends Controller
{
   public function getlike($id){
      $post = Like::where('post_id',$id)->with('user')->get();
      //get only user frome likes
      $users = $post->pluck('user');
      return response()->json([
         'status'=>'success',
         'data'=>$users,
      ]);
     /* $post = Post::find($id);//get post by id
      $likes = $post->likes;
      return response()->json([
         'status'=>'sucess',
         'data'=>$likes,
      ]);*/
   }
   public function togglelike($id){
      $user = auth()->user();//get current user logged in
      $post = Post::find($id);
      $liked = $post->likes->contains('user_id',$user->id);
      if($liked){
        $like = Like::where('user_id',$user->id)->where('post_id',$post->id)->first();
        $like->delete();
        return response()->json([
         'status'=>'success',
        ],200);
      }
      else{
         
         $like = Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
         ]);
         return response()->json([
            'status'=>'succes',
            'data'=>$like,
         ],200);
      }
      
   }
}
