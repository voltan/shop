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
namespace Module\Shop\Route;

use Pi\Mvc\Router\Http\Standard;

class Shop extends Standard
{
    /**
     * Default values.
     * @var array
     */
    protected $defaults = array(
        'module' => 'shop',
        'controller' => 'index',
        'action' => 'index'
    );

    /* protected $sortList = array(
        'create', 'update', 'price', 'stock'
    ); */

    protected $controllerList = array(
        'cart', 'category', 'index', 'json', 'product', 'tag'
    );

    /**
     * {@inheritDoc}
     */
    protected $structureDelimiter = '/';

    /**
     * {@inheritDoc}
     */
    protected function parse($path)
    {
        $matches = array();
        $parts = array_filter(explode($this->structureDelimiter, $path));

        // Set controller
        $matches = array_merge($this->defaults, $matches);
        if (isset($parts[0]) && in_array($parts[0], $this->controllerList)) {
            $matches['controller'] = $this->decode($parts[0]);
        } elseif (isset($parts[0]) && !in_array($parts[0], $this->controllerList)) {
            if (in_array($parts[0], array('index', 'filter'))) {
                $matches['controller'] = 'index';
            } else {
                return '';
            }
        }

        // Make Match
        if (isset($matches['controller'])) {
            switch ($matches['controller']) {

                case 'category':
                    if (isset($parts[1]) && $parts[1] == 'filter') {
                        $matches['action'] = 'filter';
                        $matches['slug'] = $this->decode($parts[2]);
                    } elseif (isset($parts[1]) && !empty($parts[1])) {
                        $matches['action'] = 'index';
                        $matches['slug'] = $this->decode($parts[1]);
                    } else {
                        $matches['action'] = 'list';
                    }
                    break;

                case 'cart':
                    if ($parts[1] == 'complete') {
                        $matches['action'] = 'complete';
                    } elseif ($parts[1] == 'add') {
                        $matches['action'] = 'add';
                    } elseif ($parts[1] == 'finish') {
                        $matches['action'] = 'finish';
                        $matches['id'] = intval($parts[2]);
                    } elseif ($parts[1] == 'empty') {
                        $matches['action'] = 'empty';
                    } elseif ($parts[1] == 'index') {
                        $matches['action'] = 'index';
                    } elseif ($parts[1] == 'levelAjax') {
                        $matches['action'] = 'levelAjax';
                        $matches['process'] = $this->decode($parts[2]);
                        if (is_numeric($parts[3])) {
                            $matches['id'] = intval($parts[3]);
                        } elseif ($parts[2] == 'payment') {
                            $matches['id'] = $this->decode($parts[3]);
                        }
                    } elseif ($parts[1] == 'basket') {
                        $matches['action'] = 'basket';
                        if (isset($parts[2]) && in_array($parts[2], array('remove', 'number'))) {
                            $matches['process'] = $this->decode($parts[2]);
                            $matches['product'] = intval($parts[3]);
                            if (isset($parts[4]) && in_array($parts[4], array(1, -1))) {
                                $matches['number'] = $parts[4];
                            }
                        }
                    }
                    break;

                case 'index':
                    if (isset($parts[0]) && $parts[0] == 'filter') {
                        $matches['action'] = 'filter';
                    } else {
                        $matches['action'] = 'index';
                        if (isset($_GET) && !empty($_GET)) {
                            foreach ($_GET as $key => $value) {
                                if ($key != 'page') {
                                    $matches['q'][$key] = $value;
                                }
                            }
                        }
                    }
                    break;

                case 'product':
                    $matches['slug'] = $this->decode($parts[1]);
                    break;

               /*  case 'search':
                    if ($parts[1] == 'result') {
                        $matches['action'] = 'result';
                        $matches['slug'] = $this->decode($parts[2]);
                    } elseif ($parts[1] == 'filter') {
                        $matches['action'] = 'filter';
                    }
                    break; */

                case 'tag':
                    if (isset($parts[1]) && !empty($parts[1])) {
                        $matches['action'] = 'index';
                        $matches['slug'] = $this->decode($parts[1]);
                    } else {
                        $matches['action'] = 'list';
                    }
                    break;

                case 'json':
                    $matches['action'] = $this->decode($parts[1]);
                    if (isset($parts[2]) && $parts[2] == 'id') {
                        $matches['id'] = intval($parts[3]);
                    }
                    break;
            }
        }
        return $matches;
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return string
     */
    public function assemble(
        array $params = array(),
        array $options = array()
    )
    {
        $mergedParams = array_merge($this->defaults, $params);
        if (!$mergedParams) {
            return $this->prefix;
        }

        // Set module
        if (!empty($mergedParams['module'])) {
            $url['module'] = $mergedParams['module'];
        }

        // Set controller
        if (!empty($mergedParams['controller'])
            && $mergedParams['controller'] != 'index'
            && in_array($mergedParams['controller'], $this->controllerList)
        ) {
            $url['controller'] = $mergedParams['controller'];
        }

        // Set action
        if (!empty($mergedParams['action'])
            && $mergedParams['action'] != 'index'
        ) {
            $url['action'] = $mergedParams['action'];
        }

        // Set if controller is checkou
        if ($mergedParams['controller'] == 'cart') {
            if ($mergedParams['action'] == 'basket') {
                $url['process'] = $mergedParams['process'];
                $url['product'] = $mergedParams['product'];
                if (!empty($mergedParams['number'])) {
                    $url['number'] = $mergedParams['number'];
                }
            }
        }

        // Set slug
        if (!empty($mergedParams['slug'])) {
            $url['slug'] = $mergedParams['slug'];
        }

        // Set id
        if (!empty($mergedParams['id'])) {
            $url['id'] = $mergedParams['id'];
        }

        // Set slug
        if (!empty($mergedParams['q'])) {
            $url['q'] = $mergedParams['q'];
        }

        // Set step
        if (!empty($mergedParams['step'])) {
            $url['step'] = sprintf('step%s%s',
                $this->paramDelimiter,
                $mergedParams['step']);
        }

        // Make url
        $url = implode($this->paramDelimiter, $url);

        if (empty($url)) {
            return $this->prefix;
        }
        return $this->paramDelimiter . $url;
    }
}
