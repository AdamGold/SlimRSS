<?php
/**
 * System config - database settings, custom messages, folders names etc.
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */

/* Database config */
define('DB_TYPE', 'mysql');
define('DB_HOSTNAME', 'DBHOSTNAME');
define('DB_USERNAME', 'DBUSERNAME');
define('DB_PASSWORD', 'DBPASSWORD');
define('DB_NAME', 'DBNAME');
define('DB_PREFIX', "DBPREFIX_");

/* Folders config */
/**
 * root directory
 */
define('HOME', dirname(__FILE__));
/**
 * is the whole site in a subdirectory?
 */
define('SUB_FOLDER', 'SUBFOLDER');
/**
 * application folder (also in composer.json)
 */
define('APP', 'app');
/**
 * public folder
 */
define('PUBLIC_FOLDER', 'public');
/**
 * bootstrap folder
 */
define('BOOTSTRAP_FOLDER', 'vendor/twbs/bootstrap/dist');
/**
 * controllers folder
 */
define('CONTROLLERS_FOLDER', 'Controller');
/**
 * admin folder - also configures the names of the sub folders to look in View and Model
 */
define('ADMIN_FOLDER', 'admin');
/**
 * debug mode
 */
define('DEBUG', false);

/* Custom config */
define('CUSTOM_MESSAGES', serialize(array(
  'general' => array(),
  'admin_general' => array(
    'create' => 'The item has been created successfully.',
    'update' => 'The item has been updated successfully.',
    'delete' => 'The item has been deleted successfully.',
    'not_found' => 'The requested item can not be found.'
  ),
  'admin_category' => array(
    'create' => 'The category has been created successfully.',
    'update' => 'The category has been updated successfully.',
    'delete' => 'The category has been deleted successfully.',
    'not_found' => 'The requested category can not be found.'
  ),
  'admin_channel' => array(
    'create' => 'The channel has been created successfully.',
    'update' => 'The channel has been updated successfully.',
    'delete' => 'The channel has been deleted successfully.',
    'not_found' => 'The requested channel can not be found.'
  ),
  'admin_post' => array(
    'create' => 'The post has been created successfully.',
    'update' => 'The post has been updated successfully.',
    'delete' => 'The post has been deleted successfully.',
    'not_found' => 'The requested post can not be found.'
  ),
  'post' => array(
    'createComment' => 'Your comment has been added.',
  ),
  'admin_user' => array(
    'login_failed' => 'The credintials do not match to the database.'
  ),
  'admin_comment' => array(
    'delete' => 'The comment has been deleted successfully.',
  ),
  'field_empty' => 'One of the required fields is empty. Please fill it up and try again.',
  'sql_error' => 'There was a problem proccessing the information. Please try again.'
)));

/**
 * The name that will be shown in the titles of the pages
 */
define('SITE_NAME', 'SITENAME');

/* posts limit */
define('POSTS_LIMIT', 3); // posts per page
define('ADMIN_TABLE_LIMIT', 10); // items per page in all admin tables