@extends('layouts.admin.app')

@section('title', 'Product Categories')

@section('content')
    <section>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.5rem;">
            <div>
                <h2 style="margin:0;">Product Categories</h2>
                <p style="margin:0.5rem 0 0; color:#475569;">Gestiona las categorias publicas de la tienda.</p>
            </div>
            @if(auth('admin')->user()->hasPermission('product_categories.manage'))
                <a href="{{ route('admin.product-categories.create') }}" class="admin-button">Create category</a>
            @endif
        </div>

        @if (session('error'))
            <div style="margin-bottom: 1rem; padding: 1rem; border-radius: 0.75rem; background: #fee2e2; color: #991b1b;">
                {{ session('error') }}
            </div>
        @endif

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; border-bottom:1px solid #e2e8f0;">
                        <th style="padding:0.75rem;">Order</th>
                        <th style="padding:0.75rem;">Name</th>
                        <th style="padding:0.75rem;">Slug</th>
                        <th style="padding:0.75rem;">Products</th>
                        <th style="padding:0.75rem;">Status</th>
                        <th style="padding:0.75rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:0.75rem;">{{ $category->sort_order }}</td>
                            <td style="padding:0.75rem;">{{ $category->name }}</td>
                            <td style="padding:0.75rem;">{{ $category->slug }}</td>
                            <td style="padding:0.75rem;">{{ $category->products_count }}</td>
                            <td style="padding:0.75rem;">{{ $category->is_active ? 'Active' : 'Inactive' }}</td>
                            <td style="padding:0.75rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
                                <a href="{{ route('admin.product-categories.edit', $category) }}" class="admin-button">Edit</a>
                                <form method="POST" action="{{ route('admin.product-categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:1rem; color:#64748b;">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
