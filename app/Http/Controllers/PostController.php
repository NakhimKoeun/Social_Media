<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index(){
       $posts = Post::with('user')->latest()->paginate(10);
           foreach($posts as $post){
            //count like
            $post['likes_count'] = $post->likes->count();
            //count comment
            $post['comments_count'] = $post->comments->count();
           $post['liked'] = $post->likes->where(auth()->user()->id);
           }
           return response()->json([
            'status'=>'succes',
            'data'=>$posts,
        ],200);
    }
    //get detial
    public function show($id){
        $post = Post::with(['user','comments.user','likes'])->find($id);
         //count like
         $post['likes_count'] = $post->likes->count();
         //count comment
         $post['comments_count'] = $post->comments->count();
        $post['liked'] = $post->likes->where(auth()->user()->id);
        return response()->json([
            'status'=>'succes',
            'data'=>$post,
        ],200);
    }
    //update post
    public function update(Request $request,$id){
        $post = Post::find($id);//find post by id
        //if  post not found return error
        if(!$post){
        return response()->json([
            'status'=>'erro',
            'message'=>'post not found'
        ],404);
    }
    $data = $request->all();
    //if post found check if user is authorized to update post
    if(auth()->user()->id != $post->user_id){
        return response()->json([
            'status'=>'error',
            'message'=>'you are not authorized to update this post'
        ],404);
    }
    //if request has photo
    if($request->hasFile('photo')){
        $image = Request('photo');
        $name = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/post');
        $image->move($destinationPath,$name);
       $data['image_url'] = $name;
       //get old file
      $oldImage = public_path('/posts/'.$post->image_url);
       if(file_exists($oldImage)){
        @unlink($oldImage);
       }
    }
    /*$post->update($data);
    //check request has title or body
    if($request->has('title')){
        //$post->title = $data['data'];
    }
    if($request->has('title')){
       $post->body = $data['body'];}*/
       $post->update($data);
    return response()->json([
        'status'=>'succes',
        'data'=>$post,
    ]);
}
public function destroy($id){
    $post = Post::find($id);//find post id
    //check if has photo
    if($post->image_url){
        $oldImage = public_path('/posts/').$post->image_url;
        if(file_exists($oldImage)){
            unlink($oldImage);
        }
    }
    if(!$post){
        return response()->json([
            'starus'=>'error',
            'message'=>'post not found'
        ],404);
        if(auth()->user()->id != $post->user_id){
            return response()->json([
                'status'=>'error',
                'message'=>'you are not authorized to update this post'
            ],404);
        }
    }
    $post->delete();
    return response()->json([
        'starus'=>'succes',
        'message'=>'post delete succesfully'
    ]);
}
    
    public function store(Request $request){
        $user = auth()->user();
        $data = $request->all();
        $data['user_id'] = $user->id;
        $post = Post::create($data);
        if($request->hasFile('photo')){
            $image = Request('photo');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/post');
            $image->move($destinationPath,$name);
           $data['image_url'] = $name;
        }
        $post['image_url'] = $name;
        return response()->json([
            'status'=>'success',
            'data'=>$post
        ]);
        
    }
}
