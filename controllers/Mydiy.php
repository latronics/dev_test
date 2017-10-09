<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mydiy extends Controller {
function Mydiy()
	{
		parent::Controller();		
		$this->load->model('Mydiy_model'); 
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
		$this->mysmarty->assign('area', 'DIY');
	}
	
function index()
	{	
		$this->mysmarty->assign('catlist', $this->Mydiy_model->GetAllCategories());	
		$this->mysmarty->view('mydiy/mydiy_main.html');
	}
function AddCategory() 
	{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('d_cattitle', 'Category Title', 'trim|required|min_length[3]|xss_clean');
	
					$this->inputdata['d_cattitle'] = $this->input->post('d_cattitle', TRUE);																			
			if ($this->form_validation->run() == FALSE)
			{	
				$this->mysmarty->assign('inputdata', $this->inputdata);
				$this->mysmarty->assign('catlist', $this->Mydiy_model->GetAllCategories());
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('mydiy/mydiy_main.html');
				exit();
			}
			else 
			{							
				$this->db_data['d_cattitle'] = $this->form_validation->set_value('d_cattitle');
				$this->Mydiy_model->InsertCategory($this->db_data);
				Redirect("/Mydiy"); exit();								
			}	
	}
function UpdateCategory($id) 
	{
	$this->id = (int)$id;
	if ($this->id > 0) 
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('d_cattitle', 'Category Title', 'trim|required|min_length[3]|xss_clean');
		
					$this->inputdata[$this->id] = array(	
											'd_catid' => $this->id,
											'd_cattitle' => $this->input->post('d_cattitle', TRUE)
											);														
			if ($this->form_validation->run() == FALSE)
			{	
				$this->mysmarty->assign('inputdata', $this->inputdata);	
				$this->mysmarty->assign('catlist', $this->Mydiy_model->GetAllCategories());
				$this->mysmarty->assign('errors', array( $this->id => $this->form_validation->_error_array));
				$this->mysmarty->view('mydiy/mydiy_main.html');
				exit();
			}
			else 
			{							
				$this->db_data['d_cattitle'] = $this->form_validation->set_value('d_cattitle');
				$this->Mydiy_model->UpdateCategory($this->id, $this->db_data);
				Redirect("/Mydiy"); exit();								
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
				$this->mysmarty->assign('catlist', $this->Mydiy_model->GetAllCategories());
				$this->mysmarty->assign('item', $this->Mydiy_model->GetCategory($this->id));
				$this->mysmarty->view('mydiy/mydiy_editcategory.html');
			}
			else
			{
				$this->load->library('form_validation');
				$this->form_validation->set_rules('d_cattitle', 'Category Title', 'trim|required|min_length[3]|xss_clean');
			
						$this->inputdata[$this->id] = array(	
												'd_catid' => $this->id,
												'd_cattitle' => $this->input->post('d_cattitle', TRUE)
												);														
				if ($this->form_validation->run() == FALSE)
				{	
					$this->mysmarty->assign('inputdata', $this->inputdata);	
					$this->mysmarty->assign('item', $this->Mydiy_model->GetCategory($this->id));
					$this->mysmarty->assign('catlist', $this->Mydiy_model->GetAllCategories());
					$this->mysmarty->assign('errors', $this->form_validation->_error_array);
					$this->mysmarty->view('mydiy/mydiy_editcategory.html');
					exit();
				}
				else 
				{							
					$this->db_data['d_cattitle'] = $this->form_validation->set_value('d_cattitle');
					
					$this->Mydiy_model->UpdateCategory($this->id, $this->db_data);
					Redirect("/Mydiy"); break; exit();								
				}
			}
		}
		else {
			Redirect("/Mydiy");
		}
	}
function DeleteCategory($id)
	{
		$this->id = (int)$id;
		if ($this->id > 0) 
				{
				$this->Mydiy_model->DeleteCategory($this->id);
				}
		Redirect("/Mydiy");
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
		$this->mysmarty->assign('category', $this->Mydiy_model->GetCategory($this->cat));
		$this->mysmarty->assign('list', $this->Mydiy_model->ListItems($this->cat, $this->sortby));	
		$this->mysmarty->assign('catlist', $this->Mydiy_model->GetAllCategories());
		$this->mysmarty->view('mydiy/mydiy_show.html');
		}
		else 
		{
		Redirect("/Mydiy");
		}
	}
function MakeVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mydiy_model->MakeVisible($this->id);
		Redirect("/Mydiy/Show/".$this->session->flashdata('cat').$this->session->flashdata('sortby'));
	}
function MakeNotVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mydiy_model->MakeNotVisible($this->id);
		Redirect("/Mydiy/Show/".$this->session->flashdata('cat').$this->session->flashdata('sortby'));
	}
function MakeTop ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mydiy_model->MakeTop($this->id);
		Redirect("/Mydiy/Show/".$this->session->flashdata('cat').$this->session->flashdata('sortby'));
	}
function MakeNotTop ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mydiy_model->MakeNotTop($this->id);
		Redirect("/Mydiy/Show/".$this->session->flashdata('cat').$this->session->flashdata('sortby'));	
	}
		
function Delete ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) 
			{
			$this->Mydiy_model->Delete($this->id);
			}
		Redirect("/Mydiy/Show/".$this->session->flashdata('cat').$this->session->flashdata('sortby'));
	}
function ChangeOrder ($id = '')
	{	
		$this->id = (int)$id;
		$this->order = (int)$this->input->post('d_order', TRUE);
		if ($this->id > 0) $this->Mydiy_model->ChangeOrder($this->id, $this->order);
		Redirect("/Mydiy/Show/".$this->session->flashdata('cat'));
	}
function ReOrder ($by = '', $sortby = '')
	{	
		$this->by = (int)$by;
		$this->sortby = CleanInput($sortby);
		if ($this->by > 0) $this->Mydiy_model->ReOrder($this->by, $this->sortby);
		Redirect("/Mydiy/Show/".$this->by);
		break;
	}
function Edit($itemid = '')
	{	
		$this->id = (int)$itemid;
		
		if ($this->id > 0) {
		$this->cat = (int)$this->session->flashdata('cat');
		$this->mysmarty->assign('cat', $this->cat);
		$this->mysmarty->assign('catlist', $this->Mydiy_model->GetAllCategories());
			
		$this->session->set_flashdata('cat', $this->cat);
		
		$this->displays = $this->Mydiy_model->GetItem($this->id);
		if ((int)$this->displays['d_cat'] != $this->cat) 
				{
				$this->session->set_flashdata('error_msg', 'This item does not match the category');
				redirect("/Mydiy/Show/".$this->cat);
				exit();
				}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('d_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('d_order', 'Order', 'trim|required|xss_clean');
		$this->form_validation->set_rules('d_cat', 'Category', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
											'd_title' => $this->input->post('d_title', TRUE),
											'd_order' => (int)$this->input->post('d_order'),
											'd_cat' => (int)$this->input->post('d_cat'),
											'd_desc' => $this->input->post('d_desc', TRUE)
											);	
					
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
					
				$this->editor = new FCKeditor('d_desc');
				
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				
				if (count($_POST) >	0) 
					{
						$this->editor->Value = $this->inputdata['d_desc'];
						$this->inputdata['d_desc'] = $this->editor->CreateHtml();				
					}
				else 
					{
						$this->editor->Value = $this->displays['d_desc'];
						$this->displays['d_desc'] = $this->editor->CreateHtml();
					}
											
				$this->mysmarty->assign('displays', $this->displays);
				
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('mydiy/mydiy_edit.html');
				exit();
			}
			else 
			{					
					$this->db_data = array(												 
											'd_title' => $this->form_validation->set_value('d_title'),
											'd_order' => (int)$this->form_validation->set_value('d_order'),
											'd_cat' => (int)$this->form_validation->set_value('d_cat'),
											'd_desc' => $this->input->post('d_desc', TRUE)
											);	
					
						$this->Mydiy_model->Update((int)$this->id,$this->db_data);
						redirect("/Mydiy/Show/".$this->db_data['d_cat']); exit();								
			}
	}
	else {
		redirect("/Mydiy/Show/".$this->cat."/");
	}
}
function Add($cat)
	{	
		$this->cat = (int)$cat;
		if ($this->cat > 0) {
		$this->mysmarty->assign('cat', $this->cat);
		$this->mysmarty->assign('catlist', $this->Mydiy_model->GetAllCategories());
		$this->session->set_flashdata('cat', $this->cat);
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('d_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('d_order', 'Order', 'trim|required|xss_clean');
							
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
									'd_title' => $this->input->post('d_title', TRUE),
									'd_order' => (int)$this->input->post('d_order'),
									'd_desc' => $this->input->post('d_desc', TRUE)
									);
				
				$this->displays = $this->Mydiy_model->GetMaxOrder($this->cat);
				
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('d_desc');
				if (count($_POST) >	0) $this->editor->Value = $this->inputdata['d_desc'];
				else $this->editor->Value = '';
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				$this->inputdata['d_desc'] = $this->editor->CreateHtml();
											
				$this->mysmarty->assign('displays', $this->displays);
					
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('mydiy/mydiy_add.html');
				exit();
			}
			else 
			{							
					$this->db_data = array(												 
											'd_title' => $this->form_validation->set_value('d_title'),
											'd_cat' => $this->cat,
											'd_order' => (int)$this->form_validation->set_value('d_order'),
											'd_desc' => $this->input->post('d_desc', TRUE)
											);													
						$this->Mydiy_model->Insert($this->db_data);
						redirect("/Mydiy/Show/".$this->cat."/"); exit();								
			}
	}
	else {
		redirect("/Mydiy");
	}
}	
}