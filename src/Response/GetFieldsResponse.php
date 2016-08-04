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
 * Response for the getFields API method.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class GetFieldsResponse extends Response
{
    /**
     * @return array|null
     */
    public function getFields()
    {
        return $this->getResult();
    }
}
