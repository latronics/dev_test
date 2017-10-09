<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mynews extends Controller {

function Mynews()
	{
		parent::Controller();
		$this->load->model('Mynews_model'); 
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
		$this->mysmarty->assign('area', 'News');
	}
	
function index()
	{	
	Redirect("/Mynews/Show/");
	}
function Show($sortby = '') 
	{	
		$this->sortby = CleanInput($sortby);
		$this->session->set_flashdata('sortby', '/'.$this->sortby);
		$this->mysmarty->assign('sortby', $this->sortby);	
		$this->mysmarty->assign('list', $this->Mynews_model->ListItems($this->sortby));	
		$this->mysmarty->view('mynews/mynews_show.html');
	}

function MakeVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mynews_model->MakeVisible($this->id);
		Redirect("/Mynews/Show/".$this->session->flashdata('sortby'), 'refresh');
	}
function MakeNotVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mynews_model->MakeNotVisible($this->id);
		Redirect("/Mynews/Show/".$this->session->flashdata('sortby'), 'refresh');
	}
function MakeTop ($id = '')
	{	
		$this->id = (int)$id;	
		if ($this->id > 0) $this->Mynews_model->MakeTop($this->id);
		Redirect("/Mynews/Show/".$this->session->flashdata('sortby'), 'refresh');
	}
function MakeNotTop ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mynews_model->MakeNotTop($this->id);
		Redirect("/Mynews/Show/".$this->session->flashdata('sortby'), 'refresh');	
	}	
	
	
	
function Delete ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) 
			{
			$this->Mynews_model->Delete($this->id);
			}
		Redirect("/Mynews/Show/".$this->session->flashdata('sortby'), 'refresh');
	}
function Edit($itemid = '')
	{	
		$this->id = (int)$itemid;
		
		if ($this->id > 0) {

		$this->displays = $this->Mynews_model->GetItem($this->id);

		$this->load->library('form_validation');

		$this->form_validation->set_rules('n_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('n_title_bg', 'Title Bulgarian', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('n_date', 'Date', 'trim|required|xss_clean');

		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
											'n_title' => $this->input->post('n_title', TRUE),											
											'n_date' => $this->input->post('n_date', TRUE),
											'n_desc' => $this->input->post('n_desc')
											);	
					
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
					
				$this->editor = new FCKeditor('n_desc');
				
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				
				if (count($_POST) >	0) 
					{
						$this->editor->Value = $this->inputdata['n_desc'];
						$this->inputdata['n_desc'] = $this->editor->CreateHtml();				
					}
				else 
					{
						$this->editor->Value = $this->displays['n_desc'];
						$this->displays['n_desc'] = $this->editor->CreateHtml();
					}
							
				$this->mysmarty->assign('displays', $this->displays);
				
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('mynews/mynews_edit.html');
				exit();
			}
			else 
			{					

					$this->db_data = array(												 
											'n_title' => $this->form_validation->set_value('n_title'),											
											'n_date' => $this->form_validation->set_value('n_date'),
											'n_desc' => $this->input->post('n_desc')
											);	
					
					if ($_FILES['n_img']['name'] != '') 
								{
									$image = $this->_UploadImage ('n_img', $this->config->config['paths']['imgnews'], TRUE, $this->config->config['sizes']['newsimg']['width'], $this->config->config['sizes']['newsimg']['height']);				
									if ($image) {
										$oldimage = $this->Mynews_model->GetOldItemImage($this->id);										
										if ($oldimage != '') {
											unlink($this->config->config['paths']['imgnews']."/".$oldimage);
											unlink($this->config->config['paths']['imgnews'].'/thumb_'.$oldimage);
											}
										$this->db_data['n_img'] = $image;					
									}
								}	

			

						$this->Mynews_model->Update((int)$this->id,$this->db_data);
						redirect("/Mynews/Show/"); exit();								
			}
	}
	else {
		redirect("/Mynews/Show");
	}
}

function Add()
	{	

		$this->load->library('form_validation');

		$this->form_validation->set_rules('n_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('n_date', 'Date', 'trim|required|min_length[3]|xss_clean');
							
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
											'n_title' => $this->input->post('n_title', TRUE),											
											'n_date' => $this->input->post('n_date', TRUE),
											'n_desc' => $this->input->post('n_desc')
									);
				if ($this->inputdata['n_date'] == '') $this->inputdata['n_date'] = CurrentDate();
				
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('n_desc');
				
				if (count($_POST) >	0) $this->editor->Value = $this->inputdata['n_desc'];
				else $this->editor->Value = '';
				
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				
				$this->inputdata['n_desc'] = $this->editor->CreateHtml();				
				
				if (count($_POST) == 0) {
				$this->displays['n_date'] = CurrentDate();
				$this->mysmarty->assign('displays', $this->displays);
				}
					
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('mynews/mynews_add.html');
				exit();
			}
			else 
			{							
					$this->db_data = array(												 
											'n_title' => $this->form_validation->set_value('n_title'),
											'n_date' => $this->form_validation->set_value('n_date'),
											'n_desc' => $this->input->post('n_desc')
											);													
					
					if ($_FILES['n_img']['name'] != '') 
								{
									$image = $this->_UploadImage ('n_img', $this->config->config['paths']['imgnews'], TRUE, $this->config->config['sizes']['newsimg']['width'], $this->config->config['sizes']['newsimg']['height']);	
									
									if ($image) {
										$this->db_data['n_img'] = $image;					
									}
								}
								
						$this->Mynews_model->Insert($this->db_data);
						redirect("/Mynews/Show"); break; exit();								
			}
}
function DeleteImageInItem($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) 
				{
				$this->img = $this->Mynews_model->DeleteItemImage($this->id);
				if ($this->img != '') {
					unlink($this->config->config['paths']['imgnews'].'/'.$this->img);
					unlink($this->config->config['paths']['imgnews'].'/thumb_'.$this->img);
					
					}
				}
		Redirect("/Mynews/Edit/".$this->id);
	}
	
	
	
	///////////////////////////
	


function _UploadImage ($fieldname = '', $configpath = '', $thumb = FALSE, $width = '', $height = '') 
	{
		if (($fieldname != '') || ($configpath != '') || ((int)$width != 0) || ((int)$height != 0)) 
		{
						
						$this->uconfig['upload_path'] = $configpath;
						$this->uconfig['allowed_types'] = 'gif|jpg|png|bmp';
						$this->uconfig['remove_spaces'] = TRUE;
						$this->uconfig['max_size'] = '1900';
						$this->uconfig['max_filename'] = '145';	
						
						$this->load->library('upload', $this->uconfig);

						$this->uploadresult = $this->upload->do_upload($fieldname);
						$this->imgdata = $this->upload->data();

						if ( !$this->uploadresult) { printcool ($this->upload->display_errors()); exit; }

						if (($this->imgdata['image_width'] > $width) || ($this->imgdata['image_height'] > $height)) 
						{
								$this->iconfig['image_library'] = 'gd2';
								$this->iconfig['source_image']	= $configpath.'/'.$this->imgdata['file_name'];
								if (!$thumb) $this->iconfig['create_thumb'] = FALSE;
								else $this->iconfig['create_thumb'] = TRUE;
								$this->iconfig['maintain_ratio'] = TRUE;
								if ($this->imgdata['image_width'] > $this->imgdata['image_height']) $this->iconfig['width']	= $width;
								else $this->iconfig['height'] = $height;
								
							$this->load->library('image_lib'); 
							$this->image_lib->initialize($this->iconfig);
							$this->imagesresult = $this->image_lib->resize();
							if ( $this->imagesresult != '1') { printcool ($this->image_lib->display_errors()); exit; }
							$this->image_lib->clear();						
						}

		return ($this->imgdata['file_name']);
		}
}

	
	
}
