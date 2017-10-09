<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myfaq extends Controller {

function Myfaq()
	{
		parent::Controller();
		$this->load->model('Myfaq_model'); 
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
		$this->mysmarty->assign('area', 'FAQ');
	}
	
function index()
	{	
		$this->mysmarty->assign('catlist', $this->Myfaq_model->GetAllCategories());	
		$this->mysmarty->view('myfaq/myfaq_main.html');
	}
function AddCategory() 
	{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('f_cattitle', 'Category Title', 'trim|required|min_length[3]|xss_clean');
	
					$this->inputdata['f_cattitle'] = $this->input->post('f_cattitle', TRUE);																			

			if ($this->form_validation->run() == FALSE)
			{	
				$this->mysmarty->assign('inputdata', $this->inputdata);
				$this->mysmarty->assign('catlist', $this->Myfaq_model->GetAllCategories());
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myfaq/myfaq_main.html');
				exit();
			}
			else 
			{							
				$this->db_data['f_cattitle'] = $this->form_validation->set_value('f_cattitle');
				$this->Myfaq_model->InsertCategory($this->db_data);
				Redirect("/Myfaq"); exit();								
			}	
	}
function UpdateCategory($id) 
	{
	$this->id = (int)$id;
	if ($this->id > 0) 
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('f_cattitle', 'Category Title', 'trim|required|min_length[3]|xss_clean');
		
					$this->inputdata[$this->id] = array(	
											'f_catid' => $this->id,
											'f_cattitle' => $this->input->post('f_cattitle', TRUE)
											);														
			if ($this->form_validation->run() == FALSE)
			{	
				$this->mysmarty->assign('inputdata', $this->inputdata);	
				$this->mysmarty->assign('catlist', $this->Myfaq_model->GetAllCategories());
				$this->mysmarty->assign('errors', array( $this->id => $this->form_validation->_error_array));
				$this->mysmarty->view('myfaq/myfaq_main.html');
				exit();
			}
			else 
			{							
				$this->db_data['f_cattitle'] = $this->form_validation->set_value('f_cattitle');
				$this->Myfaq_model->UpdateCategory($this->id, $this->db_data);
				Redirect("/Myfaq"); exit();								
			}
		}
	}
function EditCategory($id) 
	{
	$this->id = (int)$id;
	if ($this->id > 0) 
		{	
			$this->session->set_flashdata('cat',$this->id);
			$this->mysmarty->assign('cat',$this->id);
			
			if (count($_POST) == 0) 
			{
				$this->mysmarty->assign('catlist', $this->Myfaq_model->GetAllCategories());
				$this->mysmarty->assign('item', $this->Myfaq_model->GetCategory($this->id));
				$this->mysmarty->view('myfaq/myfaq_editcategory.html');
			}
			else
			{
				$this->load->library('form_validation');
				$this->form_validation->set_rules('f_cattitle', 'Category Title', 'trim|required|min_length[3]|xss_clean');
			
						$this->inputdata[$this->id] = array(	
												'f_catid' => $this->id,
												'f_cattitle' => $this->input->post('f_cattitle', TRUE)
												);														
				if ($this->form_validation->run() == FALSE)
				{	
					$this->mysmarty->assign('inputdata', $this->inputdata);	
					$this->mysmarty->assign('item', $this->Myfaq_model->GetCategory($this->id));
					$this->mysmarty->assign('catlist', $this->Myfaq_model->GetAllCategories());
					$this->mysmarty->assign('errors', $this->form_validation->_error_array);
					$this->mysmarty->view('myfaq/myfaq_editcategory.html');
					exit();
				}
				else 
				{							
					$this->db_data['f_cattitle'] = $this->form_validation->set_value('f_cattitle');
					
					$this->Myfaq_model->UpdateCategory($this->id, $this->db_data);
					Redirect("/Myfaq"); break; exit();								
				}
			}
		}
		else {
			Redirect("/Myfaq");
		}
	}
function DeleteCategory($id)
	{
		$this->id = (int)$id;
		if ($this->id > 0) 
				{
				$this->Myfaq_model->DeleteCategory($this->id);
				}
		Redirect("/Myfaq");
	}
	
function Show($cat = '', $sortby = '') 
	{	
		$this->cat = (int)$cat;
		if ($this->cat > 0 ) 
		{
		$this->sortby = CleanInput($sortby);
		$this->session->set_flashdata('cat', $this->cat);
		$this->session->set_flashdata('sortby', '/'.$this->sortby);
		$this->mysmarty->assign('cat', $this->cat);
		$this->mysmarty->assign('sortby', $this->sortby);	
		$this->mysmarty->assign('category', $this->Myfaq_model->GetCategory($this->cat));
		$this->mysmarty->assign('list', $this->Myfaq_model->ListItems($this->cat, $this->sortby));	
		$this->mysmarty->assign('catlist', $this->Myfaq_model->GetAllCategories());
		$this->mysmarty->view('myfaq/myfaq_show.html');
		}
		else 
		{
		Redirect("/Myfaq");
		}
	}

function MakeVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Myfaq_model->MakeVisible($this->id);
		Redirect("/Myfaq/Show/".$this->session->flashdata('cat').$this->session->flashdata('sortby'));
	}
function MakeNotVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Myfaq_model->MakeNotVisible($this->id);
		Redirect("/Myfaq/Show/".$this->session->flashdata('cat').$this->session->flashdata('sortby'));
	}
function MakeTop ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Myfaq_model->MakeTop($this->id);
		Redirect("/Myfaq/Show/".$this->session->flashdata('cat').$this->session->flashdata('sortby'));
	}
function MakeNotTop ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Myfaq_model->MakeNotTop($this->id);
		Redirect("/Myfaq/Show/".$this->session->flashdata('cat').$this->session->flashdata('sortby'));	
	}
		
function Delete ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) 
			{
			$this->Myfaq_model->Delete($this->id);
			}
		Redirect("/Myfaq/Show/".$this->session->flashdata('cat').$this->session->flashdata('sortby'));
	}
function ChangeOrder ($id = '')
	{	
		$this->id = (int)$id;
		$this->order = (int)$this->input->post('f_order', TRUE);
		if ($this->id > 0) $this->Myfaq_model->ChangeOrder($this->id, $this->order);
		Redirect("/Myfaq/Show/".$this->session->flashdata('cat'));
	}
function ReOrder ($by = '', $sortby = '')
	{	
		$this->by = (int)$by;
		$this->sortby = CleanInput($sortby);
		if ($this->by > 0) $this->Myfaq_model->ReOrder($this->by, $this->sortby);
		Redirect("/Myfaq/Show/".$this->by);
		break;
	}
function Edit($itemid = '')
	{	
		$this->id = (int)$itemid;
		
		if ($this->id > 0) {
		$this->cat = (int)$this->session->flashdata('cat');
		$this->mysmarty->assign('cat', $this->cat);
		$this->mysmarty->assign('catlist', $this->Myfaq_model->GetAllCategories());
			
		$this->session->set_flashdata('cat', $this->cat);
		
		$this->displays = $this->Myfaq_model->GetItem($this->id);

		if ((int)$this->displays['f_cat'] != $this->cat) 
				{
				$this->session->set_flashdata('error_msg', 'This item does not match the category');
				redirect("/Myfaq/Show/".$this->cat);
				exit();
				}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('f_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('f_order', 'Order', 'trim|required|xss_clean');
		$this->form_validation->set_rules('f_cat', 'Category', 'trim|required|xss_clean');

		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
											'f_title' => $this->input->post('f_title', TRUE),
											'f_order' => (int)$this->input->post('f_order'),
											'f_cat' => (int)$this->input->post('f_cat'),
											'f_desc' => $this->input->post('f_desc', TRUE)
											);	
					
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
					
				$this->editor = new FCKeditor('f_desc');
				
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				
				if (count($_POST) >	0) 
					{
						$this->editor->Value = $this->inputdata['f_desc'];
						$this->inputdata['f_desc'] = $this->editor->CreateHtml();				
					}
				else 
					{
						$this->editor->Value = $this->displays['f_desc'];
						$this->displays['f_desc'] = $this->editor->CreateHtml();
					}
											
				$this->mysmarty->assign('displays', $this->displays);
				
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myfaq/myfaq_edit.html');
				exit();
			}
			else 
			{					

					$this->db_data = array(												 
											'f_title' => $this->form_validation->set_value('f_title'),
											'f_order' => (int)$this->form_validation->set_value('f_order'),
											'f_cat' => (int)$this->form_validation->set_value('f_cat'),
											'f_desc' => $this->input->post('f_desc', TRUE)
											);	
					

						$this->Myfaq_model->Update((int)$this->id,$this->db_data);
						redirect("/Myfaq/Show/".$this->db_data['f_cat']); exit();								
			}
	}
	else {
		redirect("/Myfaq/Show/".$this->cat."/");
	}
}

function Add($cat)
	{	
		$this->cat = (int)$cat;
		if ($this->cat > 0) {
		$this->mysmarty->assign('cat', $this->cat);
		$this->mysmarty->assign('catlist', $this->Myfaq_model->GetAllCategories());
		$this->session->set_flashdata('cat', $this->cat);
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('f_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('f_order', 'Order', 'trim|required|xss_clean');
							
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
									'f_title' => $this->input->post('f_title', TRUE),
									'f_order' => (int)$this->input->post('f_order'),
									'f_desc' => $this->input->post('f_desc', TRUE)
									);
				
				$this->displays = $this->Myfaq_model->GetMaxOrder($this->cat);
				
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('f_desc');
				if (count($_POST) >	0) $this->editor->Value = $this->inputdata['f_desc'];
				else $this->editor->Value = '';
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				$this->inputdata['f_desc'] = $this->editor->CreateHtml();
											
				$this->mysmarty->assign('displays', $this->displays);
					
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myfaq/myfaq_add.html');
				exit();
			}
			else 
			{							
					$this->db_data = array(												 
											'f_title' => $this->form_validation->set_value('f_title'),
											'f_cat' => $this->cat,
											'f_order' => (int)$this->form_validation->set_value('f_order'),
											'f_desc' => $this->input->post('f_desc', TRUE)
											);													

						$this->Myfaq_model->Insert($this->db_data);
						redirect("/Myfaq/Show/".$this->cat."/"); exit();								
			}
	}
	else {
		redirect("/Myfaq");
	}
}	
}
