<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

////MITKO
/// TEST CONTROLLER FOR CREATION OF PDF...

class pptest extends Controller {

	function pptest()
	{		
		parent::Controller();	
					
	}
	function _GET2Array($data = '')
	{
		$ddt = explode ('?', $data);
		printcool ($ddt);
		if (!isset($ddt[1])) {
			$ddt = explode ('/', $data);
			$ddt[1] = $ddt[2];
		}
		$ddtt = explode('&', $ddt[1]);
		printcool ($ddt);
		foreach ($ddtt as $k => $v)
		{
			$ddttt = explode('=', $v);
			foreach ($ddttt as $kk => $vv)  if ($kk == 1 && ($ddttt[($kk-1)] == 'token' || $ddttt[($kk-1)] == 'PayerID')) $r[$ddttt[($kk-1)]] = $vv;
		}
		return $r;		
	}
	function testurl($string = '')
	{
		printcool ((string)$_SERVER['REQUEST_URI']);
		$urlparsed = $this->_GET2Array((string)$_SERVER['REQUEST_URI']);	
		printcool ($urlparsed);
	}
	function testphp($token = '', $payerid = '')
	{
		printcool ($token);
		printcool ($payerid);
	}
	
	function testpp($string = '')
	{
		//if (session_status() == PHP_SESSION_NONE) { session_start(); } //PHP >= 5.4.0
		//if(session_id() == '') { session_start(); } //uncomment this line if PHP < 5.4.0 and comment out line above
		
		$PayPalMode 			= 'sandbox'; // sandbox or live
		$PayPalApiUsername 		= 'somepaypal_api.yahoo.co.uk'; //PayPal API Username
		$PayPalApiPassword 		= '123456789'; //Paypal API password
		$PayPalApiSignature 	= 'opupouopupo987kkkhkixlksjewNyJ2pEq.Gufar'; //Paypal API Signature
		$PayPalCurrencyCode 	= 'USD'; //Paypal Currency Code
		$PayPalReturnURL 		= $this->config->config['base_url'].'/paypalprocess.php'; //Point to process.php page
		$PayPalCancelURL 		= $this->config->config['base_url'].'/paypalcancel.php'; //Cancel URL if user clicks cancel
		$logoimg 				= $this->config->config['base_url'].'/images/pplogo.jpg';
		$paypalmode = ($PayPalMode=='sandbox') ? '.sandbox' : '';
			
			if($_POST) //Post Data received from product list page.
			{
				//Mainly we need 4 variables from product page Item Name, Item Price, Item Number and Item Quantity.
				
				//Please Note : People can manipulate hidden field amounts in form,
				//In practical world you must fetch actual price from database using item id. Eg: 
				//$ItemPrice = $mysqli->query("SELECT item_price FROM products WHERE id = Product_Number");
			
				$ItemName 		= $_POST["itemname"]; //Item Name
				$ItemPrice 		= $_POST["itemprice"]; //Item Price
				$ItemNumber 	= $_POST["itemnumber"]; //Item Number
				$ItemDesc 		= $_POST["itemdesc"]; //Item Number
				$ItemQty 		= $_POST["itemQty"]; // Item Quantity
				$ItemTotalPrice = ($ItemPrice*$ItemQty); //(Item Price x Quantity = Total) Get total amount of product; 
				
				//Other important variables like tax, shipping cost
				//$TotalTaxAmount 	= 2.58;  //Sum of tax for all items in this order. 
				//$HandalingCost 		= 2.00;  //Handling cost for this order.
				//$InsuranceCost 		= 1.00;  //shipping insurance cost for this order.
				//$ShippinDiscount 	= -3.00; //Shipping discount for this order. Specify this as negative number.
				//$ShippinCost 		= 3.00; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate.
				
				//Grand total including all tax, insurance, shipping cost and discount
				$GrandTotal = $ItemTotalPrice;//($ItemTotalPrice + $TotalTaxAmount + $HandalingCost + $InsuranceCost + $ShippinCost + $ShippinDiscount);
				
				//Parameters for SetExpressCheckout, which will be sent to PayPal
				$padata = 	'&METHOD=SetExpressCheckout'.
							'&RETURNURL='.urlencode($PayPalReturnURL ).
							'&CANCELURL='.urlencode($PayPalCancelURL).
							'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE").
							
							'&L_PAYMENTREQUEST_0_NAME0='.urlencode($ItemName).
							'&L_PAYMENTREQUEST_0_NUMBER0='.urlencode($ItemNumber).
							'&L_PAYMENTREQUEST_0_DESC0='.urlencode($ItemDesc).
							'&L_PAYMENTREQUEST_0_AMT0='.urlencode($ItemPrice).
							'&L_PAYMENTREQUEST_0_QTY0='. urlencode($ItemQty).
							
							
							
							'&NOSHIPPING=0'. //set 1 to hide buyer's shipping address, in-case products that does not require shipping
							
							'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($ItemTotalPrice).
							//'&PAYMENTREQUEST_0_TAXAMT='.urlencode($TotalTaxAmount).
							//'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($ShippinCost).
							//'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($HandalingCost).
							//'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($ShippinDiscount).
							//'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($InsuranceCost).
							'&PAYMENTREQUEST_0_AMT='.urlencode($GrandTotal).
							'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode).
							'&LOCALECODE=GB'. //PayPal pages to match the language on your website.
							'&LOGOIMG='.$logoimg. //site logo
							'&CARTBORDERCOLOR=FFFFFF'. //border color of cart
							'&ALLOWNOTE=1';
							
							############# set session variable we need later for "DoExpressCheckoutPayment" #######
							$this->session->set_userdata('ItemName', $ItemName);//Item Name
							$this->session->set_userdata('ItemPrice', $ItemPrice); //Item Price
							$this->session->set_userdata('ItemNumber', $ItemNumber); //Item Number
							$this->session->set_userdata('ItemDesc', $ItemDesc); //Item Number
							$this->session->set_userdata('ItemQty',  $ItemQty); // Item Quantity
							$this->session->set_userdata('ItemTotalPrice', $ItemTotalPrice); //(Item Price x Quantity = Total) Get total amount of product; 
							
							/*$_SESSION['TotalTaxAmount'] 	=  $TotalTaxAmount;  //Sum of tax for all items in this order. 
							$_SESSION['HandalingCost'] 		=  $HandalingCost;  //Handling cost for this order.
							$_SESSION['InsuranceCost'] 		=  $InsuranceCost;  //shipping insurance cost for this order.
							$_SESSION['ShippinDiscount'] 	=  $ShippinDiscount; //Shipping discount for this order. Specify this as negative number.
							$_SESSION['ShippinCost'] 		=   $ShippinCost; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate.*/

							$this->session->set_userdata('GrandTotal', $GrandTotal);
			
					$this->session->set_userdata($ud);
					//We need to execute the "SetExpressCheckOut" method to obtain paypal token
					//$paypal= new MyPayPal();
					$httpParsedResponseAr = $this->_PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
					
					//Respond according to message we receive from Paypal
					if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
					{
			
							//Redirect user to PayPal store with Token received.
							$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
							header('Location: '.$paypalurl);
						 
					}else{
						//Show error message
						echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
						echo '<pre>';
						print_r($httpParsedResponseAr);
						echo '</pre>';
					}
			
			}
			
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			
			
			//Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
			
			$urlparsed = $this->_GET2Array((string)$_SERVER['REQUEST_URI']);
			
			if(isset($urlparsed["token"]) && isset($urlparsed["PayerID"]))
			{
				//we will be using these two variables to execute the "DoExpressCheckoutPayment"
				//Note: we haven't received any payment yet.
				
				$token = $urlparsed["token"];
				$payer_id = $urlparsed["PayerID"];
				
				//get session variables
				$ItemName 			= $this->session->userdata('ItemName'); //Item Name
				$ItemPrice 			= $this->session->userdata('ItemPrice'); //Item Price
				$ItemNumber 		= $this->session->userdata('ItemNumber'); //Item Number
				$ItemDesc 			= $this->session->userdata('ItemDesc'); //Item Number
				$ItemQty 			= $this->session->userdata('ItemQty'); // Item Quantity
				$ItemTotalPrice 	= $this->session->userdata('ItemTotalPrice'); //(Item Price x Quantity = Total) Get total amount of product; 
				/*$TotalTaxAmount 	= $_SESSION['TotalTaxAmount'] ;  //Sum of tax for all items in this order. 
				$HandalingCost 		= $_SESSION['HandalingCost'];  //Handling cost for this order.
				$InsuranceCost 		= $_SESSION['InsuranceCost'];  //shipping insurance cost for this order.
				$ShippinDiscount 	= $_SESSION['ShippinDiscount']; //Shipping discount for this order. Specify this as negative number.
				$ShippinCost 		= $_SESSION['ShippinCost']; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate. */
				$GrandTotal 		= $this->session->userdata('GrandTotal');
			
				$padata = 	'&TOKEN='.urlencode($token).
							'&PAYERID='.urlencode($payer_id).
							'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE").
							
							//set item info here, otherwise we won't see product details later	
							'&L_PAYMENTREQUEST_0_NAME0='.urlencode($ItemName).
							'&L_PAYMENTREQUEST_0_NUMBER0='.urlencode($ItemNumber).
							'&L_PAYMENTREQUEST_0_DESC0='.urlencode($ItemDesc).
							'&L_PAYMENTREQUEST_0_AMT0='.urlencode($ItemPrice).
							'&L_PAYMENTREQUEST_0_QTY0='. urlencode($ItemQty).
			
							/* 
							//Additional products (L_PAYMENTREQUEST_0_NAME0 becomes L_PAYMENTREQUEST_0_NAME1 and so on)
							'&L_PAYMENTREQUEST_0_NAME1='.urlencode($ItemName2).
							'&L_PAYMENTREQUEST_0_NUMBER1='.urlencode($ItemNumber2).
							'&L_PAYMENTREQUEST_0_DESC1=Description text'.
							'&L_PAYMENTREQUEST_0_AMT1='.urlencode($ItemPrice2).
							'&L_PAYMENTREQUEST_0_QTY1='. urlencode($ItemQty2).
							*/
			
							'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($ItemTotalPrice).
							//'&PAYMENTREQUEST_0_TAXAMT='.urlencode($TotalTaxAmount).
							//'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($ShippinCost).
							//'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($HandalingCost).
							//'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($ShippinDiscount).
							//'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($InsuranceCost).
							'&PAYMENTREQUEST_0_AMT='.urlencode($GrandTotal).
							'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode);
				
				//We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
				//$paypal= new MyPayPal();
				$httpParsedResponseAr = $this->_PPHttpPost('DoExpressCheckoutPayment', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
				
				//Check if everything went ok..
				if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
				{
			
						echo '<h2>Success</h2>';
						echo 'Your Transaction ID : '.urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
						
							/*
							//Sometimes Payment are kept pending even when transaction is complete. 
							//hence we need to notify user about it and ask him manually approve the transiction
							*/
							
							if('Completed' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
							{
								echo '<div style="color:green">Payment Received! Your product will be sent to you very soon!</div>';
							}
							elseif('Pending' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
							{
								echo '<div style="color:red">Transaction Complete, but payment is still pending! '.
								'You need to manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
							}
			
							// we can retrive transection details using either GetTransactionDetails or GetExpressCheckoutDetails
							// GetTransactionDetails requires a Transaction ID, and GetExpressCheckoutDetails requires Token returned by SetExpressCheckOut
							$padata = 	'&TOKEN='.urlencode($token);
							//$paypal= new MyPayPal();
							$httpParsedResponseAr = $this->_PPHttpPost('GetExpressCheckoutDetails', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
			
							if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
							{
								
								echo '<br /><b>Stuff to store in database :</b><br /><pre>';
								/*
								#### SAVE BUYER INFORMATION IN DATABASE ###
								//see (http://www.sanwebe.com/2013/03/basic-php-mysqli-usage) for mysqli usage
								
								$buyerName = $httpParsedResponseAr["FIRSTNAME"].' '.$httpParsedResponseAr["LASTNAME"];
								$buyerEmail = $httpParsedResponseAr["EMAIL"];
								
								//Open a new connection to the MySQL server
								$mysqli = new mysqli('host','username','password','database_name');
								
								//Output any connection error
								if ($mysqli->connect_error) {
									die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
								}		
								
								$insert_row = $mysqli->query("INSERT INTO BuyerTable 
								(BuyerName,BuyerEmail,TransactionID,ItemName,ItemNumber, ItemAmount,ItemQTY)
								VALUES ('$buyerName','$buyerEmail','$transactionID','$ItemName',$ItemNumber, $ItemTotalPrice,$ItemQTY)");
								
								if($insert_row){
									print 'Success! ID of last inserted record is : ' .$mysqli->insert_id .'<br />'; 
								}else{
									die('Error : ('. $mysqli->errno .') '. $mysqli->error);
								}
								
								*/
								
								echo '<pre>';
								print_r($httpParsedResponseAr);
								echo '</pre>';
							} else  {
								echo '<div style="color:red"><b>GetTransactionDetails failed:</b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
								echo '<pre>';
								print_r($httpParsedResponseAr);
								echo '</pre>';
			
							}
				
				}else{
						echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
						echo '<pre>';
						print_r($httpParsedResponseAr);
						echo '</pre>';
				}
			}		
		
	}
		
	
	function _PPHttpPost($methodName_, $nvpStr_, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode) {
			// Set up your API credentials, PayPal end point, and API version.
			$API_UserName = urlencode($PayPalApiUsername);
			$API_Password = urlencode($PayPalApiPassword);
			$API_Signature = urlencode($PayPalApiSignature);
			
			$paypalmode = ($PayPalMode=='sandbox') ? '.sandbox' : '';
	
			$API_Endpoint = "https://api-3t".$paypalmode.".paypal.com/nvp";
			$version = urlencode('109.0');
		
			// Set the curl parameters.
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
			// Turn off the server and peer verification (TrustManager Concept).
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
		
			// Set the API operation, version, and API signature in the request.
			$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
		
			// Set the request as a POST FIELD for curl.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		
			// Get response from the server.
			$httpResponse = curl_exec($ch);
		
			if(!$httpResponse) {
				exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
			}
		
			// Extract the response details.
			$httpResponseAr = explode("&", $httpResponse);
		
			$httpParsedResponseAr = array();
			foreach ($httpResponseAr as $i => $value) {
				$tmpAr = explode("=", $value);
				if(sizeof($tmpAr) > 1) {
					$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
				}
			}
		
			if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
				exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
			}
		
		return $httpParsedResponseAr;
	}
}
