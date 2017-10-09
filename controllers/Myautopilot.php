<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Myautopilot extends Controller
{
    function Myautopilot()
	{
		parent::Controller();
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		$this->load->model('Myautopilot_model');
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Autopilot');
	}
    function index()
	{
        exit('OK');
		$list = $this->Myautopilot_model->ShowNoSold(30);
		
        foreach($this->Myautopilot_model->ShowNoSold(30) as $row)
        {
            printcool ($row);
        }
        //$this->mysmarty->view('myautopilot/main.html');
	}
	function refreshautopilot($listingid)
	{
		$rules = $this->Myautopilot_model->GetListingRules(array((int)$listingid), TRUE);
		//printcool ($rules);
		if ($rules)
		{
			$this->load->model('Myebay_model');
			$this->load->model('Mycompetitor_model');
			
			$crules = $this->Mycompetitor_model->GetListingRules(array((int)$listingid), TRUE);
			$this->mysmarty->assign('competitorrules', $crules);
			
			foreach($rules as $eid=> $ar)
			{				
		    	$this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
				foreach ($ar as $rid => $arr)
				{
       	    	$this->mysmarty->assign('ar', $arr);
	   	    	$html[]=array('rid' => (int)$arr['rid'], 'html' => $this->mysmarty->fetch('myebay/myebay_show_loop_autopilot.html'));				
				}
			}
			echo json_encode($html);
		}
	}
	function test()
	{
		$eid = 15839;
		$this->db->select('price_ch1');
		$this->db->where('e_id', $eid);
		$l = $this->db->get('ebay');
		printcool ($l->result_array());
		$changevalue = 20;
		$sign = '-';
                $this->db->query('UPDATE ebay SET `price_ch1` = `price_ch1`'.$sign.$changevalue.' WHERE `e_id` = '.(int)$eid);
		$this->db->select('price_ch1');
		$this->db->where('e_id', $eid);
		$l = $this->db->get('ebay');
		printcool ($l->result_array());
	}
    function RuleMod($eid=0, $storecat=0, $add=0, $daystocheck=0, $isamount=0, $update=0, $dispose=0)
    {
			$changevalue = (float)$this->input->post('changevalue', true);
			$predefined = $this->input->post('predefined', true);
            if ((int)$predefined != 1) $predefined = 0;
			else $predefined = 1;
			
			$inform = $this->input->post('inform', true);
            if ((int)$inform != 1) $inform = 0;
			else $inform = 1;
			
			$hours = $this->input->post('hours', true);
            if ((int)$hours != 1) $hours = 0;
			else $hours = 1;
			
			if ((int)$dispose != 1) $dispose = 0;
			else $dispose = 1;
			
			$notes = $this->input->post('notes', true);
			
			if($changevalue<0.01 or ((int)$eid == 0 and (int)$storecat == 0 and $predefined == 0) or ((int)$eid>0 and (int)$storecat>0) or (int)$daystocheck==0)
            { 
			    exit('Wrong parameter in RuleMod function!');
            }
            $array = array(
				'predefined' => (int)$predefined,
                'e_id' => (int)$eid,
                'storecat_id' => (int)$storecat,
                'changevalue' => (float)$changevalue,
		        'add' => (int)$add,
                'daystocheck' => (int)$daystocheck,
                'isamount' => $isamount,
                'rununtil' => (float)$this->input->post('rununtil', true),
				'runtimes' => $this->input->post('runtimes', true),
                'adminassigned' => $this->session->userdata['admin_id'],
                'rulecreateddate' => CurrentTimeR(),				
				'inform' => $inform,
				'hours' => $hours,
				'notes' => $notes,
				'dispose' => (int)$dispose
                    );
				
				if ((int)$hours == 1)	
				{
				 $array['runnext'] = date("Y-m-d H:i:s", time()+((int)$daystocheck*3600));
				 $array['runnextmk'] = mktime()+((int)$daystocheck*3600);	
				}
				else
				{
					
				 $array['runnext'] = date('Y-m-d', strtotime("+".(int)$daystocheck." days"));
				 $array['runnextmk'] = mktime()+((int)$daystocheck*3600*24);
				}

            if ((int)$update == 0)
		    {
			    $this->db->insert('autopilot_rules', $array);
			    $array['rid'] = $this->db->insert_id();
		    }
            else
		    {
				unset($array['rulecreateddate']);				
				unset($array['predefined']);
			    $this->db->update('autopilot_rules', $array, array('rid' => (int)$update));
			    $array['rid'] = $update;
		    }
		    $this->load->model('Myebay_model');
		    $this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
       	    $this->mysmarty->assign('ar', $array);
	   	    echo $this->mysmarty->fetch('myebay/myebay_show_loop_autopilot.html');
    }
	function RuleReQue($rid=0)
    {
			 $array['runnext'] = date("Y-m-d H:i:s", (time()+3600));
			 $array['runnextmk'] = (mktime()+3600);	
			 $this->db->update('autopilot_rules', $array, array('rid' => (int)$rid));
			 
		    $this->load->model('Myebay_model');
		    $this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
			$this->db->where('rid', (int)$rid);
			$apo =$this->db->get('autopilot_rules');
			$rdata = false;
			if ($apo->num_rows() > 0)
			{
				$rdata = $apo->row_array();	
			}
       	    $this->mysmarty->assign('ar', $rdata);
	   	    echo $this->mysmarty->fetch('myebay/myebay_show_loop_autopilot.html');
    }
	function Predefined()
	{
		  $array = array(
		    'rid' => 0,
            'e_id' => 0,
            'storecat_id' => 0,
            'changevalue' => 0,
            'daystocheck' => 0,
            'isamount' => 1,
            'rununtil' => 0,
			'runtimes' => NULL,
            'adminassigned' => $this->session->userdata['admin_id'],
            'rulecreateddate' => '',
			'inform' => 0,
			'hours' => 0,
			'notes' => '',
			'dispose' => 0
			);
        $this->mysmarty->assign('ar', $array);
        $this->mysmarty->assign('prules', $this->Myautopilot_model->GetPredefined());
		$this->load->model('Myebay_model');
		$this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
		$this->mysmarty->view('myautopilot/myautopilot_predefined.html');
	}
    function NewMod($eid=0, $storecat=0, $title=false)
    {
		if ($title)
		{$this->db->where('rid', (int)$title);
		$m=$this->db->get('autopilot_rules');
		if ($m->num_rows() > 0){  $array = $m->row_array();  $array['e_id'] = (int)$eid;  $array['rid'] = 0; }
		else $array = false;
		/*$array = array(
		    'rid' => 0,
            'e_id' => (int)$eid,
            'storecat_id' => 0,
            'changevalue' => 10,
            'daystocheck' => 30,
            'isamount' => 0,
            'rununtil' => 0,
            'adminassigned' => $this->session->userdata['admin_id'],
            'rulecreateddate' => '');*/
		}
		else	
	    $array = array(
		    'rid' => 0,
            'e_id' => (int)$eid,
            'storecat_id' => (int)$storecat,
            'changevalue' => 0,
            'daystocheck' => 0,
            'isamount' => 1,
            'rununtil' => 0,
			'runtimes' => NULL,
            'adminassigned' => $this->session->userdata['admin_id'],
            'rulecreateddate' => '',
			'inform' => 0,
			'hours' => 0,
			'notes' => '',
			'dispose' => 0);
	    $this->load->model('Myebay_model');
		$this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
        $this->mysmarty->assign('ar', $array);
        echo $this->mysmarty->fetch('myebay/myebay_show_loop_autopilot.html');
    }
    function ShowRules($page = '')
	{
		$rules = $this->Myautopilot_model->GetRules((int)$page);
        $this->mysmarty->assign('rules', $rules['results']);
		$this->mysmarty->assign('pages', $rules['pages']);
		$this->mysmarty->assign('page', (int)$page);
		$this->mysmarty->assign('nosmallmenu', TRUE);
		$this->load->model('Myebay_model');
		$this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
		$this->mysmarty->view('myautopilot/myautopilotrules.html');
	}
    function DeleteRule($rid)
	{
        $this->db->query("DELETE FROM autopilot_rules WHERE rid = ".(int)$rid);
	}
	function Logs($page = 0, $listingid = 0)
	{
		$logs =  $this->Myautopilot_model->Logs((int)$page, $listingid);
		$this->mysmarty->assign('logs', $logs['results']);
		$this->mysmarty->assign('pages', $logs['pages']);
		$this->mysmarty->assign('page', (int)$page);
		$this->session->set_userdata('page', (int)$page);
		$this->mysmarty->view('myautopilot/myautopilot_log.html');
	}
	function OldChart($listingid = 0, $days = 0)
	{
		$this->mysmarty->assign('days', (int)$days);
		$chart =  $this->Myautopilot_model->Chart((int)$listingid, (int)$days); 		
		if (!$chart) exit('baddata');
		$ldata = false;
		//$this->db->select('e_title');
		$this->db->where('e_id',(int)$listingid);
		$ld = $this->db->get('ebay');
		if ($ld->num_rows() > 0) $ldata = $ld->row_array();	
//printcool($chart);
		$costs = false;
		$this->db->select('cost');
		$this->db->where('deleted', 0);
		$this->db->where('nr', 0);
		$this->db->where('vended', 0);
		$this->db->where('listingid', (int)$listingid);
		$q = $this->db->get('warehouse');
		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $c)
			{
				if ($c['cost'] != '') $costs[$c['cost']] = $c['cost'];					
			}
		}
		
		$this->load->model('Myseller_model');		
		$this->mysmarty->assign('hot',TRUE);


		$this->statuses = $this->Myseller_model->assignstatuses();
		

				$this->Myseller_model->getBase(array((int)$listingid));
				$this->Myseller_model->getOnHold(array((int)$listingid));
				$this->Myseller_model->countSales(array((int)$listingid));
				//$CI->Myseller_model->getEmptySales($idarray, 1);	
				$this->load->model('Myautopilot_model');
				$rules = $this->Myautopilot_model->GetListingRules(array((int)$listingid), TRUE);	
				$this->mysmarty->assign('autopilotrules', $rules);
				$this->load->model('Mycompetitor_model');
				$crules = $this->Mycompetitor_model->GetListingRules(array((int)$listingid), TRUE);
				$this->mysmarty->assign('competitorrules', $crules);
		
		$this->load->model('Myebay_model');	
		$adms = 	$this->Myebay_model->GetAdminList();
		$adms[0] = 'Cron';
		$this->mysmarty->assign('adm', $adms);
		$this->mysmarty->assign('prules', $this->Myautopilot_model->GetPredefined());
		
		$fieldset = array('price' => array(
									'headers' => "'Time', 'From', 'To', 'Rule', 'Admin'",
									/*'rowheaders' => $list['headers'], */
									'width' => "125, 125, 125,  125, 125, 125", 
									'startcols' => 5, 
									'startrows' => 1,
									'colmap' => '{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}'),
						'sales' => array(
								'headers' => "'Order', 'Where', 'Bcns', 'Sale Date', 'Price', 'Cost', 'Profit'",
								/*'rowheaders' => $list['headers'], */
								'width' => "155, 100, 350,125, 70,70,70", 
								'startcols' => 7, 
								'startrows' => 1,
								'colmap' => '{readOnly: true,renderer: "html"},{readOnly: true}, {readOnly: true,renderer: "html"},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}')
		);
	//printcool ($chart['prices']);

		foreach ($chart['prices'] as $k => $l)
		{
			if ($l['apl_rid'] == 0 || $l['apl_rid'] == "")
			{
				 if ($l['apl_adminid'] == 0 && $l['apl_nonuser'] == 1) $l['apl_rid'] = 'eBay';
				 else $l['apl_rid'] = 'NonRule';
				 $admin = $adms[(int)$l['apl_adminid']];
			}
			else
			{			
				$str = $rules[(int)$listingid][$l['apl_rid']]['daystocheck']. ' Days / ';
				if ($rules[(int)$listingid][$l['apl_rid']]['add'] == 1) $str .= 'Add ';
				else $str .= 'Deduct ';
				
				 $str .= $rules[(int)$listingid][$l['apl_rid']]['changevalue'];
				
				if ($rules[(int)$listingid][$l['apl_rid']]['isamount'] == 0) $str .= '%';
				$str .= ' until price is '.$rules[(int)$listingid][$l['apl_rid']]['rununtil'];
				
				if ($rules[(int)$listingid][$l['apl_rid']]['inform'] == 1) $str .= '(Inform)';

				$l['apl_rid'] = $str;
				$admin = $adms[(int)$l['adminassigned']];				
			}
			
			$ploaddata[] = array(cstr($l['apl_time']),cstr($l['apl_from']),cstr($l['apl_to']),cstr($l['apl_rid']),$admin, (int)$l['hasrun']);
		}
		
		foreach ($chart['sales'] as $k => $l)
		{
			if (isset($l['et_id']))
			{
			$salesdate = explode(' ',$l['datetime'] );
			$l['bcnstring'] = '';
			$l['calccosts'] = 0;
			$l['calcnetprofit'] = 0;
 			if (count($l['bcns']) > 0) foreach($l['bcns'] as $k => $v)
			{
				$l['bcnstring'] .= '<a href="'.Site_url().'Mywarehouse/bcndetails/'.$k.'" target="_blank">'.$v['bcn'].'</a>&nbsp;,&nbsp;';	
				$l['calccosts'] = $l['calccosts']+$v['cost'];	
				$l['calcnetprofit'] = $l['calcnetprofit']+$v['netprofit'];	
			}
			 $sloaddata[] = array('<a href="'.Site_url().'/Myebay/ShowOrder/'.$l['et_id'].'/1/" target="_blank">ID: '.cstr($l['et_id']).'</a>','eBay',rtrim($l['bcnstring'],',&nbsp;'),cstr($salesdate[0]),cstr($l['paid']),cstr($l['calccosts']),cstr($l['calcnetprofit']));//cstr($l['buyerid'].' - '.$l['buyeremail'])
			}
			else
			{//printcool ($l);
				$salesdate = explode(' ',$l['time'] );
				$l['bcnstring'] = '';
				if (count($l['bcns']) > 0) foreach($l['bcns'] as $k => $v)
				{
					$l['bcnstring'] .= '<a href="'.Site_url().'Mywarehouse/bcndetails/'.$k.'" target="_blank">'.$v['bcn'].'</a>&nbsp;,&nbsp;';	
					$l['calccosts'] = $l['calccosts']+$v['cost'];	
					$l['calcnetprofit'] = $l['calcnetprofit']+$v['netprofit'];				
				}
				 $sloaddata[] = array('<a href="'.Site_url().'/Myebay/ShowOrder/'.$l['oid'].'/2/" target="_blank">ID: '.cstr($l['oid']).'</a>','Website',rtrim($l['bcnstring'],',&nbsp;'),cstr($salesdate[0]),cstr($l['paid']),cstr($l['calccosts']),cstr($l['calcnetprofit']));//cstr($l['buyerid'].' - '.$l['buyeremail'])
				
			}
		}	
		
			
		$this->mysmarty->assign('pheaders', $fieldset['price']['headers']);
		$this->mysmarty->assign('pwidth', $fieldset['price']['width']);
		$this->mysmarty->assign('pstartcols', $fieldset['price']['startcols']);
		$this->mysmarty->assign('pstartrows', $fieldset['price']['startrows']);
		$this->mysmarty->assign('pcolmap', $fieldset['price']['colmap']);
		$this->mysmarty->assign('ploaddata', json_encode($ploaddata));	
		
		$this->mysmarty->assign('sheaders', $fieldset['sales']['headers']);
		$this->mysmarty->assign('swidth', $fieldset['sales']['width']);
		$this->mysmarty->assign('sstartcols', $fieldset['sales']['startcols']);
		$this->mysmarty->assign('sstartrows', $fieldset['sales']['startrows']);
		$this->mysmarty->assign('scolmap', $fieldset['sales']['colmap']);
		$this->mysmarty->assign('sloaddata', json_encode($sloaddata));	
		
		
		
		
		
		
		
		
		
		
		
		
		$this->mysmarty->assign('costs', $costs);
		$this->mysmarty->assign('ldata', $ldata);
		$this->mysmarty->assign('list', array(0=>$ldata));
		//printcool (json_encode($chart['mix']));
		$this->mysmarty->assign('prices', $chart['prices']);
		$this->mysmarty->assign('sold', $chart['sales']);
		$this->mysmarty->assign('listingid', (int)$listingid);
		$this->mysmarty->assign('blockdata', $chart['blockdata']);
		$this->mysmarty->assign('hicharts', TRUE);
		$this->mysmarty->view('myautopilot/myautopilot_chart.html');
	}
        
        
        
        
function Chart($listingid = 0,$filtertype = false,$filtersubtype = false)
	{
        $this->mysmarty->assign('chartsel',TRUE);
		$chart =  $this->Myautopilot_model->NewChart((int)$listingid,$filtertype,$filtersubtype);
		
		if (!$chart) exit('baddata');
		$ldata = false;
		//$this->db->select('e_title');
		$this->db->where('e_id',(int)$listingid);
		$ld = $this->db->get('ebay');
		if ($ld->num_rows() > 0) $ldata = $ld->row_array();	
//printcool($chart);
		$costs = false;
		$this->db->select('cost');
		$this->db->where('deleted', 0);
		$this->db->where('nr', 0);
		$this->db->where('vended', 0);
		$this->db->where('listingid', (int)$listingid);
		$q = $this->db->get('warehouse');
		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $c)
			{
				if ($c['cost'] != '') $costs[$c['cost']] = $c['cost'];					
			}
		}
		$this->load->model('Myseller_model');		
		$this->mysmarty->assign('hot',TRUE);

		$this->statuses = $this->Myseller_model->assignstatuses();

				$this->Myseller_model->getBase(array((int)$listingid));
				$this->Myseller_model->getOnHold(array((int)$listingid));
				$this->Myseller_model->countSales(array((int)$listingid));
				//$CI->Myseller_model->getEmptySales($idarray, 1);	
				$this->load->model('Myautopilot_model');
				$rules = $this->Myautopilot_model->GetListingRules(array((int)$listingid), TRUE);	
				$this->mysmarty->assign('autopilotrules', $rules);
				$this->load->model('Mycompetitor_model');
				$crules = $this->Mycompetitor_model->GetListingRules(array((int)$listingid), TRUE);
				$this->mysmarty->assign('competitorrules', $crules);
		
		$this->load->model('Myebay_model');	
		$adms = $this->Myebay_model->GetAdminList();
		$adms[0] = 'Cron';
		$this->mysmarty->assign('adm', $adms);
		$this->mysmarty->assign('prules', $this->Myautopilot_model->GetPredefined());
		
		$fieldset = array('price' => array(
									'headers' => "'Time', 'From', 'To', 'Where', 'Admin'",
									/*'rowheaders' => $list['headers'], */
									'width' => "125, 125, 125,  125, 200, 200", 
									'startcols' => 5, 
									'startrows' => 1,
									'colmap' => '{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}'),
						'sales' => array(
								'headers' => "'Order', 'Where', 'Bcns', 'Sale Date', 'Price', 'Cost', 'Profit'",
								/*'rowheaders' => $list['headers'], */
								'width' => "155, 100, 350,125, 70,70,70", 
								'startcols' => 7, 
								'startrows' => 1,
								'colmap' => '{readOnly: true,renderer: "html"},{readOnly: true}, {readOnly: true,renderer: "html"},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}')
		);

    	foreach ($chart['prices'] as $k => $l)
		{
			if (isset($l['apl_rid']))
			{
				if ($l['apl_rid'] == 0 || $l['apl_rid'] == "")
				{
					 if ($l['apl_adminid'] == 0 && $l['apl_nonuser'] == 1) $l['apl_rid'] = 'eBay';
					 else $l['apl_rid'] = 'NonRule';
					 $admin = $adms[(int)$l['apl_adminid']];
				}
				else
				{			
					$str = $rules[(int)$listingid][$l['apl_rid']]['daystocheck']. ' Days / ';
					if ($rules[(int)$listingid][$l['apl_rid']]['add'] == 1) $str .= 'Add ';
					else $str .= 'Deduct ';
					
					$str .= $rules[(int)$listingid][$l['apl_rid']]['changevalue'];
					
					if ($rules[(int)$listingid][$l['apl_rid']]['isamount'] == 0) $str .= '%';
					$str .= ' until price is '.$rules[(int)$listingid][$l['apl_rid']]['rununtil'];
					
					if ($rules[(int)$listingid][$l['apl_rid']]['inform'] == 1) $str .= '(Inform)';
	
					$l['apl_rid'] = $str;
					$admin = $adms[(int)$l['adminassigned']];				
				}
				$ploaddata[] = array(cstr($l['apl_time']),cstr($l['apl_from']),cstr($l['apl_to']),cstr($l['apl_rid']),$admin, (int)$l['hasrun']);
			}
			else
			{//printcool ($l);
				$ploaddata[] = array(cstr($l['time']),floater(cstr($l['datafrom'])),floater(cstr($l['datato'])),cstr($l['ctrl']),cstr($l['admin']), 0);
				
			}
		}
   // printcool ($chart['sales']);
		foreach ($chart['sales'] as $k => $l)
		{
            if ($l['channel'] == 4) $salesdate = explode(' ', $l['paid']);
            else $salesdate = explode(' | ', $l['created']);
			$l['bcnstring'] = '';
			$l['calccosts'] = 0;
			$l['calcnetprofit'] = 0;
            if ($l['channel'] == 4) $l['pricepaid'] = 0;
            else $l['pricepaid'] = $l['paid'];

 			if (count($l['bcns']) > 0) foreach($l['bcns'] as $k => $v)
			{
				$l['bcnstring'] .= '<a href="'.Site_url().'Mywarehouse/bcndetails/'.$k.'" target="_blank">'.$v['bcn'].'</a>&nbsp;,&nbsp;';	
				$l['calccosts'] = $l['calccosts']+$v['cost'];	
				$l['calcnetprofit'] = $l['calcnetprofit']+$v['netprofit'];
				if ($l['channel'] == 4)
                {
                    $l['pricepaid'] = $l['pricepaid']+$v['paid'];
                }
			}
			 $sloaddata[] = array('<a href="'.Site_url().'/Myebay/ShowOrder/'.$l['orderkey'].'/'.$l['channel'].'/" target="_blank">ID: '.cstr($l['orderkey']).'</a>',$l['typekey'],rtrim($l['bcnstring'],',&nbsp;'),cstr($salesdate[0]),cstr($l['pricepaid']),cstr($l['calccosts']),cstr($l['calcnetprofit']));//cstr($l['buyerid'].' - '.$l['buyeremail'])
        }
			
		$this->mysmarty->assign('pheaders', $fieldset['price']['headers']);
		$this->mysmarty->assign('pwidth', $fieldset['price']['width']);
		$this->mysmarty->assign('pstartcols', $fieldset['price']['startcols']);
		$this->mysmarty->assign('pstartrows', $fieldset['price']['startrows']);
		$this->mysmarty->assign('pcolmap', $fieldset['price']['colmap']);
		$this->mysmarty->assign('ploaddata', json_encode($ploaddata));	
		
		$this->mysmarty->assign('sheaders', $fieldset['sales']['headers']);
		$this->mysmarty->assign('swidth', $fieldset['sales']['width']);
		$this->mysmarty->assign('sstartcols', $fieldset['sales']['startcols']);
		$this->mysmarty->assign('sstartrows', $fieldset['sales']['startrows']);
		$this->mysmarty->assign('scolmap', $fieldset['sales']['colmap']);
		$this->mysmarty->assign('sloaddata', json_encode($sloaddata));
		
		
		
		
		
		$this->mysmarty->assign('costs', $costs);
		$this->mysmarty->assign('ldata', $ldata);
		$this->mysmarty->assign('list', array(0=>$ldata));
		//printcool (json_encode($chart['mix']));
		$this->mysmarty->assign('prices', $chart['prices']);
		$this->mysmarty->assign('sold', $chart['sales']);
		$this->mysmarty->assign('listingid', (int)$listingid);
		$this->mysmarty->assign('blockdata', $chart['blockdata']);
		$this->mysmarty->assign('hicharts', TRUE);
		$this->mysmarty->view('myautopilot/myautopilot_newchart.html');
	}
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
	function  tc($listingid = 0)
	{

		$chart =  $this->Myautopilot_model->gChart((int)$listingid); 
		if (!$chart) $chart = array('price' => array(), 'sales' => array());
		$ldata = false;
		$this->db->select('e_title');
		$this->db->where('e_id',(int)$listingid);
		$ld = $this->db->get('ebay');
		if ($ld->num_rows() > 0) $ldata = $ld->row_array();	

		$this->mysmarty->assign('ldata', $ldata);
		//printcool (json_encode($chart['mix']));
		$this->mysmarty->assign('price', $chart['results']);
		$this->mysmarty->assign('sales', $chart['sales']);
		$this->mysmarty->assign('listingid', (int)$listingid);
		$this->mysmarty->assign('googlechart', json_encode($chart['mix']));
		$this->mysmarty->assign('hicharts', TRUE);
		$this->mysmarty->view('myautopilot/myautopilot_chart.html');
	}
}