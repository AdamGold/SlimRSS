<?php
/**
 * Controller File - the inherited class
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
namespace Controller;

/**
 * Controller
 *
 * contains the constructor for all controllers and some other vital functions for
 * multiple controllers
 *
 * @package    SlimRSS
 * @author     AdamGold <adamgold7@gmail.com>
 * @copyright  2015 AdamGold
 */
class Controller
{
  protected $app,
            $messages,
            $classMessages,
            $model,
            $viewFolder,
            $urlParams,
            $type;
  public $limit = 3, // front end - posts per page
          $adminTableLimit = 10; // admin panel - posts per page (in the table)

  /**
   * instantiate the model for the controller and all the instance params
   * like the url arguments, Slim application, custom messages configured in config.php, etc.
   * also authenticates user if we're located in the admin panel
   *
   * @param Slim $app  Slim application
   * @param boolean $admin
   * @param array  urlParams contains the URL arguments
   */
  public function __construct($app, $admin, $urlParams)
  {
    $this->urlParams = $urlParams;
    $this->app = $app;
    $this->messages = unserialize(CUSTOM_MESSAGES);
    $this->type = strtolower($this->getClassName(get_called_class()));
    $adminStr = (false === $admin) ? '' : 'admin_';
    $messagesType = $adminStr . $this->type;
    if (isset($this->messages[$messagesType])) {
      $this->classMessages = $this->messages[$messagesType];
    } else {
      $this->classMessages = $this->messages[$adminStr . 'general'];
    }
    $modelFactory = new \Core\ModelFactory;
    $this->model = $modelFactory->buildModel($this->urlParams['controller'], $admin);
    $this->setViewFolder($admin);

    /* authenticate in admin */
    if (true === $admin) {
      if ((strtolower($this->urlParams['controller']) != 'user' || strtolower($this->urlParams['action']) != 'login') && false === $this->authenticate('admin')) {
        $this->redirectToLogin();
        return;
      } else if (strtolower($this->urlParams['controller']) == 'user' && strtolower($this->urlParams['action']) == 'login' && true === $this->authenticate('admin')) {
        $this->redirectInAdmin();
        return;
      }
    }
  }

  /**
   * sets the correct view directory for each controller
   *
   * @param boolean  $admin
   */
  protected function setViewFolder($admin)
  {
    $adminFolder = '';
    if (true === $admin) {
      $adminFolder = ADMIN_FOLDER . '/';
    }
    $controller = strtolower($this->urlParams['controller']);
    $this->viewFolder = $adminFolder . $controller;
  }

  protected function getView()
  {
    return $this->viewFolder . '/' . $this->urlParams['action'] . '.html';
  }

  /**
   *
   * @param  string  $class class name with a namespace
   *
   * @return string  class name without the namespace
   */
  protected function getClassName($class)
  {
    $function = new \ReflectionClass($class);
    return $function->getShortName();
  }

  /**
   * redirects to a specific location inside the admin panel
   *
   * @param  string  $to location to redirect to
   */
  protected function redirectInAdmin($to = '')
  {
    $headerUrl = '/' . SUB_FOLDER . '/' . ADMIN_FOLDER . '/' . strtolower($to);
    $this->app->redirect($headerUrl);
  }

  protected function redirectToLogin()
  {
    $this->redirectInAdmin('user/login');
  }

  /**
   * validates input and calls the right model method to create/update database
   *
   * @param  array  $data    the POST data
   * @param  array  $fields  the form fields names
   * @param  string $action  the model's method to be called
   * @param  array  $dbRow   if we're updating or creating a row related to another row, we need to have the related database row
   *
   * @return array  array that contains the success status - true or false and the correct message for that status (fields are empty, sql error etc.)
   */
  protected function createProccess($data, $fields, $action = 'create', $dbRow = '')
  {
    $errors = array();
    $emptyFields = array();
    $success = false;
    $returnArr = array('success' => &$success, 'message' => '');
    foreach ($fields as $field) {
      if (empty($data[$this->type . '_' . $field])) {
        $returnArr['message'] = $this->messages['field_empty'];
        return $returnArr;
      }
    }

    if (! empty($dbRow)) {
      $success = $this->model->$action($this->type, $data, $dbRow);
    } else {
      $success = $this->model->$action($this->type, $data);
    }
    if (false === $success) {
      $returnArr['message'] = $this->messages['sql_error'];
      return $returnArr;
    }

    $returnArr['message'] = $this->classMessages[$action];
    return $returnArr;
  }

  /**
   * returns true if user is logged in and validates against the given role and false otherwise
   *
   * @param  string  $role check current user against this role
   *
   * @return boolean  true if user is logged in and validates against the role and false otherwise
   */
  protected function authenticate($role = 'member')
  {
    $roles = array('member' => 1, 'admin' => 2);
    $check = $roles[$role];
    if (! isset($_SESSION['logged_in']) || ! isset($_SESSION['user_id']))
      return false;

    if (! is_numeric($_SESSION['user_id']) || ! is_bool($_SESSION['logged_in']))
      return false;

    return $this->model->checkRole($check, $_SESSION['user_id']);
  }

  /**
   * global delete function - used in all of the admin controllers
   * for removing database rows
   */
  public function delete()
  {
    $id = $this->urlParams['q1'];
    if (empty($id)) {
      $this->redirectInAdmin($this->type);
      return;
    }
    if (false === $this->model->delete($id)){
      $this->app->flash('errors', $this->messages['sql_error']);
    } else {
      $this->app->flash('success', $this->classMessages['delete']);
    }
    $this->redirectInAdmin($this->type);
  }
}