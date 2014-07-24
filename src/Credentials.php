<?php
namespace CSD\Marketo;

use CommerceGuys\Guzzle\Plugin\Oauth2\GrantType\GrantTypeInterface;
use Guzzle\Http\Client;

/**
 * Requests credentials from Marketo's identity service using the Client ID and Client Secret.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Credentials implements GrantTypeInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @param string $url
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct($url, $clientId, $clientSecret)
    {
        $this->client = new Client($url);
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenData()
    {
        $params = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ];

        $request = $this->client->get('/identity/oauth/token', null, ['query' => $params]);
        $data = $request->send()->json();

        return [
            'access_token' => $data['access_token'],
            'expires_in' => $data['expires_in']
        ];
    }
}
