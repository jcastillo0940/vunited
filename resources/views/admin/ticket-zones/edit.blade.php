@extends('layouts.admin.app')

@section('title', 'Edit Ticket Zone')

@section('content')
    <h2 style="margin-top:0;">Edit Ticket Zone</h2>
    @include('admin.ticket-zones.partials.form', [
        'action' => route('admin.ticket-zones.update', [$matchEvent, $ticketZone]),
        'method' => 'PUT',
        'matchEvent' => $matchEvent,
        'ticketZone' => $ticketZone,
    ])
@endsection
