<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mypartners extends Controller {

function Mypartners()
	{
		parent::Controller();
		$this->load->model('Mypartners_model'); 
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
		$this->mysmarty->assign('area', 'Partners');
		$this->go = DoGo($this->router->class, $this->router->method);	
		$this->mysmarty->assign('go', $this->go);
	
	}
	
function index()
	{	
		$this->mysmarty->assign('list', $this->Mypartners_model->ListAll());
		$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_main.html');
	}
function Add() 
	{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('title', 'Name or Title', 'trim|required|min_length[3]|xss_clean');
	
					$this->inputdata['title'] = $this->input->post('title', TRUE);
					$this->inputdata['url'] = $this->input->post('url', TRUE);

			if ($this->form_validation->run() == FALSE)
			{	
				$this->mysmarty->assign('inputdata', $this->inputdata);
				$this->mysmarty->assign('list', $this->Mypartners_model->ListAll());
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_main.html');
				exit();
			}
			else 
			{							
				$this->db_data['title'] = $this->form_validation->set_value('title');
				$this->db_data['url'] = $this->input->post('url', TRUE);
				$this->db_data['ordering'] = $this->Mypartners_model->GetMaxOrder();		
				
				if ($_FILES['logo']['name'] != '') 
								{
									$image = $this->_UploadImage('logo', $this->config->config['paths']['imgpartners'], TRUE, $this->config->config['sizes']['partners']['width'], $this->config->config['sizes']['partners']['height']);				
									if ($image) {
										$this->db_data['logo'] = $image;					
									}
								}	
								
								
				$this->Mypartners_model->Insert($this->db_data);
				Redirect("/".$this->go['ctr']); 					
			}	
	}
function Update($id) 
	{
	$this->id = (int)$id;
	if ($this->id > 0) 
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('title', 'Name or Title', 'trim|required|min_length[3]|xss_clean');
		
					$this->inputdata[$this->id] = array(	
											'rid' => $this->id,
											'title' => $this->input->post('title', TRUE),
											'url' => $this->input->post('url', TRUE)
											);														
			if ($this->form_validation->run() == FALSE)
			{	
				$this->mysmarty->assign('inputdata', $this->inputdata);	
				$this->mysmarty->assign('list', $this->Mypartners_model->ListAll());
				$this->mysmarty->assign('errors', array( $this->id => $this->form_validation->_error_array));
				$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_main.html');
				exit();
			}
			else 
			{							
				$this->db_data['title'] = $this->form_validation->set_value('title');	
				$this->db_data['url'] = $this->input->post('url', TRUE);
				
				$this->Mypartners_model->Update($this->id, $this->db_data);
				Redirect("/".$this->go['ctr']); 
			}
		}
	}
function Delete($id)
	{
		$this->id = (int)$id;
		if ($this->id > 0) 
				{
				$oldimage = $this->Mypartners_model->GetOldImage($this->id);
				if ($oldimage) {
						unlink($this->config->config['paths']['imgpartners'].'/'.$oldimage);
						}
				$this->Mypartners_model->Delete($this->id);
				}
		Redirect("/".$this->go['ctr']);
	}	
	

function MakeVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mypartners_model->MakeVisible($this->id);		
		Redirect("/".$this->go['ctr']);
	}
function MakeNotVisible ($id = '')
	{	
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mypartners_model->MakeNotVisible($this->id);
		Redirect("/".$this->go['ctr']);
	}
	
	
function ChangeOrder ($id = '')
	{	
		$this->id = (int)$id;
		$this->order = (int)$this->input->post('ordering', TRUE);
		if ($this->id > 0) $this->Mypartners_model->ChangeOrder($this->id, $this->order);		
		Redirect("/".$this->go['ctr']);

	}
function ReOrder()
	{	
		$this->Mypartners_model->ReOrder();
		Redirect("/".$this->go['ctr']);
		
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
								$this->iconfig['create_thumb'] = FALSE;								
								$this->iconfig['maintain_ratio'] = TRUE;								
								//$this->iconfig['width']	= $width;
								$this->iconfig['height'] = $height;
								
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
