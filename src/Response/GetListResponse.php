<?php
namespace CSD\Marketo\Response;

use CSD\Marketo\Response as Response;

/**
 * Response for the getList API method.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class GetListResponse extends Response
{
    /**
     * @return array|null
     */
    public function getList()
    {
        return $this->isSuccess()? $this->getResult()[0]: null;
    }
}
