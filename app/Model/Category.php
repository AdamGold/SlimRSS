<?php
/**
 * Admin Category model
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Model;

/**
 * Admin Category model
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Category extends Model
{
  public function getCat($id)
  {
    $sth = $this->pdo->prepare('SELECT * FROM ' . DB_PREFIX . 'categories WHERE id = ?');
    $sth->execute(array($id));
    return $sth->fetch();
  }

  public function getFirstCatID()
  {
    $sth = $this->pdo->prepare('SELECT id FROM ' . DB_PREFIX . 'posts ORDER BY id DESC');
    $sth->execute();
    return $sth->fetchColumn();
  }

  /**
   *
   * @param  int  $page
   * @param  int  $limit
   * @param  int  $cat
   *
   * @return array  all posts for a specific category
   */
  public function getPostsForCat($page, $limit, $cat)
  {
    $allPosts = true;
    $query = 'SELECT p.*, cn.title as channel_title FROM ' . DB_PREFIX . 'posts p JOIN ' . DB_PREFIX .'posts_categories pc'
    . ' ON pc.post_id = p.id LEFT JOIN ' . DB_PREFIX . 'channels cn ON cn.id = p.channel_id WHERE pc.cat_id = ? ORDER BY p.id DESC LIMIT ?, ?';

    $statement = $this->pdo->prepare($query);
    $statement->bindValue(1, $cat);
    $param = array((($page - 1) * $limit), $limit);
    foreach($param as $k => $v) {
      $statement->bindValue(($k+2), $v, \PDO::PARAM_INT);
    }
    $statement->execute();
    return $statement->fetchAll();
  }
}