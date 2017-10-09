<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends Model 
{
    function Login_model()
    {
        parent::Model();
    }

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

function ConfirmRegistration($confirm_code, $id = '') 
	{
		$this->db->select('user_id, email');
		$this->query = $this->db->get_where('users', array('confirm_code' => $confirm_code, 'user_id' => (int)$id, 'active' => "-1"), 1);
					if ($this->query->num_rows() > 0) {
					$this->user = $this->query->row_array();		
					
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
	return $this->db->insert_id();
	}
function InsertUserAddress($data) 
	{
	$this->db->insert('users_addresses',$data);
	}
function CheckUser($email,$pass, $getaddress = false)
	{	
		$this->db->select('user_id, fname, lname, email, active, reg_date, discount');
		$this->getresult = $this->db->get_where('users',array('email' => strtolower($email), 'pass' => $pass),1);
		if ($this->getresult->num_rows() > 0) {
			$this->founduser = $this->getresult->row_array();
						if ($this->founduser['active'] == 1) 
						{
								$this->db->select('ul_datein, ul_ip');
								$this->db->order_by("ul_datein", "DESC");
								$this->getresult = $this->db->get_where('users_logs',array('ul_userid' => (int)$this->founduser['user_id']),1);
			
								if ($this->getresult->num_rows() > 0) 
								{
									$this->last = $this->getresult->row();
									$this->founduser['ul_datein'] = $this->last->ul_datein;
									$this->founduser['ul_ip'] = $this->last->ul_ip;
								}
								else 
								{
									$this->last = '';
									$this->founduser['ul_datein'] = '';
									$this->founduser['ul_ip'] = '';
								}

						
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
		$this->db->select('user_id, email');
		$this->query = $this->db->get_where('users', array('email' => strtolower($email)), 1);
					if ($this->query->num_rows() > 0) 
					{
						$this->user = $this->query->row();	
						$this->load->helper('arithmetic');
						$this->newpass = rand_string(7);
						$this->db->where('user_id', (int)$this->user->user_id);
						$this->db->limit(1);
						$this->db->update('users', array('pass' => md5(md5($this->newpass))));	
					
						$this->return_data = array (
												'email' => strtolower($this->user->email),
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
	
function CheckUsernameExists($username)
	{
		$this->db->select('name');
		$this->query = $this->db->get_where('users', array('name' => $username), 1);
			if ($this->query->num_rows() > 0) 
			{
				return TRUE;
			}
			else
			{
				return FALSE;	
			}
	}

function CheckEmailExists($email)
	{
		$this->db->select('email');
		$this->query = $this->db->get_where('users', array('email' => strtolower($email)), 1);
			if ($this->query->num_rows() > 0) 
			{ 
				return TRUE; 
			}
			else
			{
				return FALSE; 	
			}
	}
	
}
?>
