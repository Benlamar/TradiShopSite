<?php
  $cat_id= ((isset($_REQUEST['cat']))?sanitize($_REQUEST['cat']):'');
  $price_sort = ((isset($_REQUEST['price_sort']))?sanitize($_REQUEST['price_sort']):'');
  $min_price = ((isset($_REQUEST['min_price']))?sanitize($_REQUEST['min_price']):'');
  $max_price = ((isset($_REQUEST['max_price']))?sanitize($_REQUEST['max_price']):'');
  $b = ((isset($_REQUEST['state']))?sanitize($_REQUEST['state']):'');
  $stateQ = $db->query("SELECT * FROM state ORDER BY state");
?>
<h3 class="text-center">Search By:</h3>
<h4 class="text-center">Price</h4>
<form action="search.php" method="post">
    <input type="hidden" name="cat" value="<?=$cart_id;?>">
    <input type="hidden" name="price_sort" value="0">
    <input type="radio" name="price_sort" value="low"<?=(($price_sort == 'low')?'checked':'');?>>Low to High<br>
    <input type="radio" name="price_sort" value="high"<?=(($price_sort == 'high')?'checked':'');?>>High to Low<br>
    <input type="type" name="min_price" class="price-range" placeholder="Min ₹" value="<?=$min_price;?>">To
    <input type="type" name="max_price" class="price-range" placeholder="Max ₹" value="<?=$max_price;?>"><br><br>
    <h4 class="text-center">State</h4>
    <input type="radio" name="state" value=""<?=(($b == '')?' checked':'');?>>All<br>
    <?php while($state = mysqli_fetch_assoc($stateQ)):?>
    <input type="radio" name="state" value="<?=$state['id'];?>"<?=(($b == $state['id'])?' checked':'');?>><?=$state['state'];?><br>
    <?php endwhile;?>
    <input type="submit" value="Search" class="btn btn-sm btn-primary">
</form>
