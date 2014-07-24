<?php
namespace CSD\Marketo\Response;

use CSD\Marketo\Response;

/**
 * Response for the getLists API method.
 *
 * @author Daniel Chesterton <daniel@chestertondevelopment.com>
 */
class GetListsResponse extends Response
{
    /**
     * @return array|null
     */
    public function getLists()
    {
        return $this->getResult();
    }

    /**
     * @param $id
     * @return array|bool
     */
    public function getList($id)
    {
        $lists = $this->getLists();

        if (!$lists) {
            return false;
        }

        foreach ($lists as $list) {
            if ($list['id'] == $id) {
                return $list;
            }
        }

        return false;
    }
}
