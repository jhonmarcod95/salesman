@extends('layouts.app')

@section('content')
    <expense-deduction-index 
    :user-role="{{ Auth::user()->roles->pluck('id')->first() }}">
</expense-deduction-index>
@endsection
