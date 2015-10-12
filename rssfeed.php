<?php
/**
 * The RSS Handler
 *
 * Parses the feeds from the channels configured in the admin panel
 * and inserts them as posts to the database
 * meant to be used as a cron job
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */

require 'config.php';
require HOME . '/vendor/autoload.php';
$modelFactory = new \Core\ModelFactory;
$pdo = $modelFactory::$connection;


$sth = $pdo->prepare('SELECT * FROM ' . DB_PREFIX . 'channels ORDER BY id DESC');
$sth->execute();
$channels = $sth->fetchAll();

foreach ($channels as $channel) {
	libxml_use_internal_errors(true);
	if (false === strpos($channel['link'], '.xml')) // this is not an xml file
		break;
	$rss = simpleXML_load_file($channel['link']);
	$items = $rss->channel->item;
	$sth = $pdo->prepare('SELECT * FROM ' . DB_PREFIX . 'posts WHERE channel_id = ? ORDER BY date DESC'); // last post from this channel
	$sth->execute(array($channel['id']));
	$lastPost = $sth->fetch();
	/* get categories of channel */
	$sth = $pdo->prepare('SELECT c.id FROM ' . DB_PREFIX . 'categories c JOIN ' . DB_PREFIX .'channels_categories cr'
	. ' ON cr.cat_id = c.id WHERE cr.channel_id = :cid');
	$sth->bindValue(':cid', $channel['id']);
	$sth->execute();
	$cats = $sth->fetchAll();
	/* check to see if we even need to update that channel (check the last post that was published from this channel) */
	foreach ($items as $item) {
		$date = $item->pubDate->__toString();
		$newDate = date('Y-m-d H:i:s', strtotime($date));
		if (! empty($lastPost)) {
			if (new DateTime($newDate) <= new DateTime($lastPost['date'])) {
				break;
			}
		}

		$sth = $pdo->prepare('INSERT INTO ' . DB_PREFIX . 'posts (title, content, image, channel_id, link, date) VALUES (:title, :content, :image, :cid, :link, :date)');

		$cid = $channel['id'];
		$title = $item->title->__toString();
		$content = $item->shortDescription->__toString(); // shortDescription?
		if (empty($content))
			$content = $item->description->__toString();
		$link = $item->link->__toString();
		$date = $newDate;
		/* handling the image */
		$image = '';
		if ($media = $item->children('media', true) && (! empty($media->content) || ! empty($media->group->content))) {
			$group = $media->group;
			if (empty($group))
				$group = $media; // sometimes there's no media:group in the rss, just media:content
			foreach ($group->content as $v) {
				$att = $v->attributes();
				if (isset($att['isDefault']) && true === $att['isDefault'])
					$image = (string) $att['url'];
			}
		} else {
			if ($media = $item->enclosure) {
				$image = (string) $media->attributes()['url'];
			} else {
				// mako for example has this field
				if (! empty($item->image624X383)) {
					$image = $item->image624X383;
				} else {
					$doc = new DOMDocument();
					$doc->loadHTML($item->description);
					$xpath = new DOMXPath($doc);
					$image = $xpath->evaluate("string(//img/@src)");
				}
			}
		}

		/* special cases in specific rss (sky news etc) */
		$image = str_replace('70x50', '736x414', $image);

		$sth->bindParam(':title', $title);
		$sth->bindParam(':content', $content);
		$sth->bindParam(':image', $image);
		$sth->bindParam(':link', $link);
		$sth->bindParam(':date', $date);
		$sth->bindParam(':cid', $cid);

		$sth->execute();
		$sthMapping = $pdo->prepare('INSERT INTO ' . DB_PREFIX . 'posts_categories (post_id, cat_id) VALUES (:post_id, :cat_id)');
		$lastId = $pdo->lastInsertId();
		$sthMapping->bindParam(':post_id', $lastId, \PDO::PARAM_INT);
		foreach ($cats as $cat) {
		  $sthMapping->bindValue(':cat_id', $cat['id']);
		  $sthMapping->execute();
		}
	}
}