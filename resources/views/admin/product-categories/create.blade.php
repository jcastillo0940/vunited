@extends('layouts.admin.app')

@section('title', 'Create Product Category')

@section('content')
    <section>
        <h2 style="margin-top:0;">Create Product Category</h2>
        @include('admin.product-categories.partials.form', [
            'action' => route('admin.product-categories.store'),
            'method' => 'POST',
            'category' => $category,
        ])
    </section>
@endsection
