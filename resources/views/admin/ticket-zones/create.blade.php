@extends('layouts.admin.app')

@section('title', 'Create Ticket Zone')

@section('content')
    <h2 style="margin-top:0;">Create Ticket Zone</h2>
    @include('admin.ticket-zones.partials.form', [
        'action' => route('admin.ticket-zones.store', $matchEvent),
        'method' => 'POST',
        'matchEvent' => $matchEvent,
        'ticketZone' => $ticketZone,
    ])
@endsection
