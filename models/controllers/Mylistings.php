<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myebay extends Controller {

function Myebay()
	{
		parent::Controller();		
		$this->load->model('Myebay_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Ebay');		
	}

function index()
	{	
		$this->ListItems();
	}

function ListItems($page = 1)
	{
		$session_search = $this->session->userdata('last_string');

		if (isset($_POST['search'])) $string = $this->input->post('search', TRUE);		
		elseif ($session_search) $string = $this->session->userdata('last_string');
		else $string = '';
		
		$this->session->set_userdata('last_string', $string);
		$this->mysmarty->assign('string', $string);	
		$data = $this->Myebay_model->ListItems($string, $page);	
		$this->mysmarty->assign('list', $data['results']);
		$this->mysmarty->assign('pages', $data['pages']);
		$this->mysmarty->assign('page', (int)$page);
			
		$this->mysmarty->view('myebay/myebay_show.html');
	}
function CleanSearch()
	{
		$this->session->unset_userdata('last_string');
		Redirect('Myebay');
	}
function GetSource($itemid = '')
	{
		$this->id = (int)$itemid;
		if ($this->id == 0)	Redirect('Myebay');		
		$this->displays = $this->Myebay_model->GetItem($this->id);			
		$this->_GetSpecialAndTree();
		$this->mysmarty->assign('displays', $this->displays);
		$this->mysmarty->view('myebay/myebay_source.html');
	}

function Delete ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0)
			{
			$this->DeleteImageInEbay($this->id, '1', TRUE);
			$this->DeleteImageInEbay($this->id, '2', TRUE);
			$this->DeleteImageInEbay($this->id, '3', TRUE);
			$this->DeleteImageInEbay($this->id, '4', TRUE);
			$this->Myebay_model->Delete($this->id);
			}
		Redirect("Myebay");
	}
function Edit($itemid = '')
	{	
		$this->id = (int)$itemid;
	
		if ($this->id > 0) {
		
		$this->displays = $this->Myebay_model->GetItem($this->id);			
		$this->_GetSpecialAndTree();
		$this->load->library('form_validation');

		$this->form_validation->set_rules('e_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('e_manuf', 'Manufacturer', 'trim|xss_clean');
		$this->form_validation->set_rules('e_model', 'Model', 'trim|xss_clean');
		$this->form_validation->set_rules('e_part', 'Part Number', 'trim|xss_clean');
		$this->form_validation->set_rules('e_compat', 'Compatibility', 'trim|xss_clean');
		$this->form_validation->set_rules('e_package', 'Package', 'trim|xss_clean');
		$this->form_validation->set_rules('e_condition', 'Condition', 'trim|xss_clean');
		$this->form_validation->set_rules('e_shipping', 'Shipping', 'trim|xss_clean');
		$this->form_validation->set_rules('e_desc', 'Description', 'trim|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
									'e_title' => $this->input->post('e_title', TRUE),
									'e_manuf' => $this->input->post('e_manuf', TRUE),
									'e_model' => $this->input->post('e_model', TRUE),
									'e_part' => $this->input->post('e_part', TRUE),
									'e_compat' => $this->input->post('e_compat', TRUE),
									'e_package' => $this->input->post('e_package', TRUE),
									'e_condition' => $this->input->post('e_condition', TRUE),
									'e_shipping' => $this->input->post('e_shipping', TRUE),
									'e_notice_header' => (int)$this->input->post('e_notice_header', TRUE),
									'e_notice_shipping' => (int)$this->input->post('e_notice_shipping', TRUE),
									'e_shipping' => $this->input->post('e_shipping', TRUE),
									'e_desc' => $this->input->post('e_desc', TRUE)
									);
								
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
					
				$this->editor = new FCKeditor('e_desc');
				
				$this->editor->Width = "350";
				$this->editor->Height = "220";
				$this->editor->ToolbarSet = "Small";
				
				if (count($_POST) >	0) 
					{
						$this->editor->Value = $this->inputdata['e_desc'];
						$this->inputdata['e_desc'] = $this->editor->CreateHtml();				
					}
				else 
					{
						$this->editor->Value = $this->displays['e_desc'];
						$this->displays['e_desc'] = $this->editor->CreateHtml();
					}
				
				$this->mysmarty->assign('displays', $this->displays);
				
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myebay/myebay_edit.html');
				exit();
			}
			else 
			{					
				
					$this->db_data = array(												 
											'e_title' => $this->form_validation->set_value('e_title'),
											'e_sef' => $this->_CleanSef($this->form_validation->set_value('e_title')),
											'e_manuf' => $this->form_validation->set_value('e_manuf'),
											'e_model' => $this->form_validation->set_value('e_model'),
											'e_part' => $this->form_validation->set_value('e_part'),
											'e_compat' => $this->form_validation->set_value('e_compat'),
											'e_package' => $this->form_validation->set_value('e_package'),
											'e_condition' => $this->form_validation->set_value('e_condition'),
											'e_shipping' => $this->form_validation->set_value('e_shipping'),
											'e_notice_header' => (int)$this->input->post('e_notice_header', TRUE),
											'e_notice_shipping' => (int)$this->input->post('e_notice_shipping', TRUE),
											'e_desc' => $this->form_validation->set_value('e_desc')
											);
								
					$this->checkexists =  $this->Myebay_model->CheckSefExists($this->db_data['e_sef'], $this->id);
					if ($this->checkexists) $this->pref = rand(1,9).rand(1,9).rand(1,9); else $this->pref = '';
					$this->db_data['e_sef'] = $this->db_data['e_sef'].$this->pref;
					
					$this->productimages = array(1,2,3,4);
					
					$this->load->library('upload');
					$watermark = FALSE;
					foreach($this->productimages as $value)
					{			if ($_FILES['e_img'.$value]['name'] != '') 
								{
									$this->_CheckImageDirExist(idpath((int)$this->id));
										
									$newname[$value] = (int)$this->id.'_'.substr($this->_CleanSef($this->db_data['e_title']), 0, 210).'_'.$value;
									$image[$value] = $this->_UploadImage ('e_img'.$value, $this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id), TRUE, $this->config->config['sizes']['ebayimg']['width'], $this->config->config['sizes']['ebayimg']['height'], FALSE, TRUE, $newname[$value]);				
									if ($image[$value]) {
										$oldimage[$value] = $this->Myebay_model->GetOldEbayImage($this->id, $value);
										if ($oldimage[$value] != '' && $image[$value] != $oldimage[$value]) {
											if ($value == 1) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'Ebay_'.$oldimage[$value]);
											unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).$oldimage[$value]);
											unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_'.$oldimage[$value]);
											unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_main_'.$oldimage[$value]);
											}									
										
										$this->db_data['e_img'.$value] = $image[$value];
										$this->db_data['idpath'] = str_replace('/', '', idpath((int)$this->id));
										$watermark = TRUE;
									}	
								}
					}
				
						$this->Myebay_model->Update((int)$this->id,$this->db_data);
						
						if ($watermark) Redirect('Myebay/DoWaterMark/'.(int)$this->id);
						else  redirect("Myebay/GetSource/".(int)$this->id);					
			}
	}
	else {
			redirect("/Myebay");
	}
}
function DoWaterMark($id, $place = 1)
	{
		$img = $this->Myebay_model->GetOldEbayImage((int)$id, $place);
		
		if ($place == 1 && $img)
		{
			if (!copy($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).$img, $this->config->config['paths']['imgebay'].'/'.idpath((int)$id).'Ebay_'.$img)) {
				echo "failed to copy file...\n";
				break;
			}
			$this->iconfig['image_library'] = 'gd2';
			$this->iconfig['source_image']	= $this->config->config['paths']['imgebay'].'/'.idpath((int)$id).'Ebay_'.$img;
			$this->iconfig['create_thumb'] = FALSE;
			$this->iconfig['maintain_ratio'] = TRUE;
			$this->iconfig['width']	= '600';
		
			$this->load->library('image_lib'); 
			$this->image_lib->initialize($this->iconfig);
			$this->imagesresult = $this->image_lib->resize();
			if ( $this->imagesresult != '1') { printcool ($this->image_lib->display_errors()); exit; }
			$this->image_lib->clear();
			
			$this->_WaterMark('bottom', 'right', 'wm_original_ebay.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), 'Ebay_'.$img);
		}
		if ($img)
		{
			$this->_WaterMark('bottom', 'right', 'wm_original_bottom.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), $img);
			$this->_WaterMark('middle', 'center', 'wm_original_center.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), $img);
			$this->_WaterMark('bottom', 'right', 'wm_bigtn_bottom.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), 'thumb_main_'.$img);
			$this->_WaterMark('middle', 'center', 'wm_bigtn_center.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), 'thumb_main_'.$img);
			$this->_WaterMark('bottom', 'right', 'wm_smalltn_bottom.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), 'thumb_'.$img);	
		}
	$place++;
	
	if ($place >4) redirect("/Myebay/GetSource/".(int)$id);
	else Redirect('Myebay/DoWaterMark/'.(int)$id.'/'.$place);
	
	}
function Add()
	{	
		$this->_GetSpecialAndTree();
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('e_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('e_manuf', 'Manufacturer', 'trim|xss_clean');
		$this->form_validation->set_rules('e_model', 'Model', 'trim|xss_clean');
		$this->form_validation->set_rules('e_part', 'Part Number', 'trim|xss_clean');
		$this->form_validation->set_rules('e_compat', 'Compatibility', 'trim|xss_clean');
		$this->form_validation->set_rules('e_package', 'Package', 'trim|xss_clean');
		$this->form_validation->set_rules('e_condition', 'Condition', 'trim|xss_clean');
		$this->form_validation->set_rules('e_shipping', 'Shipping', 'trim|xss_clean');
		$this->form_validation->set_rules('e_desc', 'Description', 'trim|xss_clean');
				
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
									'e_title' => $this->input->post('e_title', TRUE),
									'e_manuf' => $this->input->post('e_manuf', TRUE),
									'e_model' => $this->input->post('e_model', TRUE),
									'e_part' => $this->input->post('e_part', TRUE),
									'e_compat' => $this->input->post('e_compat', TRUE),
									'e_package' => $this->input->post('e_package', TRUE),
									'e_condition' => $this->input->post('e_condition', TRUE),
									'e_shipping' => $this->input->post('e_shipping', TRUE),
									'e_notice_header' => (int)$this->input->post('e_notice_header', TRUE),
									'e_notice_shipping' => (int)$this->input->post('e_notice_shipping', TRUE),
									'e_desc' => $this->input->post('e_desc', TRUE)
									);
				
				if (count($_POST) == 0) $this->inputdata['e_shipping'] = 'United States Postal Service.
We ship Internationally.
We use primarily USPS and FedEx';
								
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('e_desc');
				if (count($_POST) >	0) $this->editor->Value = $this->inputdata['e_desc'];
				else $this->editor->Value = '';
				$this->editor->Width = "350";
				$this->editor->Height = "220";
				$this->editor->ToolbarSet = "Small";				
				$this->inputdata['e_desc'] = $this->editor->CreateHtml();
					
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myebay/myebay_add.html');
				exit();
			}
			else 
			{							
					$this->db_data = array(												 
											'e_title' => $this->form_validation->set_value('e_title'),
											'e_sef' => $this->_CleanSef($this->form_validation->set_value('e_title')),
											'e_manuf' => $this->form_validation->set_value('e_manuf'),
											'e_model' => $this->form_validation->set_value('e_model'),
											'e_part' => $this->form_validation->set_value('e_part'),
											'e_compat' => $this->form_validation->set_value('e_compat'),
											'e_package' => $this->form_validation->set_value('e_package'),
											'e_condition' => $this->form_validation->set_value('e_condition'),
											'e_shipping' => $this->form_validation->set_value('e_shipping'),
											'e_notice_header' => (int)$this->input->post('e_notice_header', TRUE),
											'e_notice_shipping' => (int)$this->input->post('e_notice_shipping', TRUE),
											'e_desc' => $this->form_validation->set_value('e_desc')
											);
					
					$this->checkexists =  $this->Myebay_model->CheckSefExists($this->db_data['e_sef']);
					if ($this->checkexists) $this->pref = rand(1,9).rand(1,9).rand(1,9); else $this->pref = '';
					$this->db_data['e_sef'] = $this->db_data['e_sef'].$this->pref;
					
					$this->load->library('upload');
					
						$this->newid = $this->Myebay_model->Insert($this->db_data);
						
					///Update Images	
						$this->productimages = array(1,2,3,4);
						$watermark = FALSE;
						foreach($this->productimages as $value)
						{			if ($_FILES['e_img'.$value]['name'] != '') 
									{
									
									$this->_CheckImageDirExist(idpath($this->newid));
									
										$newname[$value] = (int)$this->newid.'_'.substr($this->_CleanSef($this->db_data['e_title']), 0, 210).'_'.$value;
										$image[$value] = $this->_UploadImage ('e_img'.$value, $this->config->config['paths']['imgebay'].'/'.idpath($this->newid), TRUE, $this->config->config['sizes']['ebayimg']['width'], $this->config->config['sizes']['ebayimg']['height'], FALSE, TRUE, $newname[$value]);				
										if ($image[$value]) {
											$this->newdb_data['e_img'.$value] = $image[$value];					
											$watermark = TRUE;	
										}
									$this->newdb_data['idpath'] = str_replace('/', '', idpath($this->newid));
									}	
	
						}	
						if (isset($this->newdb_data)) $this->Myebay_model->Update((int)$this->newid, $this->newdb_data);
						
						if ($watermark) Redirect('Myebay/DoWaterMark/'.(int)$this->newid);
						else redirect("Myebay/GetSource/".(int)$this->newid);							
			}
}

function _CheckImageDirExist($path)
{
			$this->load->helper('directory');
			$this->load->helper('file');
			$dir = directory_map($this->config->config['paths']['imgebay'].'/'.$path);
			if (!$dir && !is_array($dir))
			{		
				if (!mkdir($this->config->config['paths']['imgebay'].'/'.$path)) die('Failed to create folder...');
			}				
			if (!read_file($this->config->config['paths']['imgebay'].'/'.$path.'index.html'))
			{
				if (!write_file($this->config->config['paths']['imgebay'].'/'.$path.'index.html', $this->_indexhtml($path))) echo 'Unable to write Directory Index for '.$path;
			}
			if (!read_file($this->config->config['paths']['imgebay'].'/'.$path.'.htaccess'))
			{
				if (!write_file($this->config->config['paths']['imgebay'].'/'.$path.'.htaccess', $this->_htaccess($path))) echo 'Unable to write .htaccess for '.$path;
			}
}


function _indexhtml($path = '')
{ 	
		$msg_data = array ('msg_title' => $this->config->config['siteabrv'].': GENERATED INDEX for Path: '.$path,'msg_body' => '@ '.CurrentTimeR(),'msg_date' => CurrentTime());							
		GoMail($msg_data);
		return '<html><head><title>403 Forbidden</title></head><body>403 forbidden.</body></html>	';	
}
	
function _htaccess($path = '')
{ 		
		$msg_data = array ('msg_title' => $this->config->config['siteabrv'].': GENERATED .htaccess for Path: '.$path,'msg_body' => '@ '.CurrentTimeR(),'msg_date' => CurrentTime());							
		GoMail($msg_data);
	 	/*return 'RemoveHandler .php .phtml .php3
RemoveType .php .phtml .php3
php_flag engine off
<IfModule mod_php5.c>
  php_value engine off
</IfModule>
<IfModule mod_php4.c>
  php_value engine off
</IfModule>';*/
return '<IfModule mod_php5.c>
  php_value engine off
</IfModule>
<IfModule mod_php4.c>
  php_value engine off
</IfModule>
';

}

function DeleteImageInEbay($id = '', $place = '', $nogo = FALSE)
	{
		$this->id = (int)$id;
		$this->place = (int)$place;
		if (($this->id > 0) && ($this->place > 0))
				{
				$this->img = $this->Myebay_model->DeleteEbayImage($this->id, $this->place);
				if ($this->img != '') {
					if ($this->place == 1) unlink($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'Ebay_'.$this->img);
					unlink($this->config->config['paths']['imgebay'].'/'.idpath($this->id).$this->img);
					unlink($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'thumb_'.$this->img);
					unlink($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'thumb_main_'.$this->img);
					
					}
				}
		if (!$nogo) {
		Redirect("Myebay/Edit/".$this->id);
		}
	}
	
	///////////////////////////
	

function _CleanSef ($string) {
	$this->inputstring = str_replace(" ", "-", $string);
	$this->inputstring = str_replace("_", "-", $this->inputstring);
	$this->cyrchars = array('А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ч','Ц','Ш','Щ','Ъ','Ь','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ч','ц','ш','щ','ъ','ь','ю','я');							 
	$this->latinchars = array('A','B','V','G','D','E','J','Z','I','I','K','L','M','N','O','P','R','S','T','U','F','H','CH','TS','SH','SHT','U','U','JU','YA','a','b','v','g','d','e','j','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','ch','ts','sh','sht','u','u','ju','ya');							 
	$this->inputstring = str_replace($this->cyrchars, $this->latinchars, $this->inputstring);	
	$this->inputstring = str_replace('---', '-', $this->inputstring);	
	$this->inputstring = str_replace('--', '-', $this->inputstring);
	$this->inputstring = ereg_replace("[^A-Za-z0-9\-]", "", $this->inputstring);
	return $this->inputstring;
	}

function _UploadImage ($fieldname = '', $configpath = '', $thumb = FALSE, $width = '', $height = '', $justupload = FALSE, $wm = FALSE, $filename = FALSE) 
	{
		if (($fieldname != '') || ($configpath != '') || ((int)$width != 0) || ((int)$height != 0)) 
		{						
						$uconfig['upload_path'] = $configpath;
						$uconfig['allowed_types'] = 'gif|jpg|png|bmp';
						$uconfig['remove_spaces'] = TRUE;
						$uconfig['max_size'] = '1900';
						$uconfig['max_filename'] = '240';	
						if ($filename)$uconfig['file_name'] = $filename;
						//printcool ($filename);
						//printcool( $uconfig);

						$this->upload->initialize($uconfig);						
						$this->uploadresult = $this->upload->do_upload($fieldname);
						$processimgdata = $this->upload->data();
						//printcool($processimgdata['file_name']);
						if ( !$this->uploadresult) { printcool ($this->upload->display_errors()); exit; }

						if (!$justupload) {
						if (($processimgdata['image_width'] > $width) || ($processimgdata['image_height'] > $height)) 
						{
								$this->iconfig['image_library'] = 'gd2';
								$this->iconfig['source_image']	= $configpath.'/'.$processimgdata['file_name'];
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
							
							
							$this->nconfig['image_library'] = 'gd2';
								$this->nconfig['source_image']	= $configpath.'/'.$processimgdata['file_name'];
								$this->nconfig['maintain_ratio'] = TRUE;
								$this->nconfig['new_image'] = 'main_'.$processimgdata['file_name'];
								$this->nconfig['width']	= '200';
								$this->nconfig['height'] = '200';
								
		
							$this->image_lib->initialize($this->nconfig);
							$this->imagesresult = $this->image_lib->resize();
							if ( $this->imagesresult != '1') { printcool ($this->image_lib->display_errors()); exit; }
							$this->image_lib->clear();							
						}
							
						}
		//sleep(0.5);
		return ($processimgdata['file_name']);
		}
}
function _WaterMark($val, $hal, $wm, $path = '', $file = '')
{
							$this->load->library('image_lib'); 
							$config['source_image']	= $path.'/'.$file;
							$config['wm_type'] = 'overlay';
							$config['wm_overlay_path'] = $this->config->config['pathtopublic'].'/images/'.$wm;
							$config['wm_vrt_alignment'] = $val;
							$config['wm_hor_alignment'] = $hal;
							$config['create_thumb'] = FALSE;
							$config['wm_padding'] = '0';
							//printcool ($config);							
							$this->image_lib->initialize($config); 								
							$this->image_lib->watermark();
							//printcool ($this->image_lib->display_errors());
							$this->image_lib->clear();
							
}
	
function _clean_file_name($filename)
	{
		$bad = array(
						"<!--",
						"-->",
						"'",
						"<",
						">",
						'"',
						'&',
						'$',
						'=',
						';',
						'?',
						'/',
						"%20",
						"%22",
						"%3c",		// <
						"%253c", 	// <
						"%3e", 		// >
						"%0e", 		// >
						"%28", 		// (
						"%29", 		// )
						"%2528", 	// (
						"%26", 		// &
						"%24", 		// $
						"%3f", 		// ?
						"%3b", 		// ;
						"%3d"		// =
					);
					
		$filename = str_replace($bad, '', $filename);

		return stripslashes($filename);
	}

function _GetSpecialAndTree()
	{
		$this->load->model('Myproducts_model'); 	
		$this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllCategories());
		$this->mysmarty->assign('specials', $this->Myebay_model->GetTopSpecialAds());		
	}
	
	
	
	
	
	
	
////////////TEST


function test()
	{
	
	exit();
	
	/*
		$this->load->helper('directory');
		$this->load->helper('file');
$list = array();
		$dir = directory_map($this->config->config['paths']['imgebay']);
		foreach ($dir as $kd => $vd)
		{
		
			if (!is_array($vd))
			{
				if ((substr($vd, -5) == '1.JPG') || (substr($vd, -5) == '1.jpg') || (substr($vd, -5) == '1.gif') || (substr($vd, -5) == '1.GIF') || (substr($vd, -5) == '1.png') || (substr($vd, -5) == '1.PNG')) $list[] = $vd;
				else { }
			}
		}		
			
			printcool ($list);
			
	*/	
			
			
		$this->db->select("e_id, idpath, e_img1, e_img2, e_img3, e_img4");	
		
		/*$factor = 2;
		$range = $factor*100;
		$do = array ($range-100, $range);
		$this->db->where('e_id >=', $do[0]);
		$this->db->where('e_id <', $do[1]);
		printcool ($factor.' | '.$do[0].' - '.$do[1]);	
		*/

		$this->db->where('e_id >=', 4000);
		$this->db->where('e_id <', 5000);
		$this->query = $this->db->get('ebay');
		$d = $this->query->result_array();

		/*foreach($d as $k => $v)
		{
		
				printcool ($v['e_id'].' - '.ceil($v['e_id'] / 100).'/');
		}
		break;*/
		foreach($d as $k => $v)
		{
		
		exit();
			//$this->db->update('ebay', array('idpath' => str_replace('/', '', idpath((int)$v['e_id']))), array('e_id' => (int)$v['e_id']));
			//printcool (str_replace('/', '', idpath((int)$v['e_id'])));
			
			$loop = array(1,2,3,4);
			foreach ($loop as $lk => $lv)
			{
				if ($v['e_img'.$lv] != '')
				{				
					$this->_CheckImageDirExist(idpath((int)$v['e_id']));				
					
					if (read_file($this->config->config['paths']['imgebay'].'/Ebay_'.$v['e_img'.$lv]))
					{
						if (!copy($this->config->config['paths']['imgebay'].'/Ebay_'.$v['e_img'.$lv], $this->config->config['paths']['imgebay'].'/'.idpath((int)$v['e_id']).'Ebay_'.$v['e_img'.$lv])) {
							echo "failed to copy ".$this->config->config['paths']['imgebay'].'/Ebay_'.$v['e_img'.$lv]."...\n";
						}
					}
					
					if (read_file($this->config->config['paths']['imgebay'].'/'.$v['e_img'.$lv]))
					{
						if (!copy($this->config->config['paths']['imgebay'].'/'.$v['e_img'.$lv], $this->config->config['paths']['imgebay'].'/'.idpath((int)$v['e_id']).$v['e_img'.$lv])) {
							echo "failed to copy ".$this->config->config['paths']['imgebay'].$v['e_img'.$lv]."...\n";
						}
					}
					
					if (read_file($this->config->config['paths']['imgebay'].'/thumb_'.$v['e_img'.$lv]))
					{
						if (!copy($this->config->config['paths']['imgebay'].'/thumb_'.$v['e_img'.$lv], $this->config->config['paths']['imgebay'].'/'.idpath((int)$v['e_id']).'thumb_'.$v['e_img'.$lv])) {
							echo "failed to copy ".$this->config->config['paths']['imgebay'].'/thumb_'.$v['e_img'.$lv]."...\n";
						}
					}
					
					if (read_file($this->config->config['paths']['imgebay'].'/thumb_main_'.$v['e_img'.$lv]))
					{
						if (!copy($this->config->config['paths']['imgebay'].'/thumb_main_'.$v['e_img'.$lv], $this->config->config['paths']['imgebay'].'/'.idpath((int)$v['e_id']).'thumb_main_'.$v['e_img'.$lv])) {
							echo "failed to copy ".$this->config->config['paths']['imgebay'].'/thumb_main_'.$v['e_img'.$lv]."...\n";
						}
					}
					
				}					
			}								
		}
	}



}
