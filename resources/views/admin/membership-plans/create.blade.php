@extends('layouts.admin.app')

@section('title', 'Create Membership Plan')

@section('content')
    <section>
        <h2 style="margin-top:0;">Create Membership Plan</h2>
        @include('admin.membership-plans.partials.form', [
            'action' => route('admin.membership-plans.store'),
            'method' => 'POST',
            'plan' => $plan,
        ])
    </section>
@endsection
