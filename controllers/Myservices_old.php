<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myservices extends Controller {

function Myservices()
	{
		parent::Controller();
		
		$this->load->model('Myservices_model'); 
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
		$this->mysmarty->assign('area', 'Services');		
	}
	
function index()
	{	
		$this->mysmarty->assign('list', $this->Myservices_model->ListItems());	
		$this->mysmarty->assign('count', $this->Myservices_model->CountProducts());	
		$this->mysmarty->view('myservices/myservices_show.html');
	}

function MakeVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Myservices_model->MakeVisible($this->id);
		Redirect("/Myservices");
	}
function MakeNotVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Myservices_model->MakeNotVisible($this->id);
		Redirect("/Myservices");
	}
	
function MakeTop ($catid = '')
	{	
		$this->id = (int)$catid;
		if ($this->id > 0) $this->Myservices_model->MakeTop($this->id);
		Redirect("/Myservices");
	}
function UnTop ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Myservices_model->UnTop($this->id);
		Redirect("/Myservices");

	}
function Delete ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) 
			{
			$this->DeleteImageInSolution($this->id, TRUE);
			$this->Myservices_model->Delete($this->id);
			}
		
		Redirect("/Myservices");
	}

function ChangeOrder ($id = '')
	{	
		$this->id = (int)$id;
		$this->order = (int)$this->input->post('ordering', TRUE);
		if ($this->id > 0) $this->Myservices_model->ChangeOrder($this->id, $this->order);
		
		Redirect("/Myservices");

	}
function ReOrder ()
	{	
	   $this->Myservices_model->ReOrder();		
		Redirect("/Myservices");

	}
	
function Products($id = '')	{
		$this->id = (int)$id;
		if ($this->id == 0) exit();
		$this->mysmarty->assign('list', $this->Myservices_model->ListProducts((int)$id));
		$this->mysmarty->assign('alllist', $this->Myservices_model->ListAllProducts((int)$id));
		$this->mysmarty->assign('sid', $this->id);
		$this->mysmarty->view('myservices/myservices_list.html');	
}
function AddProduct($sid) {
		if ((int)$sid == 0) exit();
		
		if (isset($_POST)) {
		$this->Myservices_model->AddProducts((int)$this->input->post('p_id'), (int)$sid);
		Redirect("/Myservices/Products/".$sid);	
		}

}
function DeleteProducts($sid, $spid)
{
		$this->Myservices_model->DeleteProducts((int)$spid);
		Redirect("/Myservices/Products/".$sid);	
}
function Edit($itemid = '')
	{	
		$this->id = (int)$itemid;
		if ($this->id == 0) exit();
		$this->displays = $this->Myservices_model->GetItem($this->id);		
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('title', 'Title', 'trim|required|min_length[3]|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
											'title' => $this->input->post('title', TRUE),	
											'ordering' => (int)$this->input->post('ordering'),	
											'desc' => $this->input->post('desc', TRUE)
											);	
				
				
				
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
					
				$this->editor = new FCKeditor('desc');
				
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				
				if (count($_POST) >	0) 
					{
						$this->editor->Value = $this->inputdata['desc'];
						$this->inputdata['desc'] = $this->editor->CreateHtml();				
					}
				else 
					{
						$this->editor->Value = $this->displays['desc'];
						$this->displays['desc'] = $this->editor->CreateHtml();
					}

				$this->mysmarty->assign('displays', $this->displays);
				
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myservices/myservices_edit.html');
				exit();
			}
			else 
			{					
				
					$this->db_data = array(												 
											'title' => $this->form_validation->set_value('title'),
											'ordering' => (int)$this->input->post('ordering'),	
											'desc' => $this->input->post('desc', TRUE)
											);	
					
				
					if ($_FILES['image']['name'] != '') 
								{
									$image = $this->_UploadImage ('image', $this->config->config['paths']['imgproducts'], TRUE, $this->config->config['sizes']['productimg']['width'], $this->config->config['sizes']['productimg']['height']);				
									if ($image) {
										$oldimage = $this->Myservices_model->GetOldProductImage($this->id);
										if ($oldimage != '') {
											unlink($this->config->config['paths']['imgproducts'].'/'.$oldimage);
											unlink($this->config->config['paths']['imgproducts'].'/thumb_'.$oldimage);
											}
										$this->db_data['image'] = $image;					
									}
								}					

						$this->Myservices_model->Update((int)$this->id,$this->db_data);
						
						redirect("/Myservices"); exit();								
			}
}

function Add()
	{	

		$this->load->library('form_validation');

		$this->form_validation->set_rules('title', 'Title', 'trim|required|min_length[3]|xss_clean');
							
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
									'title' => $this->input->post('title', TRUE),
									'desc' => $this->input->post('desc', TRUE)
									);
				
				$this->displays = $this->Myservices_model->GetMaxOrder();

				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('desc');
				if (count($_POST) >	0) $this->editor->Value = $this->inputdata['desc'];
				else $this->editor->Value = '';
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				$this->inputdata['desc'] = $this->editor->CreateHtml();
						
				$this->mysmarty->assign('displays', $this->displays);
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myservices/myservices_add.html');
				exit();
			}
			else 
			{							
					$this->db_data = array(												 
											'title' => $this->form_validation->set_value('title'),
											'ordering' => (int)$this->input->post('ordering'),											
											'desc' => $this->input->post('desc', TRUE),
											'visible' => '1'
											);
	
					if ($_FILES['image']['name'] != '') 
								{
									$image = $this->_UploadImage ('image', $this->config->config['paths']['imgproducts'], TRUE, $this->config->config['sizes']['productimg']['width'], $this->config->config['sizes']['productimg']['height']);				
									if ($image) $this->db_data['image'] = $image;					
								}	
					

						$this->Myservices_model->Insert($this->db_data);
						
						redirect("/Myservices"); exit();								
			}

}

function DeleteImageInSolution($id = '', $nogo = FALSE)
	{
		$this->id = (int)$id;
		if ($this->id > 0)
				{
				$this->img = $this->Myservices_model->DeleteImage($this->id);
				if ($this->img != '') {
					unlink($this->config->config['paths']['imgproducts'].'/'.$this->img);
					unlink($this->config->config['paths']['imgproducts'].'/thumb_'.$this->img);
					
					}
				}
		if (!$nogo) {
		Redirect("/Myservices/Edit/".$this->id);
		}
	}

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
								
								$this->iconfig['width']	= $width;
								$this->iconfig['height'] = $height;
								
							$this->load->library('image_lib'); 
							$this->image_lib->initialize($this->iconfig);
							$this->imagesresult = $this->image_lib->resize();
							if ( $this->imagesresult != '1') { printcool ($this->image_lib->display_errors()); exit; }
							$this->image_lib->clear();
							
							
							$this->iconfig['image_library'] = 'gd2';
								$this->iconfig['source_image']	= $configpath.'/'.$this->imgdata['file_name'];
								$this->iconfig['create_thumb'] = FALSE;
								$this->iconfig['maintain_ratio'] = TRUE;
								$this->iconfig['new_image'] = 'main_'.$this->imgdata['file_name'];				
								$this->iconfig['width']	= '200';
								$this->iconfig['height'] = '200';
								
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
