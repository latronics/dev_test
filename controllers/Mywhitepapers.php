<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mywhitepapers extends Controller {
function Mywhitepapers()
	{
		parent::Controller();
		$this->load->model('Mywhitepapers_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		if ($this->session->userdata['admin_id'] == 9) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Whitepapers');		
	}
	
function index()
	{	
		$this->mysmarty->assign('list', $this->Mywhitepapers_model->ListItems());	
		$this->mysmarty->assign('count', $this->Mywhitepapers_model->CountDownloads());	
		$this->mysmarty->view('mywhitepapers/mywhitepapers_show.html');
	}
function MakeVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mywhitepapers_model->MakeVisible($this->id);
		
		Redirect("/Mywhitepapers");
	}
function MakeNotVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mywhitepapers_model->MakeNotVisible($this->id);
		
		Redirect("/Mywhitepapers");
	}
	
	
	
			
function Delete ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) 
			{
			$this->filename = $this->Mywhitepapers_model->GetFile($this->id);
			$this->Mywhitepapers_model->Delete($this->id);
			if ($this->filename) {
			unlink($this->config->config['paths']['fileswhitepapers'].'/'.$this->filename);
			}
			}
		Redirect("/Mywhitepapers");
	}
function ChangeOrder ($id = '')
	{	
		$this->id = (int)$id;
		$this->ordering = (int)$this->input->post('ordering', TRUE);
		if ($this->id > 0) $this->Mywhitepapers_model->ChangeOrder($this->id, $this->ordering);
		
		Redirect("/Mywhitepapers");
	}
function ReOrder ()
	{	
	   $this->Mywhitepapers_model->ReOrder();		
		Redirect("/Mywhitepapers");
	}
function Update($itemid = '')
	{	
		
		$this->id = (int)$itemid;		
		if ($this->id == 0) exit();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'trim|required|min_length[3]|xss_clean');
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array('title' => $this->input->post('title', TRUE)
											);	
				
				$this->mysmarty->assign('list', $this->Mywhitepapers_model->ListItems());	
				$this->mysmarty->assign('count', $this->Mywhitepapers_model->CountDownloads());	
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', array( $this->id => $this->form_validation->_error_array));
				$this->mysmarty->view('mywhitepapers/mywhitepapers_show.html');
				exit();
			}
			else 
			{					
				
					$this->db_data = array(												 
											'title' => $this->form_validation->set_value('title')
											);	
						$this->Mywhitepapers_model->Update((int)$this->id,$this->db_data);
						
						redirect("/Mywhitepapers"); exit();								
			}
}
function Add()
	{	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'trim|required|min_length[3]|xss_clean');
							
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(
									'title' => $this->input->post('title', TRUE)
									);
				
				$this->displays = $this->Mywhitepapers_model->GetMaxOrder();
				$this->mysmarty->assign('list', $this->Mywhitepapers_model->ListItems());	
				$this->mysmarty->assign('count', $this->Mywhitepapers_model->CountDownloads());	
				$this->mysmarty->assign('displays', $this->displays);					
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('mywhitepapers/mywhitepapers_show.html');
				exit();
			}
			else 
			{							
					$this->db_data = array(	
											'title' => $this->form_validation->set_value('title'),
											'ordering' => $this->Mywhitepapers_model->GetMaxOrder()
											);
					if ($_FILES['wpfile']['name'] != '') 
								{
									$this->uconfig['upload_path'] = $this->config->config['paths']['fileswhitepapers'];
									$this->uconfig['allowed_types'] = 'doc|pdf|txt|rtf';
									$this->uconfig['remove_spaces'] = TRUE;
									$this->uconfig['max_size'] = '1900';
									$this->uconfig['max_filename'] = '145';							
									$this->load->library('upload', $this->uconfig);
									$this->uploadresult = $this->upload->do_upload('wpfile');
									$this->filedata = $this->upload->data();
			
									if ( !$this->uploadresult) { printcool ($this->upload->display_errors()); exit; }
									$this->db_data['file'] = $this->filedata['file_name'];									
								}
						$this->Mywhitepapers_model->Insert($this->db_data);
						redirect("/Mywhitepapers"); exit();								
			}
}
	
	
}