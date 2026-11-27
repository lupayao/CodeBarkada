@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
    <div class="auth-in">
        <div class="auth-pitch">
            <span class="kicker">PSHS-CRC · Alternative Learning Program</span>
            <h1>Train together. Code together. Compete together.</h1>
            <p class="lead">The CodeBarkada training hub: build typing speed, check your coding profile, and prepare for ICSC, NOI.PH and HKICO.</p>
        </div>

        <div class="card auth-card">
            <h2>Sign in</h2>

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <label class="f" for="email">School email
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                           placeholder="name{{ $emailDomain }}" autocomplete="username" required autofocus>
                </label>

                <label class="f" for="password">Password
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                </label>

                <label class="keep" for="remember">
                    <input id="remember" name="remember" type="checkbox" value="1">
                    Keep me signed in on this device (not on shared lab PCs)
                </label>

                {{-- Shows "Wrong email or password." and other problems --}}
                <p class="auth-err" role="alert">{{ $errors->first('email') ?: $errors->first('password') }}</p>

                <button class="btn pri" type="submit">Sign in</button>
            </form>
        </div>
    </div>
@endsection
