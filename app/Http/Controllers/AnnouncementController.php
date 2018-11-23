<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Announcement;
use App\Message;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session(['header_text' => 'Announcements']);

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('announcement.index', compact('notification'));
    }

    /**
     * Get all announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData(){
        if(Auth::user()->level() < 8){
            return  Announcement::with('user')->whereHas('user' , function($q){
                $q->whereHas('companies', function ($q){
                    $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                });
            })->orderBy('id','desc')->get();
        }

        return  Announcement::with('user')->orderBy('id','desc')->get();
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
        $announcement = new Announcement;

        $announcement->user_id = Auth::user()->id;
        $announcement->message = $request->message;
        if($announcement->save()){
            return Announcement::with('user')->findOrfail($announcement->id);
        }

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
    public function update(Request $request, Announcement $announcement)
    {
        $announcement->message = $request->message;

        if($announcement->save()){
            return Announcement::with('user')->findOrfail($request->announcement->id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Announcement $announcement)
    {
        if($announcement->delete()){
            return $announcement;
        }
    }
}
