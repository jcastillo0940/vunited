@extends('layouts.admin.app')

@section('title', 'Patrocinadores')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 0.4rem;">Patrocinadores</h2>
            <p style="margin:0;color:#475569;">Aliados y patrocinadores del club.</p>
        </div>
        <a href="{{ route('admin.sponsors.create') }}" class="admin-button" style="background:#1D428A;color:#fff;border-color:#1D428A;">
            + Agregar patrocinador
        </a>
    </div>

    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;margin:1.5rem 0;">
        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Nombre" class="admin-button" style="min-width:240px;">
        <select name="tier" class="admin-button">
            <option value="">Todos los niveles</option>
            @foreach(['main_partner' => 'Main Partner', 'official_sponsor' => 'Official Sponsor', 'strategic_ally' => 'Alianza Estratégica'] as $key => $label)
                <option value="{{ $key }}" @selected($filters['tier'] === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="admin-button">Filtrar</button>
    </form>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #dbe4ee;">
                    <th style="padding:0.75rem;">Nombre</th>
                    <th style="padding:0.75rem;">Nivel</th>
                    <th style="padding:0.75rem;">Sitio web</th>
                    <th style="padding:0.75rem;">Activo</th>
                    <th style="padding:0.75rem;">Orden</th>
                    <th style="padding:0.75rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sponsors as $sponsor)
                    <tr style="border-bottom:1px solid #eef2f7;">
                        <td style="padding:0.75rem;">
                            {{ $sponsor->name }}<br>
                            <small style="color:#64748b;font-family:monospace;">{{ $sponsor->slug }}</small>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="
                                padding:0.2rem 0.6rem;
                                border-radius:0.4rem;
                                font-size:0.8rem;
                                background:{{ $sponsor->tier === 'main_partner' ? '#fef3c7' : ($sponsor->tier === 'official_sponsor' ? '#dbeafe' : '#f0fdf4') }};
                                color:{{ $sponsor->tier === 'main_partner' ? '#92400e' : ($sponsor->tier === 'official_sponsor' ? '#1e40af' : '#166534') }};
                            ">
                                {{ ['main_partner'=>'Main Partner','official_sponsor'=>'Official Sponsor','strategic_ally'=>'Alianza'][$sponsor->tier] ?? $sponsor->tier }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;">
                            @if ($sponsor->website_url)
                                <a href="{{ $sponsor->website_url }}" target="_blank" style="color:#1D428A;">{{ parse_url($sponsor->website_url, PHP_URL_HOST) }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:0.75rem;">{{ $sponsor->is_active ? '✓' : '—' }}</td>
                        <td style="padding:0.75rem;">{{ $sponsor->sort_order }}</td>
                        <td style="padding:0.75rem;display:flex;gap:0.5rem;">
                            <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;">Editar</a>
                            <form method="POST" action="{{ route('admin.sponsors.destroy', $sponsor) }}" onsubmit="return confirm('¿Eliminar patrocinador?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-button" style="font-size:0.8rem;padding:0.4rem 0.75rem;color:#991b1b;border-color:#fca5a5;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:1rem;">Sin patrocinadores registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
