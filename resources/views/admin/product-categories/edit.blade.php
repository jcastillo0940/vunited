@extends('layouts.admin.app')

@section('title', 'Edit Product Category')

@section('content')
    <section>
        <h2 style="margin-top:0;">Edit Product Category</h2>
        @include('admin.product-categories.partials.form', [
            'action' => route('admin.product-categories.update', $category),
            'method' => 'PUT',
            'category' => $category,
        ])
    </section>
@endsection
