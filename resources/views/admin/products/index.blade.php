@extends('layouts.admin.app')

@section('title', 'Products')

@section('content')
    <section>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.5rem;">
            <div>
                <h2 style="margin:0;">Products</h2>
                <p style="margin:0.5rem 0 0; color:#475569;">Gestiona el catalogo publico real de la tienda.</p>
            </div>
            @if(auth('admin')->user()->hasPermission('products.manage'))
                <a href="{{ route('admin.products.create') }}" class="admin-button">Create product</a>
            @endif
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; border-bottom:1px solid #e2e8f0;">
                        <th style="padding:0.75rem;">Order</th>
                        <th style="padding:0.75rem;">Name</th>
                        <th style="padding:0.75rem;">Category</th>
                        <th style="padding:0.75rem;">Price</th>
                        <th style="padding:0.75rem;">Featured</th>
                        <th style="padding:0.75rem;">Status</th>
                        <th style="padding:0.75rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:0.75rem;">{{ $product->sort_order }}</td>
                            <td style="padding:0.75rem;">{{ $product->name }}</td>
                            <td style="padding:0.75rem;">{{ $product->category?->name ?? 'Sin categoria' }}</td>
                            <td style="padding:0.75rem;">{{ number_format((float) $product->price, 2) }} {{ $product->currency }}</td>
                            <td style="padding:0.75rem;">{{ $product->is_featured ? 'Yes' : 'No' }}</td>
                            <td style="padding:0.75rem;">{{ $product->is_active ? 'Active' : 'Inactive' }}</td>
                            <td style="padding:0.75rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
                                <a href="{{ route('admin.products.edit', $product) }}" class="admin-button">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:1rem; color:#64748b;">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
