@extends('layouts.admin.app')

@section('title', 'Crear FanFest Event')

@section('content')
    <h2>Crear FanFest Event</h2>

    <form method="POST" action="{{ route('admin.fanfest-events.store') }}" style="max-width:800px;display:grid;gap:1rem;">
        @csrf
        @include('admin.fanfest-events._form')
        <div style="display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar</button>
            <a href="{{ route('admin.fanfest-events.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
