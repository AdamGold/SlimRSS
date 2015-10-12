<?php
/**
 * The Model Factory - Creation of new models
 *
 * "the mother" of models. creates the database connection and the models.
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Core;

/**
 * ModelFactory
 *
 *  instantiate database connection. require models (autoloading) and passes the connection as an argument.
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class ModelFactory
{
  public static $connection; // needs to be public for usage out of the MVC - for example, in rssfeed.php.

  /**
   * instantiate database connection
   */
  public function __construct()
  {
    if (empty(self::$connection)) {
      $dsn = 'mysql:host=' . DB_HOSTNAME . ';dbname=' . DB_NAME . ';charset=utf8';
      self::$connection = new \PDO($dsn, DB_USERNAME, DB_PASSWORD);
      self::$connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
      /* Set prepared statement emulation depending on server version */
      $emulatePreparesBelowVersion = '5.1.17';
      $serverVersion = self::$connection->getAttribute(\PDO::ATTR_SERVER_VERSION);
      $emulatePrepares = (version_compare($serverVersion, $emulatePreparesBelowVersion, '<'));
      self::$connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, $emulatePrepares);
    }
  }

  /**
   * calls the model classes
   *
   * @param  string  $controller
   * @param  boolean  $admin
   *
   * @return Model  returns the requested model class
   */
  public function buildModel($controller, $admin)
  {
    $adminNameSpace = '';
    if (true === $admin) {
      $adminNameSpace = ucwords(ADMIN_FOLDER) . '\\';
    }
    $controller = ucwords($controller);
    $class = '\Model\\' . $adminNameSpace . $controller;
    return new $class(self::$connection);
  }
}