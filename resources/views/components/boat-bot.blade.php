{{--
    The CodeBarkada mascot: a Balangay boat that is also a robot.
    Sails in the festival colors, a robot face on the hull, an antenna on the mast.
    It bobs on the water (CSS animation in school.css, stops if the visitor turns off motion).

    Use it like:
        <x-boat-bot size="34" />                         small, e.g. the header logo
        <x-boat-bot size="280" waves label="..." />      big, floating on waves

    Colors come from the classes (bb-...) in school.css, which use the theme variables.
--}}
@props(['size' => 40, 'waves' => false, 'label' => null])

<svg {{ $attributes->merge(['class' => 'boat-bot']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 200 160"
     @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" focusable="false" @endif>

    {{-- Everything inside this group bobs together --}}
    <g class="bb-boat">
        {{-- masts --}}
        <line class="bb-mast" x1="80" y1="12" x2="80" y2="100"/>
        <line class="bb-mast" x1="130" y1="16" x2="130" y2="100"/>

        {{-- left sail: blue, yellow and red bands like the poster --}}
        <polygon class="bb-blue" points="82,14 112,44 82,44"/>
        <polygon class="bb-yellow" points="82,44 112,44 124,56 122.1,68 82,68"/>
        <polygon class="bb-red" points="82,68 122.1,68 118,94 82,94"/>

        {{-- right sail: yellow with a blue wave, red at the bottom --}}
        <polygon class="bb-yellow" points="132,18 182,68 179.9,78 132,78"/>
        <polygon class="bb-red" points="132,78 179.9,78 176,96 132,96"/>
        <path class="bb-swirl" d="M142 64 q6 -16 20 -11 q-8 1 -7 8 q6 -5 12 0"/>

        {{-- robot antenna on top of the left mast --}}
        <circle class="bb-antenna" cx="80" cy="9" r="5"/>

        {{-- outrigger: two arms and a long float under the boat --}}
        <line class="bb-arm" x1="60" y1="116" x2="46" y2="138"/>
        <line class="bb-arm" x1="140" y1="116" x2="154" y2="138"/>
        <line class="bb-float" x1="16" y1="138" x2="184" y2="138"/>

        {{-- hull with curled-up ends --}}
        <path class="bb-hull" d="M22 100 L178 100 Q172 124 148 128 L52 128 Q28 124 22 100 Z"/>
        <path class="bb-prow" d="M22 100 Q10 88 16 74"/>
        <path class="bb-prow" d="M178 100 Q190 88 184 74"/>
        <line class="bb-trim" x1="26" y1="101.5" x2="174" y2="101.5"/>

        {{-- robot face: a dark visor with glowing eyes, and a smile --}}
        <rect class="bb-visor" x="74" y="105" width="52" height="13" rx="6.5"/>
        <circle class="bb-eye" cx="89" cy="111.5" r="3.6"/>
        <circle class="bb-eye" cx="111" cy="111.5" r="3.6"/>
        <path class="bb-smile" d="M91 121 q9 5 18 0"/>
    </g>

    {{-- water (only on the big version) --}}
    @if ($waves)
        {{-- the water fades out at both ends instead of stopping in hard corners --}}
        <defs>
            <linearGradient id="bb-water-fade" x1="0" x2="1" y1="0" y2="0">
                <stop offset="0" stop-color="#fff" stop-opacity="0"/>
                <stop offset=".18" stop-color="#fff" stop-opacity="1"/>
                <stop offset=".82" stop-color="#fff" stop-opacity="1"/>
                <stop offset="1" stop-color="#fff" stop-opacity="0"/>
            </linearGradient>
            <mask id="bb-water-mask"><rect x="-10" y="120" width="220" height="40" fill="url(#bb-water-fade)"/></mask>
        </defs>
        <g mask="url(#bb-water-mask)">
            <path class="bb-water" d="M-10 140 q12 -6 25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 V160 H-10 Z"/>
            <path class="bb-water bb-water-front" d="M-10 148 q12 -5 25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 t25 0 V160 H-10 Z"/>
        </g>
    @endif
</svg>
