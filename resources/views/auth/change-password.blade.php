@extends('layouts.app')

@section('title', 'Change password')

@section('content')
    <div class="card auth-card narrow">
        <h2>Change password</h2>

        @if ($mustChange)
            <p class="banner warn">You're still using a default password. Choose your own before you continue.</p>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')

            <label class="f" for="current_password">Current password
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
            </label>
            <p class="auth-err" role="alert">{{ $errors->first('current_password') }}</p>

            <label class="f" for="password">New password (at least 8 characters)
                <input id="password" name="password" type="password" autocomplete="new-password" required>
            </label>

            <label class="f" for="password_confirmation">Type the new password again
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            </label>
            <p class="auth-err" role="alert">{{ $errors->first('password') }}</p>

            <button class="btn pri" type="submit">Save new password</button>
        </form>
    </div>
@endsection
