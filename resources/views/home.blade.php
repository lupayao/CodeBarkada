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
        <div class="mt-3">
            <a class="btn btn-outline-light" href="{{ route('profile.edit') }}">Edit profile</a>
        </div>
    </div>
@endsection
