<?php
/**
 * Admin User controller
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Controller\Admin;

/**
 * Admin User controller
 *
 * responsible for login and logout of the admin
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class User extends \Controller\Controller
{
  /**
   * admin login - validates information given
   * and if it validates against the database -
   * create a new session
   */
  public function login()
  {
    $error = '';
    $post = $this->app->request->post();
    if (isset($post['submit'])) {
      $fields = array('username', 'password');
      foreach ($fields as $field) {
        if (empty($post[$field])) {
          $error = $this->messages['field_empty'];
        }
      }
      if (empty($error)) {
        foreach ($post as $k => $v) {
          $post[$k] = str_replace("'", "", $v);
        }
        $username = $post['username'];
        $password = $post['password'];
        $id = $this->model->checkLoginCred($username, $password);
        if (is_numeric($id) && $id > 0) {
          session_regenerate_id();
          $_SESSION['user_id'] = $id;
          $_SESSION['logged_in'] = true;
          $this->redirectInAdmin();
        } else {
          $error = $this->classMessages['login_failed'];
        }
      }
    }
    $this->app->flashNow('errors', $error);
    $this->app->render($this->getView());
  }

  /**
   * destroy session of logged in user
   */
  public function logout()
  {
    // Unset all of the session variables.
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session.
    session_destroy();
    $this->redirectToLogin();
  }

}