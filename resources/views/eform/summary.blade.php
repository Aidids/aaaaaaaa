@extends('layouts.dashboard')
@section('content')
    <eform-summary
        :is-project-manager="{{$isProjectManager ? 1 : 0}}"
    />
@endsection
