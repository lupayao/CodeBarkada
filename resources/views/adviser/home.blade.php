@extends('layouts.app')

@section('title', 'Adviser')

@section('content')
    <div class="card p-4">
        <span class="kicker">Adviser panel</span>
        <h1 class="h2 mt-2">Hello, {{ $adviser->displayName() }}</h1>
        <p class="text-body-secondary">Opening rounds and viewing results will appear here in the next steps.</p>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary" href="{{ route('adviser.members.index') }}">Add and view members</a>
            <a class="btn btn-outline-light" href="{{ route('password.edit') }}">Change my password</a>
        </div>
    </div>
@endsection
