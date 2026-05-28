@extends('layouts.admin.app')

@section('title', 'Edit Membership Plan')

@section('content')
    <section>
        <h2 style="margin-top:0;">Edit Membership Plan</h2>
        @include('admin.membership-plans.partials.form', [
            'action' => route('admin.membership-plans.update', $plan),
            'method' => 'PUT',
            'plan' => $plan,
        ])
    </section>
@endsection
