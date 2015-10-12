<?php
/**
 * Admin channel controller
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Controller\Admin;

/**
 * Admin channel controller
 *
 * responsible for all channel pages and actions
 * in the admin panel
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Channel extends \Controller\Controller
{
  private $reqFields = array('title', 'link', 'description'); // required fields in the creation and edit forms

  /**
   * renders the table containing all channels
   */
  public function index()
  {
    $page = (empty($this->urlParams['q1'])) ? 1 : $this->urlParams['q1'];
    $columns = array('Title', 'Description', 'Link', 'Categories');
    $channels = $this->model->getChannels($page, $this->adminTableLimit);
    $cnlCount = $this->model->getCnlCount();
    $totalPages = ceil($cnlCount / $this->adminTableLimit);
    $method = __FUNCTION__;
    $class = $this->type;
    $paginationUrl = $class . '/' . $method;
    $cats = $this->model->getAssosCats($channels);
    $this->app->render($this->getView(), array('channels' => $channels, 'columns' => $columns, 'cats' => $cats,
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
    $cnl = $this->model->get($id);
    if (false === $cnl) { // not found
      $this->app->flashNow('errors', $this->classMessages['not_found']);
    }
    $allCats = $this->model->getCats();
    $cnlCats = $this->model->getAssosCats($id);
    /* check the checkboxes if the categories belong to our channel */
    foreach ($allCats as $key => $value) {
      if (false !== array_search($value['title'], array_column($cnlCats, 'title'))) {
        $allCats[$key]['checked'] = true;
      }
    }
    $post = $this->app->request->post();
    if (isset($post['submit'])) {
      $createProccess = $this->createProccess($post, $this->reqFields, 'update', $cnl);
      if (true === $createProccess['success']) {
        $this->app->flash('success', $createProccess['message']);
        $this->redirectInAdmin($this->type);
      } else {
        $this->app->flashNow('errors', $createProccess['message']);
      }
    }

    $this->app->render($this->getView(), array('cats' => $allCats, 'cnl' => $cnl));
  }

}