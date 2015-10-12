<?php
/**
 * Admin Post controller
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Model;

/**
 * Admin Post controller
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Post extends Model
{
  public function getPost($id)
  {
    $sth = $this->pdo->prepare('SELECT * FROM ' . DB_PREFIX . 'posts WHERE id = ?');
    $sth->execute(array($id));
    return $sth->fetch();
  }

  public function getFirstPostID()
  {
    $sth = $this->pdo->prepare('SELECT id FROM ' . DB_PREFIX . 'posts ORDER BY id DESC');
    $sth->execute();
    return $sth->fetchColumn();
  }

  /**
   *
   * @param  string  $type form prefix
   * @param  array   $data form data
   * @param  array   $post post data (the post that the comment was published in)
   *
   * @return boolean  true if query succeeded, false otherwise
   */
  public function createComment($type, $data, $post)
  {
    $form_prefix = $type . '_';
    $sth = $this->pdo->prepare('INSERT INTO ' . DB_PREFIX . 'comments (content, name, post_id) VALUES (:comment_content, :comment_name, :pid)');
    $fields = array('comment_content', 'comment_name');
    foreach ($fields as $v) {
      $sth->bindParam(':' . $v, $data[$form_prefix . $v]);
    }

    $sth->bindParam(':pid', $post['id']);
    return $sth->execute();
  }

  /**
   *
   * @param  int  $id post id
   *
   * @return array  all comments for a specific post
   */
  public function getComments($id)
  {
    $sth = $this->pdo->prepare('SELECT * FROM ' . DB_PREFIX . 'comments WHERE post_id = ? ORDER BY id DESC');
    $sth->execute(array($id));
    return $sth->fetchAll();
  }
}