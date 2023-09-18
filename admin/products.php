<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/proTradi/core/init.php';
  include 'include/head.php';
  include "include/navigation.php";
  if(!is_logged_in())
  {
    login_error_redirect();
  }

  //Delete product

  if (isset($_GET['delete']))
  {
    $id = sanitize($_GET['delete']);
    $db->query("UPDATE products SET deleted = '1', featured = '0' WHERE id = '$id'");
    header('Location: products.php');
  }

  $dbpath = '';
  if (isset($_GET['add']) || isset($_GET['edit'])) {
  $stateQuery = $db->query("SELECT * FROM state ORDER BY state");
  $parentQuery = $db->query("SELECT * FROM categories WHERE parent = 0 ORDER BY category");
  $title = ((isset($_POST['title']) && !empty($_POST['title']))?sanitize($_POST['title']):'');
  $state = ((isset($_POST['state']) && !empty($_POST['state']))?sanitize($_POST['state']):'');
  $parent = ((isset($_POST['parent']) && !empty($_POST['parent']))?sanitize($_POST['parent']):'');
  $category = ((isset($_POST['child']) && !empty($_POST['child']))?sanitize($_POST['child']):'');
  $price = ((isset($_POST['price']) && !empty($_POST['price']))?sanitize($_POST['price']):'');
  $list_price = ((isset($_POST['list_price']) && !empty($_POST['list_price']))?sanitize($_POST['list_price']):'');
  $description = ((isset($_POST['description']) && !empty($_POST['description']))?sanitize($_POST['description']):'');
  $sizes = ((isset($_POST['sizes']) && !empty($_POST['sizes']))?sanitize($_POST['sizes']):'');
  $sizes = rtrim($sizes,',');
  $saved_image = '';

    if (isset($_GET['edit'])) {
      $edit_id = (int)$_GET['edit'];
      $productresults = $db->query("SELECT * FROM products WHERE id ='$edit_id'");
      $product = mysqli_fetch_assoc($productresults);
      if (isset($_GET['delete_image'])) {
        $image_url = $_SERVER['DOCUMENT_ROOT'].$product['image'];echo $image_url;
        unlink($image_url);
        $db->query("UPDATE products SET image = '' WHERE id = '$edit_id'");
        header('Location: products.php?edit='.$edit_id);
      }
      $category = ((isset($_POST['child']) && $_POST['child'] != '')?sanitize($_POST['child']):$product['categories']);
      $title = ((isset($_POST['title']) && $_POST['title'] != '')?sanitize($_POST['title']):$product['title']);
      $state = ((isset($_POST['state']) && $_POST['state'] != '')?sanitize($_POST['state']):$product['state']);
      $parentQ = $db->query("SELECT * FROM categories WHERE id = '$category'");
      $parentResult = mysqli_fetch_assoc($parentQ);
      $parent = ((isset($_POST['parent']) && $_POST['parent'] != '')?sanitize($_POST['parent']):$parentResult['parent']);
      $price = ((isset($_POST['price']) && $_POST['price'] != '')?sanitize($_POST['price']):$product['price']);
      $list_price = ((isset($_POST['list_price']))?sanitize($_POST['list_price']):$product['list_price']);
      $description = ((isset($_POST['description']))?sanitize($_POST['description']):$product['description']);
      $sizes = ((isset($_POST['sizes']) && $_POST['sizes'] != '')?sanitize($_POST['sizes']):$product['sizex']);
      $sizes = rtrim($sizes,',');
      $saved_image = (($product['image'] != '')?$product['image']:'');
      $dbpath = $saved_image;
    }
    if(!empty($sizes)){
      $sizeString = sanitize($sizes);
      $sizeString = rtrim($sizeString,',');
      $sizesArray = explode(',',$sizeString);
      $sArray = array();
      $qArray = array();
      foreach ($sizesArray as $ss) {
        $s = explode(':',$ss);
        $sArray[] = $s[0];
        $qArray[] = $s[1];
      }
    }else { $sizesArray = array(); }

  if($_POST){
    $errors = array();
    $required = array('title', 'state', 'price', 'child', 'sizes');
    foreach ($required as $field) {
      if($_POST[$field] == ''){
        $errors[] = 'All fields with * are required to be filled';
        break;
      }
    }

    if($_FILES['photo']['name'] != '')
    {
      //var_dump($_FILES);
      $photo = $_FILES['photo'];        // Set $photo to the FILES name
      $name = $photo['name'];                 // Set $name to variable $photo['name']
      $nameArray = explode('.', $name);       // Set $nameArray $name, then explode by a (.) Ex Name (.) Something
      $fileName = $nameArray[0];              // Set file name to first Array.
      $fileExt = $nameArray[1];				// Set File Extension to second Array -- EX: (mens.png) (mens) [0] || (.png) [1]
      $mime = explode('/', $photo['type']);   // EX: image/png
      $mimeType = $mime[0];                   // Set $mimeType to $mime first Array.
      $mimeExt = $mime[1];				// Set Mime Extension to second Array
      $tmpLoc = $photo['tmp_name'];          // Set $tempLoc to $photo temporary name. Temporary Location
      $fileSize = $photo['size'];
      $allowed = array('png','jpg','jpeg','gif','');
      $uploadName = md5(microtime()).'.'.$fileExt;
      $uploadPath = BASEURL.'image/products/'.$uploadName;
      $dbpath = '/proTradi/image/products/'.$uploadName;
      if ($mimeType != 'image')
      {
        $errors[] ='The file must be an image.';
      }
      if (!in_array($fileExt, $allowed))
      {
        $errors[] = 'The photo must be in jpg, jpeg, png and gif format';
      }
      if ($fileSize > 15000000)
      {
        $errors[] = 'The file size must be under 15MB.';
      }
      if ($fileExt != $mimeExt && ($mimeExt == 'jpeg' && $fileExt != 'jpg'))
      {
        $errors[] = 'File extension does not match the file.';
      }
    }
    if(!empty($errors))
    {
      echo display_errors($errors);
    }
    else
    {//upload file and upload into database
      if(!empty($_FILES))
      {
        move_uploaded_file($tmpLoc,$uploadPath);
      }
      $insertSql = "INSERT INTO products (`title`,`price`,`list_price`,`state`,`categories`,`image`,`description`,`sizex`)
      VALUES ('$title','$price','$list_price','$state','$category','$dbpath','$description','$sizes')";
      if (isset($_GET['edit']))
      {
        $insertSql = "UPDATE products SET title = '$title', price = '$price', list_price = '$list_price',
        state = '$state', categories = '$category', sizex = '$sizes', image = '$dbpath', description = '$description'
        WHERE id = '$edit_id'";
      }
      $db->query($insertSql);
      header('Location: products.php');
    }
  }
?>
<h2 class="text-center"><?=((isset($_GET['edit']))?'Edit ':'Add a New ');?>Product</h2><hr>
  <form action="products.php?<?=((isset($_GET['edit']))?'edit='.$edit_id:'add=1');?>" method="POST" enctype="multipart/form-data">

    <div class="form-group col-md-3">
      <label for="title">Title*:</label>
      <input type="text" name="title" class="form-control" id="title" value="<?=$title;?>">
    </div>

    <div class="form-group col-md-3">
      <label for="state">State*:</label>
      <select class="form-control" id="state" name="state">
        <option value=""<?=(($state == '')?'selected':'');?>></option>
        <?php while($s = mysqli_fetch_assoc($stateQuery)): ?>
          <option value="<?=$s['id'];?>"<?=(($state == $s['id'])?' selected':'');?>><?=$s['state'];?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-group col-md-3">
      <label for="parent">Parent Category*:</label>
      <select class="form-control" name="parent" id="parent">
        <option value=""<?=(($parent == '')?' selected':'');?>></option>
        <?php while($p = mysqli_fetch_assoc($parentQuery)):?>
          <option value="<?=$p['id'];?>"<?=(($parent == $p['id'])?' selected':'');?>><?=$p['category'];?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-group col-md-3">
      <label for="child">Child Category*:</label>
      <select id="child" class="form-control" name="child">
      </select>
    </div>

    <div class="form-group col-md-3">
      <label for="price">Price*:</label>
      <input type="text" name="price" id="price" class="form-control" value="<?=$price;?>">
    </div>

    <div class="form-group col-md-3">
      <label for="list_price">List Price:</label>
      <input type="text" name="list_price" id="list_price" class="form-control" value="<?=$list_price;?>">
    </div>

    <div class="form-group col-md-3">
      <label>Quantity & Sizes*:</label>
      <button class="btn btn-default form-control" onclick="jQuery('#sizesModal').modal('toggle');return false;">Quantity & Size</button>
    </div>

    <div class="form-group col-md-3">
      <label for="sizes">Sizes & Qty Preview :</label>
      <input type="text" class="form-control" name="sizes" id="sizes" value="<?=$sizes;?>" readonly>
    </div>

    <div class="form-group col-md-6">
      <?php if ($saved_image != ''):?>
        <div class="saved-image"><img src="<?=$saved_image;?>" alt="saved image"></div><br>
        <a href="products.php?delete_image=1&edit=<?=$edit_id;?>" class="text-danger">Delete Image</a>
      <?php else: ?>
        <label for="photo">Product Photo:</label>
        <input type="file" class="form-control" name="photo" id="photo">
      <?php endif; ?>
    </div>

    <div class="form-group col-md-6">
      <label for="description">Description :</label>
      <textarea type="text" class="form-control" name="description" id="description" rows="6"><?=$description;?></textarea>
    </div>

    <div class="form-group pull-right">
      <a href="products.php" class="btn btn-default">Cancel</a>
      <input type="submit" value="<?=((isset($_GET['edit']))?'Edit ':'Add ');?>Product" class="btn btn-success pull-right">
    </div>

    <div class="clearfix"></div>
  </form>

<!-- Modal -->
<div class="modal fade" id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModal" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="sizesModal">Sizes & Quantity</h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <?php for($i=1;$i<=12;$i++): ?>
            <div class="form-group col-md-4">
              <label for="size<?=$i;?>">Size:</label>
              <input type="text" name="size<?=$i;?>" id="size<?=$i;?>" value="<?=((!empty($sArray[$i-1]))?$sArray[$i-1]:'');?>" class="form-control">
            </div>
            <div class="form-group col-md-2">
              <label for="qty<?=$i;?>">Quantity</label>
              <input type="number" name="qty<?=$i;?>" id="qty<?=$i;?>" value="<?=((!empty($qArray[$i-1]))?$qArray[$i-1]:'');?>" min="0" class="form-control">
            </div>
          <?php endfor; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="updateSizes();jQuery('#sizesModal').modal('toggle');return false;">Save changes</button>
      </div>
    </div>
  </div>
</div>

<?php }
  else
  {
    $sql = "SELECT * FROM products WHERE deleted=0";
    $presults = $db->query($sql);
    if (isset($_GET['featured']))
    {
      $id = (int)$_GET['id'];
      $featured = (int)$_GET['featured'];
      $featuredSql = "UPDATE products SET featured = '$featured' WHERE id = '$id'";
      $db->query($featuredSql);
      header('Location: products.php');
    }
?>

<h2 class="text-center">Products</h2>
<a href="products.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Add Product</a>
<div class="clearfix"></div>
<hr>
<table class="table table-bordered table-condensed table-striped">
  <thead><th></th><th>Product</th><th>Price</th><th>Sizes & Avail</th><th>Category</th><th>Featured</th><th>Sold</th></thead>
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
          <a href="products.php?edit=<?=$product['id'];?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
          <a href="products.php?delete=<?=$product['id'];?>" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-remove"></span></a>
        </td>
        <td><?=$product['title'];?></td>
        <td><?=money($product['price']);?></td>
        <td><?=$product['sizex'];?></td>
        <td><?=$category;?></td>
        <td><a href="products.php?featured=<?=(($product['featured'] == 0)?'1':'0');?>&id=<?=$product['id'];?>" class="btn btn-sm btn-default">
            <span class="glyphicon glyphicon-<?=(($product['featured'] == 1)?'minus':'plus')?>"></span>
            </a>  &nbsp <?=(($product['featured'] == 1)?'Featured Product':'');?></td>
        <td>0</td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php } include 'include/footer.php';?>

<script type="text/javascript">
  jQuery('document').ready(function()
  {
    get_child_options('<?=$category?>');
  });

</script>
