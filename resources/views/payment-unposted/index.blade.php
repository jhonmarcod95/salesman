@extends('layouts.app')

@section('content')
    <payment-unposted-index :user-id={{ Auth::user()->id }}></payment-unposted-index>
@endsection