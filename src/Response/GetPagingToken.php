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

use CSD\Marketo\Response as Response;

/**
 * Response for the getPagingToken API method.
 *
 * @author Roberto Espinoza <roberto.espinoza@tamago-db.com>
 */
class GetPagingToken extends Response
{
    /**
     * The API doesn't return the nextPageToken enclosed in a result.
     * We have to get it directly from the data returned
     *
     * @return string|null
     */
    public function getNextPageToken()
    {
        if ($this->isSuccess()) {
            return $this->data['nextPageToken'];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        // if it's successful, don't return an error message
        if ($this->isSuccess()) {
            return null;
        }

        // if an error has been returned by Marketo, return that
        if ($error = parent::getError()) {
            return $error;
        }

        // if it's not successful and there's no error from Marketo, create one
        return array(
            'code' => '',
            'message' => 'Cannot retrieve a next page token'
        );
    }
}
