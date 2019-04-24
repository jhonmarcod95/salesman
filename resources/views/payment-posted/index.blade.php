@extends('layouts.app')

@section('content')
    <payment-posted-index :user-id={{ Auth::user()->id }}></payment-posted-index>
@endsection