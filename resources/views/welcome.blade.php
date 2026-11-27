@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <div class="mb-4">
        <span class="kicker">First sign-in</span>
        <h1 class="mt-2">Welcome to the barkada!</h1>
        <p class="text-body-secondary">Pick a codename, a learning track and a coder bot. You can change these later in Profile.</p>
    </div>

    <form method="POST" action="{{ route('welcome.update') }}" novalidate>
        @csrf
        @method('PUT')

        {{-- Codename, track and coder bot (shared with the Profile page) --}}
        @include('partials.profile-fields')

        <button class="btn btn-primary btn-lg" type="submit">Save and open the hub</button>
    </form>
@endsection
