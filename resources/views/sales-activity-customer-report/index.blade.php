@extends('layouts.app')

@section('content')
    <sales-activity-customer-report-index :user-id={{ Auth::user()->id }}></sales-activity-customer-report-index>
@endsection