<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auth_model extends Model 
{
    function Auth_model()
    {
        parent::Model();
		
		$this->db->select("skey, svalue");
		$this->db->where('skey', 'StoreCart');
		$this->db->or_where('skey', 'googledriveuse');
		$this->db->or_where('skey', 'ebayresponse');
		$q = $this->db->get('settings');
		$storecart = 0;
		$gdrv = 0;
		if ($q->num_rows() > 0) 
			{
				foreach($q->result_array() as $v)
				{
				if ($v['skey'] == 'googledriveuse') $this->gdrv = $v['svalue'];
				elseif ($v['skey'] == 'ebayresponse') $ebr = $v['svalue'];
				else $storecart = $v['svalue'];
				}
			}
		$this->mysmarty->assign('StoreCart', (int)$storecart);
		$this->mysmarty->assign('gDrv', (int)$this->gdrv);
		$this->mysmarty->assign('ebr', $ebr);
		$this->PendingRevision();
				
    }
function wlog($bcn, $id, $field, $from, $to, $place = false, $url = false)
{
	if (isset($this->session->userdata['admin_id'])) $admin = $this->session->userdata['admin_id'];
	else $admin = 'Cron';
	if (!$place) $place = $this->router->method;	
	if (!$url) $url = $place;
	
	if (($bcn && $bcn == '') || !$bcn || ($id && $id == '') || !$id)
	{
		GoMail(array ('msg_title' => 'wLog Null @ '.CurrentTime(), 'msg_body' => printcool ($bcn, true,'bcn').'<br>'.printcool ($id, true,'id')."<br><br>array('bcn' => ".$bcn.", 'wid'=> ".$id.", 'time' => ".CurrentTime().", 'ts' => ".mktime().",'datafrom' => ".$from.", 'datato' => ".$to.", 'field' => ".$field.", 'admin' => ".$admin.", 'ctrl' => ".$place.", 'url' => ".$url.", 'year' => ".date('Y').", 'month' =>". date('m').", 'day' => ".date('d').")", 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);		
	}
	$this->db->insert('warehouse_log', array('bcn' => $bcn, 'wid'=> $id, 'time' => CurrentTime(), 'ts' => mktime(),'datafrom' => $from, 'datato' => $to, 'field' => $field, 'admin' => $admin, 'ctrl' => $place, 'url' => $url, 'year' => date('Y'), 'month' => date('m'), 'day' => date('d')));	
}
function newlog($bcn, $id, $field, $val, $place = false, $url = false)
{
	if (isset($this->session->userdata['admin_id'])) $admin = $this->session->userdata['admin_id'];
	else $admin = 'Cron';
	if (!$place) $place = $this->router->method;	
	if (!$url) $url = $place;
	$this->db->insert('warehouse_log', array('bcn' => $bcn, 'wid'=> $id, 'time' => CurrentTime(), 'ts' => mktime(), 'datafrom' => 'NEW', 'datato' => $val, 'field' => $field, 'admin' => $admin, 'ctrl' => $place, 'url' => $url, 'year' => date('Y'), 'month' => date('m'), 'day' => date('d')));	
}
function wgetval($id, $field)
{
	$this->db->select($field);
	$this->db->where('wid', (int)$id);
	$q = $this->db->get('warehouse');
	if ($q->num_rows() > 0) 
	{
		$r = $q->row_array();
		return $r[$field];
	}
}
function gDrv()
{
		$this->db->select("svalue");
		$this->db->where('skey', 'googledriveuse');
		$q = $this->db->get('settings');
		$gdrv = 0;
		if ($q->num_rows() > 0) 
			{
				$gdrv = $q->row_array();
				$gdrv = (int)$gdrv['svalue'];
				if ($gdrv > 2) $gdrv = 0;
			}
		return $gdrv;	
}
function VerifyUser() 
	{
		if (isset($this->session->userdata['user_id'])) {
		$this->userid = (int)$this->session->userdata['user_id'];
		$this->db->select('user_id, active, discount');
		$this->query = $this->db->get_where('users', array('user_id' => $this->userid), 1);
			if ($this->query->num_rows() > 0) 
			{
			foreach ($this->query->result() as $row)
				{
					$userdata['user_id'] = $row->user_id;
					$userdata['active'] = $row->active;
					$userdata['discount'] = $row->discount;
				}
			}
		$this->bad = 0;
		if (!isset($userdata)) $this->bad++;
		if (isset($userdata)) {
			if ($userdata['user_id'] != $this->userid) $this->bad++;
			if ($userdata['active'] != 1) $this->bad++;
			}
		}
		else
		{
		$this->bad = 1;
		}
		if ($this->bad > 0 )
			{
			$this->session->sess_destroy();
			redirect("/Login/");
			exit();
			break;
			}		
		else
		{
			return $this->session->set_userdata('discount', $userdata['discount']); 
		}
	}
	
function VerifyAdmin()
	{	
		if (isset($this->session->userdata['admin_id'])) {
			
		$this->adminid = (int)$this->session->userdata['admin_id'];
		$this->db->select('admin_id, active');
		$this->query = $this->db->get_where('administrators', array('admin_id' => $this->adminid), 1);
			if ($this->query->num_rows() > 0) 
			{
			foreach ($this->query->result() as $row)
				{
					$admindata['admin_id'] = $row->admin_id;
					$admindata['active'] = $row->active;
				}
			}
		$this->bad = 0;
		if (!isset($admindata)) $this->bad++;
		if (isset($admindata)) {
			if ($admindata['admin_id'] != $this->adminid) $this->bad++;
			if ($admindata['active'] != 1) $this->bad++;
				  }
		}
		else
		{
		$this->bad = 1;
		}
		if ($this->bad > 0 )
			{
			$this->session->sess_destroy();
			redirect("/Myadmin/");
			exit();		
			}	
		$this->mysmarty->assign('class', ucfirst(str_replace('My', '', $this->router->class)));
		$this->mysmarty->assign('method', ucfirst($this->router->method));
	}
function PendingRevision()
{
	$q = $this->db->count_all_results('ebay_revise');
	$this->mysmarty->assign('ebay_revise', $q);
}
function CheckAccess($id) 
	{
		if ((int)$id == 0) 
		{	
			$this->session->sess_destroy();
			redirect("/Login/");
			exit();
			break;
		}
	}

function CheckAdminAccess($id) 
	{
		if ((int)$id == 0) 
		{	
			$this->session->sess_destroy();
			redirect("/Myadmin/");
			exit();
			break;
		}
	}
function CheckRole()
	{
		if ($this->session->userdata['type'] == 'helper') exit('You do not have acceess to this module.');		
	}
function CheckWarehouse()
	{
		if ($this->session->userdata['type'] == 'helper' && $this->session->userdata['warehouse'] == 0) exit('You do not have acceess to the Warehouse module.');		
	}
function CheckListings()
	{
		if ($this->session->userdata['type'] == 'helper' && $this->session->userdata['listings'] == 0) exit('You do not have acceess to the Listings module.');		
	}	
function CheckOrders()
	{
		if ($this->session->userdata['type'] == 'helper' && $this->session->userdata['orders'] == 0) exit('You do not have acceess to the Orders module.');		
	}


}
?>
