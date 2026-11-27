<?php

// CodeBarkada settings. Values come from .env so no passwords live in the code.
return [

    // Every account (adviser and members) must use an email ending with this.
    'email_domain' => '@crc.pshs.edu.ph',

    // The one adviser account, created by "php artisan db:seed --class=AdviserSeeder".
    'adviser' => [
        'name' => env('ADVISER_NAME', 'Club Adviser'),
        'email' => env('ADVISER_EMAIL'),
        'password' => env('ADVISER_PASSWORD'),
    ],

];
