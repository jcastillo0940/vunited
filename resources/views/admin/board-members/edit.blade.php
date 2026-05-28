@extends('layouts.admin.app')

@section('title', 'Editar — ' . $member->name)

@section('content')
    <h2>Editar — {{ $member->name }}</h2>

    <form method="POST" action="{{ route('admin.board-members.update', $member) }}" style="max-width:800px;">
        @csrf @method('PUT')
        @include('admin.board-members.partials.form')
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar cambios</button>
            <a href="{{ route('admin.board-members.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
