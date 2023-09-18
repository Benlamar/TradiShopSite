<?php
  require_once '../core/init.php';
  if(!is_logged_in())
  {
    header('Location: login.php');
  }
  include 'include/head.php';
  include "include/navigation.php";

?>
<h2>Admin page</h2>
<?php include 'include/footer.php'; ?>
