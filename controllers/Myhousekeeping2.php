<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myhousekeeping2 extends Controller {

function Myhousekeeping2()
	{
		parent::Controller();		
		$this->load->model('Mywarehouse_model');
		$this->load->model('Auth_model');
				
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('action',$this->session->flashdata('action'));
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Warehouse');
		$this->mysmarty->assign('newlayout', TRUE); 
		
	}
function parseadminhistory()
{
	$this->db->where('trec >', 0);
	$this->db->order_by('msg_id','DESC');
	//$this->db->limit(500);
	$h = $this->db->get('admin_history');	
	foreach ($h->result_array() as $ah)
	{
		$msg= explode('Transaction Updated:',$ah['msg_title']);
		if (isset($msg[1]))
		{
			$mmsg = 	explode('|',$msg[1]);
			foreach ($mmsg as $m)
			{
				$mmmsg = 	explode(': ',$m);
				if (count($mmmsg)>0)
				{
					$data['type'] = trim($mmmsg[0]);
					if ($data['type'] == 'Shipping' || $data['type'] == 'Tracking')
					{
						$data['values'] = explode(' / ',$mmmsg[1]);
						if (isset($data['values'][1]))
						{
							$data['values'][0] = trim($data['values'][0]);	
							if ($data['values'][0] == '<span style="color:blue;">[EMPTY]</span>')
							{
								$data['values'][1] = trim($data['values'][1]);
								if ($data['type'] == 'Shipping'){	
								$data['values'][1] = str_replace('<span style="color:red;">','',$data['values'][1]);
								$data['values'][1] = str_replace('</span>','',$data['values'][1]);
								if ($data['values'][1] == '0.0') unset($data['values'][1]);
								else 
								{
									$data['values'][0] = $data['values'][1];
									unset($data['values'][1]);
								}
								}
								
								if ($data['type'] == 'Tracking'){	
								$data['values'][1] = str_replace('<span style="color:red;">','',$data['values'][1]);
								$data['values'][1] = str_replace('</span>','',$data['values'][1]);
								if ($data['values'][1] == '0.0') unset($data['values'][1]);
								if ($data['values'][0] == '<span style="color:blue;">[EMPTY]</span>') unset($data['values']);
								
								}
								
							}
							else unset($data['values'][1]);
						}
					if ($data['values'][0] != '') $list[$ah['trec']][$data['type']] = $data['values'][0];	
				//	printcool ($data);
					}
					
				}

			}
		}
		
		
	}
	//printcool ($list);
	foreach ($list as $k => $v)
	{
		$this->db->select('et_id, shipping, tracking');
		$this->db->where('rec', $k);
		$t = $this->db->get('ebay_transactions');	
		foreach ($t->result_array() as $tr)
		{
			//printcool($tr);
			//printcool($v,false,$k);
			if (trim($tr['shipping']) == '' && isset($v['Shipping']))
			{
				 printcool ($v['Shipping']);
				 $this->db->update('ebay_transactions',array('shipping' => $v['Shipping']), array('et_id' => $tr['et_id']));
			}
			if (trim($tr['tracking']) == '' && isset($v['Tracking']))
			{
				 printcool ($v['Tracking']);
				  $this->db->update('ebay_transactions',array('tracking' => $v['Tracking']), array('et_id' => $tr['et_id']));
			}
		}
	}
}

function getzerossc()
{
	$this->db->select('et_id, shipping, tracking, asc, ssc');
		$this->db->where('ssc', '0.00');
		$this->db->order_by('et_id','DESC');
		//$this->db->limit(500);
		$t = $this->db->get('ebay_transactions');
		foreach ($t->result_array() as $tr)
		{
			if ($tr['asc'] != '0.00') 
			{
				printcool ($tr);
				$this->db->update('ebay_transactions',array('ssc' => $tr['asc'], 'ssc_upd' => 1), array('et_id' => $tr['et_id']));
			}
		}
}
function syncdb()
{
	//warehouseTMP
		
		
		/*$sg = $this->load->database('sg', TRUE);
		$sg->orderby('e_id', 'DESC');
		$sg->limit(1000);
		$eb = $sg->get('ebay');
		if ($eb->num_rows > 0)
		{
			foreach ($eb->result_array() as $e)
			{
				$oldebay[$e['e_id']] = $e;	
			}			
		}
		
		$sg = $this->load->database('default', TRUE);
		$sg->orderby('e_id', 'DESC');
		$sg->limit(1000);
		$eb = $sg->get('ebay');
		if ($eb->num_rows > 0)
		{
			foreach ($eb->result_array() as $e)
			{
				$newebay[$e['e_id']] = $e;	
			}			
		}
		foreach($oldebay as $k => $v)
		{
			if (!isset($newebay[$k]))
			{
				 echo '<table><tr><td valign="top">'.printcool($v['e_title'],'','OLD '.$k).'</td><td valign="top">Doesnt Exist</td></tr></table>';
				 unset($v['e_id']);
				// $sg->insert('ebay', $v);
			}
			if (isset($newebay[$k]) && $newebay[$k]['e_title'] != $v['e_title'])
			{
				unset($v['e_id']);
			echo '<table><tr><td valign="top">'.printcool($v['e_title'],'','OLD '.$k).'</td><td valign="top">'.printcool($newebay[$k]['e_title'],'','NEW '.$k).'</td></tr></table>';
			 //$sg->insert('ebay', $v);
			}
				
		}*/
		
		/*
		$sg = $this->load->database('sg', TRUE);
		$sg->orderby('e_id', 'DESC');
		$sg->where('et_id >', 51121);
		$eb = $sg->get('ebay_transactions');
		if ($eb->num_rows > 0)
		{
			foreach ($eb->result_array() as $e)
			{
				$oldebay[$e['et_id']] = $e;	
			}			
		}
		
		$sg = $this->load->database('default', TRUE);
		$sg->orderby('e_id', 'DESC');
		$sg->where('et_id >', 51121);
		$eb = $sg->get('ebay_transactions');
		if ($eb->num_rows > 0)
		{
			foreach ($eb->result_array() as $e)
			{
				$newebay[$e['et_id']] = $e;	
			}			
		}
		foreach($oldebay as $k => $v)
		{
			if (!isset($newebay[$k]))
			{
				 echo '<table><tr><td valign="top">'.printcool($v['e_title'],'','OLD '.$k).'</td><td valign="top">Doesnt Exist</td></tr></table>';
				 unset($v['e_id']);
				// $sg->insert('ebay', $v);
			}
			if (isset($newebay[$k]) && $newebay[$k]['itemid'] != $v['itemid'])
			{
				unset($v['e_id']);
			echo '<table><tr><td valign="top">'.printcool($v['itemid'],'','OLD '.$k).'</td><td valign="top">'.printcool($newebay[$k]['itemid'],'','NEW '.$k).'</td></tr></table>';
			 //$sg->insert('ebay', $v);
			}
				
		}
		*/
		/*
		$sg = $this->load->database('sg', TRUE);
		$sg->orderby('e_id', 'DESC');
		$sg->where('et_id >', 51121);
		$eb = $sg->get('ebay_transactions');
		if ($eb->num_rows > 0)
		{
			foreach ($eb->result_array() as $e)
			{
				$oldebay[$e['et_id']] = $e;	
			}			
		}
		
		$sg = $this->load->database('default', TRUE);
		$sg->orderby('e_id', 'DESC');
		$sg->where('et_id >', 51121);
		$eb = $sg->get('ebay_transactions');
		if ($eb->num_rows > 0)
		{
			foreach ($eb->result_array() as $e)
			{
				$newebay[$e['et_id']] = $e;	
			}			
		}
		foreach($oldebay as $k => $v)
		{
			foreach ($v as $kk => $vv)
			{
				if ($kk == 'asc')
				{
					if (floater($vv) > floater( $newebay[$k][$kk]))
					{
						printcool ($vv,'',$k.' OLD '.$kk);
					printcool  ($newebay[$k][$kk],'',$k.' NEW '. $kk);	
					//$this->db->update('ebay_transactions', array($kk => $vv), array('et_id' => $v['et_id']));
					}
				}
				else
				{
					if ($vv != $newebay[$k][$kk])
					{
					printcool ($vv,'',$k.' OLD '.$kk);
					printcool  ($newebay[$k][$kk],'',$k.' NEW '. $kk);	
					//$this->db->update('ebay_transactions', array($kk => $vv), array('et_id' => $v['et_id']));
					}
				}
			}
			
				
		}
		
		*/
		
		
		
		//exit();
		$this->db->where('createddatemk >', 1490741155);
		$this->db->orderby('wid', 'DESC');
		$w = $this->db->get('warehouse_tmp3dayout');
		if ($w->num_rows > 0)
		{
			foreach ($w->result_array() as $e)
			{
				$temp[$e['wid']] = $e;	
			}			
		}
				$this->db->where('createddatemk >', (1490741155-90000));
		$this->db->orderby('wid', 'DESC');
		$w = $this->db->get('warehouse');
		if ($w->num_rows > 0)
		{
			foreach ($w->result_array() as $e)
			{
				$current[$e['wid']] = $e;	
			}			
		}
		foreach($temp as $k => $v)
		{
			if (!isset($current[$k]))
			{
				 echo '<table><tr><td valign="top">'.printcool($v['title'],'','temp '.$k).'</td><td valign="top">Doesnt Exist</td></tr></table><br><br>';
				 
				 $this->db->insert('warehouse', $v);
			}
			if (isset($current[$k]) && $current[$k]['title'] != $v['title'])
			{
					echo '<table><tr><td valign="top">'.printcool($v['title'],'','temp '.$k).'</td><td valign="top">'.printcool($current[$k]['title'],'','NEW '.$k).'</td></tr></table>';
			}
			
		}
		//
}
function findbadasc()
{
	set_time_limit(1200);
		ini_set('mysql.connect_timeout', 1200);
		ini_set('max_execution_time', 1200);  
		ini_set('default_socket_timeout', 1200); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
	$this->db->select('et_id,rec, asc, ssc');
	$this->db->where('asc = ssc');
	$this->db->where('asc >', 0);
	$this->db->where('et_id >',46833);
	$this->db->orderby('et_id', 'ASC');
	$e = $this->db->get('ebay_transactions');
	if ($e->num_rows() > 0)
	{
		$c = 1;
		printcool ($e->num_rows());

		foreach ($e->result_array() as $et)
		{
			//printcool ($et);	
			
			if ($c< 2500)
			{
				
				
				$verb = 'GetSellingManagerSaleRecord';
				$compatabilityLevel = 959;
				//Create a new eBay session with all details pulled in from included keys.php
				$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
				$this->db->select('et_id, itemid, transid, shipping, tracking, asc, ssc');	
				$this->db->where('rec', (int)$et['rec']);
				$q = $this->db->get('ebay_transactions');
				
				if ($q->num_rows() > 0) 
				{
					$t = $q->row_array();
		
						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetSellingManagerSaleRecordRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
						$requestXmlBody .= "<ItemID>$t[itemid]</ItemID>";
						$requestXmlBody .= "<TransactionID>$t[transid]</TransactionID>";
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
						$requestXmlBody .=  '</GetSellingManagerSaleRecordRequest>';			
		
						//send the request and get response
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);//printcool($xml);
						$item = $xml->SellingManagerSoldOrder;
					
						if ($item && isset($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost))
						{
							$asc = floater((string)$item->ActualShippingCost);
							$ssc = floater($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost);
							if ($asc == $ssc) echo 'SAME VALUES: '.$et['rec'].'<br><br>';
							else
							{
							if ($asc != $et['asc'])
							{
								 echo 'NEW ASC: '.$et['et_id'].' ($'.$asc.') DB - ASC: $'.$et['asc'].' SSC: $'.$et['ssc'].'<br><br>';
								 $this->db->update('ebay_transactions', array('asc' => $asc), array('et_id' => $et['et_id']));
							}
							if ($ssc != $et['ssc']) 
							{
								echo 'NEW SSC: '.$et['et_id'].' ($'.$ssc.') DB - ASC: $'.$et['asc'].' SSC: $'.$et['ssc'].'<br><br>';
								$this->db->update('ebay_transactions', array('ssc' => $ssc, 'ssc_old' => $et['ssc']), array('et_id' => $et['et_id']));					
							}
							}
						}
						else echo 'No Item: '.$et['et_id'].'<br><br>';
						
				}
				
				
				
				
				
				
					
			}
			else (exit($et['et_id']));
			$c++;
		}
	}
}
function nopaypalfeewarehouse()
{
    $this->db->select('wid,paypal_fee,channel');
    $this->db->where('channel', 4);
    $this->db->where('paypal_fee >', 0);
    $wp = $this->db->get('warehouse');
    if ($wp->num_rows() > 0)
    {
        foreach ($wp->result_array() as $w)
        {
            printcool($w);
         // $this->db->update('warehouse', array('paypal_fee' => 0), array('wid' => $w['wid']));
        
        }
        
        
        
    }
    
    
    
}

function trdetcheckout()
{
    ini_set('memory_limit','8000M');
    $this->db->where('vended', 1);
    $wp = $this->db->get('warehouse');
    if ($wp->num_rows() > 0)
    {
        foreach ($wp->result_array() as $w)
        {
            
            $this->db->where('w_id', (int)$w['wid']);
            $this->db->where('sold_id', (int)$w['sold_id']);
            $this->db->where('channel', (int)$w['channel']);
            
            $ww = $this->db->get('transaction_details');
            if ($ww->num_rows() > 0)
            {
               
                
                //printcool ($ww->result_array());
                if ($ww->num_rows() > 1)
                { printcool($w);
                    $first = false;
                
                    foreach ($ww->result_array() as $trdup)
                    {
                        if ($first)
                        {
                            $diff = 0;
                            foreach ($first as $fk => $fv)
                            {
                                if ($fk != 'td_id' && $fv != $trdup[$fk])
                                {
                                    $diff++;
                                }                                
                            }
                            if ($diff > 0)
                            {
                                
                                printcool ($first,'','FIRST');
                                printcool ($trdup,'','$trdup');
                                printcool ('-------------');
                            } 
                            else {
                              // $this->db->where('td_id', $first['td_id']);
                                $this->db->delete('transaction_details');
                                printcool ($first['td_id'].' IS = TO '.$trdup['td_id']);
                            }
                        }
                        $first = $trdup;
                        

                    }
                }    
            }        
        }        
    }
}
function ch4trdetfix()
{
    
    $this->db->where('channel', 4);
            $ww = $this->db->get('transaction_details');
            if ($ww->num_rows() > 0)
            {
                foreach ($ww->result_array() as $t)
                {
                 printcool ($t);
                 //$this->db->update('transaction_details', array('paypal_fee' => 0), array('td_id' => $t['td_id']));
                }
            } 
    
    
}
function ch4findataupdate()
{
    $this->db->where('channel', 4);
            $ww = $this->db->get('transaction_details');
            if ($ww->num_rows() > 0)
            {
                foreach ($ww->result_array() as $t)
                {
                 //printcool ($t, '', 'TRDET');
                 $this->db->select('wid, sold_id, channel, sellingfee, paid, paypal_fee,createddate , createddatemk');
                 $this->db->where('wid', (int)$t['w_id']);
                 $this->db->where('sold_id', (int)$t['sold_id']);
                 $this->db->where('channel', (int)$t['channel']);
                 $w=$this->db->get('warehouse');
                 if ($w->num_rows() > 0)
                 {
                     if ($w->num_rows() > 1) printcool ('MULTIPLE');
                     $wh = $w->row_array();
                     
                     $line = false;
                     if ($t['fee'] != $wh['sellingfee']) 
                     {
                         printcool ($t,'','trfee');
                         printcool ($wh,'','whfee');
                         //$this->db->update('transaction_details',array('fee' => $wh['sellingfee']),array('td_id' => $t['td_id']));
                          $line = true;
                     }
                     if ($t['paid'] != $wh['paid']) 
                     {
                         printcool ($t,'','trpaid');
                         printcool ($wh,'','whpaid');
                         //$this->db->update('transaction_details',array('paid' => $wh['paid']),array('td_id' => $t['td_id']));
                         $line = true;
                     }
                     if ($t['paypal_fee'] != $wh['paypal_fee']) 
                     {
                         printcool ($t,'','trpaypal_fee');
                         printcool ($wh,'','whpaypal_fee');
                         $line = true;
                     }
                     if ($line) printcool('--------------');
                 }
                }
            } 
}

function deletesomebcns()
{
	$bcns='017-2367
,017-2366
,017-2365
,017-2364
,017-2363
,017-2362
,017-2361
,017-2330
,017-2329
,017-2328
,017-2327
,017-2326
,017-2325
,017-2324
,036-1535
,036-1533
,036-1532
,036-1531
,036-1530
,036-1529
,036-1528
,036-1527
,036-1526
,036-1525
,036-1524
,036-1523
,036-1522
,036-1521
,036-1520
,036-1519
,036-1518
,036-1517
,036-1516
,036-1515
,036-1514
,036-1513
,036-1512
,036-1511
,036-1510
,036-1509
,036-1508
,036-1507
,036-1506
,036-1505
,036-1504
,036-1499
,036-1494
,036-468
,036-409
,036-408
,036-396
,036-392
,036-386
,036-384
,036-369
,036-368
,036-334
,036-266
,036-26
,026-919
,026-915
,026-914
,026-907
,026-876
,026-174
,016-663
,016-661
,016-660
,016-655
,016-640
,125-11815
,125-11776
,125-11775
,125-11764
,125-11763
,125-11750
,125-11729
,125-11425
,125-11424
,125-11423
,125-11422
,125-11421
,125-11420
,125-11419
,125-11418
,125-10940
,125-10817
,125-7919
,125-4176
,125-4073
,125-3881
,125-2883
,125-2739
,125-0256';

$bcns=explode(',', $bcns);
foreach ($bcns as $b)
{
	//printcool (trim($b));	
	$this->db->where('bcn',trim($b));
	{
		$qb = $this->db->get('warehouse');
		printcool ($qb->row_array());
		$n = $qb->row_array();
		$this->db->update('warehouse', array('deleted' => 1), array('wid' => (int)$n['wid']));
	}
}
	
}


function WarehouseBuyer()
{
	$this->db->select('woid, buyer');
	$o = $this->db->get("warehouse_orders")->result_array();
	foreach($o as $oo)
	{
		$ooo[$oo['woid']] = $oo['buyer'];
	}

	$this->db->select('wid,sold, sold_id');
	$this->db->where('sold', 'Warehouse');
	$w = $this->db->get("warehouse")->result_array();
	//printcool ($w);
	foreach ($w as $ww)
	{
		$this->db->update('warehouse',array('sold' => $ooo[$ww['sold_id']]),array('wid' => $ww['wid']));

	}

}




function matchlog()
{
	$sql = 'SELECT * FROM ebay_push_log WHERE ebp_title LIKE "%Return%"';
	$q =  $this->db->query($sql);
	if ($q->num_rows() > 0)
	{
		foreach($q->result_array() as $res)
		{
			printcool ($res);
		}
	}


}

function printreturn()
{
	$this->db->select('et_id, returnid');
	$this->db->where('returnid >',0);
	//$this->db->where('ret_reproc', 0);
	$this->db->orderby('et_id','ASC');
	//$this->db->limit('30');
	$e = $this->db->get("ebay_transactions")->result_array();
	require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
	require($this->config->config['ebaypath'].'get-common/keys.php');

	foreach ($e as $ee)
	{
		$this->db->update('ebay_transactions',array('ret_reproc' => 1), array('et_id' => $ee['et_id']));
		printcool ($ee);
		$url = 'https://api.ebay.com/post-order/v2/return/'.(int)$ee['returnid']; //?fieldgroups=FULL
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
		//printcool ($data);
		$insert=array();
		$insert['et_id'] = $ee['et_id'];
		$insert['returnId'] = $data['summary']['returnId'];
		$insert['currentType'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['currentType'])));
		$insert['state'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['state'])));
		$insert['status'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['status'])));
		$insert['returntype'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['type'])));
		$insert['reason'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['reason'])));
		$insert['reasonType'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['reasonType'])));
		$insert['comments'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['comments']['content'])));
		$insert['creationDate'] = CleanBadDate($data['summary']['creationInfo']['creationDate']['value']);
		$insert['itemId'] = $data['summary']['creationInfo']['item']['itemId'];
		$insert['transactionid'] = $data['summary']['creationInfo']['item']['transactionId'];
		$insert['returnQuantity'] = $data['summary']['creationInfo']['item']['returnQuantity'];
		$insert['itemPrice'] =  $data['detail']['itemDetail']['itemPrice']['value'];
		$na_data = NULL;
		if (isset($data['detail']['responseHistory'])) $insert['responseHistory'] = serialize($data['detail']['responseHistory']);
		else $na_data = $na_data."|responseHistory";
		if (isset($data['summary']['sellerTotalRefund']['estimatedRefundAmount']['value'])) $insert['estimatedAmount'] = $data['summary']['sellerTotalRefund']['estimatedRefundAmount']['value'];
		else $na_data = $na_data."|estimatedRefundAmount";
		if (isset($data['detail']['refundInfo']['actualRefundDetail']['actualRefund']['totalAmount']['value'])) $insert['actualRefund'] = $data['detail']['refundInfo']['actualRefundDetail']['actualRefund']['totalAmount']['value'];
		else $na_data = $na_data."|actualRefundDetail";
		if (isset($data['detail']['refundInfo']['actualRefundDetail']['actualRefund']['itemizedRefundDetail'])) $insert['itemizedRefundDetail'] = serialize($data['detail']['refundInfo']['actualRefundDetail']['actualRefund']['itemizedRefundDetail']);
		else $na_data = $na_data."|itemizedRefundDetail";
		if (isset($data['detail']['returnShipmentInfo']['shipmentTracking']['trackingNumber'])) $insert['trackingNumber'] = $data['detail']['returnShipmentInfo']['shipmentTracking']['trackingNumber'];
		else $na_data = $na_data."|trackingNumber";
		if (isset($data['detail']['returnShipmentInfo']['shipmentTracking']['carrierEnum'])) $insert['carrierEnum'] = $data['detail']['returnShipmentInfo']['shipmentTracking']['carrierEnum'];
		else $na_data = $na_data."|carrierEnum";
		if (isset($data['detail']['returnShipmentInfo']['shippingLabelCost']['totalAmount']['value'])) $insert['shippingLabelCost'] = $data['detail']['returnShipmentInfo']['shippingLabelCost']['totalAmount']['value'];
		else $na_data = $na_data."|shippingLabelCost";
		$insert['datalog'] = serialize($data);
		$insert['na_data'] = $na_data;
		$this->db->insert('ebay_refunds', $insert);
		/*
		$answer['returncurrentType'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['currentType'])));
		$answer['returnnotif'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['type'])));
		$answer['returntype'] = $answer['returnnotif'];
		$answer['returnreason'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['reason'])));
		$answer['returncomment'] = $data['summary']['creationInfo']['comments']['content'];
		$answer['returnQuantity'] = $data['summary']['creationInfo']['item']['returnQuantity'];
		$answer['ebayRefundAmount'] = $data['summary']['sellerTotalRefund']['estimatedRefundAmount']['value'];
		$answer['ebayreturntime'] = CleanBadDate($data['summary']['creationInfo']['creationDate']['value']);
		*/
		/*if (isset($data['detail']['returnShipmentInfo']['shippingLabelCost']['totalAmount']['value'])) $answer['ebayreturnshipment'] = floater($data['detail']['returnShipmentInfo']['shippingLabelCost']['totalAmount']['value']);
		else
		{
			$answer['ebayreturnshipment'] = 0;
			GoMail(array(
				'msg_title' => 'No $data[\'detail\'][\'returnShipmentInfo\'][\'shippingLabelCost\'][\'totalAmount\'][\'value\'] '.$txt.'@ '.CurrentTime(),
				'msg_body' => printcool($data, TRUE,''),
				'msg_date' => CurrentTime()
			), $this->config->config['support_email'], $this->config->config['no_reply_email']);
		}*/
	}

}


function ClearERDups()
{
	$this->db->orderby('er_id', 'DESC');
	$d = $this->db->get('ebay_refunds');
	foreach ($d->result_array() as $dd)
	{
		if (!isset($ddd[$dd['returnId']])) $ddd[$dd['returnId']] = $dd;
		else
		{
			$this->db->where('er_id', $dd['er_id']);
			$this->db->delete('ebay_refunds');
		}
	}
	//printcool($ddd);

}
	function populateetid()
	{

		$r = $this->db->get("ebay_refunds")->result_array();
		foreach($r as $rr)
		{
			if ($rr['et_id'] == 0)
			{
				$this->db->select('et_id');
				$this->db->where('transid',$rr['transactionid']);
				$ebtr = $this->db->get('ebay_transactions');
				if ($ebtr->num_rows() > 0)
				{
					$ebdata=$ebtr->row_array();
					$this->db->update('ebay_refunds', array('et_id' => $ebdata['et_id']), array('er_id' => $rr['er_id']));
				}
			}

		}

	}
	function RefundtoTransactions()
	{

		$r = $this->db->get("ebay_refunds")->result_array();
		foreach ($r as $rr)
		{
			$refunds[$rr['transactionid']] = $rr;
			$transactionids[] = $rr['transactionid'];
		}
		$c = 1;
		foreach($transactionids as $t)
		{
			$this->db->select('et_id, transid, ebayRefundAmount,ebayreturnshipment,returnQuantity, paid, sellingstatus');
			if ($c==1)$this->db->where('transid',$t);
			else $this->db->or_where('transid',$t);
			$et = $this->db->get("ebay_transactions")->result_array();
			foreach($et as $etr)
			{
				if (isset($refunds[$etr['transid']]))
				{
					if (floater($refunds[$etr['transid']]['actualRefund']) > 0)
					{
						$ebayRefundAmount = floater($refunds[$etr['transid']]['actualRefund']);
					}
					else
					{
						$ebayRefundAmount = floater($refunds[$etr['transid']]['estimatedAmount']);
					}
					if (floater($refunds[$etr['transid']]['shippingLabelCost']) > 0)
					{
						if(strtolower($refunds[$etr['transid']]['returntype']) == 'remorse') $ebayreturnshipment = 0;
						else $ebayreturnshipment = floater($refunds[$etr['transid']]['shippingLabelCost']);
					}
					else
					{
						$ebayreturnshipment=0;
					}
					$update = array();
					if ($etr['ebayRefundAmount'] != $ebayRefundAmount) $update['ebayRefundAmount'] = $ebayRefundAmount;
					if ($etr['ebayreturnshipment'] != $ebayreturnshipment) $update['ebayreturnshipment'] = $ebayreturnshipment;
					if (strtolower($refunds[$etr['transid']]['state']) == 'closed')
					{
						if (floater($etr['paid']) > 0 && $etr['sellingstatus'] != 'PartiallyPaid')
						{
							$update['paid'] = 0;
							$update['sellingstatus'] ='Refunded';
							printcool($etr['et_id']);
						}
		}
					if (count($update) >0)
					{
						$this->db->update('ebay_transactions', $update, array('et_id' => $etr['et_id']));
						printcool ($update, false,$etr['et_id']);
						$sql = 'SELECT wid, bcn, returned, return_pricesold, return_shippingcost, return_netprofit';
						$sql .= ' FROM warehouse WHERE `channel` = 1 AND `deleted` = 0 AND `nr` = 0 AND `sold_id` = '.(int)$etr['et_id'];



					 $q =  $this->db->query($sql);

					if ($q->num_rows() > 0)
					{
						if ((int)$etr['returnQuantity'] == 0) $etr['returnQuantity'] = 1;
						$bcn_ebayRefundAmount = floater($ebayRefundAmount/$etr['returnQuantity']);

						if ($ebayreturnshipment > 0) $bcn_ebayreturnshipment = floater($ebayreturnshipment/$etr['returnQuantity']);
						else $bcn_ebayreturnshipment = 0;
						foreach($q->result_array() as $b)
						{
							$bupdate = array();
							if ($b['ebayRefundAmount'] != $bcn_ebayRefundAmount) $bupdate['return_pricesold'] = $bcn_ebayRefundAmount;
							if ($b['ebayreturnshipment'] != $bcn_ebayreturnshipment) $bupdate['return_shippingcost'] = $bcn_ebayreturnshipment;
							if (count($bupdate) >0)
							{
								printcool ($bupdate,false,'BCN: '.$b['bcn']);
								//$this->db->update('warehouse', $bupdate,array('wid' => $b['wid']));
								foreach ($bupdate as $k => $v) {//printcool ($v); printcool ($wid[$k]);
									{
										if ($v != $b[$k])
										{
											echo '*LOGGED* BCN: '.$b['bcn'].' - FIELD: '.$k.' = FROM: '.$b[$k].' - TO: '.$v.'<br>';
											$this->Auth_model->wlog($b['bcn'],$b['wid'],$k,$b[$k],$v);
										}
									}
								}

								$tdsql = 'SELECT * FROM `transaction_details` WHERE `channel` = 1 AND `w_id` = '.$b['wid'].' AND `sold_id` = '.(int)$etr['et_id'];
								$trd =  $this->db->query($tdsql);
								if ($trd->num_rows() > 0)
								{
									$trdetails = $trd->row_array();
									$tupdate=array();
									if ($trdetails['returned_amount'] != $bcn_ebayRefundAmount) $tupdate['returned_amount'] = $bcn_ebayRefundAmount;
									if ($trdetails['return_shipping'] != $bcn_ebayreturnshipment) $tupdate['return_shipping'] = $bcn_ebayreturnshipment;
									if (count($tupdate) >0)
									{
										printcool($tupdate,false,'td_id: '.$trdetails['td_id']);
										$this->db->update('transaction_details', $tupdate,array('td_id' => $trdetails['td_id']));
									}
								}
							}
						}
					}
					}
				}
				else echo  'Something is wrong<br>';

			}
		}

	}
}
