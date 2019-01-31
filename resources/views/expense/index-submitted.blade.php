@extends('layouts.app')

@section('content')
<expense-submitted-index :expense-entry-id={{$id}} :date-entry= "'{{ $date }}'"></expense-submitted-index> 
@endsection
