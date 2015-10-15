<?php
/**
 * The Application File
 *
 * Where the magic happens. Creates a new Slim application, defines the routes
 * and instantiate the controller responsible for the current page.
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Core;

/**
 * The Application class instantiate the Slim Application and configures (dynamically)
 * the routes. It uses ControllerFactory to build the controllers for each page.
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Application
{
  public function __construct()
  {
    $controllerFactory = new \Core\ControllerFactory($app); // the class that builds the controllers
    $view = new \Slim\Views\Twig;
    $app = new \Slim\Slim(array(
        'debug' => DEBUG,
        'view' => $view,
        'templates.path' => HOME . '/' . APP . '/View'
    ));

    $app->notFound(function () use ($app) {
      $app->render('errors/404.html');
    });

    $view->parserExtensions = array( // load my twig extensions (so the templates will have my choice of variables)
        new \MyTwigExtension($controllerFactory),
    );

    $mainRoute = '/';
    if (! empty(SUB_FOLDER)) { // is the whole site in a subdirectory?
      $mainRoute .= SUB_FOLDER . '(/)';
    }

    $checkQueries = function ($q) { // our queries must be numerical for security's sake
      if (! empty($q)) {
        if (! is_numeric($q))
          return false;
        if ($q <= 0)
          return false;
      }

      return true;
    };


    $app->group($mainRoute, function () use ($app, $checkQueries, $controllerFactory) {
       // the admin route
      $app->map(ADMIN_FOLDER . '(/)(:controller)(/)(:action)(/)(:query)(/)', function ($controller = '', $action = '', $query = '') use ($app, $checkQueries, $controllerFactory) {
        if (false === $checkQueries($query)) {
          $app->notFound();
        } else {
          $controllerFactory->buildController($controller, $action, true, $query);
        }
      })->via('POST', 'GET');


      // front end routing
      $app->map('(:controller)(/)(:action)(/)(:query)(/)(:query2)(/)', function ($controller = '', $action = '', $query = '', $query2 = '') use ($app, $checkQueries, $controllerFactory) {
        if (false === $checkQueries($query) || false === $checkQueries($query2)) {
          $app->notFound();
        } else {
          $controllerFactory->buildController($controller, $action, false, $query, $query2);
        }
      })->via('POST', 'GET');
    });
    $app->run(); // run Slim Application
  }
}