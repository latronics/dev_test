<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cart extends Controller {
function Cart()
{
		parent::Controller();

		if (isset($this->session->userdata['user_id']))
		{
			$this->load->model('Auth_model');
			$this->Auth_model->VerifyUser();
		}

		$this->load->model('Product_model');

		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('ctr', ReturnCountries($this->config->config['language_abbr']));
		$this->mysmarty->assign('sts', ReturnStates());


		if (isset($this->session->userdata['cart']) && ($this->session->userdata['cart'] != '') )
		{
			$this->mysmarty->assign('cartsession',$this->session->userdata['cart']);
			$this->mysmarty->assign('carttotal', $this->_CartTotal());
		}
}
function index()
{
	Redirect('');

}

function _CartQuantityAvailableCheck($quantity = 0, $checkproduct = array())
	{

			//////
	if ($this->config->config['cartclosed'] == 1) {
	echo '<div style="padding:10px;">';

	echo $this->config->config['cartclosemsg'];
	echo '<br><br>
	<a onclick="cartSlide()" href="#"><img src="'.Site_url().'images/close.png" style="margin-top:5px; margin-left:10px;" /></a>
	</div>';
	exit();
	}

	/////

		if ($checkproduct['quantity'] < 1 || $quantity > $checkproduct['quantity'])
			{
				$this->maildata['msg_date'] = CurrentTime();
				$this->maildata['msg_title'] = 'Quantity unavailable for purchase  @ '.FlipDateMail($this->maildata['msg_date']);

				if (isset($this->session->userdata['user_id']))
					{
						$this->maildata['msg_body'] = '						
						User <a href="'.Site_url().'Myusers/ShowUser/'.(int)$this->session->userdata['user_id'].'" target="_blank" style="text-decoration:underline; color:blue;">'.$this->session->userdata['fname'].' '.$this->session->userdata['lname'].'</a>';
					}
					else
					{
						$this->maildata['msg_body'] = 'A Site Visitor';
					}

					$this->maildata['msg_body'] .= ' attempted to purchase '.(int)$quantity.' from '.$checkproduct['quantity'].' available quantity of product <a href="'.Site_url().'Myproducts/Edit/'.$checkproduct['e_id'].'" target="_blank" style="text-decoration:underline; color:blue;">'.$checkproduct['e_title'].'</a>.
						';
					$this->mailid = 2;
					GoMail ($this->maildata);
					$this->load->model('Login_model');
					$this->Login_model->InsertHistoryData($this->maildata);

			$this->noadd = true;

			}
	}

function CartUpdate()
	{

			//////
	if ($this->config->config['cartclosed'] == 1) {
	echo '<div style="padding:10px;">';

	echo $this->config->config['cartclosemsg'];
	echo '<br><br>
	<a onclick="cartSlide()" href="#"><img src="'.Site_url().'images/close.png" style="margin-top:5px; margin-left:10px;" /></a>
	</div>';
	exit();
	}

	/////
	if (isset($_POST['e_id']) && isset($_POST['quant']))
	{
		(int)$id = $_POST['e_id'];
		(int)$quantity = $_POST['quant'] ? $_POST['quant'] : 1;

		if ((int)$quantity == 0) $quantity = 1;


		$shoppingcart = $this->session->userdata['cart'];

		if (isset($shoppingcart[(int)$id]))
		{

			$checkproduct = $this->Product_model->CheckProduct((int)$id);
			if ((int)$checkproduct['e_id'] == 0) { echo 'Product not found'; exit();}
			$this->_CartQuantityAvailableCheck($quantity, $checkproduct);
			if (!isset($this->noadd))
				{
					$shoppingcart[$checkproduct['e_id']]['quantity'] = $quantity;
					if ($shoppingcart[$checkproduct['e_id']]['buyItNowPrice'] != $checkproduct['buyItNowPrice']) $shoppingcart[$checkproduct['e_id']]['buyItNowPrice'] = $checkproduct['buyItNowPrice'];

					$this->mysmarty->assign('msg', '<br><br><span style="color:green;">Product amount updated.</span><br><br>');
				}
			else
				{
					$this->mysmarty->assign('msg', '<br><br><span style="color:green;">Not enough available quantity to update...</span><br><br>');
				}
		}
	}
	else
	{
		$this->mysmarty->assign('cartactiomsg', 'Missing product or quantity information.');
		/// Mail SUPPORT TO INFORM.
	}

	if (!isset($shoppingcart)) $shoppingcart = array();

		$cartdata['cart'] = $shoppingcart;
		$this->session->set_userdata($cartdata);
		$this->_FillMyShipping();
		$this->mysmarty->assign('cartsession', $shoppingcart);
		$this->mysmarty->assign('carttotal', $this->_CartTotal());
		$returnhtml = $this->mysmarty->fetch('welcome/welcome_cart.html');
		echo $returnhtml;

	}

function CartAdd()
{
		//////
	if ($this->config->config['cartclosed'] == 1) {
	echo '<div style="padding:10px;">';

	echo $this->config->config['cartclosemsg'];
	echo '<br><br>
	<a onclick="cartSlide()" href="#"><img src="'.Site_url().'images/close.png" style="margin-top:5px; margin-left:10px;" /></a>
	</div>';
	exit();
	}

	/////
	if (isset($this->session->userdata['cart'])) $shoppingcart = $this->session->userdata['cart'];
	else $shoppingcart = array();

	if (isset($_POST['e_id']))
	{
		(int)$id = $_POST['e_id'];
		(int)$quantity = isset($_POST['qty']) ? $_POST['qty'] : 1;

		if ($quantity == 0) $quantity = 1;
		if ((int)$id == 0) exit();

		$checkproduct = $this->Product_model->CheckProduct((int)$id);
		if ((int)$checkproduct['e_id'] == 0) { echo 'Product not found'; exit();}

		$this->_CartQuantityAvailableCheck($quantity, $checkproduct);
		$oldcart = $shoppingcart;
		if (!isset($this->noadd))
			{
				if (isset($shoppingcart[$checkproduct['e_id']]))
				{
					$shoppingcart[$checkproduct['e_id']]['quantity'] = (int)$shoppingcart[$checkproduct['e_id']]['quantity'] + $quantity;

					if ($shoppingcart[$checkproduct['e_id']]['buyItNowPrice'] != $checkproduct['buyItNowPrice']) $shoppingcart[$checkproduct['e_id']]['buyItNowPrice'] = $checkproduct['buyItNowPrice'];

					$this->mysmarty->assign('msg', '<br><br><span style="color:green;">Amount updated.</span><br>');

				}
				else
				{
					$shoppingcart[$checkproduct['e_id']] = array(
																  'e_id' => $checkproduct['e_id'],
																  'e_title' => $checkproduct['e_title'],
																  'e_sef' => $checkproduct['e_sef'],
																  'buyItNowPrice' => $checkproduct['buyItNowPrice'],
																  'e_img1' => $checkproduct['e_img1'],
																  'idpath' => $checkproduct['idpath'],
																  'quantity' => $quantity
																	  );

							//if ($checkproduct['e_img1'] != '') $shoppingcart[$checkproduct['e_id']]['imagesize'] = getimagesize(site_url().$this->config->config['wwwpath']['imgproducts'].'/thumb_'.$checkproduct['e_img1']);

					$this->mysmarty->assign('msg', '<br><br><span style="color:green;">Added to cart.</span><br>');
				}
			}
		else
			{
				$this->mysmarty->assign('msg', '<br><br><span style="color:red">Quantity not available for purchase</span><br>');
			}
	}
	else
	{
		$this->mysmarty->assign('cartactiomsg', 'Missing product or quantity information.');
		/// Mail SUPPORT TO INFORM.
	}
	$cartdata['cart'] = $shoppingcart;
	//printcool ($cartdata['cart']);
	$this->session->set_userdata($cartdata);

	$this->_FillMyShipping();


	//$this->mysmarty->assign('sel_shipping', $this->session->userdata['sel_shipping']);


	$this->mysmarty->assign('cartsession', $shoppingcart);
	$this->mysmarty->assign('carttotal', $this->_CartTotal());
	$returnhtml = $this->mysmarty->fetch('welcome/welcome_cart.html');
 	echo $returnhtml;

	//echo ('quantity '.$quantity); echo(' / '); echo ('e_id '.$id);echo '<br>';
	//echo ('cartbefore '.count($oldcart)); echo(' / ');echo ('cartafter '.count($shoppingcart)); echo(' / ');echo ('matchede_id '.$checkproduct['e_id']);

}
function _FillMyShipping()
{

	$refreshedcart = $this->_RefreshCart();

	$courier = $refreshedcart['shipping']['domestic'];
        $this->mysmarty->assign('courier', $courier);



	$this->session->set_userdata(array('rates' => $courier));
				//printcool ($courier);
	$this->load->helper('directory');
	$this->load->helper('file');

	$sresponseXml = read_file($this->config->config['ebaypath'].'/shipping.txt');
	$shxml = simplexml_load_string($sresponseXml);
	$this->mysmarty->assign('ShippingOptions', $shxml->ShippingServiceDetails);

}
function CartRemove()
{

		//////
	if ($this->config->config['cartclosed'] == 1) {
	echo '<div style="padding:10px;">';

	echo $this->config->config['cartclosemsg'];
	echo '<br><br>
	<a onclick="cartSlide()" href="#"><img src="'.Site_url().'images/close.png" style="margin-top:5px; margin-left:10px;" /></a>
	</div>';
	exit();
	}

	/////
	if (!isset($_POST['delid'])) exit('Oops');
	(int)$id = (int)$_POST['delid'];

	$sesscart = $this->session->userdata['cart'];
	if (isset($sesscart[(int)$id]))
		{
			unset ($sesscart[(int)$id]);
			$this->mysmarty->assign('msg', '<br><br><span style="color:green;">Product removed.</span><br>');
		}

	$num = count($sesscart);
	if ($num == 0)
	{
		$this->CartEmpty();
	}
	else
	{
		$this->mysmarty->assign('cartsession', $sesscart);
		$this->session->set_userdata(array('cart' => $sesscart));
		$this->mysmarty->assign('carttotal', $this->_CartTotal());
		$this->_FillMyShipping();
		$returnhtml = $this->mysmarty->fetch('welcome/welcome_cart.html');
		echo $returnhtml;
	}
}
function CartShow()
	{

			//////
	if ($this->config->config['cartclosed'] == 1) {
	echo '<div style="padding:10px;">';

	echo $this->config->config['cartclosemsg'];
	echo '<br><br>
	<a onclick="cartSlide()" href="#"><img src="'.Site_url().'images/close.png" style="margin-top:5px; margin-left:10px;" /></a>
	</div>';
	exit();
	}

	/////

		if (isset($this->session->userdata['cart']))
					{
						$this->mysmarty->assign('cartsession', $this->session->userdata['cart']);
						$this->mysmarty->assign('carttotal', $this->_CartTotal());

                                                //$this->_FillMyShipping();
					}
				else
					{

						$this->mysmarty->assign('carttotal', $this->_CartTotal());
					}


				if (isset($this->session->userdata['sel_shipping'])) $this->mysmarty->assign('sel_shipping', $this->session->userdata['sel_shipping']);



				$returnhtml = $this->mysmarty->fetch('welcome/welcome_cart.html');
				echo $returnhtml;

	}
function CartEmpty()
	{
			//////
	if ($this->config->config['cartclosed'] == 1) {
	echo '<div style="padding:10px;">';

	echo $this->config->config['cartclosemsg'];
	echo '<br><br>
	<a onclick="cartSlide()" href="#"><img src="'.Site_url().'images/close.png" style="margin-top:5px; margin-left:10px;" /></a>
	</div>';
	exit();
	}

	/////

	$this->session->unset_userdata('cart');
	$this->mysmarty->assign('cartsession', array());
	$this->mysmarty->assign('carttotal', $this->_CartTotal());
 	//$returnhtml = $this->mysmarty->fetch('welcome/welcome_cart.html');
 	echo '<script type="text/javascript">toggleCart();</script>';
	}



function _CartTotal()
	{
			//////
	if ($this->config->config['cartclosed'] == 1) {
	echo '<div style="padding:10px;">';

	echo $this->config->config['cartclosemsg'];
	echo '<br><br>
	<a onclick="cartSlide()" href="#"><img src="'.Site_url().'images/close.png" style="margin-top:5px; margin-left:10px;" /></a>
	</div>';
	exit();
	}

	/////

	if (isset($this->session->userdata['cart'])) $crtdata = $this->session->userdata['cart'];
	else $crtdata = FALSE;

	$total = 0;
	if ($crtdata && $crtdata != '')
		{
			foreach ($crtdata as $key => $value)
			{
			$total = $total + ((int)$value['quantity'] * (float)$value['buyItNowPrice']);
			}

			$sel_shipping = $this->session->userdata['sel_shipping'];
			if (is_array($sel_shipping) && isset($sel_shipping['freeship']) && isset($sel_shipping['cost']))
			{
				if ($sel_shipping['freeship'] != 'on') $total = $total + $sel_shipping['cost'];
			}
		}
		return (float)$total;
	}

function _unifyweight($quantity, $lbs, $oz, $innercheck = FALSE)
	{

	$totallbs = (int)$quantity * (float)$lbs;
	$totaloz = (int)$quantity * (float)$oz;


	$addlbs = 0;
	if ($totaloz > 16)
		{
		while ($totaloz > 16)
			{
			$totaloz = $totaloz - 16;
			$addlbs++;
			}
		}


	$totallbs = $totallbs + $addlbs;
	if (!$innercheck) $this->mysmarty->assign('calclbs',array('lbs' => $totallbs, 'oz' => $totaloz));
	if ($totaloz > 0) $totaloz = $totaloz/16;
	$totallbs = $totallbs + $totaloz;
	$totallbs = sprintf("%.1f", $totallbs);

	return $totallbs;
	}
function _RefreshCart()
	{
		if (!isset($this->session->userdata['cart']) || (count($this->session->userdata['cart']) == 0) || ($this->session->userdata['cart'] == '')){ redirect(""); exit;}

								$shipping = array();
								$totalitemsquantity = 0;
								$prodcount = 0;
								$repcount = 0;

								foreach ($this->session->userdata['cart'] as $key => $value)
								{
								$this->reforder[$key] = $this->Product_model->RefreshProduct((int)$value['e_id']);

								$this->order[$key]['e_id'] = $value['e_id'];
								$this->order[$key]['quantity'] = $value['quantity'];
								$this->order[$key]['e_title'] = $this->reforder[$key]['e_title'];
								$this->order[$key]['e_sef'] = $this->reforder[$key]['e_sef'];
								$this->order[$key]['e_img1'] = $this->reforder[$key]['e_img1'];
								$this->order[$key]['idpath'] = $this->reforder[$key]['idpath'];

								$this->order[$key]['sn'] = '';
								$this->order[$key]['revs'] = 0;
								$this->order[$key]['admin'] = '';

								$this->order[$key]['buyItNowPrice'] = (float)$this->reforder[$key]['buyItNowPrice'];
								if (strlen($this->reforder[$key]['shipping']) > 10)
								{
									$this->order[$key]['shipping'] = unserialize($this->reforder[$key]['shipping']);

									foreach ($this->order[$key]['shipping']['domestic'] as $s)
										{
											if (!isset($shipping['domestic'][$s['ShippingService']]) && $s['ShippingService'] != '') $shipping['domestic'][$s['ShippingService']] = array('cost' => $s['ShippingServiceCost'], 'additionalcost' => $s['AdditionalCost'], 'freeship' => $s['FreeShipping']);
											elseif ($shipping['domestic'][$s['ShippingService']]['cost'] < $s['ShippingServiceCost']) $shipping['domestic'][$s['ShippingService']]['cost'] = $s['ShippingServiceCost'];
										}

									foreach ($this->order[$key]['shipping']['international'] as $s)
										{

											if (!isset($shipping['international'][$s['ShippingService']]) && $s['ShippingService'] != '') $shipping['international'][$s['ShippingService']] = array('cost' => $s['ShippingServiceCost'], 'additionalcost' => $s['AdditionalCost'], 'freeship' => $s['FreeShipping']);
											elseif ($shipping['international'][$s['ShippingService']]['cost'] < $s['ShippingServiceCost']) $shipping['international'][$s['ShippingService']]['cost'] = $s['ShippingServiceCost'];
										}

								}
								else $this->order[$key]['shipping'] = false;
								$totalitemsquantity = $totalitemsquantity + (int)$value['quantity'];

								//$this->order[$key]['totalweight'] = $this->_unifyweight((int)$value['quantity'], $this->reforder[$key]['p_lbs'], $this->reforder[$key]['p_oz']);

									$this->order[$key]['totalweight_custom'] = 0;
									$prodcount++;

								$this->order[$key]['total'] = ((int)$value['quantity'] * (float)$this->reforder[$key]['buyItNowPrice']);
								}
								$total = 0;
								$weight = 0;
								$weight_custom = 0;

								foreach ($this->order as $key => $value)  $total = ($total + (float)$value['total']);

								//printcool ($shipping);


						       return array('order' => $this->order, 'total' => $total, 'shipping' => $shipping,  'totalitemsquantity' => $totalitemsquantity, 'prodcount' => $prodcount, 'repcount' => $repcount);



	}

	///////////////////// COURIER

function CartRates()
	{

			//////
	if ($this->config->config['cartclosed'] == 1) {
	echo '<div style="padding:10px;">';

	echo $this->config->config['cartclosemsg'];
	echo '<br><br>
	<a onclick="cartSlide()" href="#"><img src="'.Site_url().'images/close.png" style="margin-top:5px; margin-left:10px;" /></a>
	</div>';
	exit();
	}

	/////

		$statecodes = array_flip(ReturnStates());
		$countrycodes = array_flip(ReturnCountries());
		$this->session->unset_userdata(array('sel_shipping_adr' => ''));
		$this->session->unset_userdata(array('sel_shipping' => ''));
		$this->session->unset_userdata(array('rates' => ''));

		$this->load->library('form_validation');

		$this->form_validation->set_rules('country', 'Coutry', 'trim|required|xss_clean|max_length[50]');
		//$this->form_validation->set_rules('zip', 'Zip/Postcode', 'trim|required|xss_clean|max_length[6]');
		//$this->form_validation->set_rules('res', 'Residential', 'trim|xss_clean');

		if ($this->form_validation->run() == FALSE)
			{

				$regdata['country'] = $this->input->post('country',true);
				//$regdata['zip'] = $this->input->post('zip',true);
				//$regdata['res'] = $this->input->post('res',true);

				$this->mysmarty->assign('regdata', $regdata);
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->CartShow();
			}
			else
			{

				$client['country'] = $this->form_validation->set_value('country',true);
                                if($client['country'] == null)
                                {
                                    $client['country'] = $this->input->post("country");
                                }
				//$client['zip'] = $this->form_validation->set_value('zip',true);
				//$client['res'] = $this->form_validation->set_value('res',true);

				$this->session->set_userdata(array('sel_shipping_adr' => $client));
				$refreshedcart = $this->_RefreshCart();
				//printcool ($courier);


				if ($client['country'] == 'United States of America')
                                {

                                    $courier = $refreshedcart['shipping']['domestic'];
                                    $this->mysmarty->assign('international', 0);

                                }
				else
                                {


                                    if($refreshedcart['shipping']['international'] == null){
                                    $courier = $refreshedcart['shipping']['domestic'];
                                        $this->mysmarty->assign('international', 1);
                                    }else
                                    {
                                    $courier = $refreshedcart['shipping']['international'];
                                    }


                                }
				$this->mysmarty->assign('courier', $courier);
				$this->mysmarty->assign('regdata', $client);

				$this->session->set_userdata(array('rates' => $courier));
				//printcool ($courier);
				$this->load->helper('directory');
				$this->load->helper('file');

				$sresponseXml = read_file($this->config->config['ebaypath'].'/shipping.txt');
				$shxml = simplexml_load_string($sresponseXml);
				$this->mysmarty->assign('ShippingOptions', $shxml->ShippingServiceDetails);
				//printcool ($courier);

				//Redirect ('CheckOut');

			}


			$this->CartShow();

}
function CalcIntShipping()
{
    $total_weight = 0;
    $zip_shipp = $this->input->post("zip_shipp");
    $country = $this->input->post("country");
    $total_content = 0;
    foreach($this->session->userdata('cart') as $cart_items)
    {

        $this->db->where("e_id", $cart_items['e_id']);
        $ebay_data = $this->db->get("ebay")->result_object();
        foreach($ebay_data as $ebay_data)
        {
            $total_weight += $ebay_data->weight_lbs;
        }
        $total_content += $cart_items->buyItNowPrice;
    }
    //CALL SHIPPING METHODS HERE TO GET QUOTES
//    echo "ZipCode: ". $zip_shipp;
//    echo "Total Weight: " . $total_weight;

    $this->db->where("nicename", $country);
    $country_code_data = $this->db->get("la_country_codes")->result_object();
    $country_code = $country_code_data[0]->iso;

        $currentdata['Address']['PostalCode'] = $zip_shipp;
        $currentdata['Address']['CountryCode'] = $country_code;
        $currentdata['Address']['country'] = $country;
        $currentdata['Address']['OrigCountry'] = $country;
        $currentdata['alldata']['total'] = $total_content;
        $currentdata['Weight'] = $total_weight;
//            $courier['quote'] = $this->_PrepareFedexInternational($currentdata);
            $courier['uspsquote'] = $this->_PrepareUspsInternational($currentdata);
            $courier['dhlquote'] = $this->_PrepareDHLInternational($currentdata);

            print_r(json_encode($courier));

}

 function _PrepareDHLInternational($currentdata) {
        if ($currentdata['Address']['CountryCode'] != 'US') {

            //URL Production(PROD): https://xmlpi-ea.dhl.com/XMLShippingServlet
            //URL Test(TEST):    https://xmlpitest-ea.dhl.com/XMLShippingServlet
            // may need to urlencode xml portion
            $xml = '<?xml version="1.0" encoding="utf-8"?>
<req:DCTRequest xmlns:req="http://www.dhl.com">
  <GetQuote>
    <Request>
      <ServiceHeader>
        <MessageTime>' . date("Y-m-dTH:i:s") . '</MessageTime>
        <MessageReference>718688fff23f46f49d7cf9b67cd40d4e</MessageReference>
        <SiteID>xmlLAcompute</SiteID>
        <Password>O93aS_nitM</Password>
      </ServiceHeader>
    </Request>
    <From>
      <CountryCode>US</CountryCode>
      <Postalcode>90520</Postalcode>
      <City>Los Angeles</City>
    </From>
    <BkgDetails>
      <PaymentCountryCode>US</PaymentCountryCode>
      <Date>' . date("Y-m-d") . '</Date>
      <ReadyTime>PT9H</ReadyTime>
      <DimensionUnit>IN</DimensionUnit>
      <WeightUnit>LB</WeightUnit>
      <Pieces>
        <Piece>
          <PieceID>1</PieceID>
          <Height>15</Height>
          <Depth>10</Depth>
          <Width>5</Width>
          <Weight>5</Weight>
        </Piece>
      </Pieces>
      <PaymentAccountNumber>847064557</PaymentAccountNumber>
      <IsDutiable>Y</IsDutiable>
    </BkgDetails>
    <To>
      <CountryCode>' . $currentdata['Address']['CountryCode'] . '</CountryCode>
      <Postalcode>' . $currentdata['Address']['PostalCode'] . '</Postalcode>
	  <City></City>
      
    </To>
    <Dutiable>
      <DeclaredCurrency>USD</DeclaredCurrency>
      <DeclaredValue>10</DeclaredValue>
    </Dutiable>
  </GetQuote>
</req:DCTRequest>'; //
            try {
                $ch = curl_init();

                if (FALSE === $ch)
                    throw new Exception('failed to initialize');

                // set URL and other appropriate options
                curl_setopt($ch, CURLOPT_URL, 'https://xmlpitest-ea.dhl.com/XMLShippingServlet');
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

                if (curl_errno($ch)) {
                    // moving to display page to display curl errors
                    //echo curl_errno($ch) ;
                    //echo curl_error($ch);
                    return array();
                } else {
                    // grab URL and pass it to the browser
                    $res = objectToArray(simplexml_load_string(curl_exec($ch)));
                    if (FALSE === $res)
                        throw new Exception(curl_error($ch), curl_errno($ch));

                    // close curl resource, and free up system resources
                    curl_close($ch);
                    $quote = array();
                    if($res['GetQuoteResponse']->BkgDetails->QtdShp != null){
                    foreach ($res['GetQuoteResponse']->BkgDetails->QtdShp as $k => $v) {//printcool($v->ProductShortName);
                        //printcool ($v->QtdSInAdCur[0]->TotalAmount);
                        if (trim($v->ProductShortName) == 'EXPRESS WORLDWIDE') {
                            $quote[0]['rate'] = (float) $v->QtdSInAdCur[0]->TotalAmount; // + $this->config->config['shippingadd'];
                            @$quote[0]['rate'] = $quote[0]['rate'] + (($quote[0]['rate'] / 100) * $this->config->config['shippingpercentadd']);
                            $quote[0]['mailservice'] = "DHL Express Worldwide";
                            $quote[0]['mailcode'] = 'DHL_EXPRESS_WORLDWIDE';
                            $quote[0]['time'] = (int) $v->TotalTransitDays;
                        }

                    }
                    }
                    return $quote;
                    /* echo '<h1>Services</h1>';
                      $q = 1;
                      foreach($res['GetQuoteResponse']->Srvs->Srv as $k => $v)
                      {
                      echo '<h2>'.$q.'</h2>';
                      printcool ($v);
                      $q++;


                      } */
                }
            } catch (Exception $e) {

                trigger_error(sprintf(
                                'Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
            }
        } else
            return array();
    }

function _PrepareUspsInternational($currentdata, $inputdelivery = 0) {
        /// Check if Domestic and have single item flat rate and NO free ground shipping. Remove Priority mail from USABLEs list so as to not have have 2 when the result is apended later on in the flow...
        /*

          LTSUPGTAMPREGLTSUPGT
          [mailservice] => U.S.P.S. Priority Mail Express 2-Day<sup>�</sup> Shipping
          [mailcode] => USPS_PRIORITY_MAIL_EXPRESS_2DAYLTSUPGT8482LTSUPGT
          [mailservice] => U.S.P.S. Priority Mail Express 2-Day<sup>�</sup> Hold For Pickup Shipping
          [mailcode] => USPS_PRIORITY_MAIL_EXPRESS_2DAYLTSUPGT8482LTSUPGT_HOLD_FOR_PICKUP
          [mailservice] => U.S.P.S. Priority Mail Express 2-Day<sup>�</sup> Flat Rate Boxes Shipping
          [mailcode] => USPS_PRIORITY_MAIL_EXPRESS_2DAYLTSUPGT8482LTSUPGT_FLAT_RATE_BOXES
          [mailservice] => U.S.P.S. Priority Mail Express 2-Day<sup>�</sup> Flat Rate Boxes Hold For Pickup Shipping
          [mailcode] => USPS_PRIORITY_MAIL_EXPRESS_2DAYLTSUPGT8482LTSUPGT_FLAT_RATE_BOXES_HOLD_FOR_PICKUP
          [mailservice] => U.S.P.S. Priority Mail 2-Day<sup>�</sup> Large Flat Rate Box Shipping
          [mailcode] => USPS_PRIORITY_MAIL_2DAYLTSUPGT8482LTSUPGT_LARGE_FLAT_RATE_BOX
          [mailservice] => U.S.P.S. Priority Mail 2-Day<sup>�</sup> Medium Flat Rate Box Shipping
          [mailcode] => USPS_PRIORITY_MAIL_2DAYLTSUPGT8482LTSUPGT_MEDIUM_FLAT_RATE_BOX
          [mailservice] => U.S.P.S. Priority Mail 2-Day<sup>�</sup> Small Flat Rate Box Shipping
          [mailcode] => USPS_PRIORITY_MAIL_2DAYLTSUPGT8482LTSUPGT_SMALL_FLAT_RATE_BOX
          [mailservice] => U.S.P.S. Standard Post<sup>�</sup> Shipping
          [mailcode] => USPS_STANDARD_POSTLTSUPGT174LTSUPGT
          [mailservice] => U.S.P.S. Media Mail<sup>�</sup> Shipping
          [mailcode] => USPS_MEDIA_MAILLTSUPGT174LTSUPGT
          -----------------------------------------
          [mailservice] => U.S.P.S. Global Express Guaranteed<sup>�</sup> (GXG)** Shipping
          [mailcode] => USPS_GLOBAL_EXPRESS_GUARANTEEDLTSUPGT174LTSUPGT_GXG
          [mailservice] => U.S.P.S. Global Express Guaranteed<sup>�</sup> Non-Document Rectangular Shipping
          [mailcode] => USPS_GLOBAL_EXPRESS_GUARANTEEDLTSUPGT174LTSUPGT_NONDOCUMENT_RECTANGULAR
          [mailservice] => U.S.P.S. Global Express Guaranteed<sup>�</sup> Non-Document Non-Rectangular Shipping
          [mailcode] => USPS_GLOBAL_EXPRESS_GUARANTEEDLTSUPGT174LTSUPGT_NONDOCUMENT_NONRECTANGULAR
          [mailservice] => U.S.P.S. USPS GXG<sup>�</sup> Envelopes** Shipping
          [mailcode] => USPS_USPS_GXGLTSUPGT8482LTSUPGT_ENVELOPES
          [mailservice] => U.S.P.S. Priority Mail Express International<sup>�</sup> Shipping
          [mailcode] => USPS_PRIORITY_MAIL_EXPRESS_INTERNATIONALLTSUPGT8482LTSUPGT
          [mailservice] => U.S.P.S. Priority Mail Express International<sup>�</sup> Flat Rate Boxes Shipping
          [mailcode] => USPS_PRIORITY_MAIL_EXPRESS_INTERNATIONALLTSUPGT8482LTSUPGT_FLAT_RATE_BOXES
          [mailservice] => U.S.P.S. Priority Mail Express International<sup>�</sup> Flat Rate Envelope Shipping
          [mailcode] => USPS_PRIORITY_MAIL_EXPRESS_INTERNATIONALLTSUPGT8482LTSUPGT_FLAT_RATE_ENVELOPE
          [mailservice] => U.S.P.S. Priority Mail Express International<sup>�</sup> Legal Flat Rate Envelope Shipping
          [mailcode] => USPS_PRIORITY_MAIL_EXPRESS_INTERNATIONALLTSUPGT8482LTSUPGT_LEGAL_FLAT_RATE_ENVELOPE
          [mailservice] => U.S.P.S. Priority Mail Express International<sup>�</sup> Padded Flat Rate Envelope Shipping
          [mailcode] => USPS_PRIORITY_MAIL_EXPRESS_INTERNATIONALLTSUPGT8482LTSUPGT_PADDED_FLAT_RATE_ENVELOPE
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> Large Flat Rate Box Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT_LARGE_FLAT_RATE_BOX
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> Medium Flat Rate Box Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT_MEDIUM_FLAT_RATE_BOX
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> Small Flat Rate Box** Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT_SMALL_FLAT_RATE_BOX
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> DVD Flat Rate priced box** Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT_DVD_FLAT_RATE_PRICED_BOX
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> Large Video Flat Rate priced box** Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT_LARGE_VIDEO_FLAT_RATE_PRICED_BOX
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> Flat Rate Envelope** Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT_FLAT_RATE_ENVELOPE
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> Legal Flat Rate Envelope** Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT_LEGAL_FLAT_RATE_ENVELOPE
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> Padded Flat Rate Envelope** Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT_PADDED_FLAT_RATE_ENVELOPE
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> Gift Card Flat Rate Envelope** Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT_GIFT_CARD_FLAT_RATE_ENVELOPE
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> Small Flat Rate Envelope** Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT_SMALL_FLAT_RATE_ENVELOPE
          [mailservice] => U.S.P.S. Priority Mail International<sup>�</sup> Window Flat Rate Envelope** Shipping
          [mailcode] => USPS_PRIORITY_MAIL_INTERNATIONALLTSUPGT174LTSUPGT_WINDOW_FLAT_RATE_ENVELOPE
          [mailservice] => U.S.P.S. First-Class Package International Service<sup>�</sup>** Shipping
          [mailcode] => USPS_FIRSTCLASS_PACKAGE_INTERNATIONAL_SERVICELTSUPGT8482LTSUPGT
          [mailservice] => U.S.P.S. First-Class Mail<sup>�</sup> International Large Envelope** Shipping
          [mailcode] => USPS_FIRSTCLASS_MAILLTSUPGT174LTSUPGT_INTERNATIONAL_LARGE_ENVELOPE
         */


        //if (($currentdata['alldata']['totalitemsquantity'] == 1) && ($currentdata['alldata']['shipping'] == 1) && ($currentdata['Address']['OrigCountry'] == 'United States of America') && ($currentdata['alldata']['groundshipping'] != 1)) {
        /* $this->usable = array(
          'U.S.P.S. Express Mail Shipping',
          'U.S.P.S. Express Mail',
          'U.S.P.S. Parcel Post Shipping',
          'U.S.P.S. Parcel Post',
          'U.S.P.S. Express Mail<sup>&reg;</sup> Shipping',
          'U.S.P.S. Express Mail<sup>&reg;</sup>',
          'U.S.P.S. Parcel Post<sup>&reg;</sup> Shipping',
          'U.S.P.S. Parcel Post<sup>&reg;</sup>'
          ); }
          else { $this->usable = array(
          'U.S.P.S. Express Mail Shipping',
          'U.S.P.S. Express Mail',
          'U.S.P.S. Priority Mail Shipping',
          'U.S.P.S. Priority Mail',
          'U.S.P.S. Priority Mail International Shipping',
          'U.S.P.S. Priority Mail International',
          'U.S.P.S. Express Mail International Shipping',
          'U.S.P.S. Express Mail International',
          'U.S.P.S. Parcel Post Shipping',
          'U.S.P.S. Parcel Post',
          'U.S.P.S. Express Mail<sup>&reg;</sup> Shipping',
          'U.S.P.S. Express Mail<sup>&reg;</sup>',
          'U.S.P.S. Priority Mail<sup>&reg;</sup> Shipping',
          'U.S.P.S. Priority Mail<sup>&reg;</sup>',
          'U.S.P.S. Priority Mail<sup>&reg;</sup> International Shipping',
          'U.S.P.S. Priority Mail<sup>&reg;</sup> International',
          'U.S.P.S. Express Mail<sup>&reg;</sup> International Shipping',
          'U.S.P.S. Express Mail<sup>&reg;</sup> International',
          'U.S.P.S. Parcel Post<sup>&reg;</sup> Shipping',
          'U.S.P.S. Parcel Post<sup>&reg;</sup>'
          );





          'U.S.P.S. Priority Mail Express 1-Day Flat Rate Boxes',
          'U.S.P.S. Priority Mail Express 2-Day Flat Rate Boxes',
          'U.S.P.S. Priority Mail Express 3-Day Flat Rate Boxes',
          'U.S.P.S. Priority Mail Express 1-Day',
          'U.S.P.S. Priority Mail Express 2-Day',
          'U.S.P.S. Priority Mail Express 3-Day',
         */
        $this->usable = array(
            'U.S.P.S. Priority Mail 1-Day Large Flat Rate Box',
            'U.S.P.S. Priority Mail 2-Day Large Flat Rate Box',
            'U.S.P.S. Priority Mail 3-Day Large Flat Rate Box',
            'U.S.P.S. Priority Mail 1-Day',
            'U.S.P.S. Priority Mail 2-Day',
            'U.S.P.S. Priority Mail 3-Day',
            'Priority Mail 1-Day',
            'Priority Mail 2-Day',
            'Priority Mail 3-Day',
            'U.S.P.S. Standard Post',
            'U.S.P.S. Priority Mail Express International',
            'U.S.P.S. Priority Mail Express International Flat Rate Boxes',
            'U.S.P.S. Priority Mail International Large Flat Rate Box',
            'U.S.P.S. Priority Mail International',
            'U.S.P.S. First-Class Package International Service'
        );

        foreach ($this->usable as $ukey => $uvalue) {
            $this->mailcode[$ukey] = preg_replace("[^A-Za-z0-9\_]", "", strtoupper(str_replace('.', '', str_replace(" ", "_", cleanusps($uvalue)))));
            $this->matchservice[$this->mailcode[$ukey]] = cleanusps($uvalue);
        }

        //	printcool($currentdata['alldata']);
//printcool ($this->mailcode);


        /* $this->load->library('uspsrates');
          $this->uspsrates->setServer("http://production.shippingapis.com/ShippingAPI.dll");
          $this->uspsrates->setUserName("640LOSAN0902");
          $this->uspsrates->setPass("683GF04NK255");
          $this->uspsrates->setService("All"); */




        $this->load->library('uspsv4rates');

        if ($currentdata['Address']['CountryCode'] == 'US')
        {
            $this->uspsv4rates->setApiRoot("http://production.shippingapis.com/ShippingAPI.dll?API=RateV4");
        }
        else
        {
            $this->uspsv4rates->setApiRoot("http://production.shippingapis.com/ShippingAPI.dll?API=IntlRateV2");
        }
        $this->uspsv4rates->setClientID("323LOSAN5498");
        $this->uspsv4rates->setPass("268CL50QM520");
        //////////////////////
        //////////////////////
        //////////////////////
        //////////////////////

        if ($inputdelivery == 0) {
            ////Check for ground shipping items if over 1 then take new weight into account
            $newweight = 0;
            $updateweight = FALSE;
//            foreach ($currentdata['alldata']['order'] as $odk => $odv) {
//                if ($odv['quantity'] > 1 && $odv['p_freegrship'] == 1) {
//                    $newweight = $newweight + $this->_unifyweight(($odv['quantity'] - 1), $odv['p_lbs'], $odv['p_oz'], TRUE);
//                    $updateweight = TRUE;
//                } else {
//                    $newweight = $newweight + $this->_unifyweight($odv['quantity'], $odv['p_lbs'], $odv['p_oz'], TRUE);
//                }
//            }
            ///

//            if ($updateweight)
//                $weight = str_replace(',', '.', $newweight);
//            else
//                $weight = str_replace(',', '.', $currentdata['Weight']);

            //var_dump($weight);

            $weight = str_replace(',', '.', $currentdata['Weight']);
            if ((float) $weight <= 0)
                $weight = 5.0;
            $weight = explode('.', $weight);
            if (!isset($weight[1]))
                $weight[1] = '0';
            else {
                if ($weight[1] > 9)
                    $weight[1] = round(($weight[1] / 10) * 1.6);
                elseif ($weight[1] > 99)
                    $weight[1] = 15;
                elseif ($weight[1] == 0)
                    $weight[1] = 0;
                else
                    $weight[1] = ($weight[1] * 1.6);
            }


            $v4data['Pounds'] = $weight[0];
            $v4data['Ounces'] = $weight[1];
            $v4data['Width'] = 20;
            $v4data['Length'] = 12;
            $v4data['Height'] = 6;
            $v4data['Girth'] = 6;

            //$this->uspsrates->setWeight($weight[0], $weight[1]);
            //$this->uspsrates->setContainer("Flat Rate Box");
//printcool ($currentdata['Address']);
            if ($currentdata['Address']['OrigCountry'] == 'United States of America') {
                $v4data['Service'] = 'ALL';
                $v4data['ZipOrigination'] = 90250;
                $v4data['ZipDestination'] = $currentdata['Address']['PostalCode'];

                /* $this->uspsrates->setCountry("USA");
                  $this->uspsrates->setDestZip($currentdata['Address']['PostalCode']);
                  $this->uspsrates->setOrigZip("90250"); */
            } else {
                //$this->uspsrates->setCountry($currentdata['Address']['OrigCountry']);
                //$this->uspsrates->setDestZip($currentdata['Address']['PostalCode']);
                //$this->uspsrates->setOrigZip("90250");

                $v4data['MailType'] = 'Package';
                $v4data['ValueOfContents'] = $currentdata['alldata']['total']; //'100'; /**************MUST CHECK**************/
                $v4data['Country'] = $currentdata['Address']['OrigCountry'];
                $v4data['Container'] = 'RECTANGULAR';
                $v4data['Size'] = 'LARGE';
                $v4data['OriginZip'] = 90250;
            }
            //$this->uspsrates->setMachinable("true");
            $v4data['Container'] = 'RECTANGULAR';
            $v4data['Machinable'] = 'true';
            $v4data['Size'] = 'LARGE';
            /////
            // Begin BACK
            ////

            $td = array('msg_title' => 'USPS QUOTE @ ' . FlipDateMail(CurrentTime()),
                'msg_body' => printcool($v4data, true)
            );
            //GoMail($td, '365@1websolutions.net');

            $quote['back'] = $this->uspsv4rates->request($v4data);
            //$quote['back'] = objectToArray($this->uspsrates->getPrice());
            //Gomail(array('msg_title' => 'dump', 'msg_body' => printcool($quote['back'], '', TRUE)), 'mitko@rusev.me');
            if (isset($quote['back']['error'])) {
                if (isset($quote['back']['error']))
                    $backerror = $quote['back']['error']->description . '<br>';
                else
                    $backerror = '';
                $quote['back'] = false;
                $quote['backerror'] = '<strong>U.S.P.S:</strong> ' . $quote['back']['error']['description'] . '<br><br>Please check your delivery <strong>ZIP</strong>, <strong>State</strong> & <strong>Country</strong> or <a href="' . Site_url() . 'Contact/" target="_blank">contact us</a>...';
                $this->msg_data = array('msg_title' => 'USPS "BACK" error @ ' . FlipDateMail(CurrentTime()),
                    'msg_body' => $backerror . 'ClientAddress: ' . printcool($currentdata, TRUE) . '<br><br>
												 				Weight: ' . $weight[0] . '.' . $weight[1] . '<br><br>
																IP: ' . (htmlspecialchars($_SERVER['REMOTE_ADDR'])) . '<br><br>
																POST: ' . printcool($_POST, true) . '<br><br>Session: ' . printcool($this->session->userdata('fedex'), TRUE) . '<br><br>END',
                    'msg_date' => CurrentTime()
                );
                GoMail($this->msg_data, '365@1websolutions.net');
            }

            if (!isset($quote['back']['result']))
                $quote['back'] = false;
            else {
                $quote['back']['result'] = array_reverse($quote['back']['result']);
                $tmpbackres = $quote['back']['result'];

                foreach ($quote['back']['result'] as $qbkey => $qbvalue) {
                    if (!isset($this->matchservice[cleanusps($qbvalue['mailcode'])]))
                        unset($quote['back']['result'][$qbkey]);
                    else {
                        $quote['back']['result'][$qbkey]['rate'] = $quote['back']['result'][$qbkey]['rate'] + $this->config->config['shippingadd'];
                        $quote['back']['result'][$qbkey]['mailservice'] = cleanusps($quote['back']['result'][$qbkey]['mailservice']);
                        $quote['back']['result'][$qbkey]['mailcode'] = cleanusps($quote['back']['result'][$qbkey]['mailcode']);
                    }
                }


                if (count($quote['back']['result']) == 0) {
                    $this->msg_data = array('msg_title' => 'USPS No back matches @ ' . FlipDateMail(CurrentTime()),
                        'msg_body' => 'Returned: ' . printcool($tmpbackres, true) . '<br><br>
												 				ClientAddress: ' . printcool($currentdata, true) . '<br><br>
												 				Weight: ' . $weight[0] . '.' . $weight[1] . '<br><br>
																IP: ' . (htmlspecialchars($_SERVER['REMOTE_ADDR'])) . '<br><br>
																POST: ' . printcool($_POST, true) . '<br><br>Session: ' . printcool($this->session->userdata('fedex'), true) . '<br><br>END',
                        'msg_date' => CurrentTime()
                    );
                    GoMail($this->msg_data, '365@1websolutions.net');
                } else
                    $quote['to'] = $quote['back'];
            }

            ///////
            // If single item and flat rate shipping is selected, add flat rate to matched results...

            if ($currentdata['alldata']['totalitemsquantity'] == 1 && $currentdata['alldata']['shipping'] == 1 && $currentdata['Address']['OrigCountry'] == 'United States of America' && $currentdata['alldata']['groundshipping'] != 1)
                $quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail Flat Rate', 'mailcode' => 'USPS_FLAT_RATE', 'rate' => '5.95');
            ///////
            // If single item and ground shipping , match priority shipping and make price 0 (free)...

            /* if ($currentdata['alldata']['totalitemsquantity'] == 1 &&  $currentdata['alldata']['groundshipping'] == 1 && $currentdata['Address']['OrigCountry'] == 'United States of America')
              {
              //$matched = false;
              //foreach ($quote['back']['result'] as $kqb => $qbr)
              //	{
              //		if ($qbr['mailcode'] == 'USPS_PRIORITY_MAIL_SHIPPING' || $qbr['mailcode'] == 'USPS_PRIORITY_MAIL') $matched = TRUE;
              //	}


              //if ($matched)foreach ($quote['back']['result'] as $kqb => $qbr)
              //	{
              //		if ($qbr['mailcode'] == 'USPS_PRIORITY_MAIL_SHIPPING' || $qbr['mailcode'] == 'USPS_PRIORITY_MAIL') $quote['back']['result'][$kqb]['rate'] = 0;				}
              //else
              //	{
              //		$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail','mailcode' => 'USPS_PRIORITY_MAIL_FREE', 'rate' => '0');
              //	}

              array_unshift($quote['back']['result'], array('mailservice' => 'U.S.P.S. Ground Mail','mailcode' => 'USPS_GROUND_MAIL_FREE', 'rate' => '0'));

              } */
        }

        /////
        // BEGIN BOX
        ////
        if ($inputdelivery == 1 && ($currentdata['Address']['OrigCountry'] == 'United States of America')) {
            //if(isset($this->uspsrates->result)) unset($this->uspsrates->result);
            //$this->uspsrates->setWeight('2','0');
            //$this->uspsrates->setContainer("Flat Rate Box");
            //$this->uspsrates->setCountry("USA");
            //$this->uspsrates->setDestZip($currentdata['Address']['PostalCode']);
            //$this->uspsrates->setOrigZip("90250");
            //$this->uspsrates->setMachinable("true");

            $v4data['Pounds'] = 2;
            $v4data['Ounces'] = 0;
            $v4data['Service'] = 'PRIORITY';
            $v4data['ZipOrigination'] = 90250;
            $v4data['ZipDestination'] = $currentdata['Address']['PostalCode'];
            $v4data['Container'] = 'RECTANGULAR';
            $v4data['Size'] = 'LARGE';
            $v4data['Width'] = 20;
            $v4data['Length'] = 12;
            $v4data['Height'] = 6;
            $v4data['Girth'] = 6;


            $quote['box'] = $this->uspsv4rates->request($v4data); //objectToArray($this->uspsrates->getPrice());

            //if ($_SERVER['REMOTE_ADDR'] == '87.121.161.130') printcool ($quote['box']);
            if (isset($quote['box']['error'])) {
                if (isset($quote['box']['error']))
                    $boxerror = $quote['box']['error']->description . '<br>';
                else
                    $boxerror = '';
                $quote['box'] = false;
                $quote['boxerror'] = '<strong>U.S.P.S:</strong> ' . $quote['box']['error']['description'] . '<br><br>Please check your delivery <strong>ZIP</strong>, <strong>State</strong> & <strong>Country</strong> or <a href="' . Site_url() . 'Contact/" target="_blank">contact us</a>...';
                $this->msg_data = array('msg_title' => 'USPS "BOX" error @ ' . FlipDateMail(CurrentTime()),
                    'msg_body' => $boxerror . 'ClientAddress: ' . printcool($currentdata, TRUE) . '<br><br>
																	Weight: 2.0<br><br>
																	IP: ' . (htmlspecialchars($_SERVER['REMOTE_ADDR'])) . '<br><br>
																	POST: ' . printcool($_POST, TRUE) . '<br><br>Session: ' . printcool($this->session->userdata('fedex'), TRUE) . '<br><br>END',
                    'msg_date' => CurrentTime()
                );
                GoMail($this->msg_data, '365@1websolutions.net');
            }
            if (!isset($quote['box']['result']))
                $quote['box'] = false;
            else {
                $quote['box']['result'] = array_reverse($quote['box']['result']);
                $tmpboxres = $quote['box']['result'];
                foreach ($quote['box']['result'] as $qxkey => $qxvalue) {
                    if (!isset($this->matchservice[cleanusps($qxvalue['mailcode'])]))
                        unset($quote['box']['result'][$qxkey]);
                    else {
                        $quote['box']['result'][$qxkey]['rate'] = $quote['box']['result'][$qxkey]['rate'] + $this->config->config['shippingadd'];
                        $quote['box']['result'][$qxkey]['mailservice'] = cleanusps($quote['box']['result'][$qxkey]['mailservice']);
                        $quote['box']['result'][$qxkey]['mailcode'] = cleanusps($quote['box']['result'][$qxkey]['mailcode']);
                    }
                }
                if (count($quote['box']['result']) == 0) {

                    $this->msg_data = array('msg_title' => 'USPS No box matches @ ' . FlipDateMail(CurrentTime()),
                        'msg_body' => 'Returned: ' . printcool($tmpboxres, true) . '<br><br>
												 				ClientAddress: ' . printcool($currentdata, true) . '<br><br>
												 				Weight: 2.0<br><br>
																IP: ' . (htmlspecialchars($_SERVER['REMOTE_ADDR'])) . '<br><br>
																POST: ' . printcool($_POST, true) . '<br><br>Session: ' . printcool($this->session->userdata('fedex'), true) . '<br><br>END',
                        'msg_date' => CurrentTime()
                    );
                    GoMail($this->msg_data, '365@1websolutions.net');
                }
            }
        }


        if (isset($quote))
            return $quote;
    }

function _PrepareFedexInternational($client, $return = 0) {

        if ($client['Address']['CountryCode'] != 'US')
            return array('back' => array());
//printcool ($client);

        $this->load->library('fedex');
        $this->load->library('xml');

        /* $homeaddress =  array('Address' => array(
          'StreetLines' => array('4709 Campbell Dr'),
          'City' => 'Culver City',
          'StateOrProvinceCode' => 'CA',
          'PostalCode' => '90230',
          'CountryCode' => 'US')); */
        /* $homeaddress =  array('Address' => array(
          'StreetLines' => array('3325 S. Hoover St'),
          'City' => 'Los Angeles',
          'StateOrProvinceCode' => 'CA',
          'PostalCode' => '90230',
          'CountryCode' => 'US')); */
        $homeaddress = array('Address' => array(
                'StreetLines' => array('13822 Prairie Ave'),
                'City' => 'Hawthorne',
                'StateOrProvinceCode' => 'CA',
                'PostalCode' => '90250',
                'CountryCode' => 'US'));
//$freeshipgr = (int)$client['freegrship'];
//unset ($client['freegrship']);
        if (isset($client['alldata']['groundshipping'])) {
            $freeshipgr = (int) $client['alldata']['groundshipping'];
            //unset ($client['freegrship']);
        } else
            $freeshipgr = false;
        if (isset($client['Address']))
            $clientaddress['Address'] = $client['Address'];
        if (isset($client['City']))
            $clientaddress['City'] = $client['City'];
        if (isset($client['StateOrProvinceCode']))
            $clientaddress['StateOrProvinceCode'] = $client['StateOrProvinceCode'];
        if (isset($client['PostalCode']))
            $clientaddress['PostalCode'] = $client['PostalCode'];
        if (isset($client['CountryCode']))
            $clientaddress['CountryCode'] = $client['CountryCode'];


        $what = array('Weight' => array('Value' => $client['Weight'],
                'Units' => 'LB')
        );
        if ((float) $what['Weight']['Value'] <= 0)
            $what['Weight']['Value'] = 5.0;

//$what = array('Weight' => array('Value' => 5.0,
        //                               'Units' => 'LB')
        //                              );


        /*
          'Dimensions' => array('Length' => 20,
          'Width' => 20,
          'Height' => 6,
          'Units' => 'IN') */

        $td = array('msg_title' => 'FEDEX QUOTE @ ' . FlipDateMail(CurrentTime()),
            'msg_body' => printcool($what, true) . '<br><br>' . printcool($clientaddress, TRUE)
        );
        //	GoMail($td, '365@1websolutions.net');



        $shipment = array('PackagingType' => 'YOUR_PACKAGING', 'ReturnTransitAndCommit' => true);
// valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
        $now = CurrentTime();
        if ((int) $return == 1 && $clientaddress['Address']['CountryCode'] == 'US') {
            $toattempt = 1;
            $xmlto = $this->fedex->fedexgo($clientaddress, $homeaddress, $what, $shipment);

            if ((!$xmlto) || (($xmlto) && ($xmlto == 'code'))) {
                $xmlto = $this->fedex->fedexgo($clientaddress, $homeaddress, $what, $shipment);
                $toattempt++;
            }
            if ((!$xmlto) || (($xmlto) && ($xmlto == 'code'))) {
                $xmlto = $this->fedex->fedexgo($clientaddress, $homeaddress, $what, $shipment);
                $toattempt++;
            }

            if (!$xmlto) {
                $this->msg_data = array('msg_title' => 'Fedex "TO" no return @ ' . FlipDateMail(CurrentTime()),
                    'msg_body' => 'ClientAddress: ' . printcool($clientaddress, TRUE) . '<br><br>
												   Package: ' . printcool($what, TRUE) . '<br><br>
												   IP: ' . (htmlspecialchars($_SERVER['REMOTE_ADDR'])) . '<br><br>
												   POST: ' . printcool($_POST, TRUE) . '<br><br>Session: ' . printcool($this->session->userdata('fedex'), TRUE) . '<br><br>END',
                    'msg_date' => CurrentTime()
                );
                GoMail($this->msg_data, '365@1websolutions.net');

                $result['to'] = false;
            } else {
                $result['toraw'] = $this->xml->createArray($xmlto);
                //printcool ($result['toraw']);
                //if ($_SERVER['REMOTE_ADDR'] == '87.121.161.130') printcool ($result['toraw']);
                if (isset($result['toraw']['SOAP-ENV:Header']))
                    $hdrto = 'SOAP-ENV';
                elseif (isset($result['toraw']['ENV:Header']))
                    $hdrto = 'ENV';
                else
                    $hdrto = '';
                if (isset($result['toraw'][$hdrto . ':Header']['SOAP-ENV:Body']))
                    $bodyto = 'SOAP-ENV';
                elseif (isset($result['toraw'][$hdrto . ':Header']['ENV:Body']))
                    $bodyto = 'ENV';
                else
                    $bodyto = '';

                if (isset($result['toraw'][$hdrto . ':Header'][$bodyto . ':Body'][0]['RateReply'][0]['RateReplyDetails'])) {
                    foreach ($result['toraw'][$hdrto . ':Header'][$bodyto . ':Body'][0]['RateReply'][0]['RateReplyDetails'] as $tkey => $tvalue) {
                        foreach ($tvalue['RatedShipmentDetails'] as $tsdkey => $tsdvalue) {
                            if (($tsdvalue['ShipmentRateDetail'][0]['RateType'] == 'RATED_ACCOUNT') || ($tsdvalue['ShipmentRateDetail'][0]['RateType'] == 'PAYOR_ACCOUNT'))
                                $ratescalc[] = $tsdvalue['ShipmentRateDetail'][0]['TotalNetCharge'][0]['Amount'];
                        }

                        if (isset($ratescalc[1]) && ($ratescalc[1] > $ratescalc[0]))
                            $ratescalced = (float) $ratescalc[1];
                        else
                            $ratescalced = (float) $ratescalc[0];

                        $ratescalced = $ratescalced + $this->config->config['shippingadd'];

                        if (isset($tvalue['DeliveryTimestamp'])) {
                            $result['to'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['ServiceType']), 'sum' => $ratescalced, 'time' => $tvalue['DeliveryTimestamp'], 'stamp' => $tvalue['DeliveryTimestamp']);
                        } else {
                            if (isset($tvalue['CommitDetails'][0]['TransitTime']))
                                $result['to'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['ServiceType']), 'sum' => $ratescalced, 'time' => Days_Word_To_Number(CapsNClear($tvalue['CommitDetails'][0]['TransitTime'])));
                            else
                                $result['to'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['ServiceType']), 'sum' => $ratescalced);
                        }

                        unset($ratescalced);
                        unset($ratescalc);
                    }
                    $result['to'] = array_reverse($result['to']);
                    $result['totime'] = $now;
                    $result['toattempts'] = $toattempt;
                }
                elseif (isset($result['toraw'][$hdrto . ':Header'][$bodyto . ':Body'][0]['RateReply'][0]['Notifications'][0]['Message'])) {
                    $result['toerror'] = '<strong>Fedex:</strong> ' . $result['toraw'][$hdrto . ':Header'][$bodyto . ':Body'][0]['RateReply'][0]['Notifications'][0]['Message'] . '<br><br>Please check your delivery <strong>ZIP</strong>, <strong>State</strong> & <strong>Country</strong> or <a href="' . Site_url() . 'Contact/" target="_blank">contact us</a>...';
                    $this->msg_data = array('msg_title' => 'Fedex "TO" no valid services @ ' . FlipDateMail(CurrentTime()),
                        'msg_body' => 'ClientAddress: ' . printcool($clientaddress, TRUE) . '<br><br>
												   Package: ' . printcool($what, TRUE) . '<br><br>
												   IP: ' . (htmlspecialchars($_SERVER['REMOTE_ADDR'])) . '<br><br>
												   POST: ' . printcool($_POST, TRUE) . '<br><br>Session: ' . printcool($this->session->userdata('fedex'), TRUE) . '<br><br>END',
                        'msg_date' => CurrentTime()
                    );
                    GoMail($this->msg_data, '365@1websolutions.net');
                }
                unset($result['toraw']);
            }

            $whatbox = array('Weight' => array('Value' => 2.0,
                    'Units' => 'LB'));

            $boxattempt = 1;
            $xmlbox = $this->fedex->fedexgo($homeaddress, $clientaddress, $whatbox, $shipment);

            if ((!$xmlbox) || (($xmlbox) && ($xmlbox == 'code'))) {
                $xmlbox = $this->fedex->fedexgo($homeaddress, $clientaddress, $whatbox, $shipment);
                $boxattempt++;
            }
            if ((!$xmlbox) || (($xmlbox) && ($xmlbox == 'code'))) {
                $xmlbox = $this->fedex->fedexgo($homeaddress, $clientaddress, $whatbox, $shipment);
                $boxattempt++;
            }

            if (!$xmlbox) {
                $this->msg_data = array('msg_title' => 'Fedex "BOX" no return @ ' . FlipDateMail(CurrentTime()),
                    'msg_body' => 'ClientAddress: ' . printcool($clientaddress, TRUE) . '<br><br>
												   Package: ' . printcool($what, TRUE) . '<br><br>
												   IP: ' . (htmlspecialchars($_SERVER['REMOTE_ADDR'])) . '<br><br>
												   POST: ' . printcool($_POST, TRUE) . '<br><br>Session: ' . printcool($this->session->userdata('fedex'), TRUE) . '<br><br>END',
                    'msg_date' => CurrentTime()
                );
                GoMail($this->msg_data, '365@1websolutions.net');
                $result['box'] = false;
            } else {
                $result['boxraw'] = $this->xml->createArray($xmlbox);
                //if ($_SERVER['REMOTE_ADDR'] == '87.121.161.130') printcool ($result['boxraw']);
                if (isset($result['boxraw']['SOAP-ENV:Header']))
                    $hdrbox = 'SOAP-ENV';
                elseif (isset($result['boxraw']['ENV:Header']))
                    $hdrbox = 'ENV';
                else
                    $hdrbox = '';
                if (isset($result['boxraw'][$hdrbox . ':Header']['SOAP-ENV:Body']))
                    $bodybox = 'SOAP-ENV';
                elseif (isset($result['boxraw'][$hdrbox . ':Header']['ENV:Body']))
                    $bodybox = 'ENV';
                else
                    $bodybox = '';

                if (isset($result['boxraw'][$hdrbox . ':Header'][$bodybox . ':Body'][0]['RateReply'][0]['RateReplyDetails'])) {
                    foreach ($result['boxraw'][$hdrbox . ':Header'][$bodybox . ':Body'][0]['RateReply'][0]['RateReplyDetails'] as $tkey => $tvalue) {
                        foreach ($tvalue['RatedShipmentDetails'] as $tsdkey => $tsdvalue) {
                            if (($tsdvalue['ShipmentRateDetail'][0]['RateType'] == 'RATED_ACCOUNT') || ($tsdvalue['ShipmentRateDetail'][0]['RateType'] == 'PAYOR_ACCOUNT'))
                                $ratescalc[] = $tsdvalue['ShipmentRateDetail'][0]['TotalNetCharge'][0]['Amount'];
                        }

                        if (isset($ratescalc[1]) && ($ratescalc[1] > $ratescalc[0]))
                            $ratescalced = (float) $ratescalc[1];
                        else
                            $ratescalced = (float) $ratescalc[0];

                        $ratescalced = $ratescalced + $this->config->config['shippingadd'];

                        if (isset($tvalue['DeliveryTimestamp'])) {
                            $result['box'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['ServiceType']), 'sum' => $ratescalced, 'time' => $tvalue['DeliveryTimestamp'], 'stamp' => $tvalue['DeliveryTimestamp']);
                        } else {
                            if (isset($tvalue['CommitDetails'][0]['TransitTime']))
                                $result['box'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['ServiceType']), 'sum' => $ratescalced, 'time' => Days_Word_To_Number(CapsNClear($tvalue['CommitDetails'][0]['TransitTime'])));
                            else
                                $result['box'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['ServiceType']), 'sum' => $ratescalced);
                        }

                        unset($ratescalced);
                        unset($ratescalc);
                    }
                    $result['box'] = array_reverse($result['box']);
                    $result['boxtime'] = $now;
                    $result['boxattempts'] = $boxattempt;
                }
                elseif (isset($result['boxraw'][$hdrbox . ':Header'][$bodybox . ':Body'][0]['RateReply'][0]['Notifications'][0]['Message'])) {
                    $result['boxerror'] = '<strong>Fedex:</strong> ' . $result['boxraw'][$hdrbox . ':Header'][$bodybox . ':Body'][0]['RateReply'][0]['Notifications'][0]['Message'] . '<br><br>Please check your delivery <strong>ZIP</strong>, <strong>State</strong> & <strong>Country</strong> or <a href="' . Site_url() . 'Contact/" target="_blank">contact us</a>...';
                    $this->msg_data = array('msg_title' => 'Fedex "BOX" no valid services @ ' . FlipDateMail(CurrentTime()),
                        'msg_body' => 'ClientAddress: ' . printcool($clientaddress, TRUE) . '<br><br>
												   Package: ' . printcool($what, TRUE) . '<br><br>
												   IP: ' . (htmlspecialchars($_SERVER['REMOTE_ADDR'])) . '<br><br>
												   POST: ' . printcool($_POST, TRUE) . '<br><br>Session: ' . printcool($this->session->userdata('fedex'), TRUE) . '<br><br>END',
                        'msg_date' => CurrentTime()
                    );
                    GoMail($this->msg_data, '365@1websolutions.net');
                }
                unset($result['boxraw']);
            }
        }
        if ((int) $return == 0) {
            $backattempt = 1;
            $xmlback = $this->fedex->fedexgo($homeaddress, $clientaddress, $what, $shipment);


            if ((!$xmlback) && (($xmlback) && ($xmlback == 'code'))) {
                $xmlback = $this->fedex->fedexgo($homeaddress, $clientaddress, $what, $shipment);
                $backattempt++;
            }
            if ((!$xmlback) && (($xmlback) && ($xmlback == 'code'))) {
                $xmlback = $this->fedex->fedexgo($homeaddress, $clientaddress, $what, $shipment);
                $backattempt++;
            }
            if (!$xmlback) {
                $this->msg_data = array('msg_title' => 'Fedex "BACK" no return @ ' . FlipDateMail(CurrentTime()),
                    'msg_body' => 'ClientAddress: ' . printcool($clientaddress, TRUE) . '<br><br>
												   Package: ' . printcool($what, TRUE) . '<br><br>
												   IP: ' . (htmlspecialchars($_SERVER['REMOTE_ADDR'])) . '<br><br>
												   POST: ' . printcool($_POST, TRUE) . '<br><br>Session: ' . printcool($this->session->userdata('fedex'), TRUE) . '<br><br>END',
                    'msg_date' => CurrentTime()
                );
                GoMail($this->msg_data, '365@1websolutions.net');
                $result['back'] = false;
            } else {
                $result['backraw'] = $this->xml->createArray($xmlback);

                //GoMail(array('msg_title' => 'Fedex', 'msg_date' => CurrentTime(), 'msg_body' => printcool ($result['backraw'], '', true)), 'mitko@rusev.me');
                //if ($_SERVER['REMOTE_ADDR'] == '87.121.161.130') printcool ($result['backraw']);
                //printcool ($result['backraw']);
                if (isset($result['backraw']['SOAP-ENV:Header']))
                    $hdrback = 'SOAP-ENV';
                elseif (isset($result['backraw']['ENV:Header']))
                    $hdrback = 'ENV';
                else
                    $hdrback = '';
                if (isset($result['backraw'][$hdrback . ':Header']['SOAP-ENV:Body']))
                    $bodyback = 'SOAP-ENV';
                elseif (isset($result['backraw'][$hdrback . ':Header']['ENV:Body']))
                    $bodyback = 'ENV';
                else
                    $bodyback = '';

                if (isset($result['backraw'][$hdrback . ':Header'][$bodyback . ':Body'][0]['RateReply'][0]['RateReplyDetails'])) {
                    //printcool ($result['backraw'][$hdrback.':Header'][$bodyback.':Body'][0]['RateReply'][0]['RateReplyDetails']);
                    foreach ($result['backraw'][$hdrback . ':Header'][$bodyback . ':Body'][0]['RateReply'][0]['RateReplyDetails'] as $bkey => $bvalue) {
                        foreach ($bvalue['RatedShipmentDetails'] as $bsdkey => $bsdvalue) {
                            if (($bsdvalue['ShipmentRateDetail'][0]['RateType'] == 'RATED_ACCOUNT') || ($bsdvalue['ShipmentRateDetail'][0]['RateType'] == 'PAYOR_ACCOUNT'))
                                $ratescalc[] = $bsdvalue['ShipmentRateDetail'][0]['TotalNetCharge'][0]['Amount'];
                        }
                        if (isset($ratescalc[1]) && ($ratescalc[1] > $ratescalc[0]))
                            $ratescalced = (float) $ratescalc[1];
                        else
                            $ratescalced = (float) $ratescalc[0];

                        $ratescalced = $ratescalced + $this->config->config['shippingadd'];

                        if (isset($bvalue['DeliveryTimestamp'])) {
                            $result['back'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($bvalue['ServiceType']), 'sum' => $ratescalced, 'time' => $bvalue['DeliveryTimestamp'], 'stamp' => $bvalue['DeliveryTimestamp']);
                        } else {
                            if (isset($bvalue['CommitDetails'][0]['TransitTime']))
                                $result['back'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($bvalue['ServiceType']), 'sum' => $ratescalced, 'time' => Days_Word_To_Number(CapsNClear($bvalue['CommitDetails'][0]['TransitTime'])));
                            else
                                $result['back'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($bvalue['ServiceType']), 'sum' => $ratescalced);
                        }



                        unset($ratescalced);
                        unset($ratescalc);
                    }
                    $result['back'] = array_reverse($result['back']);

                    $result['backtime'] = $now;
                    $result['backattempts'] = $backattempt;
                }
                elseif (isset($result['backraw'][$hdrback . ':Header'][$bodyback . ':Body'][0]['RateReply'][0]['Notifications'][0]['Message'])) {
                    $result['backerror'] = '<strong>Fedex:</strong> ' . $result['backraw'][$hdrback . ':Header'][$bodyback . ':Body'][0]['RateReply'][0]['Notifications'][0]['Message'] . '<br><br>Please check your delivery <strong>ZIP</strong>, <strong>State</strong> & <strong>Country</strong> or <a href="' . Site_url() . 'Contact/" target="_blank">contact us</a>...';
                    $this->msg_data = array('msg_title' => 'Fedex "BACK" no valid services @ ' . FlipDateMail(CurrentTime()),
                        'msg_body' => 'ClientAddress: ' . printcool($clientaddress, TRUE) . '<br><br>
												   Package: ' . printcool($what, TRUE) . '<br><br>
												   IP: ' . (htmlspecialchars($_SERVER['REMOTE_ADDR'])) . '<br><br>
												   POST: ' . printcool($_POST, TRUE) . '<br><br>Session: ' . printcool($this->session->userdata('fedex'), TRUE) . '<br><br>END',
                        'msg_date' => CurrentTime()
                    );
                    GoMail($this->msg_data, '365@1websolutions.net');
                }
                //printcool ($result['backraw']);
                unset($result['backraw']);
            }
        }
        $servicetypes = array('FEDEX_EXPRESS_SAVER', 'EXPRESS_SAVER', 'FEDEX_GROUND', 'GROUND', 'GROUND_HOME_DELIVERY', 'INTERNATIONAL_ECONOMY', 'INTERNATIONAL_FIRST', 'INTERNATIONAL_PRIORITY', 'STANDARD_OVERNIGHT');

        if (isset($result['back']) && is_array($result['back'])) {
            //printcool ($result['back']);
            foreach ($result['back'] as $rbk => $rbv) {
                if (in_array($rbv['type'], $servicetypes)) {
                    if ((($rbv['type'] == 'FEDEX_GROUND') || ($rbv['type'] == 'GROUND') || ($rbv['type'] == 'GROUND_HOME_DELIVERY')) && ((int) $freeshipgr == 1)) {
                        $result['back'][$rbk]['osum'] = $result['back'][$rbk]['sum'];
                        $result['back'][$rbk]['sum'] = -1;
                    }
                } else {
                    unset($result['back'][$rbk]);
                }
            }
        }
        if (isset($result['to']) && is_array($result['to'])) {
            //printcool ($result['to']);
            foreach ($result['to'] as $rtk => $rtv) {
                if (in_array($rtv['type'], $servicetypes)) {
                    //if ((($rtv['type'] == 'FEDEX_GROUND') || ($rtv['type'] == 'GROUND') || ($rtv['type'] == 'GROUND_HOME_DELIVERY')) && ((int)$freeshipgr == 1)) $result['to'][$rtk]['sum'] = -1;
                } else {
                    unset($result['to'][$rtk]);
                }
            }
        }
        if (isset($result['box']) && is_array($result['box'])) {
            //printcool ($result['box']);
            foreach ($result['box'] as $rxk => $rxv) {
                if (in_array($rxv['type'], $servicetypes)) {
                    //if ((($rxv['type'] == 'FEDEX_GROUND') || ($rxv['type'] == 'GROUND') || ($rxv['type'] == 'GROUND_HOME_DELIVERY')) && ((int)$freeshipgr == 1)) $result['box'][$rxk]['sum'] = -1;
                } else {
                    unset($result['box'][$rxk]);
                }
            }
        }
        if (isset($result))
            return $result;
    }

    function _PrepareDHL($currentdata) {
        if ($currentdata['Address']['CountryCode'] != 'US') {

            //URL Production(PROD): https://xmlpi-ea.dhl.com/XMLShippingServlet
            //URL Test(TEST):    https://xmlpitest-ea.dhl.com/XMLShippingServlet
            // may need to urlencode xml portion
            $xml = '<?xml version="1.0" encoding="utf-8"?>
<req:DCTRequest xmlns:req="http://www.dhl.com">
  <GetQuote>
    <Request>
      <ServiceHeader>
        <MessageTime>' . date("Y-m-dTH:i:s") . '</MessageTime>
        <MessageReference>718688fff23f46f49d7cf9b67cd40d4e</MessageReference>
        <SiteID>xmlLAcompute</SiteID>
        <Password>O93aS_nitM</Password>
      </ServiceHeader>
    </Request>
    <From>
      <CountryCode>US</CountryCode>
      <Postalcode>90520</Postalcode>
      <City>Los Angeles</City>
    </From>
    <BkgDetails>
      <PaymentCountryCode>US</PaymentCountryCode>
      <Date>' . date("Y-m-d") . '</Date>
      <ReadyTime>PT9H</ReadyTime>
      <DimensionUnit>IN</DimensionUnit>
      <WeightUnit>LB</WeightUnit>
      <Pieces>
        <Piece>
          <PieceID>1</PieceID>
          <Height>15</Height>
          <Depth>10</Depth>
          <Width>5</Width>
          <Weight>5</Weight>
        </Piece>
      </Pieces>
      <PaymentAccountNumber>847064557</PaymentAccountNumber>
      <IsDutiable>Y</IsDutiable>
    </BkgDetails>
    <To>
      <CountryCode>' . $currentdata['Address']['CountryCode'] . '</CountryCode>
      <Postalcode>' . $currentdata['Address']['PostalCode'] . '</Postalcode>
	  <City></City>
      
    </To>
    <Dutiable>
      <DeclaredCurrency>USD</DeclaredCurrency>
      <DeclaredValue>10</DeclaredValue>
    </Dutiable>
  </GetQuote>
</req:DCTRequest>'; //
            try {
                $ch = curl_init();

                if (FALSE === $ch)
                    throw new Exception('failed to initialize');

                // set URL and other appropriate options
                curl_setopt($ch, CURLOPT_URL, 'https://xmlpitest-ea.dhl.com/XMLShippingServlet');
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

                if (curl_errno($ch)) {
                    // moving to display page to display curl errors
                    //echo curl_errno($ch) ;
                    //echo curl_error($ch);
                    return array();
                } else {
                    // grab URL and pass it to the browser
                    $res = objectToArray(simplexml_load_string(curl_exec($ch)));
                    if (FALSE === $res)
                        throw new Exception(curl_error($ch), curl_errno($ch));

                    // close curl resource, and free up system resources
                    curl_close($ch);
                    $quote = array();
                    foreach ($res['GetQuoteResponse']->BkgDetails->QtdShp as $k => $v) {//printcool($v->ProductShortName);
                        //printcool ($v->QtdSInAdCur[0]->TotalAmount);
                        if (trim($v->ProductShortName) == 'EXPRESS WORLDWIDE') {
                            $quote[0]['rate'] = (float) $v->QtdSInAdCur[0]->TotalAmount; // + $this->config->config['shippingadd'];
                            @$quote[0]['rate'] = $quote[0]['rate'] + (($quote[0]['rate'] / 100) * $this->config->config['shippingpercentadd']);
                            $quote[0]['mailservice'] = "DHL Express Worldwide";
                            $quote[0]['mailcode'] = 'DHL_EXPRESS_WORLDWIDE';
                            $quote[0]['time'] = (int) $v->TotalTransitDays;
                        }
                    }
                    return $quote;
                    /* echo '<h1>Services</h1>';
                      $q = 1;
                      foreach($res['GetQuoteResponse']->Srvs->Srv as $k => $v)
                      {
                      echo '<h2>'.$q.'</h2>';
                      printcool ($v);
                      $q++;


                      } */
                }
            } catch (Exception $e) {

                trigger_error(sprintf(
                                'Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
            }
        } else
            return array();
    }

    function SelectShippingInternational()
    {
        $type = $this->input->post("type");
        $dhl_id = $this->input->post("dhl_id");
        $dhl_rate = $this->input->post("dhl_price");
        $this->session->unset_userdata(array('rates'=> ''));
        if($type == "dhl"){
        $this->session->set_userdata(array("rates" => array(
            "DHLInternational" => array(
                "cost" => $dhl_rate,
                "additionalcost" => 0,
                "freeship" => ''
            )
        )));
        $this->session->set_userdata(array("sel_shipping" => array(
            "Description" => $dhl_id,
            "freeship" => '',
            "cost" => $dhl_rate,
            "service" => "DHLInternational"
        )));
//        print_r($this->session->userdata);
        $this->CartShow();
        }else if($type == "usps1")
        {
          $this->session->set_userdata(array("rates" => array(
            "USPSInternational" => array(
                "cost" => $dhl_rate,
                "additionalcost" => 0,
                "freeship" => ''
            )
        )));
        $this->session->set_userdata(array("sel_shipping" => array(
            "Description" => $dhl_id,
            "freeship" => '',
            "cost" => $dhl_rate,
            "service" => "USPSInternational"
        )));
//        print_r($this->session->userdata);
        $this->CartShow();
        }else if($type == "usps2")
        {
          $this->session->set_userdata(array("rates" => array(
            "USPSInternationalExpress" => array(
                "cost" => $dhl_rate,
                "additionalcost" => 0,
                "freeship" => ''
            )
        )));
        $this->session->set_userdata(array("sel_shipping" => array(
            "Description" => $dhl_id,
            "freeship" => '',
            "cost" => $dhl_rate,
            "service" => "USPSInternationalExpress"
        )));
//        print_r($this->session->userdata);
        $this->CartShow();
        }
    }

function SelectShipping ()
	{

			//////
	if ($this->config->config['cartclosed'] == 1) {
	echo '<div style="padding:10px;">';

	echo $this->config->config['cartclosemsg'];
	echo '<br><br>
	<a onclick="cartSlide()" href="#"><img src="'.Site_url().'images/close.png" style="margin-top:5px; margin-left:10px;" /></a>
	</div>';
	exit();
	}

	/////

	$sc = '';
	if (isset($_POST['sc'])) $sc = ereg_replace("[^A-Za-z0-9\"-\_\".]", "", $this->input->post('sc', TRUE));
	if ($sc != '')
		{

		////////////////////////////
		////////////
		/// CHECK TO SE IF EXISTS IN PRODUCT DB AND EBAY TXT

					$this->load->helper('directory');
					$this->load->helper('file');
					$sresponseXml = read_file($this->config->config['ebaypath'].'/shipping.txt');
					$shxml = simplexml_load_string($sresponseXml);

					$courier = $this->session->userdata('rates');
					foreach ($courier as $ck => $cv)
					{
						foreach ($shxml->ShippingServiceDetails as $k => $so)
						{
						if (($so->ShippingService == $ck) && ($sc == $ck))
							{
								$sel_shipping = array('Description' => (string)$so->Description, 'freeship' => (string)$cv['freeship'], 'cost' => (float)$cv['cost'], 'service' => (string)$ck);

								$this->session->unset_userdata(array('sel_shipping'=> ''));
								$this->session->set_userdata(array('sel_shipping' => $sel_shipping));
								//printcool ($sel_shipping);
								$this->mysmarty->assign('sel_shipping', $sc);
								if ($cv['freeship'] == 'on') $this->sel_shipping = 0;
								else $this->sel_shipping = $cv['cost'];

								//printcool ($this->sel_shipping);
							}
						}

					}
//

					$this->CartShow();
//print_r($this->session->userdata);
		//$this->CheckOut();

		}
	}
		/*
		$rates = $this->session->userdata['rates'];
		foreach ($rates as $r)
			{

			if (isset($r['type']) && $r['type'] == $sc)
				{
					 if(isset($this->session->userdata['sel_shipping_adr'])) $selrate['address'] = $this->session->userdata['sel_shipping_adr'];
					 $selrate['id'] = $r['type'];
					 $time = '';
					 if(isset($r['time']) && $r['time'] != '') $time = courier_date($r['time']);

					 $selrate['title'] = 'Fedex '.CapsNClear($r['type']).' '.$time;
					 $selrate['rate'] = $r['sum'];
				}

			if (isset($r['mailcode']) && $r['mailcode'] == $sc)
				{
				 if(isset($this->session->userdata['sel_shipping_adr'])) $selrate['address'] = $this->session->userdata['sel_shipping_adr'];
				 $selrate['id'] = $r['mailcode'];
				 $selrate['title'] = CapsNClear($r['mailcode']);
				 $selrate['rate'] = $r['rate'];
				}
			}

		if (isset($selrate))
				{
					$selrate['title'] = str_replace('Usps', 'U.S.P.S.', $selrate['title']);
					$this->session->unset_userdata(array('sel_shipping'=> ''));
					$this->session->set_userdata(array('sel_shipping' => $selrate));

					$this->sel_shipping = $selrate;
					$this->CheckOut();
					//$this->CartShow();
				}
		}
	}
*/
function RemoveShipping()
	{
		$this->session->unset_userdata(array('sel_shipping' => ''));
		$this->CartShow();
	}


function GetLoginData()
	{

		$data['usr'] = si($this->input->post('usr', TRUE));
		$data['pswd'] = si($this->input->post('pswd', TRUE));
		$data['Telephone'] = si($this->input->post('regTel', TRUE));
		$data['FirstName'] = si($this->input->post('FirstName', TRUE));
		$data['LastName'] = si($this->input->post('LastName', TRUE));
		$data['dAddress'] = si($this->input->post('dAddress', TRUE));
		$data['dCity'] = si($this->input->post('dCity', TRUE));
		$data['dState'] = si($this->input->post('dState', TRUE));



		if (isset($_POST['usr']) && isset($_POST['pswd']))
			{
				$this->load->model('Login_model');
				$userdata = $this->Login_model->CheckUser(addslashes($data['usr']), md5(md5(addslashes($data['pswd']))), TRUE);
						if ($userdata && $userdata['active'] == 1 )
							{
								$this->load->model('Start_model');
								$returndata = $this->Start_model->GetUserDetails($userdata['user_id']);
							}
			}
		if (!isset($returndata) || (isset($returndata) && !$returndata))
			{
			$returndata = $data;
			}

		//return $returndata;
		// return js lines
		echo "
		\$jq('#FirstName').val('".$returndata['FirstName']."');
		\$jq('#LastName').val('".$returndata['LastName']."');
		\$jq('#regTel').val('".$returndata['Telephone']."');
		";

		if (isset($returndata['dAddress'])) echo "\$jq('#dAddress').val('".$returndata['dAddress']."');";
		else echo "\$jq('#dAddress').val('".$returndata['Address']."');";
		if (isset($returndata['dState'])) echo "\$jq('#dState').val('".$returndata['dState']."');";
		else echo "\$jq('#dState').val('".$returndata['State']."');";
		if (isset($returndata['dCity'])) echo "\$jq('#dCity').val('".$returndata['dCity']."');";
		else echo "\$jq('#dCity').val('".$returndata['City']."');";


		echo " \$jq('#usr').val('".$data['usr']."');
		\$jq('#pswd').val('".$data['pswd']."');
		";

		if (isset($returndata['same']) && $returndata['same'] == 1)
		{
			/*echo "
				\$jq('#regSame').attr('checked', true);
			";*/
		}
		else
		{
			/*echo "
			\$jq('#regSame').attr('checked', false);
			";*/
			if (isset($returndata['Address']) && isset($returndata['City']) && isset($returndata['PostCode']) && isset($returndata['State']) && isset($returndata['Country'])) echo "
			\$jq('#Address').val('".$returndata['Address']."');
			\$jq('#City').val('".$returndata['City']."');
			\$jq('#PostCode').val('".$returndata['PostCode']."');
			\$jq('#State').val('".$returndata['State']."');
			\$jq('#Country').val('".$returndata['Country']."');

			";
			else echo "
			\$jq('#Address').val('');
			\$jq('#City').val('');
			\$jq('#PostCode').val('');
			\$jq('#State').val('');
			\$jq('#Country').val('');

			";


		}
		echo "
		\$jq('#payForm2').validate().form();	
		";

	}


function CheckOut()
{


		if (isset($this->session->userdata['sel_shipping'])) $sel_shipping = $this->session->userdata['sel_shipping'];

		if ($_POST && (isset($sel_shipping['cost']))) //&& isset($_POST['EndPay'])
		{
			$this->_ProcessCheckOut();
		}

		///
		/*if (!isset($this->session->userdata['sel_shipping']) && !isset($this->sel_shipping))
			{
				if (isset($this->session->userdata['cart'])) $this->mysmarty->assign('cartsession', $this->session->userdata['cart']);
				$this->mysmarty->assign('carttotal', $this->_CartTotal());
				//$this->mysmarty->assign('nogo', TRUE);
				$this->mysmarty->assign('step', 1);
				$returnhtml = $this->mysmarty->fetch('welcome/welcome_cart.html');
				echo $returnhtml;
				exit();
			}*/

		if (!isset($this->session->userdata['cart']) || (count($this->session->userdata['cart']) == 0) || ($this->session->userdata['cart'] == '')){ redirect(""); exit(); }

		$statecodes = array_flip(ReturnStates());
		$countrycodes = array_flip(ReturnCountries());

		$refreshedcart = $this->_RefreshCart();
		$currentdata['Weight'] = '';//$refreshedcart['weight_custom'];
		$currentdata['alldata'] = $refreshedcart;
		//$currentdata['Weight'] = 5;

		$sel_shipping['address'] = $client = $this->session->userdata('sel_shipping_adr');
		$currentdata['Address']['StateOrProvinceCode'] = '';
		$currentdata['Address']['PostalCode'] = $client['zip'];
		$currentdata['Address']['CountryCode'] = $countrycodes[$client['country']];
		$currentdata['Address']['OrigCountry'] = $client['OrigCountry'];

		$returndlv = 0;
		$customcart = 0;

		//tmp dev testing hack - must be > 0

		/// PROCESS BACK WITH FULL CART WEIGHT
				$currentdata['Weight'] = '';//$refreshedcart['weight'];

				//$this->mysmarty->assign('courier', array('cartweight' => $refreshedcart['weight']));
				/*
				$courier['quote']['back'] = array();
				$courier['uspsquote']['back']['result'] = array();
				$courier['quote'] = $this->_PrepareFedex($currentdata);
				$courier['uspsquote'] = $this->_PrepareUsps($currentdata);
				$courier['cartweight'] = $refreshedcart['weight'];

				if(!isset($courier['quote']['back']) || (isset($courier['quote']['back']) && $courier['quote']['back'] == '')) $courier['quote']['back'] = array();
				if(!isset($courier['uspsquote']['back']['result']) || (isset($courier['uspsquote']['back']['result']) && $courier['uspsquote']['back']['result'] == '')) $courier['uspsquote']['back']['result'] = array();

				$rates = array_merge($courier['quote']['back'],$courier['uspsquote']['back']['result']);
				$this->session->set_userdata(array('rates' => $rates));
				*/
				$this->mysmarty->assign('rates', $this->session->userdata['rates']);


		/*else
		{

			$sel_shipping = $this->session->userdata('sel_shipping');
			if ($sel_shipping['rate'] == 0) $price_returning = 0;
			else $price_returning = $sel_shipping['rate'];

		}*/

		//$sel_shipping['rate'] = $price_returning+$price_safebox+$price_send;

		$estdata = $this->session->userdata('clientdata');
		if (isset($estdata) && $estdata)
			{
				$client['FirstName'] = $estdata['FirstName'];
				$client['LastName'] = $estdata['LastName'];
				$client['Telephone'] = $estdata['Telephone'];
				$client['Email'] = $estdata['Email'];
			}
		$this->mysmarty->assign('regdata', $client);
		$this->mysmarty->assign('sel_shipping', $sel_shipping);
		//tmp dev testing hack
		//$customcart = 0;
		//$customcart = 0;
		$this->mysmarty->assign('step', 1);
		$this->mysmarty->assign('cartsession', $this->session->userdata['cart']);
		$this->mysmarty->assign('carttotal', $this->_CartTotal());

		if ($_POST && isset($_POST['EndPay']) && (!isset($sel_shipping['cost'])))
		{
			$this->mysmarty->assign('missingshipping', TRUE);
			$returnhtml = $this->mysmarty->fetch('welcome/welcome_cart_order.html');
		}
		else
		{
			if (isset($_POST['onlyshipping'])) $returnhtml = $this->mysmarty->fetch('welcome/welcome_cart_shipping.html');
			else $returnhtml = $this->mysmarty->fetch('welcome/welcome_cart.html');
		}

		echo $returnhtml;
		exit();

}


    function _ProcessCheckOut()
    {


        $this->load->library('form_validation');

        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|xss_clean');
        $this->form_validation->set_rules('FirstName', 'First Name', 'trim|required|min_length[3]|xss_clean');
        $this->form_validation->set_rules('LastName', 'Last Name', 'trim|required|min_length[3]|xss_clean');
        $this->form_validation->set_rules('Email', 'E-Mail', 'trim|required|valid_email|xss_clean');
        $this->form_validation->set_rules('Telephone', 'Telephone', 'trim|required|min_length[3]|xss_clean');
        $this->form_validation->set_rules('dAddress', 'Address', 'trim|required|min_length[5]|xss_clean');
        $this->form_validation->set_rules('dCity', 'City', 'trim|required|min_length[3]|xss_clean');
        $this->form_validation->set_rules('dPostCode', 'PostCode', 'trim|required|min_length[2]|xss_clean');
        $this->form_validation->set_rules('dState', 'State', 'trim|required|xss_clean');
        $this->form_validation->set_rules('dCountry', 'Country', 'trim|required|min_length[3]|xss_clean');
        if ($_POST['regSame'] == 'false')
        {
            $this->form_validation->set_rules('Address', 'Address', 'trim|required|min_length[5]|xss_clean');
            $this->form_validation->set_rules('City', 'City', 'trim|required|min_length[3]|xss_clean');
            $this->form_validation->set_rules('PostCode', 'PostCode', 'trim|required|min_length[2]|xss_clean');
            $this->form_validation->set_rules('State', 'State', 'trim|required|xss_clean');
            $this->form_validation->set_rules('Country', 'Country', 'trim|required|min_length[3]|xss_clean');
        }

        //if (isset($_POST['brand'])) 	$this->form_validation->set_rules('brand', 'Brand', 'trim|required|xss_clean');
        //if (isset($_POST['model'])) 	$this->form_validation->set_rules('model', 'Model', 'trim|required|xss_clean');
        //if (isset($_POST['probDesc'])) 	$this->form_validation->set_rules('probDesc', 'Problem Description', 'trim|required|xss_clean');

        $this->form_validation->set_rules('payproc', 'Payment Processor', 'trim|required|xss_clean');
        $this->form_validation->set_rules('comments', 'Comments', 'trim|xss_clean');
        $this->form_validation->set_rules('agree', 'Agree to terms & conditions', 'trim|callback__checkiftrue|xss_clean');
        //$this->form_validation->set_rules('res', 'Commercial Address', 'trim|xss_clean');


        if ($this->form_validation->run() == FALSE)
        {
            $resdata = array(
                'company_name' => $this->input->post('company_name', TRUE),
                'Email' => $this->input->post('Email', TRUE),
                'FirstName' => $this->input->post('FirstName', TRUE),
                'LastName' => $this->input->post('LastName', TRUE),
                'Telephone' => $this->input->post('Telephone', TRUE),
                'Address' => $this->input->post('Address', TRUE),
                'City' => $this->input->post('City', TRUE),
                'PostCode' => $this->input->post('PostCode', TRUE),
                'State' => $this->input->post('State', TRUE),
                'Country' => $this->input->post('Country', TRUE),
                'dAddress' => $this->input->post('dAddress', TRUE),
                'dCity' => $this->input->post('dCity', TRUE),
                'dPostCode' => $this->input->post('dPostCode', TRUE),
                'dState' => $this->input->post('dState', TRUE),
                'dCountry' => $this->input->post('dCountry', TRUE),
                'regSame' => $this->input->post('regSame', TRUE),
                'comments' => $this->input->post('comments', TRUE),
                'agree' => $this->input->post('agree', TRUE)
            );

            $this->mysmarty->assign('regdata', $resdata);
            /// THESE FIELDS WILL CONFLICT WITH THE FLOW BELOW UPON ENTERING THE FORM, PLUS THE FORM HAS JS VALIDATION SO THIS STATE SHOULD
            //  NEVER BE REACHED AT ALL, UNLESS THE JAVASCRIPT IS TURNED OFF. IN THAT CASE THERE IS PROBABLY A HACK ATTEMPT SO WHY BOTHER
            //  EASING THEM BY FILLING AGAIN THE DATA....
            //  *** JUST SHOW THE ERRORS


            $this->mysmarty->assign('regerrors', $this->form_validation->_error_array);

        }
        else
        {

            //BEGIN INSERTDATA

            $rcart = $this->_RefreshCart();



            $this->insertdata = array(
                'company_name' => $this->input->post('company_name', TRUE),
                'test' => (int)$this->config->config['testmode'],
                'submittime' => (int)mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y')),
                'buytype' => '1',
                'status' => serialize(array(array('status' => 1, 'comment' => '', 'notified' => 1, 'time' => CurrentTime()))),
                'fname' => si($this->form_validation->set_value('FirstName')),
                'lname' => si($this->form_validation->set_value('LastName')),
                'daddress' => si($this->form_validation->set_value('dAddress')),
                'dcity' => si($this->form_validation->set_value('dCity')),
                'dstate' => si($this->form_validation->set_value('dState')),
                'dpostcode' => si($this->form_validation->set_value('dPostCode')),
                'dcountry' => si($this->form_validation->set_value('dCountry')),
                'tel' => si($this->form_validation->set_value('Telephone')),
                'email' => si($this->form_validation->set_value('Email')),
                'comments' => si($this->form_validation->set_value('comments')),
                'order' => serialize($rcart['order']), //serialize($this->session->userdata['cart']),
                'endprice' => floater($rcart['total']),
                'totalweight' => 0,
                'time' => CurrentTime(),
            );
            $this->insertdata['eids'] = '';
            foreach ($rcart['order'] as $k => $v)
            {
                $this->insertdata['eids'] .= '|'.$k.'|';
                $relids[] = $k;
            }

            /*if ($rcart['prodcount'] == 0) $this->insertdata['subtype'] = 'r';
            elseif ($rcart['repcount'] == 0) $this->insertdata['subtype'] = 'p';
            elseif ($rcart['prodcount'] != 0 && $rcart['repcount'] != 0) $this->insertdata['subtype'] = 'pr';
            else $this->insertdata['subtype'] = 'u';
                */

            /// IF ADDRES IS SAME, REFILL OTHER ADDRESS
            if ($_POST['regSame'] == 'false') {

                $this->insertdata['address'] = si($this->form_validation->set_value('Address'));
                $this->insertdata['city'] = si($this->form_validation->set_value('City'));
                $this->insertdata['state'] = si($this->form_validation->set_value('State'));
                $this->insertdata['postcode'] = si($this->form_validation->set_value('PostCode'));
                $this->insertdata['country'] = si($this->form_validation->set_value('Country'));
                $this->insertdata['sameadr'] = 0;
            }
            else
            {
                $this->insertdata['address'] = si($this->form_validation->set_value('dAddress'));
                $this->insertdata['city'] = si($this->form_validation->set_value('dCity'));
                $this->insertdata['state'] = si($this->form_validation->set_value('dState'));
                $this->insertdata['postcode'] = si($this->form_validation->set_value('dPostCode'));
                $this->insertdata['country'] = si($this->form_validation->set_value('dCountry'));
                $this->insertdata['sameadr'] = 1;
            }

            /// IS COMMERCIAL ADDRESS
            //if ($this->session->userdata['sel_shipping_adr']['res'] == FALSE || $this->session->userdata['sel_shipping_adr']['res'] == 'false') $this->insertdata['residential'] = 1;
            //else $this->insertdata['residential'] = 0;

            /// REPAIR DATA IF ANY
            //$this->insertdata['repairdata'] = '';
            //	if (isset($_POST['brand'])) $this->insertdata['repairdata'] .= '<u>Brand</u>: '.$this->form_validation->set_value('brand');
            //	if (isset($_POST['model'])) $this->insertdata['repairdata'] .= '<br><u>Model</u>: '.$this->form_validation->set_value('model');
            //,	if (isset($_POST['probDesc'])) $this->insertdata['repairdata'] .= '<br><u>Problem</u>: '.$this->form_validation->set_value('probDesc');

            ///COURIER LOG ALL DATA

            $sel_shipping = $this->session->userdata('sel_shipping');
            if (is_array($sel_shipping) && isset($sel_shipping['freeship']) && isset($sel_shipping['cost']))
            {
                if ($sel_shipping['freeship'] != 'on') $shipping_cost = $sel_shipping['cost'];
                else $shipping_cost = 0;
            }

            $this->insertdata['courier_log'] = serialize($sel_shipping);
            /*
            $courier = $this->session->userdata('rates');
            foreach ($courier as $ck => $cv)
            {
                foreach ($shxml->ShippingServiceDetails as $k => $so)
                            {
                            if (($so->ShippingService == $ck) && ($sc == $ck))
                                {
                                    $sel_shipping = array('Description' => (string)$so->Description, 'freeship' => (string)$cv['freeship'], 'cost' => (float)$cv['cost'], 'service' => (string)$ck);

                                    $this->session->unset_userdata(array('sel_shipping'=> ''));
                                    $this->session->set_userdata(array('sel_shipping' => $sel_shipping));
                                    //printcool ($sel_shipping);
                                    $this->mysmarty->assign('sel_shipping', $sc);
                                    if ($cv['freeship'] == 'on') $this->sel_shipping = 0;
                                    else $this->sel_shipping = $cv['cost'];

                                    //printcool ($this->sel_shipping);
                                }
                            }

                        }

                        */


            $this->load->helper('directory');
            $this->load->helper('file');
            $sresponseXml = read_file($this->config->config['ebaypath'].'/shipping.txt');
            $shxml = simplexml_load_string($sresponseXml);

            $courier = $this->session->userdata('rates');


            foreach ($courier as $ck => $cv)
            {
                foreach ($shxml->ShippingServiceDetails as $k => $so)
                {

                    if ($so->ShippingService == $ck)
                    {
                        $this->insertdata['delivery'] = $so->Description.' - ';
                        if ($cv['freeship'] == 'on') $this->insertdata['delivery'] .= 'Free Shipping';
                        else $this->insertdata['delivery'] .= '$'.(float)$sel_shipping['cost'];

                    }
                }

            }

            if (!isset($this->insertdata['delivery'])) $this->insertdata['delivery'] = (float)$sel_shipping['cost'];

            $this->insertdata['endprice_delivery'] = floater($shipping_cost);
            $sum = (int)$this->insertdata['endprice_delivery'] + (int)$this->insertdata['endprice'];

            ///////////////////////
            ///$this->insertdata['endprice_delivery'] = 0;
            ///$this->insertdata['endprice'] = 1;

            ///////////////


            ////RECORD CHOSEN PAYMENT PROCESSOR
            $this->insertdata['payproc'] = (int)$this->form_validation->set_value('payproc');

            if (((int)$this->insertdata['payproc'] != '1') && ((int)$this->insertdata['payproc'] != '2')) $this->insertdata['payproc'] = 0;

            //////
            if ($this->insertdata['endprice'] > 0) {
                $this->load->helper('arithmetic');
                $this->insertdata['code'] = rand_string(50);
            }
            if (!isset($this->session->userdata['user_id']))
            {
                $password = $this->_RegisterNewCustomer('', $this->insertdata);
                $this->insertdata['generic'] = 1;
            }


            $this->db->trans_start();
            $this->Product_model->InsertOrder($this->insertdata);
            $this->insertdata['oid'] = $this->db->insert_id();
            $this->db->trans_complete();

            foreach ($relids as $k => $v)
            {
                $this->db->insert('order_listing_rel',array('orderid' => $this->insertdata['oid'], 'e_id' => $k));
            }

            $this->session->unset_userdata('cart');
            $this->session->unset_userdata('sel_shipping_adr');
            $this->session->unset_userdata('sel_shipping');
            $this->session->unset_userdata('rates');


            //SEND EMAIL TO THE CUSTOMER ABOUT ORDER CREATION
            $this->load->library('email');
            $config['mailtype'] = "html";

            $this->email->from('info@la-tronics.com', 'La-Tronics');
            $this->email->to($this->insertdata['email']);


            $this->email->subject('Order Created #'.$this->insertdata['oid']);
            ob_start();
            include "order_email.php";
            $email_content = ob_get_clean();

            $this->email->message($email_content);

            $this->email->send();

            //NOT HERE// redirect("/OrderComplete/".$this->insertdata['oid']); exit();

            if ($this->insertdata['payproc'] == '2')
            {
                $this->_PaypalFormData($this->insertdata, TRUE);
            }
            else
            {
                $this->_AuthorizeNetFormData($this->insertdata, TRUE);
            }

            exit();

            /// END OF ORDER
        }
    }




function _OrderComplete($data = '')
	{
		if ((int)$data == '') {Redirect(""); exit();}

		$this->chkorder = $this->Product_model->CheckValidOrder((int)$id, $this->clientdata, $this->clientdata['code']);
		if (!$this->chkorder) {Redirect(""); exit();}

		$this->load->model('Settings_model');
		$this->Settings_model->GetEbayListingLinesAddress();

		$this->clientdata['order'] = unserialize($this->clientdata['order']);
		$this->_TakeQuantity();

		$this->mysmarty->assign('productview', 'checkout2');

		$this->mysmarty->assign('clientdata', $this->clientdata);
		$this->mysmarty->view('welcome/welcome_main.html');
	}

/////////	OLD STUFF

///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

function SayThankYou()
{
	$this->load->model('Menus_model');
	$this->Menus_model->GetStructure();
	$this->Product_model->GetStructure();

	$this->mysmarty->assign('productview', 'formthanks');
	$this->mysmarty->view('welcome/welcome_main.html');
}

function AcceptAuthorizeNet()
{
	if (!$_POST) {
		echo 'No posted data from Authorize.Net. Please <a href="'.Site_url().'Contact">contact the system administrator.</a>';

		$this->admindata['msg_date'] = CurrentTime();
		$this->admindata['msg_title'] = 'URGENT!!! - Authorize.net Empty Post @ '.FlipDateMail($this->admindata['msg_date']);
		$this->admindata['msg_body'] = 'URGENT!!! - Authorize.net Empty Post @ '.FlipDateMail($this->admindata['msg_date']);

		GoMail ($this->admindata, $this->config->config['support_email']);

		exit();
	}
	$checkhash = strtoupper(md5('nikilpfixis365laptop'.'5TrF97Fv966'.$this->input->post('x_trans_id', TRUE).$this->input->post('x_amount', TRUE)));
	$recieveddata = array();

	foreach ($_POST as $key => $value){$recieveddata[$key] = $this->input->xss_clean($value);}

				if ($checkhash === $this->input->post('x_MD5_Hash', TRUE))
				{

						$matchammount = $this->Product_model->MatchAuthorizeNetPayment($recieveddata);
						if ((float)$matchammount == 0) {
							$matchrecieveddata = serialize($recieveddata);
							$this->admindata['msg_date'] = CurrentTime();
							$this->db->insert('payment_posts', array('ppdata' => $matchrecieveddata, 'pptime' => $this->admindata['msg_date']));
							$this->admindata['msg_title'] = 'URGENT!!! - Unmatched Authorize.Net post price @ '.FlipDateMail($this->admindata['msg_date']);
							$this->admindata['msg_body'] = $matchrecieveddata;
							GoMail ($this->admindata, $this->config->config['support_email']);
						}

					$paystatus = $status = $this->Product_model->UpdataAuthorizeNetPayment($recieveddata);

					$this->load->model('Settings_model');
					$this->Settings_model->GetEbayListingLinesAddress();

					$this->mysmarty->assign('status', $status);
					$this->mysmarty->assign('productview', 'relayresponse');
					$this->mysmarty->assign('relaydata', $recieveddata);

							$this->admindata['msg_date'] = CurrentTime();
							$this->admindata['msg_title'] = 'Authorize.Net data post @ '.FlipDateMail($this->admindata['msg_date']);
							$this->admindata['msg_body'] = $this->mysmarty->fetch('welcome/welcome_innerauthorizenet.html');
							$this->load->model('Login_model');
							$this->Login_model->InsertHistoryData($this->admindata);

							$this->admindata['msg_body'] .= '<br>-------------------<br>RAW DATA:';
							foreach($recieveddata as $rkey => $rvalue)
							{
							if ($rkey != 'x_MD5_Hash') $this->admindata['msg_body'] .= ' [ '.$rkey.' - '.$rvalue.' ]<br>';
							}

							$this->mailid = 3;
							GoMail ($this->admindata);



					if ($status == 1 && $recieveddata['x_response_code'] == 1)
					{

						$oid = (int)$recieveddata['x_invoice_num'];
						$email = $recieveddata['x_email'];
						$odata = $this->Product_model->GetOrderForResult($oid, $email);
						if ($odata)
						{
						$this->_MailClientReciept($odata, $recieveddata, $status);
						//$this->_MailClientReciept($odata);
						$this->history_data = $this->_MailAdminReciept($odata);

						$this->load->model('Login_model');
						$this->Login_model->InsertHistoryData($this->history_data);

						$this->mysmarty->assign('clientdata', $odata);

						$this->clientdata = $odata;
						$this->_TakeQuantity();
						}



					}

					$this->load->model('Menus_model');

					$this->Menus_model->GetStructure();
					$this->Product_model->GetStructure('top');

					$this->mysmarty->assign('status', $paystatus);
					$this->mysmarty->assign('forview', TRUE);

					$this->mysmarty->view('welcome/welcome_authorizenet.html');
				}
				else
				{
					$recieveddata = serialize($recieveddata);
						$this->admindata['msg_date'] = CurrentTime();
						$this->db->insert('payment_posts', array('ppdata' => $recieveddata, 'pptime' => $this->admindata['msg_date']));
						$this->admindata['msg_title'] = 'Unverified Authorize.Net data post @ '.FlipDateMail($this->admindata['msg_date']);
						$this->admindata['msg_body'] = $recieveddata;
						GoMail ($this->admindata, $this->config->config['support_email']);

					$this->mysmarty->assign('productview', 'relayresponse');
					$this->mysmarty->assign('relaydata', FALSE);
					$this->mysmarty->view('welcome/welcome_authorizenet.html');
				}
}
function AcceptPayPal($string = '')
{
				$string = str_replace('/PaymentComplete/', "", urldecode($string));
				$ppstring = explode("&", $this->input->xss_clean($string));
				if (isset($ppstring[0])) $txstring = explode("=", $ppstring[0]);
				if (isset($txstring[0]) && $txstring[0] == '?tx') {
					$txaccepted = $txstring[1];
					}
				else {	$recieveddata = urldecode($string);
						$this->admindata['msg_date'] = CurrentTime();
						$this->db->insert('payment_posts', array('ppdata' => $recieveddata, 'pptime' => $this->admindata['msg_date']));
						$this->admindata['msg_title'] = 'Error Return Paypal @ '.FlipDateMail($this->admindata['msg_date']);
						$this->admindata['msg_body'] = $recieveddata;
						GoMail ($this->admindata, $this->config->config['support_email']);
						echo 'Error returning from paypal. This information has been logged. You will be informed by email how to proceed when the administrator has reviewed the data of your payment. You should also have recieved a reciept from paypal.';
						exit();
					}
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-synch';
		$tx_token = $txaccepted;
		$auth_token = "8xjk6zoRLRdedgjpeMO0fvxvyicXfD9poQNiIcd0z2llIJHasS9kn9gNta0";
		$req .= "&tx=$tx_token&at=$auth_token";
		// post back to PayPal system to validate
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
		// If possible, securely post back to paypal using HTTPS
		// Your PHP server will need to be SSL enabled
		// $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
		if (!$fp) {
						$recieveddata = $tx;
						$this->admindata['msg_date'] = CurrentTime();
						$this->db->insert('payment_posts', array('ppdata' => $recieveddata, 'pptime' => $this->admindata['msg_date']));
						$this->admindata['msg_title'] = 'Error Repost Paypal @ '.FlipDateMail($this->admindata['msg_date']);
						$this->admindata['msg_body'] = $recieveddata;
						GoMail ($this->admindata, $this->config->config['support_email']);
						echo 'Error connecting with paypal. This payment has been logged. You will be informed by email how to proceed when the administrator has reviewed the data of your payment. You should also have recieved a reciept from paypal.';
		} else {
		fputs ($fp, $header . $req);
		// read the body data
		$res = '';
		$headerdone = false;
		while (!feof($fp)) {
		$line = fgets ($fp, 1024);
			if (strcmp($line, "\r\n") == 0) {
			// read the header
			$headerdone = true;
			}
			else if ($headerdone)
			{
			// header has been read. now read the contents
			$res .= $line;
			}
		}
		// parse the data
		$lines = explode("\n", $res);
		$keyarray = array();
		if (strcmp ($lines[0], "SUCCESS") == 0) {
		for ($i=1; $i<count($lines);$i++){
		if (strlen($lines[$i]) > 2) list($key,$val) = explode("=", $lines[$i]);
		$keyarray[urldecode($key)] = urldecode($val);
		}
		// check the payment_status is Completed
		// check that txn_id has not been previously processed
		// check that receiver_email is your Primary PayPal email
		// check that payment_amount/payment_currency are correct
		// process payment

		$this->load->model('Settings_model');
		$this->Settings_model->GetEbayListingLinesAddress();

						if ($keyarray['receiver_id'] != '2XKYXGF7TVVCJ')
						{
							$recieveddata = serialize($keyarray);
							$this->admindata['msg_date'] = CurrentTime();
							$this->db->insert('payment_posts', array('ppdata' => $recieveddata, 'pptime' => $this->admindata['msg_date']));
							$this->admindata['msg_title'] = 'Unmatched Reciever ID @ '.FlipDateMail($this->admindata['msg_date']);
							$this->admindata['msg_body'] = $recieveddata;
							GoMail ($this->admindata, $this->config->config['support_email']);
							echo 'We seem to be getting some details from paypal that we shouldn\'t be. This information has been logged. If your purchase is legitimate, you will be contacted by the adminitrator about how to proceed. You should also have recieved a reciept from paypal.';
							exit();
						}


						$matchammount = $this->Product_model->MatchPaypalPayment($keyarray);
						if ($matchammount == 0) {
							$matchkeyarray = serialize($keyarray);
							$this->admindata['msg_date'] = CurrentTime();
							$this->db->insert('payment_posts', array('ppdata' => $matchkeyarray, 'pptime' => $this->admindata['msg_date']));
							$this->admindata['msg_title'] = 'URGENT!!! - Unmatched Paypal post price @ '.FlipDateMail($this->admindata['msg_date']);
							$this->admindata['msg_body'] = $matchkeyarray;
							GoMail ($this->admindata, $this->config->config['support_email']);
						}

					$paystatus = $status = $this->Product_model->UpdataPayPalPayment($keyarray);
					$this->mysmarty->assign('status', $status);
					$this->mysmarty->assign('productview', 'paypalresponse');
					$this->mysmarty->assign('paypaldata', $keyarray);


							$this->admindata['msg_date'] = CurrentTime();
							$this->admindata['msg_title'] = 'Paypal data post @ '.FlipDateMail($this->admindata['msg_date']);
							$this->admindata['msg_body'] = $this->mysmarty->fetch('welcome/welcome_innerpaymentstatus.html');
							$this->load->model('Login_model');
							$this->Login_model->InsertHistoryData($this->admindata);
							$this->admindata['msg_body'] .= '<br>'.$string;
							$this->mailid = 4;
							GoMail ($this->admindata);

					if ($paystatus == 1)
					{

						$oid = (int)$keyarray['item_number'];
						$email = $keyarray['payer_email'];
						$odata = $this->Product_model->GetOrderForResult($oid, $email);
						if ($odata)
						{
						$this->_MailClientReciept($odata, $keyarray, $status);
						//$this->_MailClientReciept($odata);
						$this->history_data = $this->_MailAdminReciept($odata);

						$this->load->model('Login_model');
						$this->Login_model->InsertHistoryData($this->history_data);

						$this->mysmarty->assign('clientdata', $odata);

						$this->clientdata = $odata;
						$this->_TakeQuantity();
						}
						// status 1 = updated
						// status 0 = already complete
						// status -1 = not found
						// if status 1 get order details to display
						// if status 1 insert order into history
						// if statys 1 send email
						//
					}

					$this->load->model('Menus_model');

					$this->Menus_model->GetStructure();
					$this->Product_model->GetStructure('top');

					$this->mysmarty->assign('status', $paystatus);
					$this->mysmarty->assign('forview', TRUE);
					$this->mysmarty->view('welcome/welcome_main.html');
		}
		elseif (strcmp ($lines[0], "FAIL") == 0) {
					// log for manual investigation
					$recieveddata = serialize($lines);
						$this->admindata['msg_date'] = CurrentTime();
						$this->db->insert('payment_posts', array('ppdata' => $recieveddata, 'pptime' => $this->admindata['msg_date']));
						$this->admindata['msg_title'] = 'Unverfied Paypal data post @ '.FlipDateMail($this->admindata['msg_date']);
						$this->admindata['msg_body'] = $recieveddata;
						$this->admindata['msg_body'] .= '<br>'.$string;
						GoMail ($this->admindata, $this->config->config['support_email']);
					$this->mysmarty->assign('productview', 'paypalresponse');
					$this->mysmarty->assign('paypaldata', FALSE);
					$this->mysmarty->view('welcome/welcome_main.html');
		}
	}
	fclose ($fp);
}

///MAKE PAYMENT FROM "MY"
function MakePayment($id = '', $code = '', $update = '')
{
	if ((int)$id == 0) {Redirect(""); exit();}
	if ($code == '') {Redirect(""); exit();}

	$this->load->model('Menus_model');
	$this->Menus_model->GetStructure();
	$this->Product_model->GetStructure();

	$this->load->model('Myorders_model');
	$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());
	$this->mysmarty->assign('myorders', $this->Product_model->ListMyOrders($this->session->userdata['email']));
	$this->load->model('Settings_model');
	$this->Settings_model->GetEbayListingAddress();

	$this->directpay = TRUE;

	if ($update == '') {
		$this->cdata = $this->session->flashdata('clientdata');
		if ($this->cdata == '') {Redirect(""); exit();}
		if (count($this->cdata) < 2) {redirect(""); exit();}
		$this->chkorder = $this->Product_model->CheckValidOrder((int)$id, $this->cdata, $code);
		if (isset($this->cdata['generic'])) $this->mysmarty->assign('generic', TRUE);

		if ($this->chkorder['payproc'] == '2')
		{

		$this->session->keep_flashdata('clientdata');
		$this->mysmarty->assign('form', $this->_PaypalFormData($this->chkorder));
		}
		else
		{
		$this->session->keep_flashdata('clientdata');
		$this->mysmarty->assign('form', $this->_AuthorizeNetFormData($this->chkorder));
		}
	}
	else
	{
		if (isset($this->session->userdata['user_id']))
		{
			if ($update == '2') $this->Product_model->UpdatePayProcessor((int)$id, $code, '2');
			else $this->Product_model->UpdatePayProcessor((int)$id, $code, '1');

			$this->chkorder = $this->Product_model->CheckValidOrder((int)$id, '', $code, '1');

			if ($update == '2') $this->mysmarty->assign('form', $this->_PaypalFormData($this->chkorder));
			else $this->mysmarty->assign('form', $this->_AuthorizeNetFormData($this->chkorder));

		$this->mysmarty->assign('update', TRUE);
		}
		else {Redirect(""); exit();}
	}


	$this->mysmarty->assign('order', $this->chkorder);
	$this->mysmarty->assign('productview', 'makepayment');
	$this->mysmarty->view('welcome/welcome_main.html');
}

///AMEND PAYMENT FROM "MY"
function AmendmentPayment($type = '', $oidref = '', $oid = '', $code ='')
{
if ((($type != "AuthorizeNet") || ($type != "PayPal")) && ((int)$oidref == 0) && ((int)$oid == 0) && (strlen($code != 50)) && (!isset($this->session->userdata['user_id']))) { Redirect(''); exit(); }

	$this->load->model('Menus_model');
	$this->load->model('Product_model');
	$this->Menus_model->GetStructure();
	$this->Product_model->GetStructure('top');

$this->load->model('Settings_model');
$this->Settings_model->GetEbayListingLinesAddress();

	$this->load->model('Myorders_model');
	$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());
	$this->mysmarty->assign('myorders', $this->Product_model->ListMyOrders($this->session->userdata['email']));


	if ($type == "AuthorizeNet")
	{
		$this->chkorder = $this->Product_model->CheckValidAmendment((int)$oidref, (int)$oid, $code, 1);

		if ($this->chkorder)
			{
				$this->directpay = TRUE;
				$this->mysmarty->assign('form', $this->_AuthorizeNetFormData($this->chkorder));
				$this->mysmarty->assign('order', $this->chkorder);
				$this->mysmarty->assign('productview', 'makepayment');
				$this->mysmarty->view('welcome/welcome_main.html');
			}
	}
	elseif ($type == "PayPal")
	{
		$this->chkorder = $this->Product_model->CheckValidAmendment((int)$oidref, (int)$oid, $code, 2);
		if ($this->chkorder)
			{
				$this->load->model('Menus_model');
				$this->Menus_model->GetStructure();
				$this->Product_model->GetStructure();

				$this->directpay = TRUE;
				$this->mysmarty->assign('form', $this->_PaypalFormData($this->chkorder));
				$this->mysmarty->assign('order', $this->chkorder);
				$this->mysmarty->assign('productview', 'makepayment');
				$this->mysmarty->view('welcome/welcome_main.html');
			}
	}
	else
	{
		Redirect (''); exit();
	}

}


///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function My($id = '')
{
			$this->_CheckLogin();
				$this->load->model('Menus_model');
				$this->Menus_model->GetStructure();
				$this->Product_model->GetStructure();

				$this->load->model('Myorders_model');
				$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());
		    	$this->mysmarty->assign('myorders', $this->Product_model->ListMyOrders($this->session->userdata['email']));

			if ((int)$id > 0)
			{
				$this->order = $this->Product_model->GetOrder((int)$id, $this->session->userdata['email']);
				if (!$this->order) { Redirect("My"); exit;}
				$this->mysmarty->assign('ostatuses', $this->Myorders_model->GetStatuses($this->order['buytype']));

				$this->order['order'] = unserialize($this->order['order']);
				$this->mysmarty->assign('amendments', $this->Product_model->GetOrderAmendments($this->order['oid']));
				$this->mysmarty->assign('clientdata', $this->order);
				$this->mysmarty->assign('productview', 'myorder');
			}
			else
			{

	   			$this->mysmarty->assign('productview', 'myorders');
			}

			$this->mysmarty->view('welcome/welcome_main.html');

}

function _TakeQuantity()
	{
		if (is_array($this->clientdata['order'])) foreach ($this->clientdata['order'] as $qk => $qv)
			{
				$this->load->model('Myseller_model');
				$qv['oid'] = $this->clientdata['oid'];
				$this->Myseller_model->AssignBCN($qv, 2);
				//$this->_DoBCNS($qk, $qv, $this->clientdata['oid']);
			}
	}

function _DoBCNS($k, $i, $oid)
{
					$this->db->select('e_id, e_title, ebay_id, quantity, ebayquantity, e_part, e_qpart, ebsold');
					$this->db->where('e_id', $i['e_id']);
					$eb = $this->db->get('ebay');
					if ($eb->num_rows() > 0)
						{
							$res = $eb->row_array();
							$qty = $res['quantity'];
							$resoldquantity = $qty;
							$bcnsold = $res['e_part'];
							$res['quantity'] = $res['quantity'] - $i['quantity'];
							$bcncount = $this->_RealCount($res['e_part']);

							if ($bcncount > 0)
							{
								$bcns = explode(',', $res['e_part']);

								$start = 1;
								$moved = array();
								$unavailble = 0;
								while ($start <= $i['quantity'])
								{
									if (isset($bcns[$bcncount-1]))
									{
										$moved[] = trim($bcns[$bcncount-1]);
										unset($bcns[$bcncount-1]);
										$bcncount = count($bcns);
									}
									else $unavailble++;
									$start++;
								}
								$returnmove = $moved;
								$moved = implode(',', $moved);
								if ((int)$unavailble > 0) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">BCN Auto Update - </span><span style="color:red;">LISTING DOES NOT HAVE ENOUGH BCN ITEMS - "'.$unavailble.'" Unavailable for total required '.$i['qty'].'</span>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $oid,
												  'admin' => 'Auto',
												  'sev' => 1));

								$this->db->select("order");
								$this->db->where('oid', $oid);
								$q = $this->db->get('orders', 1);
								if ($q->num_rows() > 0)
								{
									$prs = $q->row_array();
									if ($prs)
									{
										if (isset($prs['order']))
										{
											$prs = unserialize($prs['order']);
											if (!is_array($prs)) unserialize($prs);
										}
										if (count($prs) > 0)
										{
											foreach ($prs as $pk => $pv)
											{
												if ($pk == $k)
												{
													$prs[$pk]['sn']	= $moved;
													$prs[$pk]['admin'] = '';
													$prs[$pk]['revs'] = 0;
												}
											}
											$this->db->update('orders', array('order' => serialize($prs)), array('oid' => (int)$oid));
										}
									}

								$bcns = implode(',', $bcns);

								$this->db->update('ebay', array('e_part' => $bcns, 'e_qpart' => $this->_RealCount($bcns), 'quantity' => $res['quantity'], 'ngen' =>  $this->_CountGhosts($bcns)), array('e_id' => (int)$res['e_id']));

								//$this->ReviseEbayDescriptionAndQuantity((int)$res['e_id']);

								$this->_logaction('Transactions', 'B', array('BCN' => $bcnsold), array('BCN' => $bcns), (int)$res['e_id'], $res['ebay_id'], $oid, $k);
								$this->_logaction('Transactions', 'B', array('BCN Count' => $this->_RealCount($bcnsold)), array('BCN Count' => $this->_RealCount($bcns)), (int)$res['e_id'], $res['ebay_id'], $oid, $k);
								$this->_logaction('Transactions', 'B', array('Order BCN' => ''), array('Order BCN' => $moved), (int)$res['e_id'], $res['ebay_id'], $oid, $k);
							}
							else
							{
							$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:red;">Cannot auto allocate BCN piece</span> from Listing to Order', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $oid,
												  'admin' => 'Auto',
												  'sev' => 1));
							}

							$this->db->update('ebay', array('quantity' => $res['quantity']), array('e_id' => (int)$res['e_id']));
							$this->_logaction('Transactions', 'Q', array('Quantity' => $resoldquantity), array('Quantity' => $res['quantity']), (int)$res['e_id'], $res['ebay_id'], $oid);
						}
						else
						{
							$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => 'Listing with eBay ItemID <span style="color:red">NOT FOUND</span> in database. Listing quantity to be manually changed.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $oid,
												  'admin' => 'Auto',
												  'sev' => 1));
						}

						if (isset($returnmove)) return $returnmove;
				}
}
function _CountGhosts($bcnstr = '')
{
	$ghosts = 0;
	$bcnstr = explode(',', $bcnstr);
	foreach ($bcnstr as $b)
	{
		if (substr(trim($b), 0, 1) == 'G') $ghosts++;
	}
	return (int)$ghosts;
}
function _RealCount($array)
{

	if ($array != '') return count(explode(',', $array));
	else return 0;
}


function _logaction($location = '', $type = 'M', $datafrom = '', $datato = '', $eid = '', $itemid = '', $transid = '', $key = '')
{

		foreach ($datato as $k => $v)
		{
			if ($v != $datafrom[$k])
			{
				if (isset($this->session->userdata['ownnames'])) $admin = $this->session->userdata['ownnames'];
				else $admin = 'Cron';

					$hmsg = array ('msg_title' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_body' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_date' => CurrentTime());

					//GoMail($hmsg, '365@1websolutions.net', $this->config->config['no_reply_email']);

				if ($k == 'Sold') $type = 'Q';
				if ($key == '') $this->db->insert('ebay_actionlog', array('atype' => $type, 'e_id' => (int)$eid, 'ebay_id' => (int)$itemid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom[$k], 'datato' => $v, 'field' => $k, 'admin' => $admin, 'trans_id' => (int)$transid, 'ctrl' => $location));
				else $this->db->insert('ebay_actionlog', array('atype' => $type, 'e_id' => (int)$eid, 'ebay_id' => (int)$itemid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom[$k], 'datato' => $v, 'field' => $k, 'admin' => $admin, 'oid' => (int)$transid, 'okey' => $key, 'ctrl' => $location));
			}
		}
}
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
function _checkiftrue($str)
	{
		if ($str == 'false')
		{
			$this->form_validation->set_message('checkiftrue', 'You must agree to the terms.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
function _MailClientReciept($data, $relay = '', $status = '')
	{
	//if ($data['buytype'] == '1') $data['order'] = unserialize($data['order']);


	if ($data['buytype'] == '3')
	{
		if (isset($data['is_special']) && $data['is_special'] == '1') $typetxt = 'special';
		else $typetxt = 'laptop';
		$this->sendata['msg_title'] = 'Your '.$typetxt.' repair form at '.$this->config->config['sitename'].' - No. '.$data['oid'].' from '.FlipDateMail(CurrentTime());
		$this->sendata['msg_body'] = '<h3>'.'Your '.$typetxt.' repair form at '.$this->config->config['sitename'].' - '.FlipDate(CurrentTime()).'</h3><br clear="all">';
	}
	else
	{
		$this->sendata['msg_title'] = 'Your order from '.$this->config->config['sitename'].' - No. '.$data['oid'].' from '.FlipDateMail(CurrentTime());
		$this->sendata['msg_body'] = '<h3>'.'Your order from '.$this->config->config['sitename'].' - '.FlipDate(CurrentTime()).'</h3><br clear="all">';
	}
	$this->senddata['msg_date'] = CurrentTime();

	$this->mysmarty->assign('clientdata', $data);
	$this->mysmarty->assign('nothanks', TRUE);
	$this->mysmarty->assign('status', $status);


	if ($data['buytype'] == '3') $this->mysmarty->assign('nocomments', TRUE);
	if ($relay != '') {

		if (isset($relay['x_invoice_num'])) $this->mysmarty->assign('relaydata', $relay);
		if (isset($relay['item_number'])) $this->mysmarty->assign('paypaldata', $relay);

		$this->sendata['msg_body'] .= $this->mysmarty->fetch('welcome/welcome_paymentstatus.html');

	}
	else $this->sendata['msg_body'] .= $this->mysmarty->fetch('welcome/welcome_products_order.html');
	GoMail ($this->sendata, $data['email']);
	GoMail ($this->sendata, 'info@1websolutions.net');

	}
function _MailAdminReciept($data)
	{
	//if ($data['buytype'] == '1') $data['order'] = unserialize($data['order']);

	if ($data['buytype'] == '3')
	{
		if (isset($data['is_special']) && $data['is_special'] == '1') $typetxt = 'special';
		else $typetxt = 'laptop';

		$this->admindata['msg_title'] = 'New '.$typetxt.' repair form - No. '.$data['oid'].' from '.FlipDateMail(CurrentTime());
		$this->admindata['msg_body'] = '<br clear="all">';
	}
	else
	{
		$this->admindata['msg_title'] = 'New order - No. '.$data['oid'].' from '.FlipDateMail(CurrentTime());
		$this->admindata['msg_body'] = '<br clear="all">';
	}
	$this->admindata['msg_date'] = CurrentTime();

	$this->mysmarty->assign('clientdata', $data);
	$this->mysmarty->assign('nothanks', TRUE);
	if ($data['buytype'] == '3') $this->mysmarty->assign('nocomments', TRUE);
	$this->admindata['msg_body'] .= $this->mysmarty->fetch('welcome/welcome_products_order.html');

	$this->mailid = 6;
	GoMail ($this->admindata);
	return $this->admindata;
	}
function _RegisterNewCustomer($type = '', $data)
	{
								$this->load->model('Login_model');
								$this->checkemail = $this->Login_model->CheckEmailExists($data['email']);
								if (!$this->checkemail)
									{
									$this->load->helper('arithmetic');
											$this->newpass = rand_string(8);
											$this->load->library('user_agent');
											$this->accessdata = array(
																 'Reg I.P. Address' => $this->input->ip_address(),
																 'Referer' => $this->agent->referrer(),
																 'User Agent' => $this->agent->agent_string()
																 );
									$this->compileddetails = array(
																	'Telephone' => $data['tel'],
																	'Mobile' => ''

																	);
									$this->reg_data = array(
														 'pass' => md5(md5($this->newpass)),
														 'fname' => $data['fname'],
														 'lname' => $data['lname'],
														 'email' => $data['email'],
														 'confirm_code' => '',
														 'reg_date' => CurrentTime(),
														 'details' => serialize($this->compileddetails),
														 'active' => '1'
														 );

									$this->userid = $this->Login_model->InsertUser($this->reg_data);

									$this->daddressdata = array (   'user_id' => $this->userid ,
																				'ua_type' => 'd',
																			   'Address' => $data['daddress'],
																			   'City' => $data['dcity'],
																			   'PostCode' => $data['dpostcode'],
																			   'State' => $data['dstate'],
																			   'Country' => $data['dcountry']
																			);

									if ($data['sameadr'] == '1')
										{
											$this->addressdata = $this->daddressdata;
											$this->addressdata['ua_type'] == 'b';
										}
									else
										{
											$this->addressdata = array (	'user_id' => $this->userid ,
																				'ua_type' => 'b',
																			   'Address' => $data['address'],
																			   'City' => $data['city'],
																			   'PostCode' => $data['postcode'],
																			   'State' => $data['state'],
																			   'Country' => $data['country']
																			);
										}

									$this->Login_model->InsertUserAddress($this->addressdata);
									$this->Login_model->InsertUserAddress($this->daddressdata);

									$this->load->helper('mailmsg');
                                    return $this->newpass;
									/*MailUserRepairRegistration($this->userid, $this->reg_data['email'] ,$this->newpass, $this->config->config['language_abbr']);
									$this->history_data = MailNewRegistrationFromPurchaseToAdmin($this->reg_data,$this->accessdata, $type);
									$this->Login_model->InsertHistoryData($this->history_data);
*/
									}

	}


	///////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////


function _CheckLogin()
		{
			if (!isset($this->session->userdata['user_id'])) {Redirect(""); exit();}

		}

function _AuthorizeNetFormData($insertdata = '', $dbladd = FALSE)
	{
		$loginID		= "5TrF97Fv966";
		$transactionKey = "567NB9r63Y6eVz76";
		//$amount 	= 1;//
		if ($dbladd) $amount 	= sprintf("%.2f", ((float)$insertdata['endprice']+(float)$insertdata['endprice_delivery']));
 		else $amount 		= sprintf("%.2f", (float)$insertdata['endprice']);
		////////
		//$amount = '1.00';
		///////

		//if (($_SERVER['REMOTE_ADDR'] == '93.152.154.46') || ($_SERVER['REMOTE_ADDR'] == '87.121.99.103')) $amount = 1.00;

		$description = "Purchase order ".(int)sprintf("%06sf", $insertdata['oid']);


		$label 			= "Proceed To Payment";
		$testMode		= "false";
		// By default, this sample code is designed to post to our test server for
		// developer accounts: https://test.authorize.net/gateway/transact.dll
		// for real accounts (even in test mode), please make sure that you are
		// posting to: https://secure.authorize.net/gateway/transact.dll

		//$url			= "https://test.authorize.net/gateway/transact.dll";


		$url			= "https://secure.authorize.net/gateway/transact.dll";
		//$url			= "https://developer.authorize.net/param_dump.asp";

		$invoice	= (int)$insertdata['oid'];
		// a sequence number is randomly generated
		$sequence	= rand(1, 1000);
		// a timestamp is generated
		$timeStamp	= time ();
		if( phpversion() >= '5.1.2' )
		{	$fingerprint = hash_hmac("md5", $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey); }
		else
		{ $fingerprint = bin2hex(mhash(MHASH_MD5, $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey)); }


		$states = ReturnStates();
		foreach ($states as $key => $value)
		{
		if ($insertdata['state'] == 'Non US') $insertdata['state'] = '';
		else { if ($insertdata['state'] == $value) $insertdata['state'] = $key; }

		if ($insertdata['dstate'] == 'Non US') $insertdata['dstate'] = '';
		else { if ($insertdata['dstate'] == $value) $insertdata['dstate'] = $key; }
		}

		$return = "
		
		<script language='javascript'>
				setTimeout('document.payform.submit()', 3000);				
				</script>";


		if (!isset($this->directpay)) $return .= "				
				<Br><br>
				Redirecting to payment in 3 seconds....
				<br><br>
				<img src='/images/loader.gif'>";
		else $return .= 'Loading...';
		$return .= "
		<FORM name='payform' method='post' action='$url' >
		<INPUT type='hidden' name='x_relay_url' value='http://www.la-tronics.com/PaymentResult'>
		<INPUT type='hidden' name='x_relay_response' value='TRUE'>
		<INPUT type='hidden' name='x_login' value='$loginID' />
		<INPUT type='hidden' name='x_amount' value='$amount' />
		<INPUT type='hidden' name='x_description' value='$description' />
		<INPUT type='hidden' name='x_invoice_num' value='$invoice' />
		<INPUT type='hidden' name='x_fp_sequence' value='$sequence' />
		<INPUT type='hidden' name='x_fp_timestamp' value='$timeStamp' />
		<INPUT type='hidden' name='x_fp_hash' value='$fingerprint' />
		<INPUT type='hidden' name='x_test_request' value='$testMode' />
		<INPUT type='hidden' name='x_email' value='".$insertdata['email']."' />
		<INPUT type='hidden' name='x_phone' value='".$insertdata['tel']."' />
		<INPUT type='hidden' name='x_first_name' value='".$insertdata['fname']."' />
		<INPUT type='hidden' name='x_last_name' value='".$insertdata['lname']."' />
		<INPUT type='hidden' name='x_address' value='".$insertdata['address']."' />
		<INPUT type='hidden' name='x_city' value='".$insertdata['city']."' />
		<INPUT type='hidden' name='x_state' value='".$insertdata['state']."' />
		<INPUT type='hidden' name='x_zip' value='".$insertdata['postcode']."' />
		<INPUT type='hidden' name='x_country' value='".$insertdata['country']."' />		
		<INPUT type='hidden' name='x_ship_to_first_name' value='".$insertdata['fname']."' />
		<INPUT type='hidden' name='x_ship_to_last_name' value='".$insertdata['lname']."' />
		<INPUT type='hidden' name='x_ship_to_address' value='".$insertdata['daddress']."' />
		<INPUT type='hidden' name='x_ship_to_city' value='".$insertdata['dcity']."' />
		<INPUT type='hidden' name='x_ship_to_state' value='".$insertdata['dstate']."' />
		<INPUT type='hidden' name='x_ship_to_zip' value='".$insertdata['dpostcode']."' />
		<INPUT type='hidden' name='x_ship_to_country' value='".$insertdata['dcountry']."' />		
		<INPUT type='hidden' name='x_show_form' value='PAYMENT_FORM' />
		<input type='submit' value='$label' style='display:none;' />
		</FORM>";
		echo $return;
	}

function _PaypalFormData($insertdata = '', $dbladd = FALSE)
	{

		$states = ReturnStates();
		foreach ($states as $key => $value)
		{
		if ($insertdata['state'] == 'Non US') $insertdata['state'] = '';
		else { if ($insertdata['state'] == $value) $insertdata['state'] = $key; }
		}
		//<input type="hidden" name="business" value="RH4XWM4WKM7LW">
		//<input type="hidden" name="business" value="mr.ree_1255446008_biz@gmail.com">
		//<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
		$return = '
		<script language="javascript">
				setTimeout("document.payform.submit()", 3000);				
				</script>';

		if (!isset($this->directpay)) $return .= '
				<Br><br>
				Redirecting to payment in 3 seconds....
				<br><br>
				<img src="/images/loader.gif">';
		else $return .= 'Loading...';

		$return .= '
		<form name="payform" action="https://www.paypal.com/cgi-bin/webscr" method="post">
		 <input type="hidden" name="business" value="2XKYXGF7TVVCJ">
		  <input type="hidden" name="cmd" value="_xclick">';

	  	if ((int)$insertdata['buytype'] == '1') $return .= '<input type="hidden" name="item_name" value="Purchase order '.(int)sprintf("%06sf", $insertdata['oid']).'">';
		elseif ((int)$insertdata['buytype'] == '3') $return .= '<input type="hidden" name="item_name" value="Laptop repair order '.(int)sprintf("%06sf", $insertdata['oid']).'">';
		elseif ((int)$insertdata['buytype'] == '5') $return .= '<input type="hidden" name="item_name" value="Amendment order '.(int)sprintf("%06sf", $insertdata['oid']).'">';
		else exit();


		if ($dbladd) $amount = sprintf("%.2f", ((float)$insertdata['endprice']+(float)$insertdata['endprice_delivery']));
		else $amount = sprintf("%.2f", (float)$insertdata['endprice']);


		////////
		//$amount = 1;
		///////
		$return .='<input type="hidden" name="item_number" value="'.(int)$insertdata['oid'].'" />
		<input type="hidden" name="amount" value="'.$amount.'">
		<input type="hidden" name="currency_code" value="USD">
		<input type="hidden" name="first_name" value="'.$insertdata['fname'].'" />
		<input type="hidden" name="last_name" value="'.$insertdata['lname'].'" />
		<input type="hidden" name="address1" value="'.$insertdata['address'].'" />
		<input type="hidden" name="city" value="'.$insertdata['city'].'" />
		<input type="hidden" name="state" value="'.$insertdata['state'].'" />
		<input type="hidden" name="zip" value="'.$insertdata['postcode'].'" />
		<input type="hidden" name="country" value="'.$insertdata['country'].'" />				
		  <input type="image" name="submit" style="display:none;" border="0" src="https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" alt="PayPal - The safer, easier way to pay online"><img alt="" border="0" width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" >
</form>';
		echo $return;



	}

///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

function _PrepareFedex($client, $return = 0)
{

$this->load->library('fedex');
$this->load->library('xml');

/*$homeaddress =  array('Address' => array(
                       					'StreetLines' => array('4709 Campbell Dr'),
                       					'City' => 'Culver City',
                       					'StateOrProvinceCode' => 'CA',
                      					'PostalCode' => '90230',
                       					'CountryCode' => 'US')); */
/*$homeaddress =  array('Address' => array(
                       					'StreetLines' => array('3325 S. Hoover St'),
                       					'City' => 'Los Angeles',
                       					'StateOrProvinceCode' => 'CA',
                      					'PostalCode' => '90230',
                       					'CountryCode' => 'US')); */

$homeaddress =  array('Address' => array(
                       					'StreetLines' => array('13822 Prairie Ave'),
                       					'City' => 'Hawthorne',
                       					'StateOrProvinceCode' => 'CA',
                      					'PostalCode' => '90250',
                       					'CountryCode' => 'US'));
//$freeshipgr = (int)$client['freegrship'];
//unset ($client['freegrship']);
if (isset($client['freegrship'])) unset ($client['freegrship']);

if (isset($client['Address'])) $clientaddress['Address'] = $client['Address'];
if (isset($client['City'])) $clientaddress['City'] = $client['City'];
if (isset($client['StateOrProvinceCode'])) $clientaddress['StateOrProvinceCode'] = $client['StateOrProvinceCode'];
if (isset($client['PostalCode'])) $clientaddress['PostalCode'] = $client['PostalCode'];
if (isset($client['CountryCode'])) $clientaddress['CountryCode'] = $client['CountryCode'];


$what = array('Weight' => array('Value' => $client['Weight'],
                                'Units' => 'LB')
                               );
/*
 'Dimensions' => array('Length' => 20,
                                                      'Width' => 20,
                                                      'Height' => 6,
                                                      'Units' => 'IN')*/

$shipment = array('PackagingType' => 'YOUR_PACKAGING', 'ReturnTransitAndCommit' => true);

// valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...


$now = CurrentTime();
if ((int)$return == 1 && $clientaddress['Address']['CountryCode'] == 'US')
{
	$toattempt = 1;
	$xmlto = $this->fedex->fedexgo($clientaddress, $homeaddress, $what, $shipment);

	if ((!$xmlto) || (($xmlto) && ($xmlto == 'code'))) {$xmlto = $this->fedex->fedexgo($clientaddress, $homeaddress, $what, $shipment); $toattempt++;}
	if ((!$xmlto) || (($xmlto) && ($xmlto == 'code'))) {$xmlto = $this->fedex->fedexgo($clientaddress, $homeaddress, $what, $shipment);	$toattempt++;}

	if (!$xmlto)
		{
			$this->msg_data = array ('msg_title' => 'Fedex "TO" no return @ '.FlipDateMail(CurrentTime()),
									 'msg_body' => 'ClientAddress: '.serialize($clientaddress).'<br><br>
												   Package: '.serialize($what).'<br><br>
												   IP: '.(htmlspecialchars($_SERVER['REMOTE_ADDR'])).'<br><br>
												   POST: '.serialize($_POST).'<br><br>Session: '.serialize($this->session->userdata('fedex')).'<br><br>END',
									 'msg_date' => CurrentTime()
									);
			GoMail($this->msg_data, 'errors@1websolutions.net');

		$result['to']= false;
 		}
	else
		{
			$result['toraw'] = $this->xml->createArray($xmlto);
			//if ($_SERVER['REMOTE_ADDR'] == '87.121.161.130') printcool ($result['toraw']);
			if (isset($result['toraw']['soapenv:Header'])) $hdrto = 'soapenv';
			elseif (isset($result['toraw']['env:Header'])) $hdrto = 'env';
			else $hdrto = '';
			if (isset($result['toraw'][$hdrto.':Header']['soapenv:Body'])) $bodyto = 'soapenv';
			elseif (isset($result['toraw'][$hdrto.':Header']['env:Body'])) $bodyto = 'env';
			else $bodyto = '';

			if (isset($result['toraw'][$hdrto.':Header'][$bodyto.':Body'][0]['v7:RateReply'][0]['v7:RateReplyDetails']))
			{
					foreach ($result['toraw'][$hdrto.':Header'][$bodyto.':Body'][0]['v7:RateReply'][0]['v7:RateReplyDetails'] as $tkey => $tvalue)
					{
						foreach ($tvalue['v7:RatedShipmentDetails'] as $tsdkey => $tsdvalue)
						{
						if (($tsdvalue['v7:ShipmentRateDetail'][0]['v7:RateType'] == 'RATED_ACCOUNT') || ($tsdvalue['v7:ShipmentRateDetail'][0]['v7:RateType'] == 'PAYOR_ACCOUNT')) $ratescalc[] = $tsdvalue['v7:ShipmentRateDetail'][0]['v7:TotalNetCharge'][0]['v7:Amount'];
						}

						if (isset($ratescalc[1]) && ($ratescalc[1] > $ratescalc[0])) $ratescalced = (float)$ratescalc[1];
						else $ratescalced = (float)$ratescalc[0];

						$ratescalced = $ratescalced + $this->config->config['shippingadd'];

						if (isset($tvalue['v7:DeliveryTimestamp']))
								{
								$result['to'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['v7:ServiceType']), 'sum' => $ratescalced, 'time' => $tvalue['v7:DeliveryTimestamp'], 'stamp' => $tvalue['v7:DeliveryTimestamp']);
								}
								else
								{
								if (isset($tvalue['v7:CommitDetails'][0]['v7:TransitTime'])) $result['to'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['v7:ServiceType']), 'sum' => $ratescalced, 'time' => Days_Word_To_Number(CapsNClear($tvalue['v7:CommitDetails'][0]['v7:TransitTime'])));
								else $result['to'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['v7:ServiceType']), 'sum' => $ratescalced);
								}

						unset ($ratescalced);
						unset ($ratescalc);
					}
				$result['to'] = array_reverse($result['to']);
				$result['totime'] = $now;
				$result['toattempts'] = $toattempt;
			}
			elseif (isset($result['toraw'][$hdrto.':Header'][$bodyto.':Body'][0]['v7:RateReply'][0]['v7:Notifications'][0]['v7:Message']))
			{
				$result['toerror'] = '<strong>Fedex:</strong> '.$result['toraw'][$hdrto.':Header'][$bodyto.':Body'][0]['v7:RateReply'][0]['v7:Notifications'][0]['v7:Message'].'<br><br>Please check your delivery <strong>ZIP</strong>, <strong>State</strong> & <strong>Country</strong> or <a href="'.Site_url().'Contact/" target="_blank">contact us</a>...';
					$this->msg_data = array ('msg_title' => 'Fedex "TO" no valid services @ '.FlipDateMail(CurrentTime()),
										 'msg_body' => 'ClientAddress: '.serialize($clientaddress).'<br><br>
										                Package: '.serialize($what).'<br><br>
														IP: '.(htmlspecialchars($_SERVER['REMOTE_ADDR'])).'<br><br>
														POST: '.serialize($_POST).'<br><br>
														Session: '.serialize($this->session->userdata('fedex')).'<br><br>END',
										 'msg_date' => CurrentTime()
														);
									GoMail($this->msg_data, 'errors@1websolutions.net');
		    }
		unset($result['toraw']);

		}

	$whatbox = array('Weight' => array('Value' => 2.0,
                                   'Units' => 'LB'));

	$boxattempt = 1;
	$xmlbox = $this->fedex->fedexgo($homeaddress, $clientaddress, $whatbox, $shipment);

	if ((!$xmlbox) || (($xmlbox) && ($xmlbox == 'code'))) {$xmlbox = $this->fedex->fedexgo($homeaddress, $clientaddress, $whatbox, $shipment); $boxattempt++;}
	if ((!$xmlbox) || (($xmlbox) && ($xmlbox == 'code'))) {$xmlbox = $this->fedex->fedexgo($homeaddress, $clientaddress, $whatbox, $shipment); $boxattempt++;}

	if (!$xmlbox)
		{
			$this->msg_data = array ('msg_title' => 'Fedex "BOX" no return @ '.FlipDateMail(CurrentTime()),
			 						 'msg_body' => 'ClientAddress: '.serialize($clientaddress).'<br><br>
									  				Package: '.serialize($what).'<br><br>
													IP: '.(htmlspecialchars($_SERVER['REMOTE_ADDR'])).'<br><br>
													POST: '.serialize($_POST).'<br><br>Session: '.serialize($this->session->userdata('fedex')).'<br><br>END',
									 'msg_date' => CurrentTime()
									);
			GoMail($this->msg_data, 'errors@1websolutions.net');
							$result['box']= false;

		}
	else
		{
			$result['boxraw'] = $this->xml->createArray($xmlbox);
			//if ($_SERVER['REMOTE_ADDR'] == '87.121.161.130') printcool ($result['boxraw']);
			if (isset($result['boxraw']['soapenv:Header'])) $hdrbox = 'soapenv';
			elseif (isset($result['boxraw']['env:Header'])) $hdrbox = 'env';
			else $hdrbox = '';
			if (isset($result['boxraw'][$hdrbox.':Header']['soapenv:Body'])) $bodybox = 'soapenv';
			elseif (isset($result['boxraw'][$hdrbox.':Header']['env:Body'])) $bodybox = 'env';
			else $bodybox = '';

			if (isset($result['boxraw'][$hdrbox.':Header'][$bodybox.':Body'][0]['v7:RateReply'][0]['v7:RateReplyDetails']))
				{
					foreach ($result['boxraw'][$hdrbox.':Header'][$bodybox.':Body'][0]['v7:RateReply'][0]['v7:RateReplyDetails'] as $tkey => $tvalue)
						{
							foreach ($tvalue['v7:RatedShipmentDetails'] as $tsdkey => $tsdvalue)
								{
								if (($tsdvalue['v7:ShipmentRateDetail'][0]['v7:RateType'] == 'RATED_ACCOUNT') || ($tsdvalue['v7:ShipmentRateDetail'][0]['v7:RateType'] == 'PAYOR_ACCOUNT')) $ratescalc[] = $tsdvalue['v7:ShipmentRateDetail'][0]['v7:TotalNetCharge'][0]['v7:Amount'];
								}

						if (isset($ratescalc[1]) && ($ratescalc[1] > $ratescalc[0])) $ratescalced = (float)$ratescalc[1];
						else $ratescalced = (float)$ratescalc[0];

						$ratescalced = $ratescalced + $this->config->config['shippingadd'];

						if (isset($tvalue['v7:DeliveryTimestamp']))
								{
								$result['box'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['v7:ServiceType']), 'sum' => $ratescalced, 'time' => $tvalue['v7:DeliveryTimestamp'], 'stamp' => $tvalue['v7:DeliveryTimestamp']);
								}
								else
								{
								if (isset($tvalue['v7:CommitDetails'][0]['v7:TransitTime'])) $result['box'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['v7:ServiceType']), 'sum' => $ratescalced, 'time' => Days_Word_To_Number(CapsNClear($tvalue['v7:CommitDetails'][0]['v7:TransitTime'])));
								else $result['box'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($tvalue['v7:ServiceType']), 'sum' => $ratescalced);
								}

						unset ($ratescalced);
						unset ($ratescalc);
						}
				$result['box'] = array_reverse($result['box']);
				$result['boxtime'] = $now;
				$result['boxattempts'] = $boxattempt;
				}
			elseif (isset($result['boxraw'][$hdrbox.':Header'][$bodybox.':Body'][0]['v7:RateReply'][0]['v7:Notifications'][0]['v7:Message']))
				{
				 $result['boxerror'] = '<strong>Fedex:</strong> '.$result['boxraw'][$hdrbox.':Header'][$bodybox.':Body'][0]['v7:RateReply'][0]['v7:Notifications'][0]['v7:Message'].'<br><br>Please check your delivery <strong>ZIP</strong>, <strong>State</strong> & <strong>Country</strong> or <a href="'.Site_url().'Contact/" target="_blank">contact us</a>...';
				 	$this->msg_data = array ('msg_title' => 'Fedex "BOX" no valid services @ '.FlipDateMail(CurrentTime()),
											 'msg_body' => 'ClientAddress: '.serialize($clientaddress).'<br><br>
											 				Package: '.serialize($what).'<br><br>
															IP: '.(htmlspecialchars($_SERVER['REMOTE_ADDR'])).'<br><br>
															POST: '.serialize($_POST).'<br><br>Session: '.serialize($this->session->userdata('fedex')).'<br><br>END',
											 'msg_date' => CurrentTime()
											);
									GoMail($this->msg_data, 'errors@1websolutions.net');

				}
							unset($result['boxraw']);

		}
}
if ((int)$return == 0) {
	$backattempt = 1;
	$xmlback = $this->fedex->fedexgo($homeaddress, $clientaddress, $what, $shipment);

	if ((!$xmlback) && (($xmlback) && ($xmlback == 'code'))) {$xmlback = $this->fedex->fedexgo($homeaddress, $clientaddress, $what, $shipment); $backattempt++;}
	if ((!$xmlback) && (($xmlback) && ($xmlback == 'code'))) {$xmlback = $this->fedex->fedexgo($homeaddress, $clientaddress, $what, $shipment); $backattempt++;}

	if (!$xmlback)
		{
			$this->msg_data = array ('msg_title' => 'Fedex "BACK" no return @ '.FlipDateMail(CurrentTime()),
													'msg_body' => 'ClientAddress: '.serialize($clientaddress).'<br><br>
																   Package: '.serialize($what).'<br><br>
																   IP: '.(htmlspecialchars($_SERVER['REMOTE_ADDR'])).'<br><br>
																   POST: '.serialize($_POST).'<br><br>Session: '.serialize($this->session->userdata('fedex')).'<br><br>END',
													'msg_date' => CurrentTime()
									);
										GoMail($this->msg_data, 'errors@1websolutions.net');
			$result['back'] = false;
		}
	else
		{
			$result['backraw'] = $this->xml->createArray($xmlback);

			//if ($_SERVER['REMOTE_ADDR'] == '87.121.161.130') printcool ($result['backraw']);
			//printcool ($result['backraw']);

			if (isset($result['backraw']['soapenv:Header'])) $hdrback = 'soapenv';
			elseif (isset($result['backraw']['env:Header'])) $hdrback = 'env';
			else $hdrback = '';
			if (isset($result['backraw'][$hdrback.':Header']['soapenv:Body'])) $bodyback = 'soapenv';
			elseif (isset($result['backraw'][$hdrback.':Header']['env:Body'])) $bodyback = 'env';
			else $bodyback = '';

			if (isset($result['backraw'][$hdrback.':Header'][$bodyback.':Body'][0]['v7:RateReply'][0]['v7:RateReplyDetails']))
				{
					//printcool ($result['backraw'][$hdrback.':Header'][$bodyback.':Body'][0]['v7:RateReply'][0]['v7:RateReplyDetails']);
					foreach ($result['backraw'][$hdrback.':Header'][$bodyback.':Body'][0]['v7:RateReply'][0]['v7:RateReplyDetails'] as $bkey => $bvalue)
						{
							foreach ($bvalue['v7:RatedShipmentDetails'] as $bsdkey => $bsdvalue)
								{
								if (($bsdvalue['v7:ShipmentRateDetail'][0]['v7:RateType'] == 'RATED_ACCOUNT') || ($bsdvalue['v7:ShipmentRateDetail'][0]['v7:RateType'] == 'PAYOR_ACCOUNT')) $ratescalc[] = $bsdvalue['v7:ShipmentRateDetail'][0]['v7:TotalNetCharge'][0]['v7:Amount'];
								}
						if (isset($ratescalc[1]) && ($ratescalc[1] > $ratescalc[0])) $ratescalced = (float)$ratescalc[1];
						else $ratescalced = (float)$ratescalc[0];

						$ratescalced = $ratescalced + $this->config->config['shippingadd'];

						if (isset($bvalue['v7:DeliveryTimestamp']))
								{
								$result['back'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($bvalue['v7:ServiceType']), 'sum' => $ratescalced, 'time' => $bvalue['v7:DeliveryTimestamp'], 'stamp' => $bvalue['v7:DeliveryTimestamp']);
								}
								else
								{
								if (isset($bvalue['v7:CommitDetails'][0]['v7:TransitTime'])) $result['back'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($bvalue['v7:ServiceType']), 'sum' => $ratescalced, 'time' => Days_Word_To_Number(CapsNClear($bvalue['v7:CommitDetails'][0]['v7:TransitTime'])));
								else $result['back'][] = array('courier' => 'fedex', 'type' => CleanFedexTxt($bvalue['v7:ServiceType']), 'sum' => $ratescalced);
								}



						unset ($ratescalced);
						unset ($ratescalc);
						}
				$result['back'] = array_reverse($result['back']);
				$result['backtime'] = $now;
				$result['backattempts'] = $backattempt;
				}
				elseif (isset($result['backraw'][$hdrback.':Header'][$bodyback.':Body'][0]['v7:RateReply'][0]['v7:Notifications'][0]['v7:Message']))
				{
					$result['backerror'] = '<strong>Fedex:</strong> '.$result['backraw'][$hdrback.':Header'][$bodyback.':Body'][0]['v7:RateReply'][0]['v7:Notifications'][0]['v7:Message'].'<br><br>Please check your delivery <strong>ZIP</strong>, <strong>State</strong> & <strong>Country</strong> or <a href="'.Site_url().'Contact/" target="_blank">contact us</a>...';
						$this->msg_data = array ('msg_title' => 'Fedex "BACK" no valid services @ '.FlipDateMail(CurrentTime()),
												 'msg_body' => 'ClientAddress: '.serialize($clientaddress).'<br><br>
												 				Package: '.serialize($what).'<br><br>
																IP: '.(htmlspecialchars($_SERVER['REMOTE_ADDR'])).'<br><br>
																POST: '.serialize($_POST).'<br><br>Session: '.serialize($this->session->userdata('fedex')).'<br><br>END',
												'msg_date' => CurrentTime()
												);
									GoMail($this->msg_data, 'errors@1websolutions.net');
				}
				//printcool ($result['backraw']);
		unset($result['backraw']);

		}
}

				$servicetypes = array('FEDEX_EXPRESS_SAVER', 'EXPRESS_SAVER', 'FEDEX_GROUND','GROUND', 'GROUND_HOME_DELIVERY', 'INTERNATIONAL_ECONOMY', 'INTERNATIONAL_FIRST', 'INTERNATIONAL_PRIORITY', 'STANDARD_OVERNIGHT');

				if (isset($result['back']) && is_array($result['back'])) {
					//printcool ($result['back']);
					foreach($result['back'] as $rbk => $rbv)
						{
							if (in_array($rbv['type'],$servicetypes))
							{
								//if ((($rbv['type'] == 'FEDEX_GROUND') || ($rbv['type'] == 'GROUND') || ($rbv['type'] == 'GROUND_HOME_DELIVERY')) && ($currentdata['alldata']['totalitemsquantity'] == 1 && $currentdata['alldata']['groundshipping'] == 1)) $result['back'][$rbk]['sum'] = -1;
							}
							else {
								unset($result['back'][$rbk]);
								}
						}

				}
				if (isset($result['to']) && is_array($result['to'])) {
					//printcool ($result['to']);
					foreach($result['to'] as $rtk => $rtv)
						{
							if (in_array($rtv['type'],$servicetypes))
							{
								//if ((($rtv['type'] == 'FEDEX_GROUND') || ($rtv['type'] == 'GROUND') || ($rtv['type'] == 'GROUND_HOME_DELIVERY')) && ((int)$freeshipgr == 1)) $result['to'][$rtk]['sum'] = -1;
							}
							else {
								unset($result['to'][$rtk]);
								}
						}

				}
				if (isset($result['box']) && is_array($result['box'])) {
					//printcool ($result['box']);
					foreach($result['box'] as $rxk => $rxv)
						{
							if (in_array($rxv['type'],$servicetypes))
							{
								//if ((($rxv['type'] == 'FEDEX_GROUND') || ($rxv['type'] == 'GROUND') || ($rxv['type'] == 'GROUND_HOME_DELIVERY')) && ((int)$freeshipgr == 1)) $result['box'][$rxk]['sum'] = -1;
							}
							else {
								unset($result['box'][$rxk]);
								}
						}

				}

if (isset($result)) return $result;

}

function _PrepareUsps($currentdata, $inputdelivery = 0)
	{
		/// Check if Domestic and have single item flat rate and NO free ground shipping. Remove Priority mail from USABLEs list so as to not have have 2 when the result is apended later on in the flow...

		if (($currentdata['alldata']['totalitemsquantity'] == 1) && ($currentdata['alldata']['shipping'] == 1) && ($currentdata['Address']['OrigCountry'] == 'United States of America') && ($currentdata['alldata']['groundshipping'] != 1)) {
		$this->usable = array(
										  'U.S.P.S. Express Mail Shipping',
										  'U.S.P.S. Express Mail',
										  'U.S.P.S. Parcel Post Shipping',
										  'U.S.P.S. Parcel Post',
										  'U.S.P.S. Express Mail<sup>&reg;</sup> Shipping',
										  'U.S.P.S. Express Mail<sup>&reg;</sup>',
										  'U.S.P.S. Parcel Post<sup>&reg;</sup> Shipping',
										  'U.S.P.S. Parcel Post<sup>&reg;</sup>'
										  ); }
		else { $this->usable = array(
										  'U.S.P.S. Express Mail Shipping',
										  'U.S.P.S. Express Mail',
										  'U.S.P.S. Priority Mail Shipping',
										  'U.S.P.S. Priority Mail',
										  'U.S.P.S. Priority Mail International Shipping',
										  'U.S.P.S. Priority Mail International',
										  'U.S.P.S. Express Mail International Shipping',
										  'U.S.P.S. Express Mail International',
										  'U.S.P.S. Parcel Post Shipping',
										  'U.S.P.S. Parcel Post',
										  'U.S.P.S. Express Mail<sup>&reg;</sup> Shipping',
										  'U.S.P.S. Express Mail<sup>&reg;</sup>',
										  'U.S.P.S. Priority Mail<sup>&reg;</sup> Shipping',
										  'U.S.P.S. Priority Mail<sup>&reg;</sup>',
										  'U.S.P.S. Priority Mail<sup>&reg;</sup> International Shipping',
										  'U.S.P.S. Priority Mail<sup>&reg;</sup> International',
										  'U.S.P.S. Express Mail<sup>&reg;</sup> International Shipping',
										  'U.S.P.S. Express Mail<sup>&reg;</sup> International',
										  'U.S.P.S. Parcel Post<sup>&reg;</sup> Shipping',
										  'U.S.P.S. Parcel Post<sup>&reg;</sup>'
										  );
		}


					foreach ($this->usable as $ukey => $uvalue)
					{
					$this->mailcode[$ukey] = ereg_replace("[^A-Za-z0-9\_]", "", strtoupper(str_replace(" ", "_", str_replace("<sup>&reg;</sup>", "", $uvalue))));
					$this->matchservice[$this->mailcode[$ukey]] = str_replace("<sup>&reg;</sup>", "", $uvalue);
					}



				$this->load->library('uspsrates');
				$this->uspsrates->setServer("http://production.shippingapis.com/ShippingAPI.dll");
				$this->uspsrates->setUserName("640LOSAN0902");
				$this->uspsrates->setPass("683GF04NK255");
				$this->uspsrates->setService("All");

				//////////////////////
				//////////////////////
				//////////////////////
				//////////////////////

				if ($inputdelivery == 0)
				{
							////Check for ground shipping items if over 1 then take new weight into account
							$newweight = 0;
							$updateweight = FALSE;
							foreach ($currentdata['alldata']['order'] as $odk => $odv)
							{
								if ($odv['quantity'] > 1 && $odv['p_freegrship'] == 1)
									{
										$newweight = $newweight + $this->_unifyweight(($odv['quantity'] -1), $odv['p_lbs'], $odv['p_oz'], TRUE);
										$updateweight = TRUE;
									}
								else
									{
										$newweight = $newweight + $this->_unifyweight($odv['quantity'], $odv['p_lbs'], $odv['p_oz'], TRUE);
									}

							}
							///

							if ($updateweight) $weight = str_replace(',', '.', $newweight);
							else $weight = str_replace(',', '.', $currentdata['Weight']);


							$weight = str_replace(',', '.', $currentdata['Weight']);
							$weight = explode('.', $weight);

							if (!isset($weight[1])) $weight[1] = '0';
							else {
								if ($weight[1] > 9)  $weight[1] = round(($weight[1]/10)*1.6);
								elseif ($weight[1] > 99) $weight[1] = 15;
								elseif ($weight[1] == 0) $weight[1] = 0;
								else $weight[1] = ($weight[1]*1.6);
								}
				$this->uspsrates->setWeight($weight[0], $weight[1]);

				$this->uspsrates->setContainer("Flat Rate Box");

				if ($currentdata['Address']['OrigCountry'] == 'United States of America')
				{
					$this->uspsrates->setCountry("USA");
					$this->uspsrates->setDestZip($currentdata['Address']['PostalCode']);
					$this->uspsrates->setOrigZip("90250");
				}
				else
				{
					$this->uspsrates->setCountry($currentdata['Address']['OrigCountry']);
					$this->uspsrates->setDestZip($currentdata['Address']['PostalCode']);
					$this->uspsrates->setOrigZip("90250");
				}
				$this->uspsrates->setMachinable("true");


				/////
				// Begin BACK
				////
				$quote['back'] = objectToArray($this->uspsrates->getPrice());

				if (isset($quote['back']['error']))
								 {
									 if(isset($quote['back']['error'])) $backerror = $quote['back']['error']->description.'<br>';
									 else $backerror = '';
									 $quote['back'] = false;
									 $quote['backerror'] = '<strong>U.S.P.S:</strong> '.$quote['back']['error']['description'].'<br><br>Please check your delivery <strong>ZIP</strong>, <strong>State</strong> & <strong>Country</strong> or <a href="'.Site_url().'Contact/" target="_blank">contact us</a>...';
						$this->msg_data = array ('msg_title' => 'USPS "BACK" error @ '.FlipDateMail(CurrentTime()),
												 'msg_body' => $backerror.'ClientAddress: '.serialize($currentdata).'<br><br>
												 				Weight: '.$weight[0].'.'.$weight[1].'<br><br>
																IP: '.(htmlspecialchars($_SERVER['REMOTE_ADDR'])).'<br><br>
																POST: '.serialize($_POST).'<br><br>Session: '.serialize($this->session->userdata('fedex')).'<br><br>END',
												'msg_date' => CurrentTime()
												);
									GoMail($this->msg_data, 'errors@1websolutions.net');
								 }

				if (!isset($quote['back']['result'])) $quote['back'] = false;
				else {
						$quote['back']['result'] = array_reverse($quote['back']['result']);
						$tmpbackres = $quote['back']['result'];

						foreach ($quote['back']['result'] as $qbkey => $qbvalue)
							{
								if (!isset($this->matchservice[str_replace("LTSUPGTAMPREGLTSUPGT", "", $qbvalue['mailcode'])])) unset($quote['back']['result'][$qbkey]);
								else {
										$quote['back']['result'][$qbkey]['rate'] = $quote['back']['result'][$qbkey]['rate'] + $this->config->config['shippingadd'];
										$quote['back']['result'][$qbkey]['mailservice'] = str_replace("<sup>&reg;</sup>", "", $quote['back']['result'][$qbkey]['mailservice']);
										$quote['back']['result'][$qbkey]['mailservice'] = str_replace("&lt;sup&gt;&amp;reg;&lt;/sup&gt;", "", $quote['back']['result'][$qbkey]['mailservice']);

										$quote['back']['result'][$qbkey]['mailcode'] = str_replace("LTSUPGTAMPREGLTSUPGT", "", $quote['back']['result'][$qbkey]['mailcode']);
										}
							}


						if (count($quote['back']['result']) == 0)
						{
							$this->msg_data = array ('msg_title' => 'USPS No back matches @ '.FlipDateMail(CurrentTime()),
												 'msg_body' => 'Returned: '.serialize($tmpbackres).'<br><br>
												 				ClientAddress: '.serialize($currentdata).'<br><br>
												 				Weight: '.$weight[0].'.'.$weight[1].'<br><br>
																IP: '.(htmlspecialchars($_SERVER['REMOTE_ADDR'])).'<br><br>
																POST: '.serialize($_POST).'<br><br>Session: '.serialize($this->session->userdata('fedex')).'<br><br>END',
												'msg_date' => CurrentTime()
												);
									GoMail($this->msg_data, 'errors@1websolutions.net');

						}

					 }

					///////
					// If single item and flat rate shipping is selected, add flat rate to matched results...

					if ($currentdata['alldata']['totalitemsquantity'] == 1 && $currentdata['alldata']['shipping'] == 1 && $currentdata['Address']['OrigCountry'] == 'United States of America' && $currentdata['alldata']['groundshipping'] != 1) $quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail Flat Rate','mailcode' => 'USPS_FLAT_RATE', 'rate' => '5.95');
					///////
					// If single item and ground shipping , match priority shipping and make price 0 (free)...

					if ($currentdata['alldata']['totalitemsquantity'] == 1 &&  $currentdata['alldata']['groundshipping'] == 1 && $currentdata['Address']['OrigCountry'] == 'United States of America')
					{
						$matched = false;
						foreach ($quote['back']['result'] as $kqb => $qbr)
							{
								if ($qbr['mailcode'] == 'USPS_PRIORITY_MAIL_SHIPPING' || $qbr['mailcode'] == 'USPS_PRIORITY_MAIL') $matched = TRUE;
							}


						if ($matched)foreach ($quote['back']['result'] as $kqb => $qbr)
							{
								if ($qbr['mailcode'] == 'USPS_PRIORITY_MAIL_SHIPPING' || $qbr['mailcode'] == 'USPS_PRIORITY_MAIL') $quote['back']['result'][$kqb]['rate'] = 0;				}
						else
							{
								$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail','mailcode' => 'USPS_PRIORITY_MAIL_FREE', 'rate' => '0');
							}
					}

				}

				/////
				// BEGIN BOX
				////
				if ($inputdelivery == 1 && ($currentdata['Address']['OrigCountry'] == 'United States of America'))
				{
					if(isset($this->uspsrates->result)) unset($this->uspsrates->result);
					$this->uspsrates->setWeight('2','0');
					$this->uspsrates->setContainer("Flat Rate Box");
					$this->uspsrates->setCountry("USA");
					$this->uspsrates->setDestZip($currentdata['Address']['PostalCode']);
					$this->uspsrates->setOrigZip("90250");
					$this->uspsrates->setMachinable("true");
					$quote['box'] = objectToArray($this->uspsrates->getPrice());

					//if ($_SERVER['REMOTE_ADDR'] == '87.121.161.130') printcool ($quote['box']);
					if (isset($quote['box']['error']))
									 {
										 if(isset($quote['box']['error'])) $boxerror = $quote['box']['error']->description.'<br>';
										 else $boxerror = '';
	 									 $quote['box'] = false;
										 $quote['boxerror'] = '<strong>U.S.P.S:</strong> '.$quote['box']['error']['description'].'<br><br>Please check your delivery <strong>ZIP</strong>, <strong>State</strong> & <strong>Country</strong> or <a href="'.Site_url().'Contact/" target="_blank">contact us</a>...';
							$this->msg_data = array ('msg_title' => 'USPS "BOX" error @ '.FlipDateMail(CurrentTime()),
													 'msg_body' => $boxerror.'ClientAddress: '.serialize($currentdata).'<br><br>
																	Weight: 2.0<br><br>
																	IP: '.(htmlspecialchars($_SERVER['REMOTE_ADDR'])).'<br><br>
																	POST: '.serialize($_POST).'<br><br>Session: '.serialize($this->session->userdata('fedex')).'<br><br>END',
													'msg_date' => CurrentTime()
													);
										GoMail($this->msg_data, 'errors@1websolutions.net');
									 }
					if (!isset($quote['box']['result'])) $quote['box'] = false;
					else {
							$quote['box']['result'] = array_reverse($quote['box']['result']);
							$tmpboxres = $quote['box']['result'];
							foreach ($quote['box']['result'] as $qxkey => $qxvalue)
							{
							if (!isset($this->matchservice[str_replace("LTSUPGTAMPREGLTSUPGT", "", $qxvalue['mailcode'])])) unset($quote['box']['result'][$qxkey]);
							else {
									$quote['box']['result'][$qxkey]['rate'] = $quote['box']['result'][$qxkey]['rate'] + $this->config->config['shippingadd'];
									$quote['box']['result'][$qxkey]['mailservice'] = str_replace("<sup>&reg;</sup>", "", $quote['box']['result'][$qxkey]['mailservice']);
									$quote['box']['result'][$qxkey]['mailservice'] = str_replace("&lt;sup&gt;&amp;reg;&lt;/sup&gt;", "", $quote['box']['result'][$qxkey]['mailservice']);
									$quote['box']['result'][$qxkey]['mailcode'] = str_replace("LTSUPGTAMPREGLTSUPGT", "", $quote['box']['result'][$qxkey]['mailcode']);
								 }
							}
							if (count($quote['box']['result']) == 0){

							$this->msg_data = array ('msg_title' => 'USPS No box matches @ '.FlipDateMail(CurrentTime()),
												 'msg_body' => 'Returned: '.serialize($tmpboxres).'<br><br>
												 				ClientAddress: '.serialize($currentdata).'<br><br>
												 				Weight: 2.0<br><br>
																IP: '.(htmlspecialchars($_SERVER['REMOTE_ADDR'])).'<br><br>
																POST: '.serialize($_POST).'<br><br>Session: '.serialize($this->session->userdata('fedex')).'<br><br>END',
												'msg_date' => CurrentTime()
												);
									GoMail($this->msg_data, 'errors@1websolutions.net');

							}
						}

				}


			if (isset($quote)) return $quote;
	}
function ReviseEbayDescriptionAndQuantity($id = 0)
{
	if ((int)$id > 0)
	{
		$this->session->set_flashdata('action', (int)$id);
		//redirect("Myebay");
		set_time_limit(90);

		$item = $this->Myebay_model->GetItem((int)$id);

		if (!$item) { echo 'Item not found!'; exit(); }

		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

				 ini_set('magic_quotes_gpc', false);

				$this->mysmarty->assign('displays', $item);
				$listDescHtml = htmlspecialchars($this->mysmarty->fetch('myebay/myebay_sendsource.html'));

				$requestXmlBodySTART  = '<?xml version="1.0" encoding="utf-8"?>
<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody = "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				//<VerifyOnly>".TRUE."</VerifyOnly>
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";

				$requestXmlBody .= "<Item>
    <Description>".$listDescHtml."</Description>
    <DescriptionReviseMode>Replace</DescriptionReviseMode>
    <ItemID>".$item['ebay_id']."</ItemID>";


				$requestXmlBody .= "</Item>";
				$requestXmlBodyEND = '</ReviseItemRequest>';

				$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'ReviseItem');

				$responseXml = $session->sendHttpRequest($requestXmlBodySTART.$requestXmlBody.$requestXmlBodyEND);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');

				$responseDoc = new DomDocument();
				$responseDoc->loadXML($responseXml);

				$errors = $responseDoc->getElementsByTagName('Errors');

				if($errors->length > 0)
				{
					echo '<P><B>eBay returned the following error(s):</B>';
					$code     = $errors->item(0)->getElementsByTagName('ErrorCode');
					$shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
					$longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
					//echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
					//if(count($longMsg) > 0) echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));

					$this->_recordsubmiterror(array ('msg_title' => 'REVISE DESCRIPTION ERRORS '.(int)$id.' @'.CurrentTime(), 'msg_body' => printcool(str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)), TRUE), 'msg_date' => CurrentTime()));
					//if ($save) $this->db->update('ebay', array('autorev' => -1, 'autorevtxt' => str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)).' @ '.CurrentTime()), array('e_id' => (int)$id));
				}
				 else
				{ //no errors



					//get results nodes
					$responses = $responseDoc->getElementsByTagName("ReviseItemResponse");
					$txtresp = '';
					foreach ($responses as $response)
					{
					  $acks = $response->getElementsByTagName("Ack");
 	  $ack   = $acks->item(0)->nodeValue;
					  $txtresp .= 'Result: '.$ack.'<br>';
					} // foreach response

					//GoMail(array ('msg_title' => 'REVISED DESCRIPTION '.(int)$id.' @'.CurrentTime(), 'msg_body' => $txtresp, 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);

					//if ($save) $this->db->update('ebay', array('autorev' => 1, 'autorevtxt' => '@ '.CurrentTime()), array('e_id' => (int)$id));
				}


				/*

				$this->db->select("e_id");
				$this->db->where("autorev", 0);
				$this->db->where("ebay_id > ", 0);
				$this->db->order_by("e_id", "DESC");
				$this->query = $this->db->get('ebay');
				if ($this->query->num_rows() > 0) { $goto = $this->query->row_array();
echo $goto['e_id'];
				echo '<script type="text/JavaScript">
<!--
setTimeout("location.href = \'http://www.la-tronics.com/Myebay/ReviseEbayDescription/'.$goto['e_id'].'\';",30000);
-->
</script>'



}
		;*/

				$item = $this->Myebay_model->GetItem((int)$id);

				if (!$item) { echo 'Item not found!'; exit(); }


				require($this->config->config['ebaypath'].'get-common/keys.php');
				require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				$requestXmlBody .= '<ItemID>'.(int)$item['ebay_id'].'</ItemID></GetItemRequest>';
				$verb = 'GetItem';
				$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
				$xml = simplexml_load_string($responseXml);
				if ((string)$xml->Item->ItemID == '') { echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>'; exit();}

				$oldebayvalue = (string)$xml->Item->Quantity;

						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8"?>
		<ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>
								";
						$requestXmlBody .= "<InventoryStatus>
						<ItemID>".$item['ebay_id']."</ItemID>
						<Quantity>".$item['quantity']."</Quantity>
						</InventoryStatus>
						</ReviseInventoryStatusRequest>";

						//GoMail(array ('msg_title' => 'INVENTORY UPDATED '.(int)$id.' @'.CurrentTime(), 'msg_body' => $requestXmlBody, 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);

						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'ReviseInventoryStatus');

						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');

						$responseDoc = new DomDocument();
						$responseDoc->loadXML($responseXml);

						$errors = $responseDoc->getElementsByTagName('Errors');

						if($errors->length > 0)
						{
							echo '<P><B>eBay returned the following error(s):</B>';
							$code     = $errors->item(0)->getElementsByTagName('ErrorCode');
							$shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
							$longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
							echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
							if(count($longMsg) > 0) echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
						}
						 else
						 { //no errors

							//get results nodes
							$responses = $responseDoc->getElementsByTagName("ReviseInventoryStatusResponse");
							foreach ($responses as $response)
							{
							  $acks = $response->getElementsByTagName("Ack");
		/*				*/ 	  $ack   = $acks->item(0)->nodeValue;
							   $this->session->set_flashdata('success_msg', 'Result: '.$ack);
							} // foreach response

						//$this->db->update('ebay', array('ebayquantity' => $item['quantity']), array('e_id' => (int)$id));


						$linkBase = "http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=";

						$this->session->set_flashdata('action', (int)$id);
						$this->session->set_flashdata('gotoebay', $linkBase.$item['ebay_id']);

						$this->_logaction('EbayInventoryUpdate', 'Q',array('Quantity @ eBay' => $oldebayvalue), array('Quantity @ eBay' => $item['quantity']), $id, $item['ebay_id'], 0);
						$this->_logaction('EbayInventoryUpdate', 'Q',array('Local eBay Quantity' => $item['ebayquantity']), array('Local eBay Quantity' => $item['quantity']), $id, $item['ebay_id'], 0);


					}

	}

function _recordsubmiterror($err = array())
{
					GoMail($err, '365@1websolutions.net', $this->config->config['no_reply_email']);
					$err['admin'] = 'CART';
					$this->db->insert('ebay_submitlog', $err);
}

}
}