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
    public function getName()
    {
        return 'MyTwigExtension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('baseUrl', array($this, 'base')),
            new \Twig_SimpleFunction('jsUrl', array($this, 'jsUrl')),
            new \Twig_SimpleFunction('cssUrl', array($this, 'cssUrl'))
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
}
