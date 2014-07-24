<?php
namespace CSD\Marketo;

// Guzzle
use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use Guzzle\Common\Collection;
use Guzzle\Service\Client as GuzzleClient;
use Guzzle\Service\Description\ServiceDescription;

// Response classes
use CSD\Marketo\Response\AddOrRemoveLeadsToListResponse;
use CSD\Marketo\Response\CreateOrUpdateLeadsResponse;
use CSD\Marketo\Response\GetCampaignResponse;
use CSD\Marketo\Response\GetCampaignsResponse;
use CSD\Marketo\Response\GetLeadResponse;
use CSD\Marketo\Response\GetLeadsResponse;
use CSD\Marketo\Response\GetListResponse;
use CSD\Marketo\Response\GetListsResponse;
use CSD\Marketo\Response\IsMemberOfListResponse;

/**
 * Guzzle client for communicating with the Marketo REST API.
 *
 * @see http://developers.marketo.com/documentation/rest/
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Client extends GuzzleClient
{
    /**
     * {@inheritdoc}
     */
    public static function factory($config = array())
    {
        $default = [
            'base_url' => false,
            'subdomain' => false,
            'version' => 1
        ];

        $required = ['client_id', 'client_secret', 'version'];
        $config = Collection::fromConfig($config, $default, $required);

        $baseUrl = $config->get('base_url');

        if (!$baseUrl) {
            $subdomain = $config->get('subdomain');

            if (!$subdomain) {
                throw new \Exception('Must provide either base URL or Marketo subdomain.');
            }

            $baseUrl = sprintf('https://%s.mktorest.com', $subdomain);
        }

        $grantType = new Credentials($baseUrl, $config->get('client_id'), $config->get('client_secret'));
        $auth = new Oauth2Plugin($grantType);

        $restUrl = sprintf('%s/rest/v%d', $baseUrl, $config->get('version'));

        $client = new self($restUrl, $config);
        $client->addSubscriber($auth);
        $client->setDescription(ServiceDescription::factory(__DIR__ . '/service.json'));

        return $client;
    }

    /**
     * @param array  $leads
     * @param string $lookupField
     * @param array  $args
     *
     * @return CreateOrUpdateLeadsResponse
     */
    public function createLeads($leads, $lookupField = null, $args = [])
    {
        $args['input'] = $leads;
        $args['action'] = 'createOnly';

        if (isset($lookupField)) {
            $args['lookupField'] = $lookupField;
        }

        return $this->getResult('createOrUpdateLeads', $args);
    }

    /**
     * @param array  $leads
     * @param string $lookupField
     * @param array  $args
     *
     * @return CreateOrUpdateLeadsResponse
     */
    public function createOrUpdateLeads($leads, $lookupField = null, $args = [])
    {
        $args['input'] = $leads;
        $args['action'] = 'createOrUpdate';

        if (isset($lookupField)) {
            $args['lookupField'] = $lookupField;
        }

        return $this->getResult('createOrUpdateLeads', $args);
    }

    /**
     * @param array  $leads
     * @param string $lookupField
     * @param array  $args
     *
     * @return CreateOrUpdateLeadsResponse
     */
    public function updateLeads($leads, $lookupField = null, $args = [])
    {
        $args['input'] = $leads;
        $args['action'] = 'updateOnly';

        if (isset($lookupField)) {
            $args['lookupField'] = $lookupField;
        }

        return $this->getResult('createOrUpdateLeads', $args);
    }

    /**
     * @param array  $leads
     * @param string $lookupField
     * @param array  $args
     *
     * @return CreateOrUpdateLeadsResponse
     */
    public function createDuplicateLeads($leads, $lookupField = null, $args = [])
    {
        $args['input'] = $leads;
        $args['action'] = 'createDuplicate';

        if (isset($lookupField)) {
            $args['lookupField'] = $lookupField;
        }

        return $this->getResult('createOrUpdateLeads', $args);
    }

    /**
     * @param int|array $ids
     * @param array     $args
     * @return GetListsResponse
     */
    public function getLists($ids = null, $args = [])
    {
        if ($ids) {
            $args['id'] = $ids;
        }

        return $this->getResult('getLists', $args, is_array($ids));
    }

    /**
     * @param int   $id
     * @param array $args
     * @return GetListResponse
     */
    public function getList($id, $args = [])
    {
        $args['id'] = $id;

        return $this->getResult('getList', $args);
    }

    /**
     * @param string $filterType
     * @param string $filterValues
     * @param array  $args
     *
     * @return GetLeadsResponse
     */
    public function getLeadsByFilterType($filterType, $filterValues, $args = [])
    {
        $args['filterType'] = $filterType;
        $args['filterValues'] = $filterValues;

        return $this->getResult('getLeadsByFilterType', $args);
    }

    /**
     * @param string $type
     * @param string $value
     * @param array  $args
     * @return GetLeadResponse
     */
    public function getLeadByFilterType($type, $value, $args = [])
    {
        $args['filterType'] = $type;
        $args['filterValues'] = $value;

        return $this->getResult('getLeadByFilterType', $args);
    }

    /**
     * @param int   $listId
     * @param array $args
     *
     * @return GetLeadsResponse
     */
    public function getLeadsByList($listId, $args = [])
    {
        $args['listId'] = $listId;

        return $this->getResult('getLeadsByList', $args);
    }

    /**
     * @param int   $id
     * @param array $args
     *
     * @return GetLeadResponse
     */
    public function getLead($id, $args = [])
    {
        $args['id'] = $id;
        return $this->getResult('getLead', $args);
    }

    /**
     * @param int       $listId
     * @param int|array $id
     * @param array     $args
     *
     * @return IsMemberOfListResponse
     */
    public function isMemberOfList($listId, $id, $args = [])
    {
        $args['listId'] = $listId;
        $args['id'] = $id;

        return $this->getResult('isMemberOfList', $args, is_array($id));
    }

    /**
     * @param int   $id
     * @param array $args
     *
     * @return GetCampaignResponse
     */
    public function getCampaign($id, $args = [])
    {
        $args['id'] = $id;

        return $this->getResult('getCampaign', $args);
    }

    /**
     * @param int|array $ids
     * @param array     $args
     *
     * @return GetCampaignsResponse
     */
    public function getCampaigns($ids = null, $args = [])
    {
        if ($ids) {
            $args['id'] = $ids;
        }

        return $this->getResult('getCampaigns', $args, is_array($ids));
    }

    /**
     * @param int       $listId
     * @param int|array $leads
     * @param array     $args
     *
     * @return AddOrRemoveLeadsToListResponse
     */
    public function addLeadsToList($listId, $leads, $args = [])
    {
        $args['listId'] = $listId;
        $args['id'] = (array) $leads;

        return $this->getResult('addLeadsToList', $args, true);
    }

    /**
     * @param int       $listId
     * @param int|array $leads
     * @param array     $args
     *
     * @return AddOrRemoveLeadsToListResponse
     */
    public function removeLeadsFromList($listId, $leads, $args = [])
    {
        $args['listId'] = $listId;
        $args['id'] = (array) $leads;

        return $this->getResult('removeLeadsToList', $args, true);
    }

    /**
     * @param string $command
     * @param array  $args
     * @param bool   $fixArgs
     *
     * @return Response
     */
    private function getResult($command, $args, $fixArgs = false)
    {
        $cmd = $this->getCommand($command, $args);

        // Marketo expects parameter arrays in the format id=1&id=2, Guzzle formats them as id[0]=1&id[1]=2.
        // Use a quick regex to fix it where necessary.
        if ($fixArgs) {
            $cmd->prepare();

            $url = preg_replace('/id%5B([0-9]+)%5D/', 'id', $cmd->getRequest()->getUrl());
            $cmd->getRequest()->setUrl($url);
        }

        return $cmd->getResult();
    }
}
