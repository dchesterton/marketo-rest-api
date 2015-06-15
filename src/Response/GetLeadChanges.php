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
 * Response for the getLead and getLeadByFilterType API method.
 *
 * @author Roberto Espinoza <roberto.espinoza@tamago-db.com>
 */
class GetLeadChanges extends Response
{
    /**
     * @return array|null contains an array of leads with changes and attributes
     */
    public function getLeads()
    {
        if ($this->isSuccess()) {
            $result = $this->getResult();
            return $result;
        }

        return null;
    }

    /**
     * @return bool|null true if there are more results.
     *                   null if the field was not found.
     */
    public function hasMoreResults()
    {
        if ($this->isSuccess()) {
            return $this->data['moreResult'];
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
            'message' => 'Lead changes not found'
        );
    }
}
