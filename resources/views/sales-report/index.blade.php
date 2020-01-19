@extends('layouts.app')

@section('content')
    <sales-report-index :user-id={{ Auth::user()->id }}></sales-report-index>
@endsection