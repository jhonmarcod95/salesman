@extends('layouts.app')

@section('content')
    <expense-submitted-index :expense-entry-id={{json_encode($ids)}} :date-entry= "'{{ $date }}'"></expense-submitted-index>
@endsection
