<?php
/**
 * Admin User Model
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Model\Admin;

/**
 * Admin User Model
 *
 * responsible for user database actions
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class User extends \Model\Model
{
  /**
   *
   * @param  string  $u username
   * @param  string  $p password
   *
   * @return int     returns the user id if exists and password correct, 0 otherwise
   */
  public function checkLoginCred($u, $p)
  {
    $sth = $this->pdo->prepare('SELECT * FROM ' . DB_PREFIX . 'users WHERE name = ?');
    $sth->execute(array($u));
    $user = $sth->fetch();

    if (true === password_verify($p, $user['password']) && $user['role'] = 2) // role 2 is admin
      return $user['id'];

    return 0;
  }
}