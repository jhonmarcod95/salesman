@extends('layouts.app')

@section('content')
    <appointment-duration-report-index :user-role="{{ Auth::user()->roles->pluck('id') }}"></appointment-duration-report-index>
@endsection
