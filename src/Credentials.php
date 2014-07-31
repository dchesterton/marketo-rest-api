<?php
/*
 * This file is part of the Marketo REST API Client package.
 *
 * (c) 2014 Daniel Chesterton
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CSD\Marketo;

use CommerceGuys\Guzzle\Plugin\Oauth2\GrantType\GrantTypeInterface;
use Guzzle\Http\Client as GuzzleClient;

/**
 * Requests credentials from Marketo's identity service using the Client ID and Client Secret.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Credentials implements GrantTypeInterface
{
    /**
     * @var string
     */
    private $url;

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
        $this->url = $url;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenData()
    {
        $client = new GuzzleClient($this->url);

        $params = array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        );

        $request = $client->get('/identity/oauth/token', null, array('query' => $params));
        $data = $request->send()->json();

        if (!isset($data['access_token']) || !isset($data['expires_in'])) {
            throw new \Exception('Invalid Marketo credentials response.');
        }

        return array(
            'access_token' => $data['access_token'],
            'expires_in' => $data['expires_in']
        );
    }
}
