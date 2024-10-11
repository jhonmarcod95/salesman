@extends('layouts.app')

@section('content')
    <rejected-expense-report-index 
        :user-role="{{ Auth::user()->roles[0]->id }}" 
        :user-level="{{ Auth::user()->level() }}" 
        :expense-verifier="{{ Auth::user()->is_expense_approver }}" 
        :access-dms-received="{{ Auth::user()->access_dms_received }}">
    </rejected-expense-report-index>
@endsection
