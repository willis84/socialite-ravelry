<?php

namespace SocialiteRavelry;

use GuzzleHttp\RequestOptions;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class RavelryProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The Ravelry base URL.
     *
     * @var string
     */
    protected $baseUrl = 'https://api.ravelry.com';

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string  $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            url: 'https://www.ravelry.com/oauth2/auth',
            state: $state
        );
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return 'https://www.ravelry.com/oauth2/token';
    }

    /**
     * Get the user by token.
     *
     * @param  string  $token
     * @return array
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            uri: $this->baseUrl.'/current_user.json',
            options: [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ],
            ]);

        return json_decode($response->getBody()->getContents(), true)['user'];
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw(
            user: $user
        )->map(
            attributes: [
                'id' => $user['id'],
                'nickname' => $user['username'],
                'name' => $user['first_name'] ?? $user['username'],
                'email' => null, // Ravelry API does not provide email addresses
                'avatar' => $user['photo_url'] ?? $user['large_photo_url'] ?? null,
            ]
        );
    }

    /**
     * Make a request to the Ravelry API to get an access token
     *
     * @param  mixed  $code
     * @return array
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post(
            uri: $this->getTokenUrl(),
            options: [
                RequestOptions::AUTH => [$this->clientId, $this->clientSecret],
                RequestOptions::FORM_PARAMS => $this->getTokenFields($code),
            ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Make a request to refresh the access token
     *
     * @param  string  $refreshToken
     * @return array
     */
    public function getRefreshTokenResponse($refreshToken)
    {
        $response = $this->getHttpClient()->post(
            uri: $this->getTokenUrl(),
            options: [
                RequestOptions::AUTH => [
                    $this->clientId,
                    $this->clientSecret,
                ],
                RequestOptions::FORM_PARAMS => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'redirect' => $this->redirectUrl,
                ],
            ]);

        return json_decode($response->getBody(), true);
    }
}
