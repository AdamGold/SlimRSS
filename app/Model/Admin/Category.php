<?php
/**
 * Admin Category model
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Model\Admin;

/**
 * Admin Category model
 *
 * responsible for all category database actions (queries etc.)
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Category extends \Model\Model
{
  public function create($type, $data)
  {
    $form_prefix = $type . '_';
    $title = $data[$form_prefix . 'title'];
    $sth = $this->pdo->prepare('INSERT INTO ' . DB_PREFIX . 'categories (title) VALUES (?)');
    return $sth->execute(array($title));
  }

  public function update($type, $data, $cat)
  {
    $form_prefix = $type . '_';
    $sth = $this->pdo->prepare('UPDATE ' . DB_PREFIX . 'categories SET title = :title WHERE id = :cid');
    $sth->bindParam(':cid', $cat['id'], \PDO::PARAM_INT);
    $sth->bindParam(':title' , $data[$form_prefix . 'title']);

    return $sth->execute();
  }

  /**
   * get associated channels
   *
   * returns an array that contains the categories ids as keys,
   * and as values it contains arrays of the channels titles related to each category ID
   *
   * @param  array  array of categories data
   *
   * @return array
   */
  public function getAssosChannels($cats)
  {
    $channels = array();
    $sth = $this->pdo->prepare('SELECT cn.title FROM ' . DB_PREFIX . 'channels cn JOIN ' . DB_PREFIX .'channels_categories cr'
    . ' ON cr.channel_id = cn.id WHERE cr.cat_id = :cid');
    foreach ($cats as $cat) {
      $sth->bindValue(':cid', $cat['id']);
      $sth->execute();
      $channels[$cat['id']] = $sth->fetchAll();
    }
    return $channels;
  }

  public function delete($id)
  {
    $sth = $this->pdo->prepare('DELETE FROM ' . DB_PREFIX . 'categories WHERE id =  ?');
    return $sth->execute(array($id));
  }

  public function get($id)
  {
    $sth = $this->pdo->prepare('SELECT * FROM ' . DB_PREFIX . 'categories WHERE id = ?');
    $sth->execute(array($id));
    return $sth->fetch();
  }

  public function getCatCount()
  {
    $sth = $this->pdo->prepare('SELECT count(id) FROM ' . DB_PREFIX . 'categories');
    $sth->execute();
    return $sth->fetchColumn();
  }
}