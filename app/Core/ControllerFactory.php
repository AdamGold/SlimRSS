<?php
/**
 * The Controller Factory - Creation of new controllers
 *
 * "the mother" of controllers. creates controllers and passes the right parameters.
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Core;


/**
 * ControllerFactory
 *
 * The class used to build controllers (require them by autoloading)
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class ControllerFactory
{
  /**
   * The Slim Application
   */
  private static $app;

  /**
   * creates a static variable containing the slim application
   *
   * @param Slim $app the slim application
   */
  public function __construct($app)
  {
    self::$app = $app;
  }

  /**
   *
   * @param  string $controller   the class name we need to require
   * @param  string $action       the method inside the class name
   * @param  boolean $admin       are we inside the admin panel?
   * @param  int $q1              the first argument passed in the url
   * @param  int $q2              the second argument passed in the url
   */
  public function buildController($controller = '', $action = '', $admin = false, $q1 = '', $q2 = '')
  {
    $defaultFrontEnd = 'post';
    $defaultBackEnd = 'home';
    $default = $defaultFrontEnd;
    if (true === $admin)
      $default = $defaultBackEnd;

    if (empty($controller)) {
      $controller = $default;
    }
    if (empty($action) || is_numeric($action)) {
      $action = 'index';
    }
    $adminFolder = '';
    $adminNameSpace = '';
    if (true === $admin) {  // if we're in the admin panel
      $adminFolder = '/' . ucwords(ADMIN_FOLDER);
      $adminNameSpace = ucwords(ADMIN_FOLDER) . '\\';
    }
    // check to see the controller file exists in the controllers folder
    $allControllers = scandir(HOME . '/' . APP . '/' . CONTROLLERS_FOLDER . $adminFolder);
    $controller = (in_array(strtolower($controller) . '.php', $allControllers) ? $default : $controller);

    $controller = ucwords($controller);
    $class = '\Controller\\' . $adminNameSpace . $controller;
    $urlParams = array();
    $urlFields = array('controller', 'action', 'q1', 'q2');
    foreach ($urlFields as $v) {
      if (is_numeric($$v))
        $urlParams[$v] = (int) $$v; // turn the string argument into integer
      else
        $urlParams[$v] = $$v;
    }
    if (class_exists($class)) {
      $load = new $class(self::$app, $admin, $urlParams);
      if (method_exists($load, $action)) {
        $load->$action();
      } else {
          self::$app->notFound();
      }
    } else {
      self::$app->notFound();
    }
  }

}