@extends('layouts.admin.app')

@section('title', 'Create Match Event')

@section('content')
    <h2 style="margin-top:0;">Create Match Event</h2>
    @include('admin.match-events.partials.form', [
        'action' => route('admin.match-events.store'),
        'method' => 'POST',
        'matchEvent' => $matchEvent,
    ])
@endsection
