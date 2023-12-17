<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\ValidatedData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
class AuthController extends Controller
{
    
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register',]]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password',]);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        //get user data
        $user = User::where('email',request('email'))->first();
        $userRes = [
            'id'=>$user->id,
            'name'=>$user->name,
            'email'=>$user->email,
            'profile_url'=>$user->profile_url,
        ];
        return response()->json([
            'acess_token' => $token,
            'token_type' => 'bearer',
            'user' => $userRes,
            'expirs_in' => auth()->factory()->getTTL() * 60
        ]);
       // return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $validateData = $request-> validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:5|max:12']);
        $validateData['password'] = bcrypt($request->password);
        if($request->hasFile('profile')){
            $image = Request('profile');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/profile');
            $image->move($destinationPath,$name);
           $validateData['profile_url'] = $name;
        }
        $user = User::create($validateData);
        $accessToken = $user->createToken('authToken')->accessToken;
        return response(['user' => $user, 'access_token' => $accessToken]);
     }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
        /// update user
        //update post
        public function update(Request $request,$id){
            $user = User::find($id);//find post by id
            //if  post not found return error
            if(!$user){
            return response()->json([
                'status'=>'erro',
                'message'=>'post not found'
            ],404);
        }
        if($user){
    
        //if request has photo
        if($request->hasFile('profile')){
            $image = Request('profile');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/profile');
            $image->move($destinationPath,$name);
           $data['profile_url'] = $name;
           //get old file
          $oldImage = public_path('/profile/').$user->profile_url;
           if(file_exists($oldImage)){
            @unlink($oldImage);
           }
           
        }
        $user->update($data);
       $baseUrlImage = request()->getSchemeAndHttpHost().'/profile';
       $user->profoel_url = $baseUrlImage.'/'.$data['profile_url'];
       return response(['user'=>$user]);
    }
        /*$post->update($data);
        //check request has title or body
        if($request->has('title')){
            //$post->title = $data['data'];
        }
        if($request->has('title')){
           $post->body = $data['body'];}*/
           
    }
}
