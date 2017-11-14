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
 * Response for the create/update activities API methods.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class CreateOrUpdateActivitiesResponse extends Response
{
	/**
	 * Get the status of an activity. If no activity ID is given, it returns the status of the first activity returned.
	 *
	 * @param $id
	 * @return bool
	 */
	public function getStatus($id = null)
	{
		if ($this->isSuccess()) {
			if (!$id) {
				$result = $this->getResult();
				return $result[0]['status'];
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
			$result = $this->getResult();
			return $result[0]['id'];
		}
		return false;
	}
}