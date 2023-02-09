@extends('layouts.app')

@section('content')
    <survey-display :user-role="{{ Auth::user()->roles->pluck('id') }}"></survey-display>
@endsection
