@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="card">
        <h1>Welcome, {{ $member->displayName() }}</h1>
        <p class="muted">{{ $tagline }}</p>
        <p><a href="{{ route('password.edit') }}">Change my password</a></p>
    </div>
@endsection
