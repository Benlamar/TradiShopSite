<?php
  require_once 'core/init.php';
  $full_name = sanitize($_POST['full_name']);
  $email = sanitize($_POST['email']);
  $street = sanitize($_POST['street']);
  $street2 = sanitize($_POST['street2']);
  $city = sanitize($_POST['city']);
  $state = sanitize($_POST['state']);
  $postal_code = sanitize($_POST['postal_code']);
  $country = sanitize($_POST['country']);
  $tax = sanitize($_POST['tax']);
  $sub_total = sanitize($_POST['sub_total']);
  $grand_total = sanitize($_POST['grand_total']);
  $cart_id = sanitize($_POST['cart_id']);
  $description = sanitize($_POST['description']);
//  $charge_amount = number_format((float)$grand_total, 2) * 100;
  $metadata = array(
    "cart_id" => $cart_id,
    "tax" => $tax,
    "sub_total" => $sub_total
  );

  //adjust inventory
  $itemQ = $db->query("SELECT	* FROM cart WHERE id = '{$cart_id}'");
  $iresults = mysqli_fetch_assoc($itemQ);
  $items = json_decode($iresults['items'],true);
  foreach($items as $item){
    $newSizes = array();
    $item_id = $item['id'];
    $productQ = $db->query("SELECT sizex FROM products WHERE id = '{$item_id}'");
    $products = mysqli_fetch_assoc($productQ);
    $sizes = sizesToArray($products['sizex']);
    foreach($sizes as $size){
      if($size['size'] == $item['size']){
        $q = $size['quantity'] - $item['quantity'];
        $newSizes[] = array('size' => $size['size'],'quantity' => $q);
      }else{
          $newSizes[] = array('size' => $size['size'],'quantity' => $size['quantity']);
      }
    }
    $sizeString = sizesToString($newSizes);
    $db->query("UPDATE products SET sizex = '{$sizeString}' WHERE id = '{$item_id}'");
  }
  //$charge=array( "amount"=>$charge_amount,
    //"description"=>$description,
  //  "receipt_email"=>$email,
  // "metadata"=>$metadata
//  );
//'$charge->id',charge_id,

    $db->query("UPDATE cart SET paid=1 WHERE id='{$cart_id}'");
    $db->query("INSERT INTO transactions(cart_id,full_name,email,street,street2,city,state,postal_code,country,sub_total,tax,grand_total,description)
    VALUES('$cart_id','$full_name','$email','$street','$street2','$city','$state','$postal_code','$country','$sub_total','$tax','$grand_total',
    '$description')");
    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? '.'.$_SERVER['HTTP_HOST'] : false;
    setcookie(CART_COOKIE,'',1,'/',$domain,false);
    include 'include/head.php';
    include 'include/navigation.php';
    include 'include/headerpartial.php';
?>
  <h1 class="text-center text-success">Thank you</h1>
  <p>Your card has been successfully charged <?=money($grand_total);?>. You have been emailed a receipt. Please check your span folder if it is not in your inbox.
    Additionally you can print this page as a receipt.</p>
  <p>Your order will be shipped to the address below.</p>
  <address>
    <?=$full_name;?><br>
    <?=$street;?><br>
    <?=(($street2 != '')? $street2.'<br>': '');?>
    <?=$city. ', '.$state.' '.$postal_code;?><br>
    <?=$country;?><br>
  </address>

  <?php include "include/footer.php";?>
