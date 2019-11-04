@extends('layouts.app')

@section('content')
    <map-analytics-report-map-users :user-role="{{ Auth::user()->roles->pluck('id') }}"></map-analytics-report-map-users>
@endsection
