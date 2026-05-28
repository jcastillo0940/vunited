@extends('layouts.admin.app')

@section('title', 'Create Product')

@section('content')
    <section>
        <h2 style="margin-top:0;">Create Product</h2>
        @include('admin.products.partials.form', [
            'action' => route('admin.products.store'),
            'method' => 'POST',
            'product' => $product,
            'categories' => $categories,
        ])
    </section>
@endsection
