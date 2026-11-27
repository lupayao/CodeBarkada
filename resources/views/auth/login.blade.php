@extends('layouts.app')

@section('title', 'Sign in')

{{-- Center the landing content in the space between the header and the footer --}}
@section('main_class', 'landing d-flex flex-column justify-content-center')

@section('content')
    <div class="row g-4 g-lg-5 align-items-center justify-content-between w-100 mx-0">
        <div class="col-lg-6 auth-pitch">
            <span class="kicker">PSHS-CRC · Alternative Learning Program</span>
            <h1 class="landing-title fw-bold my-3">Train together. Code together. Compete together.</h1>
            <p class="lead">The CodeBarkada training hub: build typing speed, check your coding profile, and prepare for ICSC, NOI.PH and HKICO.</p>
            <x-boat-bot size="280" waves label="The CodeBarkada boat-bot, a Balangay boat with a robot face, floating on the waves" />
        </div>

        <div class="col-lg-5">
            <div class="card p-4 p-xl-5 landing-card">
                <h2 class="h3 mb-3">Sign in</h2>

                <form method="POST" action="{{ route('login.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="email">School email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                               placeholder="name{{ $emailDomain }}" autocomplete="username" required autofocus
                               @class(['form-control', 'is-invalid' => $errors->has('email')])
                               @error('email') aria-describedby="email-error" @enderror>
                        {{-- Shows "Wrong email or password." and other problems --}}
                        @error('email')
                            <div id="email-error" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               @class(['form-control', 'is-invalid' => $errors->has('password')])
                               @error('password') aria-describedby="password-error" @enderror>
                        @error('password')
                            <div id="password-error" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                        <label class="form-check-label text-body-secondary" for="remember">
                            Keep me signed in on this device (not on shared lab PCs)
                        </label>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">Sign in</button>
                </form>
            </div>
        </div>
    </div>
@endsection
