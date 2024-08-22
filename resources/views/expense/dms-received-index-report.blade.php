@extends('layouts.app')

@section('content')
    <dms-received-expense :user-role="{{ Auth::user()->roles[0]->id }}" :user-level="{{ Auth::user()->level() }}" :expense-verifier="{{ Auth::user()->is_expense_approver }}"></dms-received-expense>
@endsection
