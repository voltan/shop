<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
 * @package         Registry
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Shop\Registry;

use Pi;
use Pi\Application\Registry\AbstractRegistry;

class SaleId extends AbstractRegistry
{
    /** @var string Module name */
    protected $module = 'shop';

    /**
     * {@inheritDoc}
     */
    protected function loadDynamic($options = array())
    {
        // Set return
        $return = array(
            'idAll' => array(),
            'idActive' => array(),
            'timeCheck' => time(),
            'timeExpire' => time(),
        );
        $timeExpire = array();
        // Get ids
        $where = array('status' => 1);
        $columns = array('product');
        $model = Pi::model('sale', $this->module);
        $select = $model->select()->where($where)->columns($columns);
        $rowset = $model->selectWith($select);
        foreach ($rowset as $row) {
            $return['idAll'][$row->product] = $row->product;
            if ($row->time_expire > time()) {
                $return['idActive'][$row->product] = $row->product;
                $timeExpire[$row->time_expire] = $row->time_expire;
            }
        }
        // Set time expire
        if (!empty($timeExpire)) {
            $return['timeExpire'] = min($timeExpire);
        }
        // return
        return $return;
    }

    /**
     * {@inheritDoc}
     * @param array
     */
    public function read()
    {
        $options = array();
        $result = $this->loadData($options);
        return $result;
    }

    /**
     * {@inheritDoc}
     * @param bool $name
     */
    public function create()
    {
        $this->clear('');
        $this->read();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function setNamespace($meta = '')
    {
        return parent::setNamespace('');
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        return $this->clear('');
    }
}