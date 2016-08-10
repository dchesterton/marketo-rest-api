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

/**
 * Interface for guzzle client for communicating with the Marketo.com REST API.
 *
 * @link http://developers.marketo.com/documentation/rest/
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
interface ClientInterface
{

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
    public function importLeadsCsv($args);

    /**
     * Get status of an async Import Lead file upload
     *
     * @param int $batchId
     *
     * @link http://developers.marketo.com/documentation/rest/get-import-lead-status/
     *
     * @return array
     */
    public function getBulkUploadStatus($batchId);

    /**
     * Get failed lead results from an Import Lead file upload
     *
     * @param int $batchId
     *
     * @link http://developers.marketo.com/documentation/rest/get-import-failure-file/
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function getBulkUploadFailures($batchId);

    /**
     * Get warnings from Import Lead file upload
     *
     * @param int $batchId
     *
     * @link http://developers.marketo.com/documentation/rest/get-import-warning-file/
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function getBulkUploadWarnings($batchId);

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
     * @return \CSD\Marketo\Response\CreateOrUpdateLeadsResponse
     */
    public function createLeads($leads, $lookupField = null, $args = array());

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
     * @return \CSD\Marketo\Response\CreateOrUpdateLeadsResponse
     */
    public function createOrUpdateLeads($leads, $lookupField = null, $args = array());

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
     * @return \CSD\Marketo\Response\CreateOrUpdateLeadsResponse
     */
    public function updateLeads($leads, $lookupField = null, $args = array());

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
     * @return \CSD\Marketo\Response\CreateOrUpdateLeadsResponse
     */
    public function createDuplicateLeads($leads, $lookupField = null, $args = array());

    /**
     * Get multiple lists.
     *
     * @param int|array $ids  Filter by one or more IDs
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-multiple-lists/
     *
     * @return \CSD\Marketo\Response\GetListsResponse
     */
    public function getLists($ids = null, $args = array(), $returnRaw = false);

    /**
     * Get a list by ID.
     *
     * @param int       $id
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-list-by-id/
     *
     * @return \CSD\Marketo\Response\GetListResponse
     */
    public function getList($id, $args = array(), $returnRaw = false);

    /**
     * Get multiple leads by filter type.
     *
     * @param string    $filterType   One of the supported filter types, e.g. id, cookie or email. See Marketo's documentation for all types.
     * @param string    $filterValues Comma separated list of filter values
     * @param array     $fields       Array of field names to be returned in the response
     * @param string    $nextPageToken
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-multiple-leads-by-filter-type/
     *
     * @return \CSD\Marketo\Response\GetLeadsResponse
     */
    public function getLeadsByFilterType($filterType, $filterValues, $fields = array(), $nextPageToken = null, $returnRaw = false);

    /**
     * Get a lead by filter type.
     *
     * Convenient method which uses {@link http://developers.marketo.com/documentation/rest/get-multiple-leads-by-filter-type/}
     * internally and just returns the first lead if there is one.
     *
     * @param string    $filterType  One of the supported filter types, e.g. id, cookie or email. See Marketo's documentation for all types.
     * @param string    $filterValue The value to filter by
     * @param array     $fields      Array of field names to be returned in the response
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-multiple-leads-by-filter-type/
     *
     * @return \CSD\Marketo\Response\GetLeadResponse
     */
    public function getLeadByFilterType($filterType, $filterValue, $fields = array(), $returnRaw = false);

    /**
     * Get lead partitions.
     *
     * @param array     $args
     * @param bool      $returnRaw

     * @link http://developers.marketo.com/documentation/rest/get-lead-partitions/
     *
     * @return \CSD\Marketo\Response\GetLeadPartitionsResponse
     */
    public function getLeadPartitions($args = array(), $returnRaw = false);

    /**
     * Get multiple leads by list ID.
     *
     * @param int       $listId
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-multiple-leads-by-list-id/
     *
     * @return \CSD\Marketo\Response\GetLeadsResponse
     */
    public function getLeadsByList($listId, $args = array(), $returnRaw = false);

    /**
     * Get a lead by ID.
     *
     * @param int       $id
     * @param array     $fields
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-lead-by-id/
     *
     * @return \CSD\Marketo\Response\GetLeadResponse
     */
    public function getLead($id, $fields = null, $args = array(), $returnRaw = false);

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
     * @return \CSD\Marketo\Response\IsMemberOfListResponse
     */
    public function isMemberOfList($listId, $id, $args = array(), $returnRaw = false);

    /**
     * Get a campaign by ID.
     *
     * @param int       $id
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-campaign-by-id/
     *
     * @return \CSD\Marketo\Response\GetCampaignResponse
     */
    public function getCampaign($id, $args = array(), $returnRaw = false);

    /**
     * Get campaigns.
     *
     * @param int|array $ids  A single Campaign ID or an array of Campaign IDs
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-multiple-campaigns/
     *
     * @return \CSD\Marketo\Response\GetCampaignsResponse
     */
    public function getCampaigns($ids = null, $args = array(), $returnRaw = false);

    /**
     * Get fields (describe).
     *
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/describe/
     *
     * @return \CSD\Marketo\Response\GetFieldsResponse
     */
    public function getFields($args = array(), $returnRaw = false);

    /**
     * Get activity types.
     *
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-activity-types
     *
     * @return \CSD\Marketo\Response\GetActivityTypesResponse
     */
    public function getActivityTypes($args = array(), $returnRaw = false);

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
     * @return \CSD\Marketo\Response\AddOrRemoveLeadsToListResponse
     */
    public function addLeadsToList($listId, $leads, $args = array(), $returnRaw = false);

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
     * @return \CSD\Marketo\Response\AddOrRemoveLeadsToListResponse
     */
    public function removeLeadsFromList($listId, $leads, $args = array(), $returnRaw = false);

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
    public function deleteLead($leads, $args = array(), $returnRaw = false);

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
    public function requestCampaign($id, $leads, $tokens = array(), $args = array(), $returnRaw = false);

    /**
     * Schedule a campaign
     *
     * @param int           $id      Campaign ID
     * @param \DateTime     $runAt   The time to run the campaign. If not provided, campaign will be run in 5 minutes.
     * @param array         $tokens  Key value array of tokens to send new values for.
     * @param array         $args
     * @param bool          $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/schedule-campaign/
     *
     * @return \CSD\Marketo\Response\ScheduleCampaignResponse
     */
    public function scheduleCampaign($id, \DateTime $runAt = NULL, $tokens = array(), $args = array(), $returnRaw = false);

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
     * @return \CSD\Marketo\Response\AssociateLeadResponse
     */
    public function associateLead($id, $cookie = null, $args = array(), $returnRaw = false);

    /**
     * Get the paging token required for lead activity and changes
     *
     * @param string $sinceDatetime String containing a datetime
     * @param array  $args
     * @param bool   $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-paging-token/
     *
     * @return \CSD\Marketo\Response\GetPagingToken
     *
     */
    public function getPagingToken($sinceDatetime, $args = array(), $returnRaw = false);

    /**
     * Get lead changes
     *
     * @param string       $nextPageToken Next page token
     * @param string|array $fields
     * @param array        $args
     * @param bool         $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-lead-changes/
     *
     * @return \CSD\Marketo\Response\GetLeadChanges
     * @see  getPagingToken
     *
     */
    public function getLeadChanges($nextPageToken, $fields, $args = array(), $returnRaw = false);

    /**
     * Get lead activities.
     *
     * @param string       $nextPageToken
     *   Next page token @see: `::getPagingToken`
     * @param string|array $leads
     * @param string|array $activityTypeIds
     *   Activity Types @see: `::getActivityTypes`.
     * @param array        $args
     * @param bool         $returnRaw
     *
     * @link http://developers.marketo.com/documentation/rest/get-lead-activities/
     *
     * @return \CSD\Marketo\Response\GetLeadActivityResponse
     * @see  getPagingToken
     *
     */
    public function getLeadActivity($nextPageToken, $leads, $activityTypeIds, $args = array(), $returnRaw = false);

    /**
     * Update an editable section in an email
     *
     * @param int       $emailId
     * @param array     $args
     * @param bool      $returnRaw
     *
     * @link http://developers.marketo.com/documentation/asset-api/update-email-content-by-id/
     *
     * @return \CSD\Marketo\Response
     */
    public function updateEmailContent($emailId, $args = array(), $returnRaw = false);

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
    public function updateEmailContentInEditableSection($emailId, $htmlId, $args = array(), $returnRaw = false);

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
    public function approveEmail($emailId, $args = array(), $returnRaw = false);
}
