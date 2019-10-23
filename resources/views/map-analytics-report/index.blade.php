@extends('layouts.app')

@section('content')
    <map-analytics-report-index :user-role="{{ Auth::user()->roles->pluck('id') }}"></map-analytics-report-index>
@endsection
