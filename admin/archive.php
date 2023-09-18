<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/proTradi/core/init.php';
  include 'include/head.php';
  include "include/navigation.php";
  if(!is_logged_in())
  {
    login_error_redirect();
  }
  //Archive product
  if (isset($_GET['refresh']))
  {
    $id = sanitize($_GET['refresh']);
    $db->query("UPDATE products SET deleted = '0', featured = '0' WHERE id = '$id'");
  }

  $sql = "SELECT * FROM products WHERE deleted=1";
  $presults = $db->query($sql);

?>


  <h2 class="text-center">Archive Products</h2>
  <div class="clearfix"></div>
  <hr>
  <table class="table table-bordered table-condensed table-striped">
    <thead><th></th><th>Product</th><th>Price</th><th>Category</th></thead>
    <tbody>
      <?php  while($product = mysqli_fetch_assoc($presults)):
          $childID = $product['categories'];
          $catSql = "SELECT * FROM categories WHERE id = '$childID'";
          $result = $db->query($catSql);
          $child = mysqli_fetch_assoc($result);
          $parentID = $child['parent'];
          $pSql = "SELECT * FROM categories WHERE id = '$parentID'";
          $presult = $db->query($pSql);
          $parent = mysqli_fetch_assoc($presult);
          $category = $parent['category'].'-'.$child['category'];
      ?>
        <tr>
          <td>
            <a href="archive.php?refresh=<?=$product['id'];?>" class="btn btn-md btn-default"><span class="glyphicon glyphicon-refresh"></span></a>
          </td>
          <td><?=$product['title'];?></td>
          <td><?=money($product['price']);?></td>
          <td><?=$category;?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

<?php include 'include/footer.php';?>
