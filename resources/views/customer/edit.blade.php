@extends('layouts.app')

@section('content')
    <customer-edit-form :customer-id={{ $id }}></customer-edit-form>
@endsection