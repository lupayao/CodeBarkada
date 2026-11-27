@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="mb-4">
        <span class="kicker">Member</span>
        <h1 class="mt-2">My profile</h1>
    </div>

    <div class="row g-4">
        <div class="col-lg-4 d-flex flex-column gap-4">
            {{-- 1. Details the adviser manages (read only) --}}
            <div class="card p-4">
                <h2 class="h5 mb-3">My details</h2>
                <dl class="row mb-0 small">
                    <dt class="col-4 text-body-secondary fw-normal">Name</dt>
                    <dd class="col-8">{{ $member->name }}</dd>

                    <dt class="col-4 text-body-secondary fw-normal">Email</dt>
                    <dd class="col-8 text-break">{{ $member->email }}</dd>

                    <dt class="col-4 text-body-secondary fw-normal">Grade</dt>
                    <dd class="col-8 mb-0">{{ $member->grade ?? '–' }}{{ $member->section ? '-'.$member->section : '' }}</dd>
                </dl>
                <p class="form-text mb-0 mt-3">Something wrong here? Ask the club adviser to fix it.</p>
            </div>

            {{-- 3. Password --}}
            <div class="card p-4">
                <h2 class="h5">Password</h2>
                <p class="small text-body-secondary">Changing it signs you out on your other devices.</p>
                <div>
                    <a class="btn btn-outline-light" href="{{ route('password.edit') }}">Change password</a>
                </div>
            </div>
        </div>

        {{-- 2. Codename, track and coder bot (same fields as the welcome page) --}}
        <div class="col-lg-8">
            <form method="POST" action="{{ route('profile.update') }}" novalidate>
                @csrf
                @method('PUT')

                @include('partials.profile-fields')

                <button class="btn btn-primary" type="submit">Save changes</button>
            </form>
        </div>
    </div>
@endsection
