<?php 

    include "connection.php";

	$payment_id = $statusMsg = ''; 
	$ordStatus = 'error';
	$id = '';

	// Check whether stripe token is not empty

	if(!empty($_POST['stripeToken'])){

		// Get Token, Card and User Info from Form
		$token = $_POST['stripeToken'];
		$name = $_POST['name'];
		$email = $_POST['email'];
		$card_no = $_POST['card_number'];
		$card_cvc = $_POST['card_cvc'];
		$card_exp_month = $_POST['card_exp_month'];
		$card_exp_year = $_POST['card_exp_year'];
		$price=$_POST['amount'];

		

		// Include STRIPE PHP Library
		require_once('stripe-php/init.php');

		// set API Key
		$stripe = array(
		"SecretKey"=>"sk_test_51NzCPbCD1vWrjtcwBsncQlNRhTGtChX1WQ1vtsvHAXujt21YDQMQKcsVEsJLchDz37EZtEr0CYnXb7rUCSGD2MpI004k6nWWrc",
		"PublishableKey"=>"pk_test_51NzCPbCD1vWrjtcwOZz1Q9Xk2nvqraIsUlx5zek1pJ3df73yF4EOSnwL9T2aJNVAtP0FgdYxMJ7roNYqcoxgVHHE00ACmJmIea"
		);

		// Set your secret key: remember to change this to your live secret key in production
		// See your keys here: https://dashboard.stripe.com/account/apikeys
		\Stripe\Stripe::setApiKey($stripe['SecretKey']);

		// Add customer to stripe 
	    $customer = \Stripe\Customer::create(array( 
	        'email' => $email, 
	        'source'  => $token,
	        'name' => $name,
	        
	    ));

	    // Generate Unique order ID 
	    $orderID = strtoupper(str_replace('.','',uniqid('', true)));
	     
	    // Convert price to cents 
	    $itemPrice = ($price*100);
	    $currency = "usd";
	   

	    // Charge a credit or a debit card 
	    $charge = \Stripe\Charge::create(array( 
	        'customer' => $customer->id, 
	        'amount'   => $itemPrice, 
	        'currency' => $currency, 
	         
	        'metadata' => array( 
	            'order_id' => $orderID 
	        ) 
	    ));

	    // Retrieve charge details 
    	$chargeJson = $charge->jsonSerialize();

    	// Check whether the charge is successful 
    	if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1){ 

	        // Order details 
	        $transactionID = $chargeJson['id']; 
	        $paidAmount = $chargeJson['amount']; 
	        $paidCurrency = $chargeJson['currency']; 
	        $payment_status = $chargeJson['status'];
	        $payment_date = date("Y-m-d H:i:s");
	        $dt_tm = date('Y-m-d H:i:s');

	        // Insert tansaction data into the database

	        $sql = "insert into registration (name,email,fees,card_number,card_expirymonth,card_expiryyear,status,paymentid,added_date) values ('".$name."','".$email."','".$price."','".$card_no."','".$card_exp_month."','".$card_exp_year."','".$payment_status."','".$transactionID."','".$dt_tm."')";
			
			
	        mysqli_query($con,$sql) or die("Mysql Error Stripe-Charge(SQL)".mysqli_error($con));

    		

	        // If the order is successful 
	        if($payment_status == 'succeeded'){ 
	            $ordStatus = 'success'; 
	            $statusMsg = 'Your Payment has been Successful!'; 
	    	} else{ 
	            $statusMsg = "Your Payment has Failed!"; 
	        } 
	    } else{ 
	        //print '<pre>';print_r($chargeJson); 
	        $statusMsg = "Transaction has been failed!"; 
	    } 
	} else{ 
	    $statusMsg = "Error on form submission."; 
	} 
	
?>

<!DOCTYPE html>
<html>
	<head>
        <title> Stripe Payment Gateway  </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="refresh" content="6; url=index.php" />
		<link rel="stylesheet" type="text/css" href="css/stripe.css">
    </head>

    <div class="container">
        <h2 style="text-align: center; color: blue;">Stripe Payment Gateway  </h2>
        <br>
        <div class="row">
	        <div class="col-lg-12">
				<div class="status">
					<h1 class="<?php echo $ordStatus; ?>"><?php echo $statusMsg; ?></h1>
				
					<h4 class="heading">Payment Information - </h4>
					<br>
					
					<p><b>Transaction ID:</b> <?php echo $transactionID; ?></p>
					<p><b>Paid Amount:</b> <?php echo $paidAmount.' '.$paidCurrency; ?> ($<?php echo $price;?>.00)</p>
					<p><b>Payment Status:</b> <?php echo $payment_status; ?></p>
					<h4 class="heading">Reward  Quantity - </h4>
					<p><b>Reward Given:</b> <?php echo $price.' '.$currency; ?> ($<?php echo $price;?>.00)</p>
				</div>
				<br> <br>
				<h1 class="heading"> This page will be redirected to Homepage  in 5s </h1>
			</div>
		</div>
	</div>
</html>