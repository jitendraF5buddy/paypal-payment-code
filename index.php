 <?php 

 if(isset($_POST['pay_num']) && $_POST['pay_num']=="Pay Now")
 {

 		$cardtype = $_POST['cardtype'];
 		$cvv = $_POST['cvv'];
		$total = $_POST['amount'];
		$card_number = $_POST['card-number'];
		$m_year = $_POST['expiry-month'].$_POST['expiry-year'];
		$user_email = "jitupatel7687@gmail.com";


        $api_version = '85.0';		  
		$api_endpoint ='https://api-3t.sandbox.paypal.com/nvp';
		$api_username='sellerid.com';
		$api_password='RB456456C24NN7';// seller password
		$api_signature='AFcWxV21C7fd0v3bYYYRCpSSfsdfssdfsdAVqx94lvzEJJYGUn--jNOsScapd8'; //this is seller key
	  	$request_params = array
					(
					'METHOD' => 'DoDirectPayment', 
					'USER' => $api_username, 
					'PWD' => $api_password, 
					'SIGNATURE' => $api_signature, 
					'VERSION' => $api_version, 
					'PAYMENTACTION' => 'Sale', 					
					'IPADDRESS' => $_SERVER['REMOTE_ADDR'],
					'CREDITCARDTYPE' => $cardtype, 
					'ACCT' => $card_number, 						
					'EXPDATE' => $m_year, 			
					'CVV2' => $cvv,
					'AMT' => $total, 
					'CURRENCYCODE' => 'USD', 
					'DESC' => 'Garment Rental',
					'EMAIL' => $user_email
					);
			
			// Loop through $request_params array to generate the NVP string.
			$nvp_string = '';
			
			foreach($request_params as $var=>$val){
				$nvp_string .= '&'.$var.'='.urlencode($val);
			}			
			// Send NVP string to PayPal and store response
			$curl = curl_init();
					curl_setopt($curl, CURLOPT_VERBOSE, 1);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

					curl_setopt($curl, CURLOPT_TIMEOUT, 30);
					curl_setopt($curl, CURLOPT_URL, $api_endpoint);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $nvp_string);
			
					$result = curl_exec($curl);
					curl_close($curl);
			
			// Parse the API response
			// Function to convert NTP string to an array
				$proArray = array();
				while(strlen($result)){
					$keypos= strpos($result,'=');
					$keyval = substr($result,0,$keypos);
					$valuepos = strpos($result,'&') ? strpos($result,'&'): strlen($result);
					$valval = substr($result,$keypos+1,$valuepos-$keypos-1);
					// decoding the respose
					$proArray[$keyval] = urldecode($valval);
					$result = substr($result,$valuepos+1,strlen($result));
				}
				print_r($proArray);

				$message = "";
				if(isset($proArray['ACK']) && $proArray['ACK']=="Failure")
				{
					$message = "Payment Failure"."<br>".$proArray['L_LONGMESSAGE0'];
				}

				if(isset($proArray['ACK']) && $proArray['ACK']=="Success")
				{
					$message = "Your Payment success full"."<br> Your Transaction ID is ".$proArray['TRANSACTIONID'];
				}   
		
}

?>	

<html>
<head>
	<link href="css/bootstrap.css" rel="stylesheet">
</head>

<body>

<?php if(isset($message) && $message!=""){
	echo $message;
} ?>

<div class="container">
  <form action="" method="post">
            <h3>Pay Direct</h3><br>
                <div class="form-group">
                      <label class="col-sm-3 control-label" for="card">Card</label>
                      <div class="col-sm-6">    
                        <select name="cardtype" id="cardtype" class="form-control" required="">
                          <option value="">Select Card</option>
                          <option value="Amex">American Express</option>
                          <option value="Discover">Discover</option>
                          <option value="MasterCard">Master Card</option>
                          <option value="Visa">Visa</option>
                        </select>
                      </div>  
                      <div class="clearfix"></div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label" for="card-holder-name">Name on Card</label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" name="card-holder-name" id="card-holder-name" placeholder="Card Holder's Name" value="" required="">
                  </div>
                  <div class="clearfix"></div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label" for="card-number">Card Number</label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" name="card-number" id="card-number" placeholder="Debit/Credit Card Number" onkeypress="return isNumberKey(event)" maxlength="16" required="">
                  </div>
                  <div class="clearfix"></div>
                </div>


                <div class="form-group">
                      <label class="col-sm-3 control-label" for="expiry-month">Expiration Date</label>
                      <div class="col-sm-9">
                        <div class="row">
                          <div class="col-xs-3">
                            <select class="form-control col-sm-2" name="expiry-month" id="expiry-month" required="">
                              <option value="">Month</option>
                              <option value="01">Jan (01)</option>
                              <option value="02">Feb (02)</option>
                              <option value="03">Mar (03)</option>
                              <option value="04">Apr (04)</option>
                              <option value="05">May (05)</option>
                              <option value="06">June (06)</option>
                              <option value="07">July (07)</option>
                              <option value="08">Aug (08)</option>
                              <option value="09">Sep (09)</option>
                              <option value="10">Oct (10)</option>
                              <option value="11">Nov (11)</option>
                              <option value="12">Dec (12)</option>
                            </select>
                          </div>
                          <div class="col-xs-3">
                            <select class="form-control" name="expiry-year" id="expiry-year" required="">
                              <option value="">Year</option>
                              <?php for($i = date('Y');$i<2023;$i++){ ?>   
                              <option value="<?php echo $i;?>"><?php echo $i;?></option>
                              <?php }?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="cvv" required="">Card CVV</label>
                        <div class="col-sm-3">
                          <input type="text" class="form-control" name="cvv" id="cvv" placeholder="Security Code" onkeypress="return isNumberKey(event)" maxlength="3">
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="cvv">Total Amount</label>
                        <div class="col-sm-3">
                        <input type="text" class="form-control" name="amount" id="cvv" placeholder="Amount onkeypress">
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-9 text-right">
                              <input type="submit" name="pay_num" id="pay_num" value="Pay Now" class="btn" onclick="return paynow()">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    </form>	

</div>
</body>
</html>                    