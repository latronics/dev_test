<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mystart_model extends Model 
{
    function Mystart_model()
    {
        parent::Model();	
		
    }


function GetHistory($page = '', $all = TRUE)
	{
		if ((int)$page > 0) $page = $page - 1;
		$this->db->limit(200, (int)$page*200);
		if (!$all) { $this->db->where('sev', 1);  $this->db->where('code !=', 2); }
		$this->db->order_by("msg_id", "DESC");
		$this->query = $this->db->get('admin_history');		
			
			if (!$all) $this->db->where('sev', 1);
			$this->countall = $this->db->count_all_results('admin_history');
			$this->pages = ceil($this->countall/200);
			for ( $counter = 1; $counter <= $this->pages ; $counter++) 
			{
			$this->pagearray[] = $counter;
			}
			
		

		if ($this->query->num_rows() > 0) 
			{
			return array('results' => $this->query->result_array(), 'pages' => $this->pagearray);
			}	
	}
function GetNoBcnHistory($page = '', $all = TRUE)
	{
		if ((int)$page > 0) $page = $page - 1;
		if (!$all) { $this->db->where('msg_title', '<span style="color:red;">Cannot auto allocate BCN in Warehouse</span> from Listing to Order'); }
		$this->db->order_by("msg_id", "DESC");
		$this->query = $this->db->get('admin_history');		
			
			if (!$all) $this->db->where('msg_title', '<span style="color:red;">Cannot auto allocate BCN in Warehouse</span> from Listing to Order'); 
			$this->countall = $this->db->count_all_results('admin_history');
			$this->pages = 1;
			for ( $counter = 1; $counter <= $this->pages ; $counter++) 
			{
			$this->pagearray[] = $counter;
			}
			
		

		if ($this->query->num_rows() > 0) 
			{
			return array('results' => $this->query->result_array(), 'pages' => $this->pagearray);
			}	
	}	
function PurgeHistory() 
	{
		$this->db->truncate('admin_history');	
	}
	
function DeleteOlderHistory($days)
	{
		$this->db->query("DELETE FROM admin_history WHERE msg_date < DATE_SUB( NOW( ),INTERVAL ".(int)$days." DAY ) ");
	}

function GetAllLogs($userid = '', $page = '')
	{
		if ((int)$userid > 0) $this->db->where('ul_userid', (int)$userid);
		$this->db->order_by("ul_datein", "DESC");
		if ((int)$page > 0) $page = $page - 1;
		$this->db->limit(15, (int)$page*15);
		$this->query = $this->db->get('users_logs');

			if ((int)$userid > 0) $this->db->where('ul_userid', (int)$userid);			
			$this->countall = $this->db->count_all_results('users_logs');
			$this->pages = ceil($this->countall/15);
			for ( $counter = 1; $counter <= $this->pages ; $counter++) 
			{
			$this->pagearray[] = $counter;
			}
			
		

		if ($this->query->num_rows() > 0) 
			{

			return array('results' => $this->query->result_array(), 'pages' => $this->pagearray);
			}	
	}

function GetUserDetails($userid)
	{
		if ($userid > 0) 
		{
		//$this->db->distinct();
		$this->db->select("user_id, fname, lname, email, active, reg_date");
		$this->db->where('user_id', (int)$userid);
		}
		$this->db->order_by("reg_date", "DESC");
		$this->query = $this->db->get('users');

		if ($this->query->num_rows() > 0) 
			{
				foreach ($this->query->result() as $row)
				{
					if ($userid > 0) 
					{
						$result['user_id'] = $row->user_id;
						$result['fname'] = $row->fname;
						$result['lname'] = $row->lname;
						$result['email'] = $row->email;
						$result['active'] = $row->active;
						$result['reg_date'] = $row->reg_date;
					}
					else 
					{
						$result[$row->user_id]['user_id'] = $row->user_id;
						$result[$row->user_id]['fname'] = $row->fname;
						$result[$row->user_id]['lname'] = $row->lname;
						$result[$row->user_id]['email'] = $row->email;
						$result[$row->user_id]['active'] = $row->active;
						$result[$row->user_id]['reg_date'] = $row->reg_date;						
					}
				}
			return $result;
			}	
	}

function PurgeIpLogs() 
	{
		$this->db->truncate('users_logs');	
	}
	
function DeleteOlderIpLogs($days)
	{
		$this->db->query("DELETE FROM users_logs WHERE ul_datein < DATE_SUB( NOW( ),INTERVAL ".(int)$days." DAY ) ");
	}
function GetAllAdminLogs($adminid, $page = '')
	{
		if ($adminid > 0) $this->db->where('al_adminid', (int)$adminid);
		$this->db->order_by("al_datein", "DESC");
		if ((int)$page > 0) $page = $page - 1;
		$this->db->limit(200, (int)$page*200);
		$this->query = $this->db->get('administrators_logs');
		
			if ($adminid > 0) $this->db->where('al_adminid', (int)$adminid);
			$this->countall = $this->db->count_all_results('administrators_logs');
			$this->pages = ceil($this->countall/200);
			for ( $counter = 1; $counter <= $this->pages ; $counter++) 
			{
			$this->pagearray[] = $counter;
			}
	

		if ($this->query->num_rows() > 0) 
			{
			return array('results' => $this->query->result_array(), 'pages' => $this->pagearray);
			}	else
		{
			return 0;
		}
	}

function GetAdminDetails($adminid)
	{
		if ($adminid > 0) 
		{
		//$this->db->distinct();
		$this->db->select("admin_id, name, ownnames, email, active");
		$this->db->where('admin_id', (int)$adminid);
		}
		$this->db->order_by("admin_id", "ASC");
		$this->query = $this->db->get('administrators');

		if ($this->query->num_rows() > 0) 
			{
				foreach ($this->query->result() as $row)
				{
					if ($adminid > 0) 
					{
						$result['admin_id'] = $row->admin_id;
						$result['name'] = $row->name;
						$result['ownnames'] = $row->ownnames;
						$result['email'] = $row->email;
						$result['active'] = $row->active;
					}
					else 
					{
						$result[$row->admin_id]['admin_id'] = $row->admin_id;
						$result[$row->admin_id]['name'] = $row->name;
						$result[$row->admin_id]['ownnames'] = $row->ownnames;
						$result[$row->admin_id]['email'] = $row->email;
						$result[$row->admin_id]['active'] = $row->active;
					}
				}
			return $result;
			}	
	}

function PurgeAdminLogs() 
	{
	$this->db->truncate('administrators_logs');	
	}
	
function DeleteOlderAdminLogs($days)
	{
	$this->db->query("DELETE FROM administrators_logs WHERE al_datein < DATE_SUB( NOW( ),INTERVAL ".(int)$days." DAY ) ");
	}
	
function FindAdminDetails ($adminid) 
	{
		$this->db->select('admin_id, pass, name, email, ownnames');
		$this->getresult = $this->db->get_where('administrators',array('admin_id' => (int)$adminid),1);
			if ($this->getresult->num_rows() > 0) 
			{
				$this->found = $this->getresult->row_array();
			}
			else 
			{
				$this->found = FALSE;
			}
		return 	$this->found;	
	}
	
function CheckPassMatch ($adminid, $pass) 
	{
		$this->db->select('admin_id, pass');
		$this->getresult = $this->db->get_where('administrators',array('admin_id' => (int)$adminid),1);
			if ($this->getresult->num_rows() > 0) 
			{	
				$this->found = $this->getresult->row_array();
				if ($this->found['pass'] == $pass) 
					{
					$this->toupdate = (int)$this->found['admin_id'];
					}
					else 
					{
					$this->toupdate = FALSE;
					}
			}
			else 
			{
				$this->toupdate = FALSE;
			}
		return 	$this->toupdate;	
	}

function UpdateAllAdminDetails($admin_id, $newpass, $email, $ownnames)
	{
		$this->db->where('admin_id',(int)$admin_id);
		$this->db->update('administrators', array('pass' => $newpass, 'ownnames' => $ownnames, 'email' => $email));			
	}
	
function UpdateAdminDetails($admin_id, $email, $ownnames)
	{
		$this->db->where('admin_id', (int)$admin_id);
		$this->db->update('administrators', array('ownnames' => $ownnames, 'email' => $email));			
	}
function InsertHistoryData($history_data) 
	{
		$this->db->insert('admin_history',$history_data);	
	}

function GetAllDeadEnds()
	{
		$this->db->order_by("d_date", "DESC");
		$this->query = $this->db->get('dead_ends');

		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}

function PurgeDeadEnds() 
	{
	$this->db->truncate('dead_ends');	
	}
	
function DeleteOlderDeadEnds($days)
	{
	$this->db->query("DELETE FROM dead_ends WHERE d_date < DATE_SUB( NOW( ),INTERVAL ".(int)$days." DAY ) ");
	}






/*

	if ($userid > 0) 
		{
		$sql = "SELECT 
				us.user_id, us.name, ul.ul_id, ul.ul_userid, ul.ul_ip, ul.ul_agent, ul.ul_platform, ul.ul_datein, ul.ul_dateout, ul.ul_ref 
				FROM users_logs AS ul, users AS us 
				WHERE ul.ul_userid = us.user_id 
				AND ul.ul_userid = '".(int)$userid."'
				ORDER BY ul_datein DESC
				"; 
		}
		else 
		{
		$sql = "SELECT 
				us.user_id, us.name, ul.ul_id, ul.ul_userid, ul.ul_ip, ul.ul_agent, ul.ul_platform, ul.ul_datein, ul.ul_dateout, ul.ul_ref 
				FROM users_logs AS ul, users AS us 
				WHERE ul.ul_userid = us.user_id 
				ORDER BY ul_datein DESC
				"; 
		}
		
		$result = $this->db->query($sql);
				if ($result->num_rows() > 0) 
					{
						return $result->result_array();
					}


*/

/*
function RecordLogout ($userid,$datetime) 
	{
	$this->db->where('ul_userid', (int)$userid);
	$this->db->where('ul_datein', $datetime);
	$this->db->update('users_logs', array('ul_dateout' => CurrentTime()));	
	}

function RecordLogin($userid,$datetime)    
	{	
		$this->load->library('user_agent');
		
		$this->log_data = array (	
					   	'ul_userid' => (int)$userid,
						'ul_ip' => $this->input->ip_address(),						
						'ul_agent' => $this->agent->agent_string(),
						'ul_platform' => $this->agent->platform(),
						'ul_datein' => $datetime,
						'ul_ref' => $this->agent->referrer()
					   );	
		$this->db->insert('users_logs',$this->log_data);
	}

function ConfirmRegistration($confirm_code) 
	{
		$this->db->select('user_id, name');
		$this->query = $this->db->get_where('users', array('confirm_code' => $this->input->xss_clean($confirm_code), 'active' => "-1"), 1);
					if ($this->query->num_rows() > 0) {
					$this->user = $this->query->result();		
					
					$this->db->where('user_id', (int)$this->user['user_id']);
					$this->db->update('users', array('active' => 1));	
					
					return $this->user;
		}
	}
	
function InsertHistoryData($history_data) 
	{
	$this->db->insert('admin_history',$history_data);	
	}
		
function InsertUser($reg_data) 
	{
	$this->db->insert('users',$reg_data);
	}

function CheckUser($user,$pass)
	{	
		$this->db->select('user_id, name, fname, lname, email, active, reg_date');
		$this->getresult = $this->db->get_where('users',array('name' => $user, 'pass' => $pass),1);
		if ($this->getresult->num_rows() > 0) {
			$this->founduser = $this->getresult->row_array();
						if ($this->founduser['active'] == 1) 
						{
								$this->db->select_max('ul_datein');
								$this->db->select('ul_ip');
								$this->db->group_by('ul_ip');
								$this->getresult = $this->db->get_where('users_logs',array('ul_userid' => (int)$this->founduser['user_id']));
			
								if ($this->getresult->num_rows() > 0) 
								{
									$this->last = $this->getresult->row();
								}
								else 
								{
									$this->last = '';
								}

						$this->founduser['ul_datein'] = $this->last->ul_datein;
						$this->founduser['ul_ip'] = $this->last->ul_ip;
						}
			unset($this->founduser['pass']);
			$this->founduser['datein'] = CurrentTime();
			
		}
		else {
			$this->founduser = FALSE;
		}
		return 	$this->founduser;	
	}
	
function HandleNewUserPassword($email) 
	{
		$this->db->select('user_id, name, email');
		$this->query = $this->db->get_where('users', array('email' => $email), 1);
					if ($this->query->num_rows() > 0) 
					{
						$this->user = $this->query->row();	
						$this->load->helper('arithmetic');
						$this->newpass = rand_string(7);
						$this->db->where('user_id', (int)$this->user->user_id);
						$this->db->update('users', array('pass' => md5(md5($this->newpass))));	
					
						$this->return_data = array ('name' => $this->user->name,
												'email' => $this->user->email,
												'newpassword' => $this->newpass,
												'ip' => $this->input->ip_address(),
												'time' => CurrentTime()
												);
						return ($this->return_data);
					}
					else 
					{
						return FALSE;
					}
	}
	
	*/
}
?>
