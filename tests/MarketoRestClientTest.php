<?php

namespace CSD\Marketo\Tests;

use CSD\Marketo\Client;

class MarketoSoapClientTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {

        parent::setUp();
    }

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


        $this->assertNotEmpty($config['client_id'], 'The `marketo_client_id` environment variable is empty.');
        $this->assertNotEmpty($config['client_secret'], 'The `marketo_client_secret` environment variable is empty.');
        $this->assertNotEmpty($config['munchkin_id'], 'The `marketo_munchkin_id` environment variable is empty.');

        $this->assertTrue($client instanceof \CSD\Marketo\Client);
    }

    public function testGetCampaigns() {
        $client = $this->_getClient();
        $campaigns = $client->getCampaigns()->getResult();

        $this->assertNotEmpty($campaigns[0]['id']);
        $campaign = $client->getCampaign($campaigns[0]['id'])->getResult();
        $this->assertNotEmpty($campaign[0]['name']);
        $this->assertEquals($campaigns[0]['name'], $campaign[0]['name']);
    }

    public function testGetLists() {
        $client = $this->_getClient();
        $lists = $client->getLists()->getResult();

        $this->assertNotEmpty($lists[0]['id']);
        $list = $client->getList($lists[0]['id'])->getResult();
        $this->assertNotEmpty($list[0]['name']);
        $this->assertEquals($lists[0]['name'], $list[0]['name']);
    }

    public function testLeadPartitions() {
        $client = $this->_getClient();
        $partitions = $client->getLeadPartitions()->getResult();

        $this->assertNotEmpty($partitions[0]['name']);
        $this->assertEquals($partitions[0]['name'], 'Default');
    }

    public function testResponse() {
        $client = $this->_getClient();
        $response = $client->getCampaigns();

        $this->assertTrue($response->isSuccess());
        $this->assertNull($response->getError());
        $this->assertNotEmpty($response->getRequestId());

        // No assertion but make sure getNextPageToken doesn't error out.
        $response->getNextPageToken();

        $this->assertEquals(serialize($response->getResult()), serialize($response->getCampaigns()));
        // @todo: figure out how to rest \CSD\Marketo\Response::fromCommand().
    }

    public function testGetFields() {
        $client = $this->_getClient();
        $response = $client->getFields();

        $this->assertTrue($response->isSuccess());
        $this->assertNull($response->getError());
        $this->assertNotEmpty($response->getFields());
    }
}
