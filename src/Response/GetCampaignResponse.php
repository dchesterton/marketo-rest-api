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
            $campaign = $this->getCampaign();
            return $campaign['id'];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        if ($this->isSuccess()) {
            $campaign = $this->getCampaign();
            return $campaign['name'];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        if ($this->isSuccess()) {
            $campaign = $this->getCampaign();
            return $campaign['description'];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getProgramName()
    {
        if ($this->isSuccess()) {
            $campaign = $this->getCampaign();
            return $campaign['programName'];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt()
    {
        if ($this->isSuccess()) {
            $campaign = $this->getCampaign();
            return $campaign['createdAt'];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt()
    {
        if ($this->isSuccess()) {
            $campaign = $this->getCampaign();
            return $campaign['updatedAt'];
        }
        return null;
    }
}
