<?php
namespace CSD\Marketo\Response;

use CSD\Marketo\Response;

/**
 * Response for the addLeadsToList and removeLeadsFromList API methods.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class AddOrRemoveLeadsToListResponse extends Response
{
    /**
     * Get the status of a lead.
     *
     * @param $id
     * @return bool
     */
    public function getStatus($id)
    {
        if ($this->isSuccess()) {
            foreach ($this->getResult() as $row) {
                if ($row['id'] == $id) {
                    return $row['status'];
                }
            }
        }

        return false;
    }}