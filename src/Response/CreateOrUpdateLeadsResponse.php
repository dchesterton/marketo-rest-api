<?php
namespace CSD\Marketo\Response;

use CSD\Marketo\Response;

/**
 * Response for the create/update lead API methods.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class CreateOrUpdateLeadsResponse extends Response
{
    /**
     * Get the status of a lead. If no lead ID is given, it returns the status of the first lead returned.
     *
     * @param $id
     * @return bool
     */
    public function getStatus($id = null)
    {
        if ($this->isSuccess()) {
            if (!$id) {
                return $this->getResult()[0]['status'];
            }

            foreach ($this->getResult() as $row) {
                if ($row['id'] == $id) {
                    return $row['status'];
                }
            }
        }

        return false;
    }

    /**
     * @return int|false
     */
    public function getId()
    {
        if ($this->isSuccess()) {
            return $this->getResult()[0]['id'];
        }
        return false;
    }
}
