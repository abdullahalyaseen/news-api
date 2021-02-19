<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
class CommentController extends Controller
{
    /**
     *
     */
    public function index()
    {

    }

    /**
     * @param Request $request
     * @param $id
     * @return CommentResource
     */
    public function store(Request $request,$id)
    {
        $request->validate([
            'content'=>['required','string'],
        ]);
        $user = $request->user();
        $comment = new Comment();
        $comment->datetime = now();
        $comment->content = $request->get('content');
        $comment->post_id = $id;
        $comment->user_id = $user->id;
        $comment->save();
        return new CommentResource($comment);
    }

    /**
     * @param $id
     * @return CommentResource
     */
    public function show($id)
    {
        $comment = Comment::with('user')->where('id',$id)->get();
        return new CommentResource($comment);
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content'=>['required','string']
        ]);
        $user = $request->user();
        $comment = Comment::find($id);
        if($user->id == $comment->user_id){
            $comment->content = $request->get('content');
            $comment->save();
            return Comment::find($id);
        }else{
            return 'you can edit your comments only';
        }
    }

    /**
     * @param $id
     */
    public function destroy(Request $request , $id)
    {
        $user = $request->user();
        $comment = Comment::find($id);
        if ($user->id == $comment->user_id) {
            $comment->delete();
        } else {
            return 'you can delete your comments only';
        }
    }
}
