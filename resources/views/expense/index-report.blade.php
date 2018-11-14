@extends('layouts.app')

@section('content')
    <expense-report-index :user-level="{{ Auth::user()->level() }}"></expense-report-index>
@endsection
