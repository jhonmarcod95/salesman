<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Faq as FaqResource;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Faq;

class FaqsControllerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('http://salesforce.lafilgroup.net:8666/authority-to-deduct/'.Auth::user()->id);
        // return redirect()->away('http://salesforce.lafilgroup.net:8666/authority-to-deduct/'.Auth::user()->id);
        // $faqs = Faq::all();
        // return FaqResource::collection($faqs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'question' => 'required',
            'answer' => 'required'
        ]);

        $faq = Faq::create([
            'user_id' => 1,
            'question' => $request->question,
            'answer' => $request->answer
        ]);

        return response()->json(new FaqResource($faq), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Faq $faq)
    {
        return response()->json(new FaqResource($faq));
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
