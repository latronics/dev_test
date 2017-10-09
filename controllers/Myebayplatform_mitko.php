<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Myebayplatform extends Controller {
function Myebayplatform()
	{
		parent::Controller();
		$this->_Start();
		$this->mysmarty->assign('area', 'Platform');
		
	}
	
function index()
	{	
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
			
			if (isset($data['GetItemTransactionsResponse']))
			{	
				$checklistings[$data['GetItemTransactionsResponse']['Item']['ItemID']] = true;
				foreach($data['GetItemTransactionsResponse']['TransactionArray'] as $t)
				{
					$mass[] = array('type' => $data['GetItemTransactionsResponse']['NotificationEventName'], 'data' => $t);
					$checktransaction[$t['ShippingDetails']['SellingManagerSalesRecordNumber']]=true;
				}
				
			}
			elseif (isset($data['NotificationEvent']))
			{
				$mass[] = array('type' => 'NotificationEvent', 'data'=> $data['NotificationEvent']);	
			}
			elseif (isset($data['GetItemResponse']))
			{
				$mass[] = array('type' => $data['GetItemResponse']['NotificationEventName'], 'data'=> $data['GetItemResponse']['Item']);
				$checklistings[$data['GetItemResponse']['Item']['ItemID']] = true;	
			}
			elseif (isset($data['GetItemSold']) || isset($data['GetItemUnsold']) || isset($data['GetItemClosed']))
            {
	            $mass[] = array('type' => $data['GetItemResponse']['NotificationEventName'], 'data'=> $data['GetItemResponse']['Item']);
	            $checklistings[$data['GetItemResponse']['Item']['ItemID']] = true;	
            }
			else
			{
				if (is_array($responseArray)) $mass[] = array('type' => 'NotYetDefined', 'data'=> $responseArray['soapenvBody']);	
			}
			}
			
			}
			}

		}
		
		$c = 1;
		foreach($checklistings as $k => $v)
		{
			if ($c ==1) $this->db->where('ebay_id', $k);
			else $this->db->or_where('ebay_id', $k);			
			$c++;
		}
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
        
                     echo '<br><strong>ConvertedStartPrice</strong>: '.$v['data']['ConvertedStartPrice'];          
                     if (isset($ourlistings[$itemid]))
                     {
                      if ($ourlistings[$itemid]['price_ch1'] == $v['data']['ConvertedStartPrice']) echo ' <strong style="color:green">Same in DB</strong>';
                      else echo ' <strong style="color:red">'.$ourlistings[$itemid]['price_ch1'].'</strong>';
                     }     
                     echo '<br><strong>Title</strong>: '.$v['data']['Title'];          
                     if (isset($ourlistings[$itemid]))
                     {
                      if ($ourlistings[$itemid]['e_title'] == $v['data']['Title']) echo ' <strong style="color:green">Same in DB</strong>';
                      else echo ' <strong style="color:red">'.$ourlistings[$itemid]['e_title'].'</strong>';
                     }      
                     echo '<br><strong>Quantity</strong>: '.$v['data']['Quantity'];          
                     if (isset($ourlistings[$itemid]))
                     {
                      if ($ourlistings[$itemid]['qn_ch1'] == $v['data']['Quantity']) echo ' <strong style="color:green">Same in DB</strong>';
                      else echo ' <strong style="color:red">'.$ourlistings[$itemid]['qn_ch1'].'</strong>';
                     }     
                     echo '<br><strong>OutOfStockControl</strong>: '.$v['data']['OutOfStockControl'];          
                     if (isset($ourlistings[$itemid]))
                     {
                      if ($ourlistings[$itemid]['ooskeepalive'] == $v['data']['OutOfStockControl']) echo ' <strong style="color:green">Same in DB</strong>';
                      else echo ' <strong style="color:red">'.$ourlistings[$itemid]['ooskeepalive'].'</strong>';
                     }      
                     echo '<br><strong>ConditionID</strong>: '.$v['data']['ConditionID'];          
                     if (isset($ourlistings[$itemid]))
                     {
                      if ($ourlistings[$itemid]['Condition'] == $v['data']['ConditionID']) echo ' <strong style="color:green">Same in DB</strong>';
                      else echo ' <strong style="color:red">'.$ourlistings[$itemid]['Condition'].'</strong>';
                     }      
                     echo '<br><strong>ListingStatus</strong>: '.$v['data']['ListingStatus'];          
                     if (isset($ourlistings[$itemid]))
                     {
                      if ($ourlistings[$itemid]['ebended'] == '' && $v['data']['ListingStatus'] == 'Active') echo ' <strong style="color:green">Same in DB</strong>';
                      elseif ($ourlistings[$itemid]['ebended'] != '' && $v['data']['ListingStatus'] == 'Complete') echo ' <strong style="color:green">Same in DB</strong>';
                      else echo ' <strong style="color:red">'.$ourlistings[$itemid]['ebended'].'</strong>';
                     } 
                }
				elseif ($v['type'] == 'ItemSold' || $v['type'] == 'ItemUnsold' || $v['type'] == 'ItemClosed')
				{
					$itemid = $v['data']['ItemID'];
					echo '<td valign="top"  style="background:#FFE8A7;">';
					
					if ($v['type'] == 'ItemSold') echo '<strong style="color:green;">SOLD</strong>';
					if ($v['type'] == 'ItemUnsold') echo '<strong style="color:red;">UNSOLD</strong>';
					if ($v['type'] == 'ItemClosed') echo '<strong style="color:blue;">CLOSED</strong>';
					echo '<br>ItemID: '.$v['data']['ItemID'];
					if (isset($ourlistings[$itemid]))
						echo ' <strong style="color:green">Exists in DB</strong>';
					else 
						echo ' <strong style="color:red">Does not exist localy</strong>';
						
					if (strlen($ourlistings[$itemid]['ebenden']) > 0) 	 echo ' <br><br><strong style="color:green">Ended in DB</strong>';
					else echo ' <br><br><strong style="color:red">Not Ended in DB</strong>';
					
					echo '<br><strong>Quantity</strong>: '.$v['data']['Quantity'];										
					if (isset($ourlistings[$itemid]))
					{
						if ($ourlistings[$itemid]['Quantity'] == $v['data']['qn_ch1']) echo ' <strong style="color:green">Same in DB</strong>';
						else echo ' <strong style="color:red">'.$ourlistings[$itemid]['qn_ch1'].'</strong>';
					}		
						
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
				else 
				{
					echo '<td valign="top"  style="background:#F9D2D3;">';
					printcool ($v);
				}
			echo '</td><td></td></tr>';
			}
			
		echo '</table>';
		
	}
function _Start() {
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();	
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->go = DoGo($this->router->class, $this->router->method);	
		$this->mysmarty->assign('go', $this->go);	
	}
}