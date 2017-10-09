<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myestimates extends Controller {
	
function Myestimates()
	{
		parent::Controller();		
		$this->_Start();
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('area', 'EST');	
		
	}
	
function index()
	{	
		$this->db->order_by("date", "DESC");
		$this->query = $this->db->get('forms_request');
		
		if ($this->query->num_rows() > 0) $data = $this->query->result_array();
		else $data = FALSE;
		
		$this->db->select('f_id');
		$this->db->distinct();
		$query = $this->db->get('forms_request_comm');
		if ($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $f)
				{
				$matched[$f['f_id']] = TRUE;
				}
			}
		else $matched = array();
		
		$this->mysmarty->assign('replies', $matched);
		$this->mysmarty->assign('requests', $data);
		$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_main.html');
	}

function Read($cid = '') 
{
		$this->_GoHome($cid);
	
		
		$this->db->where('fid', (int)$cid);	
		$this->query = $this->db->get('forms_request');
		
		if ($this->query->num_rows() > 0) $data = $this->query->row_array();
		else Redirect('Myestimates/');
			
		$this->mysmarty->assign('data', $data);
		
			
		$this->db->where('f_id', (int)$cid);
		$this->db->order_by('fc_id', 'DESC');		
		$cquery = $this->db->get('forms_request_comm');
		
		$msgs = 0;
		if ($cquery->num_rows() > 0) {
				$comm = $cquery->result_array();
				$msgs = count($comm);	
			}
		else $comm = FALSE;

		if (isset($_POST['msg']))
			{
				
				$cdata['f_msg'] = $this->input->post('msg',TRUE);
				
				$cdata['f_id'] = (int)$cid;
				$cdata['f_owner'] = 'admin';
				$cdata['f_time'] = CurrentTime();
							
				$this->db->insert('forms_request_comm', $cdata);
				
				$re = '';

				if ($msgs > 0)
					{
						$i = 1;
						while ($i <= $msgs) {
							$re = 'RE: '.$re;
							$i++;
						}	
					}
		
					
					if ($data['type'] == '1') $type = 'free estimate quote';
					else $type = 'part inquiry';
					
								$msg_data = array ('msg_title' => $re.'Reply regarding '.$type.'.',
												'msg_body' => $cdata['f_msg'],
												'msg_date' => CurrentTime()
											);
								if ($data['type'] == 1) $msg_data['msg_body'] .= '<br>To continue with the laptop repair form, follow this link:<Br><br><a href="'.Site_url().'Estimate/'.$data['code'].'/'.$data['fid'].'" target="_blank">'.Site_url().'Estimate/'.$data['code'].'/'.$data['fid'].'</a>';
						
							GoMail($msg_data, $data['email']);
							
					Redirect ('Myestimates/Read/'.$cid);
					
			}
			
		require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('msg');
				$this->editor->Width = "650";
				$this->editor->Height = "300";
				$this->editor->ToolbarSet	= 'Basic' ;
				$this->editor->Value = '';
			
		$this->mysmarty->assign('msg', $this->editor->CreateHtml());
		$this->mysmarty->assign('cid', (int)$cid);		
		$this->mysmarty->assign('comm', $comm);
		$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_read.html');

}
function Delete ($fid = '') 
{
		$this->db->where('fid', (int)$fid);
		$this->db->delete('forms_request'); 
		$this->_GoHome();
}
	
////
	
function _GoHome($id = '') 
{
		if ((int)$id == 0) { 
			Redirect("/".$this->go['ctr']);
			exit();
			}
}
	
function _Start() 
{
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();	
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->go = DoGo($this->router->class, $this->router->method);	
		$this->mysmarty->assign('go', $this->go);	
}

}