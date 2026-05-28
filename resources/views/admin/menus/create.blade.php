@extends('layouts.admin.app')

@section('title', 'Create Menu')

@section('content')
    <section>
        <h2 style="margin-top: 0;">Create Menu</h2>

        <form method="POST" action="{{ route('admin.menus.store') }}" style="display: grid; gap: 1rem;">
            @csrf
            <label style="display: grid; gap: 0.35rem;">
                <span>Name</span>
                <input type="text" name="name" value="{{ old('name') }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Location</span>
                <select name="location">
                    <option value="header" @selected(old('location') === 'header')>Header</option>
                    <option value="footer" @selected(old('location') === 'footer')>Footer</option>
                </select>
            </label>

            <label style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                <span>Active</span>
            </label>

            <div>
                <button type="submit" class="admin-button">Save menu</button>
            </div>
        </form>
    </section>
@endsection
