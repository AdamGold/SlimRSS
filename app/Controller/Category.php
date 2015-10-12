<?php
/**
 * The category controller
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Controller;

/**
 * Category Class
 *
 * responsible for the front-end category pages
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Category extends Controller
{
  /**
   * renders the display template for a specific category
   * requested in the URL.
   */
  public function show()
  {
    $page = (empty($this->urlParams['q2'])) ? 1 : $this->urlParams['q2'];
    $cat = $this->urlParams['q1'];
    /* if the url argument is empty, get first cat ID - if it does not exist, display a 404 error */
    if (empty($cat)) {
      $firstID = $this->model->getFirstCatID();
      if ($firstID != 0) {
        $cat = $firstID;
      } else {
        $this->app->notFound();
        return;
      }
    }
    $posts = $this->model->getPostsForCat($page, $this->limit, $cat);
    $postsCount = $this->model->getPostCount(array('cat' => $cat));
    $method = __FUNCTION__;
    $class = strtolower($this->getClassName(__CLASS__));
    $paginationUrl = $class . '/' . $method . '/' . $cat;
    $totalPages = ceil($postsCount / $this->limit);
    $cat = $this->model->getCat($cat);
    if (false === $cat) {
      $this->app->notFound();
      return;
    }

    $this->app->render($this->getView(), array('cat' => $cat, 'posts' => $posts, 'page' => $page,
                                                'totalPages' => $totalPages, 'paginationUrl' => $paginationUrl));
  }
}