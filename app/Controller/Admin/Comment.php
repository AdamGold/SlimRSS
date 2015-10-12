<?php
/**
 * Admin Comment controller
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Controller\Admin;

/**
 * Admin Comment controller
 *
 * responsible for all of the comment pages in the admin panel
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Comment extends \Controller\Controller
{
  /**
   * renders the table of comments
   */
  public function index()
  {
    $page = (empty($this->urlParams['q1'])) ? 1 : $this->urlParams['q1'];
    $columns = array('Author', 'Content', 'Date');
    $comments = $this->model->getComments($page, $this->adminTableLimit);
    $commentCount = $this->model->getCommentCount();
    $totalPages = ceil($commentCount / $this->adminTableLimit);
    $method = __FUNCTION__;
    $class = $this->type;
    $paginationUrl = $class . '/' . $method;
    $this->app->render($this->getView(), array('comments' => $comments, 'totalPages' => $totalPages,
                                                'paginationUrl' => $paginationUrl, 'pageNum' => $page, 'columns' => $columns));
  }
}