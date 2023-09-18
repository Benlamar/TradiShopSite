<?php
			require_once 'core/init.php';
			include "include/head.php";
		 	include "include/navigation.php";
			include "include/headerpartial.php";
			include "include/leftbar.php";


    if(isset($_GET['cat'])){
      $cat_id = sanitize($_GET['cat']);
    }else{
      $cat_id = '';
    }
			$sql = "SELECT * FROM products WHERE categories = '$cat_id'";
			$productQ = $db->query($sql);
      $category = get_category($cat_id);
?>
		<!--main Side bar-->
		<div class="col-md-8">
			<div class="row">
				<h2 class="text-center"><?=$category['parent'].' '.$category['child'];?></h2>
				<?php  while($product = mysqli_fetch_assoc($productQ)) : ?>
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
