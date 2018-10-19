<?php

namespace App\Http\Controllers;

use Auth;
use App\{
    Message,
    User
};
use App\Events\MessageSent;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session(['header_text' => 'Messages']);
        
        $notification = Message::where('user_id', '!=', Auth::user()->id)->whereNull('seen')->count();

        return view('message.index', compact('notification'));
    }

    /**
     * Get all message
     *
     * @return \Illuminate\Http\Response
     */

    public function indexData(){
        // $message = Message::with('user', 'recipient')->where('user_id', '!=', Auth::user()->id)->orderBy('id','desc')->get()->unique('user_id')->values()->all();
        // $first_message_id = Message::with('user', 'recipient')->where('first_message_from', '<>', '')->orderBy('id','desc')->get();
        $message = Message::with('user', 'recipient')->where('last_message_for', '<>', '')->orderBy('id','desc')->get();
        return $message;
    }
    
    /**
     * Get all message by specific user
     *
     * @return \Illuminate\Http\Response
     */
    public function messageByuser($id){
        
        $update = Message::where('last_message_for', $id)->update(['seen' => 1]);

        return Message::with('user','recipient')
            ->where('user_id', $id)
            ->orWhere('reply_to', $id)
            ->orderBy('id', 'asc')
            ->get();
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'message_id' => 'required'
        ]);

        // Temporary logic
        $user = Auth::user();
        $message = new Message;
        
        $message->message = $request->message;
        $message->user_id = Auth::user()->id;
        if($user->hasRole('admin') || $user->hasRole('user')){
            $message->reply_to = $request->message_id;
        }
        
        $last_message = Message::where('last_message_for',  $request->message_id)->first();
        if($last_message){
            $last_message->last_message_for = null;
            $last_message->save();
        }

        if($message->save()){
            if(!$last_message){
                Message::where('id', $message->id)->update(['last_message_for' => $request->message_id]);
            }

            Message::where('id', $message->id)->update(['last_message_for' => $request->message_id]);
            $new_message = Message::with('user', 'recipient')->where('id', $message->id)->get();
            broadcast(new MessageSent($user, $new_message))->toOthers();
            return $new_message;
        }
    }

    /**
     * Get all recipients of the message
     *
     * @return \Illuminate\Http\Response
     */

    public function recipients(){
        $user = User::whereHas('roles', function($q){
            $q->where('role_id', 3);
        })->get();

        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
