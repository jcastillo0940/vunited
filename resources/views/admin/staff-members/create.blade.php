@extends('layouts.admin.app')

@section('title', 'Agregar Miembro de Staff')

@section('content')
    <h2>Agregar Miembro de Staff</h2>

    <form method="POST" action="{{ route('admin.staff-members.store') }}" style="max-width:700px;">
        @csrf
        @include('admin.staff-members.partials.form')
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
            <button type="submit" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">Guardar miembro</button>
            <a href="{{ route('admin.staff-members.index') }}" class="admin-button">Cancelar</a>
        </div>
    </form>
@endsection
