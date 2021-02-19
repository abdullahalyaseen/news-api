<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCommentsResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostsResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * @return PostsResource
     */
    public function index()
    {
        $posts = Post::with('comments','category','user')->paginate(env('POSTS_PER_PAGE'));
        return new PostsResource($posts);
    }

    /**
     * @param Request $request
     * @return PostResource
     */
    public function store(Request $request)
    {
        $request->validate([
           'title'=>['required','string'],
           'content'=>['required','string'],
           'category_id'=>['required','integer', 'gt:0','exists:categories,id'],
        ]);
         $user = $request->user();
         $post = new Post();
         $post->title = $request->get('title');
         $post->datetime = now();
         $post->content = $request->get('content');
         $post->user_id = $user->id;
         $post->category_id = $request->get('category_id');
         if ($request->has('featured_image')){
             $request->validate([
                'featured_image'=>['image']
             ]);
             $file = $request->file('featured_image')->store('/images/featured_images');
             $post->featured_image = $file;
         }
         $post->vote_up = 0;
         $post->vote_down = 0;
         $post->vote_up_ids = array();
        $post->vote_down_ids = array();
         $post->save();
         return new PostResource($post);

    }

    /**
     * @param $id
     * @return PostResource
     */
    public function show($id)
    {
        $post = Post::with('comments.user','category','user')->where('id',$id)->get();
        return new PostResource($post);
    }

    /**
     * @param Request $request
     * @param $id
     * @return PostResource|string
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $post = Post::find($id);

       if ($post->user_id == $user->id){
           if($request->has('title')){
               $request->validate([
                   'title'=>['string'],
               ]);
               $post->title = $request->get('title');
           }

           if($request->has('content')){
               $request->validate([
                   'content'=>['string'],
               ]);
               $post->content = $request->get('content');
           }

           if($request->has('category_id')){
               $request->validate([
                   'category_id'=>['integer', 'gt:0','exists:categories,id'],
               ]);
               $post->category_id = $request->get('category_id');
           }
           if($request->has('featured_image')){
               $request->validate([
                   'featured_image'=>['image']
               ]);
               Storage::delete($post->featured_image);
               $file = $request->file('featured_image')->store('/images/featured_images');
               $post->featured_image = $file;
           }
           $post->save();
           return new PostResource(Post::find($id));
       }else{
           return "You can't edit others posts!";
       }

    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     */
    public function destroy(Request $request , $id)
    {
        $user = $request->user();
        $post = Post::find($id);
        if ($user->id == $post->user_id){
            Storage::delete($post->featured_image);
            Post::destroy($id);
            return "Post has been deleted";
        }else{
            return "You can't delete others posts!";
        }
    }

    /**
     * @param $id
     * @return PostCommentsResource
     */
    public function comments($id){
        $post = Post::find($id);
        $comments = $post->comments()->paginate(env('COMMENTS_PER_PAGE'));
        return new PostCommentsResource($comments);
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     */
    public function vote(Request $request,$id)
    {
        $user = $request->user();
        $post = Post::find($id);
        $up_array = $post->vote_up_ids;
        $down_array = $post->vote_down_ids;
        if ($request->get('vote') == 'up') {
            //check if the user did not vote up
            if (!in_array($user->id, $up_array)) {
                //check if user did vote down
                if (in_array($user->id, $down_array)) {
                    //delete user id from down array
                    $down_array = array_diff($down_array,[$user->id]);
                    $post->vote_down_ids = $down_array;
                    //decrease vote down by one
                    $post->vote_down -= 1;
                }
                //add user id to up array
                 array_push($up_array,$user->id);
                $post->vote_up_ids = $up_array;
                //increase vote up by ine
                $post->vote_up += 1;
                $post->save();
                return Post::find($id);
            }
        } elseif ($request->get('vote') == 'down') {
            //check if the user did not vote down
            if (!in_array($user->id, $down_array)) {
                //check if user did vote up
                if (in_array($user->id, $up_array)) {
                    //delete user id from up array
                    $up_array = array_diff($up_array,[$user->id]);
                    $post->vote_up_ids = $up_array;
                    //decrease vote up by one
                    $post->vote_up -= 1;
                }
                //add user id to down array
                array_push($down_array,$user->id);
                $post->vote_down_ids = $down_array;
                //increase vote down by one
                $post->vote_down += 1;
                $post->save();
                return Post::find($id);
            } else {
                return 'choose either up or down';
            }
        }
    }
}


