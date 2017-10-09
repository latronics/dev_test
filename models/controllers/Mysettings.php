<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mysettings extends Controller {
	
function Mysettings()
	{
		parent::Controller();		
		$this->_Start();
		if ($this->session->userdata['type'] != 'master') { echo "Sorry, you don't have clearance for here."; exit();}
		
		$this->mysmarty->assign('area', 'Settings');	
		
	}
function cleancache()
{
	$files = glob($this->config->config['pathtosystem'].'/cache/*'); //get all file names
		foreach($files as $file){
			if(is_file($file))
			//echo $file;echo '<br>';
			unlink($file); //delete file
		}
		$this->session->set_flashdata('success_msg', 'SUCCESS');
		Redirect('Mysettings');		
}
function index()
	{	
		$this->db->where('hide', 0);
		$this->db->order_by("sid", "ASC");
		$this->query = $this->db->get('settings');
		
		if ($this->query->num_rows() > 0) $data = $this->query->result_array();
		else $data = FALSE;
		$this->mysmarty->assign('s', $data);
		$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_main.html');
	}

function Save($sid = '') 
{
	if ((int)$sid != 0)
	{
		$setting = $this->input->post('svalue', TRUE);
		if ($setting != '') $this->db->update('settings', array('svalue' => $setting), array('sid' => (int)$sid, 'hide' => 0));
		GoMail(array('msg_title' => 'Settings Changes @ '.CurrentTimeR(), 'msg_body' => 'svalue -> '.$setting.' FOR ID '.(int)$sid, 'msg_date' => CurrentTime()), $this->config->config['support_email']);
	}
	else
	{
		$setting = $this->input->post('svalue', TRUE);
		$key = $this->input->post('skey', TRUE);
		if ($setting != '') $this->db->insert('settings', array('skey' => $key, 'svalue' => $setting));	
	}
	Redirect('Mysettings');
}

function CartStatus($val = 0)
	{
		if ((int)$val != 1) $val = 0;
		$this->db->update('settings', array('svalue' => (int)$val), array('skey' => 'StoreCart'));
		header("Location: ".$_SERVER['HTTP_REFERER']);		
	}
	
function GoogleStatus($val = 0)
	{
		if ((int)$val > 2) $val = 0;
		$this->db->update('settings', array('svalue' => (int)$val), array('skey' => 'googledriveuse'));
		header("Location: ".$_SERVER['HTTP_REFERER']);		
	}
function _Start() 
{
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();	
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->go = DoGo($this->router->class, $this->router->method);	
		$this->mysmarty->assign('go', $this->go);	
}

}