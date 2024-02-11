<!DOCTYPE html>
<html lang="en">
<head>
  <title>Payment</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-3">
  <h2>Payment Gateway</h2>
  <form action="stripe_payment.php" method="POST" name="cardpayment" id="payment-form">
  
   <div class="mb-3 mt-3">
      <label for="email">Name:</label>
      <input type="text" class="form-control" id="email" placeholder="Enter name" name="name">
    </div>
  
    <div class="mb-3 mt-3">
      <label for="email">Email:</label>
      <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
    </div>
   
    
	
	  <div class="mb-3 mt-3">
      <label for="email">Fees Amount:</label>
      <input type="text" class="form-control" id="email" placeholder="Enter course" name="amount">
    </div>
	
	 <div class="row">

     <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="cardNumber">CARD NUMBER</label><br>
                                            <img src="images/paypal.jpg" alt="PayPal" width="180"><br>
                                            <div class="input-group">


                                    

                                                <input type="text" class="form-control" name="card_number" placeholder="Valid Card Number" autocomplete="cc-number" id="card_number" maxlength="16" data-stripe="number" required />
                                                <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
                                            </div>
                                        </div>                            
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-xs-4 col-md-4">
                                        <div class="form-group">
                                            <label for="cardExpiry"><span class="visible-xs-inline">MON</span></label>
                                            <select name="card_exp_month" id="card_exp_month" class="form-control" data-stripe="exp_month" required>
                                                <option>MON</option>
                                                <option value="01">01 ( JAN )</option>
                                                <option value="02">02 ( FEB )</option>
                                                <option value="03">03 ( MAR )</option>
                                                <option value="04">04 ( APR )</option>
                                                <option value="05">05 ( MAY )</option>
                                                <option value="06">06 ( JUN )</option>
                                                <option value="07">07 ( JUL )</option>
                                                <option value="08">08 ( AUG )</option>
                                                <option value="09">09 ( SEP )</option>
                                                <option value="10">10 ( OCT )</option>
                                                <option value="11">11 ( NOV )</option>
                                                <option value="12">12 ( DEC )</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xs-4 col-md-4">
                                        <div class="form-group">
                                            <label for="cardExpiry"><span class="visible-xs-inline">YEAR</span></label>
                                            <select name="card_exp_year" id="card_exp_year" class="form-control" data-stripe="exp_year">
                                                <option>Year</option>
                                                <option value="20">2020</option>
                                                <option value="21">2021</option>
                                                <option value="22">2022</option>
                                                <option value="23">2023</option>
                                                <option value="24">2024</option>
                                                <option value="25">2025</option>
                                                <option value="26">2026</option>
                                                <option value="27">2027</option>
                                                <option value="28">2028</option>
                                                <option value="29">2029</option>
                                                <option value="30">2030</option>
                                                <option value="31">2031</option>
                                                <option value="32">2032</option>
                                                <option value="33">2033</option>
                                                <option value="34">2034</option>
                                                <option value="35">2035</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-md-4 pull-right mb-4">
                                        <div class="form-group">
                                            <label for="cardCVC">CV CODE</label>
                                            <input type="password" class="form-control" name="card_cvc" placeholder="CVC" autocomplete="cc-csc" id="card_cvc" required />
                                        </div>
                                    </div>
                                </div>
   
   
    <button type="submit" id="payBtn" class="btn btn-primary">Submit</button>
  </form>
</div>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
 <script src="https://js.stripe.com/v2/"></script>

 <script>
        // Set your publishable key
        Stripe.setPublishableKey('pk_test_51NzCPbCD1vWrjtcwOZz1Q9Xk2nvqraIsUlx5zek1pJ3df73yF4EOSnwL9T2aJNVAtP0FgdYxMJ7roNYqcoxgVHHE00ACmJmIea');

        // Callback to handle the response from stripe
        function stripeResponseHandler(status, response) {
            if (response.error) {
                // Enable the submit button
                $('#payBtn').removeAttr("disabled");
                // Display the errors on the form
                $(".payment-status").html('<p>'+response.error.message+'</p>');
            } else {
                var form$ = $("#payment-form");
                // Get token id
                var token = response.id;
                // Insert the token into the form
                form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
                // Submit form to the server
                form$.get(0).submit();
            }
        }

        $(document).ready(function() {
            // On form submit
            $("#payment-form").submit(function() {
                // Disable the submit button to prevent repeated clicks
                $('#payBtn').attr("disabled", "disabled");
                
                // Create single-use token to charge the user
                Stripe.createToken({
                    number: $('#card_number').val(),
                    exp_month: $('#card_exp_month').val(),
                    exp_year: $('#card_exp_year').val(),
                    cvc: $('#card_cvc').val()
                }, stripeResponseHandler);
                
                // Submit from callback
                return false;
            });
        });
</script>

</body>
</html>
