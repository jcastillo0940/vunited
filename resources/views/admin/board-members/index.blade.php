@extends('layouts.admin.app')

@section('title', 'Directiva')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.4rem;">Directiva</h2>
            <p style="margin:0;color:#475569;">Miembros de la junta directiva y cuerpo ejecutivo del club.</p>
        </div>
        <a href="{{ route('admin.board-members.create') }}" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">
            + Agregar miembro
        </a>
    </div>

    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;margin:1.5rem 0;">
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Nombre" class="admin-button" style="min-width:220px;">
        <select name="group" class="admin-button">
            <option value="">Todos los grupos</option>
            @foreach(['presidency' => 'Presidencia', 'executive_staff' => 'Staff Ejecutivo', 'board' => 'Junta Directiva', 'transparency' => 'Gobernanza'] as $key => $label)
                <option value="{{ $key }}" @selected($filters['group'] === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="admin-button">Filtrar</button>
    </form>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Nombre</th>
                    <th style="padding:0.75rem;">Cargo</th>
                    <th style="padding:0.75rem;">Grupo</th>
                    <th style="padding:0.75rem;">Activo</th>
                    <th style="padding:0.75rem;">Orden</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $member)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;">
                            {{ $member->name }}<br>
                            <small style="color:#64748b;font-family:monospace;">{{ $member->slug }}</small>
                        </td>
                        <td style="padding:0.75rem;">{{ $member->role }}</td>
                        <td style="padding:0.75rem;">
                            <span style="padding:0.2rem 0.6rem;border-radius:0.4rem;font-size:0.8rem;background:#dbeafe;color:#1e40af;">
                                {{ ['presidency'=>'Presidencia','executive_staff'=>'Staff Ejecutivo','board'=>'Junta Directiva','transparency'=>'Gobernanza'][$member->group] ?? $member->group }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;">{{ $member->is_active ? '✓' : '—' }}</td>
                        <td style="padding:0.75rem;">{{ $member->sort_order }}</td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;">
                            <a href="{{ route('admin.board-members.edit', $member) }}" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;">Editar</a>
                            <form method="POST" action="{{ route('admin.board-members.destroy', $member) }}" onsubmit="return confirm('¿Eliminar miembro?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;color:#991b1b;border-color:#fca5a5;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:1rem;">Sin miembros registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
