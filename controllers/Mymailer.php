<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mymailer extends Controller {

function Mymailer()
	{
		parent::Controller();
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();		
				
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Mailer');

	}
	
function index()
	{	
		$this->query = $this->db->get('administrators');
		$admins = array();
		if ($this->query->num_rows() > 0) 
			{
			$admin = $this->query->result_array();
			foreach ($admin as $v)
				{
					$admins[$v['admin_id']] = $v;
				}		
			}
		$this->mysmarty->assign('admins',$admins);
		////
		
		
		$this->query = $this->db->get('administrators_mails');
		$mails = array();
		if ($this->query->num_rows() > 0) 
			{
			$mail = $this->query->result_array();
			foreach ($mail as $m)
				{
					$mails[$m['mid']] = $m;
				}		
			}
		$this->mysmarty->assign('mails',$mails);
		////
		
		
		$this->query = $this->db->get('administrators_mails_relations');
		$rels = array();
		if ($this->query->num_rows() > 0) 
			{
			$rel = $this->query->result_array();
			foreach ($rel as $r)
				{
					$rels[$r['adminid']][$r['mid']] = $r['mid'];
				}		
			}
		$this->mysmarty->assign('rels',$rels);		
		$this->mysmarty->view('mymailer/mymailer_main.html');		
	}

function MakeRelation ($admin = 0, $mail = 0)
	{
		
	if ((int)$admin != 0 && (int)$mail != 0)
		{
			
		if (($admin != $this->session->userdata['admin_id']) && (($this->session->userdata['admin_id'] != 1) && ($this->session->userdata['admin_id'] != 2)))
			{
				echo 'Only Nikolay &amp; Mitko can modify other people\'s mail setting. Everybody else can only modify their own.';
				exit();
			}
		
		$this->db->where('adminid', (int)$admin);
		$this->db->where('mid', (int)$mail);
		$this->db->delete('administrators_mails_relations'); 	
		$this->db->insert('administrators_mails_relations', array('adminid' => (int)$admin, 'mid' => (int)$mail));			
		}		
	Redirect ('Mymailer');
	}
	
function RemoveRelation ($admin = 0, $mail = 0)
	{
		
	if ((int)$admin != 0 && (int)$mail != 0)
		{
			if (($admin != $this->session->userdata['admin_id']) && (($this->session->userdata['admin_id'] != 1) && ($this->session->userdata['admin_id'] != 2)))
			{
				echo 'Only Nikolay &amp; Mitko can modify other people\'s mail setting. Everybody else can only modify their own.';
				exit();
			}
		$this->db->where('adminid', (int)$admin);
		$this->db->where('mid', (int)$mail);
		$this->db->delete('administrators_mails_relations'); 	
		}		
	Redirect ('Mymailer');
	}
}
