<?php
/**
 * Admin comment model
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Model\Admin;

/**
 * Admin comment model
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Comment extends \Model\Model
{
  /**
   *
   * @param  int  $page
   * @param  int  $limit
   *
   * @return array  all comments for current page
   */
  public function getComments($page, $limit)
  {
    $sth = $this->pdo->prepare('SELECT * FROM ' . DB_PREFIX . 'comments ORDER BY id DESC LIMIT :min, :max');
    $param = array('min' => (($page - 1) * $limit), 'max' => $limit);
    foreach($param as $k => $v) {
      $sth->bindValue(':' . $k, $v, \PDO::PARAM_INT);
    }
    $sth->execute();
    return $sth->fetchAll();
  }

  public function getCommentCount()
  {
    $sth = $this->pdo->prepare('SELECT count(id) FROM ' . DB_PREFIX . 'comments');
    $sth->execute();
    return $sth->fetchColumn();
  }

  public function delete($id)
  {
    $sth = $this->pdo->prepare('DELETE FROM ' . DB_PREFIX . 'comments WHERE id =  ?');
    return $sth->execute(array($id));
  }
}