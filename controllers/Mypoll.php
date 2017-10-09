<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mypoll extends Controller {

function Mypoll()
	{
		parent::Controller();
		$this->load->model('Mypoll_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();	
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Poll');

	}
	
function index()
	{	
	$this->mysmarty->assign('list', $this->Mypoll_model->ListItems());	
	$this->mysmarty->view('mypoll/mypoll_show.html');
	}

function Results($id = '')
{
	if ((int)$id == 0) { Redirect("/Mypoll"); break; exit;}
	$this->poll = $this->Mypoll_model->GetItem((int)$id);
	$results = $this->Mypoll_model->GetResults((int)$id);
	
	$this->poll['options']['1'] = $this->poll['opt1'];
	$this->poll['options']['2'] = $this->poll['opt2'];
	$this->poll['options']['3'] = $this->poll['opt3'];
	$this->poll['options']['4'] = $this->poll['opt4'];
	$this->poll['options']['5'] = $this->poll['opt5'];
	$this->poll['options']['6'] = $this->poll['opt6'];
	$this->poll['options']['7'] = $this->poll['opt7'];
	$this->poll['options']['8'] = $this->poll['opt8'];
	$this->poll['options']['9'] = $this->poll['opt9'];	
	
	$items = '';
	$values = '';
	
	foreach ($results as $key => $value)
		{
			if ($this->poll['options'][$key] != '') $items .= "|".$this->poll['options'][$key];			
		}
	
	$values = implode (",", $results);	
	$maxvalues = max($results);
	
	$this->mysmarty->assign('pollchart', array('items' => $items, 'values' => $values, 'max' => $maxvalues));
	
	$this->mysmarty->assign('poll', $this->poll);
	$this->mysmarty->assign('results', $results);
	$this->mysmarty->view('mypoll/mypoll_results.html');
}

function ClearResults ($id = '') {
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mypoll_model->DeleteResults($this->id);
		Redirect("/Mypoll");
}
	
function MakeVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mypoll_model->MakeVisible($this->id);
		Redirect("/Mypoll");

	}
function MakeNotVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mypoll_model->MakeNotVisible($this->id);
		Redirect("/Mypoll");	
	}	
	
function Delete ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) 
			{
			$this->Mypoll_model->Delete($this->id);
			}
		Redirect("/Mypoll");
	}

function Edit($itemid = '')
	{	
		$this->id = (int)$itemid;
		if ($this->id > 0) {

		$this->displays = $this->Mypoll_model->GetItem($this->id);
		$this->load->library('form_validation');

		$this->form_validation->set_rules('title', 'Poll Question', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('opt1', 'Answer 1', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('opt2', 'Answer 2', 'trim|required|min_length[2]|xss_clean');

		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
											'title' => $this->input->post('title', TRUE),
											'opt1' => $this->input->post('opt1', TRUE),
											'opt2' => $this->input->post('opt2', TRUE),
											'opt3' => $this->input->post('opt3', TRUE),
											'opt4' => $this->input->post('opt4', TRUE),
											'opt5' => $this->input->post('opt5', TRUE),
											'opt6' => $this->input->post('opt6', TRUE),
											'opt7' => $this->input->post('opt7', TRUE),
											'opt8' => $this->input->post('opt8', TRUE),
											'opt9' => $this->input->post('opt9', TRUE)											
											);	
						
				$this->mysmarty->assign('displays', $this->displays);
				
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('mypoll/mypoll_edit.html');
				exit();
			}
			else 
			{		
												$this->db_data['title'] = $this->form_validation->set_value('title');
												$this->db_data['opt1'] = $this->form_validation->set_value('opt1');
												$this->db_data['opt2'] = $this->form_validation->set_value('opt2');
												$this->db_data['opt3'] = $this->input->post('opt3', TRUE);
												$this->db_data['opt4'] = $this->input->post('opt4', TRUE);
												$this->db_data['opt5'] = $this->input->post('opt5', TRUE);
												$this->db_data['opt6'] = $this->input->post('opt6', TRUE);
												$this->db_data['opt7'] = $this->input->post('opt7', TRUE);
												$this->db_data['opt8'] = $this->input->post('opt8', TRUE);
												$this->db_data['opt9'] = $this->input->post('opt9', TRUE);
												
						$this->Mypoll_model->Update((int)$this->id,$this->db_data);
						redirect("/Mypoll"); break; exit();								
			}
	}
	else {
		redirect("/Mypoll");
	}
}

function Add()
	{	
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('title', 'Poll Question', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('opt1', 'Answer 1', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('opt2', 'Answer 2', 'trim|required|min_length[2]|xss_clean');
							
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
											'title' => $this->input->post('title', TRUE),
											'opt1' => $this->input->post('opt1', TRUE),
											'opt2' => $this->input->post('opt2', TRUE),
											'opt3' => $this->input->post('opt3', TRUE),
											'opt4' => $this->input->post('opt4', TRUE),
											'opt5' => $this->input->post('opt5', TRUE),
											'opt6' => $this->input->post('opt6', TRUE),
											'opt7' => $this->input->post('opt7', TRUE),
											'opt8' => $this->input->post('opt8', TRUE),
											'opt9' => $this->input->post('opt9', TRUE)											
											);
								
								
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('mypoll/mypoll_add.html');
				exit();
			}
			else 
			{							
										
										 $this->db_data['title'] = $this->form_validation->set_value('title');
												$this->db_data['opt1'] = $this->form_validation->set_value('opt1');
												$this->db_data['opt2'] = $this->form_validation->set_value('opt2');
												$this->db_data['opt3'] = $this->input->post('opt3', TRUE);
										if (strlen($this->input->post('opt4')) > 1) $this->db_data['opt4'] = $this->input->post('opt4', TRUE);
										if (strlen($this->input->post('opt5')) > 1) $this->db_data['opt5'] = $this->input->post('opt5', TRUE);
										if (strlen($this->input->post('opt6')) > 1) $this->db_data['opt6'] = $this->input->post('opt6', TRUE);
										if (strlen($this->input->post('opt7')) > 1) $this->db_data['opt7'] = $this->input->post('opt7', TRUE);
										if (strlen($this->input->post('opt8')) > 1) $this->db_data['opt8'] = $this->input->post('opt8', TRUE);
										if (strlen($this->input->post('opt9')) > 1) $this->db_data['opt9'] = $this->input->post('opt9', TRUE);										
											$this->db_data['date'] = CurrentTime();
					
						$this->Mypoll_model->Insert($this->db_data);
						redirect("/Mypoll"); break; exit();								
			}
}

	
}
