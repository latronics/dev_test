<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mydebug extends Controller {

function Mydebug()
	{
		parent::Controller();		
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		
		//if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		//if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('action',$this->session->flashdata('action'));
		$this->mysmarty->assign('gotoebay',$this->session->flashdata('gotoebay'));
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Ebay');	
		$this->mysmarty->assign('ebupd', TRUE);
		
		$this->actabrv = array('e_img1' => 'Image 1', 'e_img2' => 'Image 2', 'e_img3' => 'Image 3', 'e_img4' => 'Image 4', 'quantity' => 'Local Quantity', 'e_part' => 'BCN', 'e_qpart' => 'BCN Count', 'buyItNowPrice' => 'Price', 'e_title' => 'Title', 'e_sef' => 'SEF URL', 'e_condition' => 'Condition', 'e_model' => 'Model', 'e_compat' => 'Compatibility', 'ebayquantity' => 'Local eBay Quantity', 'Ebay Quantity' => 'Local eBay Quantity', 'idpath' => 'Image Dir.', 'e_desc' => 'Descripion', 'upc' => 'UPC', 'e_manuf' => 'Brand', 'e_package' => 'Package', 'location' => 'Location', 'pCTitle' => 'Pri.Cat. Title', 'ebay_submitted' => 'Submitted','ebay_id' => 'eBay ID', 'sn' => 'Transaction BCN', 'asc' => 'ActShipCost', 'storeCatTitle' => 'Store Category', 'storeCatID' => 'Store Cat. ID');

	}
function index()
	{	
		
	}
function listingpictures()
	{	

		$this->db->select("e_id, e_img1, e_img2, e_img3, e_img4, idpath, nwm");	
		
		$this->db->where('e_id >', 14000);

		$this->db->order_by("e_id", "DESC");

		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0)
		$pool = $this->query->result_array();
		foreach ($this->query->result_array() as $q)
		{		
			$pool2[$q['e_id']] = $q;
		}
		$this->productimages = array(1,2,3,4);
		
		foreach($pool as $k => $v)
		{
			foreach($this->productimages as $i)
			{		
				if ($v['e_img'.$i] != '')
				{
				$imagebase[$v['e_id']][$i]['_'] = file_exists($this->config->config['paths']['imgebay'].'/'.idpath($v['e_id']).''.$v['e_img'.$i]);
				$imagebase[$v['e_id']][$i]['thumb_main_'] = file_exists($this->config->config['paths']['imgebay'].'/'.idpath($v['e_id']).'thumb_main_'.$v['e_img'.$i]);
				$imagebase[$v['e_id']][$i]['Ebay_'] = file_exists($this->config->config['paths']['imgebay'].'/'.idpath($v['e_id']).'Ebay_'.$v['e_img'.$i]);
				$imagebase[$v['e_id']][$i]['Original_'] = file_exists($this->config->config['paths']['imgebay'].'/'.idpath($v['e_id']).'Original_'.$v['e_img'.$i]);
				$imagebase[$v['e_id']][$i]['thumb_'] = file_exists($this->config->config['paths']['imgebay'].'/'.idpath($v['e_id']).'thumb_'.$v['e_img'.$i]);
				}
			}			
		}
			
		foreach ($imagebase as $k => $v)
		{
		$str[$k] = '';
		$existing[$k] = '';
		foreach ($v as $kk => $vv)
		{
			foreach ($vv as $kkk => $vvv)
			{
			
			if ($vvv != 1) { $str[$k] .= '&nbsp;&nbsp;&nbsp;&nbsp;IMG'.$kk; $str[$k] .= ' - <span style="color:red;">'.$kkk.'</span> '; $keepbase[$k] = $v;}
			else { if ($kkk == '_') $kkk = ""; $existing[$k] .= $kkk.'<img alt="'.$kkk.'" title="'.$kkk.'" src="/'.$this->config->config['wwwpath']['imgebay'].'/'.idpath($k).$kkk.$pool2[$k]['e_img'.$kk].'" />'; }
			}
			
			if ($str[$k] != '') echo '<strong>'.$k.':</strong> '.$str[$k].'<br>'.$existing[$k].'<br><br>';
		}
		
		}
		printcool ($keepbase);
				
}


function RedoImage()
{
	
	foreach($this->productimages as $value)
					{			if ($_FILES['e_img'.$value]['name'] != '') 
								{
									$this->_CheckImageDirExist(idpath((int)$this->id));
										
									$newname[$value] = (int)$this->id.'_'.substr($this->_CleanSef($this->db_data['e_title']), 0, 210).'_'.$value;
									$image[$value] = $this->_UploadImage ('e_img'.$value, $this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id), TRUE, $this->config->config['sizes']['ebayimg']['width'], $this->config->config['sizes']['ebayimg']['height'], FALSE, TRUE, $newname[$value]);				
									if ($image[$value]) {
										$oldimage[$value] = $this->Myebay_model->GetOldEbayImage($this->id, $value);
										if ($oldimage[$value] != '' && $image[$value] != $oldimage[$value]) {
											
											if ($value == 1 && file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'Ebay_'.$oldimage[$value])) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'Ebay_'.$oldimage[$value]);
											if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).$oldimage[$value])) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).$oldimage[$value]);
											if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_'.$oldimage[$value])) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_'.$oldimage[$value]);
											if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_main_'.$oldimage[$value])) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_main_'.$oldimage[$value]);
											}									
										
										$this->db_data['e_img'.$value] = $image[$value];
										$this->db_data['idpath'] = str_replace('/', '', idpath((int)$this->id));
										$watermark = TRUE;
									}	
								}
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
		$msg_data = array ('msg_title' => 'LATRONICS: GENERATED INDEX for Path: '.$path,'msg_body' => '@ '.CurrentTimeR(),'msg_date' => CurrentTime());							
		GoMail($msg_data);
		return '<html><head><title>403 Forbidden</title></head><body>403 forbidden.</body></html>	';	
}
	
function _htaccess($path = '')
{ 		
		$msg_data = array ('msg_title' => 'LATRONICS: GENERATED .htaccess for Path: '.$path,'msg_body' => '@ '.CurrentTimeR(),'msg_date' => CurrentTime());							
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
				
				$this->load->helper('directory');
				$this->load->helper('file');
		
		
					if (read_file($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'Ebay_'.$this->img)) unlink($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'Ebay_'.$this->img);
					if (read_file($this->config->config['paths']['imgebay'].'/'.idpath($this->id).$this->img)) unlink($this->config->config['paths']['imgebay'].'/'.idpath($this->id).$this->img);
					if (read_file($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'Original_'.$this->img)) unlink($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'Original_'.$this->img);
					if (read_file($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'thumb_'.$this->img)) unlink($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'thumb_'.$this->img);
					if (read_file($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'thumb_main_'.$this->img)) unlink($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'thumb_main_'.$this->img);
					
					}
				}
		if (!$nogo) {
		Redirect("Myebay/Edit/".$this->id);
		}
	}
	
	///////////////////////////
	

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

function ReWaterMark($id = '')
{
//////////////////////////////////////////////	

		$this->db->select("e_img1, e_img2, e_img3, e_img4, nwm");
		$this->db->where('e_id', (int)$id);		
		$r = $this->db->get('ebay');
		if ($r->num_rows() > 0) 
		{ 
			$r = $r->row_array();
			if ($r['nwm'] == 0)
			{
				if ($r['e_img1'] != '') $imgs[] = $r['e_img1'];
				if ($r['e_img2'] != '') $imgs[] = $r['e_img2'];
				if ($r['e_img3'] != '') $imgs[] = $r['e_img3'];
				if ($r['e_img4'] != '') $imgs[] = $r['e_img4'];
				
				$change = 0;
				foreach ($imgs as $i)
				{
					if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).'Original_'.$i)) 
					{
						//echo 'File Exists '.$i;
						if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).'thumb_main_'.$i)) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).'thumb_main_'.$i);	
						if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).'thumb_'.$i)) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).'thumb_'.$i);	
						if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).'Ebay_'.$i)) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).'Ebay_'.$i);	
						if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).$i)) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).$i);		 	
					}
					else echo 'File not found '.$i;
					$this->_ReApplyWaterMark((int)$id, $i);
					$change++;
				}
				
				if ($change > 0) $this->db->update('ebay', array('nwm' => 1), array('e_id' => (int)$id));
			}
			
		}		
		else exit('ERROR WARKING. ACTION IS CANCELLED. CONTACT ADMINISTRATOR');
		
		
		
//echo 'go';
//$this->db->update('ebay', array('nwm' => 0));
//SubmitEbay
//UpdateFromEbay
//ReSubmitEbay
	
//////////////////////////////////////////	
}


function _ReApplyWaterMark($id, $filename)
{					
			$sourcefilename = $this->config->config['paths']['imgebay'].'/'.idpath($id).'Original_'.$filename;
			
			if (!copy($sourcefilename, $this->config->config['paths']['imgebay'].'/'.idpath((int)$id).$filename)) {
				//$filename = str_replace('.jpg', '.JPG', $filename);
				$sourcefilename = str_replace('.jpg', '.JPG', $sourcefilename);
				if (!copy($sourcefilename, $this->config->config['paths']['imgebay'].'/'.idpath((int)$id).$filename)) {				
				
				echo "failed to copy Ebay_file...\n";
				break;
				}
			}
			
			$this->load->library('image_lib');
			
			$econfig['image_library'] = 'gd2';
			$econfig['source_image'] = $sourcefilename;
			$econfig['create_thumb'] = FALSE;
			$econfig['maintain_ratio'] = TRUE;
			$econfig['width'] = '600';
			$econfig['new_image'] = 'Ebay_'.$filename;	
			$this->image_lib->initialize($econfig);
			$this->image_lib->resize();			
			$this->image_lib->clear();			
			//printcool ($econfig);
			
			$iconfig['image_library'] = 'gd2';			
			$iconfig['source_image'] = $sourcefilename;
			$iconfig['create_thumb'] = TRUE;
			$iconfig['maintain_ratio'] = TRUE;								
			$iconfig['width'] = $this->config->config['sizes']['ebayimg']['width'];
			$iconfig['height'] = $this->config->config['sizes']['ebayimg']['height'];			 
			$iconfig['new_image'] = 'thumb_'.$filename;
			$this->image_lib->initialize($iconfig);
			$this->image_lib->resize();			
			$this->image_lib->clear();							
			//printcool ($iconfig);
						
			$nconfig['image_library'] = 'gd2';
			$nconfig['source_image'] = $sourcefilename;
			$nconfig['create_thumb'] = TRUE;
			$nconfig['maintain_ratio'] = TRUE;
			$nconfig['new_image'] = 'thumb_main_'.$filename;
			$nconfig['width'] = '200';
			$nconfig['height'] = '200';			
			$this->image_lib->initialize($nconfig);
			$this->image_lib->resize();			
			$this->image_lib->clear();							
			//printcool ($nconfig);	
			
			
			$this->_WaterMark('bottom', 'right', 'wm_original_ebay.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), 'Ebay_'.$filename);
				
			$this->_WaterMark('bottom', 'right', 'wm_original_bottom.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), $filename);
			$this->_WaterMark('middle', 'center', 'wm_original_center.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), $filename);
			$this->_WaterMark('bottom', 'right', 'wm_bigtn_bottom.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), 'thumb_main_'.$filename);
			$this->_WaterMark('middle', 'center', 'wm_bigtn_center.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), 'thumb_main_'.$filename);
			$this->_WaterMark('bottom', 'right', 'wm_smalltn_bottom.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), 'thumb_'.$filename);	
				
}


}