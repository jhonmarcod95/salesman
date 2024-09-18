@extends('layouts.app')

@section('content')
    <expense-rejected-index :user-role="{{ Auth::user()->roles[0]->id }}" :user-level="{{ Auth::user()->level() }}" :expense-verifier="{{ Auth::user()->is_expense_approver }}"></expense-rejected-index>
@endsection
