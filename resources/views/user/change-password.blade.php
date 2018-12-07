@extends('layouts.app')

@section('content')
    <user-change-password-index :user-id={{Auth::user()->id}}></user-change-password-index>
@endsection
