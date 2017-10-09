<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mygoogledrive extends Controller {

	function Mygoogledrive()
	{
		parent::Controller();		
		$this->load->model('Myebay_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('action',$this->session->flashdata('action'));
		$this->mysmarty->assign('gotoebay',$this->session->flashdata('gotoebay'));
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		
		
		//$this->session->set_userdata('access_token', $this->GetDBRefreshToken());
		//printcool ($_SERVER['QUERY_STRING']);
		
		
		/*
		
		
		Array
(
    [AC14-5] => Array
        (
            [0] => Array
                (
                    [title] => New Parts Inventory
                    [link] => https://docs.google.com/spreadsheets/d/1_LGI40UuKj4sEDgLJzPBKaAVQ6mORERpY4aeyeNLvWU/edit?usp=drivesdk
                    [icon] => https://ssl.gstatic.com/docs/doclist/images/icon_11_spreadsheet_list.png
                )

        )

    [AC14-6] => Array
        (
            [0] => Array
                (
                    [title] => New Parts Inventory
                    [link] => https://docs.google.com/spreadsheets/d/1_LGI40UuKj4sEDgLJzPBKaAVQ6mORERpY4aeyeNLvWU/edit?usp=drivesdk
                    [icon] => https://ssl.gstatic.com/docs/doclist/images/icon_11_spreadsheet_list.png
                )

        )

)

)
		
		
		*/
	}

	function index()
	{	
		$this->Inventory();
		exit();		
	}
	
	
	
	function Inventory()
	{	
		if(isset($_POST['gsearch'])) 
				{								
					$search_term = trim($this->input->post('gsearch', TRUE));					
					if($search_term != '') 
					{			
						$find = explode(',', $search_term);											
						$this->load->library('Googledrive');
						$this->load->library('Googlesheets');
						$spreadsheetdata = $this->googlesheets->PopulateWorksheetInfo($this->googledrive->FindInDrive($find),  TRUE);	
						
						if ($spreadsheetdata['newsheets']) $this->_GetSheetsLayout(false, $spreadsheetdata['newsheets']);						
								
						if (isset($this->actionmsg)) $this->mysmarty->assign('actionmsg', $this->actionmsg);
						$this->mysmarty->assign('worksheets', $spreadsheetdata['workspreadsheet_keys']);
						$this->mysmarty->assign('searchres', $spreadsheetdata['wsresults']);
						$this->mysmarty->assign('search_term', $search_term);
					}
				}
		$this->mysmarty->view('mygoogledrive/mygoogledrive_search.html');
	}
	function ModifyInventory()
	{
		if (!isset($_POST['row']) && !isset($_POST['spreadsheet']) && !isset($_POST['worksheet'])) exit('No Data');
		
	//printcool ($_POST); 
	
				try {
					$gdrval = $this->Auth_model->gDrv();
					$this->load->library('Googlesheets');					
					$access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLESS_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);
					
					$spreadsheet_info = $this->googlesheets->GetWorksheetsInformation(trim($_POST['spreadsheet']), $access_token);
					//printcool($spreadsheet_info);
					foreach ($spreadsheet_info as $k => $s)
						{
							$worksheetkey = GetWorksheetSheetKey($s['worksheet_id']);
							if ($worksheetkey == trim($_POST['worksheet']))
							{
								$this->mysmarty->assign('wstitle', $s['title']);
								$this->mysmarty->assign('matchrow', (int)$_POST['row']);
								$this->mysmarty->assign('spreadsheet', trim($_POST['spreadsheet']));
								$this->mysmarty->assign('worksheet', trim($_POST['worksheet']));
								
								$header = $this->googlesheets->GetCells($s['worksheet_cells_feed_url'], 1, 1, 1, $s['col_count'], $access_token);
								$row = $this->googlesheets->GetCells($s['worksheet_cells_feed_url'],(int)$_POST['row'], (int)$_POST['row'], 1, $s['col_count'], $access_token);
								$this->mysmarty->assign('header', $header);
								$this->mysmarty->assign('row', $row);
								
								if (isset($_POST['newrow']))
								{
									
									 //printcool ($_POST['newrow']);
									 	$colmap = $this->googlesheets->Colmap();
										foreach($_POST['newrow'] as $nvk => $nvv)
										{
											$cols_new[$nvk] = $nvv;					
											$cols_new[$nvk]['name'] = $colmap[($nvk+1)];										
										}
										foreach($row as $nvk => $nvv)
										{
											if (isset($cols_new[$nvk]))
											{
											$celldata[$nvk] = $nvv;					
											$celldata[$nvk]['name'] = $colmap[($nvk+1)];
											$row[$nvk]['value'] = $cols_new[$nvk]['value'];										
											}
										}
										$this->mysmarty->assign('row', $row);
									//printcool ($cols_new);
									//printcool ($celldata);
									$cols_old = array();
									$to_be_updated = array();									
									if (!isset($cols_new)) return false;
									
									for($i=0;$i<sizeof($cols_new);$i++) {
										
										//printcool(ord(strtolower($cols_new[$i]['name']))-97);
										//printcool($cols_new[$i]['name']);
											$cols_old[] = $celldata[ord(strtolower($cols_new[$i]['name']))-97];
											$celldata[ord(strtolower($cols_new[$i]['name']))-97]['value'] = $cols_new[$i]['value'];
											$to_be_updated[] = $celldata[ord(strtolower($cols_new[$i]['name']))-97];
										}
									
										//printcool($cols_old);
										//printcool($vvvv['celldata']);
										//printcool($to_be_updated);	
										
										$bcn = $to_be_updated[0]['value'];
										
										foreach($cols_old as $ck => $cv)
										{						
											if (trim($cv['value']) != trim($to_be_updated[$ck]['value'])) 
											{												
												if ($gdrval > 0)
												{
													$this->db->insert('google_sheets_logs', array('log_value' => NULL,'log_date' => CurrentTime(),'log_type' => 0,'origin' => '', 'origin_type' => 'ModifyInventory','sskey' => trim($_POST['worksheet']),'wskey' => trim($_POST['spreadsheet']),'row' => (int)$_POST['row'],'col' => $cv['col'],'admin' => $this->session->userdata['ownnames'],'bcn' => $bcn,'old' => IfEmptyReturn($cv['value']),'new' => IfEmptyReturn($to_be_updated[$ck]['value']), 'gdrv' => $gdrval));							
												}
											$success_string .= '<strong>'.$bcn.'</strong> - Row <strong>'.$cv['row'].'</strong> / Col <strong>'.$cv['col'].'</strong> - <strong>'.IfEmptyReturn($cv['value']).'</strong> = <strong>'.IfEmptyReturn($to_be_updated[$ck]['value']).'</strong><br>';
											unset($to_be_updated[$ck]['name']);
											}
											else unset($to_be_updated[$ck]);
										}
										//printcool($to_be_updated);	
										if (count($to_be_updated) > 0)
										{	
										
											if ($gdrval == 1)
											{
											try 
											{
												sort($to_be_updated);
												$this->googlesheets->UpdateCells($s['worksheet_cells_feed_url'], $to_be_updated, $access_token);
											}
											catch(Exception $e) {
												echo $e->getMessage();
											}
											}						
											
										}
										
										if ($gdrval > 0)
										{			
												if ($gdrval == 2 && $success_string != '') $success_string .= '*** Service mode ***';
												
												$this->db->insert('admin_history', array ('msg_type' => 1, 
																					  'msg_title' => 'Google Speadsheets action', 
																					  'msg_body' => $success_string, 
																					  'msg_date' => CurrentTime(),
																					  'e_id' => 0,
																					  'itemid' => 0,
																					  'trec' => 0,
																					  'opr' => 0,
																					  'admin' => $this->session->userdata['ownnames'],
																					  'sev' => 0												  
																					  ));
				

											if ($success_string != '') $this->session->set_flashdata('success_msg', '<span style="font-size:10px;">'.$success_string.'</span>'); 
											else $this->session->set_flashdata('error_msg', '<span style="font-size:10px;">No new values to be updated</span>'); 
						
										}
								}					
							}
						}
					
					}
		catch(Exception $e) {
			$echo ($e->getMessage());
			exit();
		}	
		$this->mysmarty->view('mygoogledrive/mygoogledrive_invmod.html');
	}	
	function GetAllSheets()
	{	
		try {
					$this->load->library('Googledrive');
					$access_token = $this->googledrive->GetRefreshedAccessToken(CLIENT_ID, GOOGLE_REFRESH_TOKEN, CLIENT_SECRET);
					$results = $this->googledrive->FulltextSearch('*', $access_token);
					
					if (count($results) > 0) foreach ($results as $k => $v)
						{				
							//https://docs.google.com/spreadsheet/ccc?key=0ArLUatrLqnJ0dGxsdUJrQ3BteWJaOWpLVjYwQWpHRGc&usp=drivesdk			
 
							$type = parse_url($v['link'], PHP_URL_PATH);
							$type = explode ('/', ltrim($type, '/'));
							if ($type[0] == 'spreadsheet' || $type[0] == 'spreadsheets')
							{
								$results[$k]['key'] = GetSheetKey($v['link']);
								if (!isset($this->spreadsheet_keys[$results[$k]['key']])) $this->spreadsheet_keys[$results[$k]['key']] = $v['title'];	
							}
						}
						else exit('No Matches');
						
					//printcool ($this->spreadsheet_keys);	
					
					
					$this->_GetSheetsLayout();
			}
		catch(Exception $e) {
			$echo ($e->getMessage());
			exit();
		}
			
	}
	
	function _GetSheetsLayout($fix = false, $newsheet = false)
	{
				set_time_limit(600);
				ini_set('mysql.connect_timeout', 600);
				ini_set('max_execution_time', 600);  
				ini_set('default_socket_timeout', 600); 
				
				$avoidsheets = array();
					$this->db->select('no_use, parent_key, sheet_key, parent_name, sheet_name');
					$this->query = $this->db->get('google_sheets');
					if ($this->query->num_rows() > 0) 
					{
							foreach ($this->query->result_array() as $r)
							{	
								if ($r['no_use'] == 1) $avoidsheets[$r['parent_key']][$r['sheet_key']] = $r['parent_name'].' / '.$r['sheet_name'];
								else $dbsheets[$r['parent_key']][$r['sheet_key']] = $r['parent_name'].' / '.$r['sheet_name'];
							}
					}
				
								
				//require_once($this->config->config['pathtopublic'].'/gsssettings.php');
				//printcool ($spreadsheet_keys);
				try {
					// Create an object
					$this->load->library('Googlesheets');
			
					//$this->googlesheets->testme();
					$access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLESS_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);
					
					//printcool ($access_token);		
					if (count($this->spreadsheet_keys) == 0 && $newsheet == false) exit('No Spreadsheets');
					
					//printcool ($avoidsheets);
					//printcool ($dbsheets);
					//printcool ($this->spreadsheet_keys);
					
					//exit();
					//printcool ($this->spreadsheet_keys);
					if ($newsheet) $this->spreadsheet_keys = $newsheet;
					foreach ($this->spreadsheet_keys as $skey => $sname)
					{	
			
						if (!in_array($skey, $avoidsheets))
						{
						//$this->sheetlayout = false;
						// Get Worksheet Information
						//GetWorksheetInformation
						$spreadsheet_info = $this->googlesheets->GetWorksheetsInformation($skey, $access_token);
						//printcool ($spreadsheet_info);						
						foreach ($spreadsheet_info as $k => $s)
						{
							$worksheetkey = GetWorksheetSheetKey($s['worksheet_id']);
							if (!isset($avoidsheets[$skey][$worksheetkey]))
							{
							
							// Get the column cells
								
								$cells = $this->googlesheets->GetCells($s['worksheet_cells_feed_url'], 1, 1, 1, $s['col_count'], $access_token);
								//printcool ($cells);
								$cnt = 0;
								$header_err = 0;
								$datacols = array();
								$tomatch = array_flip(array('title', 'ebay title', 'where listed', 'date listed', 'price sold', 'shipping cost', 'where sold', 'date sold'));
								$existing = false;
								//printcool ($cells);
								foreach ($cells as $k => $c)
								{	
									$datacols[(int)$c['col']] = strtolower(trim($c['value']));
									if (trim($c['value']) == '') $header_err++;
									if ((int)$c['col'] == 1) 
									{ 
										$this->sheetlayout[$skey][$worksheetkey]['bcn'] = 1; 
										$cnt++; 
									}
									else
									{
										$c['value'] = strtolower(trim($c['value']));
											
										$existing[$c['value']] = $c['value'];
																		
										switch ($c['value'])
										{
											//case 'bcn':
											case 'title' : 
											case 'ebay title' :
											case 'where listed' :
											case 'date listed' :
											case 'price sold' :
											case 'shipping cost' :
											case 'where sold' :
											case 'date sold' :
											{ 
												$this->sheetlayout[$skey][$worksheetkey][str_replace(" ", "", $c['value'])] = (int)$c['col']; 
												$cnt++; 
												
												unset($tomatch[$c['value']]);
												unset($existing[$c['value']]);
												//printcool ($tomatch);
												//printcool ($c['value']);
												//printcool ('Unset: '.$c['value']);	
												//printcool ($existing, FALSE, 'EXISTING');
												//printcool ('--END--');									
											}
												
										}	
									}
								}			
								
								
								$unmatched = 0;
								if ($cnt != 9)
								{
									if (isset($existing) && is_array($existing)) $existing = implode(', ', $existing);
									$unmatched  = count($tomatch);
									$tomatch = implode(', ', array_flip($tomatch));
									
									$this->db->insert('google_sheets_logs', array('log_value' => 'Cannot match ('.$unmatched.') columns for '.$sname.' / '.$s['title'].' - <strong>Unmatched:</strong> '.$tomatch.'<br><br><strong>In Sheet after match:</strong> '.$existing.'<br><br>Key: '.$skey.' / '.$worksheetkey, 'log_date' => CurrentTime(), 'log_type' => 1));
									 //exit('Cannot match all columns for sheet "'.$sname.' / '.$s['title'].'". Operations cannot continue!');
								}
									
								$this->sheetlayout[$skey][$worksheetkey]['uni_key'] = $skey.'|'.$worksheetkey;
								$this->sheetlayout[$skey][$worksheetkey]['sheet_key'] = $worksheetkey;
								$this->sheetlayout[$skey][$worksheetkey]['sheet_name'] = $s['title'];
								$this->sheetlayout[$skey][$worksheetkey]['parent_key'] = $skey;
								$this->sheetlayout[$skey][$worksheetkey]['parent_name'] = $sname;
								$this->sheetlayout[$skey][$worksheetkey]['sheetMod'] = CurrentTimeR();
								$this->sheetlayout[$skey][$worksheetkey]['worksheet_id'] = $s['worksheet_id'];
								$this->sheetlayout[$skey][$worksheetkey]['worksheet_list_feed_url'] = $s['worksheet_list_feed_url'];
								$this->sheetlayout[$skey][$worksheetkey]['worksheet_cells_feed_url'] = $s['worksheet_cells_feed_url'];
								$this->sheetlayout[$skey][$worksheetkey]['worksheet_edit_url'] = $s['worksheet_edit_url'];
								$this->sheetlayout[$skey][$worksheetkey]['row_count'] = $s['row_count'];
								$this->sheetlayout[$skey][$worksheetkey]['col_count'] = $s['col_count'];
								$this->sheetlayout[$skey][$worksheetkey]['data_cols'] = serialize($datacols);
								$this->sheetlayout[$skey][$worksheetkey]['header_err'] = $header_err;
								$this->sheetlayout[$skey][$worksheetkey]['unmatched_cols'] = $unmatched;
							
								//printcool ($this->sheetlayout);
								
								if (isset($dbsheets[$skey][$worksheetkey]) || isset($avoidsheets[$skey][$worksheetkey]))
								{
									$nouse = '';
									if ($this->sheetlayout[$skey][$worksheetkey]['unmatched_cols'] > 4) { $nouse = ' **NO USE**'; $this->sheetlayout[$skey][$worksheetkey]['no_use'] = 1;}
									$this->db->update('google_sheets', $this->sheetlayout[$skey][$worksheetkey], array('sheet_key' => $worksheetkey, 'parent_key' => $skey));
									 $this->actionmsg[] = '<span style="color:#0080E9;">Updated'.$nouse.' Worksheet: <u><em>'.$sname.' / '.$s['title'].'</em></u></span>';
								}
								else
								{
									$nouse = '';
									if ($this->sheetlayout[$skey][$worksheetkey]['unmatched_cols'] > 4) { $nouse = ' **NO USE**'; $this->sheetlayout[$skey][$worksheetkey]['no_use'] = 1;}
									
									$this->db->insert('google_sheets', $this->sheetlayout[$skey][$worksheetkey]);
									$this->actionmsg[] = '<span style="color:#00C90A;">Inserted'.$nouse.' Sheet: <u><em>'.$sname.' / '.$s['title'].'</em></u></span>';
								}
							
							}
						}
					}
				}
				
				if (!$newsheet) $this->ShowDBSheets();
			}
		catch(Exception $e) {
			echo $e->getMessage();
			//$msg .= $e->getMessage();
		}	
	}
	
	function ShowDBSheets()
	{
		//$this->db->order_by('unmatched_cols','DESC');
		$this->db->order_by('parent_name','ASC');
		$this->db->order_by('sheet_name','ASC');
		$this->db->where('no_use', 0);
		
		$this->query = $this->db->get('google_sheets');
		if ($this->query->num_rows() > 0) 
		{
			foreach ($this->query->result_array() as $k => $v)
			{
				$sh[$k] = $v;
				$sh[$k]['data_cols'] = unserialize($v['data_cols']);	
			}
			$this->mysmarty->assign('sheets', $sh);
			if (isset($this->actionmsg)) $this->mysmarty->assign('actionmsg', $this->actionmsg);
			$this->mysmarty->view('mygoogledrive/mygoogledrive_sheets.html');
		}
		else exit('Error: No sheets in DB');
	}
	function ShowNoDBSheets()
	{
		//$this->db->order_by('unmatched_cols','DESC');
		$this->db->order_by('parent_name','ASC');
		$this->db->order_by('sheet_name','ASC');
		$this->db->where('no_use', 1);
		
		$this->query = $this->db->get('google_sheets');
		if ($this->query->num_rows() > 0) 
		{
			foreach ($this->query->result_array() as $k => $v)
			{
				$sh[$k] = $v;
				$sh[$k]['data_cols'] = unserialize($v['data_cols']);	
			}
			$this->mysmarty->assign('sheets', $sh);
			$this->mysmarty->assign('nouse', TRUE);
			$this->mysmarty->view('mygoogledrive/mygoogledrive_sheets.html');
		}
		else exit('Error: No sheets in DB');
	}
	/*function DeleteDBSheet($id)
	{	
		$this->db->where('s_id', (int)$id);
		$this->query = $this->db->get('google_sheets');
		if ($this->query->num_rows() > 0) 
		{
			$res = $this->query->row_array();
			$this->db->insert('google_no_sheets', array('parent_key' => $res['parent_key'], 'parent_name' => $res['parent_name'], 'sheet_key' => $res['sheet_key'], 'sheet_name' => $res['sheet_name']));
		}
		$this->db->where('s_id', (int)$id);
		$this->db->delete('google_sheets');
		Redirect('Mygoogledrive/ShowDBSheets');
	}*/
	/*function ShowNoDBSheets()
	{
		$this->query = $this->db->get('google_no_sheets');
		if ($this->query->num_rows() > 0) 
		{
			$this->mysmarty->assign('sheets', $this->query->result_array());
			$this->mysmarty->view('mygoogledrive/mygoogledrive_no_sheets.html');
		}
		else exit('Error: No sheets in DB');
	}*/
	function DeleteDBSheet($id)
	{			
		$this->db->where('s_id', (int)$id);
		$this->db->delete('google_sheets');
		Redirect('Mygoogledrive/ShowNoDBSheets');
	}
	
	function MoveNoDBSheets($id)
	{
		$this->db->update('google_sheets', array('no_use' => 1), array('s_id' => (int)$id));
		/*
		if (isset($_POST['parent_key']) && isset($_POST['parent_name']) && isset($_POST['sheet_key']) && isset($_POST['sheet_name']))
		{
			$this->db->insert('google_no_sheets', array('parent_key' => $this->input->post('parent_key', TRUE), 'parent_name' => $this->input->post('parent_name', TRUE), 'sheet_key' => $this->input->post('sheet_key', TRUE), 'sheet_name' => $this->input->post('sheet_name', TRUE)));
		}*/
		Redirect('Mygoogledrive/ShowDBSheets');
	}
	
	function Logs($page = '', $system = 0)
	{
		
		if ((int)$page > 0) $page = $page - 1;
		if ((int)$system == 1) $this->db->where('log_type', 1);
		else $this->db->where('log_type', 0);
		$this->db->limit(500, (int)$page*500);
		//if (!$all) { $this->db->where('sev', 1);  $this->db->where('code !=', 2); }
		$this->db->order_by("gsl_id", "DESC");
		$query = $this->db->get('google_sheets_logs');		
			
			//if (!$all) $this->db->where('sev', 1);
			if ((int)$system == 1) $this->db->where('log_type', 1);
			else $this->db->where('log_type', 0);
			$countall = $this->db->count_all_results('google_sheets_logs');
			$pages = ceil($countall/500);
			for ( $counter = 1; $counter <= $pages ; $counter++) 
			{
				$pagearray[] = $counter;
			}
			
		if ($query->num_rows() > 0) 
			{
				$this->mysmarty->assign('pages', $pagearray);
				$this->mysmarty->assign('list', $query->result_array());
			}	
		$this->mysmarty->assign('nouse', (int)$page);
		$this->mysmarty->assign('system', $system);
		$this->mysmarty->view('mygoogledrive/mygoogledrive_logs.html');	
	}
	function LogPurgeHistory($system = 0) 
	{
		$this->db->truncate('google_sheets_logs');	
		Redirect('Mygoogledrive/Logs/1/'.$system);
	}
	function LogSystemPurgeHistory()
	{
	
		$this->db->query("DELETE FROM google_sheets_logs WHERE log_type = 1");
		Redirect('Mygoogledrive/Logs/1/1');
	}
	function LogDeleteOlderHistory($system = 0)
	{
		$days = 0;
		if (isset($_POST['days'])) $days = (int)$_POST['days'];		
		if ((int)$days > 0) $this->db->query("DELETE FROM google_sheets_logs WHERE log_date < DATE_SUB( NOW( ),INTERVAL ".(int)$days." DAY ) ");
		Redirect('Mygoogledrive/Logs/1/'.$system);
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function PerformFindAndUpdate()
	{		
		
		$search_term = 'B1484';		
		$workdata = array('newvals' => array(
											 array('name' => 'ebaytitle',
												   'value' => 'NEW EBAY TILE'
												   )
											 ), 
						  'origin' => 00000, 
						  'origin_type' => 'TEST', 
						  'admin' => $this->session->userdata['ownnames'],
						  'gdrv' => $this->Auth_model->gDrv()
						  );
	
		if (trim($search_term) != '')
							{
								$this->load->library('Googledrive');
								$this->load->library('Googlesheets');
								$res = $this->googlesheets->ProcessUpdate($this->googlesheets->PopulateWorksheetInfo($this->googledrive->FindInDrive(explode(',', $search_term))), $workdata);								
								if ($res) $this->session->set_flashdata('success_msg', $res); 
							}
		exit();
	
		
		//////// TO COPY
		
			$search_term = commasep(commadesep($submitbcns));		
		
		
							///VAR 1
							$workdata = array('newvals' => array(
											 array('name' => 'shippingcost',
												   'value' =>  $tr['asc']
												   ), 
											 array('name' => 'pricesold', 
												   'value' => $tr['paid']
												   ),
											 array('name' => 'wheresold', 
												   'value' =>'eBay ('.(int)$rec.')'
												   ),
											 array('name' => 'datesold', 
												   'value' => $tr['paidtime']
												   )
											 ), 
						  'origin' => (int)$rec, 
						  'origin_type' => 'TransactionBCNUpdate', 
						  'admin' => $this->session->userdata['ownnames'],
						  'gdrv' =>$this->Auth_model->gDrv()
						  );
		
						  ///VAR2
						  
						  $workdata = array( 
									  'origin' => (int)$rec, 
									  'origin_type' => 'TransactionBCNUpdate', 
									  'admin' => $this->session->userdata['ownnames'],
									  'gdrv' =>$this->Auth_model->gDrv()
									  );
					
							if (isset($v['paid'])) $workdata['newvals'][] = array('name' => 'pricesold', 'value' =>  $v['paid']);
							if (isset($v['asc'])) $workdata['newvals'][] = array('name' => 'shippingcost', 'value' =>  $v['asc']);
		
		
		$this->load->library('Googledrive');
		$this->load->library('Googlesheets');
		if (trim($search_term) != '') $this->googlesheets->ProcessUpdate($this->googlesheets->PopulateWorksheetInfo($this->googledrive->FindInDrive(explode(',', $search_term))), $workdata);
		
		//////////////////
		
		
		
		
		//$spreadsheetdata['workspreadsheet_keys'];
		//$spreadsheetdata['wsresults'];
			
		foreach ($spreadsheetdata['wsresults'] as $k => $v) foreach ($v as $kk => $vv) foreach ($vv as $kkk => $vvv) foreach ($vvv as $kkkk => $vvvv)
		{
			printcool ('BCN '.$kk.' - SpeadSheet '.$k.', Worksheet '.$kkk.', Row '.$kkkk);
			//printcool ($vvvv);
			//printcool ($vvv);
			//printcool ($vv);
			//printcool ($v);

					
				$cols_new = array(array('name' => $colmap[8],'value' => 'NEW EBAY TILE'), array('name' => $colmap[9],'value' =>'NEW WHERE LISTED'));
				
				$cols_old = array();
				$to_be_updated = array();
				//printcool($vvvv['celldata']);
				for($i=0;$i<sizeof($cols_new);$i++) {
					
					//printcool(ord(strtolower($cols_new[$i]['name']))-97);
					//printcool($cols_new[$i]['name']);
						$cols_old[] = $vvvv['celldata'][ord(strtolower($cols_new[$i]['name']))-97];
						$vvvv['celldata'][ord(strtolower($cols_new[$i]['name']))-97]['value'] = $cols_new[$i]['value'];
						$to_be_updated[] = $vvvv['celldata'][ord(strtolower($cols_new[$i]['name']))-97];
					}
					
					printcool($cols_old);
					//printcool($vvvv['celldata']);
					printcool($to_be_updated);
					
					
					foreach($cols_old as $ck => $cv)
					{
						$this->db->insert('google_sheets_logs', array('log_value' => NULL,'log_date' => CurrentTime(),'log_type' => 0,'sskey' => $k,'wskey' => $kkk,'ssname' => $spreadsheetdata['workspreadsheet_keys'][$k]['title'],'wsname' => $spreadsheetdata['workspreadsheet_keys'][$k]['worksheets'][$kkk]['title'],'row' => $cv['row'],'col' => $cv['col'],'admin' => 0,'bcn' => $kk,'old' => $cv['value'],'new' => $to_be_updated[$ck]['value']));
					}
					
					
					//??? WORKSHEETCELLFEEDURL
					/*try 
					{
						$this->googlesheets->UpdateCells($spreadsheetdata['workspreadsheet_keys'][$k]['worksheets'][$kkk]['worksheet_cells_feed_url'], $to_be_updated, $spreadsheetdata['access_token']);
					}
					catch(Exception $e) {
						echo $e->getMessage();
					}*/		

		//	}
			
			
			
		}					
		
		exit();
		
		
		
		require_once($this->config->config['pathtopublic'].'/gsssettings.php');
		$msg = '';
		$debuglog = '';		
		try {
			// Create an object
			$this->load->library('Googlesheets');
					
			//$this->googlesheets->testme();
			// Create an access token using the Refresh Token saved in settings.php
			$access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLESS_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);
								
			// Get the column cells
			//$searchbcncells = $this->googlesheets->GetCells($spreadsheet_info['worksheet_cells_feed_url'], 1, $spreadsheet_info['row_count'], ord(strtolower('A'))-96, ord(strtolower('A'))-96, $access_token);	
			foreach ($this->results as $skey => $data)
			{
				printcool ($data);
				$debuglog .= 'Searching spreadsheet "'.$skey.'"<br><br>';
				
				foreach ($data['worksheets'] as $k => $v)
				{
					exit();
					$debuglog .= '&nbsp;Searching worksheet "'.$k.'"<br>';
					///if (!isset($this->searchbcncells[$skey][$k])) $this>searchbcncells[$skey][$k] = $this->googlesheets->GetCells($v['worksheet_cells_feed_url'], 1, $v['row_count'], $v['BCN'], $v['BCN'], $access_token);
					$debuglog .= '&nbsp;&nbsp;Loading '.$v['row_count'].' rows ot data for cell '.$v['BCN'].'<br>';
					foreach ($this->searchbcncells[$skey][$k] as $cd)
					{
						foreach ($data['bcns'] as $shbcn)
						{
							if (strtolower(trim($cd['value'])) == strtolower(trim($shbcn)))
							{
								$this->matchedrow[trim($shbcn)][$skey][$k] = array('row' => $cd['row'], 'edit_url' => $cd['edit_url'], 'id' => $cd['id']);
								$debuglog .= '&nbsp;&nbsp;&nbsp;Found BCN "'.$shbcn.'" at row '.$cd['row'].'<br>';
							}
						}
					}
					$debuglog .= '<br>';
				}							
			}
			
			
			
			printcool ($debuglog);
			//printcool ($searchbcncells);
			exit();	
				
			if (!is_array($bcn)) $bcn[0] = $bcn; 
			foreach ($bcn as $b)
			{
				//$this->db->update('gsdata', array ('time' => CurrentTimer(), 'proc' => 1), array('gsid' => $r['gsid']));
				//$col_search = array('name' => 'A', 'value' => $r['bcn']);		
				//???
				//$newvals = unserialize($r['tvalue']);			
				//$cols_new = array(array('name' => 'G','value' => $newvals[7]), array('name' => 'J','value' => $newvals[10]), array('name' => 'K','value' => $newvals[11]), array('name' => 'M','value' => $newvals[13]), array('name' => 'U','value' => $newvals[21]));
				//???
				//$row_no = 0;
				//for($i=0;$i<sizeof($cells);$i++) 
				//	{
			//		if($cells[$i]['value'] == $b) 
			//			{
				//		$row_no = ($i+1);
			//			break;
			//			}
				//	}
				//if($row_no == 0)
				//{
					//TURNED OFF
					//$dt1 = array ('msg_title' => 'Cannot match BCN ('.$col_search['value'].') in Google Spreadsheet @ '.CurrentTime(), 'msg_body' => 'GS Record: '.$r['gsid'], 'msg_date' => CurrentTime());
					
					//GoMail($dt1, '365@1websolutions.net', $this->config->config['no_reply_email']);
						
					//$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => $dt1['msg_title'], 'msg_body' => $dt1['msg_body'], 'msg_date' => CurrentTime(), 'e_id' => $r['eid'], 'itemid' => $r['itemid'], 'trec' => $r['trans'], 'admin' => 'Auto', 'sev' => 1));
				//}
				//else
				//{
				// Get the cells of the row that was found
				$cellsin = $this->googlesheets->GetCells($spreadsheet_info['worksheet_cells_feed_url'], $row_no, $row_no, 1, $spreadsheet_info['col_count'], $access_token);
				$cols_old = array();
				$to_be_updated = array();
				for($i=0;$i<sizeof($cols_new);$i++) {
						$cols_old[] = $cellsin[ord(strtolower($cols_new[$i]['name']))-97];
						$cellsin[ord(strtolower($cols_new[$i]['name']))-97]['value'] = $cols_new[$i]['value'];
						$to_be_updated[] = $cellsin[ord(strtolower($cols_new[$i]['name']))-97];
					}
				// Update Cells
				$this->googlesheets->UpdateCells($spreadsheet_info['worksheet_cells_feed_url'], $to_be_updated, $access_token);
				// 'value' key of each element of $cols_old stores the old values of the cells that were updated
								
				//$colmap = array(1 => 'A',2 => 'B',3 => 'C',4 => 'D',5 => 'E',6 => 'F',7 => 'G',8 => 'H',9 => 'I',10 => 'J',11 => 'K',12 => 'L',13 => 'M',14 => 'N',15 => 'O',16 => 'P',17 => 'Q',18 => 'R',19 => 'S',	20 => 'T',21 => 'U',22 => 'V',23 => 'W',24 => 'X',25 => 'Y',26 => 'Z');		
					//COLMAP TO GO TO 100 CELLS
				//foreach ($cols_old as $c => $cv) { unset($cols_old[$c]['id']); unset($cols_old[$c]['row']); unset($cols_old[$c]['edit_url']); } 
					
				//foreach ($to_be_updated as $c => $cv) { unset($to_be_updated[$c]['id']); unset($to_be_updated[$c]['row']); unset($to_be_updated[$c]['edit_url']); }  
				
				//$this->db->update('gsdata', array('sheet' => $spreadsheet_key, 'row' => $row_no, 'fvalue' => serialize($cols_old)), array('gsid' => $r['gsid']));		
							
				/// CONVERT TO ACTIONLOG
				//$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => 'Google Spreadsheet Update Row '.$row_no, 'msg_body' => '<strong>From:</strong> '.serialize($cols_old).' | <strong>To:</strong>'. serialize($to_be_updated), 'msg_date' => CurrentTime(), 'e_id' => $r['eid'], 'itemid' => $r['itemid'], 'trec' => $r['trans'], 'admin' => 'Auto', 'sev' => 0));
				//
				//GoMail(array ('msg_title' => 'Google Spreadsheet Update Row '.$row_no, 'msg_body' => 'From: '.serialize($cols_old).' | To:'. serialize($to_be_updated), 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
				//}
					
			}
			}
			catch(Exception $e) {
				$msg .= $e->getMessage();
			}	
			echo $msg;			
	}	





///INSERT FUNCTION
function testdemosheet()
{
	$skey = '1jqLNC1fmx9EFgBD97ayQO-fy05_a9k9ujKh47LajN5s';
	
	try {
					// Create an object
					$this->load->library('Googlesheets');			
					//$this->googlesheets->testme();
					$access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLESS_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);					
					$spreadsheet_info = $this->googlesheets->GetWorksheetsInformation($skey, $access_token);
					
					printcool ($spreadsheet_info);
					break;		
					$cols = (int)$spreadsheet_info['col_count'];
					$rowData = range(0, ($cols-1));
					$rowData = array_fill_keys($rowData, '');
					
					
					$itemdata = array((2-1) => 'new col2 data', (4-1) => 'new col4 data');
					$atrow = 5;
					foreach ($rowData as $k => $v)
					{
						if (isset($itemdata[$k])) $rowData[$k] = $itemdata[$k];	
					}				
					
					
					/////////////////////
					
					$cells = $this->googlesheets->GetCells($spreadsheet_info['worksheet_cells_feed_url'], 1, 1, 1, $spreadsheet_info['col_count'], $access_token);
				
					$cnt = 0;
					$err = 0;
					$datacols = array();
					$tomatch = array_flip(array('title', 'ebay title', 'where listed', 'date listed', 'price sold', 'shipping cost', 'where sold', 'date sold'));
					$existing = false;
					//printcool ($cells);
					
					/*
					foreach ($cells as $k => $c)
					{	
						$datacols[(int)$c['col']] = strtolower(trim($c['value']));
						if (trim($c['value']) == '')
						{
							echo '<h1 style="color:red;">CRITICAL ERROR: Empty header at column '.$c['col'].'</h1>';
							$err++;	
						}
							$c['value'] = strtolower(trim($c['value']));					
							$existing[$c['value']] = $c['value'];
							switch ($c['value'])
							{
								
								case 'title' : 
								case 'ebay title' :
								case 'where listed' :
								case 'date listed' :
								case 'price sold' :
								case 'shipping cost' :
								case 'where sold' :
								case 'date sold' :
								{ 
									$cnt++; 
									unset($tomatch[$c['value']]);
									unset($existing[$c['value']]);
								}
			
							}	
					}				
					
					$unmatched = 0;
					if ($cnt != 9)
					{
						if (isset($existing) && is_array($existing)) $existing = implode(', ', $existing);
						$unmatched  = count($tomatch);
						$tomatch = implode(', ', array_flip($tomatch));						
						echo '<strong>Cannot match ('.$unmatched.') columns.</strong><br><strong>Unmatched:</strong> '.$tomatch.'<br><strong>In Sheet after match:</strong> '.$existing.'<br><br>';	
						$err++;			
					}
					*/
					/////////////////////
					
					if ($err == 0) $this->googlesheets->insertNewRow($spreadsheet_info,$rowData, $access_token,$atrow);
					else
					{
						
						echo '<strong style="color:red;">No Database update. Please resolve errors.</strong><br>';
						
					}
			}
			catch(Exception $e) {
				echo $e->getMessage();
			}	
	
	
	
}



///INSERT FUNCTION
function testupdatedemosheet()
{
	$skey = '1jqLNC1fmx9EFgBD97ayQO-fy05_a9k9ujKh47LajN5s';
	
	try {
					// Create an object
					$this->load->library('Googlesheets');			
					//$this->googlesheets->testme();
					$access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLESS_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);					
					$spreadsheet_info = $this->googlesheets->GetWorksheetsInformation($skey, $access_token);
					$spreadsheet_info = $spreadsheet_info[0];
					
					
					$cells = $this->googlesheets->GetCells($spreadsheet_info['worksheet_cells_feed_url'], 4, 4, 1, $spreadsheet_info['col_count'], $access_token);
				
				
				
					printcool ($spreadsheet_info);
					printcool ($cells);
					
					$workdata = array('newvals' => array(
											 array('name' => 'name',
												   'value' =>  'lalala'
												   ))
											 );
					foreach($workdata['newvals'] as $nvk => $nvv)
					{printcool ($nvk);
						printcool ($nvv);
						$cols_new[$nvk] = $nvv;					
						$cols_new[$nvk]['name'] = 'B';						
					}					
								 
					$cols_old = array();
					$to_be_updated = array();
					//printcool($vvvv['celldata']);
					for($i=0;$i<sizeof($cols_new);$i++) {
						
						//printcool(ord(strtolower($cols_new[$i]['name']))-97);
						//printcool($cols_new[$i]['name']);
							$cols_old[] = $cells[ord(strtolower($cols_new[$i]['name']))-97];
							$cells[ord(strtolower($cols_new[$i]['name']))-97]['value'] = $cols_new[$i]['value'];
							$to_be_updated[] = $cells[ord(strtolower($cols_new[$i]['name']))-97];
						}
						
						printcool($cols_old);
					
						printcool($to_be_updated);
					
					try 
						{
							$this->googlesheets->UpdateCells($spreadsheet_info['worksheet_cells_feed_url'], $to_be_updated, $access_token);
						}
						catch(Exception $e) {
							echo $e->getMessage();
						}
	}
			catch(Exception $e) {
							echo $e->getMessage();
						}			
					exit('end');
	
	
	
}
}