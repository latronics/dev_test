<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mywarehouseauctions extends Controller {

function Mywarehouseauctions()
	{
		

		//exit('Commiting update, please wait  1-2 mins...');
		parent::Controller();
		$this->load->model('Mywarehouse_model');
		$this->load->model('Auth_model');
		$this->adms = $this->Mywarehouse_model->GetAdminList();
		if ($this->router->method != 'Comm') 
		{
		$this->Auth_model->VerifyAdmin();
		$this->Auth_model->CheckWarehouse();	
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('action',$this->session->flashdata('action'));
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Warehouse');
		$this->mysmarty->assign('hot', TRUE);
		$this->mysmarty->assign('newlayout', TRUE);
		}
		
	}
	
function EditAuction($id ='')
{
	if(isset($_POST) && $_POST)
		{	
			$wdata = array();
			if (isset($_POST['wtitle'])) $wdata['wtitle'] = $this->input->post('wtitle', true);
			//if (isset($_POST['wcost'])) $wdata['wcost'] = $this->input->post('wcost', true);
			if (isset($_POST['wnotes'])) $wdata['wnotes'] = $this->input->post('wnotes', true);
			if (isset($_POST['wdate'])) $wdata['wdate'] = $this->input->post('wdate', true);
			if (isset($_POST['wvendor'])) $wdata['wvendor'] = $this->input->post('wvendor', true);
			$wdata['wacat'] = (int)$this->input->post('wacat', true);
			//if (isset($_POST['shipping'])) $wdata['shipping'] = (float)$this->input->post('shipping', true);
			//if (isset($_POST['expenses'])) $wdata['expenses'] = (float)$this->input->post('expenses', true);
			
			if (count($wdata) > 0)
			{
				$this->db->update('warehouse_auctions', $wdata, array('waid' => (int)$id));
				$this->session->set_flashdata('success_msg', 'Auction updated');						
			}
			unset($_POST);
		}
	$this->mysmarty->assign('acat', $this->Mywarehouse_model->GetAuctionCategories());	
	
	$aucdata = $this->Mywarehouse_model->GetAuction((int)$id);
	require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
	$this->editor = new FCKeditor('wnotes');				
	$this->editor->Width = "620";
	$this->editor->Height = "250";				
	$this->editor->Value = $aucdata['wnotes'];
	$aucdata['wnotes'] = $this->editor->CreateHtml();	
	$this->mysmarty->assign('auction', $aucdata);
	$this->Process((int)$id);
	$this->mysmarty->view('mywarehouse/auction_expenses.html');
}
function Process($aid = '', $return = false,$new = false)
{ //if ($this->session->userdata['admin_id'] != 1) exit('Commiting update, back in 5 mins');
 
	if(isset($_POST) && $_POST)
		{	
				$colMap = array(
							0 => 'wae_id',
							1 => 'exp_type',
							2 => 'exp_title',
							3 => 'exp_value',
							4 => 'exp_admin',
							5 => 'exp_time',
							6 => 'exp_notes'
						  );
				
				
				$bcolMap = array(
							0 => 'ID',
							1 => 'Type',
							2 => 'Title',
							3 => 'Value',
							4 => 'Admin',
							5 => 'Time',
							6 => 'Notes'
						  );
				
				 
				
				$out = '';
				$sout = '';
					
				foreach($_POST as $d)
				{
					foreach($d as $dd)
					{
					$dd[3] = floatercheck($colMap[(int)$dd[1]], $dd[3]);
					if ($dd[2] != $dd[3])
					{
						
					$this->_logit((int)$dd[4],(int)$aid,$dd[2],$dd[3],$colMap[(int)$dd[1]],(int)$this->session->userdata['admin_id']);
					$out .= ' "'.$bcolMap[(int)$dd[1]].'" Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
					$sout .= 'Expense '.(int)$dd[4].' / "'.$bcolMap[(int)$dd[1]].'" Changed ';
										
					$updt = array($colMap[(int)$dd[1]] => $dd[3]);
					
					$this->db->update('warehouse_auction_expenses', $updt, array('wae_id' => (int)$dd[4]));										
					unset($updt);
					$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd[1]);
					}
					}
				}				
				echo json_encode($out);	
				}
	
		else 
		{
			if ($new) $this->db->insert('warehouse_auction_expenses', array('wa_id' => (int)$aid,'exp_title' => 'New','exp_time' => CurrentTime(), 'exp_admin' => (int)$this->session->userdata['admin_id']));
			
			$this->db->where('wa_id', (int)$aid);
			$eo = $this->db->get('warehouse_auction_expenses');
			if ($eo->num_rows() >0) $exp = $eo->result_array();
			else $exp = false;
			//$adms = $this->Mywarehouse_model->GetAdminList();
			if ($exp) foreach ($exp as $k => $l)
			{
				$returndata[] = array(cstr($l['wae_id']),cstr($l['exp_type']),cstr($l['exp_title']),cstr($l['exp_value']),cstr($this->adms[$l['exp_admin']]),cstr($l['exp_time']),cstr($l['exp_notes']));									
			}		
		}
		$loaddata = '';
		if ($exp)
			{
			  $loaddata = json_encode($returndata);
			}	
			if ($return)
			{
				echo rtrim(json_encode($returndata), ',');
				exit();
			}
			
		$fieldset = array(
		'headers' => "'ID', 'Type', 'Title', 'Value', 'Admin', 'Time', 'Notes'",
		/*'rowheaders' => $list['headers'], */
		'width' => "30, 80, 100, 100, 80, 150,350", 
		'startcols' => 7, 
		'startrows' => 0, 
		'autosaveurl' => "/Mywarehouseauctions/Process/".(int)$aid,	
		'reloadurl' => "/Mywarehouseauctions/Process/".(int)$aid.'/TRUE',
		'newurl' => "/Mywarehouseauctions/Process/".(int)$aid.'/TRUE/TRUE',		
		'colmap' => '{readOnly: true},{type: "dropdown", source: ["Cost","Shipping","Expense"]},{},{},{readOnly: true},{readOnly: true},{}');		
		
		$this->mysmarty->assign('headers', $fieldset['headers']);
		$this->mysmarty->assign('rowheaders', $fieldset['rowheaders']);
		$this->mysmarty->assign('width', $fieldset['width']);
		$this->mysmarty->assign('startcols', $fieldset['startcols']);
		$this->mysmarty->assign('startrows', $fieldset['startrows']);
		$this->mysmarty->assign('autosaveurl', $fieldset['autosaveurl']);
		$this->mysmarty->assign('reloadurl', $fieldset['reloadurl']);
		$this->mysmarty->assign('newurl', $fieldset['newurl']);
		$this->mysmarty->assign('colmap', $fieldset['colmap']);
		if ($exp)
		{
			$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
			$this->mysmarty->assign('copyrows', count($exp));	
		}
		else
		{
			$this->mysmarty->assign('loaddata', '[]');
			$this->mysmarty->assign('copyrows', 0);	
		}
		
		$fieldset = array(
		'headers' => "'ExpID', 'Field', 'From', 'To', 'Admin', 'Time'",
		/*'rowheaders' => $list['headers'], */
		'width' => "50, 100, 100, 100, 80, 150", 
		'startcols' => 6, 
		'startrows' => 0, 
		'reloadurl' => "/Mywarehouseauctions/GetLog/".(int)$aid,
		'colmap' => '{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}');		
		
		$this->mysmarty->assign('lheaders', $fieldset['headers']);
		$this->mysmarty->assign('lrowheaders', $fieldset['rowheaders']);
		$this->mysmarty->assign('lwidth', $fieldset['width']);
		$this->mysmarty->assign('lstartcols', $fieldset['startcols']);
		$this->mysmarty->assign('lstartrows', $fieldset['startrows']);
		$this->mysmarty->assign('lreloadurl', $fieldset['reloadurl']);
		$this->mysmarty->assign('lnewurl', $fieldset['newurl']);
		$this->mysmarty->assign('lcolmap', $fieldset['colmap']);
}
function GetLog ($waid = 0)
{
			$this->db->where('waid', (int)$waid);
			$eao = $this->db->get('warehouse_auction_expenses_log');
			if ($eao->num_rows() >0) $eaxp = $eao->result_array();
			else $eaxp = false;
			//$adms = $this->Mywarehouse_model->GetAdminList();
			if ($eaxp)
			{
 
				
				
				$colMap = array(
							'wae_id' => 'ID',
							'exp_type' => 'Type',
							'exp_title' => 'Title',
							'exp_value' => 'Value',
							'exp_admin' => 'Admin',
							'exp_time' => 'Time',
							'exp_notes' => 'Notes'
						  );
				 foreach ($eaxp as $k => $l)
				{
				$returndata[] = array(cstr($l['wae_id']),$colMap[cstr($l['field'])],cstr($l['datafrom']),cstr($l['datato']),cstr($this->adms[$l['admin']]),cstr($l['time']));									
				}		
			echo rtrim(json_encode($returndata), ',');
			}
}
function _logit($waeid,$waid,$from,$to,$field,$admin)
{
	$this->db->insert('warehouse_auction_expenses_log', array('wae_id' => $waeid, 'waid'=> $waid, 'time' => CurrentTime(), 'ts'=> mktime(), 'datafrom' => $from, 'datato' => $to, 'field' => $field, 'admin' => $admin));
}
function PopulateExisting()
{
	
}
}