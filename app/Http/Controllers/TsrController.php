<?php

namespace App\Http\Controllers;

use Auth;
use App\TechnicalSalesRepresentative;
use App\{
    User,
    Role,
    Message
};
use Illuminate\Http\Request;

class TsrController extends Controller
{
    /**
     *  Display tsr index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        session(['header_text' => 'Technical Sales Representative']);

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('tsr.index', compact('notification'));
    }

    /**
     * Get all tsr
     *
     * @return \Illuminate\Http\Response
     */

    public function indexData(){
        return  TechnicalSalesRepresentative::with('company','user')
        ->when(Auth::user()->level() < 8, function($q){
            $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
        })
        ->orderBy('id','desc')->get();
    }

    /**
     * Display adding customer  page
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('tsr.create', compact('notification'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        $request->validate([
            'last_name' => 'required',
            'first_name' => 'required',
            'email' => 'required|unique:users,email',
            'address' => 'required',
            'contact_number' => 'required',
            'date_of_birth' => 'required',
            'company' => 'required'
        ]);


        $tsr = new TechnicalSalesRepresentative;

        $tsr->last_name = $request->last_name;
        $tsr->first_name = $request->first_name;
        $tsr->middle_name = $request->middle_name;
        $tsr->middle_initial = $request->middle_initial;
        $tsr->suffix = $request->suffix;
        $tsr->email = $request->email;
        $tsr->address = $request->address;
        $tsr->contact_number = $request->contact_number;
        $tsr->date_of_birth = $request->date_of_birth;
        $tsr->date_hired = $request->date_hired;
        $tsr->contact_person = $request->contact_person;
        $tsr->plate_number = $request->plate_number;
        $tsr->contact_person = $request->contact_person;
        $tsr->personal_email = $request->personal_email;
        $tsr->company_id = $request->company;

        if($tsr->save()){
            // Create Temporary code for user
            $user_password = strtolower($tsr->first_name.'.'.$tsr->last_name);
            // Save User
            $user = new User;
            $user->name = $tsr->first_name. ' ' .$tsr->last_name;
            $user->email = $tsr->email;
            $user->password = bcrypt(preg_replace('/\s+/', '', $user_password));
            $user->company_id = $request->company;
            if($user->save()){
                // Attaching role to user
                $tsrRole = Role::where('name', '=', 'Tsr')->first();
                $user->syncRoles($tsrRole);
                // Assigning of companies
                $user->companies()->sync( (array) $request->company);
                // Insert user_id in tsr table
                $tsr->update(['user_id'=> $user->id]);
                return ['redirect' => route('tsr_list')];
            }
        }
    }
    /**
     * Show the edit form
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('tsr.edit', compact('id', 'notification'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TechnicalSalesRepresentative::findOrFail($id);
    }

    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TechnicalSalesRepresentative $technicalSalesRepresentative)
    {
        $request->validate([
            'last_name' => 'required',
            'first_name' => 'required',
            'email' => 'required|unique:technical_sales_representatives,email,' .$technicalSalesRepresentative->id,
            'address' => 'required',
            'contact_number' => 'required',
            'date_of_birth' => 'required',
            'company' => 'required'
        ]);

        $technicalSalesRepresentative->last_name = $request->last_name;
        $technicalSalesRepresentative->first_name = $request->first_name;
        $technicalSalesRepresentative->middle_name = $request->middle_name;
        $technicalSalesRepresentative->middle_initial = $request->middle_initial;
        $technicalSalesRepresentative->suffix = $request->suffix;
        $technicalSalesRepresentative->email = $request->email;
        $technicalSalesRepresentative->address = $request->address;
        $technicalSalesRepresentative->contact_number = $request->contact_number;
        $technicalSalesRepresentative->date_of_birth = $request->date_of_birth;
        $technicalSalesRepresentative->date_hired = $request->date_hired;
        $technicalSalesRepresentative->contact_person = $request->contact_person;
        $technicalSalesRepresentative->plate_number = $request->plate_number;
        $technicalSalesRepresentative->contact_person = $request->contact_person;
        $technicalSalesRepresentative->personal_email = $request->personal_email;
        $technicalSalesRepresentative->company_id = $request->company;

        if($technicalSalesRepresentative->save()){

            $user = User::find($technicalSalesRepresentative->user_id);
            $user->name = $technicalSalesRepresentative->first_name. ' ' .$technicalSalesRepresentative->last_name;
            $user->email = $technicalSalesRepresentative->email;
            $user->company_id = $request->company;
            $user->save();

            return ['redirect' => route('tsr_list')];
        }
    }

    /**
     * Temporary code for inserting of user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function manuallyInsertUser($tsrId){
        $tsr = TechnicalSalesRepresentative::findOrFail($tsrId);

        // Create Temporary code for user
        $user_password = strtolower($tsr->first_name.'.'.$tsr->last_name);
        // Save User
        $user = new User;
        $user->name = $tsr->first_name. ' ' .$tsr->last_name;
        $user->email = $tsr->email;
        $user->password = bcrypt(preg_replace('/\s+/', '', $user_password));
        if($user->save()){
            // Attaching role to user
            $tsrRole = Role::where('name', '=', 'Tsr')->first();
            $user->syncRoles($tsrRole);
            // Insert user_id in tsr table
            $tsr->update(['user_id'=> $user->id]);

            return $tsr;
        }
        
    }
}
