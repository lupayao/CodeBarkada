@extends('layouts.app')

@section('title', 'Change password')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card p-4">
                <h1 class="h3 mb-3">Change password</h1>

                @if ($mustChange)
                    <div class="alert alert-warning">You're still using a default password. Choose your own before you continue.</div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="current_password">Current password</label>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password" required
                               @class(['form-control', 'is-invalid' => $errors->has('current_password')])>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">New password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                               aria-describedby="password-help"
                               @class(['form-control', 'is-invalid' => $errors->has('password')])>
                        <div id="password-help" class="form-text">At least 8 characters.</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="password_confirmation">Type the new password again</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                               class="form-control">
                    </div>

                    <button class="btn btn-primary w-100" type="submit">Save new password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
