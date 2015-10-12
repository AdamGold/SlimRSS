<?php
/**
 * Admin Model
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Model;

/**
 * Admin Model
 *
 * the main model class. all other models inherits this class
 * so it contains some global methods and constructs the pdo
 * variable for all models
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Model
{
  protected $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   *
   * @param  int  $page
   * @param  int  $limit
   *
   * @return array  all channel or just a specific number of channels in one page
   */
  public function getChannels($page = '', $limit = '')
  {
    $query = 'SELECT * FROM ' . DB_PREFIX . 'channels ORDER BY id DESC';
    if (! empty($page) && ! empty($limit))
      $query .= ' LIMIT :min, :max';
    $sth = $this->pdo->prepare($query);
    if (! empty($page) && ! empty($limit)) {
      $param = array('min' => (($page - 1) * $limit), 'max' => $limit);
      foreach($param as $k => $v) {
        $sth->bindValue(':' . $k, $v, \PDO::PARAM_INT);
      }
    }
    $sth->execute();
    return $sth->fetchAll();
  }

  /**
   *
   * @param  int  $page
   * @param  int  $limit
   *
   * @return array  all categories or just a specific number of categories in one page
   */
  public function getCats($page = '', $limit = '')
  {
    $query = 'SELECT * FROM ' . DB_PREFIX . 'categories ORDER BY id DESC';
    if (! empty($page) && ! empty($limit))
      $query .= ' LIMIT :min, :max';
    $sth = $this->pdo->prepare($query);
    if (! empty($page) && ! empty($limit)) {
      $param = array('min' => (($page - 1) * $limit), 'max' => $limit);
      foreach($param as $k => $v) {
        $sth->bindValue(':' . $k, $v, \PDO::PARAM_INT);
      }
    }
    $sth->execute();
    return $sth->fetchAll();
  }

  /**
   *
   * @param  int  $role
   * @param  int  $user_id
   *
   * @return boolean  true if the user given validates against that role, false otherwise
   */
  public function checkRole($role, $user_id)
  {
    $sth = $this->pdo->prepare('SELECT * FROM ' . DB_PREFIX . 'users WHERE id = ?');
    $sth->execute(array($user_id));
    $user = $sth->fetch();
    if ($user['role'] != $role)
      return false;

    return true;
  }

  /**
   * get posts
   *
   * returns an array containing specific number of posts,
   * for each post it contains an index called 'cats_id' - post's categories ids separated by commas,
   * cats_title - post's categories title separated by commas and channel_title - post's channel title if any
   *
   * @param  int  $page
   * @param  int  $limit
   *
   * @return array  all posts or just a specific number of posts in one page
   */
  public function getPosts($page, $limit)
  {
    $query = 'SELECT p.*, GROUP_CONCAT(c.id ORDER BY c.id) as cats_id,'
    . ' GROUP_CONCAT(c.title ORDER BY c.id) as cats_title, cn.title as channel_title'
    . ' FROM ' . DB_PREFIX . 'posts p JOIN ' . DB_PREFIX . 'posts_categories pc ON pc.post_id = p.id'
    . ' JOIN ' . DB_PREFIX . 'categories c ON pc.cat_id = c.id'
    . ' LEFT JOIN ' . DB_PREFIX . 'channels cn ON cn.id = p.channel_id GROUP BY p.id ORDER BY p.id DESC LIMIT ?, ?';
    $statement = $this->pdo->prepare($query);
    $param = array((($page - 1) * $limit), $limit);
    foreach($param as $k => $v) {
      $statement->bindValue(($k+1), $v, \PDO::PARAM_INT);
    }
    $statement->execute();

    return $statement->fetchAll();
  }

  /**
   *
   * @param  array   $options - contains the id of a category or a channel
   *
   * @return int      count of all posts or posts for a specific channel / category
   */
  public function getPostCount($options = array())
  {
    if (isset($options['cat'])) { // get posts count for a specific category
      $query = 'SELECT count(p.id) FROM ' . DB_PREFIX . 'posts p'
      . ' JOIN ' . DB_PREFIX . 'posts_categories pc ON pc.post_id = p.id'
      . ' WHERE pc.cat_id = ?';
    } else if (isset($options['cnl'])) { // get posts count for a specific channel
      $query = 'SELECT count(id) FROM ' . DB_PREFIX . 'posts WHERE channel_id = ?';
    } else { // get all posts
      $query = 'SELECT count(id) FROM ' . DB_PREFIX . 'posts';
    }
    $sth = $this->pdo->prepare($query);
    if (isset($options['cat']))
      $sth->bindParam(1, $options['cat'], \PDO::PARAM_INT);
    else if (isset($options['cnl']))
      $sth->bindParam(1, $options['cnl'], \PDO::PARAM_INT);

    $sth->execute();
    return $sth->fetchColumn();
  }
}