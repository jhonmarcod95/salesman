@extends('layouts.app')

@section('content')
    <user-edit-form :user-id={{ $id }}></user-edit-form>
@endsection