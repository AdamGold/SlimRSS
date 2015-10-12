<?php
/**
 * Admin Post model
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Model\Admin;

/**
 * Admin Post model
 *
 * responsible for all post database actions (queries etc.)
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Post extends \Model\Model
{
  /**
   *
   * @param  int  $page
   * @param  int  $limit
   *
   * @return array  all self created posts (channel_id = 0) for a specific page
   */
  public function getManualPosts($page, $limit)
  {
    $query = 'SELECT p.*, GROUP_CONCAT(c.id ORDER BY c.id) as cats_id,'
    . ' GROUP_CONCAT(c.title ORDER BY c.id) as cats_title'
    . ' FROM ' . DB_PREFIX . 'posts p JOIN ' . DB_PREFIX .'posts_categories pc ON pc.post_id = p.id'
    . ' JOIN ' . DB_PREFIX . 'categories c ON pc.cat_id = c.id'
    . ' WHERE channel_id = :cid GROUP BY p.id ORDER BY p.id DESC LIMIT :min, :max';
    $sth = $this->pdo->prepare($query);
    $sth->bindValue(':cid', 0); // channel id 0 means that this post was added manually through the admin panel
    $param = array('min' => (($page - 1) * $limit), 'max' => $limit);
    foreach($param as $k => $v) {
      $sth->bindValue(':' . $k, $v, \PDO::PARAM_INT);
    }
    $sth->execute();

    return $sth->fetchAll();
  }

  public function create($type, $data)
  {
    $form_prefix = $type . '_';
    $sth = $this->pdo->prepare('INSERT INTO ' . DB_PREFIX . 'posts (title, content, image) VALUES (:title, :content, :image)');
    $values = array('title', 'content', 'image');
    foreach ($values as $v) {
      $sth->bindParam(':' . $v, $data[$form_prefix . $v]);
    }

    if (false === $sth->execute())
      return false;

    $sthMapping = $this->pdo->prepare('INSERT INTO ' . DB_PREFIX . 'posts_categories (post_id, cat_id) VALUES (:post_id, :cat_id)');
    $lastId = $this->pdo->lastInsertId();
    $sthMapping->bindParam(':post_id', $lastId, \PDO::PARAM_INT);
    $cats = $data[$form_prefix . 'cats'];
    foreach($cats as $catId) {
      $sthMapping->bindValue(':cat_id', $catId);
      if (false === $sthMapping->execute())
        return false;
    }

    return true;
  }

  public function update($type, $data, $post)
  {
    $form_prefix = $type . '_';
    $sth = $this->pdo->prepare('UPDATE ' . DB_PREFIX . 'posts SET title = :title, content = :content, image = :image WHERE id = :pid');
    $sth->bindParam(':pid', $post['id'], \PDO::PARAM_INT);
    $values = array('title', 'content', 'image');
    foreach ($values as $v) {
      $sth->bindParam(':' . $v, $data[$form_prefix . $v]);
    }

    $sthMappingDelete = $this->pdo->prepare('DELETE FROM ' . DB_PREFIX . 'posts_categories WHERE post_id =  ?');
    $sthMappingDelete->bindParam(1, $post['id'], \PDO::PARAM_INT);
    if (false === $sthMappingDelete->execute())
      return false;

    $sthMappingInsert = $this->pdo->prepare('INSERT INTO ' . DB_PREFIX . 'posts_categories (post_id, cat_id) VALUES (:post_id, :cat_id)');
    $sthMappingInsert->bindParam(':post_id', $post['id'], \PDO::PARAM_INT);
    $cats = $data[$form_prefix . 'cats'];
    foreach($cats as $catId) {
      $sthMappingInsert->bindValue(':cat_id', $catId);
      if (false === $sthMappingInsert->execute())
        return false;
    }

    return $sth->execute(); // update the channel only if the categories update went okay
  }

  /**
   * get associated categories
   *
   * if given a post id - return the array of all categories
   * that belong to that post. if given an array of posts,
   * return a multi dimensional array with the post ids given
   * as keys, and all the cats that belong to each post as values
   *
   * @param  array/int  array of posts or just the id of one post
   *
   * @return array
   */
  public function getAssosCats($postOrArray)
  {
    $cats = array();
    $sth = $this->pdo->prepare('SELECT c.title FROM ' . DB_PREFIX . 'categories c JOIN ' . DB_PREFIX .'posts_categories pc'
    . ' ON pc.cat_id = c.id WHERE pc.post_id = :pid');
    if (is_array($postOrArray)) {
      foreach ($postOrArray as $post) {
        $sth->bindValue(':pid', $post['id']);
        $sth->execute();
        $cats[$post['id']] = $sth->fetchAll();
      }
      return $cats;
    }
    $sth->bindValue(':pid', $postOrArray);
    $sth->execute();
    return $sth->fetchAll();
  }

  public function delete($id)
  {
    $sth1 = $this->pdo->prepare('DELETE FROM ' . DB_PREFIX . 'posts_categories WHERE post_id =  ?');
    $sth2 = $this->pdo->prepare('DELETE FROM ' . DB_PREFIX . 'posts WHERE id =  ?');

    for ($i = 1; $i <= 2; $i++) {
      $var = 'sth' . $i;
      $flag = 'flag' . $i;
      $$var->bindValue(1, $id, \PDO::PARAM_INT);
      $$flag = $$var->execute();
    }

    return ($flag1 && $flag2);
  }

  public function get($id)
  {
    $sth = $this->pdo->prepare('SELECT * FROM ' . DB_PREFIX . 'posts WHERE id = ?');
    $sth->execute(array($id));
    return $sth->fetch();
  }
}