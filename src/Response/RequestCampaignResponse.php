<?php

namespace CSD\Marketo\Response;

use CSD\Marketo\Response;

/**
 * Response for the requestCampaign API method.
 *
 * @author Steve Buzonas <sbuzonas@carnegielearning.com>
 */
class RequestCampaignResponse extends Response
{
    /**
     * @return array|null
     */
    public function requestCampaign()
    {
        return $this->getResult();
    }
}
