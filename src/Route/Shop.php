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
        'module'        => 'shop',
        'controller'    => 'index',
        'action'        => 'index'
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
        //$path = 'shop/product/dasdasd-sadad-asdasd-sdsd/page/2/sort/price/order/desc/stock/1';


        $matches = null;

        $parts = array_filter(explode($this->structureDelimiter, $path));

        echo '<pre>';
        print_r($parts);
        echo '</pre>';

        // Set controller
        $controllerList = array('category', 'checkout', 'index', 'product', 'search', 'tag', 'user');
        if (isset($parts[0]) && in_array($parts[0], $controllerList)) {
            $matches['controller'] = urldecode($parts[0]);
        } elseif (isset($parts[0]) && $parts[0] == 'page') {
            $matches['page'] = intval($parts[1]);
            $matches['controller'] = 'index';
        }

        if (null !== $matches) {
            $matches = array_merge($this->defaults, $matches);
        } else {
            $path = $this->defaults['module'] . $this->structureDelimiter . $path;
            $matches = parent::parse($path);
        }
        
        // Make Match
        if (isset($matches['controller'])) {
            switch ($matches['controller']) {
                case 'category':
                
                    break;

                case 'checkout':
                
                    break; 

                case 'index':
                
                    break;

                case 'product':
                    if (!empty($parts[1])) {
                        if ($parts[1] == 'print') {
                            $matches['action'] = 'print';
                            $matches['slug'] = urldecode($parts[2]);
                        } else {
                            $matches['slug'] = urldecode($parts[1]);
                        }
                    }
                    break; 

                case 'search':
                
                    break;

                case 'tag':
                
                    break;    

                case 'user':
                
                    break;
            }    
        }
            
        echo '<pre>';
        print_r($matches);
        echo '</pre>';

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
    ) {
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
                && $mergedParams['controller'] != 'index') 
        {
            $url['controller'] = $mergedParams['controller'];
        }

        // Set action
        if (!empty($mergedParams['action']) 
                && $mergedParams['action'] != 'index') 
        {
            $url['action'] = $mergedParams['action'];
        }
        
        // Set slug
        if (!empty($mergedParams['slug'])) {
            $url['slug'] = $mergedParams['slug'];
        }

        // Set page
        if (!empty($mergedParams['page'])) {
            $url['page'] = sprintf('page%s%s', $this->paramDelimiter, $mergedParams['page']);
        }

        // Set step
        if (!empty($mergedParams['step'])) {
            $url['step'] = sprintf('step%s%s', $this->paramDelimiter, $mergedParams['step']);
        }
        
        // Set sort
        if (!empty($mergedParams['sort'])) {
            $url['sort'] = sprintf('sort%s%s', $this->paramDelimiter, $mergedParams['sort']);
        }

        // Set order
        if (!empty($mergedParams['order'])) {
            $url['order'] = sprintf('order%s%s', $this->paramDelimiter, $mergedParams['order']);
        }

        // Set stock
        if (!empty($mergedParams['stock'])) {
            $url['stock'] = sprintf('stock%s%s', $this->paramDelimiter, $mergedParams['stock']);
        }

        // Make url
        $url = implode($this->paramDelimiter, $url);

        if (empty($url)) {
            return $this->prefix;
        }
        return $this->paramDelimiter . $url;
    }
}
