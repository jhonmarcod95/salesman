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

         // Prepare the Chrome and fallback URLs
        $chromeUrl = 'googlechrome://salesforce.lafilgroup.net:8666';
        $fallbackUrl = 'http://salesforce.lafilgroup.net:8666';

        // You can do some checks here if necessary, like checking user-agent for mobile, etc.

        // If Chrome URL fails, fallback to normal HTTP
        return redirect()->away($chromeUrl)->withHeaders(['Fallback' => $fallbackUrl]);

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
