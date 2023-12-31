<?php
			require_once 'core/init.php';
			include "include/head.php";
		 	include "include/navigation.php";
			include "include/headerpartial.php";
			include "include/leftbar.php";


      $sql = "SELECT * FROM products";
      $cat_id = (($_POST['cat'] != '')?sanitize($_POST['cat']):'');
      if ($cat_id == '') {
        $sql .= " WHERE deleted = 0";
      }else {
        $sql .= " WHERE categories = '{$cart_id}' AND deleted = 0";
      }
      $price_sort = (($_POST['price_sort'] != '')?sanitize($_POST['price_sort']):'');
      $min_price = (($_POST['min_price'] != '')?sanitize($_POST['min_price']):'');
      $max_price = (($_POST['max_price'] != '')?sanitize($_POST['max_price']):'');
      $state = (($_POST['state'] != '')?sanitize($_POST['state']):'');
      if ($min_price != '') {
        $sql .= " AND price >= '{$min_price}'";
      }

      if ($max_price != '') {
        $sql .= " AND price <= '{$max_price}'";
      }
      if ($state != '') {
        $sql .= " AND state = '{$state}'";
      }
			if($price_sort == 'low'){
        $sql .= " ORDER BY price";
      }
      if($price_sort == 'high'){
        $sql .= " ORDER BY price DESC";
      }
			$productQ = $db->query($sql);
      $category = get_category($cat_id);

?>
		<!--main Side bar-->
		<div class="col-md-8">
			<div class="row">
        <?php if($cat_id != ''):?>
				  <h2 class="text-center"><?=$category['parent'].' '.$category['child'];?></h2>
        <?php else: ?>
          <h2 class="text-center">Tradi Shop</h2>
        <?php endif; ?>
				<?php  while($product = mysqli_fetch_assoc($productQ)): ?>
					<!--< php var_dump($product);?>-->
					<div class="col-sm-3 text-center">
						<h4><?= $product['title']; ?></h4>
						<img src="<?= $product['image']; ?>" alt="<?= $product['title']; ?>" class="img-thumb"/>
						<p class"list-price text-danger">List Price: <s>₹ <?= $product['list_price']; ?></s></p>
						<p class="price">Our Price: ₹ <?= $product['price']; ?></p>
						<button type="button" class="btn btn-sm btn-success" onclick="detailsmodal(<?= $product['id']; ?>)"> Details</button>
					</div>
				<?php endwhile; ?>
			</div>
		</div>

		<?php include "include/rightbar.php";

					include "include/footer.php";
		?>
