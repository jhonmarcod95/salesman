<?php

namespace App\Http\Controllers;

use App\TechnicalSalesRepresentative;
use App\{
    User,
    Role
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

        return view('tsr.index');
    }

    /**
     * Get all tsr
     *
     * @return \Illuminate\Http\Response
     */

    public function indexData(){
        return  TechnicalSalesRepresentative::orderBy('id','desc')->get();
    }

    /**
     * Display adding customer  page
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('tsr.create');
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
            'date_hired' => 'required',
            'date_of_birth' => 'required',
            'contact_person' => 'required',
            'personal_email' => 'email',
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

        if($tsr->save()){
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
        return view('tsr.edit', compact('id'));
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
            'middle_name' => 'required',
            'middle_initial' => 'required',
            'suffix' => 'required',
            'email' => 'required|unique:users,email,' .$technicalSalesRepresentative->id,
            'address' => 'required',
            'contact_number' => 'required',
            'date_of_birth' => 'required',
            'date_hired' => 'required',
            'date_of_birth' => 'required',
            'contact_person' => 'required',
            'personal_email' => 'required',
            'plate_number' => 'required'
            
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


        if($technicalSalesRepresentative->save()){
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
