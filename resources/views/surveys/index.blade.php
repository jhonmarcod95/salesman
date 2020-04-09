@extends('layouts.app')

@section('content')
    <survey-index :user-role="{{ Auth::user()->roles->pluck('id') }}"></survey-index>
@endsection
