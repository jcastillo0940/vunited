@extends('layouts.admin.app')

@section('title', 'Edit Match Event')

@section('content')
    <h2 style="margin-top:0;">Edit Match Event</h2>
    @include('admin.match-events.partials.form', [
        'action' => route('admin.match-events.update', $matchEvent),
        'method' => 'PUT',
        'matchEvent' => $matchEvent,
    ])
@endsection
