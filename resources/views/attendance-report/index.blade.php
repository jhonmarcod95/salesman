@extends('layouts.app')

@section('content')
    <attendance-report-index :user-role="{{ Auth::user()->roles->pluck('id') }}"></attendance-report-index>
@endsection
