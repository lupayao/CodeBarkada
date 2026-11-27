@extends('layouts.app')

@section('title', 'Manage '.$member->name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('adviser.members.index') }}" class="small">&larr; Back to members</a>
        <h1 class="mt-2">Manage member</h1>
    </div>

    <div class="row g-4">
        {{-- 1. Profile --}}
        <div class="col-lg-5">
            <div class="card p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <x-avatar :user="$member" size="64" />
                    <div>
                        <h2 class="h4 mb-1">{{ $member->displayName() }}</h2>
                        <span class="badge rounded-pill tone-{{ $status['tone'] }}">{{ $status['label'] }}</span>
                    </div>
                </div>

                <dl class="row mb-0 small">
                    <dt class="col-4 text-body-secondary fw-normal">Name</dt>
                    <dd class="col-8">{{ $member->name }}</dd>

                    <dt class="col-4 text-body-secondary fw-normal">Email</dt>
                    <dd class="col-8 text-break">{{ $member->email }}</dd>

                    <dt class="col-4 text-body-secondary fw-normal">Codename</dt>
                    <dd class="col-8">{{ $member->codename ?? '–' }}</dd>

                    <dt class="col-4 text-body-secondary fw-normal">Grade</dt>
                    <dd class="col-8">{{ $member->grade ?? '–' }}{{ $member->section ? '-'.$member->section : '' }}</dd>

                    <dt class="col-4 text-body-secondary fw-normal">Track</dt>
                    <dd class="col-8">{{ $tracks[$member->track]['name'] ?? '–' }}</dd>

                    <dt class="col-4 text-body-secondary fw-normal">Added</dt>
                    <dd class="col-8 mb-0">{{ $member->created_at->format('M j, Y') }}</dd>
                </dl>
            </div>
        </div>

        <div class="col-lg-7 d-flex flex-column gap-4">
            {{-- 2. Reset password --}}
            <div class="card p-4">
                <h2 class="h5">Reset password</h2>
                <p class="small text-body-secondary">
                    Sets a new default password. {{ $member->name }} must change it at the next sign-in,
                    and is signed out on every device right away.
                </p>

                <form method="POST" action="{{ route('adviser.members.password', $member) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <label class="form-label" for="default_password">New default password</label>
                    <div class="input-group has-validation">
                        <input id="default_password" name="default_password" type="text" autocomplete="off" spellcheck="false" required
                               aria-describedby="reset-help"
                               @class(['form-control', 'is-invalid' => $errors->has('default_password')])>
                        <button class="btn btn-primary" type="submit">Reset password</button>
                        @error('default_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="reset-help" class="form-text">At least 8 characters.</div>
                </form>
            </div>

            {{-- 3. Delete member --}}
            <div class="card p-4 danger-zone">
                <h2 class="h5">Delete member</h2>
                <p class="small text-body-secondary">Removes the account and all of the member's data.</p>

                <form method="POST" action="{{ route('adviser.members.destroy', $member) }}" novalidate>
                    @csrf
                    @method('DELETE')

                    <div class="form-check mb-3">
                        <input id="confirm" name="confirm" type="checkbox" value="1" required
                               @class(['form-check-input', 'is-invalid' => $errors->has('confirm')])>
                        <label class="form-check-label" for="confirm">
                            Yes, delete {{ $member->name }} and all their data. This can't be undone.
                        </label>
                        @error('confirm')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-danger" type="submit">Delete member</button>
                </form>
            </div>
        </div>
    </div>
@endsection
