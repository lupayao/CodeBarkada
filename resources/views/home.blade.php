@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="card p-4">
        <div class="d-flex align-items-center gap-3">
            <x-avatar :user="$member" size="64" />
            <div>
                <h1 class="h2 mb-1">Welcome, {{ $member->displayName() }}</h1>
                <p class="text-body-secondary mb-0">{{ $tagline }}</p>
            </div>
        </div>
        <p class="mt-3 mb-0"><a href="{{ route('password.edit') }}">Change my password</a></p>
    </div>
@endsection
