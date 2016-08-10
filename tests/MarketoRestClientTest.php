<?php

namespace CSD\Marketo\Tests;

use CSD\Marketo\Client;
use CSD\Marketo\Response\GetActivityTypesResponse;
use CSD\Marketo\Response\GetLeadActivityResponse;

class MarketoSoapClientTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {

        parent::setUp();
    }

    /**
     * Gets the marketo rest client.
     *
     * @return \CSD\Marketo\ClientInterface
     */
    private function _getClient() {

        static $client = FALSE;

        if ($client) return $client;

        $client = Client::factory([
            'client_id' => getenv('marketo_client_id'),
            'client_secret' => getenv('marketo_client_secret'),
            'munchkin_id' => getenv('marketo_munchkin_id'),
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

    public function testGetCampaigns() {
        $client = $this->_getClient();
        $campaigns = $client->getCampaigns()->getResult();

        self::assertNotEmpty($campaigns[0]['id']);
        $campaign = $client->getCampaign($campaigns[0]['id'])->getResult();
        self::assertNotEmpty($campaign[0]['name']);
        self::assertEquals($campaigns[0]['name'], $campaign[0]['name']);
    }

    public function testGetLists() {
        $client = $this->_getClient();
        $lists = $client->getLists()->getResult();

        self::assertNotEmpty($lists[0]['id']);
        $list = $client->getList($lists[0]['id'])->getResult();
        self::assertNotEmpty($list[0]['name']);
        self::assertEquals($lists[0]['name'], $list[0]['name']);
    }

    public function testLeadPartitions() {
        $client = $this->_getClient();
        $partitions = $client->getLeadPartitions()->getResult();

        self::assertNotEmpty($partitions[0]['name']);
        self::assertEquals($partitions[0]['name'], 'Default');
    }

    public function testResponse() {
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
        $client = $this->_getClient();
        $response = $client->getFields();

        self::assertTrue($response->isSuccess());
        self::assertNull($response->getError());
        self::assertNotEmpty($response->getFields());
    }

    public function testGetActivityTypes() {
        $client = $this->_getClient();
        /** @var GetActivityTypesResponse $response */
        $response = $client->getActivityTypes();

        self::assertTrue($response->isSuccess());
        self::assertNull($response->getError());
        self::assertNotEmpty($response->getActivityTypes());
    }

    public function testGetLeadActivity() {
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
}
