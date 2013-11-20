<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Shop\Api;

use Pi;
use Pi\Application\AbstractApi;

/*
 * Pi::api('shop', 'review')->official($id);
 * Pi::api('shop', 'review')->hasOfficial($id);
 * Pi::api('shop', 'review')->listReview($id, $status);
 */

class Review extends AbstractApi
{
	public function official($id)
	{
    	$where = array('product' => $id, 'official' => 1);
    	$select = Pi::model('review', $this->getModule())->select()->where($where)->limit(1);
    	$row = Pi::model('review', $this->getModule())->selectWith($select)->current();
    	if (!empty($row) && is_object($row)) {
    		$row = $row->toArray();
    		$row['description'] = Pi::service('markup')->render($row['description'], 'text', 'html');
    		$row['time_create_view'] = _date($row['time_create']);
    		$row['userinfo'] = Pi::api('user', 'user')->get(
    			$row['user'],
                array('name', 'gender'),
                true);
    	}
    	return $row;
	}

	public function hasOfficial($id)
	{
		$official = $this->official($id);
		if (empty($official)) {
			return false;
		} else {
			return true;
		}
	}

	public function listReview($id, $status = null)
	{
    	if (empty($status)) {
    		$where = array('product' => $id, 'official' => 0);
    	} else {
    		$where = array('product' => $id, 'official' => 0, 'status' => $status);
    	}
    	$select = Pi::model('review', $this->getModule())->select()->where($where);
    	$rowset = Pi::model('review', $this->getModule())->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
    		$row['description'] = Pi::service('markup')->render($row['description'], 'text', 'html');
    		$list[$row->id]['time_create_view'] = _date($row->time_create);
    		$list[$row->id]['userinfo'] = Pi::api('user', 'user')->get(
    			$row->user,
                array('name', 'gender'),
                true);
        }
    	return $list;
	}
}