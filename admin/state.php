<?php
  require_once '../core/init.php';
  include 'include/head.php';
  include "include/navigation.php";
  if(!is_logged_in())
  {
    login_error_redirect();
  }
  //get state from db
  $sql = "SELECT * FROM state ORDER BY state";
  $results = $db->query($sql);
  $errors = array();

  //edit state
  if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_id = sanitize($edit_id);
    $sql2 = "SELECT * FROM state WHERE id = '$edit_id'";
    $edit_result = $db->query($sql2);
    $eState = mysqli_fetch_assoc($edit_result);
  }

  //DElete state
  if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $delete_id = sanitize($delete_id);
    $sql = "DELETE FROM state WHERE id = '$delete_id'";
    $db->query($sql);
    header('Location: state.php');
  }

  //if add state is submitted
  if (isset($_POST['add_submit'])) {
    $state = sanitize($_POST['state']);
    # check if state is blank
    if ($_POST['state']=='') {
      $errors[] .= ' Must enter a state';
    }
    //Check if state exist in db
    $sql = "SELECT * FROM state WHERE state = '$state'";
    if (isset($_GET['edit'])) {
      $sql = "SELECT * FROM state WHERE state = '$state' AND id != '$edit_id'";
    }
    $result = $db->query($sql);
    $count = mysqli_num_rows($result);
    if ($count > 0) {
      $errors[].=$state.' State already exist';
    }
    //dispaly errors
    if (!empty($errors)) {
      echo display_errors($errors);
    }
    else {
      //add state to db
      $sql = "INSERT INTO state (state) VALUES ('$state')";
      if (isset($_GET['edit'])) {
        $sql = "UPDATE state SET state = '$state' id = '$edit_id'";
      }
      $db->query($sql);
      header('Location: state.php');
    }
  }
?>
<body>

<h2 class="text-center">States</h2><hr>
<!--State Form-->
<div class="text-center">
  <form class="form-inline" action="state.php<?=((isset($_GET['edit']))?'?edit='.$edit_id:'');?>" method="post">
    <div class="form-group">
      <?php
      $state_value = '';
      if (isset($_GET['edit'])){
          $state_value = $eState['state'];
        } else{
          if(isset($_POST['state'])){
            $state_value = sanitize($_POST['state']);
          }
        }
      ?>
      <label for="state"> <?=((isset($_GET['edit']))?'Edit':'Add a'); ?> State:</label>
      <input type="text" name="state" id="state" class="form-control" value="<?=$state_value;?>">
        <?php if (isset($_GET['edit'])): ?>
          <a href="state.php" class="btn btn-default">Cancel</a>
        <?php endif; ?>
      <input type="submit" name="add_submit" value="<?=((isset($_GET['edit']))?'Edit':'Add');?> State" class="btn btn-success">
    </div>
  </form>
</div><hr>

<table class="table table-bordered table-striped table-aut table-condensed">
  <thead>
    <th></th><th>State</th><th></th>
  </thead>
  <tbody>
    <?php while($state = mysqli_fetch_assoc($results)): ?>
    <tr>
      <td><a href="state.php?edit=<?=$state['id'];?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil"></span></a></td>
      <td><?=$state['state'];?></td>
      <td><a href="state.php?delete=<?=$state['id'];?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a></td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
<?php include 'include/footer.php'; ?>
