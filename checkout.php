<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
}

if (isset($_POST['order_btn'])) {

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $number = $_POST['number'];
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $method = mysqli_real_escape_string($conn, $_POST['method']);
   $address = mysqli_real_escape_string($conn, $_POST['flat'] . ', ' . $_POST['city']);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products[] = '';

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
   if (mysqli_num_rows($cart_query) > 0) {
      while ($cart_item = mysqli_fetch_assoc($cart_query)) {
         $cart_products[] = $cart_item['name'] . ' (' . $cart_item['quantity'] . ') ';
         $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total += $sub_total;
      }
   }

   $total_products = implode(', ', $cart_products);

   $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'");

   if ($cart_total == 0) {
      $message[] = 'Your Cart Is Empty';
   } else {
      if (mysqli_num_rows($order_query) > 0) {
         $message[] = 'order already placed!';
      } else {
         mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')");
         $message[] = 'Order Placed Successfully!';
         mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'");
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="assets/css/bootstrap.min.css">
   <link rel="stylesheet" href="assets/css/temoo.css">
   <link rel="stylesheet" href="assets/css/custom.css">

   <style>
      <?php include 'assets/css-admin/admin_style.css'; ?>
   </style>
   <style>
      <?php include 'assets/css-admin/style.css'; ?>
   </style>
   <script src="jquery-3.4.1.min.js"></script>


</head>

<body>

   <?php include 'header.php'; ?>


   <section class="display-order">

      <?php
      $grand_total = 0;
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
      if (mysqli_num_rows($select_cart) > 0) {
         while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
            ?>
            <p> <?php echo $fetch_cart['name']; ?>
               <span>(<?php echo  $fetch_cart['price'] . 'JD'. ' x ' . $fetch_cart['quantity']; ?>)</span> </p>
            <?php
         }
      } else {
         echo '<p class="empty">your cart is empty</p>';
      }
      ?>
      <div class="grand-total"> Grand Total : <span><?php echo $grand_total; ?>JD</span> </div>

   </section>

   <section class="checkout">

      <form action="" method="post">
         <h3>Place Your Order</h3>
         <div class="flex">
            <div class="inputBox">
               <input type="text" name="name" required placeholder="Enter Your Name">
            </div>
            <div class="inputBox">
               <input type="number" name="number" required placeholder="Enter Your Number">
            </div>
            <div class="inputBox">
               <input type="email" name="email" required placeholder="Enter Your Email">
            </div>
            <div class="inputBox">
               <select name="method">
                  <option value="cash on delivery">Cash On Delivery</option>
                  <option value="credit card">Credit Card</option>
                  <option value="paypal">Paypal</option>
                  <option value="paytm">Paytm</option>
               </select>
            </div>
            <div class="inputBox">
               <input type="text" min="0" name="flat" required placeholder=" Address Line .....">
            </div>

            <div class="inputBox">
               <input type="text" name="city" required placeholder="City ...">
            </div>



         </div>
         <input type="submit" value="order now" class="btn" name="order_btn">
      </form>

   </section>









   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>