@extends('layouts.dashboard')
@section('content')
<h4 class="mb-0">Travel Authorization Form</h4>
<travel-form :form_id="{{$travel->id}}"></travel-form>
@endsection
