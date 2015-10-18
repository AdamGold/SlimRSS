<?php
/**
 * The Index File
 *
 * The main file that requires all the other files in the application
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
session_cache_limiter(false);
session_start();
require '../config.php';
require HOME . '/vendor/autoload.php';
require HOME . '/' . APP . '/util/MyTwigExtension.php';

new \Core\Application();