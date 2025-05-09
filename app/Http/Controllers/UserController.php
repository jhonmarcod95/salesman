<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Message;
use App\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{

     /**
     *  Display user index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        session(['header_text' => 'Users']);

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('user.index', compact('notification'));
    }

    /**
     * Get all tsr
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData(){
        return  User::with('roles','companies')->orderBy('id','desc')->get();
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

        return view('user.create', compact('notification'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role' => 'required',
            'company' => 'required'            
        ]);

        $user = new User;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_expense_approver = $request->is_expense_approver;
        $user->password = bcrypt($request->password);

        if($user->save()){
            // Assigning of role
            $user->syncRoles($request->role);
            // Assigning of companies
            $user->companies()->sync( (array) $request->company);

            return ['redirect' => route('users_list')];
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

        return view('user.edit', compact('id', 'notification'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::with('roles', 'companies')->where('id',$id)->get();
    }

    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
            'company' => 'required'
        ]);
        
        $user->name = $request->name;
        $user->email= $request->email;
        $user->is_expense_approver= $request->is_expense_approver;
        $user->is_act_user= $request->is_act_user;
        $user->is_sales= $request->is_sales;
        $user->access_dms_received= $request->access_dms_received;

        if($user->save()){
            // Assigning of role
            $user->syncRoles($request->role);
            // Assigning of companies
            $user->companies()->sync( (array) $request->company);

            return ['redirect' => route('users_list')];
        }
    }

    /* Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrfail($id);

        if($user->delete()){
            return $user;
        }
    }

    /*
     * Return change password page
     * 
     * @return \Illuminate\Http\Response
     */

    public function changePasswordIndex(){
        session(['header_text' => 'Change Password']);

        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;  
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }
        return view('user.change-password', compact('notification'));
    }
    
    /*
     * Change password 
     * 
     * @return \Illuminate\Http\Response
     */

    public function changePassword(Request $request){

        $validator = $request->validate([
            'user_id' => 'required',
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required'
        ]);
    
        $user = User::findOrFail($request->user_id);
        $user->password = bcrypt($request->input('new_password'));
        $user->save();

        return $user;
    }

    public function getRole(){
        $role = Auth::user()->roles()->get();
        return $role[0]->name;
    }

    public function selectionRole() {
        return Role::orderBy('name')->get(['id', 'name']);
    }

    public function selectionUsers() {
        return $this->getUsersWithExpense();
    }

    public function getUsersWithExpense($company_id = null) {
        return User::select(['id', 'name'])->whereHas('companies', function ($q) use($company_id){
            $q->when($company_id, function($companyQuery) use($company_id) {
                $companyQuery->where('company_id', $company_id);
            }, function($q) {
                if (!Auth::user()->hasRole('it')) {
                    $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                }
            });
        })
        ->UserWithExpense()
        ->orderBy('name', 'ASC')
        ->get();
    }

    public function selectionCoordinators($company_id) {
        $coordinators = User::select('id', 'name')->with("roles", "companies")
            ->when($company_id != 'all' && isset($company_id), function($query) use($company_id){
                $query->whereHas("company", function ($q) use($company_id){
                    $q->where("id", $company_id);
                });
            })
            ->whereHas("roles", function ($q) {
                $q->whereIn("slug", ["coordinator", "coordinator-2"]);
            })
            ->orderBy('name', 'ASC')
            ->get();

        return $coordinators->transform(function($item) {
            // dd($item->company);
            $data['id'] = $item->id;
            $data['name'] = $item->name;
            $data['company'] = $item->companies[0]->name;
            return $data;
        });
    }
}
