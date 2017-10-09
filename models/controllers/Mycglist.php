<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mycglist extends Controller {

function Mycglist()
	{
		parent::Controller();
		//LOAD SESSION
		$this->load->model('Mycglist_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Cglist');		
	}
	
function index()
	{	
		$session_search = $this->session->userdata('last_string');

		if (isset($_POST['search'])) $string = $this->input->post('search', TRUE);		
		elseif ($session_search) $string = $this->session->userdata('last_string');
		else $string = '';
		
		$this->session->set_userdata('last_string', $string);
		$this->mysmarty->assign('string', $string);		
		$this->mysmarty->assign('list', $this->Mycglist_model->ListItems($string));	
		$this->mysmarty->view('mycglist/mycglist_show.html');

	}
function CleanSearch()
	{
		$this->session->unset_userdata('last_string');
		Redirect('Mycglist');
	}
function GetSource($itemid = '')
	{
		$this->id = (int)$itemid;
		if ($this->id == 0)	Redirect('Mycglist');		
		$this->displays = $this->Mycglist_model->GetItem($this->id);			
		$this->_GetSpecialAndTree();
		$this->mysmarty->assign('displays', $this->displays);
		$this->mysmarty->view('mycglist/mycglist_source.html');
	}

function Delete ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0)
			{
			$this->Mycglist_model->Delete($this->id);
			}
		Redirect("/Mycglist");
	}
function Edit($itemid = '')
	{	
		$this->id = (int)$itemid;
	
		if ($this->id > 0) {
		
		$this->displays = $this->Mycglist_model->GetItem($this->id);			
		$this->_GetSpecialAndTree();
		$this->load->library('form_validation');

		$this->form_validation->set_rules('c_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('c_body', 'Body', 'trim|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
									'c_title' => $this->input->post('c_title', TRUE),									
									'c_body' => $this->input->post('c_body', TRUE)
									);
								
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
					
				$this->editor = new FCKeditor('c_body');
				
				$this->editor->Width = "490";
				$this->editor->Height = "444";
				$this->editor->ToolbarSet = "Small";
				
				if (count($_POST) >	0) 
					{
						$this->editor->Value = $this->inputdata['c_body'];
						$this->inputdata['c_body'] = $this->editor->CreateHtml();				
					}
				else 
					{
						$this->editor->Value = $this->displays['c_body'];
						$this->displays['c_body'] = $this->editor->CreateHtml();
					}
				
				$this->mysmarty->assign('displays', $this->displays);
				
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('mycglist/mycglist_edit.html');
				exit();
			}
			else 
			{					
				
					$this->db_data = array(												 
											'c_title' => $this->form_validation->set_value('c_title'),
											'c_body' => $this->form_validation->set_value('c_body')
											);
									
						$this->Mycglist_model->Update((int)$this->id,$this->db_data);
						
						redirect("Mycglist/GetSource/".(int)$this->id);					
			}
	}
	else {
			redirect("Mycglist");
	}
}

function Add()
	{	
		$this->_GetSpecialAndTree();
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('c_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('c_body', 'Body', 'trim|xss_clean');
				
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
									'c_title' => $this->input->post('c_title', TRUE),
									'c_body' => $this->input->post('c_body', TRUE)
									);
															
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('c_body');
				if (count($_POST) >	0) $this->editor->Value = $this->inputdata['c_body'];
				else $this->editor->Value = '';
				$this->editor->Width = "490";
				$this->editor->Height = "426";
				$this->editor->ToolbarSet = "Small";				
				$this->inputdata['c_body'] = $this->editor->CreateHtml();
					
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('mycglist/mycglist_add.html');
				exit();
			}
			else 
			{							
					$this->db_data = array(												 
											'c_title' => $this->form_validation->set_value('c_title'),											
											'c_body' => $this->form_validation->set_value('c_body')
											);
					
								
					$this->newid = $this->Mycglist_model->Insert($this->db_data);
					
					redirect("Mycglist/GetSource/".(int)$this->newid);							
			}
}

function _GetSpecialAndTree()
	{
		$this->load->model('Myproducts_model'); 	
		$this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllCategories());
		$this->mysmarty->assign('specials', $this->Mycglist_model->GetTopSpecialAds());		
	}
}
