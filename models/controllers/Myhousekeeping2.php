<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myhousekeeping2 extends Controller {

function Myhousekeeping2()
	{
		parent::Controller();		
		$this->load->model('Mywarehouse_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		$this->Auth_model->CheckWarehouse();	
				
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
}
