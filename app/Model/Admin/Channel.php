<?php
/**
 * Admin Channel model
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Model\Admin;

/**
 * Admin Channel model
 *
 * responsible for all channel database actions (queries etc.)
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Channel extends \Model\Model
{
  public function create($type, $data)
  {
    $form_prefix = $type . '_';
    $sth = $this->pdo->prepare('INSERT INTO ' . DB_PREFIX . 'channels (title, link, description) VALUES (:title, :link, :description)');
    $values = array('title', 'link', 'description');
    foreach ($values as $v) {
      $sth->bindParam(':' . $v, $data[$form_prefix . $v]);
    }

    if (false === $sth->execute())
      return false;

    $sthMapping = $this->pdo->prepare('INSERT INTO ' . DB_PREFIX . 'channels_categories (channel_id, cat_id) VALUES (:channel_id, :cat_id)');
    $lastId = $this->pdo->lastInsertId();
    $sthMapping->bindParam(':channel_id', $lastId, \PDO::PARAM_INT);
    $cats = $data[$form_prefix . 'cats'];
    /* bind each category id and execute separately */
    foreach($cats as $catId) {
      $sthMapping->bindValue(':cat_id', $catId);
      if (false === $sthMapping->execute())
        return false;
    }

    return true;
  }

  public function update($type, $data, $cnl)
  {
    $form_prefix = $type . '_';
    $sth = $this->pdo->prepare('UPDATE ' . DB_PREFIX . 'channels SET title = :title, link = :link, description = :description WHERE id = :cid');
    $sth->bindParam(':cid', $cnl['id'], \PDO::PARAM_INT);
    $values = array('title', 'link', 'description');
    foreach ($values as $v) {
      $sth->bindParam(':' . $v, $data[$form_prefix . $v]);
    }

    $sthMappingDelete = $this->pdo->prepare('DELETE FROM ' . DB_PREFIX . 'channels_categories WHERE channel_id =  ?');
    $sthMappingDelete->bindParam(1, $cnl['id'], \PDO::PARAM_INT);
    if (false === $sthMappingDelete->execute())
      return false;

    $sthMappingInsert = $this->pdo->prepare('INSERT INTO ' . DB_PREFIX . 'channels_categories (channel_id, cat_id) VALUES (:channel_id, :cat_id)');
    $sthMappingInsert->bindParam(':channel_id', $cnl['id'], \PDO::PARAM_INT);
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
   * if given a channel id - return the array of all categories
   * that belong to that channel. if given an array of channels,
   * return a multi dimensional array with the channel ids given
   * as keys, and all the cats that belong to each channel as values
   *
   * @param  array/int  array of channels or just the id of one channel
   *
   * @return array
   */
  public function getAssosCats($channelOrArray)
  {
    $cats = array();
    $sth = $this->pdo->prepare('SELECT c.title FROM ' . DB_PREFIX . 'categories c JOIN ' . DB_PREFIX .'channels_categories cr'
    . ' ON cr.cat_id = c.id WHERE cr.channel_id = :cid');
    if (is_array($channelOrArray)) {
      foreach ($channelOrArray as $channel) {
        $sth->bindValue(':cid', $channel['id']);
        $sth->execute();
        $cats[$channel['id']] = $sth->fetchAll();
      }
      return $cats;
    }
    $sth->bindValue(':cid', $channelOrArray);
    $sth->execute();
    return $sth->fetchAll();
  }

  public function delete($id)
  {
    $sth1 = $this->pdo->prepare('DELETE FROM ' . DB_PREFIX . 'channels_categories WHERE channel_id =  ?');
    $sth2 = $this->pdo->prepare('DELETE FROM ' . DB_PREFIX . 'channels WHERE id =  ?');

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
    $sth = $this->pdo->prepare('SELECT * FROM ' . DB_PREFIX . 'channels WHERE id = ?');
    $sth->execute(array($id));
    return $sth->fetch();
  }

  public function getCnlCount()
  {
    $sth = $this->pdo->prepare('SELECT count(id) FROM ' . DB_PREFIX . 'channels');
    $sth->execute();
    return $sth->fetchColumn();
  }
}