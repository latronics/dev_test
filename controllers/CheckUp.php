<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CheckUp extends Controller {
	
	function CheckUp()
	{
		parent::Controller();

	}
	
	function index()
	{	
	
	$day = date('D');
	
	if ($day != 'Tue' && $day != 'Fri') exit();

	$nowmk = (int)mktime(0, 0, 0, date('m'), date('d'), date('Y'));
	
		$lastmk = '0000000000';
		$this->db->select("lastdate");
		$this->db->where('chkid', 1);
		$this->query = $this->db->get('checkup');
		if ($this->query->num_rows() > 0) 
			{
			 $found = $this->query->row_array();
			 $lastmk = $found['lastdate'];
			}
			
	$proceed = FALSE;		
	if ($nowmk > $lastmk) $proceed = TRUE;
	
	////
	//$proceed = FALSE;
	//if ($_SERVER['REMOTE_ADDR'] == '93.152.154.46' || $_SERVER['REMOTE_ADDR'] == '93.152.144.229' || $_SERVER['REMOTE_ADDR'] == '87.121.99.3' || $_SERVER['REMOTE_ADDR'] == '87.121.161.130')	$proceed = TRUE;
	////
	
	
	if ($proceed) 
		{ 
			$str = '';
			
			$this->_CheckOrders();
			if ($this->orderstxt != '') $str .= $this->orderstxt.'<br>';
			
			$this->_CheckZeros();
			if ($this->zerotxt != '') $str .= $this->zerotxt.'<br>';

			if ($str != '') 
				{
					$str = 'Good morning 365 team ! - '.date("l  d.M.Y").' <span style="font-size:10px;">Auto generated @ '.date("H:i:s").'</span>
					<br><br>For today\'s attention:<br><br>'.$str.'Have a nice day!';
					GoMail (array('msg_title' => 'Good morning 365 team. For today\'s attention - '.date("l d.M.Y"), 'msg_body' => $str));
					
					$this->load->model('Login_model');
					$this->Login_model->InsertHistoryData(
														  array('msg_title' => 'Good morning 365 team. For today\'s attention - '.date("l d.M.Y"),
																'msg_body' => $str, 
																'msg_date' => CurrentTime()
															   )
														  );
				}
		}
	}
	
	function _CheckOrders()
	{
		$this->orderstxt = '';
		
		$sql = "SELECT oid, oid_ref, complete, fname, lname, city, time, buytype, subtype, status, endprice, fid, payproc, sysdata, admin, rid FROM orders WHERE (complete = 1 AND returnedresponse = 1 AND test = 0) OR (buytype = 2 OR buytype = 4 OR buytype = 6 OR buytype = 7 OR buytype = 8) ORDER BY time DESC"; 

		$this->query = $this->db->query($sql);

		if ($this->query->num_rows() > 0) 
			{
				
			$skip = array(101136,100721,100701,100648,100619);
			
			$nowmk = (int)mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			foreach ($this->query->result_array() as $k => $v)	
				{
				if ($v['status'] != '' && $v['status'] != ' ') {
										$v['status'] = unserialize($v['status']);																		
										$v['status'] = end($v['status']);
										}
		
				$v['mktime'] = explode(' ', $v['time']);
				$v['mktime'] = explode('-', $v['mktime'][0]);			
				if (isset($v['mktime'][1]) && isset($v['mktime'][2]) && isset($v['mktime'][0])) $v['mktime'] = (int)mktime(0, 0, 0, $v['mktime'][1], $v['mktime'][2], $v['mktime'][0]);
				else $v['mktime'] = false;	
				
				if (!in_array($v['oid'], $skip))
				{
				if ((int)$v['buytype'] == 1)
					{
						if ($v['subtype'] != 'r')
							{
								if ((($nowmk - $v['mktime']) > 172800) && $v['complete'] == 1 && $v['status']['status'] == 1) 
								{
								$this->orderstxt .= 'Website Order <a href="'.Site_url().'Myorders/Show/'.$v['oid'].'" target="_blank" style="text-decoration:underline; color:orange;">'.$v['oid'].'</a> is 3 days or older and still is "New Order".<br>';									
								};
							}
					}
				elseif ((int)$v['buytype'] == 6 || (int)$v['buytype'] == 7 || (int)$v['buytype'] == 8)
					{
						if ($v['subtype'] != 'p')
							{
								if ((($nowmk - $v['mktime']) > 172800) && ($v['status']['status'] == 8 || $v['status']['status'] == 1)) 
								{
									$this->orderstxt .= 'Store Order <a href="'.Site_url().'Myorders/ShopRepair/'.$v['oid'].'" target="_blank" style="text-decoration:underline; color:orange;">'.$v['oid'].'</a> '.($v['admin'] ? '('.$v['admin'].')' : '').' is 3 days or older and still "Diagnosing".<br>';		
								}
							}
						if ($v['subtype'] == 'p' && $v['endprice'] == 0)
							{
								$this->orderstxt .= 'Store Order <a href="'.Site_url().'Myorders/ShopOrder/'.$v['oid'].'" target="_blank" style="text-decoration:underline; color:orange;">'.$v['oid'].'</a> '.($v['admin'] ? '('.$v['admin'].')' : '').' is a product sale and has no sale price.<br>';
							}
						if ($v['subtype'] == 'r' && $v['endprice'] == 0 && ($v['status']['status'] == 5 || $v['status']['status'] == 7 || $v['status']['status'] == 11 || $v['status']['status'] == 12))
							{
								$this->orderstxt .= 'Store Order <a href="'.Site_url().'Myorders/ShopRepair/'.$v['oid'].'" target="_blank" style="text-decoration:underline; color:orange;">'.$v['oid'].'</a> '.($v['admin'] ? '('.$v['admin'].')' : '').' is "completed/accepted" and has no actual repair price.<br>';
							}
							
							
					}
				}
				}
			}				
	}
	
function _CheckZeros()
	{		
		$this->load->model('Myproducts_model');
		$this->zerodata = $this->Myproducts_model->CheckWeightZero();
		
		$this->zerotxt = '';
		if ($this->zerodata)
				{
					if ($this->zerodata['weightzero'] > 0) $this->zerotxt .= '<a href="{$site_url}Myproducts/Zero" targer="_blank" style="text-decoration:underline; color:orange;">There are ITEMS with zero WEIGHT!</a><br>';
					if ($this->zerodata['pricezero'] > 0) $this->zerotxt .= '<a href="{$site_url}Myproducts/Zero" targer="_blank" style="text-decoration:underline; color:orange;">There are PRODUCTS / PREDEFINED REPAIRS with zero PRICE!</a><br />';
					if ($this->zerodata['quantityzero'] > 0) $this->zerotxt .= '<a href="{$site_url}Myproducts/Zero" targer="_blank" style="text-decoration:underline; color:orange;">There are PRODUCTS with zero QUANTITY!</a><br />';				
					
				}
	}
	
}