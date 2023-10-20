@extends('layouts.app')

@section('content')
    <aapc-farmer-index :user-role="{{ Auth::user()->roles->pluck('id') }}"></aapc-farmer-index>
@endsection
