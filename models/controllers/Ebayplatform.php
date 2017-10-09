<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ebayplatform extends Controller 
{
        function Ebayplatform()
	        {
		        parent::Controller();
				$this->load->model('Myautopilot_model');
				$this->load->model('Myebay_model');
				$this->load->helper('explore');	
   	        }

 

	
        function index()
	    {						
			if (!isset($_POST['ebayrawdata']))  { $this->_notify('ebayrawdata - Not Present',$_POST); exit();}
			if (!isset($_POST['seccode']) || (isset($_POST['seccode']) && $_POST['seccode'] != 'B9UG^*vUOZ364nhHtxrvKy3lE^DX%]Q3&;DB9'))  { $this->_notify('seccode - Incorrect',$_POST); exit();}
			
			
			
                $responseArray = json_decode(json_encode(simplexml_load_string(preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $_POST['ebayrawdata']))),true);	
				
				if (!isset($responseArray['soapenvBody'])) { $this->_notify('soapenvBody - Not Present',$responseArray); exit();}
            
			$this->load->model('Myseller_model');
		$this->load->model('Auth_model');
		
			//$this->_notify('Push Start',''); //exit();
			  
			    $data = $responseArray['soapenvBody'];								
                if (isset($data['GetItemTransactionsResponse']))
			    {	
				  	$checklistings[$data['GetItemTransactionsResponse']['Item']['ItemID']] = true;
					$item = $data['GetItemTransactionsResponse']['Item'];
					foreach($data['GetItemTransactionsResponse']['TransactionArray'] as $t)
				    {
						$t['Item'] = $item;
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
                   $mass[] = array('type' => $data['NotificationEvent']['NotificationEventName'], 'data'=> $data['NotificationEvent']);                }
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
			 
		

           //   exit();
				
				if (isset($checklistings))
				{
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
				}
				if (isset($checktransaction))
				{
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
				}
		         //echo '<table border="1">';
		
                foreach ($mass as $k => $v)
		        {
			       // echo '<tr><th valign="top" colspan="2">'.$k.' - '.$v['type'].'</th></tr><tr>';
				 // if ($v['type'] != 'ItemExtended' && $v['type'] != 'ItemMarkedShipped') $this->_notify('Push Process Start: '.$v['type'],$v['data']);	
	                    if ($v['type'] == 'FixedPriceTransaction')
                        {
                             $rec = $v['data']['ShippingDetails']['SellingManagerSalesRecordNumber'];
                       
							$insert = false;
								$tmpdate =  explode (' ', CleanBadDate($v['data']['CreatedDate']));
								$date = explode('-', trim($tmpdate[0]));
								$time = explode(':', trim($tmpdate[1]));
								$mkdt = mktime((int)$time[0], (int)$time[1], (int)$time[2], (int)$date[1], (int)$date[2], (int)$date[0]);		
							
								if ($mkdt >= (mktime()-86401))
								{
									$inskey = $rec;
									$eid = $this->_ListingIdFromItemID((string)$v['data']['Item']['ItemID']);
								
									if ((int)$eid > 0) $insert[$inskey]['e_id'] = $eid;
									else { unset($eid); $insert[$inskey]['e_id'] = 0; }
									
									
									/*
									[data] => Array
(
    [AmountPaid] => 0.0
    [AdjustmentAmount] => 0.0
    [ConvertedAdjustmentAmount] => 0.0
    [Buyer] => Array
        (
            [AboutMePage] => false
            [EIASToken] => nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AEl4CoDpeAqQSdj6x9nY+seQ==
            [Email] => eastorlando@ubreakifix.com
            [FeedbackScore] => 3248
            [PositiveFeedbackPercent] => 100.0
            [FeedbackPrivate] => false
            [FeedbackRatingStar] => Red
            [IDVerified] => false
            [eBayGoodStanding] => true
            [NewUser] => false
            [RegistrationDate] => 2011-08-09T02:43:09.000Z
            [Site] => US
            [Status] => Confirmed
            [UserID] => ubif16co
            [UserIDChanged] => false
            [UserIDLastChanged] => 2011-08-09T02:43:07.000Z
            [VATStatus] => NoVATTax
            [BuyerInfo] => Array
                (
                    [ShippingAddress] => Array
                        (
                            [Name] => Ubreakifix Joe Skubak
                            [Street1] => 11779 E Colonial Dr
                            [CityName] => Orlando
                            [StateOrProvince] => FL
                            [Country] => US
                            [CountryName] => United States
                            [Phone] => (407) 243-9994
                            [PostalCode] => 32817-4610
                            [AddressID] => 4985184811018
                            [AddressOwner] => eBay
                            [AddressUsage] => DefaultShipping
                        )

                )

            [UserAnonymized] => false
            [StaticAlias] => ubif16_jqny6896ni@members.ebay.com
        )

    [ShippingDetails] => Array
        (
            [ChangePaymentInstructions] => true
            [PaymentEdited] => false
            [SalesTax] => Array
                (
                    [SalesTaxPercent] => 0.0
                    [ShippingIncludedInTax] => false
                )

            [ShippingServiceOptions] => Array
                (
                    [ShippingService] => USPSPriority
                    [ShippingServiceCost] => 0.0
                    [ShippingServiceAdditionalCost] => 0.0
                    [ShippingServicePriority] => 1
                    [ExpeditedService] => false
                    [ShippingTimeMin] => 1
                    [ShippingTimeMax] => 3
                )

            [ShippingType] => Flat
            [SellingManagerSalesRecordNumber] => 46534
            [ThirdPartyCheckout] => false
            [TaxTable] => Array
                (
                )

            [GetItFast] => false
            [ExcludeShipToLocation] => Array
                (
                    [0] => Africa
                    [1] => AF
                    [2] => AM
                    [3] => AZ
                    [4] => BD
                    [5] => BT
                    [6] => GE
                    [7] => IN
                    [8] => KZ
                    [9] => KG
                    [10] => MV
                    [11] => MN
                    [12] => NP
                    [13] => PK
                    [14] => RU
                    [15] => TJ
                    [16] => TM
                    [17] => UZ
                    [18] => AL
                    [19] => BY
                    [20] => HR
                    [21] => CY
                    [22] => CZ
                    [23] => EE
                    [24] => GR
                    [25] => LV
                    [26] => LT
                    [27] => MD
                    [28] => ME
                    [29] => RS
                    [30] => SI
                    [31] => UA
                    [32] => AI
                    [33] => AG
                    [34] => AW
                    [35] => BS
                    [36] => BB
                    [37] => BZ
                    [38] => VG
                    [39] => KY
                    [40] => CR
                    [41] => DM
                    [42] => DO
                    [43] => SV
                    [44] => GD
                    [45] => GP
                    [46] => GT
                    [47] => HT
                    [48] => HN
                    [49] => JM
                    [50] => MS
                    [51] => AN
                    [52] => NI
                    [53] => PA
                    [54] => KN
                    [55] => LC
                    [56] => VC
                    [57] => TT
                    [58] => TC
                    [59] => VI
                    [60] => BN
                    [61] => KH
                    [62] => ID
                    [63] => LA
                    [64] => MO
                    [65] => MY
                    [66] => PH
                    [67] => SG
                    [68] => AR
                    [69] => BO
                    [70] => BR
                    [71] => CL
                    [72] => CO
                    [73] => EC
                    [74] => FK
                    [75] => GF
                    [76] => GY
                    [77] => PY
                    [78] => SR
                    [79] => UY
                    [80] => VE
                    [81] => MX
                    [82] => BH
                    [83] => IQ
                    [84] => JO
                    [85] => KW
                    [86] => LB
                    [87] => OM
                    [88] => QA
                    [89] => SA
                    [90] => TR
                    [91] => AE
                    [92] => YE
                )

        )

    [ConvertedAmountPaid] => 289.95
    [ConvertedTransactionPrice] => 289.95
    [CreatedDate] => 2016-12-26T17:05:06.000Z
    [DepositType] => None
    [QuantityPurchased] => 1
    [Status] => Array
        (
            [eBayPaymentStatus] => NoPaymentFailure
            [CheckoutStatus] => CheckoutIncomplete
            [LastTimeModified] => 2016-12-26T17:05:07.000Z
            [PaymentMethodUsed] => None
            [CompleteStatus] => Incomplete
            [BuyerSelectedShipping] => false
            [PaymentHoldStatus] => None
            [IntegratedMerchantCreditCardEnabled] => false
        )

    [TransactionID] => 1599043409008
    [TransactionPrice] => 289.95
    [BestOfferSale] => false
    [ShippingServiceSelected] => Array
        (
            [ShippingService] => USPSPriority
            [ShippingServiceCost] => 0.0
        )

    [FinalValueFee] => 17.4
    [TransactionSiteID] => US
    [Platform] => eBay
    [PayPalEmailAddress] => demolaptopparts@yahoo.com
    [BuyerGuaranteePrice] => 20000.0
    [IntangibleItem] => false
    [Item] => Array
        (
            [AutoPay] => false
            [BuyItNowPrice] => 0.0
            [Currency] => USD
            [ItemID] => 181922607405
            [ListingDetails] => Array
                (
                    [StartTime] => 2015-11-04T17:57:53.000Z
                    [EndTime] => 2016-12-28T17:57:53.000Z
                    [ViewItemURL] => http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&Item=181922607405
                    [ViewItemURLForNaturalSearch] => http://cgi.ebay.com/New-Apple-Macbook-Pro-13-Retina-Late-2013-2014-A1502-Screen-Assembly-661-8153?item=181922607405&category=0&cmd=ViewItem
                )

            [ListingType] => StoresFixedPrice
            [PaymentMethods] => PayPal
            [PrimaryCategory] => Array
                (
                    [CategoryID] => 31569
                )

            [PrivateListing] => false
            [Quantity] => 112
            [SecondaryCategory] => Array
                (
                    [CategoryID] => 0
                )

            [Seller] => Array
                (
                    [AboutMePage] => true
                    [EIASToken] => nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wHl4ulAJeFpA+dj6x9nY+seQ==
                    [Email] => demolaptopparts@yahoo.com
                    [FeedbackScore] => 17153
                    [PositiveFeedbackPercent] => 99.4
                    [FeedbackPrivate] => false
                    [FeedbackRatingStar] => YellowShooting
                    [IDVerified] => false
                    [eBayGoodStanding] => true
                    [NewUser] => false
                    [RegistrationDate] => 2006-07-23T02:11:20.000Z
                    [Site] => US
                    [Status] => Confirmed
                    [UserID] => la.tronics
                    [UserIDChanged] => false
                    [UserIDLastChanged] => 2012-06-25T20:17:12.000Z
                    [VATStatus] => NoVATTax
                    [SellerInfo] => Array
                        (
                            [AllowPaymentEdit] => true
                            [CheckoutEnabled] => true
                            [CIPBankAccountStored] => false
                            [GoodStanding] => true
                            [LiveAuctionAuthorized] => false
                            [MerchandizingPref] => OptIn
                            [QualifiesForB2BVAT] => false
                            [StoreOwner] => true
                            [StoreURL] => http://www.stores.ebay.com/id=379487753
                            [SafePaymentExempt] => true
                            [TopRatedSeller] => true
                        )

                )

            [SellingStatus] => Array
                (
                    [ConvertedCurrentPrice] => 289.95
                    [CurrentPrice] => 289.95
                    [QuantitySold] => 67
                    [ListingStatus] => Active
                )

            [Site] => US
            [StartPrice] => 289.95
            [Title] => New Apple Macbook Pro 13" Retina Late 2013 2014 A1502 Screen Assembly 661-8153
            [GetItFast] => false
            [IntegratedMerchantCreditCardEnabled] => false
            [ConditionID] => 1000
            [ConditionDisplayName] => New
        )

)
									*/
									$insert[$inskey]['datetime'] = CleanBadDate($v['data']['CreatedDate']);
									$insert[$inskey]['mkdt'] = $mkdt;
									$insert[$inskey]['rec'] = $inskey;
									//$insert[$inskey]['paid'] = floater((string)$v['data']['AmountPaid']);
									//if (isset($v['data']['ShippingServiceSelected'])) $insert[$inskey]['paid'] = floater(((float)$v['data']['TransactionPrice']*(int)$v['data']['QuantityPurchased'])+(float)$v['data']['ShippingServiceSelected']);
									//////!!!!!!!!!!/////////
									//else $insert[$inskey]['paid'] = floater((float)$v['data']['TransactionPrice']*(int)$v['data']['QuantityPurchased']);
									$insert[$inskey]['paid'] = floater((float)$v['data']['TransactionPrice']*(int)$v['data']['QuantityPurchased']);
									$insert[$inskey]['eachpaid'] = floater((float)$v['data']['TransactionPrice']);
									$insert[$inskey]['fee'] = floater((float)$v['data']['FinalValueFee']);
									$insert[$inskey]['shipping'] = (string)$v['data']['ShippingServiceSelected']['ShippingService'];
									//$insert[$inskey]['tracking'] = (string)$v['data']['ShippingServiceSelected']['ShippingServiceCost'];
									if (isset($v['data']['PaidTime'])) $insert[$inskey]['paidtime'] = CleanBadDate((string)$v['data']['PaidTime']);
									else $insert[$inskey]['paidtime'] = ''; 
									$insert[$inskey]['itemid'] = (string)$v['data']['Item']['ItemID'];
									$insert[$inskey]['buyerid'] = (string)$v['data']['Buyer']['UserID'];
									$insert[$inskey]['buyeremail'] = (string)$v['data']['Buyer']['Email'];
									$insert[$inskey]['qtyof'] = (int)$v['data']['Item']['Quantity'];
									$insert[$inskey]['qty'] = (int)$v['data']['QuantityPurchased'];	
									$insert[$inskey]['asc'] = (string)$v['data']['ActualShippingCost'];	
									$insert[$inskey]['ssc'] = floater((string)$v['data']['ShippingServiceSelected']['ShippingServiceCost']);
									$insert[$inskey]['ebsold'] = (string)$v['data']['Item']['SellingStatus']['QuantitySold'];	
									$insert[$inskey]['transid'] = (string)$v['data']['TransactionID'];
									$insert[$inskey]['pushed'] = 1;
									$pool[$inskey]['itemid'] = (string)$v['data']['Item']['ItemID'];
									$pool[$inskey]['transid'] = (string)$v['data']['TransactionID'];
									$insert[$inskey]['notpaid'] = 0;
									$insert[$inskey]['refunded'] = 0;
									$insert[$inskey]['pendingpay'] = 0;
									$insert[$inskey]['customcode'] = 0;
									
									if (isset($v['data']['Status']))
									{										
										
											
										if ((string)$v['data']['Status']['CompleteStatus'] == 'CustomCode')
										{
											$insert[$inskey]['customcode'] = 1;												
										}
										elseif ((string)$v['data']['Status']['CompleteStatus'] == 'Incomplete')
										{
											$insert[$inskey]['notpaid'] = 1;																					
										}
										elseif ((string)$v['data']['Status']['CompleteStatus'] == 'Pending')
										{
											$insert[$inskey]['pendingpay'] = 1;											
										}
									
									}
									
								if (isset($v['data']['ContainingOrder']['ShippingDetails']['SellingManagerSalesRecordNumber'])) $insert[$inskey]['contorderid'] =(int)$v['data']['ContainingOrder']['ShippingDetails']['SellingManagerSalesRecordNumber'];
								
								$insert[$inskey]['hk_amp'] = serialize(array('AmountPaid' => (string)$v['data']['AmountPaid'], 'AdjustmentAmount' => (string)$v['data']['AdjustmentAmount'], 'ConvertedAdjustmentAmount' => (string)$v['data']['ConvertedAdjustmentAmount'], 'ConvertedAmountPaid' => (string)$v['data']['ConvertedAmountPaid'],'ConvertedTransactionPrice' => (string)$v['data']['ConvertedTransactionPrice']));
								}
									
			
							
							if (is_array($insert)) ksort($insert);
							$echo = 'EB:'.count($insert);
							$unsetted = '<br>Unset: ';
							$unsetcount = 0;
							$sql = 'SELECT `et_id`, `e_id`, `rec`, `paid`, `eachpaid`, `fee`, `shipping`, `tracking`, `paidtime`, `qty`, `qtyof`, `asc`, `ssc`, `updated`, `ebsold`,`notpaid`,`refunded`,`pendingpay`,`customcode` FROM ebay_transactions ';
							
							$c = 1;
							foreach ($pool as $p)
							{
								if ($c == 1) $sql .= 'WHERE (`itemid` = '.$p['itemid'].' AND `transid` = '.$p['transid'].')';	
								else $sql .= ' OR (`itemid` = '.$p['itemid'].' AND `transid` = '.$p['transid'].')';									
							}
							
							$sql .=  ' ORDER BY `rec` DESC';
							$q =  $this->db->query($sql);
							if ($q->num_rows() > 0) 
							{
							$echo .= ' DB:'.count($q->result_array());
								 foreach($q->result_array() as $t)
								 {					 
									if (isset($insert[$t['rec']]) && ($insert[$t['rec']]['paid'] == $t['paid'] && $insert[$t['rec']]['fee'] == $t['fee'] && $insert[$t['rec']]['shipping'] == $t['shipping'] && $insert[$t['rec']]['tracking'] == $t['tracking']  && $insert[$t['rec']]['paidtime'] == $t['paidtime'] && $insert[$t['rec']]['qtyof'] == $t['qtyof'] && $insert[$t['rec']]['asc'] == $t['asc'] && $insert[$t['rec']]['ssc'] == $t['ssc'])) { $unsetted .= $t['rec'].' '; $unsetcount++; unset($insert[$t['rec']]);  }
									elseif (isset($insert[$t['rec']]))
									{
										//$updatedata = $insert[$t['rec']];	
										//<tr><td>Asc</td><td>'.$insert[$t['rec']]['asc'].'</td><td>'.$t['asc'].'</td></tr>				
										$this->printstr .= printcool ('<table>				
									<tr><td>Rec</td><td>'.$t['rec'].'</td><td></td></tr>
									<tr><td>Paid</td><td>'.$insert[$t['rec']]['paid'].'</td><td>'.$t['paid'].'</td></tr>
									<tr><td>Fee</td><td>'.$insert[$t['rec']]['fee'].'</td><td>'.$t['fee'].'</td></tr>
									<tr><td>Shipping</td><td>'.$insert[$t['rec']]['shipping'].'</td><td>'.$t['shipping'].'</td></tr>
									<tr><td>Tracking</td><td>'.$insert[$t['rec']]['tracking'].'</td><td>'.$t['tracking'].'</td></tr>
									<tr><td>PaidTime</td><td>'.$insert[$t['rec']]['paidtime'].'</td><td>'.$t['paidtime'].'</td></tr>
									<tr><td>QtyOf</td><td>'.$insert[$t['rec']]['qtyof'].'</td><td>'.$t['qtyof'].'</td></tr>									
									<tr><td>Ssc</td><td>'.$insert[$t['rec']]['ssc'].'</td><td>'.$t['ssc'].'</td></tr>
									<tr><td>Sold</td><td>'.$insert[$t['rec']]['ebsold'].'</td><td>'.$t['ebsold'].'</td></tr>
									</table><br>', true);
										
										$updstr = '';
										$paychange = FALSE;
										$warehouse = array();		
										if ($insert[$t['rec']]['paid'] != $t['paid'])
										{
											$updstr .= ' Paid: '.IfFillEmpty($insert[$t['rec']]['paid'],'b').' / '.IfFillEmpty($t['paid'],'r').' |';
											$updatedata['paid'] = $insert[$t['rec']]['paid'];
											$paychange = TRUE;
											$warehouse['paid'] = $insert[$t['rec']]['eachpaid'];
										}
															
										if ($insert[$t['rec']]['fee'] != $t['fee'])
										{
											$updstr .= ' Fee: '.IfFillEmpty($insert[$t['rec']]['fee'],'b').' / '.IfFillEmpty($t['fee'],'r').' |';
											$updatedata['fee'] = $insert[$t['rec']]['fee'];
											$warehouse['sellingfee'] = (float)$insert[$t['rec']]['fee']/$t['qty'];
										}
															
										if ($insert[$t['rec']]['shipping'] != $t['shipping']  && $insert[$t['rec']]['shipping'] != '') 
										{
											$updstr .= ' Pushed Shipping: '.IfFillEmpty($insert[$t['rec']]['shipping'],'b').' / '.IfFillEmpty($t['shipping'],'r').' |';
											$updatedata['shipping'] = $insert[$t['rec']]['shipping'];
										}										
															
										if ($insert[$t['rec']]['tracking'] != $t['tracking'] && $insert[$t['rec']]['tracking'] != '')
										{
											$updstr .= ' Pushed Tracking : '.IfFillEmpty($insert[$t['rec']]['tracking'],'b').' / '.IfFillEmpty($t['tracking'],'r').' |';
											$updatedata['tracking'] = $insert[$t['rec']]['tracking'];
										}
															
										if ($insert[$t['rec']]['paidtime'] != $t['paidtime'])
										{
											$updstr .= ' Pushed PaidTime: '.IfFillEmpty($insert[$t['rec']]['paidtime'],'b').' / '.IfFillEmpty($t['paidtime'],'r').' |';
											$updatedata['paidtime'] = $insert[$t['rec']]['paidtime'];
										}										
															
										if ($insert[$t['rec']]['qtyof'] != $t['qtyof'])
										{
											$updstr .= ' QtyOf: '.IfFillEmpty($insert[$t['rec']]['qtyof'],'b').' / '.IfFillEmpty($t['qtyof'],'r').' |'; 
											$updatedata['qtyof'] = $insert[$t['rec']]['qtyof'];
										}
										
										/*if ($insert[$t['rec']]['asc'] != $t['asc'] && $insert[$t['rec']]['asc'] > $t['asc']) 
										{
											$updstr .= ' ActShipCost: '.IfFillEmpty($insert[$t['rec']]['asc'],'b').' / '.IfFillEmpty($t['asc'],'r').' |'; 
											$updatedata['asc'] = $insert[$t['rec']]['asc'];
											$warehouse['shipped_actual'] = (float)$insert[$t['rec']]['asc']/$t['qty'];
										}*/
										
										if ($insert[$t['rec']]['ssc'] != $t['ssc'])//  && floater($insert[$t['rec']]['ssc']) > 0) 
										{
											$updstr .= ' ShipCost: '.IfFillEmpty($insert[$t['rec']]['ssc'],'b').' / '.IfFillEmpty($t['ssc'],'r').' |'; 
											$updatedata['ssc_old'] =  floater($t['ssc']);
                                                                                        $updatedata['ssc'] = $insert[$t['rec']]['ssc'];
											$warehouse['shipped'] = (float)$insert[$t['rec']]['ssc']/$t['qty'];
										}
										if ($insert[$t['rec']]['ebsold'] != $t['ebsold']) 
										{
											$updstr .= ' Sold: '.IfFillEmpty($insert[$t['rec']]['ebsold'],'b').' / '.IfFillEmpty($t['ebsold'],'r').' |'; 
											$updatedata['ebsold'] = $insert[$t['rec']]['ebsold'];
										}
										
											if ($insert[$t['rec']]['notpaid'] != $t['notpaid'])
											{
												$updatedata['notpaid'] = 1;
												$updstr .= ' Set To Not Paid |'; 
											}
											if ($insert[$t['rec']]['refunded'] != $t['refunded'])
											{
												$updatedata['refunded'] = 1;
												$updstr .= ' Set To Refunded |'; 
											}
											if ($insert[$t['rec']]['pendingpay'] != $t['pendingpay'])
											{
												$updatedata['pendingpay'] = 1;
												$updstr .= ' Set To Pending Pay |'; 
											}
											if ($insert[$t['rec']]['customcode'] != $t['customcode'])
											{
												$updatedata['customcode'] = 1;
												$updstr .= ' Set To CustomCode |'; 
											}
											
										$updstr .= '<br>';
										
										$updatedata['updated'] = $t['updated'].$updstr;
										$this->printstr .= printcool ($updstr, true);
										$this->printstr .= printcool ($updatedata,true);
										
										$this->db->update('ebay_transactions', $updatedata, array('rec' => (int)$t['rec']));
										
										$this->_notify('FixedPriceTransaction - Update Run '.(int)$t['rec'],$this->printstr);
										
										if (strlen($updstr) > 7) $this->db->insert('admin_history', array('msg_type' => 1, 'msg_title' => 'Transaction Push Updated: '.$updstr, 'msg_body' => '', 'msg_date' => CurrentTime(),
																	  'e_id' => $this->_GetEbayFromItemID($insert[$t['rec']]['itemid']),
																	  'itemid' => $insert[$t['rec']]['itemid'],
																	  'trec' => $t['rec'],
																	  'admin' => 'Auto',
																	  'sev' => '')); 
										if ($paychange)
										{						
																	
											//$bcnarray = $this->_DoBCNS($insert[$t['rec']]);
					/*						A - BCN
					
											G Ebay Title
											J Date Sold
											K Price Sold
											L Shipping
											M Where (ebay)
											U ItemID link to ebay listing*/
											//echo 'UPDATE';
											$e = $this->_GetEbayTitleFromItemID($insert[$t['rec']]['itemid']);
											
										}				  
										if (count($warehouse) > 0)
										{											
											
											$bcns = $this->Myseller_model->getSales(array((int)$t['et_id']),1, TRUE, TRUE);
											if ($bcns) foreach($bcns as $wid)
											{
												if ($wid['vended'] == 1) $this->Myseller_model->HandleBCN($warehouse, $wid);
												
												/*if (isset($warehouse['paid']))
												{
												$warehouse['paypal_fee'] = $this->Myseller_model->PayPalFee(((float)$warehouse['paid']+(float)$wid['shipped_actual']));
												$warehouse['netprofit'] = $this->Myseller_model->NetProfitCalc((float)$warehouse['paid'], (float)$warehouse['shipped'], (float)$wid['cost'], (float)$wid['sellingfee'], (float)$wid['shipped_actual'],$warehouse['paypal_fee']);
												}
												foreach($warehouse as $k => $v)
												{								
													if ($v != $wid[$k] && $wid['vended'] == 1) $this->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);	
													else unset($warehouse[$k]);
												}
												if (count($warehouse) > 0) $this->db->update('warehouse', $warehouse, array('wid' => (int)$wid['wid']));	
												*/
											}
										}
										unset($updatedata);
										unset($updstr);
										unset($insert[$t['rec']]);
										
									}
								 }			 
							}
							$echo .= ' FIN:'.count($insert);
							$this->printstr .= printcool ($insert,true);
						
							$this->printstr .= printcool ('------ INSERT LIST START -----',true);
							
							

							if ($insert) foreach($insert as $i) 
								{
									$this->printstr .= printcool ($i['rec'],true);
									$i['et_id'] = $i['rec'];
									$this->db->insert('ebay_transactions', $i); 
									$i['et_id'] = $this->db->insert_id();
									
                                                                        $theeid = $this->_GetEbayFromItemID($i['itemid']);
                                                                        if ($theeid ==  18599 || $theeid == 18725 || $theeid ==  18599 || $theeid == 18653 || $theeid == 18654 || $theeid == 18655 || $theeid == 18656 || $theeid ==  18657) GoMail(array(
									'msg_title' => 'Item sale for '.$theeid.' @'.CurrentTime(),
									'msg_body' => printcool($i, TRUE,''),
									'msg_date' => CurrentTime()			
									), 'mr.reece@gmail.com', $this->config->config['no_reply_email']);
                                                                            
									$this->load->model('Myautopilot_model');	
									$this->Myautopilot_model->ResetRules((int)$i['e_id'], 'ProcessTransactions');
																	
									$this->db->insert('ebay_cron', array('e_id' => $i['e_id'], 'data' => 'QuantityPurchased: '.(int)$i['qty'].' - of Quantity: '.(int)$i['qtyof'].' - QuantitySold: '.(string)$i['ebsold'].' - @ ProcessTransactions for '.$i['et_id'], 'time' => CurrentTime(), 'ts' => mktime()));
													
									if ($i['paid'] > 0 && $i['paidtime'] != '') $pay = ' <span style="color:#FF9900;">(Paid)</span>';
									else $pay = ' <span style="color:red;">(Unpaid)</span>';
									
										/*$this->db->insert('admin_history', array('msg_type' => 1, 'msg_title' => '<span style="color:blue;">New eBay Transaction</span>'.$pay, 'msg_body' => '', 'msg_date' => CurrentTime(),
																	  'e_id' => $this->_GetEbayFromItemID($i['itemid']),
																	  'itemid' => $i['itemid'],
																	  'trec' => $i['rec'],
																	  'admin' => 'Auto',
																	  'sev' => '')); 
										*/
										
										$this->_DoBCNS($i);
										if ($i['paid'] > 0 && $i['paidtime'] != '')
										{
											//$bcnarray = $this->_DoBCNS($i);
					/*						A - BCN
					
											G Ebay Title
											J Date Sold
											K Price Sold
											L Shipping
											M Where (ebay)
											U ItemID link to ebay listing*/
											$e = $this->_GetEbayTitleFromItemID($i['itemid']);
											/*echo 'INSERT';
											printcool ($e);
											printcool ($i);*/						
										}			
								}
								$this->printstr .= printcool ('------ INSERT LIST END -----',true);
								$this->_notify('FixedPriceTransaction - New Run '.$rec,$this->printstr);
					   
						   /*
						   	  echo '<td valign="top"  style="background:#CEFFC3;">';
                             if (isset($ourtransactions[$rec])) continue;
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
                             }  */  
                        }
                        elseif ($v['type'] == 'ItemRevised')
                        {					
                             // echo '<td valign="top"  style="background:#FFE8A7;">';
                             // echo '<br>ItemID: '.$v['data']['ItemID'];
                              $itemid = $v['data']['ItemID'];
     
                             if (!isset($ourlistings[$itemid])) { $this->_notify('ItemRevised - Does not exist Localy',$v['data']); exit();}//echo '<strong style="color:red">Does not exist localy</strong>';        

                             //Replace your row with row below
                            // echo '<br><strong>ConvertedStartPrice</strong>: '.$v['data']['ListingDetails']['ConvertedStartPrice'];

                             if (isset($ourlistings[$itemid]))
                             {
                               //Replace your row with row below
                               if ($ourlistings[$itemid]['price_ch1'] != $v['data']['ListingDetails']['ConvertedStartPrice']) $update['price_ch1'] = $v['data']['ListingDetails']['ConvertedStartPrice'];//echo ' <strong style="color:red">'.$ourlistings[$itemid]['price_ch1'].'</strong>';
                             }     
                             //echo '<br><strong>Title</strong>: '.$v['data']['Title'];          
                             if (isset($ourlistings[$itemid]))
                             {
                              if ($ourlistings[$itemid]['e_title'] != $v['data']['Title']) $update['e_title'] = $v['data']['Title'];//echo ' <strong style="color:red">'.$ourlistings[$itemid]['e_title'].'</strong>';
                             }      
                             //echo '<br><strong>Quantity</strong>: '.$v['data']['Quantity'];          
                             if (isset($ourlistings[$itemid]))
                             {
								 $qn = $v['data']['Quantity']-$v['data']['SellingStatus']['QuantitySold'];
                              if ($ourlistings[$itemid]['quantity'] != $qn) $update['quantity'] = $update['ebayquantity'] = $qn;//echo ' <strong style="color:red">'.$ourlistings[$itemid]['qn_ch1'].'</strong>';
                             }     
                             //echo '<br><strong>OutOfStockControl</strong>: '.$v['data']['OutOfStockControl'];          
                             if (isset($ourlistings[$itemid]))
                             {
							
                               if ($ourlistings[$itemid]['ooskeepalive'] == 0 && (string)$v['data']['OutOfStockControl'] == 'true') $osc = 1;//echo ' <strong style="color:green"> Same in DB</strong>';
                              elseif ($ourlistings[$itemid]['ooskeepalive'] == 1 && (string)$v['data']['OutOfStockControl'] != 'true') $osc = 0;
							   if (isset($osc))	$update['ooskeepalive'] = $osc;//echo ' <strong style="color:red">'.$ourlistings[$itemid]['ooskeepalive'].'
                             }      
                             //echo '<br><strong>ConditionID</strong>: '.$v['data']['ConditionID'];          
                             if (isset($ourlistings[$itemid]))
                             {
                              if ($ourlistings[$itemid]['Condition'] != $v['data']['ConditionID']) $update['Condition'] = $v['data']['ConditionID'];//echo ' <strong style="color:red">'.$ourlistings[$itemid]['Condition'].'</strong>';
                             } 
                              //Replace your row with row below     
                             if(isset($v['data']['SellingStatus']['ListingStatus']))
                            //Replace your row with row below
                             // echo '<br><strong>ListingStatus</strong>: '.$v['data']['SellingStatus']['ListingStatus'];          
                   
                             if (isset($ourlistings[$itemid]))
                             {
                             //Replace your row with row below
                             if ($ourlistings[$itemid]['ebended'] == '' && $v['data']['SellingStatus']['ListingStatus'] != 'Active')
							 $update['ebended'] = CurrentTime();//continue;// echo ' <strong style="color:green">Same in DB</strong>';
                              else $update['ebended'] = NULL;//if ($ourlistings[$itemid]['ebended'] != '' && $v['data']['SellingStatus']['ListingStatus'] != 'Complete')continue; //echo ' <strong style="color:green">Same in DB</strong>';
                             // else 
							  //{
								  
							 // }//echo ' <strong style="color:red">'.$ourlistings[$itemid]['ebended'].'</strong>';
							 if (isset($update))
							 	{
									$eid = $this->_GetEbayFromItemID($itemid);
							 	 	$this->_notify('PushCommit Listing '.$eid,$update);									
									$this->_logaction('eBayPush ItemRevised', 'M', $ourlistings[$itemid], $update, $eid, $itemid);
									$this->db->update('ebay',$update,array('e_id'=>$eid));
								}
                             } 
							 else  	$this->_notify('Push No Commit - No data to update '.$eid,printcool ($ourlistings[$itemid],true).printcool ($v['data'],true));
                        }
                        elseif ($v['type'] == 'ItemSold' || $v['type'] == 'ItemUnsold' || $v['type'] == 'ItemClosed')
                        {					
                             $itemid = $v['data']['ItemID'];
                             //echo '<td valign="top"  style="background:#FFE8A7;">';
                            // echo '<br>ItemID: '.$v['data']['ItemID'];
                             if (!isset($ourlistings[$itemid]))  { $this->_notify('ItemSold/Unsold/Closed - Does not exist Localy',$v['data']); exit();}
                              //echo ' <strong style="color:red">Does not exist localy</strong>';
      
                             if (strlen($ourlistings[$itemid]['ebended']) < 6) $update['ebended'] = CurrentTime();//echo ' <br><br><strong style="color:red">Not Ended in DB</strong>';

							  if (isset($update))
							 	{
									$eid = $this->_GetEbayFromItemID($itemid);
							 	 	$this->_notify('PushCommit Listing '.$eid,$update);									
									$this->_logaction('eBayPush '.$v['type'], 'M', $ourlistings[$itemid], $update, $eid, $itemid);
									$this->db->update('ebay',$update,array('e_id'=>$eid));
								}
						      else  	$this->_notify('Push No Commit - No data to update '.$eid,printcool ($ourlistings[$itemid],true).printcool ($v['data'],true));
                        }
                        elseif ($v['type'] == 'ReturnCreated')

                        {
                            //echo '<td valign="top"  style="background:#FFBBF8;">';

                           // echo '<br>ItemID: '.$v['data']['TransactionIdentity']['ItemId'];
                            //echo '<br>TransactionID: '.$v['data']['TransactionIdentity']['TransactionId'];
							$this->_notify('Return Notif: '.$v['type'],$v['data']);	
							
														
							 $details = $this->_GetReturnData((int)$v['data']['ReturnId'],$v['type']);
							
							 if ($details)
							 {
								 $details['returnurl'] = $v['data']['ReturnLink'];
								 $details['returnid'] = (int)$v['data']['ReturnId'];
								 $this->db->update('ebay_transactions', $details, array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
							 }
							 else $this->db->update('ebay_transactions', array('returnnotif' => $v['type'],'returnurl' => $v['data']['ReturnLink'],'returnid'=>(int)$v['data']['ReturnId']), array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
							 
								$this->_notify('Return Notif 2: '.$v['type'], $details);
                            /*
							
Array
(
    [NotificationEventName] => ReturnCreated
    [RecipientUserID] => la.tronics
    [ReturnId] => 5045631603
    [CreationDate] => 2016-12-30T14:59:26.182Z
    [OtherPartyId] => k.davila
    [OtherPartyRole] => BUYER
    [ReturnStatus] => returnRequested
    [ReturnGlobalId] => EBAY_MAIN
    [OrderId] => 172172344768-1635568522007!777634195010
    [TransactionIdentity] => Array
        (
            [ItemId] => 172172344768
            [TransactionId] => 1635568522007
        )

    [ReturnLink] => http://postorder.ebay.com/Return/ReturnsDetail?returnId=5045631603
    [Priority] => 1
    [ReturnTrackingId] => NULL
)
*/
                              
                        }						
						elseif ($v['type'] == 'ReturnShipped')

                        {
                          //  echo '<td valign="top"  style="background:#FFBBF8;">';

                          //  echo '<br>ItemID: '.$v['data']['TransactionIdentity']['ItemId'];
                           // echo '<br>TransactionID: '.$v['data']['TransactionIdentity']['TransactionId'];
							$this->_notify('Return Notif: '.$v['type'],$v['data']);	
							
							/*GoMail(array(
			'msg_title' => 'Return shipped dump @'.CurrentTime(),
			'msg_body' => printcool($this->_GetReturnData((int)$v['data']['ReturnId']), TRUE,''),
			'msg_date' => CurrentTime()			
			), 'errors@la-tronics.com', $this->config->config['no_reply_email']);*/
							
							$details = $this->_GetReturnData((int)$v['data']['ReturnId'],$v['type']);
							
							 if ($details)
							 {
								 $details['returnurl'] = $v['data']['ReturnLink'];
								 $details['returnid'] = (int)$v['data']['ReturnId'];
								 $this->db->update('ebay_transactions', $details, array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
							 }
							 else $this->db->update('ebay_transactions', array('returnnotif' => $v['type'],'returnurl' => $v['data']['ReturnLink'],'returnid'=>(int)$v['data']['ReturnId']), array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
							 
							//$this->db->update('ebay_transactions', array('returnnotif' => $v['type'],'returnurl' => $v['data']['ReturnLink']), array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
							
							
                            /*Array
(
    [NotificationEventName] => ReturnShipped
    [RecipientUserID] => la.tronics
    [ReturnId] => 5045603937
    [CreationDate] => 2016-12-30T16:36:20.399Z
    [OtherPartyId] => richard_velazquez
    [OtherPartyRole] => BUYER
    [ReturnStatus] => itemShipped
    [ReturnGlobalId] => EBAY_MAIN
    [OrderId] => 201569533771-1468194830010!260000003842316
    [TransactionIdentity] => Array
        (
            [ItemId] => 201569533771
            [TransactionId] => 1468194830010
        )

    [ReturnLink] => http://postorder.ebay.com/Return/ReturnsDetail?returnId=5045603937
    [Priority] => 3
    [ReturnTrackingId] => 9302020130300854200554
)*/
                              
                        }
						elseif ($v['type'] == 'ReturnDelivered')

                        {
                            //echo '<td valign="top"  style="background:#FFBBF8;">';

                            //echo '<br>ItemID: '.$v['data']['TransactionIdentity']['ItemId'];
                           // echo '<br>TransactionID: '.$v['data']['TransactionIdentity']['TransactionId'];
							$this->_notify('Return Notif: '.$v['type'],$v['data']);	
							
							/*GoMail(array(
			'msg_title' => 'Return delivered dump @'.CurrentTime(),
			'msg_body' => printcool($this->_GetReturnData((int)$v['data']['ReturnId']), TRUE,''),
			'msg_date' => CurrentTime()			
			), 'errors@la-tronics.com', $this->config->config['no_reply_email']);*/
			
							/*$details = $this->_GetReturnData((int)$v['data']['ReturnId']);
							
							 if ($details)
							 {
								 $details['returnurl'] = $v['data']['ReturnLink'];
								 $details['returnid'] = (int)$v['data']['ReturnId'];
								 $this->db->update('ebay_transactions', $data, array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
							 }
							 else $this->db->update('ebay_transactions', array('returnnotif' => $v['type'],'returnurl' => $v['data']['ReturnLink'],'returnid'=>(int)$v['data']['ReturnId']), array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
							 */
							$this->db->update('ebay_transactions', array('returnnotif' => $v['type'],'returnurl' => $v['data']['ReturnLink']), array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
                             /*
							 Array
(
    [NotificationEventName] => ReturnDelivered
    [RecipientUserID] => la.tronics
    [ReturnId] => 5045118558
    [CreationDate] => 2016-12-30T14:27:29.090Z
    [OtherPartyId] => mitch1b
    [OtherPartyRole] => BUYER
    [ReturnStatus] => itemDelivered
    [ReturnGlobalId] => EBAY_MAIN
    [OrderId] => 181922607405-1597001688008!771586863019
    [TransactionIdentity] => Array
        (
            [ItemId] => 181922607405
            [TransactionId] => 1597001688008
        )

    [ReturnLink] => http://postorder.ebay.com/Return/ReturnsDetail?returnId=5045118558
    [Priority] => 1
    [ReturnTrackingId] => 9382120128700436969721
)*/
                              
                        }
						elseif ($v['type'] == 'ReturnRefundOverdue')

                        {
                            //echo '<td valign="top"  style="background:#FFBBF8;">';

                            //echo '<br>ItemID: '.$v['data']['TransactionIdentity']['ItemId'];
                            //echo '<br>TransactionID: '.$v['data']['TransactionIdentity']['TransactionId'];
							$this->_notify('Return Notif: '.$v['type'],$v['data']);	
							$this->db->update('ebay_transactions', array('returnnotif' => $v['type'],'returnurl' => $v['data']['ReturnLink']), array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
                               // echo '<br>Found Return Created';
                              
                        }
						elseif ($v['type'] == 'ReturnClosed')

                        {
                            //echo '<td valign="top"  style="background:#FFBBF8;">';

                            //echo '<br>ItemID: '.$v['data']['TransactionIdentity']['ItemId'];
                            //echo '<br>TransactionID: '.$v['data']['TransactionIdentity']['TransactionId'];
							$this->_notify('Return Notif: '.$v['type'],$v['data']);	
							
							/*GoMail(array(
			'msg_title' => 'Return closed dump @'.CurrentTime(),
			'msg_body' => printcool($this->_GetReturnData((int)$v['data']['ReturnId']), TRUE,''),
			'msg_date' => CurrentTime()			
			), 'errors@la-tronics.com', $this->config->config['no_reply_email']);*/
			
							/*$details = $this->_GetReturnData((int)$v['data']['ReturnId']);
							
							 if ($details)
							 {
								 $details['returnurl'] = $v['data']['ReturnLink'];
								 $details['returnid'] = (int)$v['data']['ReturnId'];
								 $this->db->update('ebay_transactions', $data, array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
							 }
							 else $this->db->update('ebay_transactions', array('returnnotif' => $v['type'],'returnurl' => $v['data']['ReturnLink'],'returnid'=>(int)$v['data']['ReturnId']), array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
							 */
							$this->db->update('ebay_transactions', array('returnnotif' => $v['type']), array('transid' => $v['data']['TransactionIdentity']['TransactionId'],'itemid'=>$v['data']['TransactionIdentity']['ItemId']));
                               // echo '<br>Found Return Created';
                              
                        }
						
                        elseif ($v['type'] == 'NotificationEvent')
                        {
							$this->_notify('NotificationEvent',$v['data']);	
                            //echo '<td valign="top"  style="background:#D7E7FF;">';

                           /* foreach ($v['data'] as $kk => $vv)
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
                            }	*/						
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
                          
                             if (!isset($ourtransactions[$rec])) { $this->_notify('ItemMarkedShipped - Does not exist Localy',$v['data']); exit();}
                             //echo '<strong style="color:red">Does not exist localy</strong>';
						 
                             //echo '<br><strong>FinalValueFee</strong>: '.$v['data']['FinalValueFee'];
                             if (isset($ourtransactions[$rec]))
                             {
                              if ($ourtransactions[$rec]['fee'] != $v['data']['FinalValueFee'])
							  {
								   
								   $update['fee'] = floater($v['data']['FinalValueFee']);
								   $warehouse['sellingfee'] = $update['fee']/$ourtransactions[$rec]['qty'];
								   //echo ' <strong style="color:red">'.$ourtransactions[$rec]['fee'].'</strong>';
							  }
                             }
							 
							 
							 $smasc = $this->GetSellingManagerSaleRecordRequest((int)$v['data']['TransactionID']);
							 
							 //$this->_notify('ItemMarkedShipped Our ASC for Rec '.$rec,$ourtransactions[$rec]['asc']); 
							  //$this->_notify('ItemMarkedShipped Got ASC for Rec '.$rec,$smasc); 
							  
							if ($ourtransactions[$rec]['asc'] != (string)$smasc) 
							{
								$update['asc'] = floater((string)$smasc);
								
								if (floater($ourtransactions[$rec]['sasc']) != 0.00) $warehouse['shipped_actual'] = floater($ourtransactions[$rec]['sasc'])/$ourtransactions[$rec]['qty'];
								else $warehouse['shipped_actual'] = $update['asc']/$ourtransactions[$rec]['qty'];
						
								
							}// echo ' <strong style="color:purple;">'.$smasc.'</strong>';
							/*		
                             if (isset($v['data']['ActualShippingCost'])) 
                             {      
                              //echo '<br><strong>ActualShippingCost</strong>: '.$v['data']['ActualShippingCost'];
                              if (isset($ourtransactions[$rec]))
                              {
                                if ($ourtransactions[$rec]['asc'] == $v['data']['ActualShippingCost']) continue;//echo ' <strong style="color:green">Same in DB</strong>';
                                else 
								{ 
									//echo ' <strong style="color:red">'.$ourtransactions[$rec]['asc'].'</strong>';
									$smasc = $this->GetSellingManagerSaleRecordRequest((int)$v['data']['TransactionID']);
									if ($ourtransactions[$rec]['asc'] == $smasc) continue;// echo ' <strong style="color:00FF0D;">'.$smasc.'</strong>';
									else $update['asc'] = $smasc;// echo ' <strong style="color:purple;">'.$smasc.'</strong>';
									
									}    
                              }
                             }*/
                             //if (isset($v['data']['ActualHandlingCost'])) echo '<br><strong>ActualHandlingCost</strong>: '.$v['data']['ActualHandlingCost'];
  
                             //echo '<br>ShippingServiceSelected: ';
                            // foreach ($v['data']['ShippingServiceSelected'] as $kk => $vv) echo '<strong>'.$kk.':</strong> '.$vv.' | ';
					     	if (isset($warehouse))
							 	{
									//$eid = $this->_GetEbayFromItemID($itemid);
									
							 	 	if ((float)$smasc > 0) $this->_notify('PushCommit Transaction '.$rec,$update);										
									$this->_logaction('eBayPush '.$v['type'], 'B', $ourtransactions[$rec], $update, 0, 0,(int)$v['data']['TransactionID']);
									$update['cascupd'] = 1;
                                                                        $this->db->update('ebay_transactions',$update,array('rec'=>$rec));
									
									
										$this->load->model('Myseller_model');
										$this->load->model('Auth_model');
										
												$this->db->select('wid, bcn, '.$this->Myseller_model->sellingfields());
												$this->db->where('channel', 1);
												$this->db->where('sold_id', $rec);
												$this->db->where('vended', 1);
												
												$f = $this->db->get('warehouse');
												if ($f->num_rows() > 0)
												{
													$fr = $f->result_array();
													foreach ($fr as $fl)
													{	
														if ($fl['vended'] == 1) $this->Myseller_model->HandleBCN($warehouse, $fl);																
													}
												}	
							
										
								
								}
								 else  	$this->_notify('Push No Commit - No data to update '.$rec,printcool ($ourtransactions[$rec],true).printcool ($v['data'],true));
						}
				        else
				        {
					       // echo '<td valign="top"  style="background:#F9D2D3;">NEW DATATYPE';
					       // printcool ($v);
						   $this->_notify('NEW DATATYPE',$v);
				        }
			            //echo '</td><td></td></tr>';
			        }
	
		       // echo '</table>';
		echo 'OK';
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
                    GoMail(array(
									'msg_title' => 'Item marked shipped ERROR @'.CurrentTime(),
									'msg_body' => printcool(simplexml_load_string($responseXml), TRUE,''),
									'msg_date' => CurrentTime()			
									), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
                }
                else
                {
                    $xml = simplexml_load_string($responseXml);
					/*GoMail(array(
									'msg_title' => 'Item marked shipped SellingManagerSoldOrder dump @'.CurrentTime(),
									'msg_body' => printcool($xml, TRUE,''),
									'msg_date' => CurrentTime()			
									), 'errors@la-tronics.com', $this->config->config['no_reply_email']);*/
                    return (string)$xml->SellingManagerSoldOrder->ActualShippingCost;
                    //printcool($xml);
                }
    }
function _GetReturnData($returnid = 0, $txt = '')// = 5046058098
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
				 
		$answer['returncurrentType'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['currentType'])));
		$answer['returnnotif'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['type'])));
		$answer['returntype'] = $answer['returnnotif'];
		$answer['returnreason'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['reason'])));
		$answer['returncomment'] = $data['summary']['creationInfo']['comments']['content'];
		$answer['returnQuantity'] = $data['summary']['creationInfo']['item']['returnQuantity'];
		$answer['ebayRefundAmount'] = $data['summary']['sellerTotalRefund']['estimatedRefundAmount']['value'];
		$answer['ebayreturntime'] = CleanBadDate($data['summary']['creationInfo']['creationDate']['value']);
		if (isset($data['detail']['returnShipmentInfo']['shippingLabelCost']['totalAmount']['value'])) $answer['ebayreturnshipment'] = floater($data['detail']['returnShipmentInfo']['shippingLabelCost']['totalAmount']['value']);
		else
		{
			$answer['ebayreturnshipment'] = 0;
			GoMail(array(
			'msg_title' => 'No $data[\'detail\'][\'returnShipmentInfo\'][\'shippingLabelCost\'][\'totalAmount\'][\'value\'] '.$txt.'@ '.CurrentTime(),
			'msg_body' => printcool($data, TRUE,''),
			'msg_date' => CurrentTime()			
			), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
		}
		return ($answer);
}
function _ListingIdFromItemID($itemid = '')
{
	$this->db->select('e_id');
	$this->db->where('ebay_id', (int)$itemid);
	$e = $this->db->get('ebay');
	if ($e->num_rows() > 0)
	{
		$er = $e->row_array();
		return $er['e_id'];	
	}
	else return 0;	
}
function _GetEbayFromItemID($itemid = '')
	{
		$this->db->select('e_id');
		$this->db->where('ebay_id', $itemid);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
			{
			$r = $this->query->row_array();	
			return ($r['e_id']);
			}
		else return 0;
	}
function _GetEbayTitleFromItemID($itemid = '')
	{
		$this->db->select('e_id, e_title');
		$this->db->where('ebay_id', $itemid);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
			{
			$r = $this->query->row_array();	
			return ($r);
			}
		else return array('e_id' => 0, 'e_title' => 'Not Found', 'gsid1' => 0);
	}	

function _logaction($location = '', $type = 'M', $datafrom = '', $datato = '', $eid = '', $itemid = '', $transid = '', $admin = '')
{

		foreach ($datato as $k => $v)
		{
			if ($v != $datafrom[$k])
			{
				if (isset($this->session->userdata['ownnames'])) $admin = $this->session->userdata['ownnames'];
				elseif ($admin == '') $admin = 'Cron';

				
					
					$hmsg = array ('msg_title' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_body' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_date' => CurrentTime());
					
					//GoMail($hmsg, 'errors@la-tronics.com', $this->config->config['no_reply_email']);
				
				if ($k == 'Sold') $type = 'Q';
				$this->db->insert('ebay_actionlog', array('atype' => $type, 'e_id' => (int)$eid, 'ebay_id' => (int)$itemid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom[$k], 'datato' => $v, 'field' => $k, 'admin' => $admin, 'trans_id' => (int)$transid, 'ctrl' => $location)); 			
			}
		}
}
function _notify($title,$body)
{
	$this->db->insert('ebay_push_log', array('ebp_title' => $title.' @ '.CurrentTime(), 'ebp_body' =>printcool($body, TRUE), 'ebp_ts' =>mktime()));
	$this->db->query('DELETE FROM ebay_push_log WHERE ebp_ts < '.(mktime()-2592000));
	//GoMail(array ('msg_title' => 'NOTIFY: '.$title.' @ '.CurrentTime(), 'msg_body' => printcool($body, TRUE), 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);	
}

function wlog($bcn, $id, $field, $from, $to, $place = false, $url = false)
{

	if (!$place) $place = $this->router->method;	
	if (!$url) $url = $place;
	$this->db->insert('warehouse_log', array('bcn' => $bcn, 'wid'=> $id, 'time' => CurrentTime(), 'ts' => mktime(), 'datafrom' => $from, 'datato' => $to, 'field' => $field, 'admin' => 'Cron', 'ctrl' => $place, 'url' => $url, 'year' => date('Y'), 'month' => date('m'), 'day' => date('d')));	
}
function _DoBCNS($i)
{
	
	/*$insert[$inskey]['datetime'] = CleanBadDate($v['data']['CreatedDate']);
									$insert[$inskey]['mkdt'] = $mkdt;
									$insert[$inskey]['rec'] = $inskey;
									//$insert[$inskey]['paid'] = (string)$v['data']['AmountPaid;
									if (isset($v['data']['ShippingServiceSelected'])) $insert[$inskey]['paid'] = ((float)$v['data']['TransactionPrice']*(int)$v['data']['QuantityPurchased'])+(float)$v['data']['ShippingServiceSelected'];
									//////!!!!!!!!!!/////////
									else $insert[$inskey]['paid'] = (float)$v['data']['TransactionPrice']*(int)$v['data']['QuantityPurchased'];
									$insert[$inskey]['eachpaid'] = (float)$v['data']['TransactionPrice'];
									$insert[$inskey]['fee'] = (float)$v['data']['FinalValueFee'];
									$insert[$inskey]['shipping'] = (string)$v['data']['ShippingServiceSelected']['ShippingService'];
									$insert[$inskey]['tracking'] = (string)$v['data']['ShippingServiceSelected']['ShippingServiceCost'];
									if (isset($v['data']['PaidTime'])) $insert[$inskey]['paidtime'] = CleanBadDate((string)$v['data']['PaidTime']);
									else $insert[$inskey]['paidtime'] = ''; 
									$insert[$inskey]['itemid'] = (string)$v['data']['Item']['ItemID'];
									$insert[$inskey]['buyerid'] = (string)$v['data']['Buyer']['UserID'];
									$insert[$inskey]['buyeremail'] = (string)$v['data']['Buyer']['Email'];
									$insert[$inskey]['qtyof'] = (int)$v['data']['Item']['Quantity'];
									$insert[$inskey]['qty'] = (int)$v['data']['QuantityPurchased'];	
									//$insert[$inskey]['asc'] = (string)$v['data']['ActualShippingCost'];	
									$insert[$inskey]['ssc'] = (string)$v['data']['ShippingServiceSelected']['ShippingServiceCost'];
									$insert[$inskey]['ebsold'] = (string)$v['data']['Item']['SellingStatus']['QuantitySold'];	
									$insert[$inskey]['transid'] = (string)$v['data']['TransactionID'];
									$insert[$inskey]['pushed'] = 1;
									$pool[$inskey]['itemid'] = (string)$v['data']['Item']['ItemID'];
									$pool[$inskey]['transid'] = (string)$v['data']['TransactionID'];*/
				
				
				$this->load->model('Myseller_model');
				
				$this->Myseller_model->AssignBCN($i, 1);
				/*
					$this->db->select('e_id, e_title, ebay_id, quantity, ebayquantity, e_part, e_qpart, ebsold');
					$this->db->where('ebay_id', $i['itemid']);
					$eb = $this->db->get('ebay');
					if ($eb->num_rows() > 0) 
						{
							$res = $eb->row_array();
							$qty = $res['quantity'];
							$resoldquantity = $qty;
							$res['e_part'] = commasep(commadesep($res['e_part']));
							$bcnsold = $res['e_part'];							
							$res['quantity'] = $res['quantity'] - $i['qty'];
							$bcncount = $this->_RealCount($res['e_part']);					
												
							$this->printstr .= printcool ($bcncount, true);
							if ($bcncount > 0) 
							{
								$bcns = explode(',', $res['e_part']);
								
								$this->printstr .= printcool ($bcns,true);		
								$this->printstr .= printcool ('BCN MOVE BEGIN', true);
								
								$start = 1;
								$moved = array();
								$unavailble = 0;
								while ($start <= $i['qty'])
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
								$moved = commasep(commadesep(implode(',', $moved)));
								if ((int)$unavailble > 0) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">BCN Auto Update - </span><span style="color:red;">LISTING DOES NOT HAVE ENOUGH BCN ITEMS - "'.$unavailble.'" Unavailable for total required '.$i['qty'].'</span>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => 1));
								
								$this->db->update('ebay_transactions', array('sn' => $moved), array('rec' => (int)$i['rec']));
								
								
												  
								$this->printstr .= printcool ($moved, true);
								$this->printstr .= printcool ($bcns,true);
								$bcns = commasep(commadesep(implode(',', $bcns)));
								$this->printstr .= printcool ($bcns,true);
								
								$this->printstr .= printcool ('BCN MOVE END',true);
								$this->db->update('ebay', array('e_part' => $bcns, 'e_qpart' => $this->_RealCount($bcns), 'ngen' =>  $this->_CountGhosts($bcns)), array('e_id' => (int)$res['e_id']));
								
								$this->_logaction('Transactions', 'B', array('BCN' => $bcnsold), array('BCN' => $bcns), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
								$this->_logaction('Transactions', 'B', array('BCN Count' => $this->_RealCount($bcnsold)), array('BCN Count' => $this->_RealCount($bcns)), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
								$this->_logaction('Transactions', 'B', array('Transaction BCN' => ''), array('Transaction BCN' => $moved), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
								
															
							
							}
							else
							{
							$this->printstr .= printcool ('<span class="red">Cannot allocate BCN pcs.</span> from Listing <a href="'.Site_url().'Myebay/Edit/'.$res['e_id'].'" target="_blank">'.$res['e_id'].'</a> | ItemID: <a href="http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item='.$res['ebay_id'].'" target="_blank">'.$res['ebay_id'].'</a> for Transaction record '.$i['rec'], true);
							$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:red;">Cannot auto allocate BCN piece</span> from Listing to Transaction', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => 1));
							}
							$this->printstr .= printcool ('----', true);
							$colorstep = '#FF9900';
							if ($res['quantity'] < 1) $colorstep = 'red';
							

							$this->db->update('ebay', array('quantity' => $res['quantity'], 'ebayquantity' => $i['qtyof'], 'ebsold' => $i['ebsold']), array('e_id' => (int)$res['e_id']));
							$this->_logaction('Transactions', 'Q', array('Quantity' => $resoldquantity), array('Quantity' => $res['quantity']), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
							$this->_logaction('Transactions', 'Q', array('Local eBay Quantity' => $res['ebayquantity']), array('Local eBay Quantity' => $i['qtyof']-$i['ebsold']), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
							
							$this->_logaction('Transactions', 'Q', array('Sold' => $res['ebsold']), array('Sold' => $i['ebsold']), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
							
							
								
						}
						else
						{
							$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => 'Listing with eBay ItemID <span style="color:red">NOT FOUND</span> in database. Listing quantity to be manually changed, deduction by <span style="color:#FF9900;">'.$i['qty'].'</span>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => $i['itemid'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => 1)); 							
						}
						
						if (isset($returnmove)) return $returnmove;*/
}
}