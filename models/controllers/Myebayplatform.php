<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Myebayplatform extends Controller 
{
        function Myebayplatform()
	        {
		        parent::Controller();
		        $this->_Start();
		        $this->mysmarty->assign('area', 'Platform');

   	        }

 
	
        function index()
	    {	
		
			ini_set('memory_limit','1024M');
	set_time_limit(600);
		ini_set('mysql.connect_timeout', 600);
		ini_set('max_execution_time', 600);  
		ini_set('default_socket_timeout', 600);
		
		$this->db->order_by('nid','DESC');
		        $n = $this->db->get('ebay_notifications');

		        if ($n->num_rows() > 0)
		        { 
			        foreach ($n->result_array() as $nn)
			        {
    		            foreach ($nn as $kkk => $nnn)
			            {
	    	                if ($kkk == 'notification')
			                {	
		                        $responseArray = json_decode(json_encode(simplexml_load_string(preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $nnn))),true);	
			
			                    $data = $responseArray['soapenvBody'];								
                                //printcool($data);
			                    if (isset($data['GetItemTransactionsResponse']))
			                    {	
				                    $checklistings[$data['GetItemTransactionsResponse']['Item']['ItemID']] = true;
				                    foreach($data['GetItemTransactionsResponse']['TransactionArray'] as $t)
				                    {
					                    $mass[] = array('type' => $data['GetItemTransactionsResponse']['NotificationEventName'], 'data' => $t);
					                    $checktransaction[$t['ShippingDetails']['SellingManagerSalesRecordNumber']]=true;
				                    }				
			                    }			            
			                    elseif (isset($data['GetItemResponse']))
			                    {
				                    $mass[] = array('type' => $data['GetItemResponse']['NotificationEventName'], 'data'=> $data['GetItemResponse']['Item']);
				                    $checklistings[$data['GetItemResponse']['Item']['ItemID']] = true;	
			                    }
                                elseif (isset($data['GetItemSold']))
                                {
									$mass[] = array('type' => $data['GetItemResponse']['NotificationEventName'], 'data'=> $data['GetItemResponse']['Item']);
                                    $checklistings[$data['GetItemResponse']['Item']['ItemID']] = true;
                                }
                                
                                elseif($data['NotificationEvent']['NotificationEventName']=='ReturnCreated')
                                {
                                    $mass[] = array('type' => $data['NotificationEvent']['NotificationEventName'], 'data'=> $data['NotificationEvent']);
                                }
								 elseif($data['NotificationEvent']['NotificationEventName']=='ReturnDelivered')
                                {
                                    $mass[] = array('type' => $data['NotificationEvent']['NotificationEventName'], 'data'=> $data['NotificationEvent']);
                                }
								 elseif($data['NotificationEvent']['NotificationEventName']=='ReturnShipped')
                                {
                                    $mass[] = array('type' => $data['NotificationEvent']['NotificationEventName'], 'data'=> $data['NotificationEvent']);                                }
								elseif($data['NotificationEvent']['NotificationEventName']=='ReturnRefundOverdue')
                                {
                                    $mass[] = array('type' => $data['NotificationEvent']['NotificationEventName'], 'data'=> $data['NotificationEvent']);
                                }
								elseif($data['NotificationEvent']['NotificationEventName']=='ReturnClosed')
                                {
                                    $mass[] = array('type' => $data['NotificationEvent']['NotificationEventName'], 'data'=> $data['NotificationEvent']);
                                }
								elseif (isset($data['NotificationEvent']) && $data['NotificationEvent']['NotificationEventName'])
			                    {
				                    $mass[] = array('type' => 'NotificationEvent', 'data'=> $data['NotificationEvent']);                    	
			                    }							
                                 else
			                    {
				                    if (is_array($responseArray))  $mass[] = array('type' => 'NotYetDefined', 'data'=> $responseArray['soapenvBody']);	
									
			                    }
			                }
			            }
			        }
    	        }//if
		

           //   exit();

		        $c = 1;
		        foreach($checklistings as $k => $v)
		        {
			        if ($c ==1) $this->db->where('ebay_id', $k);
			        else $this->db->or_where('ebay_id', $k);			
			        $c++;
		        }
                //printcool($checklistings);
        
                //$this->db->where_in('ebay_id',$checklistings);

		        $e = $this->db->get('ebay');
		        if ($e->num_rows() > 0)
		        {
                    foreach ($e->result_array() as $eb)
			        {
				        $ourlistings[$eb['ebay_id']] = $eb;	
			        }
		        }
   
		        $c = 1;
		        foreach($checktransaction as $k => $v)
		        {
			        if ($c ==1) $this->db->where('rec', $k);
			        else $this->db->or_where('rec', $k);			
			        $c++;
		        }
		        $e = $this->db->get('ebay_transactions');
		        if ($e->num_rows() > 0)
		        {
			        foreach ($e->result_array() as $eb)
			        {
				        $ourtransactions[$eb['rec']] = $eb;	
			        }
		        }
		        //printcool ($mass);
		        //printcool ($checklistings);
		        //printcool ($checktransaction);
		        //exit();
		         echo '<table border="1">';
		
                foreach ($mass as $k => $v)
		        {
			        echo '<tr><th valign="top" colspan="2">'.$k.' - '.$v['type'].'</th></tr><tr>';
	                    if ($v['type'] == 'FixedPriceTransaction')
                        {
                             $rec = $v['data']['ShippingDetails']['SellingManagerSalesRecordNumber'];
                             echo '<td valign="top"  style="background:#CEFFC3;">';
                             if (isset($ourtransactions[$rec]))
                              echo '<strong style="color:green">Exists in DB from Cron</strong>';
                             else 
                             echo '<strong style="color:red">Does not exist localy</strong>';
							 echo '<br><strong>Record:</strong> '.$v['data']['ShippingDetails']['SellingManagerSalesRecordNumber'];
     						 echo '<br><strong>Buyer</strong>: '.$v['data']['Buyer']['Email'];
                             if (isset($ourtransactions[$rec]))
                             {
                              if ($ourtransactions[$rec]['buyeremail'] == $v['data']['Buyer']['Email']) echo ' <strong style="color:green">Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourtransactions[$rec]['buyeremail'].'</strong>';
                             }
                             echo '<br><strong>AmountPaid</strong>: '.$v['data']['AmountPaid'];

                             if (isset($ourtransactions[$rec]))
                             {
                              if ($ourtransactions[$rec]['paid'] == $v['data']['ConvertedAmountPaid']) echo ' <strong style="color:green">Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourtransactions[$rec]['paid'].'</strong>';
                             }
                             echo '<br><strong>ConvertedAmountPaid</strong>: '.$v['data']['ConvertedAmountPaid'];
                             echo '<br><strong>ConvertedTransactionPrice</strong>: '.$v['data']['ConvertedTransactionPrice'];
                             if (isset($ourtransactions[$rec]))
                             {
                              if ($ourtransactions[$rec]['eachpaid'] == $v['data']['ConvertedTransactionPrice']) echo ' <strong style="color:green">Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourtransactions[$rec]['eachpaid'].'</strong>';
                             }      
                             echo '<br><strong>CreatedDate</strong>: '.CleanBadDate($v['data']['CreatedDate']);
                             if (isset($ourtransactions[$rec]))
                             {
                              if ($ourtransactions[$rec]['datetime'] == CleanBadDate($v['data']['CreatedDate'])) echo ' <strong style="color:green">Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourtransactions[$rec]['datetime'].'</strong>';
                             }
                             echo '<br><strong>QuantityPurchased</strong>: '.$v['data']['QuantityPurchased'];

                             if (isset($ourtransactions[$rec]))
                             {
                              if ($ourtransactions[$rec]['qty'] == $v['data']['QuantityPurchased']) echo ' <strong style="color:green">Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourtransactions[$rec]['qty'].'</strong>';
                             }
                             echo '<br><strong>TransactionID</strong>: '.$v['data']['TransactionID'];
                             if (isset($ourtransactions[$rec]))
                             {
                              if ($ourtransactions[$rec]['transid'] == $v['data']['TransactionID']) echo ' <strong style="color:green">Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourtransactions[$rec]['transid'].'</strong>';
                             }
                             echo '<br><strong>TransactionPrice</strong>: '.$v['data']['TransactionPrice'];
                             if (isset($ourtransactions[$rec]))
                             {
                              if ($ourtransactions[$rec]['eachpaid'] == $v['data']['TransactionPrice']) echo ' <strong style="color:green">Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourtransactions[$rec]['eachpaid'].'</strong>';
                             }
                             echo '<br><strong>FinalValueFee</strong>: '.$v['data']['FinalValueFee'];
                             if (isset($ourtransactions[$rec]))
                             {
                              if ($ourtransactions[$rec]['fee'] == $v['data']['FinalValueFee']) echo ' <strong style="color:green">Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourtransactions[$rec]['fee'].'</strong>';
                             }
                             if (isset($v['data']['ActualShippingCost'])) 
                             {      
                              echo '<br><strong>ActualShippingCost</strong>: '.$v['data']['ActualShippingCost'];
                              if (isset($ourtransactions[$rec]))
                              {
                                if ($ourtransactions[$rec]['asc'] == $v['data']['ActualShippingCost']) echo ' <strong style="color:green">Same in DB</strong>';
                                else echo ' <strong style="color:red">'.$ourtransactions[$rec]['asc'].'</strong>';      
                              }
                             }
                             if (isset($v['data']['ActualHandlingCost'])) echo '<br><strong>ActualHandlingCost</strong>: '.$v['data']['ActualHandlingCost'];
  
                             echo '<br>ShippingServiceSelected: ';
                             foreach ($v['data']['ShippingServiceSelected'] as $kk => $vv) echo '<strong>'.$kk.':</strong> '.$vv.' | ';
     
                             if (isset($v['data']['MonetaryDetails']))
                             {
                               echo '<br>MonetaryDetails: ';
                               foreach ($v['data']['MonetaryDetails']['Payments']['Payment'] as $kk => $vv) echo '<strong>'.$kk.':</strong> '.$vv.' | ';
                             }
                             if (isset($v['data']['ExternalTransaction']))
                             {
                               echo '<br>ExternalTransaction: ';
                               foreach ($v['data']['ExternalTransaction'] as $kk => $vv) echo '<strong>'.$kk.':</strong> '.$vv.' | ';
                             }
                             if (isset($v['data']['Status']))
                             {
                               echo '<br>Status: ';
                               foreach ($v['data']['Status'] as $kk => $vv) echo '<strong>'.$kk.':</strong> '.$vv.' | ';
                             }    
                        }
                        elseif ($v['type'] == 'ItemRevised')
                        {
                              echo '<td valign="top"  style="background:#FFE8A7;">';
                              echo '<br>ItemID: '.$v['data']['ItemID'];
                              $itemid = $v['data']['ItemID'];
     
                             if (isset($ourlistings[$itemid]))  echo '<strong style="color:green">Exists in DB from Cron</strong>';
                             else echo '<strong style="color:red">Does not exist localy</strong>';        

                             //Replace your row with row below
                             echo '<br><strong>ConvertedStartPrice</strong>: '.$v['data']['ListingDetails']['ConvertedStartPrice'];

                             if (isset($ourlistings[$itemid]))
                             {
                               //Replace your row with row below
                               if ($ourlistings[$itemid]['price_ch1'] == $v['data']['ListingDetails']['ConvertedStartPrice']) echo ' <strong style="color:green"> Same in DB</strong>';
                               else echo ' <strong style="color:red">'.$ourlistings[$itemid]['price_ch1'].'</strong>';
                             }     
                             echo '<br><strong>Title</strong>: '.$v['data']['Title'];          
                             if (isset($ourlistings[$itemid]))
                             {
                              if ($ourlistings[$itemid]['e_title'] == $v['data']['Title']) echo ' <strong style="color:green"> Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourlistings[$itemid]['e_title'].'</strong>';
                             }      
                             echo '<br><strong>Quantity</strong>: '.$v['data']['Quantity'];          
                             if (isset($ourlistings[$itemid]))
                             {
                              if ($ourlistings[$itemid]['qn_ch1'] == $v['data']['Quantity']) echo ' <strong style="color:green"> Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourlistings[$itemid]['qn_ch1'].'</strong>';
                             }     
                             echo '<br><strong>OutOfStockControl</strong>: '.$v['data']['OutOfStockControl'];          
                             if (isset($ourlistings[$itemid]))
                             {
                             if ($ourlistings[$itemid]['ooskeepalive'] == 1 && (string)$v['data']['OutOfStockControl'] == 'true') echo ' <strong style="color:green"> Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourlistings[$itemid]['ooskeepalive'].'</strong>';
                             }      
                             echo '<br><strong>ConditionID</strong>: '.$v['data']['ConditionID'];          
                             if (isset($ourlistings[$itemid]))
                             {
                              if ($ourlistings[$itemid]['Condition'] == $v['data']['ConditionID']) echo ' <strong style="color:green"> Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourlistings[$itemid]['Condition'].'</strong>';
                             } 
                              //Replace your row with row below     
                             if(isset($v['data']['SellingStatus']['ListingStatus']))
                            //Replace your row with row below
                              echo '<br><strong>ListingStatus</strong>: '.$v['data']['SellingStatus']['ListingStatus'];          
                   
                             if (isset($ourlistings[$itemid]))
                             {
                             //Replace your row with row below
                             if ($ourlistings[$itemid]['ebended'] == '' && $v['data']['SellingStatus']['ListingStatus'] == 'Active') echo ' <strong style="color:green">Same in DB</strong>';
                              elseif ($ourlistings[$itemid]['ebended'] != '' && $v['data']['SellingStatus']['ListingStatus'] == 'Complete') echo ' <strong style="color:green">Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourlistings[$itemid]['ebended'].'</strong>';
                             } 
                        }
                        elseif ($v['type'] == 'ItemSold' || $v['type'] == 'ItemUnsold' || $v['type'] == 'ItemClosed')
                        {					
                             $itemid = $v['data']['ItemID'];
                             echo '<td valign="top"  style="background:#FFE8A7;">';
                             echo '<br>ItemID: '.$v['data']['ItemID'];
                             if (isset($ourlistings[$itemid]))
                              echo ' <strong style="color:green">Exists in DB</strong>';
                             else 
                              echo ' <strong style="color:red">Does not exist localy</strong>';
      
                             if (strlen($ourlistings[$itemid]['ebended']) > 0)
                             {
                                echo ' <br><br><strong style="color:green">Ended in DB</strong>';
                             }
                             else
                             {
                                echo ' <br><br><strong style="color:red">Not Ended in DB</strong>';
                             }
     
                        }
                        elseif ($v['type'] == 'ReturnCreated')

                        {
                            echo '<td valign="top"  style="background:#FFBBF8;">';

                            echo '<br>ItemID: '.$v['data']['TransactionIdentity']['ItemId'];
                            echo '<br>TransactionID: '.$v['data']['TransactionIdentity']['TransactionId'];
printcool ($v['data']);
							$this->_GetReturnData((int)$v['data']['ReturnId']);
                               // echo '<br>Found Return Created';
                              
                        }						
						elseif ($v['type'] == 'ReturnShipped')

                        {
                            echo '<td valign="top"  style="background:#FFBBF8;">';

                            echo '<br>ItemID: '.$v['data']['TransactionIdentity']['ItemId'];
                            echo '<br>TransactionID: '.$v['data']['TransactionIdentity']['TransactionId'];
printcool ($v['data']);
                               // echo '<br>Found Return Created';
                              
                        }
						elseif ($v['type'] == 'ReturnDelivered')

                        {
                            echo '<td valign="top"  style="background:#FFBBF8;">';

                            echo '<br>ItemID: '.$v['data']['TransactionIdentity']['ItemId'];
                            echo '<br>TransactionID: '.$v['data']['TransactionIdentity']['TransactionId'];
printcool ($v['data']);
                               // echo '<br>Found Return Created';
                              
                        }
						elseif ($v['type'] == 'ReturnRefundOverdue')

                        {
                            echo '<td valign="top"  style="background:#FFBBF8;">';

                            echo '<br>ItemID: '.$v['data']['TransactionIdentity']['ItemId'];
                            echo '<br>TransactionID: '.$v['data']['TransactionIdentity']['TransactionId'];
printcool ($v['data']);
                               // echo '<br>Found Return Created';
                              
                        }
						elseif ($v['type'] == 'ReturnClosed')

                        {
                            echo '<td valign="top"  style="background:#FFBBF8;">';

                            echo '<br>ItemID: '.$v['data']['TransactionIdentity']['ItemId'];
                            echo '<br>TransactionID: '.$v['data']['TransactionIdentity']['TransactionId'];

                               // echo '<br>Found Return Created';
                          printcool ($v['data']);    
                        }
						
                        elseif ($v['type'] == 'NotificationEvent')
                        {
                            echo '<td valign="top"  style="background:#D7E7FF;">';

                            foreach ($v['data'] as $kk => $vv)
                            {
                              echo '<strong>'.$kk.':</strong>';
                              if (is_array($vv))
                              {
                                foreach ($vv as $kkk => $vvv)
                                {
                                    echo '<strong>'.$kkk.':</strong> '.$vvv.' | ';
                                }
                              }
                              else 
                              {
                                 if ($kk == 'CreationDate') echo CleanBadDate($vv).' | ';
                                         else echo $vv.' | ';
                              }
                            }							
                        }
						elseif ($v['type'] == 'ItemExtended')
						{
							//DO NOTHING, LISTING IS EXTENDED
						}
						elseif ($v['type'] == 'ItemMarkedShipped')
						{
							if (isset($v['data']['ShippingDetails']['SellingManagerSalesRecordNumber'])) $rec = $v['data']['ShippingDetails']['SellingManagerSalesRecordNumber'];
							else
							{
								 $this->load->model('Myebay_model');  
									$this->db->select('rec');
									$this->db->where('transid', (int)$transid);	
									$s = $this->db->get('ebay_transactions');
									if ($s->num_rows() == 0) $rec = false;
									else $ss = $s->row_array();
									$rec = $ss['rec'];	
							}
                             echo '<td valign="top"  style="background:#AEFFFD;">';
                             if (isset($ourtransactions[$rec]))
                              echo '<strong style="color:green">Exists in DB from Cron</strong>';
                             else 
                             echo '<strong style="color:red">Does not exist localy</strong>';
							
                             echo '<br><strong>FinalValueFee</strong>: '.$v['data']['FinalValueFee'];
                             if (isset($ourtransactions[$rec]))
                             {
                              if ($ourtransactions[$rec]['fee'] == $v['data']['FinalValueFee']) echo ' <strong style="color:green">Same in DB</strong>';
                              else echo ' <strong style="color:red">'.$ourtransactions[$rec]['fee'].'</strong>';
                             }
                             if (isset($v['data']['ActualShippingCost'])) 
                             {      
                              echo '<br><strong>ActualShippingCost</strong>: '.$v['data']['ActualShippingCost'];
                              if (isset($ourtransactions[$rec]))
                              {
                                if ($ourtransactions[$rec]['asc'] == $v['data']['ActualShippingCost']) echo ' <strong style="color:green">Same in DB</strong>';
                                else 
								{ 
									//echo ' <strong style="color:red">'.$ourtransactions[$rec]['asc'].'</strong>';
								//	$smasc = $this->GetSellingManagerSaleRecordRequest((int)$v['data']['TransactionID']);
									//if ($ourtransactions[$rec]['asc'] == $smasc) echo ' <strong style="color:00FF0D;">'.$smasc.'</strong>';
									//else echo ' <strong style="color:purple;">'.$smasc.'</strong>';
									
									}    
                              }
                             }
                             if (isset($v['data']['ActualHandlingCost'])) echo '<br><strong>ActualHandlingCost</strong>: '.$v['data']['ActualHandlingCost'];
  
                             echo '<br>ShippingServiceSelected: ';
                             foreach ($v['data']['ShippingServiceSelected'] as $kk => $vv) echo '<strong>'.$kk.':</strong> '.$vv.' | ';
     	
						}
				        else
				        {
					        echo '<td valign="top"  style="background:#F9D2D3;">NEW DATATYPE';
					        printcool ($v);
				        }
			            echo '</td><td></td></tr>';
			        }
	
		        echo '</table>';
	}
	function Log($page = 0)
	{
		if ((int)$page > 0) $page = $page - 1;
		$this->db->limit(300, (int)$page*300);
		$this->db->order_by("ebpid", "DESC");
		$this->query = $this->db->get('ebay_push_log');		
			
			$this->countall = $this->db->count_all_results('ebay_push_log');
			$this->pages = ceil($this->countall/300);
			for ( $counter = 1; $counter <= $this->pages ; $counter++) 
			{
			$pagearray[] = $counter;
			}
		
		$result = false;
		if ($this->query->num_rows() > 0)  $result = $this->query->result_array();
		$this->mysmarty->assign('log', $result);
		$this->mysmarty->assign('pages', $pagearray);
		$this->mysmarty->assign('page', (int)$page);
		$this->mysmarty->view('myebay/myebay_pushlog.html');	
	}
    function _Start() 
    {
		    $this->load->model('Auth_model');
		    $this->Auth_model->VerifyAdmin();	
		    $this->mysmarty->assign('session',$this->session->userdata);
		    $this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		    $this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		    $this->go = DoGo($this->router->class, $this->router->method);	
		    $this->mysmarty->assign('go', $this->go);	
    }
    function GetSellingManagerSaleRecordRequest($transid=0)
    {
		        $this->load->model('Myebay_model');  
				$this->db->select('itemid');
				$this->db->where('transid', (int)$transid);	
				$s = $this->db->get('ebay_transactions');
				if ($s->num_rows() == 0) return 'No Match in table';
				else $ss = $s->row_array();
				
                require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');	

                require($this->config->config['ebaypath'].'get-common/keys.php');

                 $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
                                                        <GetSellingManagerSaleRecordRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                                                           <RequesterCredentials>
                                                             <eBayAuthToken>'.$userToken.'</eBayAuthToken>
                                                           </RequesterCredentials>
                                                          <ItemID>'.(int)$ss['itemid'].'</ItemID>
                                                          <TransactionID>'.(int)$transid.'</TransactionID>                                              
                                                       </GetSellingManagerSaleRecordRequest>';                                                     


                $verb = 'GetSellingManagerSaleRecord';
         
                $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		        $responseXml = $session->sendHttpRequest($requestXmlBody);
                
                if (stristr($responseXml, 'HTTP 404') || $responseXml == '') 
                {
                    echo '<Br>Error in GetSellingManagerSaleRecordRequest function!';
                }
                else
                {
                    $xml = simplexml_load_string($responseXml);
                    return $xml->SellingManagerSoldOrder->ActualShippingCost;
                    //printcool($xml);
                }
    }
	function _GetReturnData($returnid = 0)// = 5046058098
{

		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');	
        require($this->config->config['ebaypath'].'get-common/keys.php');

         $url = 'https://api.ebay.com/post-order/v2/return/'.(int)$returnid; //?fieldgroups=FULL
         //Setup cURL
         $header = array(
                        'Accept: application/json',
                        'Authorization: TOKEN '.$userToken,
                        'Content-Type: application/json',
                        'X-EBAY-C-MARKETPLACE-ID: EBAY-US'
                         );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 0);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            $this->_notify('Returns Curl error',curl_error($ch));
			return false;
        }
        curl_close($ch); 
        $data = (json_decode($response,true));	
		printcool ($data);
		
		$answer['currentType'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['currentType'])));
		$answer['type'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['type'])));
		$answer['reason'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['reason'])));
		$answer['comment'] = $data['summary']['creationInfo']['comments']['content'];
		$answer['returnQuantity'] = $data['summary']['creationInfo']['item']['returnQuantity'];
		$answer['ebayRefundAmount'] = $data['summary']['sellerTotalRefund']['estimatedRefundAmount']['value'];
		$answer['ebayreturntime'] = CleanBadDate($data['summary']['creationInfo']['creationDate']['value']);
		$answer['ebayreturnshipment'] = $data['detail']['returnShipmentInfo']['shippingLabelCost']['totalAmount']['value'];
		printcool ($answer);
		//return ($answer);
}
}