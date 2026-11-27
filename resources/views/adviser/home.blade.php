@extends('layouts.app')

@section('title', 'Adviser')

@section('content')
    <div class="card">
        <span class="kicker">Adviser panel</span>
        <h1>Hello, {{ $adviser->displayName() }}</h1>
        <p class="muted">Adding members, opening rounds and viewing results will appear here in the next steps.</p>
        <p><a href="{{ route('password.edit') }}">Change my password</a></p>
    </div>
@endsection
