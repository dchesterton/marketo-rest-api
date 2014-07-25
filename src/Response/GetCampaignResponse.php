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
 * Response for the getCampaign API method.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class GetCampaignResponse extends Response
{
    /**
     * @return array|null
     */
    public function getCampaign()
    {
        return $this->isSuccess()? $this->getResult()[0]: null;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->isSuccess()? $this->getCampaign()['id']: null;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->isSuccess()? $this->getCampaign()['name']: null;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->isSuccess()? $this->getCampaign()['description']: null;
    }

    /**
     * @return string|null
     */
    public function getProgramName()
    {
        return $this->isSuccess()? $this->getCampaign()['programName']: null;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->isSuccess()? $this->getCampaign()['createdAt']: null;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->isSuccess()? $this->getCampaign()['updatedAt']: null;
    }
}
