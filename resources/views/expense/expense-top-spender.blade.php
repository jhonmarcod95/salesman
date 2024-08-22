@extends('layouts.app')

@section('content')
    <expense-top-spender :user-role="{{ Auth::user()->roles[0]->id }}" :user-level="{{ Auth::user()->level() }}" :expense-verifier="{{ Auth::user()->is_expense_approver }}"></expense-top-spender>
@endsection
    