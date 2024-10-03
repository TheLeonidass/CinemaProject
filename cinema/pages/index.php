<?php
session_start();
require_once '../includes/bootstrap.php';
require_once "../includes/db_config.php";
require_once '../includes/footer.php';

if (isset($_POST['login'])) {
  $emailOrUsername = $_POST['emailorusername'];
  $password = $_POST['password'];

  $database = new Connection();
  $connection = $database->getConnection();

  //for user login
  if ($database->userLogin($emailOrUsername, $password)) {
    $_SESSION['logged_in'] = true;
    $_SESSION['emailorusername'] = $emailOrUsername;

    header("Location: welcome.php");
    exit();

    //for admin login - redirection to admin panel
  } elseif ($database->adminLogin($emailOrUsername, $password)) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['emailorusername'] = $emailOrUsername;

    header("Location: admin.php");
    exit();


  } else {
    echo "Login failed. Please check your credentials!";
  }
  $database->closeConnection();
}
?>

<html>
<?php echo bootstrap(); ?>

<body class="image img-fluid">
  <div class="d-flex text-center flex-column align-items-center vh-100 justify-content-around container-xl">
    <form method="post">
      <div class="login mb-3">
        <h2>Welcome to Movie Night!</h2>
      </div>
      <div class="mb-3">
        <div>
          <input type="text" class="form-control" placeholder="Email or Username" name="emailorusername" required />
        </div>
        <div class="mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password" required />
        </div>
        <div class="col-md-12">
          <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </div>
        <a href="register.php">Don't have an account? </a>
      </div>
    </form>
  </div>
  <?php
  echo footer();
  ?>
</body>

</html>