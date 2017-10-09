<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myhousekeeping extends Controller {

function Myhousekeeping()
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
function bcns517()
{
	$list = '116-7755	,
116-7754	,
116-7753	,
116-7752	,
116-7751	,
116-7750	,
116-7749	,
116-7748	,
116-7747	,
116-7746	,
116-7745	,
116-7744	,
116-7743	,
116-7742	,
116-7741	,
116-7740	,
116-7739	,
116-7738	,
116-7737	,
116-7736	,
116-7735	,
116-7734	,
116-7733	,
116-7732	,
116-7731	,
116-7730	,
116-7729	,
116-7728	,
116-7727	,
116-7726	,
116-7725	,
116-7724	,
116-7723	,
116-7722	,
116-7721	,
116-7720	,
116-7719	,
116-7718	,
116-7717	,
116-7716	,
116-7715	,
116-7714	,
116-7713	,
116-7712	,
116-7711	,
116-7710	,
116-7709	,
116-7708	,
116-7707	,
116-7706	,
116-7705	,
116-7704	,
116-7703	,
116-7702	,
116-7701	,
116-7700	,
116-7699	,
116-7698	,
116-7697	,
116-7696	,
116-7695	,
116-7694	,
116-7693	,
116-7692	,
116-7691	,
116-7690	,
116-7689	,
116-7688	,
116-7687	,
116-7686	,
116-7685	,
116-7684	,
116-7683	,
116-7682	,
116-7681	,
116-7680	,
116-7679	,
116-7678	,
116-7677	,
116-7676	,
116-7675	,
116-7674	,
116-7673	,
116-7672	,
116-7671	,
116-7670	,
116-7669	,
116-7668	,
116-7667	,
116-7666	,
116-7665	,
116-7664	,
116-7663	,
116-7662	,
116-7661	,
116-7660	,
116-7659	,
116-7658	,
116-7657	,
116-7656	,
116-7655	,
116-7654	,
116-7653	,
116-7652	,
116-7651	,
116-7650	,
116-7649	,
116-7648	,
116-7647	,
116-7646	,
116-7645	,
116-7644	,
116-7643	,
116-7642	,
116-7641	,
116-7640	,
116-7639	,
116-7638	,
116-7637	,
116-7636	,
116-7635	,
116-7634	,
116-7633	,
116-7632	,
116-7631	,
116-7630	,
116-7629	,
116-7628	,
116-7627	,
116-7626	,
116-7625	,
116-7624	,
116-7623	,
116-7622	,
116-7621	,
116-7620	,
116-7619	,
116-7618	,
116-7617	,
116-7616	,
116-7615	,
116-7614	,
116-7613	,
116-7612	,
116-7611	,
116-7610	,
116-7609	,
116-7608	,
116-7607	,
116-7606	,
116-7605	,
116-7604	,
116-7603	,
116-7602	,
116-7601	,
116-7600	,
116-7599	,
116-7598	,
116-7597	,
116-7596	,
116-7595	,
116-7594	,
116-7593	,
116-7592	,
116-7591	,
116-7590	,
116-7589	,
116-7588	,
116-7587	,
116-7586	,
116-7585	,
116-7584	,
116-7583	,
116-7582	,
116-7581	,
116-7580	,
116-7579	,
116-7578	,
116-7577	,
116-7576	,
116-7575	,
116-7574	,
116-7573	,
116-7572	,
116-7571	,
116-7570	,
116-7569	,
116-7568	,
116-7567	,
116-7566	,
116-7565	,
116-7564	,
116-7563	,
116-7562	,
116-7561	,
116-7560	,
116-7559	,
116-7558	,
116-7557	,
116-7556	,
116-7555	,
116-7554	,
116-7553	,
116-7552	,
116-7551	,
116-7550	,
116-7549	,
116-7548	,
116-7547	,
116-7546	,
116-7545	,
116-7544	,
116-7543	,
116-7542	,
116-7541	,
116-7540	,
116-7539	,
116-7538	,
116-7537	,
116-7536	,
116-7535	,
116-7534	,
116-7533	,
116-7532	,
116-7531	,
116-7530	,
116-7529	,
116-7528	,
116-7527	,
116-7526	,
116-7525	,
116-7524	,
116-7523	,
116-7522	,
116-7521	,
116-7520	,
116-7519	,
116-7518	,
116-7517	,
116-7516	,
116-7515	,
116-7514	,
116-7513	,
116-7512	,
116-7511	,
116-7510	,
116-7509	,
116-7508	,
116-7507	,
116-7506	,
116-7505	,
116-7504	,
116-7503	,
116-7502	,
116-7501	,
116-7500	,
116-7499	,
116-7498	,
116-7497	,
116-7496	,
116-7495	,
116-7494	,
116-7493	,
116-7492	,
116-7491	,
116-7490	,
116-7489	,
116-7488	,
116-7487	,
116-7486	,
116-7485	,
116-7484	,
116-7483	,
116-7482	,
116-7481	,
116-7480	,
116-7479	,
116-7478	,
116-7477	,
116-7476	,
116-7475	,
116-7474	,
116-7473	,
116-7472	,
116-7471	,
116-7470	,
116-7469	,
116-7468	,
116-7467	,
116-7466	,
116-7465	,
116-7464	,
116-7463	,
116-7462	,
116-7461	,
116-7460	,
116-7459	,
116-7458	,
116-7457	,
116-7456	,
116-7455	,
116-7454	,
116-7453	,
116-7452	,
116-7451	,
116-7450	,
116-7449	,
116-7448	,
116-7447	,
116-7446	,
116-7445	,
116-7444	,
116-7443	,
116-7442	,
116-7441	,
116-7440	,
116-7439	,
116-7438	,
116-7437	,
116-7436	,
116-7435	,
116-7434	,
116-7433	,
116-7432	,
116-7431	,
116-7430	,
116-7429	,
116-7428	,
116-7427	,
116-7426	,
116-7425	,
116-7424	,
116-7423	,
116-7422	,
116-7421	,
116-7420	,
116-7419	,
116-7418	,
116-7417	,
116-7416	,
116-7415	,
116-7414	,
116-7413	,
116-7412	,
116-7411	,
116-7410	,
116-7409	,
116-7408	,
116-7407	,
116-7406	,
116-7405	,
116-7404	,
116-7403	,
116-7402	,
116-7401	,
116-7400	,
116-7399	,
116-7398	,
116-7397	,
116-7396	,
116-7395	,
116-7394	,
116-7393	,
116-7392	,
116-7391	,
116-7390	,
116-7389	,
116-7388	,
116-7387	,
116-7386	,
116-7385	,
116-7384	,
116-7383	,
116-7382	,
116-7381	,
116-7380	,
116-7379	,
116-7378	,
116-7377	,
116-7376	,
116-7375	,
116-7374	,
116-7373	,
116-7372	,
116-7371	,
116-7370	,
116-7369	,
116-7368	,
116-7367	,
116-7366	,
116-7365	,
116-7364	,
116-7363	,
116-7362	,
116-7361	,
116-7360	,
116-7359	,
116-7358	,
116-7357	,
116-7356	,
116-7355	,
116-7354	,
116-7353	,
116-7352	,
116-7351	,
116-7350	,
116-7349	,
116-7348	,
116-7347	,
116-7346	,
116-7345	,
116-7344	,
116-7343	,
116-7342	,
116-7341	,
116-7340	,
116-7339	,
116-7338	,
116-7337	,
116-7336	,
116-7335	,
116-7334	,
116-7333	,
116-7332	,
116-7331	,
116-7330	,
116-7329	,
116-7328	,
116-7327	,
116-7326	,
116-7325	,
116-7324	,
116-7323	,
116-7322	,
116-7321	,
116-7320	,
116-7319	,
116-7318	,
116-7317	,
116-7316	,
116-7315	,
116-7314	,
116-7313	,
116-7312	,
116-7311	,
116-7310	,
116-7309	,
116-7308	,
116-7307	,
116-7306	,
116-7305	,
116-7304	,
116-7303	,
116-7302	,
116-7301	,
116-7300	,
116-7299	,
116-7298	,
116-7297	,
116-7296	,
116-7295	,
116-7294	,
116-7293	,
116-7292	,
116-7291	,
116-7290	,
116-7289	,
116-7288	,
116-7287	,
116-7286	,
116-7285	,
116-7284	,
116-7283	,
116-7282	,
116-7281	,
116-7280	,
116-7279	,
116-7278	,
116-7277	,
116-7276	,
116-7275	,
116-7274	,
116-7273	,
116-7272	,
116-7271	,
116-7270	,
116-7269	,
116-7268	,
116-7267	,
116-7266	,
116-7265	,
116-7264	,
116-7263	,
116-7262	,
116-7261	,
116-7260	,
116-7259	,
116-7258	,
116-7257	,
116-7256	,
116-7255	,
116-7254	,
116-7253	,
116-7252	,
116-7251	,
116-7250	,
116-7249	,
116-7248	,
116-7247	,
116-7246	,
116-7245	,
116-7244	,
116-7243	,
116-7242	,
116-7241	,
116-7240	,
116-7239	
';
$list = explode(',', $list);
//printcool ($list);
//exit();
$c = 1;
	$this->db->select('wid, status, sold_id, bcn, channel, vended, listingid');
	//$this->db->where('sold_id', 1143);
	//$this->db->where('channel', 4);
	foreach ($list as $l)
	{
		if ($c == 1) $this->db->where('bcn', trim($l));
		else $this->db->or_where('bcn', trim($l));
		$c++;
	}
	$d = $this->db->get('warehouse');printcool ($d);
	if ($d->num_rows() > 0)
	{
		foreach ($d->result_array() as $res)
		{
			printcool($res);
			//$this->db->update('warehouse', array('sold_id' => 0, 'channel' => 0, 'vended' => 0), array('wid' => $res['wid']));
		}
	}
}
function blanktitlepskus()
{
	$this->db->where('title', NULL);
	//$this->db->where('is_p', 1);
	$ws = $this->db->get('warehouse_sku');
	if ($ws->num_rows() > 0)
	{printcool($ws->num_rows());
		//$this->db->select("wslid, wsid, e.e_id, e.e_title", false);
		//$this->db->join('ebay e', 'listing = e.e_id', 'LEFT');
		//$c = 1;
		foreach ($ws->result_array() as $sku)
		{
			$this->db->where('wsid', $sku['wsid']);
			$q = $this->db->get('warehouse_sku_listing');
		if ($q->num_rows() > 0)
		{printcool($q->num_rows());
			foreach ($q->result_array()as $g)
			{
				printcool ($g);
				$this->db->select('e_id, e_title');
				$this->db->where('e_id', $g['listing']);
				$e = $this->db->get('ebay');	
				if ($e->num_rows() > 0)
				{
					//printcool ($e->row_array());
					$el = $e->row_array();
					$this->db->update('warehouse_sku', array('title' => $el['e_title'], 'housekeeping' => 1), array('wsid' => $g['wsid']));
					//printcool ($g['wsid']);
				}
				else printcool('NO LISTING');
			}
		}
			
		}
//
	
	}
	
	printcool('-----');
	$this->db->where('title', NULL);
	$this->db->where('is_p', 1);
	$ws1 = $this->db->get('warehouse_sku');
	if ($ws1->num_rows() > 0)
	{
		foreach ($ws1->result_array() as $sku)
		{
			$this->db->where('wsid', $sku['wsid']);
			$q = $this->db->get('warehouse_sku_listing');
			if ($q->num_rows() > 0)
			{
				printcool($q->num_rows());
			}
			else printcool ('no skulisting match');
		}
		
		foreach ($ws1->result_array() as $sku)
		{
			$this->db->where('psku', $sku['wsid']);
			$q = $this->db->get('warehouse');
			if ($q->num_rows() > 0)
			{
				printcool($q->num_rows());
			}
			else 
			{
				$this->db->insert('warehouse_sku_deleted', $sku);
				printcool ('no skubcn match');
				$this->db->where('wsid', (int)$sku['wsid']);
				$this->db->delete('warehouse_sku');
			}
		}
	}
	
}
function RenameBlankTitleFromListing($save = false)
{
	$this->db->select('wid, bcn, title, listingid');
	$this->db->where('listingid !=', 0);
	$this->db->where('title', '');
	$rb = $this->db->get('warehouse');
	if ($rb->num_rows() > 0)
	{
		foreach ($rb->result_array() as $b)
		{
			$blanks[$b['listingid']][] = $b;	
			$listings[$b['listingid']] = TRUE;
		}
		$this->db->select('e_id, e_title');
		$c = 0;
		foreach ($listings as $k => $v)
		{
			if ($c == 0) $this->db->where('e_id', $k);
			else 	$this->db->or_where('e_id', $k);
			$c++;
		}
			$lt = $this->db->get('ebay');
			if ($lt->num_rows() > 0)
			{
				foreach ($lt->result_array() as $lst)
				{
					$listings[$lst['e_id']] = $lst['e_title'];	
				}
			}
		//printcool ($blanks);
		//printcool ($listings);
		echo '<a href="'.Site_url().'/Myhousekeeping/RenameBlankTitleFromListing/Save">Save</a><br><br>';
		foreach ($blanks as $k => $v)
		{
			foreach ($v as $kk => $vv)
			{
				$blanks[$k][$kk]['title'] = $listings[$k];
				echo 'BCN: ('.$vv['wid'].') <strong>'.$vv['bcn'].'</strong> - Title: "'.$vv['title'].'" to "'.$listings[$k].'"<br>';
				if ($save)
				{
				$this->db->update('warehouse',array('title' => $listings[$k]), array('wid' => $vv['wid']));	
				$this->Auth_model->wlog($vv['bcn'], $vv['wid'], 'title', $vv['title'], $listings[$k]);
				}
				}
		}
				echo '<br><br><a href="'.Site_url().'/Myhousekeeping/RenameBlankTitleFromListing/Save">Save</a>';
		//printcool ($blanks);
	}
}
function fixbacklogreturns()
{
	//$this->db->select()	
	$this->db->where('return_id',324);
	$w = $this->db->get('warehouse');
	if ($w->num_rows()  >0 )
	{
		printcool ($w->result_array());	
	}
}
function takefromdev()
{
	$this->db->where('et_id >=', 40832);
	$this->db->where('et_id <=', 40888);	
	$query = $this->db->get('ebay_transactions');
	if ($query->num_rows() > 0)
	{
		foreach($query->result_array() as $t)
		{
			
				$update['paid']= $t['paid'];
				$update['shipped'] = (float)$t['ssc']/(int)$t['qty'];					
				$update['sellingfee'] = $t['fee']/(int)$t['qty'];
			
							$this->db->select('wid, bcn, cost, sellingfee, shipped, shipped_actual, paid, paid_date, netprofit');
							$this->db->where('channel', 1);
							$this->db->where('sold_id', $t['et_id']);
							$this->db->where('vended', 1);
							
							$f = $this->db->get('warehouse');
							if ($f->num_rows() > 0)
							{
								$fr = $f->result_array();
								foreach ($fr as $fl)
								{	
									if (isset($update['paid']))
									{
										
										$update['netprofit'] = ((float)$update['paid']+(float)$update['shipped'])-((float)$fl['cost']+(float)$update['sellingfee']['sellingfee']+(float)$fl['shipped_actual']);										
										
									}
									foreach ($update as $k => $v)
									{
									  //if ($v != $fl[$k]) $this->Auth_model->wlog($fl['bcn'], $fl['wid'], $k, $fl[$k], $v);	
									}
									printcool ($update);
									$this->db->update('warehouse', $update, array('wid' => $fl['wid']));
									
								}
							}	
			
		}

	}
	
	
	exit();
	$this->dev = $this->load->database('dev', TRUE);
	$this->dev->where('et_id >=', 40832);
	$this->dev->where('et_id <=', 40888);	
	$query = $this->dev->get('ebay_transactions');
	if ($query->num_rows() > 0)
	{
		foreach($query->result_array() as $t)
		{
			$l = $live[$t['et_id']];
			foreach ($t as $k=>$v)
			{
				if ($v != $l[$k]) 
				{
					$this->live = $this->load->database('default', TRUE);
					printcool ($this->live->update('ebay_transactions', array($k => $v),array('et_id' => $t['et_id']))); 
					//exit();
				}
			}
		}
	}
	//printcool ($dev);
	
}
function fixnetscrapped()
{
	$this->db->select('wid, paid, cost, sellingfee, shipped_actual, status');
	$this->db->where('status', 'Scrap');
	$query = $this->db->get('warehouse');
		//GoMail(array ('msg_title' => 'ReProcessNetProfit Run @ '.CurrentTime(), 'msg_body' => printcool ($wid, true,'wid'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
		if ($query->num_rows() > 0)
		{		
			foreach ($query->result_array() as $wid)
			{
			if ($status == 'Scrap')$wid['paid'] = 0;
			$wid['cost'] = str_replace('$', '', $wid['cost']);

			$data['netprofit'] = sprintf("%01.2f", (((float)$wid['paid']+(float)$wid['shipped'])-((float)$wid['cost']+(float)$wid['sellingfee']+(float)$wid['shipped_actual'])));	
			
			$this->db->update('warehouse', $data, array('wid'=> (int)$wid['wid']));
			//GoMail(array ('msg_title' => 'ReProcessNetProfit Saved @ '.CurrentTime(), 'msg_body' => printcool ($data, true,'data'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
			}
		}
}
function LoopBCNSFromTransaction()
{
	

	$this->db->select('rec, et_id, itemid, transid, fee, asc, ssc, qty, paydata, paid, eachpaid, paidtime');	
		$this->db->where('reprocess', 1);
		$this->db->where('notpaid', 0);
		$this->db->where('refunded', 0);
		$this->db->where('pendingpay', 0);
		$this->db->where('customcode', 0);
		$this->db->order_by('et_id', 'DESC');
		$this->db->limit(1000);
		$q = $this->db->get('ebay_transactions');
		
		if ($q->num_rows() > 0)
		{
		foreach ($q->result_array() as $t)
		{
		
				
					
					$update['paid'] = (float)$t['eachpaid'];
					$update['shipped'] = (float)$t['ssc']/(int)$t['qty'];					
					$update['sellingfee'] = $t['fee']/(int)$t['qty'];

				
			
							$this->db->select('wid, bcn, cost, sellingfee, shipped, shipped_actual, paid, paid_date, netprofit');
							$this->db->where('channel', 1);
							$this->db->where('sold_id', $t['et_id']);
							$this->db->where('vended', 1);
							
							$f = $this->db->get('warehouse');
							if ($f->num_rows() > 0)
							{
								$fr = $f->result_array();
								foreach ($fr as $fl)
								{	
									
										
									$update['netprofit'] = ((float)$update['paid']+(float)$update['shipped'])-((float)$fl['cost']+(float)$update['sellingfee']['sellingfee']+(float)$fl['shipped_actual']);
										
								
									foreach ($update as $k => $v)
									{
									  if ($v != $fl[$k])
									  {
										   $this->Auth_model->wlog($fl['bcn'], $fl['wid'], $k, $fl[$k], $v);	
										   $this->db->update('warehouse', array($k => $fl[$k]), array('wid' => $fl['wid']));
										   printcool ($k. ' - from: '.$v.' to '.$fl[$k],'', $fl['wid'].' - TR:'.$t['et_id']);
									  }
									}
									
									
									
								}
							}	
				
				
				unset($update);	
				$this->db->update('ebay_transactions', array('reprocess' => 2), array('et_id' => $t['et_id']));		
		}		
		}
	
}
function LoopUpdateCurrentTransaction()
{

	$this->db->select('rec, et_id, itemid, transid, fee, asc, ssc, qty, paydata, paid, paidtime');	
		$this->db->where('reprocess', 0);
		$this->db->where('notpaid', 0);
		$this->db->where('refunded', 0);
		$this->db->where('pendingpay', 0);
		$this->db->where('customcode', 0);
		$this->db->order_by('et_id', 'DESC');
		$this->db->limit(500);
		$q = $this->db->get('ebay_transactions');
		

		set_time_limit(120);
		ini_set('mysql.connect_timeout', 120);
		ini_set('max_execution_time', 120);  
		ini_set('default_socket_timeout', 120); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetSellingManagerSaleRecord';
		$compatabilityLevel = 959;
		//Create a new eBay session with all details pulled in from included keys.php
		
		
		if ($q->num_rows() > 0)
		{
		foreach ($q->result_array() as $t)
		{
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
		

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
				
				$xml = simplexml_load_string($responseXml);
				
				$item = $xml->SellingManagerSoldOrder;
			
		//printcool ($item);
				
				$this->db->update('ebay_transactions', array('eachpaid' => (float)$item->SellingManagerSoldTransaction->ItemPrice), array('et_id' => $t['et_id']));
								 
				if (isset($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost)) $paid = ((int)$item->SellingManagerSoldTransaction->QuantitySold*(float)$item->SellingManagerSoldTransaction->ItemPrice)+(float)$item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost;
				else $paid = ((int)$item->SellingManagerSoldTransaction->QuantitySold*(float)$item->SellingManagerSoldTransaction->ItemPrice);
				
				echo '<br>'.$t['et_id'].' - Paid: '.$paid;
				if ((float)$paid != (float)$t['paid'])
				{
					echo ' - UPDATED from '.(float)$t['paid'].'('.$t['et_id'].')';
					printcool ($item);
					$this->db->update('ebay_transactions', array('paid' => (float)$paid, 'cascupd' => 2), array('et_id' => $t['et_id']));
					$this->_logaction('UpdateCurrentTransaction', 'B', array('Paid' => (float)$t['paid']), array('Paid' =>(float)$paid), 0, $t['itemid'], $t['rec']);
					
					$update['paid']= (float)$item->SellingManagerSoldTransaction->ItemPrice;
					
					if (isset($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost)) $update['shipped'] =  	$item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost/(int)$item->SellingManagerSoldTransaction->QuantitySold;
					else $update['shipped'] = (float)$t['ssc']/(int)$item->SellingManagerSoldTransaction->QuantitySold;
					
					$update['sellingfee'] = $t['fee']/(int)$item->SellingManagerSoldTransaction->QuantitySold;
				}		
				
			
				if (isset($update))
				{			$this->db->select('wid, bcn, cost, sellingfee, shipped, shipped_actual, paid, paid_date, netprofit');
							$this->db->where('channel', 1);
							$this->db->where('sold_id', $t['et_id']);
							$this->db->where('vended', 1);
							
							$f = $this->db->get('warehouse');
							if ($f->num_rows() > 0)
							{
								$fr = $f->result_array();
								foreach ($fr as $fl)
								{	
									if (isset($update['paid']))
									{
										
										$update['netprofit'] = ((float)$update['paid']+(float)$update['shipped'])-((float)$fl['cost']+(float)$update['sellingfee']['sellingfee']+(float)$fl['shipped_actual']);										
										
									}
									foreach ($update as $k => $v)
									{
									  if ($v != $fl[$k]) $this->Auth_model->wlog($fl['bcn'], $fl['wid'], $k, $fl[$k], $v);	
									}
									printcool ($update);
									$this->db->update('warehouse', $update, array('wid' => $fl['wid']));
									
								}
							}	
				}
				$this->db->update('ebay_transactions', array('reprocess' => 1), array('et_id' => $t['et_id']));
				unset($update);
				}
        
			
		}		
}
function fixmultipleebaytrans()
{
	set_time_limit(600);
		ini_set('mysql.connect_timeout', 600);
		ini_set('max_execution_time', 600);  
		ini_set('default_socket_timeout', 600); 
		require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<IncludeContainingOrder>true</IncludeContainingOrder>';
		
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version><NumberOfDays>1</NumberOfDays>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		//$dates = array('from' => date('Y-m-d H:i:s', strtotime("-2 Hours")), 'to' => date("Y-m-d H:i:s"));
		//<ModTimeFrom>'.$dates['from'].'</ModTimeFrom>
 		//<ModTimeTo>'.$dates['to'].'</ModTimeTo>  
		
			
		//<IncludeCodiceFiscale>'.TRUE.'</IncludeCodiceFiscale>		
		//<IncludeContainingOrder>'.TRUE.'</IncludeContainingOrder> 
		
		$requestXmlBody .= '
	
		 <IncludeFinalValueFee>'.TRUE.'</IncludeFinalValueFee>
			<NumberOfDays>5</NumberOfDays>	
		<Pagination>
		<EntriesPerPage>200</EntriesPerPage>
		</Pagination>
		</GetSellerTransactionsRequest>';	
		$verb = 'GetSellerTransactions';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		$xml = simplexml_load_string($responseXml);
		
		$list = $xml->TransactionArray->Transaction;
		if ($list) foreach ($list as $l)
		{
			if (isset($l->ContainingOrder->ShippingDetails->SellingManagerSalesRecordNumber))
			{
			printcool ('___GO__');
			printcool ((int)$l->ShippingDetails->SellingManagerSalesRecordNumber);
			printcool ((string)$l->AmountPaid, '','Amount');
			printcool ((string)$l->TransactionPrice, '','TransactionPrice');
			printcool ((int)$l->QuantityPurchased, '','QuantityPurchased');
			printcool ('<strong>'.(string)$l->TransactionPrice*(int)$l->QuantityPurchased.'</strong>', '','END PAY');
			 printcool ((int)$l->ContainingOrder->ShippingDetails->SellingManagerSalesRecordNumber,'','ORDER ID');
			printcool((string)$l->ShippingServiceSelected->ShippingServiceCost, '', 'ShippingServiceCost');
			// printcool($l);
			 printcool ('___STOP__');
			 
			 
			}
		}
}
function fixparting()
{
	$this->db->select('wid,bcn_p1, bcn_p2, bcn_p3');
	$this->db->where('bcn_p3 !=', '');
	$this->db->where('bcn_p1 !=', 'G');
	$e = $this->db->get('warehouse');
	foreach ($e->result_array() as $ee)
	{
		if (strlen($ee['bcn_p1'] ) < 3)
		{ 
			$ee['bcn_p1'] = '0'.$ee['bcn_p1'];	
			$ee['bcn'] = $ee['bcn_p1'].'-'.$ee['bcn_p2'].'-'.$ee['bcn_p3'];
			$wid = $ee['wid'];
			unset($ee['wid']);
			$this->db->update('warehouse', $ee, array('wid' => $wid));

		}
		
	}
}
function timer()
{
	printcool (CurrentTime());	
}
function index()
{
	$query = $this->db->query('SELECT "wid" FROM warehouse WHERE paid != "" AND paid != "0" AND paid != "0.00" AND status != "Sold" AND status != "On Hold" AND status != "Scrap" AND status != "Parted" AND deleted = 0');
	$this->mysmarty->assign('soldupdater', $query->num_rows());
	
	$this->mysmarty->view('mywarehouse/housekeeping_main.html');	
}
function fixGeneric()
{
	$this->db->select('wid, bcn');
	$this->db->where('bcn_p1', NULL);
	$this->db->where('deleted', 0);
	$q = $this->db->get('warehouse');
	foreach ($q->result_array() as $r)
	{
		printcool ($r);	
		$this->db->update('warehouse', array('bcn' =>trim($r['bcn']), 'bcn_p1' => trim($r['bcn'])), array('wid' => $r['wid']));
		
	}
}
function updateebsubm()
{
	$query = $this->db->query('SELECT e_id, ebay_submitted FROM ebay WHERE `ebay_id` != 0 ORDER BY e_id DESC');

	$m = $query->result_array();
	foreach ($m as $k => $v)
	{
		$m[$k]['ebay_msubm'] = explode(' @ ', $v['ebay_submitted']);	
		if (count($m[$k]['ebay_msubm']) > 1) $m[$k]['ebay_msubm'] = $m[$k]['ebay_msubm'][1];
		else $m[$k]['ebay_msubm'] = $m[$k]['ebay_msubm'][0];
		$m[$k]['ebay_msubm'] = explode(' by', $m[$k]['ebay_msubm']);
		$m[$k]['ebay_msubm'] = $m[$k]['ebay_msubm'][0];
		$m[$k]['ebay_msubm'] = explode(' | ', $m[$k]['ebay_msubm']);
		if (count($m[$k]['ebay_msubm']) == 1) $m[$k]['ebay_msubm'] = $m[$k]['ebay_msubm'][0] = explode(' - ', $m[$k]['ebay_msubm'][0]);
		if (count($m[$k]['ebay_msubm']) == 1) $m[$k]['ebay_msubm'] = $m[$k]['ebay_msubm'][0] = explode(' ', $m[$k]['ebay_msubm'][0]);
		 $m[$k]['ebay_msubm'][0] = explode('-', $m[$k]['ebay_msubm'][0]);
		 $m[$k]['ebay_msubm'][1] = explode(':', $m[$k]['ebay_msubm'][1]);
		 if (!isset($m[$k]['ebay_msubm'][0][1]) && !isset($m[$k]['ebay_msubm'][1][1]))
		 {
			 $date = explode('/', $m[$k]['ebay_msubm'][1][0]);
			 $time = explode(':', $m[$k]['ebay_msubm'][0][0]);
			 
				
			 $m[$k]['ebay_msubm'][0][0] = $date[2];
			 $m[$k]['ebay_msubm'][0][1] = $date[1];
			 $m[$k]['ebay_msubm'][0][2] = $date[0];
			 
			 $m[$k]['ebay_msubm'][1] = $time;

		 }
		 $m[$k]['ebay_msubm'] = mktime ($m[$k]['ebay_msubm'][1][0] , $m[$k]['ebay_msubm'][1][1] , $m[$k]['ebay_msubm'][1][2] , $m[$k]['ebay_msubm'][0][1],$m[$k]['ebay_msubm'][0][2] ,$m[$k]['ebay_msubm'][0][0]);
		//if (count($m[$k]['ebay_msubm']) == 1)printcool( $m[$k]['ebay_msubm']);
		$this->db->update('ebay', array('ebay_msubm' => $m[$k]['ebay_msubm']), array('e_id' => $m[$k]['e_id']));
	}
		printcool ($m);
		
}
function deleteeBayDB()
{
	$this->db->select('wid, waid, bcn, title, status, status_notes');
		//$this->db->where('waid <=', 148);
		//$this->db->where('waid >', 1);
		$this->db->where('waid', 1);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{
		
		foreach ($this->query->result_array() as $r)
		{		
		//printcool ($r);	
			//$this->db->update('warehouse', array('deleted' => $r['waid']), array('wid' => $r['wid']));
			
		}	
		}

}
function showlotoldbcn()
{
	$this->db->select('wid, bcn, title, lot, oldbcn');
	$this->db->where('lot !=', '');
	$this->db->or_where('oldbcn !=', '');
	$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{
		echo '<table border="1"><tr><th>ID</th><th>BCN</th><th>Lot</th><th>Old BCN</th><th>Title</th></tr>';
		foreach ($this->query->result_array() as $r)
		{	
			echo '<tr>';
			echo '<td>'.$r['wid'].'</td>';
			echo '<td>'.$r['bcn'].'</td>';
			
			echo '<td><a href="/Myhousekeeping/lottoold/'.$r['wid'].'" target="_blank">'.$r['lot'].'</a></td>';
			echo '<td>'.$r['oldbcn'].'</td>'
			;echo '<td>'.$r['title'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
		
		}
}
function lottoold($id)







{
	$this->db->select('wid, lot');
	$this->db->where('wid', (int)$id);
	$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{		
			$r = $this->query->row_array();
			$this->db->update('warehouse', array('oldbcn' => $r['lot']), array('wid' => $r['wid']));		
		}
	
}

function showlotfix()
{
	$this->db->select('wid, bcn, title, lot, oldbcn');
	$this->db->where('wid >=', 15508);
	$this->db->where('wid <=', 15578);
	$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{
		echo '<table border="1"><tr><th>ID</th><th>BCN</th><th>Lot</th><th>Old BCN</th><th>Title</th></tr>';
		foreach ($this->query->result_array() as $r)
		{	
		//if ($r['lot'] != '') $this->db->update('warehouse', array('oldbcn' => $r['lot']), array('wid' => $r['wid']));	
			echo '<tr>';
			echo '<td>'.$r['wid'].'</td>';
			echo '<td>'.$r['bcn'].'</td>';
			
			echo '<td><a href="/Myhousekeeping/lottoold/'.$r['wid'].'" target="_blank">'.$r['lot'].'</a></td>';
			echo '<td>'.$r['oldbcn'].'</td>'
			;echo '<td>'.$r['title'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
		
		}
}
function GenGhostBCN()
{	
/*
$this->db->select("e_id, ebay_id, e_part, e_qpart, quantity, ebayquantity");
$this->db->where('e_part !=', '');
		$this->db->order_by("e_id", "DESC");
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0)  
		{
			foreach ($q->result_array() as $e)
			{
				printcool($e['e_part']);
				
				//$this->db->update('ebay', array('e_part' => $e['e_part']), array('e_id' => $e['e_id']));
			}
		}
exit();
*/

$process = false; 
/*if ($process)
{
	$this->load->dbutil();
		$this->load->helper('file');
		$datetime_string = date("Y.m.d").'-'.date("H_i_s");
		$prefs = array('tables' => array('warehouse'));		
		$backup =& $this->dbutil->backup($prefs);		
		$databasefilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'_-_Warehouse.sql.gz';
		
		write_file($databasefilename, $backup); 		
		$prefs = array('tables' => array('ebay'));
		$backup =& $this->dbutil->backup($prefs);		
		$databasefilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'_-_eBay_And_Log.sql.gz';
		
		write_file($databasefilename, $backup); 	
}*/
	$totalghostbcns = 0;
	
	$this->db->select("e_id, ebay_id, e_part, e_qpart, quantity, ebayquantity");
	$this->db->where('ebay_id !=', 0);
	$this->db->where('ebended', NULL);
		$this->db->order_by("e_id", "DESC");
		
		$q = $this->db->get('ebay');

		if ($q->num_rows() > 0)  
		{
		
		
						$this->db->select("bcn");
						$this->db->where('waid' , 0);
						$this->db->order_by("wid", "DESC");
						$w = $this->db->get('warehouse', 1);

						if ($w->num_rows() > 0)
						{
							 $snext = $w->row_array();
							 $snext = ((str_replace('G', '', $snext['bcn']))+1);
						}
						//printcool ($snext);
			echo '<table border="1">';
			foreach ($q->result_array() as $e)
			{
				$listingbcncount = '';
				$lbcstr = '';
				echo '<tr>';
				echo '<td>'.$e['e_id'].'</td>';
				
				$listingbcncount = count(explode(',', $e['e_part']));
				if ($listingbcncount != $e['e_qpart'] && trim($e['e_part']) != '')
				{
					 $lbcstr = ' <span style="color:red;">('.$listingbcncount.' [BCNCount: '.$e['e_qpart'].'])</span>';
					if ($process) $this->db->update('ebay', array('e_qpart' => (int)$listingbcncount), array('e_id' => $e['e_id']));
					
					 	 	$ra['admin'] = $this->session->userdata['ownnames'];
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'GhostBCNGen';
							$ra['field'] = 'e_qpart';
							$ra['atype'] = 'B';
							$ra['e_id'] = (int)$e['e_id'];
							$ra['ebay_id'] = $e['ebay_id'];
							$ra['datafrom'] = $e['e_qpart'];
							$ra['datato'] = (int)$listingbcncount;
										
							if ($process) $this->db->insert('ebay_actionlog', $ra);  
							$e['e_qpart'] = (int)$listingbcncount;
							unset($ra);
				
				}
				echo '<td width="400">'.str_replace(',',', ', $e['e_part'].$lbcstr).'</td>';
				
				
				$color = '';
				if ($e['e_qpart'] != $e['quantity'] && $e['quantity'] >= 0) $color = 'style="color:red;"';
				echo '<td '.$color.'><sup>BCN</sup>&nbsp;&nbsp;<strong style="font-size:30px;">'.$e['e_qpart'].'</strong></td>';
				echo '<td '.$color.'><sup>LQ</sup>&nbsp;&nbsp;<strong style="font-size:30px;">'.$e['quantity'].'</strong></td>';
				$color = '';
				if ($e['ebayquantity'] != $e['quantity']) $color = 'style="color:red;"';
				if ($e['e_qpart'] != $e['quantity'])
				{
				if ($e['quantity'] >= 0) $val = $e['quantity']-$e['e_qpart'];
				else $val=0;
				$ngen = 0;
				$ngenarray = '';
				if ($val > 0)
				{
					$ngen = $e['quantity']-$e['e_qpart'];
					$loop = 1;
					
					while ($loop <= $ngen)
					{
						$totalghostbcns++;
						
						/*if ($process)
						{
						$this->db->select("bcn");
						$this->db->where('waid' , 0);
						$this->db->order_by("wid", "DESC");
						$w = $this->db->get('warehouse', 1);

						if ($w->num_rows() > 0) $next = $w->row_array();
						else $next = ('G1');
						}
						else
						{*/
						$next = $snext;
						//}
						$next = (int)str_replace ('G', '', $next);
						$next = "G".($next+1);
						$snext = $next;
						$ngenarray .= ''.$next.',';				
						
						$wh =array(
						'waid' => 0,
						'listingid' => $e['e_id'],
						'aucid' => 'G',
						'dates' => serialize(array('created' => CurrentTime(), 'createdstamp' => mktime())),
						'adminid' => (int)$this->session->userdata['admin_id'],
						'bcn' => $next
						);
						if ($process) $this->db->insert('warehouse', $wh);
						
						$loop++;						
					}					
					 echo '<td align="right" width="50" style="color:#00C723;"><sup>nGen</sup>&nbsp;&nbsp;<strong style="font-size:30px;">'.$ngen.'</strong></td>';
					 
				}
				else echo '<td align="right" width="50" style="color:red;">Zero/Minus</td>';
				}
				else echo '<td></td>';
				echo '<td '.$color.' width="50"><sup>LeBQ</sup>&nbsp;&nbsp;<strong style="font-size:30px;">'.$e['ebayquantity'].'</strong></td>';
				$e['e_part'] = preg_replace('/\s+/', '', $e['e_part']);


				if (trim($e['e_part'] != '')) $e['e_part'] = rtrim($e['e_part'], ',').',';
				if ($ngenarray != '')
				{
					$oldepart = $e['e_part'];
					 echo '<td>'.$e['e_part'].'<strong>'.rtrim($ngenarray, ',').'</strong></td>';
					 if ($process) $this->db->update('ebay', array('e_part' => $e['e_part'].rtrim($ngenarray, ','), 'e_qpart' => count(explode(',', ($e['e_part'].rtrim($ngenarray, ',')))), 'ngen' => (int)$ngen), array('e_id' => $e['e_id']));
					
					 	 	$ra['admin'] = $this->session->userdata['ownnames'];
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'GhostBCNGen';
							$ra['field'] = 'e_part';
							$ra['atype'] = 'B';
							$ra['e_id'] = (int)$e['e_id'];
							$ra['ebay_id'] = $e['ebay_id'];
							$ra['datafrom'] = $oldepart;
							$ra['datato'] = $e['e_part'].rtrim($ngenarray, ',');
										
							if ($process) $this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							
				}unset($ngenarray);
				echo '</tr>';				
			}
			echo '</table>';	
		}
		printcool ($totalghostbcns);
}
	
	

function SoldUpdater($confirm = false)
{
		$q = $this->db->query('SELECT wid, bcn, paid, status, status_notes, sold, sold_date FROM warehouse WHERE paid != "" AND paid != "0" AND paid != "0.00" AND status != "Sold" AND status != "On Hold" AND status != "Scrap" AND status != "Parted" AND deleted = 0 ORDER BY wid DESC');		
		
		echo $this->mysmarty->fetch('header.html');
		echo $this->mysmarty->fetch('messages/error_success_msg.html');
		echo $this->mysmarty->fetch('mywarehouse/top_menu.html');
		
		echo '<h4><a href="'.Site_url().'/Myhousekeeping/SoldUpdater/Go">Confirm</a></h4><br clear="all"><table class="admintable3" cellpadding="5">
		<tr>';
		if ($confirm) echo '<th></th>';

		echo '<th>BCN</th><th>Paid</th><th>Cur. Status</th><th>Notes</th><th>Sold</th><th>Sold Date</th></tr>';
		if ($q->num_rows() > 0) foreach ($q->result_array() as $r)
		{
			//if ($r['sold'] == '') $r['sold'] = '*AutoSetSoldStatus Fix 21/11/2015*';
			if ($r['status_notes'] == '') $r['status_notes'] = date("m/d/y");
			else $r['status_notes'] = 'SoldUpdater '.date("m/d/y").' '.$r['status_notes'];
			
		
			echo '<tr>';
			
			if ($confirm)
			{
				echo '<td style="color:green;">Updated</td>';
				$this->Auth_model->wlog($r['bcn'], (int)$r['wid'], 'status', $r['status'], 'Sold', 'SoldUpdater');
				$this->db->update('warehouse', array('status' => 'Sold', 'vended' => 1, 'status_notes' => $r['status_notes']), array('wid' => $r['wid']));
			}		
			
			echo '<td><a href="'.Site_url().'Mywarehouse/bcndetails/'.$r['wid'].'" target="_blank"><img src="/images/admin/table.png" border="0"></a> '.$r['bcn'].'</td><td>'.$r['paid'].'</td><td>'.$r['status'].'</td><td>'.$r['status_notes'].'</td><td>'.$r['sold'].'</td><td>'.$r['sold_date'].'</td></tr>';	
		
		}	
		echo '</table><br clear="all"><h4><a href="'.Site_url().'/Myhousekeeping/SoldUpdater/Go">Confirm</a></h4>';
		
		echo $this->mysmarty->fetch('footer.html');
	
}

function RevenueUpdater()
{
		$q = $this->db->query('SELECT wid, aucid, bcn, cost,paid, status, title, sold, sold_date FROM warehouse WHERE ((cost = "" OR cost = "0" OR cost = "0.00") AND status = "Sold") or ((paid != "" AND paid != "0" AND paid != "0.0") AND (status != "Sold")) AND deleted = 0 ORDER BY wid DESC');		
		
		echo $this->mysmarty->fetch('header.html');
		echo $this->mysmarty->fetch('messages/error_success_msg.html');
		echo $this->mysmarty->fetch('mywarehouse/top_menu.html');
		
		echo '<table class="admintable3" cellpadding="5">
		<tr>';
		

		echo '<th>BCN</th><th>Cost</th><th>Paid</th><th>Cur. Status</th><th>Sold</th><th>Sold Date</th><th>Title</th></tr>';
		if ($q->num_rows() > 0) foreach ($q->result_array() as $r)
		{
			$coststyle= '';
			$paidstyle= '';
			if ($r['cost'] == '' && $r['status'] == 'Sold') $coststyle= 'style="color:red; border: 1px solid red;"';
			if ($r['paid'] != '' && $r['status'] != 'Sold') $paidstyle= 'style="color:#FF00FF; border: 1px solid #FF00FF;"';
			
			echo '<tr><td nowrap><a href="'.Site_url().'Mywarehouse/bcndetails/'.$r['wid'].'" target="_blank"><img src="/images/admin/table.png" border="0"></a> '.$r['bcn'].'</td><td '.$coststyle.'><input onChange="var fval = document.getElementById(\'field'.$r['wid'].'cost\').value; updatefield(\'cost\', fval, \''.$r['wid'].'\', \'field'.$r['wid'].'cost\', \'false\', \'RevenueUpdater\');" type="text" value="'.$r['cost'].'" id="field'.$r['wid'].'cost" name="cost" style="width:70px;"></td>
			
			<td '.$paidstyle.'><input onChange="var fval = document.getElementById(\'field'.$r['wid'].'paid\').value; updatefield(\'paid\', fval, \''.$r['wid'].'\', \'field'.$r['wid'].'paid\', \'false\', \'RevenueUpdater\');" type="text" value="'.$r['paid'].'" id="field'.$r['wid'].'paid" name="cost" style="width:70px;"></td>
			
			<td '.$coststyle.' '.$paidstyle.'>'.$r['status'].'</td><td>'.$r['sold'].'</td><td>'.$r['sold_date'].'</td><td>'.$r['title'].'</td></tr>';	

		}	
		echo '</table>';
		
		echo $this->mysmarty->fetch('footer.html');
	
}


function FixActiveSiteSell($confirm = false)
{
	$this->db->where('etype', 'a');
	$query = $this->db->get('ebay_live');

		if ($query->num_rows() > 0) 
			{
				foreach ($query->result_array() as $row)
				{
					$active[$row['itemid']] = true;
				}				
			}
	$this->db->select('e_id, ebay_id, ebended, e_title');
	//$this->db->where('ebended !=', '');
	$r = $this->db->get('ebay');	
	
	echo $this->mysmarty->fetch('header.html');
	echo $this->mysmarty->fetch('messages/error_success_msg.html');
	echo $this->mysmarty->fetch('mywarehouse/top_menu.html');
		
		echo '<h4><a href="'.Site_url().'/Myhousekeeping/FixActiveSiteSell/Go">Confirm</a></h4><br clear="all">
		<span style="font-size:16px; font-weight:bold;">Currently Active in eBay, any others will be not sold on site</span><br><br><table class="admintable3" cellpadding="5">
		<tr>';
		if ($confirm) echo '<th></th>';

		echo '<th>ID</th><th>eBay ID</th><th>Title</th></tr>';
		
		
	if ($r->num_rows() > 0)
	{ 	
		$offline = 0;
		foreach ($r->result_array() as $rk => $rv)
		{
			if (isset($active[$rv['ebay_id']])) 
			{
				if ($confirm)
				{
					echo '<td style="color:green;">Updated</td>';
					$this->db->update('ebay', array('ebended' => NULL, 'sitesell' => 1), array('e_id' => $rv['e_id']));
				}
				
				echo '<td><a href="'.Site_url().'Myebay/Search/'.$rv['e_id'].'" target="_blank" style=" color:#0099FF;">'.$rv['e_id'].'</a></td><td><a style=" color:#0099FF;" href="http://www.ebay.com/itm/'.$rv['ebay_id'].'" target="_blank">'.$rv['ebay_id'].'</a></td><td>'.$rv['e_title'].'</td></tr>';	
			}
			else
			{
				if ($confirm)
				{
					$this->db->update('ebay', array('ebended' => 'Ended By FixActiveSiteSell Script', 'sitesell' => 0), array('e_id' => $rv['e_id']));
					
				}
				$offline++;
			}
		}
			echo '<td colspan="3">Not active: '.$offline.'</td></tr>';	
			echo '</table><br clear="all"><h4><a href="'.Site_url().'/Myhousekeeping/FixActiveSiteSell/Go">Confirm</a></h4>';
		
		echo $this->mysmarty->fetch('footer.html');
	}
	
}
function VerifyEbayTransaction($transid, $itemid)
{
	
	set_time_limit(90);
		ini_set('mysql.connect_timeout', 90);
		ini_set('max_execution_time', 90);  
		ini_set('default_socket_timeout', 90); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetOrderTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
		$requestXmlBody .= ' <ItemTransactionIDArray>
    <ItemTransactionID>
	<ItemID>'.$itemid.'</ItemID>
      <TransactionID>'.$transid.'</TransactionID>
    </ItemTransactionID>
  </ItemTransactionIDArray>
		</GetOrderTransactionsRequest>';
		$verb = 'GetOrderTransactions';
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
		die('<P>Error sending request');
						
		$xml = simplexml_load_string($responseXml);
		$this->db->select('paydata, et_id, e_id');
		$this->db->where('transid', $transid);
		$this->db->where('itemid', $itemid);
		$q = $this->db->get('ebay_transactions');
		if ($q->num_rows() > 0) 
		{
			$e = $q->row_array();
			$this->load->helper('explore');
			$paydata = unserialize($e['paydata']);
			$newdata['ManualVerPaymentMethod'] = (string)$xml->OrderArray->Order->CheckoutStatus->PaymentMethod;
			$newdata['ManualVerCheckoutStatus'] = (string)$xml->OrderArray->Order->CheckoutStatus->Status;
			$newdata['ManualVerShippingSelected'] = (string)$xml->OrderArray->Order->ShippingServiceSelected->ShippingService;			
			$ssc = $newdata['ManualVerSelShippingCost'] = (string)$xml->OrderArray->Order->ShippingServiceSelected->ShippingServiceCost;
			$asc = $newdata['ManualVerActShippingCost'] = (string)$xml->OrderArray->Order->TransactionArray->Transaction->ActualShippingCost;
			foreach ($paydata as $k => $v) $newdata[$k] = $v;
			$newdata = serialize($newdata);
			echo 'Updated: <strong>VerifyEbayTransaction:</strong> Payment Method: <strong>'.(string)$xml->OrderArray->Order->CheckoutStatus->PaymentMethod.'</strong> | Checkout Status: <strong>'.(string)$xml->OrderArray->Order->CheckoutStatus->Status.'</strong> | Shipping Selected: <strong>'.(string)$xml->OrderArray->Order->ShippingServiceSelected->ShippingService.'</strong> | Selected ShippingCost: <strong>'.$ssc.'</strong> | Actual Shipping Cost: <strong>'.$asc.'</strong>';
		
			$this->db->update('ebay_transactions', array('paydata' => $newdata, 'ssc' => $newdata['ManualVerShippingSelected'], 'asc' => $asc, 'mverif' => 1,'cascupd' => 2), array('transid' => $transid, 'itemid' => $itemid));
			
			$this->load->model('Mywarehouse_model');
					$data = $this->Mywarehouse_model->getsaleattachdata(1, $e['et_id'], $e['e_id'],1);
					
					if(isset($data['qty']) && $data['qty'] > 1) $warehouse['shipped_actual'] = sprintf("%01.2f", (float)$asc/$data['qty']);
					else $warehouse['shipped_actual'] = $asc;
					
					if(isset($data['qty']) && $data['qty'] > 1) $warehouse['shipped'] = sprintf("%01.2f", (float)$ssc/$data['qty']);
					else $warehouse['shipped'] = $ssc;					
			
					$this->load->model('Myseller_model');
						
						$bcns = $this->Myseller_model->getSales(array((int)$e['et_id']),1, TRUE, TRUE);
						if ($bcns) foreach($bcns as $wid)
						{
							$warehouse['netprofit'] = ((float)$wid['paid']+(float)$warehouse['shipped'])-((float)$wid['cost']+(float)$wid['sellingfee']+(float)$warehouse['shipped_actual']);
							
							foreach($warehouse as $k => $v)
							{								
							 	if ($v != $wid[$k]) $this->Auth_model->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);	
								else unset($warehouse[$k]);
							}
							if (count($warehouse) > 0) $this->db->update('warehouse', $warehouse, array('wid' => (int)$wid['wid']));	
						}
		}		
		


		//$this->mysmarty->view('myebay/myebay_likeitem.html');	
	
}
function ListingToListed($confirm = false)
{
	$this->db->select('wid, bcn, title, status, status_notes');
		$this->db->where("listingid !=", 0);
		$this->db->where("status !=", 'Sold');
		$this->db->where("status !=", 'On Hold');
		$this->db->where("status !=", 'Listed');
		$this->db->where('deleted', 0);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{
			
		echo $this->mysmarty->fetch('header.html');
		echo $this->mysmarty->fetch('messages/error_success_msg.html');
		echo $this->mysmarty->fetch('mywarehouse/top_menu.html');
		
		echo '<h4><a href="'.Site_url().'/Myhousekeeping/ListingToListed/Go">Confirm</a></h4><br clear="all"><table class="admintable3" cellpadding="5">
		<tr>';
		if ($confirm) echo '<th></th>';

		echo '<th>BCN</th><th>Title</th><th>Status</th><th>Status Notes</th></tr>';
		foreach ($this->query->result_array() as $r)
		{			
			if (trim($r['status']) != 'Listed')
			{
				echo '<tr>';
				if ($confirm)
				{
					echo '<td style="color:green;">Updated</td>';
					$this->Auth_model->wlog($r['bcn'], (int)$r['wid'], 'status', $r['status'], 'Listed', 'HousekeepingListed');
					$this->db->update('warehouse', array('status' => 'Listed', 'status_notes' => 'HousekeepingListed - Previous: "'.$r['status']), array('wid' => $r['wid']));//.'"'.$r['status_notes']
					$r['status'] = 'Listed';
					$r['status_notes'] = 'HousekeepingListed - Previous: "'.$r['status'];//.'"'.$r['status_notes']
 				}
				if ($r['status'] == '') $r['status'] = 'Empty';
				if ($r['status_notes'] == '') $r['status_notes'] = 'Empty';
				echo '<td><a href="'.Site_url().'Mywarehouse/bcndetails/'.$r['wid'].'" target="_blank"><img src="/images/admin/table.png" border="0"></a> '.$r['bcn'].'</td><td>'.stripslashes(stripslashes($r['title'])).'</td><td>'.$r['status'].'</td><td>'.$r['status_notes'].'</td></tr>';	
			}
		}		

		echo '</table><br clear="all"><h4><a href="'.Site_url().'/Myhousekeeping/ListingToListed/Go">Confirm</a></h4>';
		
		echo $this->mysmarty->fetch('footer.html');
		}else echo 'Cool, no data found, everthing is in sync!';
}




function IsTested($confirm = false)
{
		$this->query = $this->db->query("SELECT wid, bcn, title, status, status_notes, notes, problems FROM warehouse WHERE `status` = 'Not Tested' AND (`problems` != '' || `notes` != '') and deleted = 0 ORDER BY wid DESC");
		if ($this->query->num_rows() > 0)
		{
			
		echo $this->mysmarty->fetch('header.html');
		echo $this->mysmarty->fetch('messages/error_success_msg.html');
		echo $this->mysmarty->fetch('mywarehouse/top_menu.html');
		
		echo '<h4><a href="'.Site_url().'/Myhousekeeping/IsTested/Go">Confirm</a></h4><br clear="all"><table class="admintable3" cellpadding="5">
		<tr>';
		if ($confirm) echo '<th></th>';

		echo '<th>BCN</th><th>Title</th><th>Status</th><th>Status Notes</th><th>Problems</th><th>Notes</th></tr>';
		foreach ($this->query->result_array() as $r)
		{			

				echo '<tr>';
				if ($confirm)
				{
					echo '<td style="color:green;">Updated</td>';
					//$this->Auth_model->wlog($r['bcn'], (int)$r['wid'], 'status', $r['status'], 'Testing', 'HousekeepingIsTested');
					//$this->db->update('warehouse', array('status' => 'Testing', 'status_notes' => 'HousekeepingIsTested - Previous: "'.$r['status'].'"'.$r['status_notes']), array('wid' => $r['wid']));
					$r['status'] = 'Testing';
					$r['status_notes'] = 'HousekeepingIsTested - Previous: "'.$r['status'].'"'.$r['status_notes'];
 				}
				if ($r['status'] == '') $r['status'] = 'Empty';
				if ($r['status_notes'] == '') $r['status_notes'] = 'Empty';
				//if ($r['problems'] == '') $r['problems'] = 'Empty';
				//if ($r['notes'] == '') $r['notes'] = 'Empty';
				echo '<td nowrap><a href="'.Site_url().'Mywarehouse/bcndetails/'.$r['wid'].'" target="_blank"><img src="/images/admin/table.png" border="0"></a> '.$r['bcn'].'</td><td>'.stripslashes(stripslashes($r['title'])).'</td><td nowrap>'.$r['status'].'</td><td>'.$r['status_notes'].'</td><td>'.$r['problems'].'</td><td>'.$r['notes'].'</td></tr>';	
	
		}		

		echo '</table><br clear="all"><h4><a href="'.Site_url().'/Myhousekeeping/IsTested/Go">Confirm</a></h4>';
		
		echo $this->mysmarty->fetch('footer.html');
		}else echo 'Cool, no data found, everthing is in sync!';
}

function DupBcn()
{
	$this->db->select("wid, bcn");
	$this->db->where('deleted', 0);
	$this->db->order_by("wid", "DESC");
	$w = $this->db->get('warehouse');

	if ($w->num_rows() > 0)
	{
		foreach ($w->result_array() as $g)
		{
			$bcns[$g['bcn']][$g['wid']] = TRUE;	
		}
		
		foreach ($bcns as $k=>$v)
		{
			if (count($bcns[$k]) ==1)  unset($bcns[$k]);	
		}
		if (count($bcns) > 0)
		{
			foreach ($bcns as $k=>$v)
			{
				foreach ($v as $kv=>$vv)
				{
					echo '<a href="/Mywarehouse/bcndetails/'.$kv.'" target="_blank"><img src="/images/admin/table.png" border="0"></a> '. $k.': Warehouse ID:'.$kv.'<br>';	
				}
				echo "<br>";
			}
		}
	}	
}
function BcnStringIDer()
{	
/*
$this->db->select("e_id, ebay_id, e_part, e_qpart, quantity, ebayquantity");
$this->db->where('e_part !=', '');
		$this->db->order_by("e_id", "DESC");
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0)  
		{
			foreach ($q->result_array() as $e)
			{
				printcool($e['e_part']);
				
				//$this->db->update('ebay', array('e_part' => $e['e_part']), array('e_id' => $e['e_id']));
			}
		}
exit();
*/

$process = false; 


		$haystack = array();
		$warehouse = array();
		$this->db->select("e_id, ebay_id, e_part, e_qpart, quantity, ebayquantity");
		$this->db->where('e_part !=', '');
		$this->db->order_by("e_id", "DESC");
		$q = $this->db->get('ebay');
		
		if ($q->num_rows() > 0)  
		{
			$this->db->select('wid, bcn, listingid, status, status_notes');			
			$c = 1;
			
			foreach ($q->result_array() as $e)
			{
				$bcnlist = explode(',', $e['e_part']);				
				foreach ($bcnlist as $wh)
				{
				if (trim($wh) != '') 
					{	
						$haystack[$e['e_id']][trim($wh)] = true;					
						if ($c == 1) { $this->db->where('bcn', trim($wh)); $this->db->or_where('lot', trim($wh)); }
						else { $this->db->or_where('bcn', trim($wh)); $this->db->or_where('lot', trim($wh)); }
						$c++;					
					}					
				}
			}
			
			$q = $this->db->get('warehouse');
			if ($q->num_rows() > 0)
			{
			foreach ($q->result_array() as $r)
					{
						$warehouse[trim($r['bcn'])] = array('wid' => $r['wid'], 'listingid' => $r['listingid'], 'status' => $r['status'], 'status_notes' => $r['status_notes']);						
					}				
			}
			//printcool ($haystack);

			//printcool ($warehouse);
			foreach ($haystack as $eid => $bcnarray)
			{
				foreach ($bcnarray as $thebcn => $body)
				{
					if (isset($warehouse[trim($thebcn)]))	
					{
						if ($warehouse[trim($thebcn)]['listingid'] == (int)$eid) $haystack[(int)$eid][trim($thebcn)] = array('audit' => 'islinked', 'wid' => $warehouse[trim($thebcn)]['wid']);
						elseif ($warehouse[trim($thebcn)]['listingid'] == 0) $haystack[(int)$eid][trim($thebcn)] = array('audit' => 'tobeupdated', 'listingid' => (int)$eid, 'wid' => $warehouse[trim($thebcn)]['wid'], 'status' => $warehouse[trim($thebcn)]['status'], 'status_notes' => $warehouse[trim($thebcn)]['status_notes']);
						else $haystack[(int)$eid][trim($thebcn)] = array('audit' => 'alreadyassignedtoanotherlisting', 'listingid' => $warehouse[trim($thebcn)]['listingid'], 'wid' => $warehouse[trim($thebcn)]['wid']);						
					}
					else
					{
						$haystack[(int)$eid][trim($thebcn)]	= array('audit' => 'tobeinserted', 'waid' => 0, 'listingid' => (int)$eid);
					}
				
					
				}
				
			}
			
			
			foreach ($haystack as $bcns)
			{
				foreach ($bcns as $bcn => $data)

				if ($data['audit'] == 'tobeinserted')
				{
					unset($data['audit']);
					$data['bcn'] = trim($bcn);
					
					printcool ($data, '' , 'Inserted');
					$this->db->insert('warehouse', $data);					
				}
				elseif ($data['audit'] == 'tobeupdated')
				{
					unset($data['audit']);
					$wid = (int)$data['wid'];
					unset($data['wid']);					
					if (trim($data['status_notes']) != '')
					{
						 $data['status_notes'] = 'Updated from: '.$data['status'].' by BcnStringIDer | '.$data['status_notes'];
						 $data['status_notes'] = str_replace(' |  | ', ' | ', $data['status_notes']);
					}
					else $data['status_notes'] = 'Updated from "'.$data['status'].'" by BcnStringIDer';
					$data['status'] = 'Listed';
					printcool ($data, '' , 'Updated '.$wid);
					$this->db->update('warehouse', $data, array('wid' => $wid));
										
				}
				elseif ($data['audit'] == 'alreadyassignedtoanotherlisting') printcool ($data, '' , '<span style="color:red;">MULTIPLE</span>');
			}
			//printcool ($haystack);
			
		}
		
}
	
function genericmarker()
{
	
						$this->db->select("wid, bcn");
						$this->db->where('waid' , 0);
						$this->db->where('generic' , 0);
						$this->db->order_by("wid", "DESC");
						$w = $this->db->get('warehouse');

						if ($w->num_rows() > 0)
						{
							foreach ($w->result_array() as $g)
							{
								if (substr($g['bcn'],0,1) == 'G')
								{
									$this->db->update('warehouse', array('generic' => 1), array('wid' => $g['wid']));
									echo $g['bcn'].'<br>';
								}
							}
						}			
				
					
}

function TransactionsStringIDer()
{	
/*
$this->db->select("e_id, ebay_id, e_part, e_qpart, quantity, ebayquantity");
$this->db->where('e_part !=', '');
		$this->db->order_by("e_id", "DESC");
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0)  
		{
			foreach ($q->result_array() as $e)
			{
				printcool($e['e_part']);
				
				//$this->db->update('ebay', array('e_part' => $e['e_part']), array('e_id' => $e['e_id']));
			}
		}
exit();
*/

$process = false; 


		$haystack = array();
		$warehouse = array();
		
		$this->db->select("et_id, e_id, sn,paid,fee,shipping,paidtime,paydata,itemid,buyerid,buyeremail,sn,asc,ssc");
		$this->db->where('sn !=', '');
		$this->db->order_by("et_id", "DESC");
		$q = $this->db->get('ebay_transactions');		
		
		if ($q->num_rows() > 0)  
		{
			$this->db->select('wid, bcn, channel, sold_id, ordernotes, cost, status, status_notes, sold_date, sold, paid, shipped, sellingfee, netprofit');			
			$c = 1;
			
			foreach ($q->result_array() as $e)
			{
				$bcnlist = explode(',', $e['sn']);	
					
				foreach ($bcnlist as $wh)
				{
				if (trim($wh) != '') 
					{	
						$haystack[$e['et_id']][trim($wh)] = $e['e_id'];	
						if ($c == 1) { $this->db->where('bcn', trim($wh)); $this->db->or_where('lot', trim($wh)); }
						else { $this->db->or_where('bcn', trim($wh)); $this->db->or_where('lot', trim($wh)); }				
						$c++;					
					}					
				}
			}
			
			$q = $this->db->get('warehouse');
			if ($q->num_rows() > 0)
			{
			foreach ($q->result_array() as $r)
					{
						$warehouse[trim($r['bcn'])] = array('wid' => $r['wid'], 'sold_id' => $r['sold_id'], 'channel' => $r['channel']);						
					}				
			}
		
			foreach ($haystack as $etid => $bcnarray)
			{
				foreach ($bcnarray as $thebcn => $listingid)
				{
					if (isset($warehouse[trim($thebcn)]))	
					{
						if ($warehouse[trim($thebcn)]['sold_id'] == (int)$etid) $haystack[(int)$etid][trim($thebcn)] = array('audit' => 'islinked', 'wid' => $warehouse[trim($thebcn)]['wid']);
						elseif ($warehouse[trim($thebcn)]['sold_id'] == 0) $haystack[(int)$etid][trim($thebcn)] = array('audit' => 'tobeupdated', 'sold_id' => (int)$etid, 'listingid' => (int)$listingid, 'wid' => $warehouse[trim($thebcn)]['wid'], 'channel' => 1, 'vended' => 1);
						else $haystack[(int)$etid][trim($thebcn)] = array('audit' => 'alreadyassignedtoanotherSale', 'sold_id' => $warehouse[trim($thebcn)]['sold_id'], 'listingid' => (int)$listingid, 'wid' => $warehouse[trim($thebcn)]['wid']);						
					}
					else
					{
						$haystack[(int)$eid][trim($thebcn)]	= array('audit' => 'Notfound', 'waid' => 0, 'sold_id' => (int)$etid, 'listingid' => (int)$listingid,'bcn' => trim($thebcn), 'generic' =>0, 'vended' => 1, 'channel' => 1);
					}
				
					
				}
				
			}
			
			
			foreach ($haystack as $bcns)
			{
				foreach ($bcns as $bcn => $data)
				
				if ($data['audit'] == 'tobeinserted')
				{
					unset($data['audit']);
					$data['bcn'] = trim($bcn);
					
					printcool ($data, '' , 'Inserted');
					//$this->db->insert('warehouse', $data);					
				}
				elseif ($data['audit'] == 'tobeupdated')
				{
					unset($data['audit']);
					$wid = (int)$data['wid'];
					unset($data['wid']);	
					
					printcool ($data, '' , 'Updated '.$wid);
					//$this->db->update('warehouse', $data, array('wid' => $wid));
										
				}
				elseif ($data['audit'] == 'alreadyassignedtoanotherSale') printcool ($data, '' , '<span style="color:red;">MULTIPLE</span>'.$wid);
				elseif ($data['audit'] == 'Notfound')
				{
					 printcool ($data, '' , '<span style="color:red;">Notfound</span>'.$wid);
					 unset($data['audit']);
					 printcool ($data, '' , 'Inserted');
						//$this->db->insert('warehouse', $data);
				}
			}
			//printcool ($haystack);
			
		}
}


function OrdersStringIDer()
{	
/*
$this->db->select("e_id, ebay_id, e_part, e_qpart, quantity, ebayquantity");
$this->db->where('e_part !=', '');
		$this->db->order_by("e_id", "DESC");
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0)  
		{
			foreach ($q->result_array() as $e)
			{
				printcool($e['e_part']);
				
				//$this->db->update('ebay', array('e_part' => $e['e_part']), array('e_id' => $e['e_id']));
			}
		}
exit();
*/

$process = false; 


		$haystack = array();
		$warehouse = array();
		
		$this->db->select("oid, order");
		$this->db->order_by("oid", "DESC");
		$q = $this->db->get('orders');		
		
		if ($q->num_rows() > 0)  
		{
			$this->db->select('wid, bcn, channel, sold_id, sold_subid');			
			$c = 1;
			
			foreach ($q->result_array() as $e)
			{
				$e['order'] = unserialize($e['order']);
				foreach ($e['order'] as $k => $v)
				{
					if (trim($v['sn']) != '')	
					{
						$bcnlist = explode(',', $v['sn']);	
					
						foreach ($bcnlist as $wh)
						{
						if (trim($wh) != '') 
							{	
								$haystack[$e['oid']][$k][trim($wh)] = true;	
								if ($c == 1) { $this->db->where('bcn', trim($wh)); $this->db->or_where('lot', trim($wh)); }
								else { $this->db->or_where('bcn', trim($wh)); $this->db->or_where('lot', trim($wh)); }				
								$c++;					
							}					
						}
						
					}
					
					
				}
			}
				
			
			$q = $this->db->get('warehouse');
			if ($q->num_rows() > 0)
			{
			foreach ($q->result_array() as $r)

					{
						$warehouse[trim($r['bcn'])] = array('wid' => $r['wid'], 'sold_id' => $r['sold_id'], 'sold_subid' => $r['sold_subid'], 'channel' => $r['channel']);						
					}				
			}
	
			foreach ($haystack as $oid => $earray)
			foreach ($earray as $eid => $bcnarray)
			{
				foreach ($bcnarray as $thebcn => $body)
				{
					if (isset($warehouse[trim($thebcn)]))	
					{
						if ($warehouse[trim($thebcn)]['sold_id'] == (int)$oid && $warehouse[trim($thebcn)]['sold_subid'] == (int)$eid) $haystack[(int)$eid][trim($thebcn)] = array('audit' => 'islinked', 'wid' => $warehouse[trim($thebcn)]['wid']);
						elseif ($warehouse[trim($thebcn)]['sold_id'] == 0) $haystack[(int)$eid][trim($thebcn)] = array('audit' => 'tobeupdated', 'sold_id' => (int)$oid, 'sold_subid' => (int)$eid, 'wid' => $warehouse[trim($thebcn)]['wid'], 'channel' => 2, 'vended' => 1, 'listingid' =>$eid);
						else $haystack[(int)$eid][trim($thebcn)] = array('audit' => 'alreadyassignedtoanotherSale', 'bcn' => trim($thebcn).'|W'.$warehouse[trim($thebcn)]['wid'].'|E'.$warehouse[trim($thebcn)]['sold_id'],'waid' => 0, 'sold_id' => (int)$oid, 'sold_subid' => (int)$eid, 'channel' => 2, 'vended' => 1, 'generic' =>0, 'listingid' =>$eid);						
					}
					else
					{
						$haystack[(int)$eid][trim($thebcn)]	= array('audit' => 'Notfound', 'generic' =>0, 'waid' => 0, 'bcn' => trim($thebcn), 'sold_id' => (int)$oid, 'sold_subid' => (int)$eid, 'channel' => 2, 'vended' => 1, 'listingid' =>$eid);
					}
				
					
				}
				
			}
			
			
			foreach ($haystack as $bcns)
			{
				foreach ($bcns as $bcn => $data)

				if ($data['audit'] == 'tobeinserted')
				{
					unset($data['audit']);
					$data['bcn'] = trim($bcn);
					
					printcool ($data, '' , 'Inserted');
					$this->db->insert('warehouse', $data);					
				}
				elseif ($data['audit'] == 'tobeupdated')
				{
					unset($data['audit']);
					$wid = (int)$data['wid'];
					unset($data['wid']);										
					printcool ($data, '' , 'Updated '.$wid);
					$this->db->update('warehouse', $data, array('wid' => $wid));
										
				}
				elseif ($data['audit'] == 'alreadyassignedtoanotherSale')
				{
					unset($data['audit']);
					printcool ($data, '' , '<span style="color:red;">MULTIPLE</span>');
					$this->db->insert('warehouse', $data);	
				}
				elseif ($data['audit'] == 'Notfound')
				{					 
					 unset($data['audit']);
					 printcool ($data, '' , 'Inserted');
						$this->db->insert('warehouse', $data);
				}
			}
			//printcool ($haystack);
			
		}
}
function UpdateOrderDataInWarehouse()
{
	
}
function MassItemIDToEid()
{
	$this->db->select('et_id, itemid');
	$et = $this->db->get('ebay_transactions');
	if ($et->num_rows() > 0)
	{
		foreach ($et->result_array() as $ett)
		{
			$this->db->select('e_id');
			$this->db->where('ebay_id', (int)$ett['itemid']);
			$e = $this->db->get('ebay');
			if ($e->num_rows() > 0)
			{
				$er = $e->row_array();
				$theid = $er['e_id'];	
			}
			else $theid = 0;		
			printcool ($ett);
			printcool ($theid);
			if ($theid > 0) $this->db->update('ebay_transactions', array('e_id' => $theid),array('et_id' => $ett['et_id']));
		}
		
	}
	
	
	
	
}
function eBaySubmitLog()
{
	$this->db->order_by('msg_id' , 'desc');
	$et = $this->db->get('ebay_submitlog');
	if ($et->num_rows() > 0)
	{
		$loop = $et->result_array();
		$c = 0;
		foreach ($loop as $l)
		{
			if (strlen($l['msg_body']) > 500) 
			{
				$this->db->where('msg_id', $l['msg_id']);
				$this->db->delete('ebay_submitlog');
				$c++;
			}
		}
		if ($c > 0) Redirect('Myhousekeeping/eBaySubmitLog');
		else 
		{
			$this->mysmarty->assign('logs', $loop);
			$this->mysmarty->view('myebay/myebay_submitlog.html');	
		}
	}
}
function markedstatus()
{
		$liste = array();

		$this->db->select("distinct t.*, e_part, e_title, idpath, e_img1, mark", false);
		
		$this->db->order_by("rec", "DESC");
		$this->db->join('ebay e', 't.e_id = e.e_id', 'LEFT');
		$q = $this->db->get('ebay_transactions t');
		
		if ($q->num_rows() > 0) 
		{
			foreach ($q->result_array() as $k=>$v)
			{				
				if (strlen($v['paydata']) > 10) 
				{				
					$v['paydata'] = unserialize($v['paydata']);
					if (isset($v['paydata'])) unset($v['paydata']['PaidTime']);
				}
				else $v['paydata'] = false;
				$liste[$v['et_id']] = $v;
				
				$idarray[] = $v['et_id'];
				$listings[$v['e_id']] = TRUE;	
			
			}
			if (isset($idarray))
			{				
				$this->load->model('Myseller_model'); 	
				$e_bcn =  $this->Myseller_model->getSales($idarray, 1, TRUE);
				unset($idarray);
			}
			
		}
		
		
		
		
		$this->db->order_by("submittime", "DESC");
		
		$this->query = $this->db->get('orders');
		$orders = array();
		if ($this->query->num_rows() > 0) 
			{
			foreach ($this->query->result_array() as $k => $v)	
				{
				if ($v['status'] != '' && $v['status'] != ' ') {
										$v['status'] = unserialize($v['status']);
										//$v['origstatus'] = $v['status'][0];										
										$v['status'] = end($v['status']);
										}
				
				
				if (strlen($v['order']) > 9) 
				{ 
					$v['order'] = unserialize($v['order']); 
					if (is_array($v['order']))
					foreach ($v['order'] as $k => $ov) 
					{
						$os[$ov['e_id']] = $ov['quantity']; 
						if (!isset($ov['sn'])) $v['order'][$k]['sn'] = '';
						if (!isset($ov['admin'])) $v['order'][$k]['admin'] = '';
						$listings[$ov['e_id']] = TRUE;
					}
				}
		
				$orderse[$v['oid']] = $v;	
				
				$idarray[] = $v['oid'];	
			
				}
				if (isset($idarray))
				{
					$this->load->model('Myseller_model'); 	
					$o_bcn = $this->Myseller_model->getSales($idarray, 2, true);
					
					unset($idarray);
				}				
			}

//printcool ($e_bcn);
//printcool ($liste);
foreach ($e_bcn[1] as $k => $v)
{
		//printcool ($k);
		//printcool ($liste[$k]);
		foreach ($v as $b)
		{
		if ($liste[$k]['mark'] == 0)
		{
			if ($b['status'] != 'On Hold')
			{
				$this->db->update('warehouse', array('status' => 'On Hold', 'vended' => 2, 'Location' => 'On Hold'), array('wid' => $b['wid']));	
			}				
		}
		else if ($b['status'] != 'Sold')
			{
				$this->db->update('warehouse', array('status' => 'Sold', 'vended' => 1, 'Location' => 'Sold'), array('wid' => $b['wid']));	
			}
			//printcool ($b);	
		}
}

foreach ($o_bcn[2] as $oid => $eids)
{//printcool ($eids);
	foreach ($eids as $eid => $bcns)
	{ foreach ($bcns as $bcn => $b)
	{ 
		//printcool ($eid, '', 'e_id'	);
		//printcool ($orderse[$oid]['mark'], '', 'MARK');
		
		if ($orderse[$oid]['mark'] == 0)
		{
			if ($b['status'] != 'On Hold')
			{
				$this->db->update('warehouse', array('status' => 'On Hold', 'vended' => 2, 'Location' => 'On Hold'), array('wid' => $b['wid']));	
			}				
		}
		else if ($b['status'] != 'Sold')
			{
				$this->db->update('warehouse', array('status' => 'Sold', 'vended' => 1, 'Location' => 'Sold'), array('wid' => $b['wid']));	
			}
		//printcool ($b);
	}
	//printcool ('--');
	}
}
		//printcool ($liste);
}
function distributeprice()
{
	$this->db->select('e_id, buyItNowPrice');
	$e = $this->db->get('ebay');
	if ($e->num_rows() >0)
	{
		foreach ($e->result_array() as $er)
		{
			$this->db->update('ebay', array ('price_ch1' => (float)$er['buyItNowPrice'], 'price_ch2' => (float)$er['buyItNowPrice'], 'price_ch3' => (float)$er['buyItNowPrice']), array('e_id' => $er['e_id']));	
		}
	}
}
function setchannels()
{
		$this->db->select("e_id");
		$this->db->where('e_part !=', '');
		$this->db->order_by("e_id", "DESC");
		$q = $this->db->get('ebay');		
		if ($q->num_rows() > 0)  
		{
			$this->load->model('Myseller_model');
			foreach ($q->result_array() as $e)
			{
				$dbdata = $this->Myseller_model->getBase(array((int)$e['e_id']), true);
				$am = count($dbdata);
				$ch['qn_ch1'] = $am;
				$ch['qn_ch2'] = $am;
				$ch['qn_ch3'] = $am;	
				$this->db->update('ebay', $ch, array('e_id' => $e['e_id']));
			}
		}
}
function DoMerger()
{
	echo'
		ALTER TABLE `ebay_transactions` ADD `e_id` INT NOT NULL DEFAULT \'0\' AFTER `rec`;<br>
		ALTER TABLE `orders` ADD `revs` INT(2) NULL DEFAULT \'0\' AFTER `mark`;<br>
		ALTER TABLE `ebay` ADD `qn_ch1` INT NOT NULL , ADD `qn_ch2` INT NOT NULL , ADD `qn_ch3` INT NOT NULL , ADD `price_ch1` FLOAT NOT NULL , ADD `price_ch2` FLOAT NOT NULL , ADD `price_ch3` FLOAT NOT NULL ;
		
	<A HREF="'.Site_url().'/Myhousekeeping/BcnStringIDer" target="_blank">BcnStringIDer</a><br>
		<A HREF="'.Site_url().'/Myhousekeeping/genericmarker" target="_blank">genericmarker</a><br>
			<A HREF="'.Site_url().'/Myhousekeeping/TransactionsStringIDer" target="_blank">TransactionsStringIDer</a><br>
				<A HREF="'.Site_url().'/Myhousekeeping/OrdersStringIDer" target="_blank">OrdersStringIDer</a><br>
					<A HREF="'.Site_url().'/Myhousekeeping/MassItemIDToEid" target="_blank">MassItemIDToEid</a><br>
						<A HREF="'.Site_url().'/Myhousekeeping/markedstatus" target="_blank">markedstatus</a><br>
						<A HREF="'.Site_url().'/Myhousekeeping/distributeprice" target="_blank">distributeprice</a><br>
						<A HREF="'.Site_url().'/Myhousekeeping/setchannels" target="_blank">setchannels</a><br>
	
	//3225 t.e_id = e.e_id /myebay
	
	';
}
function CleanupGhostNames()
{
	$this->db->select('wid, bcn');
	$this->db->where('waid', 0);
	$w =$this->db->get('warehouse');	
	if ($w->num_rows() > 0)
	{
		foreach ($w->result_array() as $wid)
		{
			$widt = explode('_', $wid['bcn']);
			if (count($widt) > 1) $this->db->update('warehouse', array('bcn' => str_replace('_','-', $wid['bcn'])),array('wid'=>$wid['wid']));//
		}
		
	}
}
function gtitle()
{
		$this->db->select("wid, bcn, listingid, title");
		$this->db->where('waid' , 0);
		$this->db->where('generic' , 1);
		$this->db->order_by("wid", "DESC");
		$w = $this->db->get('warehouse');		
		if ($w->num_rows() > 0)
		{
			foreach ($w->result_array() as $g)
			{
				if ($g['listingid'] > 0)
				{
				$title = $this->Mywarehouse_model->GetListingTitleAndCondition((int)$g['listingid'], true);
				if (trim($g['title']) == '') $g['title'] = $title;

				$this->db->update('warehouse', array('title' => $g['title']), array('wid' => $g['wid']));
				}
				
				
			}
			
		}
}
function GhostsRenamedExists()
{
	$this->db->select("wid, bcn,  bcn_p1, bcn_p2, listingid, sold_id, title");
		$this->db->where('waid' , 0);
		$this->db->where('generic' , 1);
		$this->db->order_by("wid", "DESC");
		$w = $this->db->get('warehouse');		
		if ($w->num_rows() > 0)
		{
			$this->db->select("wid, bcn,  bcn_p1, bcn_p2, listingid, sold_id, title");
			$cn = 1;
			foreach ($w->result_array() as $g)
			{
				
				if ($cn == 1) $this->db->where('bcn', $g['bcn']);
				else $this->db->or_where('bcn', $g['bcn']);
				$cn++;
				$ghosts[$g['bcn']] = $g;
			}
			$wm = $this->db->get('warehouse');		
				if ($wm->num_rows() > 0)
				{
					foreach ($wm->result_array() as $wmr)
					{
						if ($wmr['wid'] != $ghosts[$wmr['bcn']]['wid'])	
						{
							if ($wmr['bcn'] == $ghosts[$wmr['bcn']]['bcn'])
							{
								$ghosts[$wmr['bcn']]['matched'] = $wmr;
							}
						}
						
					
					}
				}				
			foreach ($ghosts as $k=>$v)
			{
				if (!isset($v['matched'])) unset($ghosts[$k]);	
				else
				{
					$this->db->select("wid, bcn");
					$this->db->where('waid' , 0);
					$this->db->where('generic' , 1);
					$this->db->where('bcn_p1' , "G");
					//$this->db->order_by("bcn_p2", "DESC");
					$this->db->order_by("wid", "DESC");
					$w = $this->db->get('warehouse', 1);		
					if ($w->num_rows() > 0)
					{
						$next = $w->row_array();
						echo($next['wid'].'<br>');
						$next = (int)str_replace('G', '', trim($next['bcn']));	
						echo $next;
						$next++;
						if ((int)$v['wid'] > (int)$v['matched']['wid'])
						{
							$ghosts[$k]['matched']['bcn'] = 'G'.$next;
							$ghosts[$k]['matched']['bcn_p2'] = $next;echo 1;
							$this->db->update('warehouse', array('bcn' => $ghosts[$k]['matched']['bcn'], 'bcn_p2' => $ghosts[$k]['matched']['bcn_p2']), array('wid' => $ghosts[$k]['matched']['wid']));
						}
						else

						{
							$ghosts[$k]['bcn'] = 'G'.$next;
							$ghosts[$k]['bcn_p2'] = $next;echo 2;
							$this->db->update('warehouse', array('bcn' => $ghosts[$k]['bcn'], 'bcn_p2' => $ghosts[$k]['bcn_p2']), array('wid' => $ghosts[$k]['wid']));
						}
						
					}
				}
			}
			printcool ($ghosts);
		}
}
function respoolgenerics()
{
	
	$this->db->select("wid, bcn,  bcn_p1, bcn_p2, listingid, sold_id, title");
		$this->db->where('waid' , 0);
		$this->db->where('generic' , 1);
		$this->db->where('deleted' , 0);
		$this->db->order_by("wid", "ASC");
		$w = $this->db->get('warehouse');		
		if ($w->num_rows() > 0)
		{			
			foreach ($w->result_array() as $g)
			{
				$ghosts[(int)$g['wid']] = $g;
			}
			ksort($ghosts);
			$cn = 1;
			foreach ($ghosts as $g)
			{				
				$this->db->update('warehouse', array('bcn' => 'G'.(int)$cn, 'bcn_p1' => 'G', 'bcn_p2' => (int)$cn), array('wid' => $g['wid']));
				$cn++;
				
			}
		}
	
}


function GhostsNonGenericRenamedExists($confirm = false)
{
	//$this->db->select("wid, bcn, oldbcn, waid, bcn_p1, bcn_p2, listingid, sold_id, title");
		$this->db->where('waid' , 0);
		$this->db->where('generic' , 0);
		$this->db->where('deleted', 0);
		$this->db->where('nr', 0);
		$this->db->order_by("wid", "DESC");
		$w = $this->db->get('warehouse');		
		if ($w->num_rows() > 0)
		{//wid, bcn, oldbcn, waid,  bcn_p1, bcn_p2, listingid, sold_id, title
			$sql = "SELECT * FROM warehouse WHERE `generic` = 0 AND `generic` = 0 AND `nr` = 0 AND (";	
			$cn = 1;
			foreach ($w->result_array() as $g)
			{				
				if ($cn == 1) $sql .= '`bcn` = "'.htmlspecialchars($g['bcn']).'"';
				else $sql .= ' OR `bcn` = "'.htmlspecialchars($g['bcn']).'"';
				$sql .= ' OR `oldbcn` = "'.htmlspecialchars($g['bcn']).'"';
				$cn++;
				$ghosts[$g['bcn']] = $g;
			}
			$sql .= ')';
				$wm = $this->db->query($sql);
				if ($wm->num_rows() > 0)
				{
					foreach ($wm->result_array() as $wmr)
					{
						if ($wmr['wid'] != $ghosts[$wmr['bcn']]['wid'])	
						{
							if ($wmr['bcn'] == $ghosts[$wmr['bcn']]['bcn'])
							{
								$ghosts[$wmr['bcn']]['matched'] = $wmr;
							}							
							if ($wmr['oldbcn'] == $ghosts[$wmr['oldbcn']]['bcn'])
							{
								$ghosts[$wmr['oldbcn']]['matchedoldbcn'] = $wmr;
							}
						}
						
					
					}
				}				
			foreach ($ghosts as $k=>$v)
			{
				if (!isset($v['matched']) && !isset($v['matchedoldbcn'])) unset($ghosts[$k]);	
			}
			//printcool ($ghosts);
		}
		if (count($ghosts) > 0)
		{
			echo '<h4><a href="/Myhousekeeping/GhostsNonGenericRenamedExists/Confirm">Confirm</a><br clear="all">
			
			<table cellpadding="1" cellspacing="1" border="1"><tr><th>WID</th><th>BCN</th><th>OLDBCN</th><th>LISTING</th><th>SOLDID</th></tr>';
			foreach ($ghosts as $k=>$v)
			{
				if (trim($v['wid']) != '')
				{
				echo '<tr><td><a href="/Mywarehouse/bcndetails/'.$v['wid'].'" target="_blank"><img src="/images/admin/table.png" border="0"></a> '.$v['wid'].'</td><td>'. $k.'</td><td></td><td>'.$v['listingid'].'</td><td>'.$v['sold_id'].'</td></tr>';
				if (isset($v['matched']))
				{
				echo '<tr><td><a href="/Mywarehouse/bcndetails/'.$v['matched']['wid'].'" target="_blank"><img src="/images/admin/table.png" border="0"></a> '.$v['matched']['wid'].'</td><td>'. $v['matched']['bcn'].'</td><td></td><td>'.$v['matched']['listingid'].'</td><td>'.$v['matched']['sold_id'].'</td></tr>';
				echo '<tr><Td colspan="5">Append "-1" to second BCN</td></tr>';	
				if ($confirm) $this->db->update('warehouse', array('bcn' => $v['matched']['bcn'].'-1'), array('wid' => $v['matched']['wid']));
				}
							
				if (isset($v['matchedoldbcn'])) 
				{
				echo '<tr><td><a href="/Mywarehouse/bcndetails/'.$v['matchedoldbcn']['wid'].'" target="_blank"><img src="/images/admin/table.png" border="0"></a> '.$v['matchedoldbcn']['wid'].'</td><td>'. $v['matchedoldbcn']['bcn'].'</td><td>'.$v['matchedoldbcn']['oldbcn'].'</td><td>'.$v['matchedoldbcn']['listingid'].'</td><td>'.$v['matchedoldbcn']['sold_id'].'</td></tr>';
				if (($v['listingid'] == 0 && $v['sold_id'] == 0) || ($v['listingid'] == 0 && $v['sold_id'] == 0 && $v['matchedoldbcn']['listingid'] == 0 && $v['matchedoldbcn']['sold_id'] == 0))
				{
					echo '<tr><Td colspan="5">Remove Ghost BCN, no change to matched BCN</td></tr>';	
					
					if ($confirm) $this->db->update('warehouse', array('deleted' => 1), array('wid' => $v['wid']));

				}
				elseif ($v['matchedoldbcn']['listingid'] == 0 && $v['matchedoldbcn']['sold_id'] == 0)
				{
					echo '<tr><Td colspan="5">Remove '.$v['wid'].', take listing ID '.$v['listingid'].' &amp; SoldId '.$v['sold_id'].' and other specific data, </td></tr>';	
					
					if ($confirm) $this->db->update('warehouse', array('deleted' => 1), array('wid' => $v['wid']));
					unset($v['wid']);
					unset($v['waid']);
					unset($v['insid']);
					unset($v['bcn']);
					unset($v['bcn_p1']);
					unset($v['bcn_p2']);
					unset($v['bcn_p3']);
					unset($v['oldbcn']);
					
					unset($v['location']);
					unset($v['sn']);
					unset($v['post']);
					unset($v['battery']);
					unset($v['charger']);
					unset($v['hddstatus']);
					unset($v['problems']);
					unset($v['notes']);
					unset($v['partsneeded']);
					unset($v['warranty']);
					unset($v['techlastupdate']);
					unset($v['tech']);
					
					$m['matchedoldbcn'] = $v['matchedoldbcn'];
					unset($v['matchedoldbcn']);					
					if ($confirm){ $this->db->update('warehouse', $v, array('wid' => $m['matchedoldbcn']['wid']));
					foreach ($v as $kk => $vv)
					{
					 if ($vv != $m['matchedoldbcn'][$kk]) $this->Auth_model->wlog($m['matchedoldbcn']['bcn'], $m['matchedoldbcn']['wid'], $kk, $m['matchedoldbcn'][$kk], $vv);	
					}		}

				}
				elseif (($v['listingid'] == 0 && $v['matchedoldbcn']['sold_id'] == 0) || ($v['listingid'] == $v['matchedoldbcn']['listingid'] && $v['matchedoldbcn']['sold_id'] == 0))
				{
					echo '<tr><Td colspan="5">Remove '.$v['wid'].', take only  SoldId '.$v['sold_id'].' and other specific data, </td></tr>';	
					
					if ($confirm) $this->db->update('warehouse', array('deleted' => 1), array('wid' => $v['wid']));
					unset($v['wid']);
					unset($v['waid']);
					unset($v['insid']);
					unset($v['bcn']);
					unset($v['bcn_p1']);
					unset($v['bcn_p2']);
					unset($v['bcn_p3']);
					unset($v['oldbcn']);
					unset($v['listingid']);
					
					unset($v['location']);
					unset($v['sn']);
					unset($v['post']);
					unset($v['battery']);
					unset($v['charger']);
					unset($v['hddstatus']);
					unset($v['problems']);
					unset($v['notes']);
					unset($v['partsneeded']);
					unset($v['warranty']);
					unset($v['techlastupdate']);
					unset($v['tech']);
							
					$m['matchedoldbcn'] = $v['matchedoldbcn'];
					unset($v['matchedoldbcn']);					
					if ($confirm){ $this->db->update('warehouse', $v, array('wid' => $m['matchedoldbcn']['wid']));
					foreach ($v as $kk => $vv)
					{
					 if ($vv != $m['matchedoldbcn'][$kk]) $this->Auth_model->wlog($m['matchedoldbcn']['bcn'], $m['matchedoldbcn']['wid'], $kk, $m['matchedoldbcn'][$kk], $vv);	
					}		}	

				}
				elseif ($v['listingid'] == $v['matchedoldbcn']['listingid'] && $v['matchedoldbcn']['listing_id'] == 0)
				{
					echo '<tr><Td colspan="5">Remove '.$v['wid'].'</td></tr>';	
					
					if ($confirm) $this->db->update('warehouse', array('deleted' => 1), array('wid' => $v['wid']));						

				}
				
				
				
				}
				
				echo '<tr><Td colspan="5"></td></tr>';				
			}
				}
			echo '</table>';
			if ($confirm) echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.location.href='/Myhousekeeping/GhostsNonGenericRenamedExists'
        </SCRIPT>");
		}
}
function cleangwid()
{
	
	$this->db->select("wid, bcn, bcn_p1, bcn_p2");
		$this->db->where('waid' , 0);
		$this->db->where('generic' , 1);
		$this->db->order_by("wid", "DESC");
		$w = $this->db->get('warehouse');		
		if ($w->num_rows() > 0)
		{
			$n = $w->result_array();
			/*$next = (int)str_replace('G', '', trim($next['bcn']));		

			if (!$called) $amount = (int)$_POST['amount'];
			else $amount = $called['qn'];
			
			$start = 1;
			while ($start <= $amount)
						{
							$next++;
							$array['waid'] = 0;
							$array['bcn'] = "G".$next;
							$array['bcn_p1'] = "G";
							$array['bcn_p2'] = $next;
							$array['listingid'] = $listingid;
							$array['status'] = 'Listed';
							$array['title'] = $title;
							$array['listed_date'] = CurrentTime();
							$array['generic'] = 1;							
							$array['adminid'] = (int)$this->session->userdata['admin_id']; 
							$this->db->insert('warehouse', $array);
							$start++;
							
						}
			$actionqn = 0 - $amount;		*/
			foreach ($n as $nn)
			{
		printcool($nn);
				//$this->db->update('warehouse', array('bcn_p1' => 'G', 'bcn_p2' => (int)str_replace('G', '', trim($nn['bcn']))), array('wid' => $nn['wid']));
			
				
			}
		}
}
function GhostTitlePopulate($confirm = false)
{
		$this->db->select("wid, bcn, listingid, title");
		$this->db->where('waid' , 0);
		$this->db->where('deleted', 0);
		$this->db->where('listingid !=', 0);

		$this->db->where('nr', 0);
		$this->db->order_by("wid", "DESC");
		$w = $this->db->get('warehouse');		
		if ($w->num_rows() > 0)
		{
			$this->db->select('e_id, e_title');
			$cn = 1;
			foreach ($w->result_array() as $g)
			{				
				$pool[$g['listingid']][] = $g;
				if ($cn == 1) $this->db->where('e_id', $g['listingid']);
				else $this->db->or_where('e_id', $g['listingid']);
				$cn++;
			}
			$e = $this->db->get('ebay');
			if ($e->num_rows() > 0)
			{
				foreach ($e->result_array() as $eb)
				{				
					$listings[$eb['e_id']] = $eb['e_title'];
				}		
				
			}
			echo '<h4><a href="/Myhousekeeping/GhostTitlePopulate/Confirm">Confirm</a><br clear="all">
				<table cellpadding="1" cellspacing="1" border="1"><tr><th>WID</th><th>ListingID</th><th>Ghost Title</th><th>Listing Title to Replace</th></tr>';
			foreach ($pool as $k => $v)
			{
				//echo '<tr><td colspan="4">Listing: '.$k.'</td></tr>';
				foreach ($v as $vv)
				{
				if (trim($vv['title']) != trim($listings[$vv['listingid']]))
				{
				echo '<tr><td>'.$vv['wid'].'</td><td>'. $vv['listingid'].'</td><td>'.$vv['title'].'</td><td>'.$listings[$vv['listingid']].'</td></tr>';
				if ($confirm) $this->db->update('warehouse', array('title' => $listings[$vv['listingid']]), array('wid' => $vv['wid']));
				}
				}
				
			}
			echo '</table>';
			if ($confirm) echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.location.href='/Myhousekeeping/GhostTitlePopulate'
        </SCRIPT>");
		}
			
			
}
function SalesDataPopulate($confirm = false)
{
	//$confirm = false;
		//$this->db->select("wid, bcn, listingid, title");
		$this->db->where('sold_id !=', 0);
		$this->db->where('deleted', 0);
		$this->db->order_by("wid", "DESC");
		$w = $this->db->get('warehouse');		
		if ($w->num_rows() > 0)
		{	
		echo '<h4><a href="/Myhousekeeping/SalesDataPopulate/Confirm">Confirm</a></h4><br clear="all"><table cellpadding="1" cellspacing="1" border="1">
		<tr><td>ID</td><td>BCN</td><td>Order/QN</td><td>Field</td><td>From</td><td>To (NEW VAL)</td><td>Log</td></tr>';
			foreach ($w->result_array() as $g)			
			{				
				$data = $this->Mywarehouse_model->getsaleattachdata((int)$g['channel'], (int)$g['sold_id'], $g['listingid'],0);
				
				$qty = $data['qty'];
				$mark = $data['mark'];
				$data['ordernotes'] = $g['ordernotes'];
				unset($data['qty']);
				unset($data['mark']);
					
				if ($mark == 1 || (int)$g['channel'] == 4)
				{
					//$data['status'] = 'Sold';
					//$data['location'] = 'Sold';
					//$data['vended'] = 1;
					$data['netprofit'] = sprintf("%01.2f",((float)$data['paid']-((float)$g['cost']+(float)$data['sellingfee']+(float)$data['shipped_actual'])));				
				}
				else
				{
					//$data['status'] = 'On Hold';
					  //$data['location'] = 'On Hold';
					//$data['vended'] = 2;					
				}
				$data['soldqn'] = $qty;
				$data['prevstatus'] = $g['status'];
				if (trim($g['sold']) == '')
				{
					if ((int)$g['channel'] == 1) $data['sold'] = 'eBay';
					elseif ((int)$g['channel'] == 2) $data['sold'] = 'WebSite';	
					elseif ((int)$g['channel'] == 4) $data['sold'] = 'Warehouse';				
				}
				else
				{	
					if ((int)$g['channel'] == 1) { if ($g['sold'] != 'eBay') $data['sold'] = $g['sold'].' eBay'; }
					elseif ((int)$g['channel'] == 2) { if ($g['sold'] != 'WebSite') $data['sold'] = $g['sold'].' WebSite';	 }
					elseif ((int)$g['channel'] == 4) { if ($g['sold'] != 'Warehouse') $data['sold'] = $g['sold'].' Warehouse'; }
				}		
			
			//MARK COMPLETE - vended = 1;
			if ((int)$g['channel'] == 2) $sdata =  'WebSite Sale '.(int)$g['sold_id'].'/'.(int)$g['sold_subid'];
			elseif ((int)$_POST['channel'] == 4) $sdata =  'Warehouse Sale '.(int)$g['sold_id'];
			else $sdata =  'eBay Sale '.(int)$g['sold_id'];
			
			//if ($g['status'] != $data['status']) $data['status_notes'] = 'Changed from "'.$g['status'].'" - '.$sdata.' by AutoPopulateSalesData';
			
			if((int)$g['channel'] == 4)
			{
				unset($data['shipped']);	
				unset($data['shipped_actual']);	
				unset($data['sellingfee']);	
				unset($data['paid']);	
				unset($data['sold_date']);	
			}
			
			foreach ($data as $k => $v)
			{
				if ($k == 'ordernotes')	
				{
					$data[$k] = str_replace('| ', '', $data[$k]);
					$data[$k] = str_replace('| ', '', $data[$k]);
					$data[$k] = str_replace(' Order', '', $data[$k]);
					$data[$k] = str_replace('Transaction', '', $data[$k]);	
					$data[$k] = str_replace((int)$g['sold_id'], '', $data[$k]);
					$data[$k] = str_replace($data['sold'], '', $data[$k]);
					$data[$k] = str_replace('  ', ' ', $data[$k]);
					$data[$k] = str_replace('  ', ' ', $data[$k]);					
					//$data[$k] = $data['sold'].' Order '.(int)$g['sold_id'].' | '.$data[$k];
					
				}
				//if ($k == 'status_notes' && (trim($g['status']) == 'Sold')) $data[$k] = $g['status_notes'];
				if ($k == 'prevstatus') $data[$k] = $g['prevstatus'];
				//if ((int)$qty > 1)
				//{				
				//	//if ($k == 'shipped' && (float)$v < (float)$g['shipped']) $data[$k] = $g['shipped'];
				//	if ($k == 'shipped') $data[$k] = sprintf("%01.2f", (float)$v/$qty);
				//	if ($k == 'shipped_actual') $data[$k] = sprintf("%01.2f", (float)$v/$qty);
				//	if ($k == 'sellingfee') $data[$k] = sprintf("%01.2f", (float)$v/$qty);
				//}
				
				if ($k == 'paid' && trim($g['paid']) != $v) $data[$k] = sprintf("%01.2f", (float)$v);
				if ($k == 'shipped' && trim($g['shipped']) != $v) $data[$k] = sprintf("%01.2f", (float)$v);
				if ($k == 'shipped_actual' && trim($g['shipped_actual']) != $v) $data[$k] = sprintf("%01.2f", (float)$v);
				if ($k == 'sellingfee' && trim($g['sellingfee']) != $v) $data[$k] = sprintf("%01.2f", (float)$v);
						
			}
			if ($confirm) $this->db->update('warehouse', $data, array('wid' => (int)$g['wid']));	
					
			foreach ($data as $k => $v)
			{
				unset($nosave);		
					
				if ($k == 'ordernotes') $nosave = 'NO LOG';
				if ($k == 'soldqn') $nosave = 'NO LOG'; 
				
			 if (trim($v) != trim($g[$k]))
			 {  
			 	
				   if ($confirm && trim($g[$k]) != '' && !isset($nosave)) $this->Auth_model->wlog($g['bcn'], $g['wid'], $k, $g[$k], $v);	
			  		if (trim($g[$k]) != '' && $k != 'soldqn')echo '<tr><td>'.$g['wid'].'</td><td>'.$g['bcn'].'</td><td><a href="/Myebay/Showorder/'.$g['sold_id'].'/'.$g['channel'].'" target="_blank">'.$g['sold_id'].'</a> QN:'.$qty.'</td><td>'.$k.'</td><td>'.$g[$k].'</td><td>'.$v.'</td><td>'.$nosave.'</td></tr>';
				
			 }			 
			}
		}
	}
		echo '</table>';
		if ($confirm) echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.location.href='/Myhousekeeping/SalesDataPopulate'
        </SCRIPT>");				
}
function ListingDataPopulate($confirm = false)
{
	//$confirm = false;
		$this->db->select("wid, bcn, listingid, listed, listed_date");
		$this->db->where('deleted', 0);
		$this->db->where('listingid !=', 0);
		$this->db->order_by("wid", "DESC");
		$w = $this->db->get('warehouse');		
		if ($w->num_rows() > 0)
		{	
		echo '<h4><a href="/Myhousekeeping/ListingDataPopulate/Confirm">Confirm</a></h4><br clear="all"><table cellpadding="1" cellspacing="1" border="1">
		<tr><td>ID</td><td>BCN</td><td>Listing</td><td>Field</td><td>From</td><td>To (NEW VAL)</td><td>Log</td></tr>';
			foreach ($w->result_array() as $g)			
			{				
				$this->db->select('ebended, endedreason, ebay_submitted');
				$this->db->where('e_id', (int)$g['listingid']);
				$e = $this->db->get('ebay');
				if ($e->num_rows() > 0)
				{
					$edata = $e->row_array();	
				}
				if (isset($edata))
				{
					$edata['ebay_submitted'] = str_replace('Manual @ ', '', $edata['ebay_submitted']); 
					$edata['ebay_submitted'] = explode(' by', $edata['ebay_submitted']);
					$edata['ebay_submitted'] = $edata['ebay_submitted'][0];
					if ($g['listed'] != 'eBay '.$g['listingid']) $data['listed'] = 'eBay '.$g['listingid'];
					if (trim($g['listed_date']) == '' || trim($g['listed_date']) == 'EBAY added qty' || trim($g['listed_date']) == 'ebay') { if ($g['listed_date'] != trim($edata['ebay_submitted'])) $data['listed_date'] = $edata['ebay_submitted'];		 }
					
				if ($confirm && isset($data)) $this->db->update('warehouse', $data, array('wid' => (int)$g['wid']));	
						
					if (isset ($data))foreach ($data as $k => $v)
					{				
					 if (trim($v) != trim($g[$k]))
						 {  
							
							   //if ($confirm) $this->Auth_model->wlog($g['bcn'], $g['wid'], $k, $g[$k], $v);	
								echo '<tr><td>'.$g['wid'].'</td><td>'.$g['bcn'].'</td><td><a href="/Myebay/Search/'.$g['listingid'].'/" target="_blank">'.$g['listingid'].'</a></td><td>'.$k.'</td><td>'.$g[$k].'</td><td>'.$v.'</td><td></td></tr>';
							
						 }			 
					}
					unset($data);
			}
		}
	}
		echo '</table>';
		if ($confirm) echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.location.href='/Myhousekeeping/ListingDataPopulate'
        </SCRIPT>");				
}
function eBayTransactionQNPriceingChecker($confirm = false)
{
	$this->db->select('et_id, qty,mark, paid,fee,shipping,paidtime,paydata,itemid,buyerid,buyeremail,sn,asc,ssc');	
	$this->db->where('qty >', 1);
	$q = $this->db->get('ebay_transactions');
	if ($q->num_rows() > 0) 
	{
		$sales = $q->result_array();
		$this->load->model('Myseller_model'); 
		echo '<h4><a href="/Myhousekeeping/eBayTransactionQNPriceingChecker/Confirm">Confirm</a></h4><br clear="all"><table cellpadding="1" cellspacing="1" border="1">
		<tr><td>Trans.ID</td><td>BCN</td><td>Trans Total Pay</td><td>Trans. QTY</td><td>BCN Orig Paid</td><td>New Paid</td>
		<td>Total Shipped</td><td>Shipped</td><Td>New Shipped</td>
		<td>Total Sh.Act</td><td>Sh.Act</td><Td>New Sh.Act</td>
		<td>Total Fee</td><td>Fee</td><Td>New Fee</td>
		
		</tr>';	
		foreach ($sales as $s)
		{
			$n['paid'] = sprintf("%01.2f", (float)$s['paid']/(int)$s['qty']);
			$n['shipped'] = sprintf("%01.2f", (float)$s['ssc']/(int)$s['qty']);
			$n['shipped_actual'] = sprintf("%01.2f", (float)$s['asc']/(int)$s['qty']);
			$n['sellingfee'] = sprintf("%01.2f", (float)$s['fee']/(int)$s['qty']);

			
			$bcns = $this->Myseller_model->getSales(array((int)$s['et_id']), 1, TRUE, TRUE);
			if ($bcns)
			{
				//printcool($s);

				foreach ($bcns as $b)
				{
					//printcool($b);	
					if (trim($b['paid']) == $n['paid']) $n['paid'] = 'No Change ('.$n['paid'].')';
					if ((float)trim($b['shipped']) == (float)$n['shipped']) $n['shipped'] = 'No Change';
					if ((float)trim($b['shipped_actual']) == (float)$n['shipped_actual'])$n['shipped_actual'] = 'No Change';
					if ((float)trim($b['sellingfee']) == (float)$n['sellingfee']) $n['sellingfee'] = 'No Change';

						echo '<tr><td>'.$s['et_id'].'</td><td>'.$b['bcn'].'</td><td>'.$s['paid'].'</td><td>'.$s['qty'].'</td><td>'.(float)$b['paid'].'</td><td>'.$n['paid'].'</td>
						
						<td>'.$s['ssc'].'</td><td>'.(float)$b['shipped'].'</td><td>'.$n['shipped'].'</td>
						<td>'.$s['asc'].'</td><td>'.(float)$b['shipped_actual'].'</td><td>'.$n['shipped_actual'].'</td>
						<td>'.$s['fee'].'</td><td>'.(float)$b['sellingfee'].'</td><td>'.$n['sellingfee'].'</td>
						
						</tr>';
						if ($confirm)
						{
							if (trim($b['paid']) == $n['paid']) unset($n['paid']);
							if ((float)trim($b['shipped']) == (float)$n['shipped']) unset($n['shipped']);
							if ((float)trim($b['shipped_actual']) == (float)$n['shipped_actual']) unset($n['shipped_actual']);
							if ((float)trim($b['sellingfee']) == (float)$n['sellingfee']) unset($n['paid']);
													
							if (isset($n) && count($n) > 0) $this->db->update('warehouse', $n, array('wid' => (int)$b['wid']));
							
							//$this->Auth_model->wlog($b['bcn'], $b['wid'], 'paid', $b['paid'], $paid);	
						}
						unset($n);	

				}
			}
		}
		
		echo '</table>';
	}	
	
	if ($confirm) echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.location.href='/Myhousekeeping/eBayTransactionQNPriceingChecker'
        </SCRIPT>");
}

function LastStatusPopulateFromLog($confirm = false)
{

		$this->db->select("distinct l.*, status, prevstatus, status_notes", false);
		$this->db->where('l.field', 'status');
		$this->db->order_by("wl_id", "DESC");
		$this->db->join('warehouse w', 'l.wid = w.wid', 'LEFT');
		//$this->db->limit(10);
		$q = $this->db->get('warehouse_log l');
		if ($q->num_rows() > 0)
		{
			echo '<h4><a href="/Myhousekeeping/LastStatusPopulateFromLog/Confirm">Confirm</a><br clear="all">
				<table cellpadding="1" cellspacing="1" border="1"><tr><th>WID</th><th>Logged Prev Status</th><th>Current Prev Status</th><th>Current Status</th><th>Notes</th></tr>';
			foreach ($q->result_array() as $v)
			{
				if ((int)$v['wid'] != 0)
				{
				$v['oldprevstatus'] = $v['prevstatus'];
				$v['prevstatus'] = trim($v['datafrom']);
				if ($v['prevstatus'] != '' && $v['prevstatus'] != 'Sold' && $v['prevstatus'] != 'On Hold')
				{	
					$v['status_notes'] = explode('|', $v['status_notes']);
					$v['status_notes'] = trim($v['status_notes'][0]);
					echo '<tr><td><a href="'.Site_url().'Mywarehouse/logger/'.$v['wid'].'" target="_blank"><img src="/images/admin/table.png" border="0"></a> '.$v['wid'].'</td><td>'. $v['prevstatus'].'</td><td>'.$v['oldprevstatus'].'</td><td>'.$v['status'].'</td><td>'.$v['status_notes'].'</td></tr>';
					
					if ($confirm) $this->db->update('warehouse', array('prevstatus' => $v['prevstatus'], 'status_notes' => $v['status_notes']), array('wid' => $v['wid']));	
				}
				}
			}
		}	
		echo '</table>';
			if ($confirm) echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.location.href='/Myhousekeeping/LastStatusPopulateFromLog'
        </SCRIPT>");

			
}function _XML2Array($parent)
{
    $array = array();

    foreach ($parent as $name => $element) {
        ($node = & $array[$name])
            && (1 === count($node) ? $node = array($node) : 1)
            && $node = & $node[];

        $node = $element->count() ? $this->_XML2Array($element) : trim($element);
    }

    return $array;
}
function showlistingswithouteid()
{
	$this->db->select('et_id, itemid');
		$this->db->where('e_id', 0);
		$this->db->order_by('et_id', 'ASC');
		//$this->db->limit(100);
		$q = $this->db->get('ebay_transactions');
		
		if ($q->num_rows() > 0)printcool ($q->num_rows());

		
}
//////////////////////////////////////////////////////////////
function SetShipped($soldid, $channel)
{
	$this->load->model('Mywarehouse_model');
	$_POST['soldid'] = $soldid;
	$_POST['channel'] = $channel;
	if (isset($_POST['soldid']) && isset($_POST['channel']))
	{
		$this->load->model('Myseller_model');
		$wids = $this->Myseller_model->getSales(array((int)$_POST['soldid']), $_POST['channel'], true, true);
		if ($wids) 
		{
			foreach ($wids as $wid)
			{
				//$data['status'] = 'Sold';
				//$data['location'] = 'Sold';
				//$data['vended'] = 1;	
				//$data['shipped_date'] = CurrentTime();
				//$data['status_notes'] = 'Changed from "'.$wid['status'].'" - SetShipped by '.$this->session->userdata['ownnames'];
				//if (trim($wid['status_notes']) == '') $data['status_notes'] = $statusnotes;
				//else $data['status_notes'] = $statusnotes.' | '.$wid['status_notes'];			

								
				//$this->db->update('warehouse', $data, array('wid' => (int)$wid['wid']));	
				//LOG CHANGES
				//foreach ($data as $k => $v)
				//{//printcool ($v); printcool ($wid[$k]);
				// if ($v != $wid[$k]) $this->Auth_model->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);	
				//}
				//if ($wid['history'] == 1)
				//{
				$listingid = $wid['listingid'];
				$_POST['subid'] = $subid = $wid['sold_subid'];
				$_POST['wid'] = $wid['wid'];
				$_POST['remove'] = 0;
				$this->BCNSalesAttach(FALSE);
				//}
			}

			
			/*
				$this->db->select('admin, revs, qty');
				$this->db->where('et_id', (int)$_POST['soldid']);
				$q = $this->db->get('ebay_transactions');
				if ($q->num_rows() > 0) 
				{	$res = $q->row_array();
					$res['revs']++;	
					$qty = $res['qty'];
					$res['admin'] = '('.$res['revs'].') Housekeeping Pre Oct 2015';
//$this->db->update('ebay_transactions', array('mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('et_id' => (int)$_POST['soldid']));
					//printcool ($res);
					
				}			
			*/

		}
		
	}
}
function BCNSalesAttach($assign = TRUE)
{
	$this->load->model('Mywarehouse_model');
	if (isset($_POST['wid']) && isset($_POST['soldid']) && isset($_POST['subid']) && isset($_POST['channel']) && isset($_POST['remove']))
	{
		$wid = $this->Mywarehouse_model->getbcnattachdata((int)$_POST['wid']);
		$data = $this->Mywarehouse_model->getsaleattachdata((int)$_POST['channel'], (int)$_POST['soldid'], $wid['listingid'],(int)$_POST['remove']);
			
		//printcool ($wid, '', 'getbcndata');
		if ($wid )//&& $wid['history'] == 1)
		{				
			//printcool ($data, '', 'getsalesdata');
			$qty = $data['qty'];
			$mark = $data['mark'];
			unset($data['qty']);
			unset($data['mark']);		
			$data['channel'] = (int)$_POST['channel'];
							
				$data['sold_id'] = (int)$_POST['soldid'];
				$data['sold_subid'] = (int)$_POST['subid'];
				$data['status'] = 'Sold';
					$data['location'] = 'Sold';
					$data['vended'] = 1;
					$data['setshipped'] = 0;
					if ((int)$_POST['channel'] == 1) $data['sold'] = 'eBay '.(int)$_POST['soldid'];
					elseif ((int)$_POST['channel'] == 2) $data['sold'] = 'WebSite';	
					elseif ((int)$_POST['channel'] == 4) $data['sold'] = 'Warehouse';	
					//$data['ordernotes'] = $data['sold'].' Order '.$data['sold_id'].' | '.$data['ordernotes'];	
					if ((int)$_POST['channel'] == 4)
					{
						$data['netprofit'] = (float)$data['paid']-((float)$wid['cost']+(float)$data['sellingfee']+(float)$data['shipped_actual']);
						unset($data['paid']);
						unset($data['shipped']);
						unset($data['shipped_actual']);
						unset($data['sellingfee']);									
					}
					else
					{
						$data['netprofit'] = (float)$data['paid']-((float)$wid['cost']+(float)$data['sellingfee']+(float)$data['shipped_actual']);
						
					}
			
				//$data['prevstatus'] = $wid['status'];
				$actionqn = 1;
		
			//MARK COMPLETE - vended = 1;
			
			//$data['status_notes'] = 'Changed from "'.$wid['status'].'" - '.$sdata.' by Housekeeping Pre Oct 2015';
			//if (trim($wid['status_notes']) == '') $data['status_notes'] = $statusnotes;
			//else $data['status_notes'] = $statusnotes.' | '.$wid['status_notes'];			
			
			//printcool ($data, '', 'update');
			//printcool($_POST['wid']);
			echo $_POST['wid'].', ';
			//printcool($wid);
$this->db->update('warehouse', $data, array('wid' => (int)$_POST['wid']));	
			//LOG CHANGES

$this->Myseller_model->SaveSoldQN($data['channel'], $data['sold_id'], $data['sold_subid'], $qty);
			
			
		}else echo 0;		
	}else echo 0;
}
function OldSetShipped()
{
	set_time_limit(180);
		ini_set('mysql.connect_timeout', 180);
		ini_set('max_execution_time', 180);  
		ini_set('default_socket_timeout', 180); 
	$mk = mktime (0,0,0,10,1,2015);
	$this->db->select('et_id');
	$this->db->where('mkdt <', $mk );
	//$this->db->where('paid !=','0.0');
	$this->db->where('paidtime !=','');
	$this->db->where('notpaid',0);
	$this->db->where('refunded',0);
	$this->db->where('mark',0);
	//$this->db->where('notes !=','');
	$this->db->order_by('et_id', 'ASC');
	$q = $this->db->get('ebay_transactions');
	printcool ($q->num_rows());
	$qn = 0;
	if ($q->num_rows() > 0)
	{
		foreach($q->result_array() as $k => $v)
		{
			$this->SetShipped($v['et_id'], 1);	
			echo $v['et_id'].'<Br>';
		}
		
	}
}
function Ghostoldtrans()
{
	$mk = mktime (0,0,0,10,1,2015);
	$this->db->select('et_id,e_id, rec,datetime,itemid,paid,paidtime,notpaid,refunded, pendingpay, customcode, sellingstatus,paydata, qty, asc, ssc,	mark, revs, 	cascupd, notes,transid');
	$this->db->where('mkdt <', $mk );
	//$this->db->where('paid !=','0.0');
	$this->db->where('paidtime !=','');
	$this->db->where('notpaid',0);
	$this->db->where('refunded',0);
	//$this->db->where('notes !=','');
	$this->db->order_by('et_id', 'ASC');
	$q = $this->db->get('ebay_transactions');
	printcool ($q->num_rows());
	$qn = 0;
	if ($q->num_rows() > 0)
	{
		//printcool ($q->result_array());
		//exit();
		foreach ($q->result_array() as $r)
		{
			if (strlen($r['paydata']) > 10) $r['paydata'] = unserialize($r['paydata']);	
			if(isset($r['paydata']['CheckoutStatus']) && $r['paydata']['CheckoutStatus'] == 'CheckoutIncomplete')
			{
				$r['stat'] = 'notpaid';
				//$this->db->update('ebay_transactions', array('notpaid' => 1), array('et_id' => $r['et_id']));				
			}
			elseif(isset($r['paydata']['CheckoutStatus']) && $r['paydata']['CheckoutStatus'] == 'CheckoutComplete')
			{ 
				$r['stat'] = 'paid';
				//$this->db->update('ebay_transactions', array('refunded' => 1), array('et_id' => $r['et_id']));	
				//if ($r['transid'] > 0 && $r['itemid'] > 0) //{ $this->_UpdateCurrentTransaction($r['transid'], $r['itemid']); echo 'STOP'; exit(); }
			}
			else 
			{
				$r['stat'] = 'paid';
				//$this->db->update('ebay_transactions', array('notpaid' => 1), array('et_id' => $r['et_id']));	
			}
			//if ($r['stat'] != 'paid') printcool ($r);
			$process[$r['et_id']]['qty'] = $r['qty'];			
			if ($r['e_id'] > 0)
			{				
				$this->db->select('e_title');
				$this->db->where('e_id', $r['e_id']);
				$q = $this->db->get('ebay');
				if ($q->num_rows() > 0)
				{
					$title = $q->row_array();
					$process[$r['et_id']]['title'] = $title['e_title'];
					$process[$r['et_id']]['e_id'] = $r['e_id'];
				}
			}
			$qn = $qn+$r['qty'];
		}
	}
	
	
	$this->db->select("bcn");
		$this->db->where('waid' , 0);
		$this->db->where('generic' , 1);
		$this->db->where('bcn_p1' , "G");
		//$this->db->order_by("bcn_p2", "DESC");
		$this->db->order_by("wid", "DESC");
		$w = $this->db->get('warehouse', 1);
		if ($w->num_rows() > 0)
		{			
			$next = $w->row_array();
			$next = (int)str_replace('G', '', trim($next['bcn']));		
		}
			
		printcool($next);
		
		foreach ($process as $k => $v)
		{	
			$idarray[] = $k;
		}
		if (isset($idarray))
			{				
				$this->load->model('Myseller_model'); 	
				$base = $this->Myseller_model->getSales($idarray, 1, TRUE);
				unset($idarray);
			}
		
		//printcool ($base);
		//exit(); 
		$created = 0;
		foreach ($process as $k => $v)
		{	
		if (isset($base[1][$k]))
		{
			//printcool ($v);
			//printcool ($v['qty'].' - Base: '.count($base[1][$k]));
			//if ($v['qty'] != count($base[1][$k]))
		}
		else
		{
		$start = 1;
		$amount = $v['qty'];
		while ($start <= $amount)
						{
							
							$next++;
							
							/*$this->db->where('bcn', "G".$next);
							$this->db->or_where('lot', "G".$next);
							$this->db->or_where('oldbcn', "G".$next);
							$q = $this->db->get('warehouse');
							if ($q->num_rows() > 0)
							{
								$next++;
							}*/
							$array['waid'] = 0;
							$array['bcn'] = "G".$next;
							$array['bcn_p1'] = "G";
							$array['bcn_p2'] = $next;
							if (isset($v['e_id']))
							{
								$array['listingid'] = $v['e_id'];
								$array['title'] = $v['title'];
							}
							$array['sold_id'] = $k;
							$array['status'] = 'On Hold';															
							$array['channel'] = 1;	
							$array['vended'] = 2;							
							$array['generic'] = 1;
							$array['history'] = 1;							
							$array['adminid'] = 0; 
							//$this->db->insert('warehouse', $array);
							$start++;
							$created++;
							printcool ($array);
						}
		}
		}
	printcool ($qn);
	printcool ($created);
	//printcool ($process);
}
function PreGhostoldtrans()
{
	//before oct 2015
	$mk = mktime (0,0,0,10,1,2015);
	$this->db->select('et_id,rec,datetime,itemid,paid,paidtime,notpaid,refunded, pendingpay, customcode, sellingstatus,paydata, qty, asc, ssc,	mark, revs, 	cascupd, notes,transid');
	$this->db->where('mkdt <', $mk );
	$this->db->where('paid','0.0');
	//$this->db->where('paidtime','');
	$this->db->where('notpaid',0);
	$this->db->where('refunded',0);
	$this->db->order_by('et_id', 'ASC');
	$q = $this->db->get('ebay_transactions');
	printcool ($q->num_rows());
	if ($q->num_rows() > 0)
	{
		foreach ($q->result_array() as $r)
		{
			
			if (strlen($r['paydata']) > 10) $r['paydata'] = unserialize($r['paydata']);	
			if(isset($r['paydata']['CheckoutStatus']) && $r['paydata']['CheckoutStatus'] == 'CheckoutIncomplete')
			{
				$r['stat'] = 'notpaid';
				//$this->db->update('ebay_transactions', array('notpaid' => 1), array('et_id' => $r['et_id']));				
			}
			elseif(isset($r['paydata']['CheckoutStatus']) && $r['paydata']['CheckoutStatus'] == 'CheckoutComplete')
			{ 
				$r['stat'] = 'refunded';
				//$this->db->update('ebay_transactions', array('refunded' => 1), array('et_id' => $r['et_id']));	
				//if ($r['transid'] > 0 && $r['itemid'] > 0) //{ $this->_UpdateCurrentTransaction($r['transid'], $r['itemid']); echo 'STOP'; exit(); }
			}
			else 
			{
				$r['stat'] = 'notpaid';
				//$this->db->update('ebay_transactions', array('notpaid' => 1), array('et_id' => $r['et_id']));	
			}
			printcool ($r);
			
		}
	}
	
}
function _logaction($location = '', $type = 'M', $datafrom = '', $datato = '', $eid = '', $itemid = '', $transid = '')
{

		foreach ($datato as $k => $v)
		{
			if ($v != $datafrom[$k])
			{
				if (isset($this->session->userdata['ownnames'])) $admin = $this->session->userdata['ownnames'];
				else $admin = 'Cron';
				
					
					$hmsg = array ('msg_title' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_body' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_date' => CurrentTime());
					
					//GoMail($hmsg, $this->config->config['support_email'], $this->config->config['no_reply_email']);
				
				if ($k == 'Sold') $type = 'Q';
				$this->db->insert('ebay_actionlog', array('atype' => $type, 'e_id' => (int)$eid, 'ebay_id' => (int)$itemid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom[$k], 'datato' => $v, 'field' => $k, 'admin' => $admin, 'trans_id' => (int)$transid, 'ctrl' => $location)); 			
			}
		}
}
function _UpdateCurrentTransaction($transid = 0, $itemid = 0)
{

		set_time_limit(120);
		ini_set('mysql.connect_timeout', 120);
		ini_set('max_execution_time', 120);  
		ini_set('default_socket_timeout', 120); 
		
		require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetSellingManagerSaleRecord';
		$compatabilityLevel = 959;
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		

				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetSellingManagerSaleRecordRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
				$requestXmlBody .= "<ItemID>$itemid</ItemID>";
				$requestXmlBody .= "<TransactionID>$transid</TransactionID>";
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				$requestXmlBody .=  '</GetSellingManagerSaleRecordRequest>';			

				//send the request and get response
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				$xml = simplexml_load_string($responseXml);
				$item = $xml->SellingManagerSoldOrder;
				printcool ($xml);
				printcool ($item);
				/*if ($item)
				{
					$asc = (string)$item->ActualShippingCost;
					echo 'Actual Shipping Cost: '.$asc;
				$asc = (string)$item->ActualShippingCost;
				if ((float)$asc != (float)$t['asc'])
				{
					
					$this->db->update('ebay_transactions', array('asc' => (float)$asc, 'cascupd' => 2), array('et_id' => $t['et_id']));
					$this->_logaction('Transactions', 'B', array('ActShipCost' => $t['asc']), array('ActShipCost' =>(float)$asc), 0, $t['itemid'], $t['rec']);		
				}				 

			 $ar = $this->_XML2Array($item->OrderStatus);
			 $ar = $ar['OrderStatus'];
			if (isset($ar['PaidTime'])) echo '<br>Paid Time: '.CleanBadDate((string)$ar['PaidTime']);
			 if (isset($ar['PaidTime']) && (CleanBadDate((string)$ar['PaidTime']) != $t['paidtime']) && (CleanBadDate((string)$ar['PaidTime']) != ''))
				{
					
					$this->db->update('ebay_transactions', array('paidtime' => CleanBadDate((string)$ar['PaidTime'])), array('et_id' => $t['et_id']));
					$this->_logaction('Transactions', 'B', array('PaidTime' => $t['paidtime']), array('PaidTime' => CleanBadDate((string)$ar['PaidTime'])), 0, $t['itemid'], $t['rec']);		
				}	
			 unset($ar['paidtime']);
			 $pd = serialize($ar);
			  if ($item && ($pd != $t['paydata']))
				{					
					$this->db->update('ebay_transactions', array('paydata' => $pd), array('et_id' => $t['et_id']));					
				}
				}
				*/      
}

function TryGetEbayListing()
{
	// WHEN ALL DONE, RERUN FOR EID 0 only to get autoid1 without matcheid.
	
		$this->db->select('et_id, itemid, autoid, autotitle');
		$this->db->where('e_id', 0);
		//$this->db->where('et_id >', 24519);
		$this->db->where('autoid', 0);
		//$this->db->where('autotitle', '');
		$this->db->order_by('et_id', 'ASC');
		$this->db->limit(30);
		$q = $this->db->get('ebay_transactions');
		printcool ($q->num_rows());
		if ($q->num_rows() > 0)
		{
			//printcool ($q->result_array());
		//exit();
			//printcool ($q->num_rows());
			require_once($this->config->config['ebaypath'].'get-common/keys.php');
			require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
			/*echo '
			<html>
<head>
<script type="text/JavaScript">
function timeRefresh(timeoutPeriod) 
{
	setTimeout("location.reload(true);",timeoutPeriod);
}
</script>
</head>

<body onLoad="JavaScript:timeRefresh(180000);">

			';*/
			
			
			$verb = 'GetApiAccessRules';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);




				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetApiAccessRulesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				$requestXmlBody .=  '</GetApiAccessRulesRequest>';			

				//send the request and get response
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				$xml = simplexml_load_string($responseXml);
				$data = $this->_XML2Array($xml);

printcool ($data['ApiAccessRule']['DailyUsage']);
if ($data['ApiAccessRule']['DailyUsage'] > 4500) exit();

			foreach ($q->result_array() as $k => $v)
			{
			echo $k.' - '.$v['et_id'].'<br>';
				
				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
				$requestXmlBody .= '<ItemID>'.(int)$v['itemid'].'</ItemID></GetItemRequest>';
				$verb = 'GetItem';
				$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
				$xml = simplexml_load_string($responseXml);
				if ((string)$xml->Item->ItemID == '') { echo 'ERROR: Invalid Item ID...<br>'; }
				
				$d = (string)$xml->Item->Description;
				$fid = explode('thumb_main_', $d);
				if (isset($fid[1])) 
				{
					$fid = explode('_', $fid[1]);
					$fid = $fid[0];	
				} else $fid = 0;
				
				if(isset($xml->Item->PictureDetails->ExternalPictureURL)) 
				{ 
					$autoid = explode('ebay_images/', (string)$xml->Item->PictureDetails->ExternalPictureURL);
					$autoid = $autoid[1];
					$autoid = explode('_', $autoid);
					$autoid = (int)$autoid[1];
					$this->db->update('ebay_transactions', array('e_id' => $autoid, 'autoid' => 1, 'autotitle' => (string)$xml->Item->Title), array('et_id' => $v['et_id']));
					echo $v['et_id'].' -> '.$autoid.'<br>';
				}
				elseif ((int)$fid > 0)
				{
					$this->db->update('ebay_transactions', array('e_id' => $fid, 'autoid' => 2, 'autotitle' => (string)$xml->Item->Title), array('et_id' => $v['et_id']));
					echo $v['et_id'].' => '.$fid.'<br>';
				}
				elseif (isset($xml->Item->Title)) { $this->db->update('ebay_transactions', array('autoid' => 3, 'autotitle' => (string)$xml->Item->Title), array('et_id' => $v['et_id']));
				 echo (string)$xml->Item->Title.'<br>';}
				else 
				{
					$this->db->update('ebay_transactions', array('autoid' => 4), array('et_id' => $v['et_id']));
					
					echo 'Passed - '.$v['et_id'].'<br>';
				}
		
		
			}
			
		}	
}
function AutoIDed()
{			
				
	$this->db->select("distinct l.*, et_id, itemid, autotitle", false);
		$this->db->where('t.autoid > ', 0);
		$this->db->order_by("t.et_id", "DESC");
		$this->db->join('ebay_transactions t', 'l.e_id = t.e_id', 'LEFT');
		$q = $this->db->get('ebay l');
		if ($q->num_rows() > 0)
		{	
		echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/warehouse.js"></script>
</head><body>
<table cellpadding="1" cellspacing="1" border="1"><tr><th>Tr. ID</th><th>Item ID</th><th>Listing ID</th><th>Admin Title</th><th>eBay Title</th></tr>';
			foreach ($q->result_array() as $v)
			{
				if (trim($v['e_title']) == trim($v['autotitle'])) $c = 'green';
				else $c = 'red';
				echo '<tr>
				<td><a href="'.Site_url().'Myebay/Showorder/'.$v['et_id'].'/1" target="_blank" style=" color:#0099FF;">'.$v['et_id'].'</a></td>
				<td><a href="http://www.ebay.com/itm/'.$v['itemid'].'" target="_blank">'.$v['itemid'].'</a></td>
				<td><a href="'.Site_url().'Myebay/Search/'.$v['e_id'].'" target="_blank" style=" color:#0099FF;">'.$v['e_id'].'</td>
				<td>'.$v['e_title'].'</td>
				<td style="color:'.$c.';">'.$v['autotitle'].'</td>
				</tr>';				
			}
			
		}
		
		$this->db->select("et_id, itemid, autotitle, autoid", false);
		$this->db->where('autoid > ', 0);
		$this->db->where('e_id', 0);
		$this->db->order_by("et_id", "DESC");		
		$q = $this->db->get('ebay_transactions');
		if ($q->num_rows() > 0)
		{	
		echo '<table cellpadding="1" cellspacing="1" border="1"><tr><th>Tr. ID</th><th>Item ID</th><th>eBay Title</th></tr>';
			foreach ($q->result_array() as $k => $v)
			{
				
				echo '<tr>
				<td><a href="'.Site_url().'Myebay/Showorder/'.$v['et_id'].'/1" target="_blank" style=" color:#0099FF;">'.$v['et_id'].'</a></td>
				<td><a href="http://www.ebay.com/itm/'.$v['itemid'].'" target="_blank">'.$v['itemid'].'</a></td>				
				<td>'.$v['autotitle'].'</td>
				<td><input type="text" style="width:60px; height:15px; border:1px solid" id="eid'.$k.'" onChange="savelistingid('.$v['et_id'].', \'eid'.$k.'\')" /></td>
				</tr>';	
				
				if ($v['autotitle'] != '')
				{
				$this->db->select('e_title, e_id');
				$this->db->like('e_title', $v['autotitle']);
				$t = $this->db->get('ebay');
				if ($t->num_rows() > 0)
				{
					foreach ($t->result_array() as $r)
					{
					echo '<tr>
					<td>&nbsp;</td>
					<td><a href="'.Site_url().'Myebay/Search/'.$r['e_id'].'" target="_blank" style=" color:#0099FF;">'.$r['e_id'].'</a></a></td>				
					<td>'.$r['e_title'].'</td>
					</tr>';	
					
					
					//REMOVE IF TO GET PARTIAL MATCHES
					
					//if (trim($r['e_title']) == trim($v['autotitle'])) $this->db->update('ebay_transactions', array('e_id' => $r['e_id']), array('et_id' => $v['et_id']));
					}	
					echo '<tr><td colspan="4"></td></tr>';	
				}
				}			
			}
			
		}
	
}
function findfile()
{
	$this->load->helper('directory');
	$map = directory_map('./ebay_images/', 1);
	printcool ($map);
	
	
}
function SaleListingIdToGhost()
{exit();
		$this->db->select("wid, bcn, listingid, sold_id, sold_subid, channel, title");
		$this->db->where('waid' , 0);
		$this->db->where('deleted', 0);
		$this->db->where('nr', 0);
		$this->db->where('listingid', 0);
		$this->db->where('sold_id !=', 0);		
		$this->db->order_by("wid", "DESC");
		$w = $this->db->get('warehouse');		
		if ($w->num_rows() > 0)
		{$start = 1;
			foreach ($w->result_array() as $g)
			{
				if ($g['channel'] == 1)
				{
					$this->db->select('et_id, e_id');
					$this->db->where('et_id', $g['sold_id']);
					$l = $this->db->get('ebay_transactions');
					if ($l->num_rows() > 0)
					{
						
						$listingid = $l->row_array();
						printcool ($listingid);						
						$listingid = $listingid['e_id'];						
						printcool ($g);	
						printcool ((int)$listingid);
						if ((int)$listingid > 0)
						{
							//$this->db->update('warehouse', array('listingid' => (int)$listingid), array('wid' => $g['wid']));
							//$this->Auth_model->wlog($g['bcn'], $g['wid'], 'listingid', '0', $listingid);	
						$start++;
						}
					}
				}
			}
			printcool ($start);
		}		
}
function fixemptyasc()
{
	
	$this->db->select('et_id, asc');
	$this->db->where('asc', '');
	$this->db->order_by('et_id', 'DESC');
	$l = $this->db->get('ebay_transactions');
	if ($l->num_rows() > 0)
	{
			foreach($l->result_array() as $r)
			{
				printcool ($r);
				//$this->db->update('ebay_transactions', array('asc' => '0.0'), array('et_id' => $r['et_id']));
			}
	}
}
function RefreshActualShippingLive()
{	
	$this->db->select('et_id, rec, itemid, transid, asc');
	$this->db->where('asc', 0);
	$this->db->where('transid >', 0);
	$this->db->where('et_id <', 16050);
	$this->db->limit(2500);
	$this->db->order_by('et_id', 'DESC');
	$l = $this->db->get('ebay_transactions');
	if ($l->num_rows() > 0)
	{
		printcool ($l->num_rows());
		
		//printcool ($l->result_array());
		
		set_time_limit(300);
				ini_set('mysql.connect_timeout', 300);
				ini_set('max_execution_time', 300);  
				ini_set('default_socket_timeout', 300); 
				
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetApiAccessRules';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);




				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetApiAccessRulesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				$requestXmlBody .=  '</GetApiAccessRulesRequest>';			

				//send the request and get response
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				$xml = simplexml_load_string($responseXml);
				$data = $this->_XML2Array($xml);

printcool ($data['ApiAccessRule']['DailyUsage']);

exit();
	
		
		$verb = 'GetSellingManagerSaleRecord';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
		foreach($l->result_array() as $t)
			 {
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetSellingManagerSaleRecordRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
				$requestXmlBody .= "<ItemID>".$t['itemid']."</ItemID>";
				$requestXmlBody .= "<TransactionID>".$t['transid']."</TransactionID>";
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				$requestXmlBody .=  '</GetSellingManagerSaleRecordRequest>';			

				//send the request and get response
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				$xml = simplexml_load_string($responseXml);
				$item = $xml->SellingManagerSoldOrder;
				$asc = (string)$item->ActualShippingCost;
				
				if ((float)$t['asc'] != (float)$asc)
				{
					printcool ($t['et_id']);
					
					printcool ((float)$t['asc']);
					printcool ((float)$asc);
					$this->db->update('ebay_transactions', array('asc' => (float)$asc), array('et_id' => $t['et_id']));
				}
			 }
			
	}	
}
function RevertNonGenericRenamedEmptyFields()
{
		$this->db->where('ctrl', 'GhostsNonGenericRenamedExists');
		$this->db->order_by("wl_id", "DESC");
		$wl = $this->db->get('warehouse_log');
		if ($wl->num_rows() > 0) 
		{
			echo '<table border=1>';
			foreach ($wl->result_array() as $v)
			{
				if ($v['datafrom'] != '' && $v['datato'] == '')
				{echo '<tr>';
					
				foreach ($v as $kk=>$vv)
				{
				//	echo '<td>'.$vv.'</td>';
					
				}
				$this->db->update('warehouse', array($v['field'] => $v['datafrom']), array('wid' => $v['wid']));
				$this->Auth_model->wlog('', $v['wid'], $v['field'], $v['datato'], $v['datafrom']);	
				echo '</tr>';
				}
			}
		}	
}
function PopulateBCNInWLog()
{
	
	$this->load->model('Mywarehouse_model'); 
	
	$this->db->select('wl_id, wid,bcn');
		$this->db->where('ctrl', 'RevertNonGenericRenamedEmptyFields');
		$this->db->order_by("wl_id", "DESC");
		$wl = $this->db->get('warehouse_log');
		if ($wl->num_rows() > 0) 
		{
			echo '<table border=1>';
			foreach ($wl->result_array() as $v)
			{
				$v['bcn'] = $this->Mywarehouse_model->wid2bcn($v['wid']);
				$this->db->update('warehouse_log', array('bcn' => $v['bcn']), array('wl_id' => $v['wl_id']));
				printcool ($v);
			}
		}
}
function findcancelled()
{
	
//MAKEDEBUGFUCTION FOR ORDERS , SEE THERE
		set_time_limit(60);
		ini_set('mysql.connect_timeout', 60);
		ini_set('max_execution_time', 60);  
		ini_set('default_socket_timeout', 60); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
	
		
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$compat = 887;
		$requestXmlBody .= "<Version>$compat</Version><NumberOfDays>1</NumberOfDays>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		//$dates = array('from' => date('Y-m-d H:i:s', strtotime("-2 Hours")), 'to' => date("Y-m-d H:i:s"));
		//<ModTimeFrom>'.$dates['from'].'</ModTimeFrom>
 		//<ModTimeTo>'.$dates['to'].'</ModTimeTo>  
		
			
		//<IncludeCodiceFiscale>'.TRUE.'</IncludeCodiceFiscale>		
		//<IncludeContainingOrder>'.TRUE.'</IncludeContainingOrder> 
		
		$requestXmlBody .= '
	
		 <IncludeFinalValueFee>'.TRUE.'</IncludeFinalValueFee>
			<NumberOfDays>10</NumberOfDays>	
		<Pagination>
		<EntriesPerPage>200</EntriesPerPage>
		</Pagination>
		</GetSellerTransactionsRequest>';	
		$verb = 'GetSellerTransactions';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compat, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$this->load->helper('directory');
		$this->load->helper('file');
		$xml = simplexml_load_string($responseXml);
		printcool ($xml);
	
	
	
}
function SoldQnToEmtpy()
{
		$this->load->model('Mywarehouse_model'); 
	
		$this->db->select('wid, soldqn');
		$this->db->where('soldqn', '0');
		$wl = $this->db->get('warehouse');
		if ($wl->num_rows() > 0) 
		{
			foreach ($wl->result_array() as $v)
			{
				$this->db->update('warehouse', array('soldqn' => NULL), array('wid' => $v['wid']));
				//printcool ($v);
			}
		}
}
function CronLog()
{		
		$this->db->order_by('ec_id', 'DESC');
		$wl = $this->db->get('ebay_cron');
		if ($wl->num_rows() > 0) 
		{
		$this->mysmarty->assign('cron', $wl->result_array());	
		$this->mysmarty->view('mywarehouse/cron_log.html');		
		}
		else echo 'Nothing here';
}
function NeedsRelisting()
{
	
	$query = 'SELECT e_id FROM ebay WHERE ebended IS NOT NULL AND quantity > 0';
	$q = $this->db->query($query);
		
	if ($q->num_rows() > 0)
		{$this->load->model('Myseller_model');
			printcool($q->num_rows());
			foreach($q->result_array() as $e)
			{
				$this->Myseller_model->getWareHouseCount((int)$e['e_id']);	
				
			}
		}
	
}
function  cleanup()
{
	
	
	
	
	$array = '105-354-7
105-354-6
105-354-5
105-354-4
105-354-3
105-353-2
105-353-1
105-354-2
105-354-1'; 
$array = explode('
', $array);
//printcool ($array);
	$this->db->select('wid,bcn, deleted');
	$c = 1;
	foreach ($array as $a)
	{
		if ($c == 1) $this->db->where('bcn', trim($a));
		else $this->db->or_where('bcn', trim($a));	
		$c++;
	}
	$d = $this->db->get('warehouse');
	printcool ($d);
	foreach ($d->result_array() as $w)
	{
		printcool ($w);
		//$this->db->update('warehouse', array('deleted'=> 1), array('wid' => (int)$w['wid']));	
	}
		
	
}

function fixghost()
{
	
	
$sql = 'SELECT e_id FROM ebay WHERE `ebay_id` != 0 AND `ebended` IS NULL'; 
		$q = $this->db->query($sql);
		$sql2 = '';
		$this->mysmarty->assign('linked', $q->num_rows());
		foreach ($q->result_array() as $e)
		{
			$listingid = $e['e_id'];
			$ch['quantity'] = 0;
			$ch['ngen'] = 0;
			$ch['e_qpart'] = 0;
			$sql = 'SELECT wid, waid, generic FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND `status` = "Listed" AND `listingid` = '.$listingid;
			$cc =  $this->db->query($sql);
			if ($cc->num_rows() > 0)
			{
				foreach ($cc->result_array() as $c)
				{
					$ch['quantity']++;
					$ch['e_qpart']++;	
					if ($c['generic'] != 0) $ch['ngen']++;		
				}
			}
			//printcool ($ch);
			$this->db->update('ebay', $ch, array('e_id' => (int)$listingid));
		}//
}
function fixxqn()
{
	
	
$sql = 'SELECT e_id FROM ebay WHERE `ebay_id` != 0 AND `ebended` IS NULL'; 
		$q = $this->db->query($sql);
		$sql2 = '';
		$this->mysmarty->assign('linked', $q->num_rows());
		foreach ($q->result_array() as $e)
		{
			$listingid = $e['e_id'];
			$ch['xquantity'] = 0;			
			$sql = 'SELECT wid FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND `listingid` = '.$listingid;
			$cc =  $this->db->query($sql);
			if ($cc->num_rows() > 0)
			{
				foreach ($cc->result_array() as $c)
				{
					$ch['xquantity']++;
				}
			}
			//printcool ($ch);
			$this->db->update('ebay', $ch, array('e_id' => (int)$listingid));
		}//
}

function auditmk()
{
	$sql = 'SELECT e_id, audit FROM ebay WHERE `audit` IS NOT NULL'; 
		$q = $this->db->query($sql);
		foreach ($q->result_array() as $e)
		{
			printcool ($e['audit']);
			$date = explode (' ',$e['audit']);
			$time = explode (':', $date[1]);
			$date = explode ('-',$date[0]);
			//printcool ($time);
			//printcool ($date);
			$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
			printcool ($mk);
			
			//$this->db->update('ebay', array('auditmk' => (int)$mk), array('e_id' => (int)$e['e_id']));
		}//
}

function auditwmk()
{
	$sql = 'SELECT wid, audit FROM warehouse WHERE `audit` IS NOT NULL'; 
		$q = $this->db->query($sql);
		foreach ($q->result_array() as $e)
		{
			//printcool ($e['audit']);
			$date = explode (' ',$e['audit']);
			$time = explode (':', $date[1]);
			$date = explode ('-',$date[0]);
			//printcool ($time);
			//printcool ($date);
			$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
			//printcool ($mk, false, $e['wid']);
			
			$this->db->update('warehouse', array('auditmk' => (int)$mk), array('wid' => (int)$e['wid']));
		}//
}

function soldminusqn()
{
	$sql = 'SELECT wid, soldqn FROM warehouse WHERE `soldqn` = "-1"'; 
		$q = $this->db->query($sql);
		foreach ($q->result_array() as $e)
		{
			printcool ($e);
			
			//$this->db->update('warehouse', array('soldqn' => 1), array('wid' => (int)$e['wid']));
		}//
}

function fixlistingbcnnotlistedstatus()
{
	
	$sql = 'SELECT distinct w.wid, w.status, e.e_id, e.ebay_id, e.ebended FROM (warehouse w) LEFT JOIN ebay e ON w.listingid = e.e_id WHERE `e`.`ebended` IS NOT NULL AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0 AND `w`.`status` != "Not Listed"'; 
	
	//$sql = 'SELECT e_id FROM ebay WHERE `ebended` IS NOT NULL'; 
		$q = $this->db->query($sql);
		printcool ($q->result_array());	
		foreach($q->result_array() as $w)
		{
			//$this->db->update('warehouse', array('status' => 'Not Listed'), array('wid' => (int)$w['wid']));
		}
}
function fixnotlisted()
{
	
		$query = $this->db->query('SELECT distinct w.wid, w.status, e.e_id, e.ebended, e.qn_ch1, e.qn_ch2, e.ebayquantity FROM (warehouse w) LEFT JOIN ebay e ON w.listingid = e.e_id WHERE `w`.`listingid` != 0 AND `e`.`ebended` IS  NULL AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0 AND `w`.`status` = "Not Listed"');
		foreach ($query->result_array() as $nl)
		{
			$data[$nl['e_id']]['ebended'] = $nl['ebended'];
			$data[$nl['e_id']]['qn_ch1'] = $nl['qn_ch1'];
			$data[$nl['e_id']]['qn_ch2'] = $nl['qn_ch2'];
			$data[$nl['e_id']]['ebayquantity'] = $nl['ebayquantity'];
			$data[$nl['e_id']]['bcns'][$nl['wid']] = array('wid'=> $nl['wid'], 'status' => $nl['status']);
			$data[$nl['e_id']]['actualcount'] = count($data[$nl['e_id']]['bcns']);				
		}
		foreach ($data as $k => $d)
		{
			//unset($data[$nl['e_id']]['bcns']);		
			if ($d['qn_ch1'] != $d['qn_ch2']) $data[$k]['situation'] = 'QNCH Mismatch';

			if ($d['qn_ch1'] != $d['ebayquantity']) $data[$k]['situation'] = 'QNebQ Mismatch';
			if ($d['actualcount'] != $d['ebayquantity']) $data[$k]['situation'] = 'Act/eBQ Mismatch';
			if ($d['actualcount'] != $d['qn_ch1']) $data[$k]['situation'] = 'Act/CH Mismatch';
			if (isset($data[$k]['situation']))
			{
				$cb = $d['ebayquantity'];
				$c = 0;
				krsort($d['bcns']);
				foreach($d['bcns'] as $b)
				{		
					$c++;
					if ($c >= $cb)
					{			
						//printcool ($b, false, $c.' - '.$cb.' - '.$k);
					}
					else
					{
						//$this->db->update('warehouse', array('Status' => 'Listed'), array('wid' => $b['wid']));
					}
					
				}
			}
		}
		
		printcool ($data);
			
}

function fixallcount()
{
	$this->load->model('Myseller_model'); 
		$query = $this->db->query('SELECT e_id FROM (ebay) WHERE `ebended` IS  NULL AND `ebay_id` != 0');
		foreach ($query->result_array() as $nl)
		{
			$this->Myseller_model->ProcessFinalCounts($nl['e_id']);
		}
}
function updatesetshippedmk()
{
	$sql = 'SELECT wid, sold_id, shipped_date, paid_date, dates, channel FROM warehouse WHERE `deleted` = 0  AND `nr` = 0 AND `status` = "Sold" AND `setshipped` = 0'; 
	$q = $this->db->query($sql);
		//printcool ($q->result_array());	
		foreach($q->result_array() as $w)
		{
			$shippedmk = 0;
			if ($w['shipped_date'] != '' && strlen($w['shipped_date'] >= 18))
			{ 
				$shippedmk = $w['shipped_date'];
				$date = explode (' ',$shippedmk);
				$time = explode (':', $date[1]);
				$date = explode ('-',$date[0]);
				$shippedmk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
			}
			else 
			{
				$dt = unserialize($w['dates']);
				$shippedmk = (int)$dt[0]['createdstamp'];
			}
			if ($shippedmk == 0)
			{
				if ($w['channel'] == 2)
				{
					$sql = 'SELECT submittime FROM orders WHERE oid = '.$w['sold_id'];
					$q = $this->db->query($sql);
					$q = $q->row_array();
					$shippedmk = (int)$q['submittime'];
				} 
				elseif ($w['channel'] == 4)
				{
					$sql = 'SELECT timemk FROM warehouse_orders WHERE woid = '.$w['sold_id'];
					$q = $this->db->query($sql);
					$q = $q->row_array();
					$shippedmk = (int)$q['timemk'];
				}
				else
				{
					$sql = 'SELECT mkdt FROM ebay_transactions WHERE et_id = '.$w['sold_id'];
					$q = $this->db->query($sql);
					$q = $q->row_array();
					$shippedmk = (int)$q['mkdt'];
				}				
			}
			printcool ($shippedmk);
			//$this->db->update('warehouse', array('setshipped' => $shippedmk), array('wid' => (int)$w['wid']));
		}
	
}
function fixonholds()
{
	$msql = 'SELECT distinct w.*, e.et_id, e.mark, e.paid, e.paidtime, e.paydata FROM (warehouse w) LEFT JOIN ebay_transactions e ON e.et_id = w.sold_id WHERE `e`.`mark` = 1 AND `e`.`notpaid` = 0 AND `e`.`refunded` = 0 AND  `w`.`status` = "On Hold" AND `w`.`deleted` = 0'; 
	$q = $this->db->query($msql);		
	if ($q->num_rows() > 0)
	{echo ($q->num_rows());
		foreach ($q->result_array() as $v)
		{	
			printcool ($v);
			//$this->SetShipped($v['et_id'], 1);	
		}
	}
}
function fixnikipaste()
{
	$sql = 'SELECT * FROM warehouse_log WHERE (bcn = "036-1785" OR bcn = "036-1784" OR bcn = "036-1783" OR bcn = "036-1782" OR bcn = "036-1781" OR bcn = "036-1780" OR bcn = "036-1779" OR bcn = "036-1778" OR bcn = "036-1777" OR bcn = "036-1776" OR bcn = "036-1775" OR bcn = "036-1774" OR bcn = "036-1773") AND admin = 2 AND datato = "http://www.la-tronics.com/Mywarehouse/Accounting/248"';
	$q = $this->db->query($sql);		
	if ($q->num_rows() > 0)
	{echo ($q->num_rows());
		foreach ($q->result_array() as $v)
		{	
			$this->db->update('warehouse', array($v['field'] => $v['datafrom']), array('wid' => $v['wid']));
			echo 'array('.$v['field'].' => '.$v['datafrom'].'), array('.wid.' =>, '.$v['wid'].'));<br>';
			printcool ($v);			
		}
	}
}
function revlogsev()
{
	
		$this->db->order_by("erlid", "DESC");
		$this->db->where('sev !=', 0);

		$q = $this->db->get('ebay_revise_log');				
		foreach ($q->result_array() as $r)
		{
			$time = $r['attime'];

			//printcool (mktime());
			
			if ($r['erlid'] < 2590)
			{
				printcool ($time, false, 'OLD');
				$date = explode (' - ',$time);
				$time = explode (':', $date[0]);
				$date = explode ('/',$date[1]);
				//printcool ($date);
				//printcool ($time);
				$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[0],$date[2]);	
			}
			else
			{
				//printcool ($time);
				$date = explode (' ',$time);
				$time = explode (':', $date[1]);
				$date = explode ('-',$date[0]);
				$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);	
			}
			printcool ($mk);
			$this->db->update('ebay_revise_log', array('atmk' => $mk), array('erlid' => $r['erlid']));
		}
}
function debugGetMyeBaySelling()
{

	require($this->config->config['ebaypath'].'get-common/keys.php');
	require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

	set_time_limit(300);
				ini_set('mysql.connect_timeout', 300);
				ini_set('max_execution_time', 300);  
				ini_set('default_socket_timeout', 300);
	//$this->DoRevise();
	//sleep(20);
	
	$compatabilityLevel = 959;
	
	

						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						//$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
						$days = 60; //maxtime	
									
						$requestXmlBody .= " <ActiveList>
    <Include>TRUE</Include>
	<Pagination>
<EntriesPerPage>200</EntriesPerPage>
<PageNumber>1</PageNumber>
</Pagination>
  </ActiveList> 
  <HideVariations>FALSE</HideVariations> 
  <SellingSummary>
    <Include>TRUE</Include>
  </SellingSummary> 
  </GetMyeBaySellingRequest>";
						$verb = 'GetMyeBaySelling';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);
						
						$pages = (int)$xml->ActiveList->PaginationResult->TotalNumberOfPages;
						$entries = (int)$xml->ActiveList->PaginationResult->TotalNumberOfEntries;
						
						$list['active'][1] = $xml->ActiveList->ItemArray;						
	if ($pages > 1)
	{
		$page = 2;
		while ($page <= $pages) 
		{
			
			$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						//$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= "<ActiveList><Include>TRUE</Include><Pagination><EntriesPerPage>200</EntriesPerPage><PageNumber>".$page."</PageNumber></Pagination></ActiveList></GetMyeBaySellingRequest>";
						$verb = 'GetMyeBaySelling';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');						
						$xml = simplexml_load_string($responseXml);
						$list['active'][$page] = $xml->ActiveList->ItemArray;
						$page++;
		}								
	}
	
	
	$this->db->select("e_id, ebay_id, e_title, quantity, qn_ch1, qn_ch2, price_ch1, price_ch2, ebayquantity, ebended, sitesell");		
	$cnt = 1;
	
	foreach($list['active'] as $v)
	{		
		foreach ($v as $vv)
		{
			foreach ($vv->Item as $i)
			{
				if (!isset($done[(int)$i->ItemID])) 
				{
					if ($cnt == 1) $this->db->where("ebay_id", (int)$i->ItemID);	
					else $this->db->or_where("ebay_id", (int)$i->ItemID);	
					$cnt++;											
				}
			}
		}
	}
	
	$r = $this->db->get('ebay');		
	if ($r->num_rows() > 0)
	{ 	
		foreach ($r->result_array() as $rk => $rv)
		{
			$mitemids[(int)$rv['ebay_id']] = $rv;				
		} 	   	
	}		
	
	$ebon = $this->db->query('SELECT e_id, ebay_id FROM ebay WHERE `ebay_id` != 0 AND `ebended` IS NULL');
	if ($ebon->num_rows() > 0)
	{
		foreach ($ebon->result_array() as $ee)
		{
			$localactive[$ee['ebay_id']] = $ee['e_id']; 	
		}
	}

	$this->load->model('Myseller_model');
	$this->load->model('Auth_model');
	
	//printcool ($list['active']);		
	foreach($list['active'] as $k=>$v)
	{
		foreach ($v as $vv)
		{ 
			foreach ($vv->Item as $i)
			{	
				$itemid = (int)$i->ItemID;
				if (isset($mitemids[$itemid]))
				{
					$this->Myseller_model->ProcessFinalCounts((int)$mitemids[$itemid]['e_id']);
					//$this->db->update('ebay', array('ebended'=> NULL), array('e_id' => $mitemids[$itemid]['e_id']));
					unset($localactive[$itemid]);//printcool ($mitemids[$itemid]);
					unset($bcns);
					$bcns = $this->Myseller_model->getBase(array((int)$mitemids[$itemid]['e_id']),TRUE);
					$lq = 0;
					$lqg = array();				
					$nl = array();
					//if ($bcns) printcool ($bcns, FALSE, $mitemids[$itemid]['e_id']);
					if ($bcns) foreach($bcns as $k => $wid)
					{
						if ($wid['status'] == 'Listed' && ($wid['channel'] == 0 || $wid['channel'] == 1)) 
						{
							 $lq++;				
							 if ($wid['generic'] != 0) 
							 { 
							 	 $lqg[] = array('wid'=>$wid['wid'], 'bcn'=>$wid['bcn']); 
								 unset($bcns[$k]); 
							 }
						}
						if ($wid['status'] == 'Not Listed' && ($wid['channel'] == 0 || $wid['channel'] == 1)) 
						{
							$nl[] = array('wid'=>$wid['wid'], 'bcn'=>$wid['bcn']);
						}
						
					}	
					
					//printcool ($lq, FALSE, 'LQ '.$mitemids[$itemid]['e_id']);
					//printcool ($lqg, FALSE, 'LQ-Ghost '.$mitemids[$itemid]['e_id']);
					//printcool ($nl, FALSE, 'NL '.$mitemids[$itemid]['e_id']);
					if (count($lqg) == count($nl))
					{
						//printcool ('TOFIX');
						foreach ($lqg as $k => $v)
						{
							printcool ('DELETE GHOST '.$v['wid']);
							//$this->db->update('warehouse',array('deleted' => 5), array('wid' => (int)$v['wid']));
						}
						foreach ($nl as $k => $v)
						{
							printcool ('LIST REAL BCN '.$v['bcn'].' - '.$v['wid']);
							//$this->db->update('warehouse',array('status' => 'Listed'), array('wid' => (int)$v['wid']));
							//$this->Auth_model->wlog($v['bcn'], (int)$v['wid'], 'status', 'Not Listed', 'Listed');
						}	
					}
					
						
				}
				else printcool ('Not Active');
			}
		}
				
	}
	printcool ($localactive);
}
function movedeleted()
{

	$sql = 'SELECT * FROM warehouse WHERE deleted > 0';
	$q = $this->db->query($sql);		
	if ($q->num_rows() > 0)
	{
		echo ($q->num_rows());
		foreach ($q->result_array() as $v)
		{	
		//	$this->db->insert('warehouse_deleted', $v);
			$this->db->where('wid', $v['wid']);
			$this->db->delete('warehouse');
		}
	}
}	
function uncascupd()
{
	$this->db->select('et_id, cascupd');
	$this->db->where('et_id', 39622);
	$this->db->or_where('et_id', 39523);
	$this->db->or_where('et_id', 39443);
	$this->db->or_where('et_id', 39516);
	$this->db->or_where('et_id', 39434);
	$this->db->or_where('et_id', 39433);
	$this->db->or_where('et_id', 26473);
	$this->db->or_where('et_id', 39845);
	$this->db->or_where('et_id', 39660);
	$this->db->or_where('et_id', 39641);
	$this->db->or_where('et_id', 39701);
	$l = $this->db->get('ebay_transactions');
	if ($l->num_rows() > 0)
	{
			foreach($l->result_array() as $r)
			{
				printcool ($r);
				$this->db->update('ebay_transactions', array('cascupd' => 0), array('et_id' => $r['et_id']));
			}
	}
}


function transdate()
{
		$liste = array();



		$this->db->select("et_id, datetime, mkdt");		
		$this->db->order_by("rec", "DESC");		
		$q = $this->db->get('ebay_transactions');
		
		if ($q->num_rows() > 0) 
		{
			foreach ($q->result_array() as $k=>$v)
			{				
				$liste[$v['et_id']] = $v;				
				$idarray[] = $v['et_id'];
			
			}
			if (isset($idarray))
			{				
				$this->load->model('Myseller_model'); 	
				$e_bcn =  $this->Myseller_model->getSales($idarray, 1, TRUE);
				unset($idarray);
			}
			
		}
		
		
		
		$this->db->select('oid, time, submittime');
		$this->db->order_by("submittime", "DESC");
		
		$this->query = $this->db->get('orders');
		$orders = array();
		if ($this->query->num_rows() > 0) 
			{
			foreach ($this->query->result_array() as $k => $v)	
				{
				
		
				$orderse[$v['oid']] = $v;	
				
				$idarray[] = $v['oid'];	
			
				}
				if (isset($idarray))
				{
					$this->load->model('Myseller_model'); 	
					$o_bcn = $this->Myseller_model->getSales($idarray, 2, true);
					
					unset($idarray);
				}				
			}

	//printcool ($e_bcn);
	//printcool ($liste);
	foreach ($e_bcn[1] as $k => $v)
	{
			foreach ($v as $b)
			{
				printcool ($liste[$k]['datetime'].$liste[$k]['mkdt'], false,$b['wid'] );
				//$this->db->update('warehouse', array('trans_date' => $liste[$k]['datetime'], 'trans_mk' => $liste[$k]['mkdt']), array('wid' => $b['wid']));	
			}
	}

	foreach ($o_bcn[2] as $oid => $eids)
	{//printcool ($eids);
		foreach ($eids as $eid => $bcns)
		{ 
			foreach ($bcns as $bcn => $b)
			{ 
			//printcool ($eid, '', 'e_id'	);
			//printcool ($orderse[$oid]['mark'], '', 'MARK');
			
			printcool ($orderse[$oid]['time'].$orderse[$oid]['submittime'], false,$b['wid'] );
			//$this->db->update('warehouse', array('trans_date' => $orderse[$oid]['time'], 'trans_mk' => $orderse[$oid]['submittime']), array('wid' => $b['wid']));	
			
			}
		//printcool ('--');
		}
	}
		//printcool ($liste);
}
function OrderEids()
{
	$this->db->select('oid, order');
	$d = $this->db->get('orders');
	if ($d->num_rows() > 0)
	{
		foreach ($d->result_array() as $o)
		{
			$o['order'] = unserialize($o['order']);
			$eids = '';
			foreach ($o['order'] as $k => $v)
			{
				$eids .= '|'.trim($k).'|';	
			}
			printcool ($eids);
			$this->db->update('orders', array('eids' => $eids),array('oid' => $o['oid']));	
		}
	}
}

function PopulateLoctions()
{
	$this->db->select('wid,location');
	$this->db->where('location !=', '');
	$q = $this->db->get('warehouse');
	if ($q->num_rows() > 0)
	{
		$locations = array();	
		$bcns = $q->result_array();
		foreach ($bcns as $r)
		{
			$locations[trim(ucwords($r['location']))][] = $r['wid'];

		}		
		ksort($locations);
		//printcool ($locations);
		//exit();
		foreach ($locations as $k => $l)
		{
			$this->db->insert('locations', array('loc_name' => $k));
			$id  = $this->db->insert_id();
			foreach ($l as $vv)
			{
					$this->db->update('warehouse', array('loc_id' => $id), array('wid' => (int)$vv));		
			}
		}
		
	}
	
	
	
	
}


function decimalcleaner()
{
	
//MYSQL
/*
ALTER TABLE  `autopilot_log` CHANGE  `apl_from`  `apl_from` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `autopilot_log` CHANGE  `apl_to`  `apl_to` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';

ALTER TABLE  `autopilot_rules` CHANGE  `changevalue`  `changevalue` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `autopilot_rules` CHANGE  `rununtil`  `rununtil` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';

ALTER TABLE  `competitor_rules` CHANGE  `competitor_price`  `competitor_price` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `competitor_rules` CHANGE  `price_change_value`  `price_change_value` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';

ALTER TABLE  `competitor_rules_log` CHANGE  `cl_from`  `cl_from` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `competitor_rules_log` CHANGE  `cl_to`  `cl_to` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';

ALTER TABLE  `ebay` CHANGE  `startPrice`  `startPrice` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `ebay` CHANGE  `buyItNowPrice`  `buyItNowPrice` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `ebay` CHANGE  `price_ch1`  `price_ch1` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `ebay` CHANGE  `price_ch2`  `price_ch2` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `ebay` CHANGE  `price_ch3`  `price_ch3` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';

ALTER TABLE  `ebay_transactions` CHANGE  `eachpaid`  `eachpaid` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `ebay_transactions` CHANGE  `fee`  `fee` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `ebay_transactions` CHANGE  `paid`  `paid` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `ebay_transactions` CHANGE  `returned_extracost`  `returned_extracost` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `ebay_transactions` CHANGE  `asc`  `asc` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `ebay_transactions` CHANGE  `ssc`  `ssc` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';

ALTER TABLE  `orders` CHANGE  `endprice`  `endprice` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `orders` CHANGE  `endprice_delivery`  `endprice_delivery` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `orders` CHANGE  `returned_extracost`  `returned_extracost` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';

ALTER TABLE  `warehouse` CHANGE  `paid`  `paid` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `warehouse` CHANGE  `cost`  `cost` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `warehouse` CHANGE  `shipped`  `shipped` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `warehouse` CHANGE  `shipped_actual`  `shipped_actual` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `warehouse` CHANGE  `shipped_inbound`  `shipped_inbound` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `warehouse` CHANGE  `sellingfee`  `sellingfee` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `warehouse` CHANGE  `netprofit`  `netprofit` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `warehouse` CHANGE  `returned_extracost`  `returned_extracost` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `warehouse` CHANGE  `return_pricesold`  `return_pricesold` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `warehouse` CHANGE  `return_sellingfee`  `return_sellingfee` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `warehouse` CHANGE  `return_shoppingcost`  `return_shoppingcost` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `warehouse` CHANGE  `return_netprofit`  `return_netprofit` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';

ALTER TABLE  `warehouse_auction_expenses` CHANGE  `exp_value`  `exp_value` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
*/
	ini_set('memory_limit','1024M');
printcool ('autopilot_log');
$run = $this->db->query("SELECT 	`apl_id`, `apl_from`, `apl_to` FROM `autopilot_log`");
if ($run->num_rows() > 0)
{
	$ok = 0;
	$updt = 0;
	$key = 'apl_id';
	$dbtbl = 'autopilot_log';
	foreach ($run->result_array() as $k => $v)
	{
		foreach ($v as $kk => $vv)
		{
		if ($kk != $key)
			{
				$tmp = floater($vv);
				if ($tmp != $vv)
				{
					$updt++;
					//printcool ($vv,false,$v[$key].' Was');
					//printcool ($tmp,false,$v[$key].' Fixed');
					printcool ('update('.$dbtbl.', array('.$kk.'=>'.$tmp.'),array('.$key.' => '.$v[$key].'));	');
					//$this->db->update($dbtbl, array($kk=>$tmp),array($key => $vv[$key]));	
					$this->db->insert('decimal_log', array('tbl' => $dbtbl,'key' => $key,'keyvalue' => $v[$key],'field' => $kk,'was' => $vv,'to' => $tmp));	
				}	
				else $ok++;		
			}
		}
	}
	printcool ($ok,false,'OK');	
	printcool ($updt,false,'Updated');	
}

printcool ('autopilot_rules');
$run = $this->db->query("SELECT 	`rid`, `changevalue`, `rununtil` FROM `autopilot_rules`");
if ($run->num_rows() > 0)
{
	$ok = 0;
	$updt = 0;
	$key = 'rid';
	$dbtbl = 'autopilot_rules';
	foreach ($run->result_array() as $k => $v)
	{
		foreach ($v as $kk => $vv)
		{

		if ($kk != $key)
			{
				$tmp = floater($vv);
				if ($tmp != $vv)
				{
					$updt++;
					//printcool ($vv,false,$v[$key].' Was');
					//printcool ($tmp,false,$v[$key].' Fixed');
					printcool ('update('.$dbtbl.', array('.$kk.'=>'.$tmp.'),array('.$key.' => '.$v[$key].'));	');
					//$this->db->update($dbtbl, array($kk=>$tmp),array($key => $vv[$key]));	
					$this->db->insert('decimal_log', array('tbl' => $dbtbl,'key' => $key,'keyvalue' => $v[$key],'field' => $kk,'was' => $vv,'to' => $tmp));
				}		
				else $ok++;		
			}
		}
	}
	printcool ($ok,false,'OK');	
	printcool ($updt,false,'Updated');	
}

printcool ('competitor_rules');
$run = $this->db->query("SELECT 	`cid`, `competitor_price`, `price_change_value` FROM `competitor_rules`");	
if ($run->num_rows() > 0)
{
	$ok = 0;
	$updt = 0;
	$key = 'cid';
	$dbtbl = 'competitor_rules';
	foreach ($run->result_array() as $k => $v)
	{
		foreach ($v as $kk => $vv)
		{
		if ($kk != $key)
			{
				$tmp = floater($vv);
				if ($tmp != $vv)
				{
					$updt++;
					//printcool ($vv,false,$v[$key].' Was');
					//printcool ($tmp,false,$v[$key].' Fixed');
					printcool ('update('.$dbtbl.', array('.$kk.'=>'.$tmp.'),array('.$key.' => '.$v[$key].'));	');
					//$this->db->update($dbtbl, array($kk=>$tmp),array($key => $vv[$key]));	
					$this->db->insert('decimal_log', array('tbl' => $dbtbl,'key' => $key,'keyvalue' => $v[$key],'field' => $kk,'was' => $vv,'to' => $tmp));
				}		
				else $ok++;		
			}
		}
	}
	printcool ($ok,false,'OK');	
	printcool ($updt,false,'Updated');	
}

printcool ('competitor_rules_log');
$run = $this->db->query("SELECT 	`cl_id`, `cl_from`, `cl_to` FROM `competitor_rules_log`");
if ($run->num_rows() > 0)
{
	$ok = 0;
	$updt = 0;
	$key = 'cl_id';
	$dbtbl = 'competitor_rules_log';
	foreach ($run->result_array() as $k => $v)
	{
		foreach ($v as $kk => $vv)
		{
		if ($kk != $key)
			{
				$tmp = floater($vv);
				if ($tmp != $vv)
				{
					$updt++;
					//printcool ($vv,false,$v[$key].' Was');
					//printcool ($tmp,false,$v[$key].' Fixed');
					printcool ('update('.$dbtbl.', array('.$kk.'=>'.$tmp.'),array('.$key.' => '.$v[$key].'));	');
					//$this->db->update($dbtbl, array($kk=>$tmp),array($key => $vv[$key]));	
					$this->db->insert('decimal_log', array('tbl' => $dbtbl,'key' => $key,'keyvalue' => $v[$key],'field' => $kk,'was' => $vv,'to' => $tmp));	
				}	
				else $ok++;		
			}
		}
	}
	printcool ($ok,false,'OK');	
	printcool ($updt,false,'Updated');	
}

printcool ('ebay');
$run = $this->db->query("SELECT 	`e_id`, `startPrice`, `buyItNowPrice`, `price_ch1`, `price_ch2`,`price_ch3` FROM `ebay`	");
if ($run->num_rows() > 0)
{
	$ok = 0;
	$updt = 0;
	$key = 'e_id';
	$dbtbl = 'ebay';
	foreach ($run->result_array() as $k => $v)
	{
		foreach ($v as $kk => $vv)
		{
		if ($kk != $key)
			{
				$tmp = floater($vv);
				if ($tmp != $vv)
				{
					$updt++;
					//printcool ($vv,false,$v[$key].' Was');
					//printcool ($tmp,false,$v[$key].' Fixed');
					printcool ('update('.$dbtbl.', array('.$kk.'=>'.$tmp.'),array('.$key.' => '.$v[$key].'));	');
					//$this->db->update($dbtbl, array($kk=>$tmp),array($key => $vv[$key]));	
					$this->db->insert('decimal_log', array('tbl' => $dbtbl,'key' => $key,'keyvalue' => $v[$key],'field' => $kk,'was' => $vv,'to' => $tmp));
				}		
				else $ok++;		
			}
		}
	}
	printcool ($ok,false,'OK');	
	printcool ($updt,false,'Updated');	
}

printcool ('ebay_transactions');
$run = $this->db->query("SELECT 	`et_id`, `eachpaid`, `fee`, `paid`, `returned_extracost`,`asc`,`ssc` FROM `ebay_transactions`");	
if ($run->num_rows() > 0)
{
	$ok = 0;
	$updt = 0;
	$key = 'et_id';
	$dbtbl = 'ebay_transactions';
	foreach ($run->result_array() as $k => $v)
	{
		foreach ($v as $kk => $vv)
		{
		if ($kk != $key)
			{
				$tmp = floater($vv);
				if ($tmp != $vv)
				{
					$updt++;
					//printcool ($vv,false,$v[$key].' Was');
					//printcool ($tmp,false,$v[$key].' Fixed');
					printcool ('update('.$dbtbl.', array('.$kk.'=>'.$tmp.'),array('.$key.' => '.$v[$key].'));	');
					//$this->db->update($dbtbl, array($kk=>$tmp),array($key => $vv[$key]));	
					$this->db->insert('decimal_log', array('tbl' => $dbtbl,'key' => $key,'keyvalue' => $v[$key],'field' => $kk,'was' => $vv,'to' => $tmp));
				}		
				else $ok++;		
			}
		}
	}
	printcool ($ok,false,'OK');	
	printcool ($updt,false,'Updated');	
}

printcool ('orders');
$run = $this->db->query("SELECT 	`oid`, `endprice`, `endprice_delivery`, `returned_extracost` FROM `orders`	");
if ($run->num_rows() > 0)
{
	$ok = 0;
	$updt = 0;
	$key = 'oid';
	$dbtbl = 'orders';
	foreach ($run->result_array() as $k => $v)
	{
		foreach ($v as $kk => $vv)
		{
		if ($kk != $key)
			{
				$tmp = floater($vv);
				if ($tmp != $vv)
				{
					$updt++;
					//printcool ($vv,false,$v[$key].' Was');
					//printcool ($tmp,false,$v[$key].' Fixed');
					printcool ('update('.$dbtbl.', array('.$kk.'=>'.$tmp.'),array('.$key.' => '.$v[$key].'));	');
					//$this->db->update($dbtbl, array($kk=>$tmp),array($key => $vv[$key]));	
					$this->db->insert('decimal_log', array('tbl' => $dbtbl,'key' => $key,'keyvalue' => $v[$key],'field' => $kk,'was' => $vv,'to' => $tmp));
				}		
				else $ok++;		
			}
		}
	}
	printcool ($ok,false,'OK');	
	printcool ($updt,false,'Updated');	
}

printcool ('warehouse');
$run = $this->db->query("SELECT 	`wid`, `paid`, `cost`, `shipped`, `shipped_actual`,`shipped_inbound`,`sellingfee`,  `netprofit`, `returned_extracost`,`return_pricesold`,`return_sellingfee`,`return_shoppingcost`,`return_netprofit` FROM `warehouse`");	
if ($run->num_rows() > 0)
{
	$ok = 0;
	$updt = 0;
	$key = 'wid';
	$dbtbl = 'warehouse';
	foreach ($run->result_array() as $k => $v)
	{
		foreach ($v as $kk => $vv)
		{
		if ($kk != $key)
			{
				$tmp = floater($vv);
				if ($tmp != $vv)
				{
					$updt++;
					//printcool ($vv,false,$v[$key].' Was');
					//printcool ($tmp,false,$v[$key].' Fixed');
					printcool ('update('.$dbtbl.', array('.$kk.'=>'.$tmp.'),array('.$key.' => '.$v[$key].'));	');
					//$this->db->update($dbtbl, array($kk=>$tmp),array($key => $vv[$key]));	
					$this->db->insert('decimal_log', array('tbl' => $dbtbl,'key' => $key,'keyvalue' => $v[$key],'field' => $kk,'was' => $vv,'to' => $tmp));
				}		
				else $ok++;		
			}
		}
	}
	printcool ($ok,false,'OK');	
	printcool ($updt,false,'Updated');	
}

printcool ('warehouse_auction_expenses');	
$run = $this->db->query("SELECT 	`wae_id`, `exp_value` FROM `warehouse_auction_expenses`");
if ($run->num_rows() > 0)
{
	$ok = 0;
	$updt = 0;
	$key = 'wae_id';
	$dbtbl = 'warehouse_auction_expenses';
	foreach ($run->result_array() as $k => $v)
	{
		foreach ($v as $kk => $vv)
		{
		if ($kk != $key)
			{
				$tmp = floater($vv);
				if ($tmp != $vv)
				{
					$updt++;
					//printcool ($vv,false,$v[$key].' Was');
					//printcool ($tmp,false,$v[$key].' Fixed');
					printcool ('update('.$dbtbl.', array('.$kk.'=>'.$tmp.'),array('.$key.' => '.$v[$key].'));	');
					//$this->db->update($dbtbl, array($kk=>$tmp),array($key => $vv[$key]));	
					$this->db->insert('decimal_log', array('tbl' => $dbtbl,'key' => $key,'keyvalue' => $v[$key],'field' => $kk,'was' => $vv,'to' => $tmp));
				}	
				else $ok++;		
			}
		}
	}
	printcool ($ok,false,'OK');	
	printcool ($updt,false,'Updated');	
}
	
	
	
	
}
function fixdecmess()
{
	$this->db->where('tbl', 'warehouse');
	$ddl = $this->db->get('decimal_log');	
	if ($ddl->num_rows() > 0)
	{
		foreach ($ddl->result_array() as $k => $v)
		{
			$this->db->update($v['tbl'], array($v['field'] => $v['to']), array($v['key'] => $v['keyvalue']));
		}
	}
}
function fixincorrect()
{
	$this->db->where('tbl', 'warehouse');
	$this->db->orderby('was', 'DESC');
	$ddl = $this->db->get('decimal_log');	
	if ($ddl->num_rows() > 0)
	{
		foreach ($ddl->result_array() as $k => $v)
		{
			if ($k >= 30 && $k <= 120 )
			{
			printcool ($k);
			printcool ($v);
			$this->db->select('wid, ordernotes');
			$this->db->where('wid', $v['keyvalue']);
			$wh = $this->db->get('warehouse');
			if ($wh->num_rows() > 0)
			{
				$ware = $wh->row_array();
				printcool ($ware);
				//$this->db->update('warehouse', array('ordernotes' => $ware['ordernotes'].' '.$v['was']), array('wid' => $ware['wid']));
			}
			
			}
		}
	}
}
function moveauctionexpenses()
{
	$this->db->where('wcost !=', '');
	$this->db->or_where('costdata !=', '');
	$this->db->or_where('shipping !=', '');
	$this->db->or_where('expenses !=', '');
	$wadb= $this->db->get('warehouse_auctions');
	if ($wadb->num_rows() >0)
	{
		foreach ($wadb->result_array() as $k=>$v)
		{
			if ($v['deleted'] != 1)
			{
				if (floater($v['wcost'])!= '0.00')
				{
					printcool (floater($v['wcost']));
					printcool ($v);
					$this->db->insert('warehouse_auction_expenses', array('wa_id' => $v['waid'],'exp_type' => 'Cost','exp_title' =>'Moved Cost', 'exp_value' => $v['wcost'],'exp_admin' => $v['wadmin'],'exp_notes' => $v['costdata']));
				}
			}
		}
	}
}


function organizelog($step = 0, $dbgo = false)
{
$this->load->model('Myebay_model'); 
	set_time_limit(2400);
		ini_set('mysql.connect_timeout', 2400);
		ini_set('max_execution_time', 2400);  
		ini_set('default_socket_timeout', 2400);
		ini_set('memory_limit','16384M');
	
	//field = "sold_date" || field = "cost" ||
	$sql = 'SELECT wl_id,bcn, wid, time, ts, datafrom, datato, field, admin, ctrl FROM warehouse_log WHERE
	
	(field = "status" || field = "paid" ||  field = "sold" || field = "sellingfee" || field = "setshipped" || field = "shipped_actual" || field = "sold_id" || field = "channel" || field = "trans_date" || field = "trans_mk" || field = "vended" || field = "returned" || field = "returned_extracost" || field = "returned_recieved" || field = "returned_refunded" || field = "returned_time" || field = "returned_notes" || field = "returned_refunded" || field = "returned_extracost" || field = "return_datesold" || field = "return_id" || field = "return_netprofit" || field = "return_pricesold" || field = "return_sellingfee" || field = "return_shoppingcost" || field = "return_wheresold"|| field = "status_notes")  ORDER BY wl_id ASC 
	';//LIMIT 100000 OFFSET 600000
	// AND wl_id > 800000
	if ($step == 0)
	{ 
	echo 'Starting Step 1
	';
	$wl = $this->db->query($sql);
	/*$this->db->select('wl_id,bcn, wid, time, ts, datafrom, datato, field, admin, ctrl');
	$this->db->where('field', 'status');
	$this->db->or_where('field', 'paid');
	$this->db->or_where('field', 'sold_date');
	//$this->db->or_where('field', 'cost');
	$this->db->or_where('field', 'sold');
	$this->db->or_where('field', 'sellingfee');
	//$this->db->or_where('field', 'netprofit');
	$this->db->or_where('field', 'setshipped');
	$this->db->or_where('field', 'shipped_actual');
	$this->db->or_where('field', 'sold_id');
	$this->db->or_where('field', 'channel');
	$this->db->or_where('field', 'status');
	$this->db->or_where('field', 'trans_date');
	$this->db->or_where('field', 'trans_mk');
	$this->db->or_where('field', 'vended');
	
	$this->db->or_where('field', 'returned');
	$this->db->or_where('field', 'returned_extracost');
	$this->db->or_where('field', 'returned_recieved');
	$this->db->or_where('field', 'returned_refunded');
	$this->db->or_where('field', 'returned_time');
	$this->db->or_where('field', 'return_datesold');
	$this->db->or_where('field', 'return_id');
	$this->db->or_where('field', 'return_netprofit');
	$this->db->or_where('field', 'return_pricesold');
	$this->db->or_where('field', 'return_sellingfee');
	$this->db->or_where('field', 'return_shoppingcost');
	$this->db->or_where('field', 'return_wheresold');
	//$this->db->limit(900000);
	$this->db->order_by('wl_id', 'ASC');
	$wl = $this->db->get('warehouse_log');*/
	if ($wl->num_rows() > 0)
	{
			$this->load->model('Myseller_model');
		//$echo = 'Go 1';
		/*$echo .= '<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg th{background-color:#E3E3E3; font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg .tg-yw4l{vertical-align:top}
#o {color:#BDBDBD !important;}
</style>

<table cellpadding="2" class="tg">;*/
		/*	$echo .= 'wid, bcn, status, sold_id,';
				$echo .= 'channel,';
				$echo .= 'trans_mk,';
				$echo .= 'trans_date,';
				$echo .= 'sellingfee,';
				$echo .= 'paid,';
				$echo .= 'return_id,';
				$echo .= 'status,';
				$echo .= 'asc,';
				$echo .= 'sold,';
				$echo .= 'setshipped,';
				$echo .= 'return_id,';
				$echo .= 'returned,';
				$echo .= 'ret.time,';
				$echo .= 'ret.recieved,';
				$echo .= 'ret.wheresold,';
				$echo .= 'ret.netprofit,';
				$echo .= 'ret.shoppingcost,';
				$echo .= 'ret.sellingfee,';
				$echo .= 'ret.pricesold,';
				$echo .= 'ret.datesold';
				$echo .= '
				';
			*/	
				/*
				$echo .= '<tr><th>sold_id</th>';
				$echo .= '<th>channel</th>';
				$echo .= '<th>trans_mk</th>';
				$echo .= '<th>trans_date</th>';
				$echo .= '<th>sellingfee</th>';
				$echo .= '<th>paid</th>';
				$echo .= '<th>return_id</th>';
				$echo .= '<th>status</th>';
				$echo .= '<th>vended</th>';
				$echo .= '<th>asc</th>';
				$echo .= '<th>sold</th>';
				$echo .= '<th>setshipped</th>';
				$echo .= '<th>return_id</th>';
				$echo .= '<th>returned</th>';
				$echo .= '<th>ret.time</th>';
				$echo .= '<th>ret.recieved</th>';
				$echo .= '<th>ret.wheresold</th>';
				$echo .= '<th>ret.netprofit</th>';
				$echo .= '<th>ret.shoppingcost</th>';
				$echo .= '<th>ret.sellingfee</th>';
				$echo .= '<th>ret.pricesold</th>';
				$echo .= '<th>ret.datesold</th>';
				$echo .= '</tr>';
				*/
		foreach ($wl->result_array() as $w)
		{
				$ts = explode(" ", $w['time']);
				$ts[0] = explode("-", trim($ts[0]));				
				$ts[1] = explode(":", trim($ts[1]));
				//
				$ts = mktime ((int)$ts[1][0], (int)$ts[1][1], 0, (int)$ts[0][1], (int)$ts[0][2], (int)$ts[0][0]);
				
				//echo $ts.'<br>';
			if ($w['wid'] != 0) $log[$w['wid']][$ts][] = $w;	
		}
		$soldidcount = 0;
		
		$prevsoldins = array();
		foreach ($log as $k => $v)
		{
			//printcool ('BCN WID:'.$k);
			
			foreach ($v as $kk => $vv)
			{
				foreach ($vv as $vvv)
				{
					//$echo .= '<tr>';
					//if ($vvv['field'] == 'sold_id' || $vvv['field'] == 'channel' || $vvv['field'] == 'created' || $vvv['field'] == 'fee' || $vvv['field'] == 'paid' || $vvv['field'] == 'return_id' || $vvv['field'] == 'trans_mk' || $vvv['field'] == 'trans_date')
					//{
						//if ($vvv['field'] == 'trans_mk') $vvv['field'] = 'uts';
						//if ($vvv['field'] == 'trans_date') $vvv['field'] = 'created';
						if (isset($compiled[$vvv['wid']][$kk][$vvv['field']]) && trim($compiled[$vvv['wid']][$kk][$vvv['field']]) != '' && $compiled[$vvv['wid']][$kk][$vvv['field']] != $vvv['datato']) 
						{
							//////////////////printcool ($compiled[$vvv['wid']][$kk][$vvv['field']],false,$vvv['field'].' BEING OVERWRITTEN to '.$vvv['datato']);
						}
						$compiled[$vvv['wid']][$kk][$vvv['field']] = $vvv['datato'];
						$compiled[$vvv['wid']][$kk]['ctrl'] = $vvv['ctrl'];
						
					//}
					//else
					//{
						//printcool ($vvv['field']);	
					//}
				}
			}
		}
				//printcool ($kk);
				//printcool ($vv);
				//
				//if ($vv['field'] == 'sold_id' && $vv['datato'] >0)
				//{
				//	echo '<td>SOLD TO '.$vv['datato'].'</td>';	
				//}
				//if ($vv['field'] == 'sold_id' && $vv['datato'] == 0)
				//{
				//	echo '<td>REMOVED FROM SOLD '.$vv['datafrom'].'</td>';	
				//}
				
				/*foreach ($vv as $kkk => $vvv)
				{
					echo '<td>'.$vvv.'</td>';	
				}*/
				//echo '</tr>';
				
				
	
			
			//printcool($compiled);
			//exit();
			//if ($k == 138643 || $k == 137938 ) printcool ($compiled[$k]);
			$c = 0;
			foreach ($compiled as $k => $v)
			{
			$trdet['wid']=
			$trdet['bcn']=
			$trdet['sold_id']=
			$trdet['channel']=
			$trdet['trans_mk']=
			$trdet['trans_date']=
			$trdet['sellingfee']=
			$trdet['paid']=
			$trdet['return_id']=
			$trdet['status']=
			$trdet['status_notes']=		
			$trdet['ctrl']=			
			$trdet['vended']=
			$trdet['shipped_actual']=
			$trdet['sold']=
			$trdet['setshipped']=
			$trdet['return_id']=
			$trdet['returned']=
			$trdet['returned_notes']=
			$trdet['returned_time']=
			$trdet['returned_recieved']=
			$trdet['returned_refunded']=
			$trdet['returned_extracost']=
			$trdet['return_wheresold']=
			$trdet['return_netprofit']=
			$trdet['return_shoppingcost']=
			$trdet['return_sellingfee']=
			$trdet['return_pricesold'] =
			$trdet['return_datesold'] = '';	
			foreach ($v as $kk => $vv)
			{
				if ($kk == 0 && ($vv['status'] == 'Not Tested' || $vv['status'] == "Ready To Sell" || $vv['status'] == "Parts Needed"|| $vv['status'] == "Listed"|| $vv['status'] == "Parted"|| $vv['status'] == "Repairing"|| $vv['status'] == "Scrap")) $c =1; //do nothing
				else
				{
				
				if ($k != $trdet['wid']) 
				{ 
					//$techo .= ''.$k.','; 
					
					$trdet['wid']=$k;
					
				}
				//else $techo .= '| '.$trdet['wid'].','; 
				//printcool ($vv);
				$c++;
				//if ($c == 20000) exit('enough');
				if (isset($vv['status'])) 
				{ 
					if ($trdet['status'] == 'Sold' && (int)$trdet['sold_id'] == 0) 
					{  
						
						$stat = explode ('- ',  $trdet['status_notes']);
						//////////////printcool ($stat,false,'STATPRE');
						if (isset($stat[1]))
						{
							$stat[1] = 	explode (' Sale ',  $stat[1]);
							if (isset($stat[1][1]))
							{
								$stat[1][1] = 	explode (' by ', $stat[1][1]);
								////////////printcool ($stat,false,'STATEXPL');
								if (isset($stat[1][0]))
								{									
									if (trim($stat[1][0]) == 'eBay') $trdet['channel']	= 1;
									if (trim($stat[1][0]) == 'WebSite') $trdet['channel']	= 2;
									if (trim($stat[1][0]) == 'Warehouse') $trdet['channel']	= 4;
								}
								if (isset($stat[1][1][0]) && (int)$stat[1][1][0] > 0) $trdet['sold_id']	= (int)$stat[1][1][0];
								
								
							}
						}
						//////////////if ((int)$trdet['sold_id'] == 0) printcool ($trdet, false, 'NO SOLD ID '.$k);  
					
					}
					elseif ($trdet['status'] == 'Sold' && (int)$trdet['sold_id'] > 0)  $soldidcount++;
					if ($trdet['status'] == 'Sold' && $vv['status'] != $trdet['status']) 
					{
						//printcool ($vv, false, 'Change from Sold for wid '.$k); 
						//printcool ($trdet, false, 'Sold TRDET '.$k); 
						if (trim($trdet['return_pricesold']) != '')
						{
							//echo 'Adding Return<br>
							//';
								////////////////// printcool ($trdet, false, 'Return Initiated '.$k); 
								 $dataids[(int)$trdet['channel']][$k][] = (int)$trdet['sold_id'];
								
								$returns[$k][] = $trdet;
						}
					}
					//$techo .= ''.$vv['status'].','; $trdet['status']=$vv['status'];
				}
				//else $techo .= '| '.$trdet['status'].',';
				$trdet['bcn']=$vv['bcn'];
				if (isset($vv['sold_id'])) { //$techo .= ''.$vv['sold_id'].','; 
				if ((int)$vv['sold_id'] > 0) $trdet['sold_id']=$vv['sold_id'];}
				//else $techo .= '| '.$trdet['sold_id'].','; 
				if (isset($vv['channel'])) {  //$techo .= ''.$vv['channel'].',';
				$trdet['channel']= $vv['channel'];}
				//else $techo .= '| '.$trdet['channel'].','; 
				if (isset($vv['trans_mk'])) { // $techo .= ''.$vv['trans_mk'].',';
				$trdet['trans_mk'] =$vv['trans_mk'];}
				//else $techo .= '| '.$trdet['trans_mk'].','; 
				if (isset($vv['trans_date'])) { // $techo .= ''.$vv['trans_date'].','; 
				$trdet['trans_date']=$vv['trans_date'];}
				//else  $techo .= '| '.$trdet['trans_date'].',';  
				if (isset($vv['sellingfee'])) { // $techo .= ''.$vv['sellingfee'].',';
				 $trdet['sellingfee']=$vv['sellingfee'];}
				//else  $techo .= '| '.$trdet['sellingfee'].','; 
				if (isset($vv['paid'])) {  //$techo .= ''.$vv['paid'].',';
				$trdet['paid'] =$vv['paid'];}
				//else  $techo .= '| '.$trdet['paid'].','; 
				if (isset($vv['return_id'])) {  //$techo .= ''.$vv['return_id'].','; 
				$trdet['return_id']=$vv['return_id'];}
				//else $techo .= '| '.$trdet['return_id'].','; 
				if (isset($vv['status_notes'])) { // $techo .= ''.$vv['status_notes'].',';
				$trdet['status_notes'] =$vv['status_notes'];}
				//else $techo .= '| '.$trdet['status_notes'].','; 
				if (isset($vv['ctrl'])) { // $techo .= ''.$vv['ctrl'].',';
				$trdet['ctrl'] =$vv['ctrl'];}
				//else $techo .= '| '.$trdet['ctrl'].','; 
				 if (isset($vv['status'])) {  //$techo .= ''.$vv['vended'].',';
				$trdet['status'] =$vv['status'];}
				if (isset($vv['vended'])) {  //$techo .= ''.$vv['vended'].',';
				$trdet['vended'] =$vv['vended'];}
				//else $techo .= '| '.$trdet['vended'].','; 
				if (isset($vv['shipped_actual'])) {  //$techo .= ''.$vv['shipped_actual'].','; 
				$trdet['shipped_actual']=$vv['shipped_actual'];}
				//else $techo .= '| '.$trdet['shipped_actual'].','; 
				if (isset($vv['sold'])) {  //$techo .= ''.$vv['sold'].','; 
				$trdet['sold']=$vv['sold'];}
				//else  $techo .= '| '.$trdet['sold'].','; 
				if (isset($vv['setshipped'])) {  //$techo .= ''.$vv['setshipped'].',';
				 $trdet['setshipped']=$vv['setshipped'];}
				//else  $techo .= '| '.$trdet['setshipped'].','; 
				if (isset($vv['return_id'])) {  //$techo .= ''.$vv['return_id'].',';
				 $trdet['return_id']=$vv['return_id'];}
				//else $techo .= '| '.$trdet['return_id'].','; 
				if (isset($vv['returned'])) {  //$techo .= ''.$vv['returned'].','; 
				$trdet['returned']=$vv['returned'];}
				//else $techo .= '| '.$trdet['returned'].','; 
				if (isset($vv['returned_notes'])) {  //$techo .= ''.$vv['returned_notes'].',';
				$trdet['returned_notes'] =$vv['returned_notes'];}
				//else $techo .= '| '.$trdet['returned_notes'].','; 
				if (isset($vv['returned_refunded'])) { // $techo .= ''.$vv['returned_refunded'].',';
				$trdet['returned_refunded'] =$vv['returned_refunded'];}
				//else $techo .= '| '.$trdet['returned_refunded'].','; 
				if (isset($vv['returned_extracost'])) {  //$techo .= ''.$vv['returned_extracost'].',';
				$trdet['returned_extracost'] =$vv['returned_extracost'];}
				//else $techo .= '| '.$trdet['returned_extracost'].','; 
				

			
				if (isset($vv['returned_time'])) { // $techo .= ''.$vv['returned_time'].',';
				$trdet['returned_time'] =$vv['returned_time'];}
				//else $techo .= '| '.$trdet['returned_time'].','; 
				if (isset($vv['returned_recieved'])) { // $techo .= ''.$vv['returned_recieved'].','; 
				$trdet['returned_recieved']=$vv['returned_recieved'];}
				//else  $techo .= '| '.$trdet['returned_recieved'].','; 
				if (isset($vv['return_wheresold'])) {  //$techo .= ''.$vv['return_wheresold'].',';
				$trdet['return_wheresold']= $vv['return_wheresold'];}
				//else  $techo .= '| '.$trdet['return_wheresold'].','; 
				if (isset($vv['return_netprofit'])) {  //$techo .= ''.$vv['return_netprofit'].',';
				$trdet['return_netprofit'] =$vv['return_netprofit'];}
				//else $techo .= '| '.$trdet['return_netprofit'].',';  
				if (isset($vv['return_shoppingcost'])) {  //$techo .= ''.$vv['return_shoppingcost'].','; 
				$trdet['return_shoppingcost']=$vv['return_shoppingcost'];}
				//else  $techo .= '| '.$trdet['return_shoppingcost'].',';  
				if (isset($vv['return_sellingfee'])) {  //$techo .= ''.$vv['return_sellingfee'].',';
				$trdet['return_sellingfee'] =$vv['return_sellingfee'];}
				//else  $techo .= '| '.$trdet['return_sellingfee'].','; 
				if (isset($vv['return_pricesold'])) {  //$techo .= ''.$vv['return_pricesold'].',';
				$trdet['return_pricesold'] =$vv['return_pricesold'];}
				//else $techo .= '| '.$trdet['return_pricesold'].','; 
				if (isset($vv['return_datesold'])) {  //$techo .= ''.$vv['return_datesold'].''; 
				$trdet['return_datesold']=$vv['return_datesold'];}
				//else $techo .= '| '.$trdet['return_datesold'].''; 
		
				if (isset($vv['ctrl'])) {  //$techo .= ''.$vv['return_datesold'].''; 
				$trdet['ctrl']=$vv['ctrl'];}
				//$techo .= '
				//';
				//echo 'Begin Insert Sold
				//';
				
				//printcool ($prevsoldins,false,'PrevSoldIns');	
				//printcool ($trdet,false,'$trdet');	
						
				if ((int)$trdet['sold_id'] > 0
				&&
				(int)$trdet['channel'] > 0
				&&
				(int)$trdet['trans_mk'] > 0
				&&
				$trdet['trans_date'] != ''
				&&
				(floater($trdet['sellingfee']) > 0 || (int)$trdet['channel'] != 1)
				&&
				floater($trdet['paid']) > 0
				&&
				(int)$trdet['vended'] == 1
				&&
				strtolower($trdet['status']) == 'sold'
				//&&

				//floater($trdet['shipped_actual']) > 0
				) { 
					//echo 'Adding Sold<br>
						//	';
				//printcool ($prevsoldins['w_id'],false,'$prevsoldins[wid]');	
				//printcool ($trdet['wid'],false,'$trdet[wid]');
					if(isset($prevsoldins['w_id']) && $prevsoldins['w_id'] == $trdet['wid'])
					{
							//printcool ($prevsoldins,false,'PrevSoldIns');	
						//printcool ($trdet,false,'$trdet');
					}
					if((isset($prevsoldins['w_id']) && $prevsoldins['w_id'] == $trdet['wid'])
					&&
					(isset($prevsoldins['sold_id']) && $prevsoldins['sold_id'] == $trdet['sold_id'])
					&&
					(isset($prevsoldins['channel']) && $prevsoldins['channel'] == $trdet['channel'])
					&&
					(isset($prevsoldins['uts']) && $prevsoldins['uts'] == $trdet['trans_mk'])
					&&
					(isset($prevsoldins['fee']) && $prevsoldins['fee'] == $trdet['sellingfee'])
					&&
					(isset($prevsoldins['paid']) && $prevsoldins['paid'] == $trdet['paid']))
					{ //echo '(Dup)<br>';
					
					}
					else 
					{
			
					$prevsoldins = $insert_sold[$trdet['wid'].'_'.$trdet['sold_id'].'_'.$trdet['channel']] = array('w_id' => $trdet['wid'],'sold_id' => $trdet['sold_id'],'channel' => $trdet['channel'],'uts' =>  $trdet['trans_mk'],'created' => $trdet['trans_date'],'fee' => $trdet['sellingfee'],'paid' => $trdet['paid'],'paypal_fee' => $this->Myseller_model->PayPalFee(((float)$trdet['paid']+(float)$trdet['shipped_actual'])),'returned_amount' => '','return_id' => '','ctrl' => $trdet['ctrl']);
		 
					}
					
					//printcool(array('w_id' => $trdet['wid'],'sold_id' => $trdet['sold_id'],'channel' => $trdet['channel'],'uts' =>  $trdet['trans_mk'],'created' => $trdet['trans_date'],'fee' => $trdet['sellingfee'],'paid' => $trdet['paid'],'returned_amount' => '','return_id' => ''),false,'INSERT SOLD');
						
				
					//$techo .= 'INSERT SOLD,,,,,,,,,,,,,,,,,,,,,,
				//'; 
				//$sales[$k][] = $trdet; 
						
				}
				
				/*
				$techo = '<tr>';

				if (isset($vv['sold_id'])) { $techo .= '<td>'.$vv['sold_id'].'</td>'; $trdet['sold_id']=$vv['sold_id'];}
				else $techo .= '<td id="o">'.$trdet['sold_id'].'</td>'; 
				if (isset($vv['channel'])) {  $techo .= '<td>'.$vv['channel'].'</td>';$trdet['channel']= $vv['channel'];}
				else $techo .= '<td id="o">'.$trdet['channel'].'</td>'; 
				if (isset($vv['trans_mk'])) {  $techo .= '<td>'.$vv['trans_mk'].'</td>';$trdet['trans_mk'] =$vv['trans_mk'];}
				else $techo .= '<td id="o">'.$trdet['trans_mk'].'</td>'; 
				if (isset($vv['trans_date'])) {  $techo .= '<td>'.$vv['trans_date'].'</td>'; $trdet['trans_date']=$vv['trans_date'];}
				else  $techo .= '<td id="o">'.$trdet['trans_date'].'</td>';  
				if (isset($vv['sellingfee'])) {  $techo .= '<td>'.$vv['sellingfee'].'</td>'; $trdet['sellingfee']=$vv['sellingfee'];}
				else  $techo .= '<td id="o">'.$trdet['sellingfee'].'</td>'; 
				if (isset($vv['paid'])) {  $techo .= '<td>'.$vv['paid'].'</td>';$trdet['paid'] =$vv['paid'];}
				else  $techo .= '<td id="o">'.$trdet['paid'].'</td>'; 
				if (isset($vv['return_id'])) {  $techo .= '<td>'.$vv['return_id'].'</td>'; $trdet['return_id']=$vv['return_id'];}
				else $techo .= '<td id="o">'.$trdet['return_id'].'</td>'; 
				if (isset($vv['status'])) {  $techo .= '<td>'.$vv['status'].'</td>'; $trdet['status']=$vv['status'];}
				else $techo .= '<td id="o">'.$trdet['status'].'</td>'; 
				if (isset($vv['vended'])) {  $techo .= '<td>'.$vv['vended'].'</td>';$trdet['vended'] =$vv['vended'];}
				else $techo .= '<td id="o">'.$trdet['vended'].'</td>'; 
				if (isset($vv['shipped_actual'])) {  $techo .= '<td>'.$vv['shipped_actual'].'</td>'; $trdet['shipped_actual']=$vv['shipped_actual'];}
				else $techo .= '<td id="o">'.$trdet['shipped_actual'].'</td>'; 
				if (isset($vv['sold'])) {  $techo .= '<td>'.$vv['sold'].'</td>'; $trdet['sold']=$vv['sold'];}
				else  $techo .= '<td id="o">'.$trdet['sold'].'</td>'; 
				if (isset($vv['setshipped'])) {  $techo .= '<td>'.$vv['setshipped'].'</td>'; $trdet['setshipped']=$vv['setshipped'];}
				else  $techo .= '<td id="o">'.$trdet['setshipped'].'</td>'; 
				if (isset($vv['return_id'])) {  $techo .= '<td>'.$vv['return_id'].'</td>'; $trdet['return_id']=$vv['return_id'];}
				else $techo .= '<td id="o">'.$trdet['return_id'].'</td>'; 
				if (isset($vv['returned'])) {  $techo .= '<td>'.$vv['returned'].'</td>'; $trdet['returned']=$vv['returned'];}
				else $techo .= '<td id="o">'.$trdet['returned'].'</td>'; 
				if (isset($vv['returned_time'])) {  $techo .= '<td>'.$vv['returned_time'].'</td>';$trdet['returned_time'] =$vv['returned_time'];}
				else $techo .= '<td id="o">'.$trdet['returned_time'].'</td>'; 
				if (isset($vv['returned_recieved'])) {  $techo .= '<td>'.$vv['returned_recieved'].'</td>'; $trdet['returned_recieved']=$vv['returned_recieved'];}
				else  $techo .= '<td id="o">'.$trdet['returned_recieved'].'</td>'; 
				if (isset($vv['return_wheresold'])) {  $techo .= '<td>'.$vv['return_wheresold'].'</td>';$trdet['return_wheresold']= $vv['return_wheresold'];}
				else  $techo .= '<td id="o">'.$trdet['return_wheresold'].'</td>'; 
				if (isset($vv['return_netprofit'])) {  $techo .= '<td>'.$vv['return_netprofit'].'</td>';$trdet['return_netprofit'] =$vv['return_netprofit'];}
				else $techo .= '<td id="o">'.$trdet['return_netprofit'].'</td>';  
				if (isset($vv['return_shoppingcost'])) {  $techo .= '<td>'.$vv['return_shoppingcost'].'</td>'; $trdet['return_shoppingcost']=$vv['return_shoppingcost'];}
				else  $techo .= '<td id="o">'.$trdet['return_shoppingcost'].'</td>';  
				if (isset($vv['return_sellingfee'])) {  $techo .= '<td>'.$vv['return_sellingfee'].'</td>';$trdet['return_sellingfee'] =$vv['return_sellingfee'];}
				else  $techo .= '<td id="o">'.$trdet['return_sellingfee'].'</td>'; 
				if (isset($vv['return_pricesold'])) {  $techo .= '<td>'.$vv['return_pricesold'].'</td>';$trdet['return_pricesold'] =$vv['return_pricesold'];}
				else $techo .= '<td id="o">'.$trdet['return_pricesold'].'</td>'; 
				if (isset($vv['return_datesold'])) {  $techo .= '<td>'.$vv['return_datesold'].'</td>'; $trdet['return_datesold']=$vv['return_datesold'];}
				else $techo .= '<td id="o">'.$trdet['return_datesold'].'</td>'; 
				
				$techo .= '</tr>';*/
				//$withdata = 0;
				//foreach ($trdet as $tk => $td) { if (trim($td) != '' && $tk != 'status') $withdata++; }
				//if ($withdata > 0) {
					//if ($kk != 0) $echo .= '-> '.$k.',,,,,,,,,,,,,,,,,,,,,,
					//';//$echo .= '<tr><th colspan="22" align="left">'.$k.'</th></tr>';
					//$echo .= $techo;
				//}
				//$techo = '';
				}
				// unset($compiled[$k][$kk]);	
			}
			}
			//printcool($compiled[$k]);
		
		
			echo 'We are here @ '.$c.' processed<br>';
		
			//$this->load->helper('directory');
			//$this->load->helper('file');
			//write_file($this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication'].'/insert_sold.txt', serialize($insert_sold));
			$write['insert_sold'] = $insert_sold;
			//printcool ($insert_sold);
			//$write['sales'] = $sales;
			$write['dataids'] = $dataids;
			$write['returns'] = $returns;
			$allwrite = serialize($write);
			echo 'Preparing to write file<br>
			';
			$this->load->helper('directory');
			$this->load->helper('file');
			write_file($this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication'].'/step1.txt', $allwrite);
			exit('File Written');
		}
	}
	
		if ($step >0 )
		{
			
		$this->load->helper('directory');
		$this->load->helper('file');
		$allwrite = read_file($this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication'].'/step1.txt');	
		$write = unserialize($allwrite);
		$insert_sold = $write['insert_sold'];
		//$sales = $write['sales'];
		$dataids = $write['dataids'];
		$returns = $write['returns'];
		
		//= unserialize(read_file($this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication'].'/insert_sold.txt'));	
		//$write = unserialize($allwrite);
		
		//exit('Pit Stop');
		//printcool($this->Myebay_model->getOrderReturn((int)$trdet['sold_id'], (int)$trdet['channel']), false, 'ORDER RETURN');
		
		//sleep(15);
		
		if ($step == 1)
		{
			
			if ($dbgo) foreach($insert_sold as $k => $v)
			{
				$v['housekeeped'] = 1;
				$this->db->insert('transaction_details',$v);
				$this->db->update('warehouse',array('td_id' => $this->db->insert_id()), array('wid' => (int)$v['w_id']));
			}
			else printcool ($insert_sold);
		}
		
		
		//printcool ($sales,false,'SALES');
		//printcool ($dataids,false,'DATAIDS');
		if ($step ==2)
		{
		foreach($dataids as $k => $v)
		{
			switch ($k)
			{
				case 1: 
					$tbl = 'ebay_transactions WHERE ';
					$fld = 'et_id';
					$sql = 'SELECT '.$fld.',qty,  return_id, returned, returned_notes, returned_time, returned_recieved, returned_refunded, returned_extracost, returnid, ebayreturntime, ebayRefundAmount, returnQuantity, ebayreturnshipment FROM '.$tbl;
			
			/*
			
				returnid ebayreturntime 	ebayRefundAmount returnreason returncomment	 returncurrentType	returntype	
			*/
				break;
				case 2: 				
					$tbl = 'orders WHERE ';
					$fld = 'oid';
					$sql = 'SELECT '.$fld.', order, return_id, returned, returned_notes, returned_time, returned_recieved, returned_refunded, returned_extracost FROM '.$tbl;
			
				break;
				case 4: 
					$tbl = 'warehouse_orders WHERE ';
					$fld = 'woid';
					$sql = 'SELECT '.$fld.', return_id, returned, returned_notes, returned_time, returned_recieved, returned_refunded, returned_extracost FROM '.$tbl;
			
				break;
			}
			
			$c =1;
			foreach ($v as $kk => $vv)
			{
				foreach ($vv as $kkk => $vvv)
				{
					if ($c==1) $sql .= $fld.' = '.(int)$vvv;
					else  $sql .= ' OR '.$fld.' = '.(int)$vvv;
					$c++;
				}
			
				
			}
			$d = $this->db->query($sql);
			if ($d->num_rows() > 0)
			{
				$r = $d->result_array();
				foreach ($r as $rr)
				{
					if (isset($rr['et_id'])) $getreturns[$k][$rr['et_id']] = $rr; 
					elseif (isset($rr['oid'])) $getreturns[$k][$rr['oid']] = $rr; 
					elseif (isset($rr['woid'])) $getreturns[$k][$rr['woid']] = $rr; 
				}
				
			}
		}
		//printcool ($getreturns, false, 'GET RETURNS');
		
		//$dataids[(int)$trdet['channel']][$k][] = (int)$trdet['sold_id'];
		//$returns[$k][] = $trdet;
		$this->load->model('Auth_model');
		$this->load->model('Mywarehouse_model');
		foreach ($returns as $k => $v)
		{
			foreach ($v as $kk => $vv)
				{
					if (isset($getreturns[$vv['channel']][$vv['sold_id']]))
					{
						 $returns[$k][$kk]['EXTRACTED'] = $getreturns[$vv['channel']][$vv['sold_id']];
						 /*$returns[$k][$kk]['return_id'] = $getreturns[$vv['channel']][$vv['sold_id']]['return_id'];
						 $returns[$k][$kk]['returned'] = $getreturns[$vv['channel']][$vv['sold_id']]['returned'];
						if (isset($returns[$k][$kk]['returned_time']) && trim($returns[$k][$kk]['returned_time'] == '')) $returns[$k][$kk]['returned_time'] = $getreturns[$vv['channel']][$vv['sold_id']]['returned_time'];
						
						if (isset($returns[$k][$kk]['returned_notes']) && trim($returns[$k][$kk]['returned_notes'] == '')) $returns[$k][$kk]['returned_notes'] = $getreturns[$vv['channel']][$vv['sold_id']]['returned_notes'];
						
						$returns[$k][$kk]['returned_recieved'] = $getreturns[$vv['channel']][$vv['sold_id']]['returned_recieved'];
						if(trim($returns[$k][$kk]['returned_refunded'] != trim($getreturns[$vv['channel']][$vv['sold_id']]['returned_refunded'])))
						{
							//$returns[$k][$kk]['old_returned_refunded'] = $returns[$k][$kk]['returned_refunded'];
							 $returns[$k][$kk]['returned_refunded'] = $getreturns[$vv['channel']][$vv['sold_id']]['returned_refunded'];
						}
						if(trim($returns[$k][$kk]['returned_extracost'] != trim($getreturns[$vv['channel']][$vv['sold_id']]['returned_extracost']))) 
						{
							//$returns[$k][$kk]['old_returned_extracost'] = $returns[$k][$kk]['returned_extracost'];
							$returns[$k][$kk]['returned_extracost'] = $getreturns[$vv['channel']][$vv['sold_id']]['returned_extracost'];
						}*/
						$update = false;
						foreach ($returns[$k][$kk]['EXTRACTED'] as $rk => $rv)
						{
							if (trim($returns[$k][$kk][$rk]) != trim($rv) && ($rk != 'et_id' && $rk != 'oid' && $rk != 'woid'))// && $rk != 'returnQuantity'))
							{
								 //printcool ($rv,false,$rk.' from '.$vv[$rk]);
								 if($returns[$k][$kk]['EXTRACTED']['returnQuantity'] ==0 )$returns[$k][$kk]['EXTRACTED']['returnQuantity'] =1; 	
								 if ($rk == 'returnid') $update['ebayReturnId'] = $rv;
								 elseif ($rk == 'ebayreturntime') $update['return_datesold'] = $rv;
								 elseif ($rk == 'ebayRefundAmount') $update['return_pricesold'] = $rv/$returns[$k][$kk]['EXTRACTED']['returnQuantity'];
								  elseif ($rk == 'ebayreturnshipment') $update['return_shippingcost'] = $returns[$k][$kk]['EXTRACTED']['ebayreturnshipment'];
								   elseif ($rk == 'returned_extracost') $update['returned_extracost'] = $rv/$returns[$k][$kk]['EXTRACTED']['returnQuantity'];
								 elseif ($rk == 'returnQuantity') {}
								 else $update[$rk] = $rv;
								
								 //if ($dbgo) $this->Auth_model->wlog($this->Mywarehouse_model->wid2bcn($vv['wid']), $vv['wid'], $rk, $vv[$rk], $rv);
							}
						}
						$this->db->where('wid', (int)$vv['wid']);
						$w = $this->db->get('warehouse');
						 
						$ww = $w->row_array();
						foreach($update as $wk => $wv)
						{
							printcool($ww[$wk],false,$wk);	
						}
						printcool($ww['status']);
						//if ($dbgo) if (is_array($update)) $this->db->update('warehouse', $update, array('wid' => $vv['wid']));							
						printcool ($update,false,'WID TO UPDATE FIELD WID '.$vv['wid']);
						printcool ($returns[$k][$kk]['EXTRACTED'],false,"$returns[$k][$kk]['EXTRACTED']");
						
						printcool ($vv,false,'VV');
						//printcool ($returns[$k][$kk],false,'$returns[$k][$kk]');
					}
					$returned_amount= 0;
					//printcool($returns[$k][$kk]['EXTRACTED']['returned'], false, 'RET1');
					if ($returns[$k][$kk]['EXTRACTED']['returned'] == 1) 
					{
						//printcool($returns[$k][$kk]['EXTRACTED']['returned'], false, 'RET2');
						$returned_amount= $vv['return_pricesold'];
						if (floater($vv['return_pricesold']) == 0.00) printcool ($vv['return_pricesold'], false,'PRICE SOLD ERROR');
					}
					
					
					if (trim($returns[$k][$kk]['EXTRACTED']['returned_time']) != '')
					{
						$dbvals = array(
						'w_id' => $vv['wid'],
						'sold_id' => $vv['sold_id'],
						'channel' => $vv['channel'],
						'uts' =>  date2mk($returns[$k][$kk]['EXTRACTED']['returned_time']),
						'created' => $returns[$k][$kk]['EXTRACTED']['returned_time'],
						'fee' => $vv['return_sellingfee'],
						'paid' => $vv['return_pricesold'],
						'returned_amount' => $returned_amount,
						'return_id' => $returns[$k][$kk]['EXTRACTED']['return_id'], 
						'housekeeped' => 1
						);
						$qty = 1;
						if ($vv['channel'] == 1)
						{
							$qty = $returns[$k][$kk]['EXTRACTED']['qty'];
						}
						elseif($vv['channel'] == 2)
						{
							if (count($returns[$k][$kk]['EXTRACTED']['order']) > 0)
									 {
										foreach ($sale['order'] as $qk => $qv)
										{									
											$qty = $qty+$qv['quantity'];
										}
									 }	
						}
						elseif ($vv['channel'] == 4)						
						{
								$qty = 1;
						}
						
						if ($returns[$k][$kk]['EXTRACTED']['returned']==1)  $dbvals['returned_amount'] = floater($vv['return_pricesold']/$qty);
						else $dbvals['returned_amount']= 0; 
						
						if (floater($returns[$k][$kk]['EXTRACTED']['returned_extracost']) > 0) $dbvals['extra_cost'] = floater($returns[$k][$kk]['EXTRACTED']['returned_extracost']/$qty);
						else $dbvals['extra_cost'] = 0;
						
						$dbvals['return_id'] = $returns[$k][$kk]['EXTRACTED']['return_id'];
						
						if ($vv['channel'] == 1)
						{
							printcool ($returns[$k][$kk], false, 'Returns Channel 1');
							
							$dbvals['returnID'] = 	$returns[$k][$kk]['EXTRACTED']['returnid'];
							if (trim($returns[$k][$kk]['EXTRACTED']['returned_time']) != '')
							{ 
								$dbvals['refund_date'] = $returns[$k][$kk]['EXTRACTED']['returned_time'];
								$dbvals['refund_date_mk'] = date2mk($return['refund_date']);								
							}
							
							$rqty = (int)$returns[$k][$kk]['EXTRACTED']['returnQuantity'];
							if ($rqty == 0) $rqty = 1;
							
							$dbvals['return_total_qty'] = $rqty;
							if (floater($returns[$k][$kk]['EXTRACTED']['ebayRefundAmount']) > 0) $dbvals['returned_amount'] = ($returns[$k][$kk]['EXTRACTED']['ebayRefundAmount']/$rqty);
							else $dbvals['returned_amount'] = 0;
							$dbvals['uts'] = date2mk($dbvals['created']);
							
							if (floater($returns[$k][$kk]['EXTRACTED']['ebayreturnshipment']) > 0)  $dbvals['return_shipping'] = floater(($returns[$k][$kk]['EXTRACTED']['ebayreturnshipment']/$rqty));
							else $dbvals['return_shipping'] = 0;					
							printcool($returns[$k][$kk]['EXTRACTED']['return_shipping'],false,"$returns[$k][$kk]['EXTRACTED']['return_shipping']");
						}
						
					printcool($dbvals,false,'RETURN INSERT');
					if ($dbgo) $this->db->insert('transaction_details', $dbvals);
					
					
					}
					printcool('-----------------------------');
				}	
		}
		}
		//printcool ($returns,false,'RETURNS');
		
		
		$echo .= '
		';
		//printcool ($soldidcount);
		//echo $echo;
		$this->load->helper('download');
		//force_download('organizedwlog.csv', $echo);
		}
}
function fixwhlogts()
{
	set_time_limit(600);
	ini_set('mysql.connect_timeout', 600);
	ini_set('max_execution_time', 600);  
	ini_set('default_socket_timeout', 600);
	ini_set('memory_limit','2048M');
	
	$sql = 'SELECT wl_id, time FROM warehouse_log WHERE ts = 0 ORDER BY wl_id DESC LIMIT 500000';
	$f = $this->db->query($sql);
	if ($f->num_rows() > 0)
	{
		foreach ($f->result_array() as $t)
		{
				$ts = explode(" ", $t['time']);
				$ts[0] = explode("-", trim($ts[0]));				
				$ts[1] = explode(":", trim($ts[1]));
				$ts = mktime ((int)$ts[1][0], (int)$ts[1][1], (int)$ts[1][2], (int)$ts[0][1], (int)$ts[0][2], (int)$ts[0][0]);
				echo $ts.'<br>';
				$this->db->update('warehouse_log',array('ts' => $ts),array('wl_id' => $t['wl_id']));
			
		}
	}
}
function comparedatetimeandtimestamp()
{
	$this->db->select('et_id, datetime, mkdt');
	$this->db->limit(1000);
	$this->db->order_by('et_id', 'DESC');
	$c = $this->db->get('ebay_transactions');
	foreach ($c->result_array() as $v)
	{
		$v['genMK_from_db_datetime'] = date2mk($v['datetime']);
		$v['genDate_from_db_mkdt']= mk2date($v['mkdt']);
		printcool ($v);	
	}
	
}
function expandbcncreateddate()
{
	set_time_limit(600);
	ini_set('mysql.connect_timeout', 600);
	ini_set('max_execution_time', 600);  
	ini_set('default_socket_timeout', 600);
	ini_set('memory_limit','2048M');
	$this->db->select('wid,dates');
	$this->db->where('deleted', 0);
	$this->db->where('nr', 0);
	$this->db->where('createddatemk', 0);
	//$this->db->limit(100);
	//$this->db->where('wid', 66198);
	$w = $this->db->get('warehouse');
	if ($w->num_rows() > 0)
	{printcool ($w->num_rows());
	//exit();
		foreach ($w->result_array() as $ww)
		{
			$ww['dates'] = unserialize($ww['dates']);
			if(isset($ww['dates'][0]))
			{
				$update['createddate']	 = trim($ww['dates'][0]['created']);
				$update['createddatemk'] = (int)$ww['dates'][0]['createdstamp'];
			}
			elseif (isset($ww['dates']['created']) && isset($ww['dates']['createdstamp']))
			{
				$update['createddate']	 = trim($ww['dates']['created']);
				$update['createddatemk'] = (int)$ww['dates']['createdstamp'];
			}
			//printcool ($ww);
			printcool ($update);
			$this->db->update('warehouse', $update, array('wid' => $ww['wid']));
			
		}
	}
}
function auctionexpensesmk()
{
	$this->db->select('wae_id,exp_time');
	$w = $this->db->get('warehouse_auction_expenses');
	if ($w->num_rows() > 0)
	{
		foreach ($w->result_array() as $ww)
		{			
			$this->db->update('warehouse_auction_expenses', array('exp_time_mk' => date2mk($ww['exp_time'])), array('wae_id' => $ww['wae_id']));
		}
	}
}
function testreturnapi()
{
	require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');	
        require($this->config->config['ebaypath'].'get-common/keys.php');

         $url = 'https://api.ebay.com/post-order/v2/return/5049167583'; //?fieldgroups=FULL
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
		printcool($data);
		/*
		$answer['currentType'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['currentType'])));
		$answer['type'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['type'])));
		$answer['reason'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['reason'])));
		$answer['comment'] = $data['summary']['creationInfo']['comments']['content'];
		$answer['returnQuantity'] = $data['summary']['creationInfo']['item']['returnQuantity'];
		*/
}
function LoopReturns()
{
	set_time_limit(1800);
	ini_set('mysql.connect_timeout', 1800);
	ini_set('max_execution_time', 1800);  
	ini_set('default_socket_timeout', 1800);
	ini_set('memory_limit','2048M');
	$this->db->select('et_id, itemid, transid');
	$this->db->where('return_id > ', 0);
	$this->db->or_where('returnid > ', 0);	
	//$this->db->limit(10);
	$r = $this->db->get('ebay_transactions');
	if ($r->num_rows() > 0)
	{ 
		foreach ($r->result_array() as $rr)
		{
			$this->_getReturnData($rr['itemid'], $rr['transid'], $rr['et_id']);
			
				
		}
	}
}
function _getReturnData($item_id, $transaction_id, $et_id)
{
	/*
	GET https://api.ebay.com/post-order/v2/return/search?
  item_id=string&
  transaction_id=string&
  return_state=ReturnCountFilterEnum&
  offset=integer&
  limit=integer&
  sort=ReturnSortField&
  creation_date_range_from=string&
  creation_date_range_to=string&
  states=ReturnStateEnum
	*/
	
	require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');	
        require($this->config->config['ebaypath'].'get-common/keys.php');

         $url = 'https://api.ebay.com/post-order/v2/return/search?item_id='.$item_id.'&transaction_id='.$transaction_id; //?fieldgroups=FULL
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

		if (isset($data['members']))
		{
		
		
		$returnID = (int)$data['members'][0]['returnId'];
		$url = 'https://api.ebay.com/post-order/v2/return/'.(int)$returnID;  //?fieldgroups=FULL
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
		//exit('because of dump, stopped here at 5824 row');
	//	printcool($data['members'][0]['sellerTotalRefund']['estimatedRefundAmount']['value']);
	//	printcool(CleanBadDate($data['members'][0]['creationInfo']['creationDate']['value']));
		$answer['returnId'] = $returnID;	
		
		$answer['returncurrentType'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['currentType'])));
		
		$answer['returntype'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['type'])));
		
		$answer['returnreason'] = ucwords(strtolower(str_replace('_', ' ', $data['summary']['creationInfo']['reason'])));
		$answer['returncomment'] = $data['summary']['creationInfo']['comments']['content'];
		$answer['returnQuantity'] = $data['summary']['creationInfo']['item']['returnQuantity'];
		$answer['ebayRefundAmount'] = $data['summary']['sellerTotalRefund']['estimatedRefundAmount']['value'];
		$answer['ebayreturntime'] = CleanBadDate($data['summary']['creationInfo']['creationDate']['value']);
		if (isset($data['detail']['returnShipmentInfo']['shippingLabelCost']['totalAmount']['value'])) $answer['ebayreturnshipment'] = floater($data['detail']['returnShipmentInfo']['shippingLabelCost']['totalAmount']['value']);
		else $answer['ebayreturnshipment'] = 0;
		
		$this->db->update('ebay_transactions', $answer, array('et_id' => (int)$et_id));
		echo 'Commit for rec: '.(int)$et_id.'<br>';
		
		
		
		}	
		
}

function populatetransid()
{
	$e = $this->db->query('SELECT et_id, paydata, paid FROM ebay_transactions WHERE paydata IS NOT NULL');
	if ($e->num_rows() > 0)
	{
		foreach ($e->result_array() as $ee)
		{
			$ee['paydata'] = explode('"PayPalTransactionID";s:17:"', $ee['paydata']);
			if (isset($ee['paydata'][1])) $ee['paydata'] = explode('";s:', $ee['paydata'][1]);
			if (isset($ee['paydata'][1])) 
			{
				$eee['pptransid'] = $ee['paydata'][0];
				if ($ee['paid'] > 0) $eee['paypal_fee'] = ((floater($ee['paid'])/100)*2.2)+0.30;
				$this->db->update('ebay_transactions', $eee, array('et_id'=>$ee['et_id']));
				printcool ($eee, false, $ee['et_id']);	
			}
			
		}
	}
	
	
	
}


function remergeXdays()
{$this->load->helper('directory');
		$this->load->helper('file');
	
		set_time_limit(1800);
		ini_set('mysql.connect_timeout', 1800);
		ini_set('max_execution_time', 1800);  
		ini_set('default_socket_timeout', 1800); 
		require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
///HARDCODED
$pages = 10;
$page = 1;
////
		while ($page <= $pages) 
		{
								
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= '<IncludeContainingOrder>true</IncludeContainingOrder>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version><NumberOfDays>1</NumberOfDays>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		//$dates = array('from' => date('Y-m-d H:i:s', strtotime("-2 Hours")), 'to' => date("Y-m-d H:i:s"));
		//<ModTimeFrom>'.$dates['from'].'</ModTimeFrom>
 		//<ModTimeTo>'.$dates['to'].'</ModTimeTo>  
		
			
		//<IncludeCodiceFiscale>'.TRUE.'</IncludeCodiceFiscale>		
		//<IncludeContainingOrder>'.TRUE.'</IncludeContainingOrder> 
		
		
		$requestXmlBody .= '
	
		 <IncludeFinalValueFee>'.TRUE.'</IncludeFinalValueFee>
			<NumberOfDays>30</NumberOfDays>	
		<Pagination>
		<EntriesPerPage>200</EntriesPerPage> 
 	   	<PageNumber>'.$page.'</PageNumber>
 
		</Pagination>
		</GetSellerTransactionsRequest>';	
		$verb = 'GetSellerTransactions';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		write_file($this->config->config['ebaypath'].'/transdev_'.$page.'.txt', $responseXml);
		//$xml = simplexml_load_string($responseXml);
		//$pages = $xml->PaginationResult->TotalNumberOfPages;
	//	[PaginationResult] => SimpleXMLElement Object
      //  (
        //    [TotalNumberOfPages] => 10
         //   [TotalNumberOfEntries] => 1888
       // )
	   $page++;
		}		
}
function remergepart2()
{set_time_limit(1800);
		ini_set('mysql.connect_timeout', 1800);
		ini_set('max_execution_time', 1800);  
		ini_set('default_socket_timeout', 1800); 
		$files=array('transdev_1.txt', 'transdev_2.txt', 'transdev_3.txt', 'transdev_4.txt', 'transdev_5.txt', 'transdev_6.txt', 'transdev_7.txt', 'transdev_8.txt', 'transdev_9.txt', 'transdev_10.txt' );
		$this->load->helper('directory');
		$this->load->helper('file');
		$c = 1;
		foreach ($files as $f)
		{printcool ($f);
		$list = read_file($this->config->config['ebaypath'].'/'.$f);
		$xml = simplexml_load_string($list);
		
		$list = $xml->TransactionArray->Transaction;
		
		if ($list) foreach ($list as $l)
		{
			$insert=array();
			
			
			
			$insert['hk_amp'] = serialize(array('AmountPaid' => (string)$l->AmountPaid, 'AdjustmentAmount' => (string)$l->AdjustmentAmount, 'ConvertedAdjustmentAmount' => (string)$l->ConvertedAdjustmentAmount, 'ConvertedAmountPaid' => (string)$l->ConvertedAmountPaid,'ConvertedTransactionPrice' => (string)$l->ConvertedTransactionPrice));
			printcool($insert,false, (int)$l->ShippingDetails->SellingManagerSalesRecordNumber.' ('.$c.')' );

			$this->db->update('ebay_transactions', $insert, array('rec' =>  (int)$l->ShippingDetails->SellingManagerSalesRecordNumber ));
			$c++;
			
		}
		}
}



function findstrangepaid()
{
	$this->db->select('et_id, paid, eachpaid,ssc,asc,qty');
	$this->db->where('ssc !=', 0);
	$this->db->order_by('et_id', 'DESC');
	$e = $this->db->get('ebay_transactions');	
	if ($e->num_rows() > 0)
	{
		echo '<table><tr><th>ID</th><th>QTY</th><th>PAID</th><th>EACHPAID</th><th>SSC</th><th>ASC</th></tr>';
		foreach ($e->result_array() as $r)
		{
			echo '<tr>';
			echo '<td>';
			echo $r['et_id'];
			echo '</td>';
			echo '<td>';
			if ((int)$r['qty'] > 1)echo '<strong>'.$r['qty'].'</strong>';
			else echo $r['qty'];
			echo '</td>';
			echo '<td>';
			echo $r['paid'];
			echo '</td>';
			echo '<td>';
			echo $r['eachpaid'];
			echo '</td>';
			echo '<td>';
			echo $r['ssc'];
			echo '</td>';			
			echo '<td>';
			echo $r['asc'];
			echo '</td>';
			echo '</tr>';
			
			
		}
		echo '</table>';
	}
}
function SpoolPayPalFee()
{
	$this->db->select('wid, bcn, paid, shipped_actual');
	$this->db->where('vended', 1);
	$this->db->where('channel', 1);
	$this->db->where('sold_id >', 0);
	$d = $this->db->get('warehouse');
	if ($d->num_rows() > 0)
	{$this->load->model('Myseller_model');
		foreach ($d->result_array() as $data)
		{
			//printcool ($data);
			$ppfee = $this->Myseller_model->PayPalFee(((float)$data['paid']+(float)$data['shipped_actual']));
			//printcool($ppfee);
			$this->db->update('warehouse', array('paypal_fee' => $ppfee), array('wid' => $data['wid']));
		}
	}
}		
function FixAscMissing()
{
	$q = $this->db->query('SELECT w.wid, w.sold_id, w.bcn, w.shipped_actual, t.qty, t.asc FROM (warehouse w) LEFT JOIN ebay_transactions t ON t.et_id = w.sold_id WHERE w.vended = 1 AND w.channel = 1 AND w.sold_id > 0 AND w.shipped_actual = 0 AND t.asc > 0');
	if ($q->num_rows() > 0)
	{
		foreach ($q->result_array() as $res)
		{
			$newval = floater(($res['asc']/$res['qty']));
			//printcool ($res);
			$this->Auth_model->wlog($res['bcn'], $res['wid'], 'shipped_actual', $res['shipped_actual'], $newval);	
			$this->db->update('warehouse', array('shipped_actual' => $newval), array('wid'=> $res['wid']));	
		}
	}	
}	
function ReprocessNetProfit()
{
	set_time_limit(600);
	ini_set('mysql.connect_timeout', 600);
	ini_set('max_execution_time', 600);  
	ini_set('default_socket_timeout', 600);
	ini_set('memory_limit','2048M');
	$this->load->model('Myseller_model');
	$this->db->select('wid, bcn, '.$this->Myseller_model->sellingfields());
	$this->db->where('vended', 1);
	$this->db->where('sold_id >', 0);
    $this->db->where('wid', 177461);
	$d = $this->db->get('warehouse');
	if ($d->num_rows() > 0)
	{
		
		foreach ($d->result_array() as $data)
		{
			//printcool ($data);
						
			$netprofit  = $this->Myseller_model->NetProfitCalc((float)$data['paid'], (float)$data['shipped'], (float)$data['shipped_inbound'], (float)$data['cost'], (float)$data['sellingfee'], (float)$data['shipped_actual'],$data['paypal_fee']);
	
			//function NetProfitCalc($paid = 0, $shipping = 0, $cost = 0, $fee = 0, $actualshipping = 0,$paypalfee = 0)
			//printcool($netprofit);
			if (floater($data['netprofit']) != floater($netprofit))
			{
				//printcool ($data['netprofit']);
				//printcool ($netprofit,false,'NEW');
				$this->Auth_model->wlog($data['bcn'], $data['wid'], 'netprofit', $data['netprofit'], floater($netprofit));	
				$this->db->update('warehouse', array('netprofit' => floater($netprofit)), array('wid' => $data['wid']));
			}
		}
	}
}

function CheckBcnData()
{
	$this->load->model('Myseller_model');
	set_time_limit(600);
	ini_set('mysql.connect_timeout', 600);
	ini_set('max_execution_time', 600);  
	ini_set('default_socket_timeout', 600);
	ini_set('memory_limit','2048M');
	$this->db->order_by('et_id', 'DESC');
	$this->db->where('et_id >', 22000);
	$e = $this->db->get('ebay_transactions');
	foreach ($e->result_array() as $et)
	{
		if ((int)$et['qty'] == 0) $et['qty'] = 1;
		if (floater($et['eachpaid']) > 0) $warehouse['paid'] = floater($et['eachpaid']);		
		else $warehouse['paid'] = floater($et['paid']/$et['qty']);	
		$warehouse['sellingfee'] = floater($et['fee']/$et['qty']);		
		$warehouse['shipped_actual'] = floater($et['asc']/$et['qty']);
		$warehouse['shipped'] = floater($et['ssc']/$et['qty']);
		//printcool ($warehouse);
							$this->db->select('wid, bcn, '.$this->Myseller_model->sellingfields());
							$this->db->where('channel', 1);
							$this->db->where('sold_id', $et['et_id']);
							$this->db->where('vended', 1);
							
							$f = $this->db->get('warehouse');
							if ($f->num_rows() > 0)
							{
								$fr = $f->result_array();
								foreach ($fr as $fl)
								{	
									if ($fl['paid'] != $warehouse['paid'] && floater($warehouse['paid']) > 0) { 
									printcool ($fl['paid'],false,$fl['wid'].' OLD paid ('.$et['et_id'].')'); 
									 printcool ($warehouse['paid'],false,$fl['wid'].' NEW paid ('.$et['et_id'].')'); }
									else unset($warehouse['paid']);
									
									if (floater($fl['sellingfee']) != floater($warehouse['sellingfee']) && floater($warehouse['sellingfee']) > 0) {
									//	 printcool (floater($fl['sellingfee']),false,$fl['wid'].' OLD sellingfee ('.$et['et_id'].')');  
									//printcool (floater($warehouse['sellingfee']),false,$fl['wid'].' NEW sellingfee ('.$et['et_id'].')'); 
									}
									else unset($warehouse['sellingfee']);
									
									if ($fl['shipped_actual'] != $warehouse['shipped_actual']) { 
									//printcool ($fl['shipped_actual'],false,$fl['wid'].' OLD shipped_actual ('.$et['et_id'].')'); 
									//printcool ($warehouse['shipped_actual'],false,$fl['wid'].' NEW shipped_actual ('.$et['et_id'].')'); 
									}
									else unset($warehouse['shipped_actual']);
									
									if ($fl['shipped'] != $warehouse['shipped'] && floater($et['ssc']) > 0) { 
									//printcool ($fl['shipped'],false,$fl['wid'].' OLD shipped ('.$et['et_id'].')'); 
									// printcool ($warehouse['shipped'],false,$fl['wid'].' NEW shipped ('.$et['et_id'].')');
									 }
									else unset($warehouse['shipped']);
									if (floater($et['ssc']) == 0) unset($warehouse['shipped']);
									if (count($warehouse) > 0) printcool ($warehouse,false,$fl['wid'].' ('.$et['et_id'].')');
									if ($fl['vended'] == 1 && count($warehouse > 0)) $this->Myseller_model->HandleBCN($warehouse, $fl);									
								}
							}	
							
							unset ($warehouse);	
	}
}


function fixeqpartmismatch()
{
    $this->db->select('e_id');
    $this->db->where('ebay_id !=', 0);
    $this->db->where('ebended', NULL);
    $this->db->where('quantity != e_qpart');
    $res = $this->db->get('ebay');
    if ($res->num_rows() >0)
    {  
        printcool($res->num_rows());
    }
    //exit();
    foreach ($res->result_array() as $r)
    {
        $this->listingid = (int)$r['e_id'];
	$this->eb['quantity'] = 0;
	//$this->ch['xquantity'] = 0;
	$this->eb['ngen'] = 0;
	$this->eb['e_qpart'] = 0;
	$sql = 'SELECT wid, waid, generic, status FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND `listingid` = '.$this->listingid;
	$cc =  $this->db->query($sql);
	if ($cc->num_rows() > 0)
	{
		foreach ($cc->result_array() as $c)
		{
			if ($c['status'] == 'Listed') $this->eb['quantity']++;
			//$this->ch['xquantity']++;
			$this->eb['e_qpart']++;	
			if ($c['generic'] != 0) $this->eb['ngen']++;
					
		}
	}
	//printcool ($this->ch);
	$this->db->update('ebay', $this->eb, array('e_id' => (int)$this->listingid));
    }
}

function fixebayzero()
{
    $this->load->model('Myseller_model');
    $this->Myseller_model->que_rev(14088, 'q', 0);
    $this->Myseller_model->que_rev(9766, 'q', 0);
                                
}
function fixebaych4()
{
    $this->db->select('e_id,qn_ch1,qn_ch3');
    $this->db->where('qn_ch1 != qn_ch3');
    $e = $this->db->get('ebay');
    printcool($e->result_array());
    foreach ($e->result_array() as $b)
    {
      //  $this->db->update('ebay', array('qn_ch3' => $b['qn_ch1']), array('e_id' => $b['e_id']));
        
    }
}
function fixebayall()
{
    $this->db->select('e_id,qn_ch1,qn_ch3,quantity');
    $this->db->where('qn_ch1 != quantity');
     $this->db->where('ebay_id !=', 0);
    $this->db->where('ebended', NULL);
    $e = $this->db->get('ebay');
    printcool($e->result_array());
    foreach ($e->result_array() as $b)
    {
       // $this->db->update('ebay', array('qn_ch1' => $b['quantity'],'qn_ch2' => $b['quantity'], 'qn_ch3' => $b['quantity']), array('e_id' => $b['e_id']));
       // $this->Myseller_model->que_rev($b['e_id']), 'q', $b['quantity']);
    }
}

}