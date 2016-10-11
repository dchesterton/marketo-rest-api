<?php

namespace CSD\Marketo\Tests;

use CSD\Marketo\Client;
use CSD\Marketo\Response\GetActivityTypesResponse;
use CSD\Marketo\Response\GetLeadActivityResponse;
use Guzzle\Http\Message\Response;
use Guzzle\Tests\GuzzleTestCase;
use Guzzle\Tests\Service\Mock\Command\MockCommand;

/**
 * @group marketo-rest-api
 */
class MarketoSoapClientTest extends GuzzleTestCase {

    /**
     * Gets the marketo rest client.
     *
     * @return \CSD\Marketo\ClientInterface
     */
    private function _getClient() {

        static $client = FALSE;

        if ($client) return $client;

        $client = Client::factory([
            'url' => $this->getServer()->getUrl(),
            'client_id' => 'example_id',
            'client_secret' => 'example_secret',
            'munchkin_id' => 'example_munchkin_id',
        ]);

        return $client;
    }

    public function testConstructor() {

        $client = $this->_getClient();

        $config = $client->getConfig()->getAll();


        self::assertNotEmpty($config['client_id'], 'The `marketo_client_id` environment variable is empty.');
        self::assertNotEmpty($config['client_secret'], 'The `marketo_client_secret` environment variable is empty.');
        self::assertNotEmpty($config['munchkin_id'], 'The `marketo_munchkin_id` environment variable is empty.');

        self::assertTrue($client instanceof \CSD\Marketo\Client);
    }

    public function testExecutesCommands()
    {
        $this->getServer()->flush();
        $this->getServer()->enqueue("HTTP/1.1 200 OK\r\nContent-Length: 0\r\n\r\n");

        $client = new Client($this->getServer()->getUrl());
        $cmd = new MockCommand();
        $client->execute($cmd);

        $this->assertInstanceOf('Guzzle\\Http\\Message\\Response', $cmd->getResponse());
        $this->assertInstanceOf('Guzzle\\Http\\Message\\Response', $cmd->getResult());
        $this->assertEquals(1, count($this->getServer()->getReceivedRequests(false)));
    }

    public function testGetCampaigns() {
        // Campaign response json.
        $response_json = '{"requestId": "f81c#157b104ca98","result": [{ "id": 1004, "name": "Foo", "description": " ", "type": "trigger", "workspaceName": "Default","createdAt": "2012-09-12T19:04:12Z","updatedAt": "2014-10-22T15:51:18Z","active": false}],"success": true}';
        // Queue up a response for getCampaigns as well as getCampaign (by ID).
        $this->getServer()->enqueue($this->generateResponses(200, [$response_json, $response_json], TRUE));

        $client = $this->_getClient();
        $campaigns = $client->getCampaigns()->getResult();

        self::assertNotEmpty($campaigns[0]['id']);
        $campaign = $client->getCampaign($campaigns[0]['id'])->getResult();
        self::assertNotEmpty($campaign[0]['name']);
        self::assertEquals($campaigns[0]['name'], $campaign[0]['name']);
    }

    public function testGetLists() {
        // Campaign response json.
        $response_json = '{"requestId":"5e2c#157b132e104","result":[{"id":1,"name":"Foo","description":"Foo description","programName":"Foo program name","workspaceName":"Default","createdAt":"2016-05-05T16:37:00Z","updatedAt":"2016-05-19T17:27:41Z"}],"success":true}';
        // Queue up a response for getLists as well as getList (by ID).
        $this->getServer()->enqueue($this->generateResponses(200, [$response_json, $response_json]));

        $client = $this->_getClient();
        $lists = $client->getLists()->getResult();

        self::assertNotEmpty($lists[0]['id']);
        $list = $client->getList($lists[0]['id'])->getResult();
        self::assertNotEmpty($list[0]['name']);
        self::assertEquals($lists[0]['name'], $list[0]['name']);
    }

    public function testLeadPartitions() {
        // Queue up a response for getLeadPartitions request.
        $this->getServer()->enqueue($this->generateResponses(200,'{"requestId":"984e#157b140b012","result":[{"id":1,"name":"Default","description":"Initial system lead partition"}],"success":true}'));

        $client = $this->_getClient();
        $partitions = $client->getLeadPartitions()->getResult();

        self::assertNotEmpty($partitions[0]['name']);
        self::assertEquals($partitions[0]['name'], 'Default');
    }

    public function testResponse() {
        // Queue up a response for getCampaigns request.
        $this->getServer()->enqueue($this->generateResponses(200,'{"requestId": "f81c#157b104ca98","result": [{ "id": 1004, "name": "Foo", "description": " ", "type": "trigger", "workspaceName": "Default","createdAt": "2012-09-12T19:04:12Z","updatedAt": "2014-10-22T15:51:18Z","active": false}],"success": true}'));

        $client = $this->_getClient();
        $response = $client->getCampaigns();

        self::assertTrue($response->isSuccess());
        self::assertNull($response->getError());
        self::assertNotEmpty($response->getRequestId());

        // No assertion but make sure getNextPageToken doesn't error out.
        $response->getNextPageToken();

        self::assertEquals(serialize($response->getResult()), serialize($response->getCampaigns()));
        // @todo: figure out how to rest \CSD\Marketo\Response::fromCommand().
    }

    public function testGetFields() {
        // Queue up a response for getFields request.
        $this->getServer()->enqueue($this->generateResponses(200,'{"requestId":"fb0#157b1501f31","result":[{"id":48,"displayName":"First Name","dataType":"string","length":255,"rest":{"name":"firstName","readOnly":false},"soap":{"name":"FirstName","readOnly":false}},{"id":50,"displayName":"Last Name","dataType":"string","length":255,"rest":{"name":"lastName","readOnly":false},"soap":{"name":"LastName","readOnly":false}},{"id":51,"displayName":"Email Address","dataType":"email","length":255,"rest":{"name":"email","readOnly":false},"soap":{"name":"Email","readOnly":false}},{"id":60,"displayName":"Address","dataType":"text","rest":{"name":"address","readOnly":false},"soap":{"name":"Address","readOnly":false}}],"success":true}'));

        $client = $this->_getClient();
        $response = $client->getFields();

        self::assertTrue($response->isSuccess());
        self::assertNull($response->getError());
        self::assertNotEmpty($response->getFields());
    }

    public function testGetActivityTypes() {
        // Queue up a response for getActivityTypes request.
        $this->getServer()->enqueue($this->generateResponses(200,'{"requestId":"6e78#148ad3b76f1","success":true,"result":[{"id":2,"name":"Fill Out Form","description":"User fills out and submits form on web page","primaryAttribute":{"name":"Webform ID","dataType":"integer"},"attributes":[{"name":"Client IP Address","dataType":"string"},{"name":"Form Fields","dataType":"text"},{"name":"Query Parameters","dataType":"string"},{"name":"Referrer URL","dataType":"string"},{"name":"User Agent","dataType":"string"},{"name":"Webpage ID","dataType":"integer"}]}]}'));

        $client = $this->_getClient();
        /** @var GetActivityTypesResponse $response */
        $response = $client->getActivityTypes();

        self::assertTrue($response->isSuccess());
        self::assertNull($response->getError());
        self::assertNotEmpty($response->getActivityTypes());
    }

    public function testGetLeadActivity() {
        // Queue up a response for getActivityTypes, getPagingToken and getLeadActivity requests.
        $this->getServer()->enqueue($this->generateResponses(200,[
            '{"requestId":"6e78#148ad3b76f1","success":true,"result":[{"id":2,"name":"Fill Out Form","description":"User fills out and submits form on web page","primaryAttribute":{"name":"Webform ID","dataType":"integer"},"attributes":[{"name":"Client IP Address","dataType":"string"},{"name":"Form Fields","dataType":"text"},{"name":"Query Parameters","dataType":"string"},{"name":"Referrer URL","dataType":"string"},{"name":"User Agent","dataType":"string"},{"name":"Webpage ID","dataType":"integer"}]}]}',
            '{"requestId":"f84c#157b16681eb","success":true,"nextPageToken":"JXBIK3O6SUWULQ12345678Y57ZJCBBZRGHQV57IZSKSLYLLU6PPQ===="}',
            '{"requestId":"24fd#15188a88d7f","result":[{"id":102988,"leadId":1,"activityDate":"2015-01-16T23:32:19Z","activityTypeId":1,"primaryAttributeValueId":71,"primaryAttributeValue":"localhost/munchkintest2.html","attributes":[{"name":"Client IP Address","value":"10.0.19.252"},{"name":"Query Parameters","value":""},{"name":"Referrer URL","value":""},{"name":"User Agent","value":"Mozilla/5.0(Windows NT6.1;WOW64)AppleWebKit/537.36(KHTML,like Gecko)Chrome/39.0.2171.95Safari/537.36"},{"name":"Webpage URL","value":"/munchkintest2.html"}]}],"success":true,"nextPageToken":"WQV2VQVPPCKHC6AQYVK7JDSA3J62DUSJ3EXJGDPTKPEBFW3SAVUA====","moreResult":false}',
        ]));

        $client = $this->_getClient();
        // Get activity types, needed for $activityTypesIds.
        $activity_types = $client->getActivityTypes()->getResult();
        // Get only the ids of the activity types.
        $activity_types_ids = array_map(function ($type) {return $type['id'];}, $activity_types);
        /** @var GetLeadActivityResponse $response */
        $response = $client->getLeadActivity($client->getPagingToken(date('c'))->getNextPageToken(), [1], array_slice($activity_types_ids, 0, 10));

        self::assertTrue($response->isSuccess());
        self::assertNull($response->getError());

        // No activity found in the sandbox so don't check the response for usable data.
//        self::assertNotEmpty($response->getLeadActivity());
    }

    protected function generateResponses($status_code, $response_data, $add_token_response = FALSE) {
        $responses = !$add_token_response  ? [] : [
            new Response(200, NULL, '{"access_token": "0f9cc479-30ae-4d7a-b850-53bd9d44de45:sj","token_type": "bearer","expires_in": 3599,"scope": "smuvva+apiuser@tibco.com"}'),
        ];

        foreach ((array) $response_data as $item) {
            $json_string = is_array($item) ? json_encode($item) : $item;
            $responses[] = new Response($status_code, NULL, $json_string);
        }

        return $responses;
    }
}
