<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myadmins extends Controller {

function Myadmins()
	{
		parent::Controller();		
		$this->load->model('Myadmins_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		if ($this->session->userdata['type'] != 'master') { echo "Sorry, you don't have clearance for here."; exit();}						
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Admins');
		//printcool ($this->session->userdata);
	}
	
function index()
	{
        $this->load->model('Mywarehouse_model');
        $this->mysmarty->assign('acat', $this->Mywarehouse_model->GetAuctionCategories());
        $this->mysmarty->assign('admins', $this->Myadmins_model->GetAllUsers());
		$this->mysmarty->view('myadmins/myadmins_main.html');
	}	
function ShowUser($userid = '')
	{	
		$this->id = (int)$userid;	
		if ($this->id > 0) 
		{
			$this->load->library('form_validation');
			$pass = $this->input->post('newpass', TRUE);
			
			if ($pass != '') 
			{
			$this->form_validation->set_message('matches', 'The Passwords do not mach!');
			$this->form_validation->set_rules('newpass', 'Password', 'trim|required|min_length[4]|xss_clean|md5|md5');
			$this->form_validation->set_rules('newpassretype', 'Re-type Password', 'trim|required|min_length[4]|matches[newpass]|xss_clean');
			}
			$this->form_validation->set_rules('type', 'Account Type', 'callback__admintypecheck|trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|xss_clean');
			$this->form_validation->set_rules('name', 'Username', 'callback__userexistscheckupdt|trim|required|min_length[3]|xss_clean');
			$this->form_validation->set_rules('ownnames', 'Names', 'trim|required|min_length[3]|xss_clean');
			
			$this->regdata = array(
							   'admin_id' => $this->id,									
						       'type' => $this->input->post('type', TRUE),							
							   'email' => $this->input->post('email', TRUE),
							   'name' => $this->input->post('name', TRUE),
 							   'ownnames' => $this->input->post('ownnames', TRUE),
							   				   
								);
			if ($pass != '') 
			{
		       	$this->regdata['newpass'] = $this->input->post('newpass', TRUE);
				$this->regdata['newpassretype'] = $this->input->post('newpassretype', TRUE);
			}
				if ($this->form_validation->run() == FALSE)
				{	
					$this->userdanni = $this->Myadmins_model->GetUserAll($this->id);
					if (count($_POST) > 0) $this->mysmarty->assign('admindbdata', $this->regdata);
					else $this->mysmarty->assign('admindbdata', $this->userdanni);		
					$this->mysmarty->assign('errors', $this->form_validation->_error_array);
					$this->mysmarty->view('myadmins/myadmins_edituser.html');
					exit();
				}
				else 
				{	
					$this->reg_data = array(
											 'type' => $this->form_validation->set_value('type'),
											 'name' => $this->form_validation->set_value('name'),
											 'ownnames' => $this->form_validation->set_value('ownnames'),
											 'email' => $this->form_validation->set_value('email')
											);
					if ($pass != '') $this->reg_data['pass'] = $this->form_validation->set_value('newpass');
					$this->Myadmins_model->UpdateUser($this->reg_data, $this->id);								
										
					if ($pass != '') $this->session->set_flashdata('success_msg', 'Admin '.$this->reg_data['ownnames'].' Updated + New Password');
					else $this->session->set_flashdata('success_msg', 'Admin '.$this->reg_data['ownnames'].' Updated');
					redirect("Myadmins");					
				}	
		}
		else redirect("Myadmins");
}

function AddUser ()
	{
		$this->load->library('form_validation');
		
		$this->form_validation->set_message('matches', 'The Passwords do not mach!');
		$this->form_validation->set_rules('newpass', 'Password', 'trim|required|min_length[3]|xss_clean|md5|md5');
		$this->form_validation->set_rules('newpassretype', 'Re-type Password', 'trim|required|min_length[3]|matches[newpass]|xss_clean');
		$this->form_validation->set_rules('type', 'Account Type', 'callback__admintypecheck|trim|required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('name', 'Username', 'callback__userexistscheck|trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('ownnames', 'Names', 'trim|required|min_length[3]|xss_clean');

		$this->regdata = array(									
						       'newpass' => $this->input->post('newpass', TRUE),
							   'newpassretype' => $this->input->post('newpassretype', TRUE),
							   'type' => $this->input->post('type', TRUE),							
							   'email' => $this->input->post('email', TRUE),
							   'name' => $this->input->post('name', TRUE),
 							   'ownnames' => $this->input->post('ownnames', TRUE)				   
								);
		
		if ($this->form_validation->run() == FALSE)
			{	
				$this->mysmarty->assign('admindbdata', $this->regdata);
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myadmins/myadmins_adduser.html');
				exit();
			}
		else 
			{
				$this->reg_data = array(													 
										 'pass' => $this->form_validation->set_value('newpass'),
										 'type' => $this->form_validation->set_value('type'),
										 'name' => $this->form_validation->set_value('name'),
										 'ownnames' => $this->form_validation->set_value('ownnames'),
										 'email' => $this->form_validation->set_value('email'),
										 'active' => 1
										 );
								
								$this->Myadmins_model->InsertUser($this->reg_data);								
								$this->session->set_flashdata('success_msg', 'Admin '.$this->reg_data['ownnames'].' Created');
								redirect("Myadmins");
										
			}
}

function DeActivate ($userid = '')
	{	
		if ((int)$userid > 0)  $this->Myadmins_model->DeactivateUser((int)$userid);
		Redirect("Myadmins");
	}

function Activate ($userid = '')
	{	
		if ((int)$userid > 0) $this->Myadmins_model->ActivateUser((int)$userid);
		Redirect("Myadmins");
	}

function DeleteUser ($userid = '')
	{
		if ((int)$userid > 0) $this->Myadmins_model->DeleteUser((int)$userid);
		Redirect("Myadmins");
	}

function _admintypecheck($actype)
{
	if ($actype == 'master' || $actype == 'helper')	 return TRUE;			
	else 
	{
		$this->form_validation->set_message('_admintypecheck', 'Invalid Account Type.');
		return FALSE;	
	}
}
function _userexistscheck($username)
{
	$chk = $this->Myadmins_model->CheckUserExists($username);
	if ($chk)
	{
		$this->form_validation->set_message('_userexistscheck', 'Username already taken.');
		return FALSE;	
	}
	else return TRUE;		
}
function _userexistscheckupdt($username)
{
	$chk = $this->Myadmins_model->CheckUserExistsUpdate($username, $this->id);
	if ($chk)
	{
		$this->form_validation->set_message('_userexistscheckupdt', 'Username already taken.');
		return FALSE;	
	}
	else return TRUE;		
}
function togglewarehouse()
{
	$this->db->select('warehouse');
	$this->db->where('admin_id', (int)$_POST['adminid']);
	$this->query = $this->db->get('administrators');
	if ($this->query->num_rows() > 0) 
	{
		$r = $this->query->row_array();
		if ($r['warehouse'] == 0) $this->db->update('administrators', array('warehouse' => 1), array('admin_id' => (int)$_POST['adminid']));
		else $this->db->update('administrators', array('warehouse' => 0), array('admin_id' => (int)$_POST['adminid']));
	}
}
function togglelistings()
{
	$this->db->select('listings');
	$this->db->where('admin_id', (int)$_POST['adminid']);
	$this->query = $this->db->get('administrators');
	if ($this->query->num_rows() > 0) 
	{
		$r = $this->query->row_array();
		if ($r['listings'] == 0) $this->db->update('administrators', array('listings' => 1), array('admin_id' => (int)$_POST['adminid']));
		else $this->db->update('administrators', array('listings' => 0), array('admin_id' => (int)$_POST['adminid']));
	}
}
function toggleorders()
{
	$this->db->select('orders');
	$this->db->where('admin_id', (int)$_POST['adminid']);
	$this->query = $this->db->get('administrators');
	if ($this->query->num_rows() > 0) 
	{
		$r = $this->query->row_array();
		if ($r['orders'] == 0) $this->db->update('administrators', array('orders' => 1), array('admin_id' => (int)$_POST['adminid']));
		else $this->db->update('administrators', array('orders' => 0), array('admin_id' => (int)$_POST['adminid']));
	}
}
function toggleaccounting()
{
	$this->db->select('accounting');
	$this->db->where('admin_id', (int)$_POST['adminid']);
	$this->query = $this->db->get('administrators');
	if ($this->query->num_rows() > 0) 
	{
		$r = $this->query->row_array();
		if ($r['accounting'] == 0) $this->db->update('administrators', array('accounting' => 1), array('admin_id' => (int)$_POST['adminid']));
		else $this->db->update('administrators', array('accounting' => 0), array('admin_id' => (int)$_POST['adminid']));
	}
}

    function saveauclimit()
    {
        $this->db->where('admin_id', (int)$_POST['adminid']);
        $this->query = $this->db->get('administrators');
        if ($this->query->num_rows() > 0)
        {
            $this->db->update('administrators', array('auclimit' => (int)$_POST['aucid']), array('admin_id' => (int)$_POST['adminid']));
        }
    }


}