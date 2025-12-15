# Socialite Ravelry Oauth2 Provider

## Installation

`composer require willis84/socialite-ravelry`

Add your Ravelry credentials to your `config/services.php` array / `.env` file

```php
    'ravelry' => [
        'client_id' => env('RAVELRY_CLIENT_ID'),
        'client_secret' => env('RAVELRY_CLIENT_SECRET'),
        'redirect' => env('RAVELRY_REDIRECT_URI'),
    ],
```

Add the service provider to your `bootstrap/providers.php` file

```php

return [
    // ...
    SocialiteRavelry\ServiceProvider::class
];

```

Use the driver as follows

```php

$socialite = Socialite::driver('ravelry');

// get the redirect request to authenticate
$socialite->scopes(['offline'])->redirect();

// get the user when the user has approved your connection
$user = $socialite->user();

// refresh the oauth2 token, fetch the refresh token from your database
$socialite->scopes(['offline'])->refreshToken($refreshToken);

```

Please note, Ravelry does not provide the users email address, and in order to get the refresh token you will need
to request the offline scope. Requesting additional scopes are done space delimited: `$socialite->scopes(['offline patternstore-read'])`

Many thanks to Cassidy at Ravelry for all her help in understanding the Ravelry Oauth2 flow.

Ravelry API documentation can be found at [www.ravelry.com/api](https://www.ravelry.com/api)