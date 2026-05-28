@extends('layouts.admin.app')

@section('title', 'Edit Product')

@section('content')
    <section>
        <h2 style="margin-top:0;">Edit Product</h2>
        @include('admin.products.partials.form', [
            'action' => route('admin.products.update', $product),
            'method' => 'PUT',
            'product' => $product,
            'categories' => $categories,
        ])
    </section>
@endsection
