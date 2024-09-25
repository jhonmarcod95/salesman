@extends('layouts.app')

@section('content')
    <expense-report-index 
        :user-role="{{ Auth::user()->roles[0]->id }}" 
        :user-level="{{ Auth::user()->level() }}" 
        :expense-verifier="{{ Auth::user()->is_expense_approver }}" 
        :access-dms-received="{{ Auth::user()->access_dms_received }}"></expense-report-index>
@endsection
