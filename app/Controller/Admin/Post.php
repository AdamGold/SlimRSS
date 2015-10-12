<?php
/**
 * Admin Post controller
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Controller\Admin;

/**
 * Admin Post controller
 *
 * responsible for all post related pages and actions
 * in the admin panel
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Post extends \Controller\Controller
{
  private $reqFields = array('title', 'content', 'image');
  /**
   * renders the table of all self created posts
   */
  public function index()
  {
    $page = (empty($this->urlParams['q1'])) ? 1 : $this->urlParams['q1'];
    $columns = array('Title', 'Date', 'Categories');
    $posts = $this->model->getManualPosts($page, $this->adminTableLimit);
    $postsCount = $this->model->getPostCount(array('cnl' => 0)); // channel id 0 means that this post was added manually through the admin panel
    $totalPages = ceil($postsCount / $this->adminTableLimit);
    $cats = $this->model->getAssosCats($posts);
    $method = __FUNCTION__;
    $class = $this->type;
    $paginationUrl = $class . '/' . $method;
    $this->app->render($this->getView(), array('posts' => $posts, 'totalPages' => $totalPages,
                                                'paginationUrl' => $paginationUrl, 'pageNum' => $page,
                                                  'columns' => $columns, 'cats' => $cats));
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
    $cats = $this->model->getCats();
    $this->app->render($this->getView(), array('cats' => $cats));
  }

  /**
   * renders the edit form and calls the createProccess
   * method located in the main Controller class in order to update
   * the database data
   */
  public function edit()
  {
    $id = (empty($this->urlParams['q1'])) ? 1 : $this->urlParams['q1'];
    $post = $this->model->get($id);
    if (false === $post) { // not found
      $this->app->flashNow('errors', $this->classMessages['not_found']);
    }
    $allCats = $this->model->getCats();
    $postCats = $this->model->getAssosCats($id);
    foreach ($allCats as $key => $value) {
      if (false !== array_search($value['title'], array_column($postCats, 'title'))) {
        $allCats[$key]['checked'] = true;
      }
    }
    $data = $this->app->request->post();
    if (isset($data['submit'])) {
      $createProccess = $this->createProccess($data, $this->reqFields, 'update', $post);
      if (true === $createProccess['success']) {
        $this->app->flash('success', $createProccess['message']);
        $this->redirectInAdmin($this->type);
      } else {
        $this->app->flashNow('errors', $createProccess['message']);
      }
    }

    $this->app->render($this->getView(), array('cats' => $allCats, 'post' => $post));
  }

}