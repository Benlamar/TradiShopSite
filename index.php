<?php
			require_once 'core/init.php';
			include "include/head.php";
		 	include "include/navigation.php";
			include "include/headerfull.php";
			include "include/leftbar.php";

			$sql = "SELECT * FROM products WHERE featured = 1";
			$featured = $db->query($sql);

?>
		<!--main Side bar-->
		<div class="col-md-8">
			<div class="row">
				<h2 class="text-center">Featured Product</h2>
				<?php  while($product = mysqli_fetch_assoc($featured)) : ?>
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
