<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Design;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Design $design)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Design $design, Request $request)
    {
        $request->validate([
            'body' => ['required']
        ]);

        $comment = $design->comments()->create([
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        return response()->json($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Design $design, Comment $comment)
    {
        return response()->json($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Design $design, Comment $comment)
    {
        $this->authorize('update',$comment);

        $request->validate([
            'body' => ['required']
        ]);

        $comment->update([
            "body" => $request->body,
        ]);

        return response()->json($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Design $design, Comment $comment)
    {
        $this->authorize('delete',$comment);

        $comment->delete();

        return response()->json(['msg' => 'comment deleted successfuly']);
    }
}
