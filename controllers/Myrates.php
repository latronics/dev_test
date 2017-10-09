<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myrates extends Controller {
	
function Myrates()
	{
		parent::Controller();
		$this->load->model('Myrates_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();	
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}		
		if ($this->session->userdata['admin_id'] == 9) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Rates');
	}
	
function index()
	{	
		$this->mysmarty->assign('rates', $this->Myrates_model->GetRates());	
		$this->mysmarty->view('myrates/myrates_main.html');
	}
function Update($id) 
	{
	if ((int)$id > 0) 
		{
					$this->inputdata = array(	
											'rategrn' => (float)PriceUnification($this->input->post('rategrn', TRUE)),
											'rateprio' => (float)PriceUnification($this->input->post('rateprio', TRUE)),
											'ratenxtd' => (float)PriceUnification($this->input->post('ratenxtd', TRUE)),
											'label_grn' => (float)PriceUnification($this->input->post('label_grn', TRUE)),
											'label_prio' => (float)PriceUnification($this->input->post('label_prio', TRUE)),
											'label_nxtd' => (float)PriceUnification($this->input->post('label_nxtd', TRUE)),
											'kit_grn' => (float)PriceUnification($this->input->post('kit_grn', TRUE)),
											'kit_prio' => (float)PriceUnification($this->input->post('kit_prio', TRUE)),
											'kit_nxtd' => (float)PriceUnification($this->input->post('kit_nxtd', TRUE)),
											'return_grn' => (float)PriceUnification($this->input->post('return_grn', TRUE)),
											'return_prio' => (float)PriceUnification($this->input->post('return_prio', TRUE)),
											'return_nxtd' =>(float)PriceUnification($this->input->post('return_nxtd', TRUE)),
											);
							
			
				$this->Myrates_model->Update((int)$id, $this->inputdata);
				Redirect("/Myrates"); 
				exit();								

		}
	}
}