<?php
namespace CSD\Marketo\Response;

use CSD\Marketo\Response;

/**
 * Response for the getLeads API method.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class GetLeadsResponse extends Response
{
    /**
     * @return array|null
     */
    public function getLeads()
    {
        return $this->getResult();
    }
}