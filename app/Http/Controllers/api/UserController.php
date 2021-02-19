<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UserCommentsResource;
use App\Http\Resources\UserPostsResource;
use App\Http\Resources\UsersResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * @return UsersResource
     */
    public function index()
    {
        $users = User::paginate(env('AUTHOR_PER_PAGE'));
        return new UsersResource($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:64', 'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:64'],
        ]);
        $user = new User();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = Hash::make($request->get('password'));
        $user->save();
        return ($user->name);
    }

    public function addav(Request $request){
        $user = $request->user();
        if ($request->has('avatar')) {
            $request->validate([
                'avatar'=>'image'
            ]);
            Storage::delete($user->avatar);
            $file = $request->file('avatar')->store('/images/avatar_images');
            $user->avatar = $file;
        }
        $user->save();
        return User::find($user->id);
    }

    /**
     * @param $id
     * @return UserResource
     */
    public function show($id)
    {
        $user = User::find($id);
        return new UserResource($user);
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {

    }

    public function renew(Request $request){
        $user = $request->user();
            if ($request->has('name')) {
                $request->validate([
                    'name'=>['string', 'min:3', 'max:64', 'regex:/^[\pL\s\-]+$/u']
                ]);
                $user->name = $request->get('name');
            }
            $user->save();
            return User::find($user->id);

    }


    public function delav(Request $request){
        $user = $request->user();
            Storage::delete($user->avatar);
            $user->avatar = null;
            $user->save();
            return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function posts($id)
    {
        $posts = User::with('posts.comments.user')->where('id',$id)->paginate(env('POSTS_PER_PAGE'));
        return new UserPostsResource($posts);
    }

    public function comments($id)
    {
        $user = User::find($id);
        $comments = $user->comments()->paginate(env('COMMENTS_PER_PAGE'));
        return new UserCommentsResource($comments);
    }

    public function token(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $creds = $request->only('email', 'password');
        if (Auth::attempt($creds)) {
            $user = User::where('email', $request->get('email'))->first();
            $token = $user->api_token;
            return new TokenResource(['token' => $token]);
        }
        return response()->json(['message' => 'Wrong Email or Password'], 442);
    }

}


