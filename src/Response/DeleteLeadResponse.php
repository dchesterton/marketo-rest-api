<?php
/*
 * This file is part of the Marketo REST API Client package.
 *
 * (c) 2014 Daniel Chesterton
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CSD\Marketo\Response;

use CSD\Marketo\Response;

/**
 * Response for the deleteLead API methods.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class DeleteLeadResponse extends Response
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
    }
}
