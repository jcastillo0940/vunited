@extends('layouts.admin.app')

@section('title', 'Edit Menu')

@section('content')
    <section style="display: grid; gap: 2rem;">
        <div>
            <h2 style="margin-top: 0;">Edit Menu</h2>

            <form method="POST" action="{{ route('admin.menus.update', $menu) }}" style="display: grid; gap: 1rem;">
                @csrf
                @method('PUT')
                <label style="display: grid; gap: 0.35rem;">
                    <span>Name</span>
                    <input type="text" name="name" value="{{ old('name', $menu->name) }}">
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Location</span>
                    <select name="location">
                        <option value="header" @selected(old('location', $menu->location) === 'header')>Header</option>
                        <option value="footer" @selected(old('location', $menu->location) === 'footer')>Footer</option>
                    </select>
                </label>

                <label style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $menu->is_active))>
                    <span>Active</span>
                </label>

                <div>
                    <button type="submit" class="admin-button">Update menu</button>
                </div>
            </form>
        </div>

        <div>
            <h3>Add Item</h3>

            <form method="POST" action="{{ route('admin.menus.items.store', $menu) }}" style="display: grid; gap: 1rem;">
                @csrf
                <label style="display: grid; gap: 0.35rem;">
                    <span>Parent item</span>
                    <select name="parent_id">
                        <option value="">No parent</option>
                        @foreach ($menu->items as $item)
                            <option value="{{ $item->id }}">{{ $item->label }}</option>
                        @endforeach
                    </select>
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Label</span>
                    <input type="text" name="label">
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>URL</span>
                    <input type="text" name="url">
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Target</span>
                    <input type="text" name="target" value="_self">
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Sort order</span>
                    <input type="number" name="sort_order" value="0">
                </label>

                <label style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>Active</span>
                </label>

                <div>
                    <button type="submit" class="admin-button">Add item</button>
                </div>
            </form>
        </div>

        <div>
            <h3>Existing Items</h3>

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Label</th>
                        <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">URL</th>
                        <th style="text-align: left; border-bottom: 1px solid #d1d5db; padding: 0.75rem;">Order</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menu->items as $item)
                        <tr>
                            <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $item->label }}</td>
                            <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $item->url }}</td>
                            <td style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem;">{{ $item->sort_order }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="padding: 0.75rem;">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
