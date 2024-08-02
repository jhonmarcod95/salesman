@extends('layouts.app')

@section('content')
    <expense-report-index :user-role="{{ Auth::user()->roles[0]->id }}" :user-level="{{ Auth::user()->level() }}"></expense-report-index>
@endsection
