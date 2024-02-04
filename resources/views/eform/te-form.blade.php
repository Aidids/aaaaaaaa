@extends('layouts.dashboard')
@section('content')
    <h4 class="mb-0">Travel Expense Form</h4>
    <expense-form :form_id="{{$travel->id}}"></expense-form>
@endsection
