<?php
/**
 * Post controller
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Controller;

/**
 * Post class
 *
 * responsible for the front-end post pages and actions
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Post extends Controller
{
  /**
   * default function - displays all posts
   */
  public function index()
  {
    $page = (empty($this->urlParams['q1'])) ? 1 : $this->urlParams['q1'];

    $posts = $this->model->getPosts($page, $this->limit);
    $postsCount = $this->model->getPostCount();
    /* create the base url for the pagination */
    $method = __FUNCTION__;
    $class = $this->type;
    $paginationUrl = $class . '/' . $method;
    $cats = $this->createCatsArray($posts);
    $totalPages = ceil($postsCount / $this->limit);
    $this->app->render($this->getView(), array('posts' => $posts, 'page' => $page, 'cats' => $cats,
                                                'totalPages' => $totalPages, 'paginationUrl' => $paginationUrl));
  }

  /**
   * creates a new array containing all cats for each post
   *
   * @param  array  $posts
   *
   * @return array  all cats for each post
   */
  protected function createCatsArray($posts)
  {
    $cats = array();
    foreach ($posts as $post) {
      $cats_id = explode(',', $post['cats_id']);
      $cats_title = explode(',', $post['cats_title']);
      foreach ($cats_id as $k => $v) {
        $cats[$post['id']][] = array('id' => $v, 'title' => $cats_title[$k]);
      }
    }

    return $cats;
  }

  /**
   * renders the template to display each post.
   * also responsible for the comment creation - checks for POST data sent
   * and calls the right model function to handle it
   */
  public function show()
  {
    $id = $this->urlParams['q1'];
    if (empty($id)) {
      $firstID = $this->model->getFirstPostID();
      if ($firstID != 0) {
        $id = $firstID;
      } else {
        $this->app->notFound();
        return;
      }
    }

    $post = $this->model->getPost($id);
    $data = $this->app->request->post();
    /* comment creation section */
    if (isset($data['submit'])) {
      $createProccess = $this->createProccess($data, array('comment_content', 'comment_name'), 'createComment', $post);
      if (true === $createProccess['success']) {
        $this->app->flashNow('success', $createProccess['message']);
      } else {
        $this->app->flashNow('errors', $createProccess['message']);
      }
    }
    $comments = $this->model->getComments($id);
    $this->app->render($this->getView(), array('item' => $post, 'comments' => $comments));
  }
}