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
use CSD\Marketo\Response\GetLeadChanges;
use CSD\Marketo\Response\GetPagingToken;
use Guzzle\Common\Collection;
use Guzzle\Service\Client as GuzzleClient;
use Guzzle\Service\Description\ServiceDescription;

// Response classes
use CSD\Marketo\Response\AddOrRemoveLeadsToListResponse;
use CSD\Marketo\Response\AssociateLeadResponse;
use CSD\Marketo\Response\CreateOrUpdateLeadsResponse;
use CSD\Marketo\Response\GetCampaignResponse;
use CSD\Marketo\Response\GetCampaignsResponse;
use CSD\Marketo\Response\GetLeadResponse;
use CSD\Marketo\Response\GetLeadPartitionsResponse;
use CSD\Marketo\Response\GetLeadsResponse;
use CSD\Marketo\Response\GetListResponse;
use CSD\Marketo\Response\GetListsResponse;
use CSD\Marketo\Response\IsMemberOfListResponse;

/**
 * Guzzle client for communicating with the Marketo.com REST API.
 *
 * @link http://developers.marketo.com/documentation/rest/
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class Client extends GuzzleClient
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
     * Import Leads via file upload
     *
     * @param array $args - Must contain 'format' and 'file' keys
     *     e.g. array( 'format' => 'csv', 'file' => '/full/path/to/filename.csv'
     *
     * @link http://developers.marketo.com/documentation/rest/import-lead/
     *
     * @return array
     *
     * @throws \Exception
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
     * Get status of an async Import Lead file upload
     *
     * @param int $batchId
     *
     * @throws \Exception
     *
     * @link http://developers.marketo.com/documentation/rest/get-import-lead-status/
     *
     * @return array
     */
    public function getBulkUploadStatus($batchId)
    {
        if (empty($batchId) || !is_int($batchId)) {
            throw new \Exception('Invalid $batchId provided in ' . __METHOD__);
        }

        return $this->getResult('getBulkUploadStatus', array('batchId' => $batchId));
    }

    /**
     * Get failed lead results from an Import Lead file upload
     *
     * @param int $batchId
     *
     * @throws \Exception
     *
     * @link http://developers.marketo.com/documentation/rest/get-import-failure-file/
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function getBulkUploadFailures($batchId)
    {
        if( empty($batchId) || !is_int($batchId) ) {
            throw new \Exception('Invalid $batchId provided in ' . __METHOD__);
        }

        return $this->getResult('getBulkUploadFailures', array('batchId' => $batchId));
    }

    /**
     * Get warnings from Import Lead file upload
     *
     * @param int $batchId
     *
     * @throws \Exception
     *
     * @link http://developers.marketo.com/documentation/rest/get-import-warning-file/
     *
     * @return \Guzzle\Http\Message\Response
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
     * @return CreateOrUpdateLeadsResponse
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
     * Create the given leads.
     *
     * @param array  $leads
     * @param string $lookupField
     * @param array  $args
     * @see Client::createOrUpdateLeadsCommand()
     *
     * @link http://developers.marketo.com/documentation/rest/createupdate-leads/
     *
     * @return CreateOrUpdateLeadsResponse
     */
    public function createLeads($leads, $lookupField = null, $args = array())
    {
        return $this->createOrUpdateLeadsCommand('createOnly', $leads, $lookupField, $args);
    }

    /**
     * Update the given leads, or create them if they do not exist.
     *
     * @param array  $leads
     * @param string $lookupField
     * @param array  $args
     * @see Client::createOrUpdateLeadsCommand()
     *
     * @link http://developers.marketo.com/documentation/rest/createupdate-leads/
     *
     * @return CreateOrUpdateLeadsResponse
     */
    public function createOrUpdateLeads($leads, $lookupField = null, $args = array())
    {
        return $this->createOrUpdateLeadsCommand('createOrUpdate', $leads, $lookupField, $args);
    }

    /**
     * Update the given leads.
     *
     * @param array  $leads
     * @param string $lookupField
     * @param array  $args
     * @see Client::createOrUpdateLeadsCommand()
     *
     * @link http://developers.marketo.com/documentation/rest/createupdate-leads/
     *
     * @return CreateOrUpdateLeadsResponse
     */
    public function updateLeads($leads, $lookupField = null, $args = array())
    {
        return $this->createOrUpdateLeadsCommand('updateOnly', $leads, $lookupField, $args);
    }

    /**
     * Create duplicates of the given leads.
     *
     * @param array  $leads
     * @param string $lookupField
     * @param array  $args
     * @see Client::createOrUpdateLeadsCommand()
     *
     * @link http://developers.marketo.com/documentation/rest/createupdate-leads/
     *
     * @return CreateOrUpdateLeadsResponse
     */
    public function createDuplicateLeads($leads, $lookupField = null, $args = array())
    {
        return $this->createOrUpdateLeadsCommand('createDuplicate', $leads, $lookupField, $args);
    }

    /**
     * Get multiple lists.
     *
     * @param int|array $ids  Filter by one or more IDs
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-multiple-lists/
     *
     * @return GetListsResponse
     */
    public function getLists($ids = null, $args = array(), $returnRaw = false)
    {
        if ($ids) {
            $args['id'] = $ids;
        }

        return $this->getResult('getLists', $args, is_array($ids), $returnRaw);
    }

    /**
     * Get a list by ID.
     *
     * @param int   $id
     * @param array $args
     * @param bool  $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-list-by-id/
     *
     * @return GetListResponse
     */
    public function getList($id, $args = array(), $returnRaw = false)
    {
        $args['id'] = $id;

        return $this->getResult('getList', $args, false, $returnRaw);
    }

    /**
     * Get multiple leads by filter type.
     *
     * @param string $filterType   One of the supported filter types, e.g. id, cookie or email. See Marketo's documentation for all types.
     * @param string $filterValues Comma separated list of filter values
     * @param array  $fields       Array of field names to be returned in the response
     * @param string $nextPageToken
     * @param bool   $returnRaw
     * @link http://developers.marketo.com/documentation/rest/get-multiple-leads-by-filter-type/
     *
     * @return GetLeadsResponse
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
     * Get a lead by filter type.
     *
     * Convenient method which uses {@link http://developers.marketo.com/documentation/rest/get-multiple-leads-by-filter-type/}
     * internally and just returns the first lead if there is one.
     *
     * @param string $filterType  One of the supported filter types, e.g. id, cookie or email. See Marketo's documentation for all types.
     * @param string $filterValue The value to filter by
     * @param array  $fields      Array of field names to be returned in the response
     * @param bool   $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-multiple-leads-by-filter-type/
     *
     * @return GetLeadResponse
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
     * Get lead partitions.
     *
     * @param array $args
     * @param bool $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-lead-partitions/
     *
     * @return GetLeadPartitionsResponse
     */
    public function getLeadPartitions($args = array(), $returnRaw = false)
    {
        return $this->getResult('getLeadPartitions', $args, false, $returnRaw);
    }

    /**
     * Get multiple leads by list ID.
     *
     * @param int   $listId
     * @param array $args
     * @param bool  $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-multiple-leads-by-list-id/
     *
     * @return GetLeadsResponse
     */
    public function getLeadsByList($listId, $args = array(), $returnRaw = false)
    {
        $args['listId'] = $listId;

        return $this->getResult('getLeadsByList', $args, false, $returnRaw);
    }

    /**
     * Get a lead by ID.
     *
     * @param int   $id
     * @param array $fields
     * @param array $args
     * @param bool  $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-lead-by-id/
     *
     * @return GetLeadResponse
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
     * Check if a lead is a member of a list.
     *
     * @param int       $listId List ID
     * @param int|array $id     Lead ID or an array of Lead IDs
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/member-of-list/
     *
     * @return IsMemberOfListResponse
     */
    public function isMemberOfList($listId, $id, $args = array(), $returnRaw = false)
    {
        $args['listId'] = $listId;
        $args['id'] = $id;

        return $this->getResult('isMemberOfList', $args, is_array($id), $returnRaw);
    }

    /**
     * Get a campaign by ID.
     *
     * @param int   $id
     * @param array $args
     * @param bool  $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-campaign-by-id/
     *
     * @return GetCampaignResponse
     */
    public function getCampaign($id, $args = array(), $returnRaw = false)
    {
        $args['id'] = $id;

        return $this->getResult('getCampaign', $args, false, $returnRaw);
    }

    /**
     * Get campaigns.
     *
     * @param int|array $ids  A single Campaign ID or an array of Campaign IDs
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-multiple-campaigns/
     *
     * @return GetCampaignsResponse
     */
    public function getCampaigns($ids = null, $args = array(), $returnRaw = false)
    {
        if ($ids) {
            $args['id'] = $ids;
        }

        return $this->getResult('getCampaigns', $args, is_array($ids), $returnRaw);
    }

    /**
     * Add one or more leads to the specified list.
     *
     * @param int       $listId List ID
     * @param int|array $leads  Either a single lead ID or an array of lead IDs
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/add-leads-to-list/
     *
     * @return AddOrRemoveLeadsToListResponse
     */
    public function addLeadsToList($listId, $leads, $args = array(), $returnRaw = false)
    {
        $args['listId'] = $listId;
        $args['id'] = (array) $leads;

        return $this->getResult('addLeadsToList', $args, true, $returnRaw);
    }

    /**
     * Remove one or more leads from the specified list.
     *
     * @param int       $listId List ID
     * @param int|array $leads  Either a single lead ID or an array of lead IDs
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/remove-leads-from-list/
     *
     * @return AddOrRemoveLeadsToListResponse
     */
    public function removeLeadsFromList($listId, $leads, $args = array(), $returnRaw = false)
    {
        $args['listId'] = $listId;
        $args['id'] = (array) $leads;

        return $this->getResult('removeLeadsFromList', $args, true, $returnRaw);
    }

    /**
     * Delete one or more leads
     *
     * @param int|array $leads  Either a single lead ID or an array of lead IDs
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/delete-lead/
     *
     * @return \CSD\Marketo\Response\DeleteLeadResponse
     */
    public function deleteLead($leads, $args = array(), $returnRaw = false)
    {
        $args['id'] = (array) $leads;

        return $this->getResult('deleteLead', $args, true, $returnRaw);
    }

    /**
     * Trigger a campaign for one or more leads.
     *
     * @param int       $id     Campaign ID
     * @param int|array $leads  Either a single lead ID or an array of lead IDs
     * @param array     $tokens Key value array of tokens to send new values for.
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/request-campaign/
     *
     * @return \CSD\Marketo\Response\RequestCampaignResponse
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
     * Schedule a campaign
     *
     * @param int         $id      Campaign ID
     * @param \DateTime   $runAt   The time to run the campaign. If not provided, campaign will be run in 5 minutes.
     * @param array       $tokens  Key value array of tokens to send new values for.
     * @param array       $args
     * @param bool        $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/schedule-campaign/
     *
     * @return \CSD\Marketo\Response\ScheduleCampaignResponse
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
     * Associate a lead
     *
     * @param int       $id
     * @param string    $cookie
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/associate-lead/
     *
     * @return AssociateLeadResponse
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
     * Get the paging token required for lead activity and changes
     *
     * @param string $sinceDatetime String containing a datetime
     * @param array  $args
     * @param bool   $returnRaw
     *
     * @return GetPagingToken
     * @link http://developers.marketo.com/documentation/rest/get-paging-token/
     *
     */
    public function getPagingToken($sinceDatetime, $args = array(), $returnRaw = false)
    {
        $args['sinceDatetime'] = $sinceDatetime;

        return $this->getResult('getPagingToken', $args, false, $returnRaw);
    }

    /**
     * Get lead changes
     *
     * @param string       $nextPageToken Next page token
     * @param string|array $fields
     * @param array        $args
     * @param bool         $returnRaw
     *
     * @return GetLeadChanges
     * @link http://developers.marketo.com/documentation/rest/get-lead-changes/
     * @see  getPagingToken
     *
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
     * Update an editable section in an email
     *
     * @param int       $emailId
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/asset-api/update-email-content-by-id/
     *
     * @return Response
     */
    public function updateEmailContent($emailId, $args = array(), $returnRaw = false)
    {
        $args['id'] = $emailId;

        return $this->getResult('updateEmailContent', $args, false, $returnRaw);
    }

    /**
     * Update an editable section in an email
     *
     * @param int       $emailId
     * @param string    $htmlId
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/asset-api/update-email-content-in-editable-section/
     *
     * @return \CSD\Marketo\Response\UpdateEmailContentInEditableSectionResponse
     */
    public function updateEmailContentInEditableSection($emailId, $htmlId, $args = array(), $returnRaw = false)
    {
        $args['id'] = $emailId;
        $args['htmlId'] = $htmlId;

        return $this->getResult('updateEmailContentInEditableSection', $args, false, $returnRaw);
    }

    /**
     * Approve an email
     *
     * @param int       $emailId
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/asset-api/approve-email-by-id/
     *
     * @return \CSD\Marketo\Response\ApproveEmailResponse
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
     * @param bool   $returnRaw
     *
     * @return Response
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
