<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


///

//OLD USPS
function _PrepareUsps($currentdata, $inputdelivery = 0) 
	{
		//printcool ($currentdata);
		//break;
		/*
		
		[alldata] => Array
        (
            [order] => Array
                (
                    [294] => Array
                        (
                            [quantity] => 4
                            [p_title] => Compaq G60 CQ60 DC Power Jack 50.4AH28.001 496835-001
                            [p_type] => 0
                            [p_sef] => Compaq-G60-CQ60-DC-Power-Jack-504AH28001-496835-001
                            [p_img1] => 294_Compaq-G60-CQ60-DC-Power-Jack-504AH28001-496835-001_1.JPG
                            [price] => 69.95
                            [imagesize] => Array
                                (
                                    [0] => 88
                                    [1] => 50
                                    [2] => 2
                                    [3] => width="88" height="50"
                                    [bits] => 8
                                    [channels] => 3
                                    [mime] => image/jpeg
                                )

                            [p_lbs] => 0
                            [p_oz] => 5
                            [p_shipping] => 1
                            [p_freegrship] => 0
                            [totalweight] => 1.2
                            [totalweight_custom] => 0
                            [total] => 279.8
                        )

                )

            [total] => 279.8
            [weight] => 1.2
            [weight_custom] => 0
            [shipping] => 4
            [groundshipping] => 2
            [totalitemsquantity] => 4
        )*/
	
		if ($currentdata['alldata']['totalitemsquantity'] == 1 && $currentdata['alldata']['groundshipping'] == 1)
		{
			/*if ($currentdata['Address']['OrigCountry'] == 'United States of America')
						{							
							$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail Flat Rate','mailcode' => 'USPS_FLAT_RATE', 'rate' => '5.95');
							$quote['back']['result'][] = array('mailservice' => 'First Class Mail','mailcode' => 'USPS_FIRST_CLASS', 'rate' => '4.95');							
						}
						else
						{							
							$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail International Flat Rate','mailcode' => 'USPS_FLAT_RATE', 'rate' => '14.95');
							$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Express Mail International (EMS) Flat-Rate','mailcode' => 'USPS_EMS_FLAT_RATE', 'rate' => '30.00');							
						}*/
			
		}
		if ($currentdata['alldata']['totalitemsquantity'] == 1 && $currentdata['alldata']['shipping'] == 1)
		{
					if ($currentdata['Address']['OrigCountry'] == 'United States of America')
						{
							//if ($inputdelivery == 0)
							//{
							$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail Flat Rate','mailcode' => 'USPS_FLAT_RATE', 'rate' => '5.95');
							//$quote['back']['result'][] = array('mailservice' => 'First Class Mail','mailcode' => 'USPS_FIRST_CLASS', 'rate' => '4.95');
							//}
							/*if ($inputdelivery == 1 && ($currentdata['Address']['OrigCountry'] == 'United States of America')) 
							{
								$quote['box']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail Flat Rate','mailcode' => 'USPS_FLAT_RATE', 'rate' => '5.95');
								$quote['box']['result'][] = array('mailservice' => 'First Class Mail','mailcode' => 'USPS_FIRST_CLASS', 'rate' => '4.95');
							}*/
						}
						//else
					//	{
							//if ($inputdelivery == 0)
						//	{
							//$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail International Flat Rate','mailcode' => 'USPS_FLAT_RATE', 'rate' => '14.95');
							//$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Express Mail International (EMS) Flat-Rate','mailcode' => 'USPS_EMS_FLAT_RATE', 'rate' => '30.00');
							//}
							/*if ($inputdelivery == 1 && ($currentdata['Address']['OrigCountry'] == 'United States of America')) 
							{
								$quote['box']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail International Flat Rate', 'mailcode' => 'USPS_FLAT_RATE', 'rate' => '14.95');
								$quote['box']['result'][] = array('mailservice' => 'U.S.P.S. Express Mail International (EMS) Flat-Rate', 'mailcode' => 'USPS_EMS_FLAT_RATE', 'rate' => '30.00');
							}*/
						//}
				
		}
		/*	if (isset($currentdata['has_shipping']) && (int)$currentdata['has_shipping'] == 1)	
			{
				if ($currentdata['Address']['OrigCountry'] == 'United States of America')
				{
					if ($inputdelivery == 0)
					{
					$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail Flat Rate','mailcode' => 'USPS_FLAT_RATE', 'rate' => '5.95');
					$quote['back']['result'][] = array('mailservice' => 'First Class Mail','mailcode' => 'USPS_FIRST_CLASS', 'rate' => '4.95');
					}
					if ($inputdelivery == 1 && ($currentdata['Address']['OrigCountry'] == 'United States of America')) 
					{
						$quote['box']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail Flat Rate','mailcode' => 'USPS_FLAT_RATE', 'rate' => '5.95');
						$quote['box']['result'][] = array('mailservice' => 'First Class Mail','mailcode' => 'USPS_FIRST_CLASS', 'rate' => '4.95');
					}
				}
				else
				{
					if ($inputdelivery == 0)
					{
					$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail International Flat Rate','mailcode' => 'USPS_FLAT_RATE', 'rate' => '14.95');
					$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Express Mail International (EMS) Flat-Rate','mailcode' => 'USPS_EMS_FLAT_RATE', 'rate' => '30.00');
					}
					if ($inputdelivery == 1 && ($currentdata['Address']['OrigCountry'] == 'United States of America')) 
					{
						$quote['box']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail International Flat Rate', 'mailcode' => 'USPS_FLAT_RATE', 'rate' => '14.95');
						$quote['box']['result'][] = array('mailservice' => 'U.S.P.S. Express Mail International (EMS) Flat-Rate', 'mailcode' => 'USPS_EMS_FLAT_RATE', 'rate' => '30.00');
					}
				}
			}*/
			
				/*if (($currentdata['Address']['OrigCountry'] != 'United States of America') && (isset($currentdata['has_shipping']) && (int)$currentdata['has_shipping'] == 1))	
				{
					if ($inputdelivery == 0)
					{
					$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail International Flat Rate','mailcode' => 'USPS_FLAT_RATE', 'rate' => '14.95');
					$quote['back']['result'][] = array('mailservice' => 'U.S.P.S. Express Mail International (EMS) Flat-Rate','mailcode' => 'USPS_EMS_FLAT_RATE', 'rate' => '30.00');
					}
					if ($inputdelivery == 1 && ($currentdata['Address']['OrigCountry'] == 'United States of America')) 
					{
						$quote['box']['result'][] = array('mailservice' => 'U.S.P.S. Priority Mail International Flat Rate', 'mailcode' => 'USPS_FLAT_RATE', 'rate' => '14.95');
						$quote['box']['result'][] = array('mailservice' => 'U.S.P.S. Express Mail International (EMS) Flat-Rate', 'mailcode' => 'USPS_EMS_FLAT_RATE', 'rate' => '30.00');
					}
				}*/
			else			
			{	
					/*$this->unusable = array('U.S.P.S. Express Mail Hold For Pickup',
											'U.S.P.S. Priority Mail Small Flat-Rate Box International',
											'U.S.P.S. Express Mail Hold For Pickup',
											'U.S.P.S. Priority Mail Small Flat-Rate Box International',
											'U.S.P.S. Priority Mail Large Flat-Rate Box International',
											'U.S.P.S. Priority Mail Regular-Medium Flat-Rate Box International',
											'U.S.P.S. USPS GXG Envelopes',
											'U.S.P.S. Global Express Guaranteed Non-Document Non-Rectangular',
											'U.S.P.S. Global Express Guaranteed Non-Document Rectangular',
											'U.S.P.S. Global Express Guaranteed',
											'U.S.P.S. Parcel Post',
											'U.S.P.S. Priority Mail International Large Flat-Rate Box',
											'U.S.P.S. Priority Mail International Regular/Medium Flat-Rate Boxes',
											'U.S.P.S. Express Mail International (EMS) Flat-Rate Envelope',
											'U.S.P.S. Priority Mail International Medium Flat Rate Box Shipping',
											'U.S.P.S. Priority Mail International Large Flat Rate Box Shipping',
											'U.S.P.S. Express Mail International Flat Rate Envelope Shipping',
											'U.S.P.S. Priority Mail International Medium Flat Rate Box',
											'U.S.P.S. Priority Mail International Large Flat Rate Box',
											'U.S.P.S. Express Mail International Flat Rate Envelope',
											'U.S.P.S. Global Express Guaranteed GXG',
											'U.S.P.S. Express Mail International Flat Rate Envelope', 
											'U.S.P.S. Priority Mail International Medium Flat Rate Box',
											'U.S.P.S. Priority Mail International Large Flat Rate Box', 
											'U.S.P.S. Express Holiday Guaranteed, U.S.P.S. Express Holiday',
											'U.S.P.S. Express Mail Sunday/Holiday', 
											'U.S.P.S. Express Mail Flat-Rate Envelope Sunday/Holiday',
											'U.S.P.S. FirstClass Mail International Large Envelope',
											'U.S.P.S. Priority Mail International Flat Rate Envelope',
											'U.S.P.S. Priority Mail International Flat Rate Envelope Shipping',
											'U.S.P.S. Priority Mail International Small Flat Rate Box',
											'U.S.P.S. Priority Mail International Small Flat Rate Box Shipping',
											'U.S.P.S. Priority Mail International Large Flat Rate Box Shipping',
											'U.S.P.S. Express Mail International Flat Rate Envelope Shipping',
											'U.S.P.S. USPS GXG Envelopes Shipping',
											'U.S.P.S. Global Express Guaranteed Non-Document Non-Rectangular Shipping',
											'U.S.P.S. Global Express Guaranteed NonDocument NonRectangular',
											'U.S.P.S. Global Express Guaranteed NonDocument Rectangular',
											'U.S.P.S. Global Express Guaranteed NonDocument Rectangular',
											'U.S.P.S. Global Express Guaranteed GXG Shipping',
											'U.S.P.S. First-Class Mail International Package Shipping',
											'U.S.P.S. First-Class Mail International Package',
											'U.S.P.S. FirstClass Mail International Package Shipping',
											'U.S.P.S. FirstClass Mail International Package'
											);*/
					
					$this->usable = array(
										  'U.S.P.S. Express Mail Shipping',
										  'U.S.P.S. Express Mail',
										  'U.S.P.S. Priority Mail Shipping',
										  'U.S.P.S. Priority Mail',
										  'U.S.P.S. Priority Mail International Shipping',
										  'U.S.P.S. Priority Mail International',
										  'U.S.P.S. Express Mail International Shipping',
										  'U.S.P.S. Express Mail International',
										  'U.S.P.S. Parcel Post Shipping',
										  'U.S.P.S. Parcel Post'
										  );
	
					
					
					foreach ($this->usable as $ukey => $uvalue)
					{
					$this->mailcode[$ukey] = ereg_replace("[^A-Za-z0-9\_]", "", strtoupper(str_replace(" ", "_", $uvalue)));
					$this->matchservice[$this->mailcode[$ukey]] = $uvalue;
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
					//$this->uspsrates->setDestZip("20008");
					//$this->uspsrates->setOrigZip("10022");
				}
				else 
				{
					$this->uspsrates->setCountry($currentdata['Address']['OrigCountry']);
					$this->uspsrates->setDestZip($currentdata['Address']['PostalCode']);
					$this->uspsrates->setOrigZip("90250");
					//$this->uspsrates->setDestZip("20008");
					//$this->uspsrates->setOrigZip("10022");
				}
				$this->uspsrates->setMachinable("true");

				$quote['back'] = objectToArray($this->uspsrates->getPrice()); 
				//if ($_SERVER['REMOTE_ADDR'] == '87.121.161.130') printcool ($quote['back']);
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
								
							if (!isset($this->matchservice[$qbvalue['mailcode']])) unset($quote['back']['result'][$qbkey]);
							}
						
						if (($currentdata['Address']['OrigCountry'] == 'United States of America') && (isset($currentdata['has_shipping']) && (int)$currentdata['has_shipping'] == 1))
						{
							$flatrate = array('mailservice' => 'U.S.P.S. Priority Mail Flat Rate','mailcode' => 'USPS_FLAT_RATE', 'rate' => '4.95');
							array_unshift($quote['back']['result'], $flatrate);
						}
	
						if (count($quote['back']['result']) == 0){
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
				}
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
							if (!isset($this->matchservice[$qxvalue['mailcode']])) unset($quote['box']['result'][$qxkey]);
							}
							if (count($quote['box']['result']) == 0){
							$this->msg_data = array ('msg_title' => 'USPS No box matches @ '.FlipDateMail(CurrentTime()),
												 'msg_body' => 'Returned: '.serialize($tmpboxres).'<br><br>
												 				ClientAddress: '.serialize($currentdata).'<br><br>
												 				Weight: '.$weight[0].'.'.$weight[1].'<br><br>
																IP: '.(htmlspecialchars($_SERVER['REMOTE_ADDR'])).'<br><br>
																POST: '.serialize($_POST).'<br><br>Session: '.serialize($this->session->userdata('fedex')).'<br><br>END',		
												'msg_date' => CurrentTime()
												);			
									GoMail($this->msg_data, 'errors@1websolutions.net');	
						
							}
						}

				}
			}		
			
			return $quote;
	}
	/////////////
	/////////////
	/////////////
	/////////////
	/////////////



function Repair($step = 'Step1', $confirmcode = '', $id = '') 
{	 
/*if ($_SERVER['REMOTE_ADDR'] != '93.152.154.46') {
	echo 'Shipping options are being upgraded. Please visit again later. Thank you for your understanding';
	exit();
}*/
	if (($step != 'Step1') && ($step != 'Step2') && ($step != 'Complete')) { redirect("/Laptop-Repair-Form"); exit();}
	
	$this->load->library('form_validation');
	$this->mysmarty->assign('productview', 'repair');

	if (isset($_POST['reply']))
		{
			$this->load->model('Captcha_model');
			$postcaptcha = $this->Captcha_model->CheckCaptcha();
			$msg = htmlspecialchars($this->input->post('msg',TRUE));
			
			if ($postcaptcha)
						{
							$cleanedcode = $this->input->xss_clean($confirmcode);
							$cleanedcode = ereg_replace("[^A-Za-z0-9]", "", $cleanedcode); 
							$codelength = strlen($cleanedcode);
							if ($codelength == '50') 
								{ 
									$checked = $this->Product_model->MatchConfirmCode($cleanedcode, (int)$id);
									if ($checked) 
										{
											$cdata['f_msg'] = $msg;				
											$cdata['f_id'] = (int)$id;
											$cdata['f_owner'] = 'cust';
											$cdata['f_time'] = CurrentTime();
											
											$this->db->insert('forms_request_comm', $cdata);
											
											$this->comm = $this->Product_model->GetCommData($cleanedcode, (int)$id);
											
											$msg_data = array ('msg_title' => 'Reply regarding free estimate quote No.'.(int)$id.'.',
																'msg_body' => $cdata['f_msg'],
																'msg_date' => CurrentTime()
															);
										
											$msg_data['msg_body'] .= '<br><br>
											------------------------------------------------------------<br><br>**** History: ****<br><br>';
											foreach ($this->comm as $c)
												{
													$msg_data['msg_body'] .= '<strong>From: ';
													if ($c['f_owner'] == 'admin') $msg_data['msg_body'] .= 'Shop';
													else $msg_data['msg_body'] .= 'Client';
													$msg_data['msg_body'] .= '</strong> - Date &amp; Time: '.FlipDate($c['f_time']).'<br>
													<br>'.$c['f_msg'].'<br>
													 ------------------------------------------------------------<br />    
													';													
												}
											GoMail($msg_data, '', $checked['email']);	
												
											$this->mysmarty->assign("sentmsg", 'Your message hase been sent.');
										}
							}
							
						}
			else 
						{
							$this->mysmarty->assign("replymsg", $msg);
							$this->mysmarty->assign("captchaerror", 'Please specify if you are human');
						}
			$_POST = array();
		}


	if ($step == 'Step1') {
	$this->form_validation->set_rules('dAddress', 'Delivery Address', 'trim|required|xss_clean|min_length[5]');					
	$this->form_validation->set_rules('dCity', 'Delivery City', 'trim|required|xss_clean|max_length[50]');
	$this->form_validation->set_rules('dPostCode', 'Delivery PostCode / ZIP', 'trim|required|xss_clean|max_length[20]');	
	$this->form_validation->set_rules('dState', 'Delivery State', 'trim|required|xss_clean|max_length[50]');
	$this->form_validation->set_rules('dCountry', 'Delivery Country', 'trim|required|xss_clean|max_length[50]');
	$this->form_validation->set_rules('residential', 'Residential Address', 'trim|integer|xss_clean');
	$this->form_validation->set_rules('brand', 'Laptop Brand', 'trim|required|xss_clean');
	$this->form_validation->set_rules('model', 'Laptop Model', 'trim|required|xss_clean');
	$this->form_validation->set_rules('item', 'Problem Description', 'trim|required|xss_clean');
	$this->form_validation->set_rules('freeship', 'Free Shipping', 'trim|integer|xss_clean');
	/*if (isset($_POST) && ($_POST['dState'] == 'Non US') && ($_POST['dCountry'] == 'United States of America'))
		{
			
		}												*/
	}
	elseif ($step == 'Step2')
	{
	$this->form_validation->set_rules('send', 'Sending options', 'trim|required|xss_clean');
	$this->form_validation->set_rules('safebox', 'Safety Shipment Box', 'trim|required|xss_clean');
	$this->form_validation->set_rules('return', 'Return options', 'trim|required|xss_clean');		
	$this->form_validation->set_rules('Email', 'Email', 'trim|required|valid_email|xss_clean');
	$this->form_validation->set_rules('FirstName', 'First Name', 'trim|required|min_length[2]|xss_clean');
	$this->form_validation->set_rules('LastName', 'Last Name', 'trim|required|min_length[2]|xss_clean');
	$this->form_validation->set_rules('Telephone', 'Telephone', 'trim|required|xss_clean|numeric|max_length[50]');

	$this->form_validation->set_rules('Mobile', 'Mobile', 'trim|xss_clean|numeric|max_length[50]');
	$this->form_validation->set_rules('same', 'same', 'trim|xss_clean|max_length[1]');
		if (!isset($_POST['same'])) {
		$this->form_validation->set_rules('Address', 'Billing Address', 'trim|required|xss_clean|min_length[5]');
		$this->form_validation->set_rules('City', 'Billing City', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('PostCode', 'Billing PostCode / ZIP', 'trim|required|xss_clean|max_length[20]');
		$this->form_validation->set_rules('State', 'Billing State', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('Country', 'Billing Country', 'trim|required|xss_clean|max_length[50]');
		}
	}
	elseif($step == 'Complete') 
	{
	$this->form_validation->set_rules('agree', 'Agree to terms & conditions', 'trim|required|integer|xss_clean');
	$this->form_validation->set_rules('payproc', 'Method of payment', 'trim|required|integer|xss_clean');
	}
	
	$this->messages = array ('en' => array ('inv-code' => 'Please specify if you are human', 'accdeact' => 'Account has been de-activated. Please contact the administator', 'wrusrpss' => 'Wrong username or password', 'tknuser' => 'The username is taken', 'emlreg' => 'The e-mail address is already registered', 'success' => 'Success. Please check your email', 'nouserwithemail' => 'No users with this e-mail address'));
	
	$this->mysmarty->assign('ctr', ReturnCountries($this->config->config['language_abbr']));
	$this->mysmarty->assign('sts', ReturnStates());
	if ($step == 'Step1') { 
		if (!$_POST && $confirmcode != 'Change') 
			{
			$this->session->unset_userdata('fedex'); 
			$this->session->unset_userdata('regdata');
			}
	}
	
	if (($this->form_validation->run() == FALSE) && ($step == 'Step1'))
			{		
			
			if ($confirmcode == 'Change') 
				{
				if (isset($this->session->userdata['regdata']))  $this->sessregdata = $this->session->userdata['regdata']; 
				else { redirect("/Laptop-Repair-Form"); exit();}
					
				if (!isset($this->sessregdata['dAddress']) || !isset($this->sessregdata['dCity']) || !isset($this->sessregdata['dPostCode']) || !isset($this->sessregdata['dState']) || !isset($this->sessregdata['dCountry']) || !isset($this->sessregdata['freeship']) || !isset($this->sessregdata['brand']) || !isset($this->sessregdata['model']) || !isset($this->sessregdata['item'])) { redirect("/Laptop-Repair-Form"); exit();}
				
				
				$this->regdata = $this->sessregdata;
				}
				else
				{
				 	 $this->regdata = array(
												'dAddress' => $this->input->post('dAddress', TRUE),									
												'dCity' => $this->input->post('dCity', TRUE),
												'dPostCode' => $this->input->post('dPostCode', TRUE),
												'dState' => $this->input->post('dState', TRUE),
												'dCountry' => $this->input->post('dCountry', TRUE),												
												'freeship' => (int)$this->input->post('freeship', TRUE),
												'brand' => $this->input->post('brand', TRUE),
												'model' => $this->input->post('model', TRUE),										  
											    'item' => $this->input->post('item', TRUE),
												'residential' => (int)$this->input->post('residential', TRUE)
												
										   );	
					 
					 if ($confirmcode != '')
									{	
										$this->cleanedcode = $this->input->xss_clean($confirmcode);
										$this->cleanedcode = ereg_replace("[^A-Za-z0-9]", "", $this->cleanedcode); 
										$this->codelength = strlen($this->cleanedcode);
										if ($this->codelength == '50') 
											{ 
												$this->confirmcode = $this->cleanedcode;
												$this->mysmarty->assign('confirmcode', $this->confirmcode);
												$this->mysmarty->assign('confirmid', (int)$id);
											}
									}
					 
					 if (!$_POST)
					 {
							if (isset($this->session->userdata['user_id'])) 
								{
								$this->regdata = $this->Start_model->GetUserDetails((int)$this->session->userdata['user_id']);
								}								
									
									if (isset($this->confirmcode) && $this->confirmcode) 
										{				
															$this->formreqdata = $this->Product_model->GetFormData($this->confirmcode, (int)$id);																
															$this->regdata['brand'] = $this->formreqdata['brand'];
															$this->regdata['model'] = $this->formreqdata['model'];
															$this->regdata['item'] = $this->formreqdata['item'];
															$this->regdata['FirstName'] = $this->formreqdata['fname'];
															$this->regdata['LastName'] = $this->formreqdata['lname'];
															$this->regdata['Telephone'] = $this->formreqdata['tel'];
															$this->regdata['Email'] = $this->formreqdata['email'];
										$this->regdata['confirmcode'] = $this->confirmcode;
										$this->regdata['confirmid'] = (int)$id;
										}
					 }
				}
				if (isset($this->confirmcode) && $this->confirmcode) 
										{
											if (!isset($this->comm)) $this->comm = $this->Product_model->GetCommData($this->confirmcode, (int)$id);
											$this->load->model('Captcha_model');
											$this->Captcha_model->DoCaptcha();

										}
				if (!isset($this->comm)) $this->comm = FALSE;
				$this->mysmarty->assign('comm', $this->comm);	
				$this->mysmarty->assign('regdata', $this->regdata);	
				$this->session->set_userdata('regdata', $this->regdata);
				
				$this->mysmarty->assign('step', '1');
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('welcome/welcome_main.html');
				exit();
			}
	elseif (($this->form_validation->run() == TRUE) && ($step == 'Step1'))
			{
				
				if (isset($this->session->userdata['regdata']))  $this->sessregdata = $this->session->userdata['regdata']; 
				else { redirect("/Laptop-Repair-Form"); exit();}	
				
							 $this->regdata = array(
												'dAddress' => $this->form_validation->set_value('dAddress', TRUE),								
												'dCity' => $this->form_validation->set_value('dCity', TRUE),
												'dPostCode' => $this->form_validation->set_value('dPostCode', TRUE),
												'dState' => $this->form_validation->set_value('dState', TRUE),
												'dCountry' => $this->form_validation->set_value('dCountry', TRUE),												
												'freeship' => (int)$this->form_validation->set_value('freeship', TRUE),
												'brand' => $this->form_validation->set_value('brand', TRUE),
												'model' => $this->form_validation->set_value('model', TRUE),										  
											    'item' => $this->form_validation->set_value('item', TRUE),													
												'residential' => (int)$this->form_validation->set_value('residential', TRUE),
												'has_shipping' => 0
										   );
												
												
				$this->regdata = array_merge($this->sessregdata, $this->regdata);
				$this->session->set_userdata('regdata', $this->regdata);
				
				$this->_ProcessAddress($this->regdata, 1, 1);
				$this->mysmarty->assign('session',$this->session->userdata);				
				redirect("/Laptop-Repair-Form/Step2");
				exit();
			}
	elseif ((($this->form_validation->run() == FALSE) || ($this->form_validation->run() == TRUE)) && ($step == 'Step2'))
			{			
				if (isset($this->session->userdata['regdata']))  $this->sessregdata = $this->session->userdata['regdata']; 
				else { redirect("/Laptop-Repair-Form"); exit();}	
				
							 if ($this->form_validation->run() == FALSE) 
								{
									if ($confirmcode == 'Change') 
									{
										if (isset($this->session->userdata['regdata']))  $this->sessregdata = $this->session->userdata['regdata']; 
										else { redirect("/Laptop-Repair-Form"); exit();}
										
												if (!isset($this->sessregdata['dAddress']) || !isset($this->sessregdata['dCity']) || !isset($this->sessregdata['dPostCode']) || !isset($this->sessregdata['dState']) || !isset($this->sessregdata['dCountry']) || !isset($this->sessregdata['freeship']) || !isset($this->sessregdata['brand']) || !isset($this->sessregdata['model']) || !isset($this->sessregdata['item']) || !isset($this->sessregdata['Email']) || !isset($this->sessregdata['FirstName']) || !isset($this->sessregdata['LastName']) || !isset($this->sessregdata['Telephone']) || !isset($this->sessregdata['send']) || !isset($this->sessregdata['safebox']) || !isset($this->sessregdata['return'])) { redirect("/Laptop-Repair-Form"); exit();}

												if (!isset($this->sessregdata['same'])) { 
												if (!isset($this->sessregdata['Address']) || !isset($this->sessregdata['City']) || !isset($this->sessregdata['PostCode']) || !isset($this->sessregdata['State']) ||  !isset($this->sessregdata['Country'])) { redirect("/Laptop-Repair-Form"); exit();}
												}
												
										$this->regdata = $this->sessregdata;											
									}
									else 
									{
								
										if ($_POST) 
										{
											$this->regdata = array(
												    'Email' => $this->input->post('Email', TRUE),
												    'FirstName' => $this->input->post('FirstName', TRUE),
												    'LastName' => $this->input->post('LastName', TRUE),				  										   
												    'Telephone' => $this->input->post('Telephone', TRUE),
												    'Mobile' => $this->input->post('Mobile', TRUE),
													'same' => (int)$this->input->post('same', TRUE),
													'send' => $this->input->post('send', TRUE),
													'safebox' => $this->input->post('safebox', TRUE),
													'return' => $this->input->post('return', TRUE)
												   );
											if ($this->regdata['same'] == 0)
											{
											$this->regdata['Address'] = $this->input->post('Address', TRUE);									
											$this->regdata['City'] = $this->input->post('City', TRUE);
											$this->regdata['PostCode'] = $this->input->post('PostCode', TRUE);
											$this->regdata['State'] = $this->input->post('State', TRUE);
											$this->regdata['Country'] = $this->input->post('Country', TRUE);
											}
										}
										else 
										{
											if (isset($this->session->userdata['regdata']))  $this->sessregdata = $this->session->userdata['regdata']; 
											$this->regdata = array();	
										}
									}
								}
							else
								{
									$this->regdata = array(
												    'Email' => $this->form_validation->set_value('Email', TRUE),
												    'FirstName' => $this->form_validation->set_value('FirstName', TRUE),
												    'LastName' => $this->form_validation->set_value('LastName', TRUE),				  										
												    'Telephone' => $this->form_validation->set_value('Telephone', TRUE),
												    'Mobile' => $this->form_validation->set_value('Mobile', TRUE),												
													'same' => (int)$this->form_validation->set_value('same', TRUE),
													'send' => $this->form_validation->set_value('send', TRUE),
													'safebox' => $this->form_validation->set_value('safebox', TRUE),
													'return' => $this->form_validation->set_value('return', TRUE)
												   );
									if ($this->regdata['same'] == 0)
											{
											$this->regdata['Address'] = $this->form_validation->set_value('Address', TRUE);									
											$this->regdata['City'] = $this->form_validation->set_value('City', TRUE);
											$this->regdata['PostCode'] = $this->form_validation->set_value('PostCode', TRUE);
											$this->regdata['State'] = $this->form_validation->set_value('State', TRUE);
											$this->regdata['Country'] = $this->form_validation->set_value('Country', TRUE);
											}
								}
				
				$this->regdata = array_merge($this->sessregdata, $this->regdata);
				$this->session->set_userdata('regdata', $this->regdata);
				
				if ($this->form_validation->run() == TRUE) 
				{					
					redirect("/Laptop-Repair-Form/Complete");
					exit();
				}
				else
				{
					$this->mysmarty->assign('step', '2');
					$this->mysmarty->assign('errors', $this->form_validation->_error_array);			
					$this->mysmarty->assign('regdata', $this->regdata);					
					$this->mysmarty->assign('session',$this->session->userdata);				
					$this->mysmarty->view('welcome/welcome_main.html');
					exit();
				}
			}	
	elseif (($this->form_validation->run() == FALSE) && ($step == 'Complete'))
			{
				if (isset($this->session->userdata['regdata']))  $this->sessregdata = $this->session->userdata['regdata']; 
				else { redirect("/Laptop-Repair-Form"); exit();}
				if (!isset($this->sessregdata['dAddress']) || !isset($this->sessregdata['dCity']) || !isset($this->sessregdata['dPostCode']) || !isset($this->sessregdata['dState']) || !isset($this->sessregdata['dCountry']) || !isset($this->sessregdata['freeship']) || !isset($this->sessregdata['Email']) || !isset($this->sessregdata['FirstName']) || !isset($this->sessregdata['LastName']) || !isset($this->sessregdata['Telephone']) || !isset($this->sessregdata['send']) || !isset($this->sessregdata['safebox']) || !isset($this->sessregdata['return'])) { redirect("/Laptop-Repair-Form"); exit();}

				
								$this->regdata = array(
													'agree' => (int)$this->input->post('agree', TRUE),
													'payproc' => (int)$this->input->post('payproc', TRUE)
													);
				
				$this->regdata = array_merge($this->sessregdata, $this->regdata);
				
									if (isset($this->session->userdata['fedex']['quote']['to']))
									{
									$this->fedexquoteto = $this->session->userdata['fedex']['quote']['to'];
									foreach ($this->fedexquoteto as $fkeyt => $fvaluet) {										
									if ($this->regdata['send'] == $fvaluet['type']) {
										$this->priceto = (float)$fvaluet['sum']+3;
										if ((float)$fvaluet['sum'] == 0) $this->priceto = 0;}
									elseif ($this->regdata['send'] == '0') { $this->priceto = 0;}
									}	
									}
									
									
									if (substr($this->regdata['safebox'], 0, 5) == 'USPS_') {
										if (isset($this->session->userdata['fedex']['uspsquote']['box']['result'])){
										$this->uspsquotebox = $this->session->userdata['fedex']['uspsquote']['box']['result'];
										foreach ($this->uspsquotebox as $fkeyx => $fvaluex) {		
										if ($this->regdata['safebox'] == $fvaluex['mailcode']) {
											$this->pricebox = (float)$fvaluex['rate']+3;
											if ((float)$fvaluex['rate'] == 0) $this->pricebox = 0;}
										elseif ($this->regdata['safebox'] == '0') {$this->pricebox = 0;}
										}
										}
									}
									else
									{
										if (isset($this->session->userdata['fedex']['quote']['box'])){
										$this->fedexquotebox = $this->session->userdata['fedex']['quote']['box'];
										foreach ($this->fedexquotebox as $fkeyx => $fvaluex){		
										if ($this->regdata['safebox'] == $fvaluex['type']) {
											$this->pricebox = (float)$fvaluex['sum']+3;
											if ((float)$fvaluex['sum'] == 0) $this->pricebox = 0;}
										elseif ($this->regdata['safebox'] == '0') {$this->pricebox = 0;}
										}
										}
									}								
									
									if (substr($this->regdata['return'], 0, 5) == 'USPS_') {
										if (isset($this->session->userdata['fedex']['uspsquote']['back']['result'])){
										$this->uspsquoteback = $this->session->userdata['fedex']['uspsquote']['back']['result'];
										foreach ($this->uspsquoteback as $fkeyb => $fvalueb) {										
										if ($this->regdata['return'] == $fvalueb['mailcode']) {
											$this->priceback = (float)$fvalueb['rate']+3;
											if ((float)$fvalueb['rate'] == 0) $this->priceback = 0;}										
										}}								
									}
									else
									{
										if (isset($this->session->userdata['fedex']['quote']['back'])){
										$this->fedexquoteback = $this->session->userdata['fedex']['quote']['back'];
										foreach ($this->fedexquoteback as $fkeyb => $fvalueb) {										
										if ($this->regdata['return'] == $fvalueb['type']) {
											$this->priceback = (float)$fvalueb['sum']+3;
											if ((float)$fvalueb['sum'] == 0) $this->priceback = 0;}										
										}}
									}
									if (!isset($this->priceto)) $this->priceto = 0;
									if (!isset($this->pricebox)) $this->pricebox = 0;
									if (!isset($this->priceback)) $this->priceback = 0;
						$this->total['total'] = $this->priceto+$this->pricebox+$this->priceback;
						$this->total['to'] = $this->priceto;
						$this->total['box'] = $this->pricebox;
						$this->total['back'] = $this->priceback;
				
				$this->mysmarty->assign('total', $this->total);

				$this->mysmarty->assign('step', 'Complete');
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);			
				
				$this->mysmarty->assign('regdata', $this->regdata);	
				$this->session->set_userdata('regdata', $this->regdata);			
				
				$this->mysmarty->assign('session',$this->session->userdata);
				
				$this->mysmarty->view('welcome/welcome_main.html');
				exit();	
			}
	elseif (($this->form_validation->run() == TRUE) && ($step == 'Complete'))
			{
						if (isset($this->session->userdata['regdata']))  $this->endregdata = $this->session->userdata['regdata']; 
						else { redirect("/Laptop-Repair-Form"); exit();}

						$this->insertdata['email'] = $this->endregdata['Email'];
						$this->insertdata['fname'] = $this->endregdata['FirstName'];
						$this->insertdata['lname'] = $this->endregdata['LastName'];			  										   
						$this->insertdata['tel'] = $this->endregdata['Telephone'].' '.$this->endregdata['Mobile'];
						$this->insertdata['daddress'] = $this->endregdata['dAddress'];
						$this->insertdata['dcity'] = $this->endregdata['dCity'];
						$this->insertdata['dpostcode'] = $this->endregdata['dPostCode'];
						$this->insertdata['dstate'] = $this->endregdata['dState'];
						$this->insertdata['dcountry'] = $this->endregdata['dCountry'];
						
						if ((int)$this->endregdata['same'] == 1) 
						{							
						$this->insertdata['address'] = $this->endregdata['dAddress'];
						$this->insertdata['city'] = $this->endregdata['dCity'];
						$this->insertdata['postcode'] = $this->endregdata['dPostCode'];
						$this->insertdata['state'] = $this->endregdata['dState'];
						$this->insertdata['country'] = $this->endregdata['dCountry'];
						}
						else
						{
						$this->insertdata['address'] = $this->endregdata['Address'];
						$this->insertdata['city'] = $this->endregdata['City'];
						$this->insertdata['postcode'] = $this->endregdata['PostCode'];
						$this->insertdata['state'] = $this->endregdata['State'];
						$this->insertdata['country'] = $this->endregdata['Country'];							
						}												
						$this->insertdata['send'] = $this->endregdata['send'];
						$this->insertdata['safebox'] = $this->endregdata['safebox'];
						$this->insertdata['return'] = $this->endregdata['return'];
						$this->insertdata['freeship'] = (int)$this->endregdata['freeship'];
						
						$this->insertdata['payproc'] = (int)$this->form_validation->set_value('payproc', TRUE);
						if (($this->insertdata['payproc'] != '1') && ($this->insertdata['payproc'] != '2')) $this->insertdata['payproc'] = 0;
						
						if (isset($this->endregdata['confirmcode']) && isset($this->endregdata['confirmid'])) 
							{
								$this->formreq2 = $this->Product_model->GetFormData($this->endregdata['confirmcode'], (int)$this->endregdata['confirmid']);
								if ($this->formreq2) $this->insertdata['fid'] = (int)$this->endregdata['confirmid'];							
							}

								if (isset($this->session->userdata['fedex']['quote']['to']))
								{
									$this->fedexquoteto = $this->session->userdata['fedex']['quote']['to'];
									foreach ($this->fedexquoteto as $fkeyt => $fvaluet) 
									{										
									if ($this->insertdata['send'] == $fvaluet['type']) 
										{
										$this->priceto = (float)$fvaluet['sum']+3;
										if ((float)$fvaluet['sum'] == 0) $this->priceto = 0;
										$this->txtto = 'E-mail me a Fedex '.CapsNClear(CleanInput($fvaluet['type'])).' Label - $'.sprintf("%.2f", $this->priceto);							
										}
									elseif ($this->insertdata['send'] == '0') 
										{
										$this->priceto = 0;	
										$this->txtto = 'I\'ll send it in - (No Charge)';
										}
									}
								}
								else
								{
								$this->priceto = 0;	
								$this->txtto = 'I\'ll send it in - (No Charge)';									
								}
									
									if (substr($this->insertdata['safebox'], 0, 5) == 'USPS_') 
									{
										if (isset($this->session->userdata['fedex']['uspsquote']['box']['result']))
										{
										$this->uspsquotebox = $this->session->userdata['fedex']['uspsquote']['box']['result'];
										foreach ($this->uspsquotebox as $fkeyx => $fvaluex) 
											{												
											if ($this->insertdata['safebox'] == $fvaluex['mailcode']) 
												{
												$this->pricebox = (float)$fvaluex['rate']+3;
												if ((float)$fvaluex['rate'] == 0) $this->pricebox = 0;
												$this->txtbox = 'Yes, send me a safebox with '.$fvaluex['mailservice'].'- $'.sprintf("%.2f", $this->pricebox);						
												}
											elseif ($this->insertdata['safebox'] == '0') 
												{
												$this->pricebox = 0;	
												$this->txtbox = 'I\'ll pack it - (No Charge)';											
												}
											}
										}
										else
										{
											$this->pricebox = 0;	
											$this->txtbox = 'I\'ll pack it - (No Charge)';	
										}										
									}
									else
									{
										if (isset($this->session->userdata['fedex']['quote']['box']))
										{
										$this->fedexquotebox = $this->session->userdata['fedex']['quote']['box'];
										foreach ($this->fedexquotebox as $fkeyx => $fvaluex) 
											{											
											if ($this->insertdata['safebox'] == $fvaluex['type']) 
												{
												$this->pricebox = (float)$fvaluex['sum']+3;
												if ((float)$fvaluex['sum'] == 0) $this->pricebox = 0;
												$this->txtbox = 'Yes, send me a safebox with Fedex '.CapsNClear(CleanInput($fvaluex['type'])).' Shipping - $'.sprintf("%.2f", $this->pricebox);							
												}
											elseif ($this->insertdata['safebox'] == '0') 
												{
												$this->pricebox = 0;	
												$this->txtbox = 'I\'ll pack it - (No Charge)';
											
												}
											}
										}
										else
										{
										$this->pricebox = 0;	
										$this->txtbox = 'I\'ll pack it - (No Charge)';											
										}
									}
									
									if (substr($this->insertdata['return'], 0, 5) == 'USPS_') 
									{
										if (isset($this->session->userdata['fedex']['uspsquote']['back']['result'])){
										$this->uspsquoteback = $this->session->userdata['fedex']['uspsquote']['back']['result'];
										foreach ($this->uspsquoteback as $fkeyb => $fvalueb) 
										{										
										if ($this->insertdata['return'] == $fvalueb['mailcode']) 
											{
											$this->priceback = (float)$fvalueb['rate']+3;
											if ((float)$fvalueb['rate'] == 0) $this->priceback = 0;
											$this->txtback = 'Return with '.$fvalueb['mailservice'].' - $'.sprintf("%.2f", $this->priceback);							
											}										
										}}										
									}
									else
									{
										if (isset($this->session->userdata['fedex']['quote']['back'])){
										$this->fedexquoteback = $this->session->userdata['fedex']['quote']['back'];
										foreach ($this->fedexquoteback as $fkeyb => $fvalueb) 
										{										
										if ($this->insertdata['return'] == $fvalueb['type']) 
											{
											$this->priceback = (float)$fvalueb['sum']+3;
											if ((float)$fvalueb['sum'] == 0) $this->priceback = 0;
											$this->txtback = 'Return with Fedex '.CapsNClear(CleanInput($fvalueb['type'])).' Shipping - $'.sprintf("%.2f", $this->priceback);		
											}										
										}}
									}
						
						$this->insertdata['courier_log'] = serialize ($this->session->userdata['fedex']);
						$this->insertdata['endprice'] = sprintf("%.2f", ($this->priceto+$this->pricebox+$this->priceback));
						$this->insertdata['endprice_delivery'] = sprintf("%.2f", $this->insertdata['endprice']);
						$this->insertdata['order'] = $this->txtto.'<br><br>'.$this->txtbox.'<br><br>'.$this->txtback;	
						
						if ($this->insertdata['freeship'] == '1') $this->insertdata['order'] .='<br><br>Get free shipping';

						$this->insertdata['buytype'] = '3';
						$this->insertdata['status'] = serialize(array(array('status' => 1, 'comment' => '', 'notified' => 1, 'time' => CurrentTime())));
						$this->insertdata['time'] = CurrentTime();
						$this->insertdata['comments'] = '<strong>Brand:</strong> '.$this->endregdata['brand'].'<br><strong>Model:</strong> '.$this->endregdata['model'].'<br><strong>Problem:</strong> '.$this->endregdata['item'];
						
						unset($this->insertdata['send']);
						unset($this->insertdata['safebox']);
						unset($this->insertdata['return']);
						unset($this->insertdata['freeship']);
						$this->session->unset_userdata('fedex'); 
						$this->session->unset_userdata('regdata');
						
						if ($this->insertdata['endprice'] > 0) {
						$this->load->helper('arithmetic');
						$this->insertdata['code'] = rand_string(50);
						}
						
								$this->db->trans_start();
								$this->Product_model->InsertOrder($this->insertdata);
								$this->insertdata['oid'] = $this->db->insert_id();
								$this->db->trans_complete();
								$this->_MailClientReciept($this->insertdata);
								$this->history_data = $this->_MailAdminReciept($this->insertdata);
								
								$this->load->model('Login_model');
								$this->Login_model->InsertHistoryData($this->history_data);
								
								if (!isset($this->session->userdata['user_id'])) 
								{									
								$this->_RegisterNewCustomer('RepairForm', $this->endregdata);
								$this->insertdata['generic'] = TRUE;
								}
								$this->session->set_flashdata('clientdata', $this->insertdata);
								redirect("/MakePayment/".$this->insertdata['oid']."/".$this->insertdata['code']); exit();
			}
	else 
			{
			echo 'Ooops, there has been an error in the site. Would you please back up one page, and go to the "contacts area" and send the administrator a message. Thanks in advance';
			exit();
			}
}


function CheckOutDelivery() {
		
		if (!isset($this->session->userdata['fedex']['quote']['back']) && !isset($this->session->userdata['fedex']['uspsquote']['back'])){$this->session->keep_flashdata('clientdata');redirect('CheckOut');}	
		if (!isset($this->session->userdata['cart'])) { Redirect(""); exit(); }
		
		
			$this->load->model('Menus_model');	
	$this->Menus_model->GetStructure();		
	$this->Product_model->GetStructure();
	
		$this->clientdata = $this->session->flashdata('clientdata');
		
		$this->load->library('form_validation');	
		$this->form_validation->set_rules('delivery', 'Delivery Options', 'trim|required|xss_clean');

     if ($this->form_validation->run() == FALSE)
			{	
				$this->mysmarty->assign('productview', 'checkout1');
				$this->mysmarty->assign('step', '2');
				
				$this->clientdata['delivery'] = $this->input->post('delivery', TRUE);				
				
				$this->session->keep_flashdata('clientdata');
				
				$this->refreshedcard = $this->_RefreshCart();
				$this->mysmarty->assign('order', $this->refreshedcard['order']);
				$this->mysmarty->assign('total', $this->refreshedcard['total']);
				
				$this->mysmarty->assign('imgproductpath', $this->config->config['wwwpath']['imgproducts']);
				$this->mysmarty->assign('regdata', $this->clientdata);
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('welcome/welcome_main.html');
			}
			else 
			{  
			$this->clientdata['delivery'] = $this->form_validation->set_value('delivery');
				
			if (substr($this->clientdata['delivery'], 0, 5) == 'USPS_') 
			{
				$this->uspsquote = $this->session->userdata['fedex']['uspsquote']['back']['result'];	
			
					foreach ($this->uspsquote as $fkey => $fvalue) 
					{								
						if ($this->clientdata['delivery'] == $fvalue['mailcode']) $this->clientdata['calcedprice'][0] = (float)$fvalue['rate']+3;
					}
			}
			else
			{			
				$this->fedexquote = $this->session->userdata['fedex']['quote']['back'];	
			
					foreach ($this->fedexquote as $fkey => $fvalue) 
					{								
						if ($this->clientdata['delivery'] == $fvalue['type']) $this->clientdata['calcedprice'][0] = (float)$fvalue['sum']+3;
					}	
			}
					
			$this->session->set_flashdata('clientdata', $this->clientdata);
		
			redirect("ProcessCheckOut");
			exit;	
			}
	
}
function ProcessCheckOut() {		
		if (!isset($this->session->userdata['fedex']['quote']['back']) && !isset($this->session->userdata['fedex']['uspsquote']['back'])){$this->session->keep_flashdata('clientdata');redirect('CheckOut');}		
		if (!isset($this->session->userdata['cart'])) { Redirect(""); exit(); }
		
		$this->clientdata = $this->session->flashdata('clientdata'); 
				
		if (!isset($this->clientdata['calcedprice'][0])) redirect('CheckOut');
				
			$missing = 0;
		if (!isset($this->clientdata['FirstName'])) $missing++;
		if (!isset($this->clientdata['LastName'])) $missing++;
		if (!isset($this->clientdata['Address'])) $missing++;
		if (!isset($this->clientdata['City'])) $missing++;
		if (!isset($this->clientdata['State'])) $missing++;
		if (!isset($this->clientdata['PostCode'])) $missing++;
		if (!isset($this->clientdata['same'])) $missing++;
		
		if ((int)$missing > 0)
		{
		$this->session->set_flashdata('nosess', $missing);
		redirect('CheckOut');
		}
		
		$this->load->library('form_validation');	
		$this->form_validation->set_rules('payproc', 'Method of payment', 'trim|required|integer|xss_clean');
		
	     if ($this->form_validation->run() == FALSE)
			{	
				$this->mysmarty->assign('productview', 'checkout1');
				$this->mysmarty->assign('step', '3');
		
				$this->clientdata['payproc'] = (int)$this->input->post('payproc', TRUE);	
				
				$this->refreshedcard = $this->_RefreshCart();
				$this->mysmarty->assign('order', $this->refreshedcard['order']);
				$this->mysmarty->assign('total', $this->refreshedcard['total']);
				
				$this->mysmarty->assign('regdata', $this->clientdata);
				
				$this->clientdata['total'] = $this->refreshedcard['total'];
				
				$this->session->set_flashdata('clientdata', $this->clientdata);				
							
				$this->mysmarty->assign('imgproductpath', $this->config->config['wwwpath']['imgproducts']);							
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('welcome/welcome_main.html');
			}
			else 
			{      	
				
					$this->insertdata = array(
											 'buytype' => '1',
											 'status' => serialize(array(array('status' => 1, 'comment' => '', 'notified' => 1, 'time' => CurrentTime()))),
											 'fname' => $this->clientdata['FirstName'],
										     'lname' => $this->clientdata['LastName'],
										     'address' => $this->clientdata['Address'],	
										     'city' => $this->clientdata['City'],
										     'state' => $this->clientdata['State'],
											 'postcode' => $this->clientdata['PostCode'],
											 'country' => $this->clientdata['Country'],											 
										     'tel' => $this->clientdata['Telephone'],
   										     'email' => $this->clientdata['Email'],
  										     'comments' => $this->clientdata['comments'],
											 'order' => serialize($this->session->userdata['cart']),
											 'endprice' => sprintf("%.2f", (float)$this->clientdata['total']),
											 'totalweight' => (float)$this->clientdata['cartweight'],
											 'time' => CurrentTime(),											 
											  );

						
						if (substr($this->clientdata['delivery'], 0, 5) == 'USPS_') 
						{
							$this->uspsquote = $this->session->userdata['fedex']['uspsquote']['back']['result'];	
								foreach ($this->uspsquote as $fkeyb => $fvalueb) 
									{										
									if ($this->clientdata['delivery'] == $fvalueb['mailcode']) 
										{
										$this->insertdata['endprice_delivery'] = sprintf("%.2f", ((float)$fvalueb['rate']+3));
										if ((float)$fvalueb['rate'] == 0) $this->insertdata['endprice_delivery'] = 0;
										$this->insertdata['delivery'] = $fvalueb['mailservice'].' - $'.sprintf("%.2f", $this->insertdata['endprice_delivery']);							
										}										
									}
						}
						else
						{
							$this->fedexquoteback = $this->session->userdata['fedex']['quote']['back'];
								foreach ($this->fedexquoteback as $fkeyb => $fvalueb) 
									{										
									if ($this->clientdata['delivery'] == $fvalueb['type']) 
										{
										$this->insertdata['endprice_delivery'] = sprintf("%.2f", (float)$fvalueb['sum']+3);
										if ((float)$fvalueb['sum'] == 0) $this->insertdata['endprice_delivery'] = 0;
										$this->insertdata['delivery'] = 'Fedex '.CapsNClear(CleanInput($this->clientdata['delivery'])).' shipping - $'.sprintf("%.2f", $this->insertdata['endprice_delivery']);							
										}										
									}
						}
					$this->insertdata['courier_log'] = serialize($this->session->userdata['fedex']);					
		///////////////////////////////////////////////////////////////			
		//			$this->insertdata['endprice'] = 1;
		//			$this->insertdata['endprice_delivery'] = 0;
		///////////////////////////////////////////////////////////////			
					if ((int)$this->clientdata['same'] == 0) {
						
						$this->insertdata['daddress'] = $this->clientdata['dAddress'];
				     	$this->insertdata['dcity'] = $this->clientdata['dCity'];
						$this->insertdata['dstate'] = $this->clientdata['dState'];
						$this->insertdata['dpostcode'] = $this->clientdata['dPostCode'];
						$this->insertdata['dcountry'] = $this->clientdata['dCountry'];
					}
					else
					{
						$this->insertdata['daddress'] = $this->clientdata['Address'];
				     	$this->insertdata['dcity'] = $this->clientdata['City'];
						$this->insertdata['dstate'] = $this->clientdata['State'];
						$this->insertdata['dpostcode'] = $this->clientdata['PostCode'];
						$this->insertdata['dcountry'] = $this->clientdata['Country'];
					}
					$this->clientdata['payproc'] = $this->form_validation->set_value('payproc');
					if (((int)$this->clientdata['payproc'] != '1') && ((int)$this->clientdata['payproc'] != '2')) $this->insertdata['payproc'] = 0;
					else $this->insertdata['payproc'] = (int)$this->clientdata['payproc'];
					
					if ($this->insertdata['endprice'] > 0) {
						$this->load->helper('arithmetic');
						$this->insertdata['code'] = rand_string(50);
						}					
					
					$this->db->trans_start();
					$this->Product_model->InsertOrder($this->insertdata);
					$this->insertdata['oid'] = $this->db->insert_id();
					$this->db->trans_complete();
					$this->_MailClientReciept($this->insertdata);
					$this->history_data = $this->_MailAdminReciept($this->insertdata);
					$this->session->unset_userdata('cart');
					$this->session->unset_userdata('fedex');
					
					$this->load->model('Login_model');
					$this->Login_model->InsertHistoryData($this->history_data);
					if (!isset($this->session->userdata['user_id'])) 
								{									
								$this->_RegisterNewCustomer('', $this->clientdata);	
								$this->insertdata['generic'] = TRUE;
								}
					$this->session->set_flashdata('clientdata', $this->insertdata);
					redirect("/OrderComplete/".$this->insertdata['oid']); exit();
	}
	
}
function OrderComplete($id)
	{	
		if ((int)$id == 0) {Redirect(""); exit();}
		$this->clientdata = $this->session->flashdata('clientdata');
		if ($this->clientdata == '') {Redirect(""); exit();}
		
		if (count($this->clientdata) < 2) {redirect(""); exit();}
		
		$this->chkorder = $this->Product_model->CheckValidOrder((int)$id, $this->clientdata, $this->clientdata['code']);
		if (!$this->chkorder) {Redirect(""); exit();}
		
		if (isset($this->clientdata['generic'])) $this->mysmarty->assign('generic', TRUE);
		
		if ($this->chkorder['payproc'] == '2') 
		{
		$this->mysmarty->assign('form', $this->_PaypalFormData($this->chkorder, TRUE));		
		}
		else
		{
		$this->mysmarty->assign('form', $this->_AuthorizeNetFormData($this->chkorder, TRUE));	
		}
		
		if ($this->clientdata['buytype'] == '1') {
			$this->clientdata['order'] = unserialize($this->clientdata['order']);
			$this->_TakeQuantity();	
		}
		
			$this->mysmarty->assign('productview', 'checkout2');
		
		$this->mysmarty->assign('clientdata', $this->clientdata);
		$this->mysmarty->view('welcome/welcome_main.html');	
	}

