<?php
/**
 * Twig Extensions
 *
 * The file which holds extensions for Twig Templates
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */

use Slim\Slim;

/**
 * MyTwigExtension
 *
 * extends Twig_Extension, contains globals and functions for the templates
 */
class MyTwigExtension extends \Twig_Extension
{
    private $controllerFactory;

    public function __construct($controllerFactory)
    {
        $this->controllerFactory = $controllerFactory;
    }

    public function getName()
    {
        return 'MyTwigExtension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('baseUrl', array($this, 'base')),
            new \Twig_SimpleFunction('jsUrl', array($this, 'jsUrl')),
            new \Twig_SimpleFunction('cssUrl', array($this, 'cssUrl')),
            new \Twig_SimpleFunction('render', array($this, 'render')),
            new \Twig_SimpleFunction('getUrlParams', array($this, 'getUrlParams'))
        );
    }

    function getGlobals()
    {
        return array(
            'admin' => ADMIN_FOLDER,
            'bootstrap' => BOOTSTRAP_FOLDER,
            'siteName' => SITE_NAME
        );
    }

    public function base($extend = '', $appName = 'default')
    {
        $req = Slim::getInstance($appName)->request();
        $uri = $req->getUrl();
        $uri .= $req->getRootUri();
        $uri =  str_replace(PUBLIC_FOLDER, '', $uri);
        $uri .= $extend;
        return $uri;
    }

    public function jsUrl($resource)
    {
        return $this->base() . PUBLIC_FOLDER . '/js/' . $resource;
    }

    public function cssUrl($resource)
    {
        return $this->base() . PUBLIC_FOLDER . '/css/' . $resource;
    }

    public function render($controller, $action)
    {
        $this->controllerFactory->buildController($controller, $action);
    }

    public function getUrlParams($arg)
    {
        $path = $_SERVER['REQUEST_URI'];
        $path = str_replace(SUB_FOLDER, '', $path);
        $path = str_replace(ADMIN_FOLDER, '', $path);
        $pathArr = array_values(array_filter(explode('/', $path)));
        return (isset($pathArr[$arg])) ? $pathArr[$arg] : '';
    }
}
