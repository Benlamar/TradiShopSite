<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/proTradi/core/init.php';
  include 'include/head.php';

  $email = ((isset($_POST['email']))?sanitize($_POST['email']):'');
  $email = trim($email);
  $password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
  $password = trim($password);
  $errors = array();
?>

<style>
  body
  {
    background-image: url("/proTradi/image/banner/background.jpg");
    background-size: 100vw 100vh;
    background-attachment: fixed;
  }
</style>
<div id="login-form">
  <div>
    <?php
      if($_POST)
      {
        //form validation
        if(empty($_POST['email']) || empty($_POST['password']))
        {
          $errors[] = 'You must provide email and password.';
        }
        //validate Email
        if(!filter_var($email,FILTER_VALIDATE_EMAIL))
        {
          $errors[] = 'You must enter a valid email.';
        }

        //check if user exist
        $query = $db->query("SELECT * FROM users WHERE email = '$email'");
        $user = mysqli_fetch_assoc($query);
        $userCount = mysqli_num_rows($query);
        if($userCount < 1){
          $errors[] = 'That email do not exit in database.';
        }

        if(!password_verify($password, $user['password']))
        {
          $errors[] = 'password did not matched.';
        }

        //check for error
        if(!empty($errors))
        {
          echo display_errors($errors);
        }else {
          //log user in
          $user_id = $user['id'];
          login($user_id);
        }
      }
    ?>
  </div>
  <h2 class="text-center">Login</h2>
  <form action="login.php" method="post">
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="text" name="email" id="email" class="form-control" value="<?=$email;?>">
    </div>

    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" class="form-control" value="<?=$password;?>">
    </div>

    <div class="form-group">
      <input type="submit" value="Login" class="btn btn-primary">
    </div>
  </form>
  <p class="text-right"><a href="/proTradi/index.php" atl="home">Visit Site</a></p>
</div>

<?php include 'include/footer.php';?>
