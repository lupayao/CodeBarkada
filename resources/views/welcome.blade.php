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

        <div class="card p-4 mb-4">
            <h2 class="h4 mb-3">Your profile</h2>

            <div class="mb-4">
                <label class="form-label" for="codename">Codename</label>
                <input id="codename" name="codename" type="text" maxlength="18" required
                       value="{{ old('codename', $member->codename) }}" placeholder="e.g. ByteBayani"
                       aria-describedby="codename-help"
                       @class(['form-control', 'is-invalid' => $errors->has('codename')])>
                <div id="codename-help" class="form-text">Shown on the leaderboard and your social card (3–18 characters).</div>
                @error('codename')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <fieldset>
                <legend class="form-label fs-6">Learning track</legend>
                <div class="row g-3">
                    @foreach ($tracks as $key => $track)
                        <div class="col-sm-6">
                            {{-- btn-check hides the radio circle; the card after it shows the choice --}}
                            <input type="radio" class="btn-check" name="track" id="track-{{ $key }}" value="{{ $key }}" required
                                   @checked(old('track', $member->track ?? 'py') === $key)>
                            <label class="track-card" for="track-{{ $key }}">
                                <b>{{ $track['name'] }}</b>
                                <span class="small text-body-secondary">{{ $track['about'] }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('track')
                    <div class="text-danger-emphasis small mt-2">{{ $message }}</div>
                @enderror
            </fieldset>
        </div>

        {{-- "bot-pick": the CSS recolors the preview bot as soon as a swatch is clicked --}}
        <div class="card p-4 mb-4 bot-pick">
            <h2 class="h4 mb-3">Choose your coder bot</h2>
            <div class="d-flex flex-wrap align-items-center gap-4">
                <x-avatar :user="$member" size="96" />
                <fieldset>
                    <legend class="small text-body-secondary">Bot color</legend>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($colors as $color)
                            <input type="radio" class="btn-check" name="color" id="color-{{ $color }}" value="{{ $color }}" required
                                   @checked((int) old('color', $member->avatarColor()) === $color)>
                            <label class="swatch hue-{{ $color }}" for="color-{{ $color }}">
                                <span class="visually-hidden">Color {{ $color }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            </div>
            @error('color')
                <div class="text-danger-emphasis small mt-2">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary btn-lg" type="submit">Save and open the hub</button>
    </form>
@endsection
