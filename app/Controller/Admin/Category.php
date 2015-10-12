<?php
/**
 * Admin Category controller
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Controller\Admin;

/**
 * responsible for all of the admin category pages and actions
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Category extends \Controller\Controller
{
  private $reqFields = array('title');

  /**
   * renders the table containing all categories
   */
  public function index()
  {
    $page = (empty($this->urlParams['q1'])) ? 1 : $this->urlParams['q1'];
    $columns = array('Title', 'Associated Channels');
    $cats = $this->model->getCats($page, $this->adminTableLimit);
    $catCount = $this->model->getCatCount();
    $totalPages = ceil($catCount / $this->adminTableLimit);
    $method = __FUNCTION__;
    $class = $this->type;
    $paginationUrl = $class . '/' . $method;
    $channels = $this->model->getAssosChannels($cats);
    $this->app->render($this->getView(), array('cats' => $cats, 'columns' => $columns, 'channels' => $channels,
                                                'totalPages' => $totalPages, 'paginationUrl' => $paginationUrl,
                                                  'pageNum' => $page));
  }

  /**
   * renders the creation form and calls the createProccess
   * method located in the main Controller class in order to create
   * the database data
   */
  public function create()
  {
    $post = $this->app->request->post();
    if (isset($post['submit'])) {
      $createProccess = $this->createProccess($post, $this->reqFields);
      if (true === $createProccess['success']) {
        $this->app->flash('success', $createProccess['message']);
        $this->redirectInAdmin($this->type);
      } else {
        $this->app->flashNow('errors', $createProccess['message']);
      }
    }
    $this->app->render($this->getView());
  }

  /**
   * renders the edit form and calls the createProccess
   * method located in the main Controller class in order to update
   * the database data
   */
  public function edit()
  {
    $id = (empty($this->urlParams['q1'])) ? 1 : $this->urlParams['q1'];
    $cat = $this->model->get($id);
    if (false === $cat) { // not found
      $this->app->flashNow('errors', $this->classMessages['not_found']);
    }
    $post = $this->app->request->post();
    if (isset($post['submit'])) {
      $createProccess = $this->createProccess($post, $this->reqFields, 'update', $cat);
      if (true === $createProccess['success']) {
        $this->app->flash('success', $createProccess['message']);
        $this->redirectInAdmin($this->type);
      } else {
        $this->app->flashNow('errors', $createProccess['message']);
      }
    }

    $this->app->render($this->getView(), array('cat' => $cat));
  }
}