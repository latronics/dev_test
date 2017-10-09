<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myadmin_model extends Model 
{
    function Myadmin_model()
    {
        parent::Model();
    }

function RecordLogout ($adminid,$datetime) 
	{
	$this->db->where('al_adminid', (int)$adminid);
	$this->db->where('al_datein', $datetime);
	$this->db->update('administrators_logs', array('al_dateout' => CurrentTime()));	
	}

function RecordLogin($adminid,$datetime)    
	{	
		$this->load->library('user_agent');
		
		$this->log_data = array (	
					   	'al_adminid' => (int)$adminid,
						'al_ip' => $this->input->ip_address(),						
						'al_agent' => $this->agent->agent_string(),
						'al_platform' => $this->agent->platform(),
						'al_datein' => $datetime,
						'al_ref' => $this->agent->referrer()
					   );	
		$this->db->insert('administrators_logs',$this->log_data);
	}

	
function InsertHistoryData($history_data) 
	{
	$this->db->insert('admin_history',$history_data);	
	}
		
function CheckUser($user,$pass)
	{	
		$this->db->select('admin_id, type, name, ownnames, email, active, warehouse, listings, orders, accounting, lastip,auclimit');
		$this->getresult = $this->db->get_where('administrators',array('name' => $user, 'pass' => $pass),1);
		if ($this->getresult->num_rows() > 0) {
			$this->founduser = $this->getresult->row_array();
						if ($this->founduser['active'] == 1) 
						{
								$this->db->select('al_datein, al_ip');
								$this->db->order_by("al_datein", "DESC");	
								$this->getresult = $this->db->get_where('administrators_logs',array('al_adminid' => (int)$this->founduser['admin_id']), 1);
			
								if ($this->getresult->num_rows() > 0) 
								{
									$this->last = $this->getresult->row();
									$this->founduser['al_datein'] = $this->last->al_datein;
									$this->founduser['al_ip'] = $this->last->al_ip;
								}
								else 
								{
									$this->last = '';
									$this->founduser['al_datein'] = '';
									$this->founduser['al_ip'] = '';
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
		$this->db->select('admin_id, name, email');
		$this->query = $this->db->get_where('administrators', array('email' => $email), 1);
					if ($this->query->num_rows() > 0) 
					{
						$this->user = $this->query->row();	
						$this->load->helper('arithmetic');
						$this->newpass = rand_string(7);
						$this->db->where('admin_id', (int)$this->user->admin_id);
						$this->db->update('administrators', array('pass' => md5(md5($this->newpass))));	
					
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
	
	
}
?>
