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

// Guzzle
use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use Guzzle\Common\Collection;
use Guzzle\Service\Client as GuzzleClient;
use Guzzle\Service\Description\ServiceDescription;

/**
 * Guzzle client for communicating with the Marketo.com REST API.
 *
 * @link http://developers.marketo.com/documentation/rest/
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Client extends GuzzleClient implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public static function factory($config = array())
    {
        $default = array(
            'url' => false,
            'munchkin_id' => false,
            'version' => 1,
            'bulk' => false
        );

        $required = array('client_id', 'client_secret', 'version');
        $config = Collection::fromConfig($config, $default, $required);

        $url = $config->get('url');

        if (!$url) {
            $munchkin = $config->get('munchkin_id');

            if (!$munchkin) {
                throw new \Exception('Must provide either a URL or Munchkin code.');
            }

            $url = sprintf('https://%s.mktorest.com', $munchkin);
        }

        $grantType = new Credentials($url, $config->get('client_id'), $config->get('client_secret'));
        $auth = new Oauth2Plugin($grantType);

        if ($config->get('bulk') === true) {
            $restUrl = sprintf('%s/bulk/v%d', rtrim($url, '/'), $config->get('version'));
        } else {
            $restUrl = sprintf('%s/rest/v%d', rtrim($url, '/'), $config->get('version'));
        }

        $client = new self($restUrl, $config);
        $client->addSubscriber($auth);
        $client->setDescription(ServiceDescription::factory(__DIR__ . '/service.json'));
        $client->setDefaultOption('headers/Content-Type', 'application/json');

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    public function importLeadsCsv($args)
    {
        if (!is_readable($args['file'])) {
            throw new \Exception('Cannot read file: ' . $args['file']);
        }

        if (empty($args['format'])) {
            $args['format'] = 'csv';
        }

        return $this->getResult('importLeadsCsv', $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getBulkUploadStatus($batchId)
    {
        if (empty($batchId) || !is_int($batchId)) {
            throw new \Exception('Invalid $batchId provided in ' . __METHOD__);
        }

        return $this->getResult('getBulkUploadStatus', array('batchId' => $batchId));
    }

    /**
     * {@inheritdoc}
     */
    public function getBulkUploadFailures($batchId)
    {
        if( empty($batchId) || !is_int($batchId) ) {
            throw new \Exception('Invalid $batchId provided in ' . __METHOD__);
        }

        return $this->getResult('getBulkUploadFailures', array('batchId' => $batchId));
    }

    /**
     * {@inheritdoc}
     */
    public function getBulkUploadWarnings($batchId)
    {
        if( empty($batchId) || !is_int($batchId) ) {
            throw new \Exception('Invalid $batchId provided in ' . __METHOD__);
        }

        return $this->getResult('getBulkUploadWarnings', array('batchId' => $batchId));
    }

    /**
     * Calls the CreateOrUpdateLeads command with the given action.
     *
     * @param string $action
     * @param array  $leads
     * @param string $lookupField
     * @param array  $args
     * @param bool   $returnRaw
     *
     * @see Client::createLeads()
     * @see Client::createOrUpdateLeads()
     * @see Client::updateLeads()
     * @see Client::createDuplicateLeads()
     *
     * @link http://developers.marketo.com/documentation/rest/createupdate-leads/
     *
     * @return \CSD\Marketo\Response\CreateOrUpdateLeadsResponse
     */
    private function createOrUpdateLeadsCommand($action, $leads, $lookupField, $args, $returnRaw = false)
    {
        $args['input'] = $leads;
        $args['action'] = $action;

        if (isset($lookupField)) {
            $args['lookupField'] = $lookupField;
        }

        return $this->getResult('createOrUpdateLeads', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function createLeads($leads, $lookupField = null, $args = array())
    {
        return $this->createOrUpdateLeadsCommand('createOnly', $leads, $lookupField, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function createOrUpdateLeads($leads, $lookupField = null, $args = array())
    {
        return $this->createOrUpdateLeadsCommand('createOrUpdate', $leads, $lookupField, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function updateLeads($leads, $lookupField = null, $args = array())
    {
        return $this->createOrUpdateLeadsCommand('updateOnly', $leads, $lookupField, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function createDuplicateLeads($leads, $lookupField = null, $args = array())
    {
        return $this->createOrUpdateLeadsCommand('createDuplicate', $leads, $lookupField, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getLists($ids = null, $args = array(), $returnRaw = false)
    {
        if ($ids) {
            $args['id'] = $ids;
        }

        return $this->getResult('getLists', $args, is_array($ids), $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($id, $args = array(), $returnRaw = false)
    {
        $args['id'] = $id;

        return $this->getResult('getList', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getLeadsByFilterType($filterType, $filterValues, $fields = array(), $nextPageToken = null, $returnRaw = false)
    {
        $args['filterType'] = $filterType;
        $args['filterValues'] = $filterValues;

        if ($nextPageToken) {
            $args['nextPageToken'] = $nextPageToken;
        }

        if (count($fields)) {
            $args['fields'] = implode(',', $fields);
        }

        return $this->getResult('getLeadsByFilterType', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getLeadByFilterType($filterType, $filterValue, $fields = array(), $returnRaw = false)
    {
        $args['filterType'] = $filterType;
        $args['filterValues'] = $filterValue;

        if (count($fields)) {
            $args['fields'] = implode(',', $fields);
        }

        return $this->getResult('getLeadByFilterType', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getLeadPartitions($args = array(), $returnRaw = false)
    {
        return $this->getResult('getLeadPartitions', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getLeadsByList($listId, $args = array(), $returnRaw = false)
    {
        $args['listId'] = $listId;

        return $this->getResult('getLeadsByList', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getLead($id, $fields = null, $args = array(), $returnRaw = false)
    {
        $args['id'] = $id;

        if (is_array($fields)) {
            $args['fields'] = implode(',', $fields);
        }

        return $this->getResult('getLead', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function isMemberOfList($listId, $id, $args = array(), $returnRaw = false)
    {
        $args['listId'] = $listId;
        $args['id'] = $id;

        return $this->getResult('isMemberOfList', $args, is_array($id), $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getCampaign($id, $args = array(), $returnRaw = false)
    {
        $args['id'] = $id;

        return $this->getResult('getCampaign', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getCampaigns($ids = null, $args = array(), $returnRaw = false)
    {
        if ($ids) {
            $args['id'] = $ids;
        }

        return $this->getResult('getCampaigns', $args, is_array($ids), $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getFields($args = array(), $returnRaw = false)
    {
        return $this->getResult('getFields', $args, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getActivityTypes($args = array(), $returnRaw = false)
    {
        return $this->getResult('getActivityTypes', $args, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function addLeadsToList($listId, $leads, $args = array(), $returnRaw = false)
    {
        $args['listId'] = $listId;
        $args['id'] = (array) $leads;

        return $this->getResult('addLeadsToList', $args, true, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function removeLeadsFromList($listId, $leads, $args = array(), $returnRaw = false)
    {
        $args['listId'] = $listId;
        $args['id'] = (array) $leads;

        return $this->getResult('removeLeadsFromList', $args, true, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLead($leads, $args = array(), $returnRaw = false)
    {
        $args['id'] = (array) $leads;

        return $this->getResult('deleteLead', $args, true, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function requestCampaign($id, $leads, $tokens = array(), $args = array(), $returnRaw = false)
    {
        $args['id'] = $id;

        $args['input'] = array('leads' => array_map(function ($id) {
            return array('id' => $id);
        }, (array) $leads));

        if (!empty($tokens)) {
            $args['input']['tokens'] = $tokens;
        }

        return $this->getResult('requestCampaign', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function scheduleCampaign($id, \DateTime $runAt = NULL, $tokens = array(), $args = array(), $returnRaw = false)
    {
        $args['id'] = $id;

        if (!empty($runAt)) {
          $args['input']['runAt'] = $runAt->format('c');
        }

        if (!empty($tokens)) {
            $args['input']['tokens'] = $tokens;
        }

        return $this->getResult('scheduleCampaign', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function associateLead($id, $cookie = null, $args = array(), $returnRaw = false)
    {
        $args['id'] = $id;

        if (!empty($cookie)) {
            $args['cookie'] = $cookie;
        }

        return $this->getResult('associateLead', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getPagingToken($sinceDatetime, $args = array(), $returnRaw = false)
    {
        $args['sinceDatetime'] = $sinceDatetime;

        return $this->getResult('getPagingToken', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getLeadChanges($nextPageToken, $fields, $args = array(), $returnRaw = false)
    {
        $args['nextPageToken'] = $nextPageToken;
        $args['fields'] = (array) $fields;

        if (count($fields)) {
            $args['fields'] = implode(',', $fields);
        }

        return $this->getResult('getLeadChanges', $args, true, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function getLeadActivity($nextPageToken, $leads, $activityTypeIds, $args = array(), $returnRaw = false) {
        $args['nextPageToken'] = $nextPageToken;
        $args['leadIds'] = count((array) $leads) ? implode(',', (array)$leads) : '';
        $args['activityTypeIds'] = count((array) $activityTypeIds) ? implode(',', (array)$activityTypeIds) : '';

        return $this->getResult('getLeadActivity', $args, true, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function updateEmailContent($emailId, $args = array(), $returnRaw = false)
    {
        $args['id'] = $emailId;

        return $this->getResult('updateEmailContent', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function updateEmailContentInEditableSection($emailId, $htmlId, $args = array(), $returnRaw = false)
    {
        $args['id'] = $emailId;
        $args['htmlId'] = $htmlId;

        return $this->getResult('updateEmailContentInEditableSection', $args, false, $returnRaw);
    }

    /**
     * {@inheritdoc}
     */
    public function approveEmail($emailId, $args = array(), $returnRaw = false)
    {
        $args['id'] = $emailId;

        return $this->getResult('approveEmailbyId', $args, false, $returnRaw);
    }

    /**
     * Internal helper method to actually perform command.
     *
     * @param string $command
     * @param array  $args
     * @param bool   $fixArgs
     *
     * @return \CSD\Marketo\Response
     */
    private function getResult($command, $args, $fixArgs = false, $returnRaw = false)
    {
        $cmd = $this->getCommand($command, $args);

        // Marketo expects parameter arrays in the format id=1&id=2, Guzzle formats them as id[0]=1&id[1]=2.
        // Use a quick regex to fix it where necessary.
        if ($fixArgs) {
            $cmd->prepare();

            $url = preg_replace('/id%5B([0-9]+)%5D/', 'id', $cmd->getRequest()->getUrl());
            $cmd->getRequest()->setUrl($url);
        }

        $cmd->prepare();

        if ($returnRaw) {
            return $cmd->getResponse()->getBody(true);
        }

        return $cmd->getResult();
    }
}
