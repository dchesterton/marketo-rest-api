<?php
namespace CSD\Marketo\Response;

use CSD\Marketo\Response as Response;

/**
 * Response for the getLead and getLeadByFilterType API method.
 *
 * @author Roberto Espinoza <roberto.espinoza@tamago-db.com>
 */
class GetLeadActivities extends Response
{
	public function getActivities()
	{
        if ($this->isSuccess()) {
            $result = $this->getResult();
            return $result;
        }

        return null;
	}
}