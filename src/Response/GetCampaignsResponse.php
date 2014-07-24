<?php
namespace CSD\Marketo\Response;

use CSD\Marketo\Response;

/**
 * Response for the getCampaigns API method.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class GetCampaignsResponse extends Response
{
    /**
     * @return array|null
     */
    public function getCampaigns()
    {
        return $this->getResult();
    }
}