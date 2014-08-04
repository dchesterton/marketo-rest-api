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
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class GetLeadResponse extends Response
{
    /**
     * @return array|null
     */
    public function getLead()
    {
        if ($this->isSuccess()) {
            $result = $this->getResult();
            return $result[0];
        }
        return null;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        if ($this->isSuccess()) {
            $lead = $this->getLead();
            return $lead['id'];
        }
        return null;
    }

    /**
     * Override success function as Marketo incorrectly responds 'success'
     * even if the lead ID does not exist. Overriding it makes it consistent
     * with other API methods such as getList.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return parent::isSuccess()? count($this->getResult()) > 0: false;
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
            'message' => 'Lead not found'
        );
    }
}
