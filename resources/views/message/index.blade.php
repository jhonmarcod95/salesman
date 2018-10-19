@extends('layouts.app')

@section('content')
    <message-index :user-id={{ Auth::user()->id }}></message-index>
@endsection
