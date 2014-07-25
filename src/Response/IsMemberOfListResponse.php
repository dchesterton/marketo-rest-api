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
 * Response for the isMemberOfList API method.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class IsMemberOfListResponse extends Response
{
    /**
     * @param int $id
     * @return bool
     */
    public function isMember($id = null)
    {
        $result = $this->getResult();

        // error or no rows found
        if (!$result || count($result) == 0) {
            return false;
        }

        if ($id) {
            foreach ($result as $row) {
                if ($row['id'] == $id) {
                    return 'memberof' === $row['status'];
                }
            }

            return false; // ID not found in response
        }

        // no ID provided, return status of first row
        return 'memberof' === $result[0]['status'];
    }
}
