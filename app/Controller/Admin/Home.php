<?php
/**
 * Admin Home controller
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Controller\Admin;

/**
 * Admin Home controller
 *
 * repsonsible for the admin panel dashboard
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Home extends \Controller\Controller
{
  /**
   * renders the dashboard view
   */
  public function index()
  {
    $this->app->render($this->getView());
  }

}