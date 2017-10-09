<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Mytracking extends Controller {
	
function Mytracking()
	{
		parent::Controller();
		$this->load->model('Mytracking_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();	
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Tracking');
	}
	
function index()
	{	
		$this->mysmarty->assign('tracklist', $this->Mytracking_model->GetTracks());	
		$this->mysmarty->view('mytracking/mytracking_main.html');
	}
}