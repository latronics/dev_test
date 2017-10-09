<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Commport extends Controller {

function Commport()
	{
		parent::Controller();
		
		$this->credentials = 'bU>4iS/A.43%Iin(q/W6?kRfs]}v$%?iObSV^bw$:-xP';	
			
		$this->load->model('Myebay_model'); 
		

	}
function index()
	{	
		echo 'Error';
		
		/* POST STRUCTURE
		
		
		
		credentials => ''
		control => 'Edit'
		centralized => 1
		run $this->_$_POST['control']
		*/
	}
 
function fcp()
{
	if (isset($_POST) && (isset($_POST['params']) && isset($_POST['action']) && $_POST['credentials'] == $this->credentials))
	{ 		
		printcool (httpPost(Site_url().'Commport/pcp', $_POST));	
	}
	else exit('You\'re lost');
	
}
function pcp()
{
	if (isset($_POST) && (isset($_POST['params']) && isset($_POST['action']) && $_POST['credentials'] == $this->credentials))
	{  
		$params = $_POST['params'];
		$action = (string)$_POST['action'];
		$this->$action($params);
	}
	else exit('You lost ?');
}
function _example($params)
{
	$params = unserialize($params);
	printcool ($params);	
}



function _Readcattxt()
{
	
	$this->load->helper('directory');
	$this->load->helper('file');	
	
	$responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
	$store = simplexml_load_string($responseXml);						
					
	$sc = array();
	if (isset($store->Store->CustomCategories->CustomCategory))
	{
		foreach ($store->Store->CustomCategories->CustomCategory as $s)
		{
			$a = (array)$s;
			$sc[$a['CategoryID']] = $a['Name'];	
		}
	}
	echo (serialize($sc));
}
function _Readshippingtxt()
{		
	$this->load->helper('directory');
	$this->load->helper('file');		
	echo (read_file($this->config->config['ebaypath'].'/shipping.txt'));						
}
function _GeteBaySuggested($params)
{
	$itemid = (int)$params;
	set_time_limit(90);
						ini_set('mysql.connect_timeout', 90);
						ini_set('max_execution_time', 90);  
						ini_set('default_socket_timeout', 90); 
						require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= '<ItemID>'.(int)$itemID.'</ItemID>
						</GetItemRequest>';
						$verb = 'GetItem';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						echo ($responseXml);
	
}

function _AddInventory($params)
{
	$params = unserialize($params['params']);
		
	$this->db_data = array(		 
	'e_title' => $params['e_title'],
	'e_sef' => $this->_CleanSef($params['e_sef']),
	'e_manuf' => $params['e_manuf'],
	'e_model' => $params['e_model'],
	'e_part' => $this->_SerialSave($params['e_part']),
	'e_qpart' => $this->_RealCount($this->_SerialSave($params['e_part'])),
	'e_compat' => $params['e_compat'],
	'e_package' => $params['e_package'],
	'e_condition' => $params['e_condition'],
	'e_shipping' => $params['e_shipping'],
	'e_notice_header' => (int)$params['e_notice_header'],
	'e_notice_shipping' => (int)$params['e_notice_shipping'],
	'e_desc' => $params['e_desc'],
	'admin_id' => (int)$params['admin_id'],
	'created' => CurrentTimeR(),	
	'listingType' => $params['listingType'],
	'primaryCategory' => (int)$params['primaryCategory'],
	'pCTitle' => $params['pCTitle'],
	'listingDuration' => $params['listingDuration'],
	'startPrice' => $params['buyItNowPrice'],
	'buyItNowPrice' => $params['buyItNowPrice'],
	'quantity' => (int)$params['quantity'],
	'PaymentMethod' => serialize($params['PaymentMethod']),
	'Subtitle' => $params['Subtitle'],
	'Condition' => $params['Condition'],
	'upc' => $params['upc'],
	'location' => $params['location'],
	'shipping' => serialize($params['shipping']),
	'storeCatID' => $params['storeCatID'],
	'storeCatTitle' => $params['storeCatTitle'],
	'gtaxonomy' => addslashes($params['gtaxonomy']),
	'weight_lbs' => $params['weight_lbs'],
	'weight_oz' => $params['weight_oz'],
	'weight_kg' => lbsoz2kg($params['weight_lbs'], $params['weight_oz'])
	);
	

if ($this->db_data['PaymentMethod'] == 'b:0;') $this->db_data['PaymentMethod'] ='';

$this->checkexists =  $this->Myebay_model->CheckSefExists($this->db_data['e_sef']);
if ($this->checkexists) $this->pref = rand(1,9).rand(1,9).rand(1,9); else $this->pref = '';
$this->db_data['e_sef'] = $this->db_data['e_sef'].$this->pref;

printcool ($this->db_data);
//$this->newid = $this->Myebay_model->Insert($this->db_data);
//Myebay_model
	
	
	
	
	
}




















function _LikeItem($ebayid = '')
{
		set_time_limit(90);
		ini_set('mysql.connect_timeout', 90);
		ini_set('max_execution_time', 90);  
		ini_set('default_socket_timeout', 90); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
		$requestXmlBody .= '<ItemID>'.(int)$ebayid.'</ItemID>
		</GetItemRequest>';
		$verb = 'GetItem';
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
		die('<P>Error sending request');
						
		$xml = simplexml_load_string($responseXml);
		
		//printcool ((string)$xml->Item->Title);

		$string = str_replace("'", "",(string)$xml->Item->Title);
		$string = str_replace('"', '', $string);
			$this->db->like('e_title', $string);	
			$this->db->where('ebay_id !=', (int)$ebayid);							
			$qs = $this->db->get('ebay');
			if ($qs->num_rows() > 0)
			{
				$this->mysmarty->assign('list', $qs->result_array());
			}
			else
			{
				$this->mysmarty->assign('list', false);
			}
		$this->mysmarty->assign('parent', (int)$ebayid);
		$this->mysmarty->view('myebay/myebay_likeitem.html');
}
function _ReplaceEbayId($eid = '', $parent = '')
{
		$this->ReWaterMark((int)$id);
		
		$item = $this->Myebay_model->GetItem((int)$eid);	
		if (!$item) $item['ebay_id'] = 0;
		
			if ((int)$eid != 0 && (int)$parent != 0) $this->db->update('ebay', array('ebay_id' => (int)$parent, 'ebended' => NULL), array('e_id' => $eid));
			$ra['admin'] = $this->session->userdata['ownnames'];
			$ra['time'] = CurrentTimeR();
			$ra['ctrl'] = 'ReplaceLike';
			$ra['field'] = 'ebay_id';
			$ra['atype'] = 'M';
			$ra['e_id'] = (int)$eid;
			$ra['field'] = 'ebay_id';
			$ra['ebay'] = (int)$parent;
			$ra['time'] = CurrentTime();
			$ra['datafrom'] = $item['ebay_id'];
			$ra['datato'] = (int)$parent;
						
			$this->db->insert('ebay_actionlog', $ra); 
		Redirect('Myebay/LikeItem/'.(int)$parent);

}

function _ChangeItemId($eid = '', $page = 1)
{

	if ((int)$_POST['itemid'] > 0 && (int)$eid > 0)
	{
		$this->ReWaterMark((int)$id);
		
		$item = $this->Myebay_model->GetItem((int)$eid);	
		if (!$item) $item['ebay_id'] = 0;
			
			$this->db->update('ebay', array('ebay_id' => (int)$_POST['itemid'], 'ebended' => NULL), array('e_id' => (int)$eid));
			$ra['admin'] = $this->session->userdata['ownnames'];
			$ra['time'] = CurrentTimeR();
			$ra['ctrl'] = 'ChangeItemID';
			$ra['field'] = 'ebay_id';
			$ra['atype'] = 'M';
			$ra['local'] = (int)$eid;
			$ra['field'] = 'ebay_id';
			$ra['ebay'] = (int)$_POST['itemid'];
			$ra['time'] = CurrentTime();
			$ra['datafrom'] = $item['ebay_id'];
			$ra['datato'] = (int)$_POST['itemid'];
						
			$this->db->insert('ebay_actionlog', $ra); 
		
		
	}
		
	Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$eid);

}

function _RevertAction($alid = 0, $page = 1)
{
		$this->db->where('al_id', (int)$alid);							
		$q = $this->db->get('ebay_actionlog');
		if ($q->num_rows() > 0)
		{
		 	$ra = $q->row_array();
			
			unset($this->actabrv['Ebay Quantity']);
			$revactabrv = array_flip($this->actabrv);
			if (isset($revactabrv[$ra['field']])) $ra['field'] = $revactabrv[$ra['field']];
			
			//printcool ($ra);

			if ($ra['field'] == 'sn') $this->db->update('ebay_transactions', array($ra['field'] => $ra['datafrom']), array('rec' => (int)$ra['trans_id']));
			elseif ($ra['field'] == 'asc') $this->db->update('ebay_transactions', array($ra['field'] => $ra['datafrom']), array('rec' => (int)$ra['trans_id']));
			/*elseif ()
			{
				
				
				
				
			}*/
			else $this->db->update('ebay', array($ra['field'] => $ra['datafrom']), array('e_id' => (int)$ra['e_id']));
			
			$ra['admin'] = $this->session->userdata['ownnames'];
			$ra['time'] = CurrentTimeR();
			unset($ra['al_id']);
			$ra['ctrl'] = 'Revert';
			$from = $ra['datafrom'];
			$ra['datafrom'] = $ra['datato'];
			$ra['datato'] = $from;
			
			$this->db->insert('ebay_actionlog', $ra); 
			//printcool ($ra);
			//printcool ($revactabrv);
			
			if ($ra['field'] == 'e_part') 
			{
				$this->ReviseEbayDescription($ra['e_id']);
				echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/ActionLog/'.$page.'#'.(int)$alid.'\';",4000);
-->
</script>';
			exit();
			}
			if ($ra['field'] == 'e_qpart')
			{
				$this->EbayInventoryUpdate((int)$ra['e_id'], false);
				echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/ActionLog/'.$page.'#'.(int)$alid.'\';",4000);
-->
</script>';
			exit();
			
			}
		}

		Redirect('Myebay/ActionLog/'.$page.'#'.(int)$alid);
}
function _ActionLog($page = 1)		
{
		$last_search = $this->session->userdata('last_search');
		
		if (isset($_POST['field']))
		{
			if (isset($_POST['append'])) $last_search = $this->input->post('field', TRUE);		
			else
			{
				 $ls = $this->input->post('field', TRUE);
				 foreach ($ls as $k => $v)
				 {
				 	if (trim($v) != '') $last_search[$k] = trim($v); 
				 }
			}
		}
		$this->mysmarty->assign('lastsearch', $last_search);
		$this->session->set_userdata('last_search', $last_search);
			
		if (!$_POST && !$last_search) $this->session->set_userdata('page', $page);		
		
		$this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());			
		$data = $this->Myebay_model->GetActionLogS($last_search, $page, $this->actabrv);
	
		if ($data['results'])
		{
			foreach ($data['results'] as $k => $v)
			{
				$time = explode('-', $v['time']);
				$data['results'][$k]['date'] = trim($time[1]);				
			}
		}	
		$this->mysmarty->assign('list', $data['results']);
		$this->mysmarty->assign('pages', $data['pages']);
		$this->mysmarty->assign('page', (int)$page);
		
		$this->mysmarty->assign('abbr', $this->actabrv);
		
		$this->mysmarty->view('myebay/myebay_actionlog.html');
		
}
function _ActionLogold($page = 1)
	{			
		$session_search = $this->session->userdata('alast_string');
		$session_where = $this->session->userdata('alast_where');

		if (isset($_POST['search'])) $string = $this->input->post('search', TRUE);		
		elseif ($session_search) $string = $this->session->userdata('last_string');
		else $string = '';
		if (isset($_POST['where']) && $_POST['where'] < 8) $where = (int)$this->input->post('where', TRUE);		
		elseif ($session_where) $where = $this->session->userdata('last_where');
		else $where = '';
		
		//printcool ($string);
		if (!$_POST && $string == '' && $where == '') $this->session->set_userdata('page', $page);
		$this->session->set_userdata('alast_string', $string);
		$this->mysmarty->assign('string', $string);	
		$this->session->set_userdata('alast_where', $where);
		$this->mysmarty->assign('where', $where);	
		
		$this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());			
		$data = $this->Myebay_model->GetActionLog($string, $where, $page, $this->actabrv);
		if ($data['results'])
		{
			foreach ($data['results'] as $k => $v)
			{
				$time = explode('-', $v['time']);
				$data['results'][$k]['date'] = trim($time[1]);				
			}
		}	
		$this->mysmarty->assign('list', $data['results']);
		$this->mysmarty->assign('pages', $data['pages']);
		$this->mysmarty->assign('page', (int)$page);
		
		$this->mysmarty->assign('abbr', $this->actabrv);
		
		$this->mysmarty->view('myebay/myebay_actionlog.html');
	}


function _FindInDrive()
{	
	$string = trim($this->input->post('gsearch'));
	if ($string == '') exit('No search');
	echo 'Testing';	
}
function _SaveGS($id = 0, $page = 1)
{
	if ((int)$id > 0)
	{
	
		$this->db->select('gsid1, gsid2, gsid3, gsid4, gsid5, ebay_id');
		$this->db->where('e_id', (int)$id);
		$gss = $this->db->get('ebay');
		if ($this->gss->num_rows() > 0) 
		{
			$r = $this->query->row_array();						
		}
	 $this->db->update('ebay', array('gsid1' => (int)$this->input->post('gsid1'), 'gsid2' => (int)$this->input->post('gsid2'), 'gsid3' => (int)$this->input->post('gsid3'), 'gsid4' => (int)$this->input->post('gsid4'), 'gsid5' => (int)$this->input->post('gsid5')), array('e_id' => (int)$id));
	 	
		$this->_logaction('SaveGoogleSheet', 'G',array('gsid1' => $r['gsid1']), array('gsid1' => (int)$this->input->post('gsid1')), $id, $r['ebay_id'], 0);
		$this->_logaction('SaveGoogleSheet', 'G',array('gsid2' => $r['gsid2']), array('gsid2' => (int)$this->input->post('gsid2')), $id, $r['ebay_id'], 0);
		$this->_logaction('SaveGoogleSheet', 'G',array('gsid3' => $r['gsid3']), array('gsid3' => (int)$this->input->post('gsid3')), $id, $r['ebay_id'], 0);
		$this->_logaction('SaveGoogleSheet', 'G',array('gsid4' => $r['gsid4']), array('gsid4' => (int)$this->input->post('gsid4')), $id, $r['ebay_id'], 0);
		$this->_logaction('SaveGoogleSheet', 'G',array('gsid5' => $r['gsid5']), array('gsid5' => (int)$this->input->post('gsid5')), $id, $r['ebay_id'], 0);
	 	
	$this->session->set_flashdata('success_msg', 'SUCCESS - Updated Sheets for Ebay item '.(int)$id);
	$this->session->set_flashdata('action', (int)$id);
	}
	 Redirect('Myebay/ListItems/'.(int)$page);
}

function _AddFromStore($storeid = 0)
	{
		if ((int)$storeid == 0) Redirect('Myebay');
		$itemid = $this->Myebay_model->GetStoreFirstProduct($storeid);	
		if ($itemid) Redirect ('Myebay/Add/0/'.$itemid);		
		else Redirect ('Myebay');	
	}
function _CleanSearch()
	{
		$this->session->unset_userdata('last_string');
		$this->session->unset_userdata('last_where');
		$this->session->unset_userdata('last_zero');
		$this->session->unset_userdata('last_mm');
		$this->session->unset_userdata('last_bcnmm');
		$this->session->unset_userdata('last_sitesell');
		Redirect('Myebay');
	}
function _CleanActionLogSearch()
	{
		//$this->session->unset_userdata('last_string');
		//$this->session->unset_userdata('last_where');
		$this->session->unset_userdata('last_search');		
		$page = $this->session->userdata['page'];
		if ((int)$page != 0) Redirect('Myebay/ActionLog/'.$page);
		else Redirect('Myebay/ActionLog');
	}
function _GetSource($itemid = '')
	{
		$this->id = (int)$itemid;
		if ($this->id == 0)	Redirect('Myebay');		
		$this->displays = $this->Myebay_model->GetItem($this->id);			
		$this->_GetSpecialAndTree();
		$this->mysmarty->assign('displays', $this->displays);
		$this->load->model('Settings_model'); 
		$this->Settings_model->GetEbayListingAddress();
		$this->mysmarty->view('myebay/myebay_source.html');
	}

function _Delete ($id = '')
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
			
		$this->session->set_flashdata('success_msg', 'Item '.$this->id.' Deleted');
		Redirect("Myebay");
	}
	

function _frontsell($id = '', $page = '')
	{
	
	if ((int)$id > 0) $do = $this->Myebay_model->SwapSiteSellVal((int)$id);
	
	$this->session->set_flashdata('action', (int)$id);	
	Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);

	}
function _FrontOffice()
{
	
	$this->load->helper('directory');
	$this->load->helper('file');
	$responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
	$store = simplexml_load_string($responseXml);						
		
	$sc = array();
	if (isset($store->Store->CustomCategories->CustomCategory))
	{
		foreach ($store->Store->CustomCategories->CustomCategory as $s)
		{
			$a = (array)$s;
			$sc[$a['CategoryID']] = trim($a['Name']);	
		}
	}
	natcasesort($sc);
	$this->mysmarty->assign('store', $sc);
	
	$this->db->select('storeCatID, sitesell');	
	$this->db->where('storeCatID !=', 0);
	$sq = $this->db->get('ebay');
	$sell = 0;
	$nosell = 0;
	$sccount = array();
	if ($sq->num_rows() > 0) 
			{
				foreach ($sq->result_array() as $k => $v)
				{
					if (isset($sc[$v['storeCatID']])) $key = $sc[$v['storeCatID']];
					else $key = $v['storeCatID'];
					if (isset($sccount[$key][$v['sitesell']])) $sccount[$key][$v['sitesell']]++;
					else $sccount[$key][$v['sitesell']] = 1;
					$sccount[$key]['id'] = $v['storeCatID']; 
				}
			}	
	ksort($sccount);
	foreach ($sccount as $s)
	{
		$sell = $sell + $s[1];
		$nosell = $nosell + $s[0];	
	}
	
	$this->mysmarty->assign('sell', $sell);
	
	$this->mysmarty->assign('nosell', $nosell);
	
	//printcool ($sccount);
	$this->mysmarty->assign('storecount', $sccount);
	$this->mysmarty->view('myebay/myebay_frontoffice.html');
	
}
function _FrontStoreOn($id)
{
	 $this->db->update('ebay', array('sitesell' => 1), array('storeCatID' => (int)$id));
	 $this->session->set_flashdata('action', (int)$id);	
	 Redirect("Myebay/FrontOffice");
}
function _FrontStoreOff($id)
{
	 $this->db->update('ebay', array('sitesell' => 0), array('storeCatID' => (int)$id));
	 $this->session->set_flashdata('action', (int)$id);	
	 Redirect("Myebay/FrontOffice");
}
function __GetCategorySpecifics($catID = '')
{
	if ($catID != '')
	{	

						require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetCategorySpecificsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<WarningLevel>High</WarningLevel>';
						$requestXmlBody .= '<CategorySpecific><CategoryID>'.$catID.'</CategoryID></CategorySpecific>
						</GetCategorySpecificsRequest>';
						$verb = 'GetCategorySpecifics';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);
						printcool ($xml);


	}
}

function _Download($id = 0, $place = 1)
{
	if ((int)$id > 0 && (int)$place < 5 && (int)$place > 0)
	{
		$img = $this->Myebay_model->GetImage((int)$id, (int)$place);
		$this->load->helper('download');
		
		if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath($id).'Original_'.$img))
		{
			$data = file_get_contents($this->config->config['paths']['imgebay'].'/'.idpath($id).'Original_'.$img); 
			force_download('Original_'.$img, $data);
		}
		else
		{
			echo 'File Does Not Exist';
		}		
	}
}
function _Edit($itemid = '', $catID = 0, $merge = false)
	{	
	if (isset($_POST['catsearch']) && (trim($_POST['catsearch']) != '')) $this->mysmarty->assign('searchcat', trim($_POST['catsearch']));
 	else $this->mysmarty->assign('searchcat', '');
		$this->id = (int)$itemid;
	
		if ($this->id > 0) 
		{
						$this->mysmarty->assign('shipcount',array(1,2,3,4));
		
						$this->load->helper('directory');
						$this->load->helper('file');		
		
						$sresponseXml = read_file($this->config->config['ebaypath'].'/shipping.txt');
						$shxml = simplexml_load_string($sresponseXml);
						$this->mysmarty->assign('ShippingOptions', $shxml->ShippingServiceDetails);		
						
						$responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
						$store = simplexml_load_string($responseXml);						
					
						$sc = array();
						if (isset($store->Store->CustomCategories->CustomCategory))
						{
							foreach ($store->Store->CustomCategories->CustomCategory as $s)
							{
								$a = (array)$s;
								$sc[$a['CategoryID']] = $a['Name'];	
							}
						}
						$this->mysmarty->assign('store', $sc);
						
						
		
		
		$this->displays = $this->Myebay_model->GetItem($this->id);	
		
		if ($this->displays['e_img1'] != '') $imgexists[1] = file_exists($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'Original_'.$this->displays['e_img1']);
		else $imgexists[2] = false;
		
		if ($this->displays['e_img2'] != '') $imgexists[2] = file_exists($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'Original_'.$this->displays['e_img2']);
		else $imgexists[2] = false;
		
		if ($this->displays['e_img3'] != '') $imgexists[3] = file_exists($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'Original_'.$this->displays['e_img3']);
		else $imgexists[3] = false;
		
		if ($this->displays['e_img4'] != '') $imgexists[4] = file_exists($this->config->config['paths']['imgebay'].'/'.idpath($this->id).'Original_'.$this->displays['e_img4']);
		else $imgexists[4] = false;	
		
		$this->mysmarty->assign('imgexists', $imgexists);
			
		//$this->_GetCategorySpecifics($this->displays['primaryCategory']);
		$this->_GetSpecialAndTree();
		$this->load->library('form_validation');

		if (!$merge)
		{
		$this->form_validation->set_rules('e_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('e_manuf', 'Manufacturer', 'trim|xss_clean');
		$this->form_validation->set_rules('e_model', 'Model', 'trim|xss_clean');
		$this->form_validation->set_rules('e_part', 'Part Number', 'trim|xss_clean');
		$this->form_validation->set_rules('e_compat', 'Compatibility', 'trim|xss_clean');
		$this->form_validation->set_rules('e_package', 'Package', 'trim|xss_clean');
		$this->form_validation->set_rules('e_condition', 'Condition', 'trim|xss_clean');
		$this->form_validation->set_rules('e_shipping', 'Shipping', 'trim|xss_clean');
		$this->form_validation->set_rules('e_desc', 'Description', 'trim|xss_clean');
		
		$this->form_validation->set_rules('listingType', 'Listing Type', 'trim|xss_clean');
		$this->form_validation->set_rules('primaryCategory', 'Primary Category', 'trim|xss_clean');
		//$this->form_validation->set_rules('pCTitle', 'Description', 'trim|xss_clean');
		$this->form_validation->set_rules('listingDuration', 'Duration', 'trim|required|xss_clean');
		$this->form_validation->set_rules('startPrice', 'Start Price', 'trim|xss_clean');
		$this->form_validation->set_rules('buyItNowPrice', 'Price', 'trim|required|xss_clean');
		$this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|xss_clean');
		$this->form_validation->set_rules('PaymentMethods', 'Payment method', 'required');
		$this->form_validation->set_rules('Subtitle', 'Subtitle', 'trim|xss_clean');
		$this->form_validation->set_rules('Condition', 'Condition', 'trim|required|xss_clean');
		$this->form_validation->set_rules('upc', 'UPC No.', 'trim|xss_clean');
		$this->form_validation->set_rules('location', 'Location', 'trim|xss_clean');
		$this->form_validation->set_rules('storecat', 'Store Category', 'trim|required|xss_clean');
		}
		if ($this->form_validation->run() == FALSE || $merge)
			{	
				if (!$merge) $this->inputdata = array(										
									'e_title' => $this->input->post('e_title', TRUE),
									'e_manuf' => $this->input->post('e_manuf', TRUE),
									'e_model' => $this->input->post('e_model', TRUE),
									'e_part' => $this->_SerialSave($this->input->post('e_part', TRUE)),
									'e_compat' => $this->input->post('e_compat', TRUE),
									'e_package' => $this->input->post('e_package', TRUE),
									'e_condition' => $this->input->post('e_condition', TRUE),
									'e_shipping' => $this->input->post('e_shipping', TRUE),
									'e_notice_header' => (int)$this->input->post('e_notice_header', TRUE),
									'e_notice_shipping' => (int)$this->input->post('e_notice_shipping', TRUE),
									'e_shipping' => $this->input->post('e_shipping', TRUE),
									'e_desc' => $this->input->post('e_desc', TRUE),									
									
									'listingType' => $this->input->post('listingType', TRUE),
									'primaryCategory' => (int)$this->input->post('primaryCategory', TRUE),
									'listingDuration' => $this->input->post('listingDuration', TRUE),									
									'buyItNowPrice' => $this->input->post('buyItNowPrice', TRUE),
									'quantity' => (int)$this->input->post('quantity', TRUE),
									'PaymentMethods' => $this->input->post('PaymentMethods', TRUE),
									'Subtitle' => $this->input->post('Subtitle', TRUE),
									'Condition' => $this->input->post('Condition', TRUE),
									'upc' => $this->input->post('upc', TRUE),
									'location' => $this->input->post('location', TRUE),									
									'storecat' =>  $this->input->post('storecat', TRUE),
									'gtaxonomy' => addslashes($this->input->post('gtaxonomy')),
									'weight_lbs' => $this->input->post('weight_lbs'),
									'weight_oz' => $this->input->post('weight_oz')
									);
									
									if (isset($_POST['shipping'])) $this->inputdata['shipping'] = $_POST['shipping'];
									else $this->inputdata['shipping'] = array();
						
			
					if (!$_POST || $merge)
					{ 
						$catID = $this->displays['primaryCategory'];
						
						
						$this->mysmarty->assign('catname', $this->displays['pCTitle']);
						$this->mysmarty->assign('storecat', $this->displays['storeCatID']);	
						$this->displays['storecat'] = $this->displays['storeCatID'];
						$this->inputdata = $this->displays;
					}
					
					if (isset($_POST['storecat']))
					{
						if ($merge == 2) $siv = $this->Myebay_model->GetItemItemValues((int)$_POST['storecat']);	
						else $siv = $this->Myebay_model->GetStoreItemValues((int)$_POST['storecat']);	
						if ($siv)
						{
							$this->mysmarty->assign('takenfrom', $siv['e_id']);

							$this->displays['PaymentMethod'] = $this->inputdata['PaymentMethod'] = $siv['PaymentMethod'];
							$this->displays['shipping'] = $this->inputdata['shipping'] = $siv['shipping'];
							
							if (isset($_POST['catsearch']) && (trim($_POST['catsearch']) != ''))
							{
							$this->displays['pCTitle'] = $this->inputdata['pCTitle'];
							$this->displays['primaryCategory'] = $this->inputdata['primaryCategory'];
							}
							else
							{
								$this->displays['pCTitle'] = $this->inputdata['pCTitle'] = $siv['pCTitle'];
							$this->displays['primaryCategory'] = $this->inputdata['primaryCategory'] = $siv['primaryCategory'];
							}
							$this->displays['storeCatID'] = $this->inputdata['storeCatID'] = $siv['storeCatID'];
							$this->displays['storeCatTitle'] = $this->inputdata['storeCatTitle'] = $siv['storeCatTitle'];
							$this->displays['storecat'] = $this->inputdata['storecat'] = $siv['storeCatID'];
							
							$this->mysmarty->assign('catname', $this->inputdata['pCTitle']);
							$this->mysmarty->assign('storecat', $this->inputdata['storeCatID']);	
	
						}
					}

				if (!isset($sc[$this->displays['storeCatID']])) 
				{	
					$this->mysmarty->assign('storecatnotfound', TRUE);
					$this->mysmarty->assign('storeCatTitle', $this->displays['storeCatTitle']);	
					$this->mysmarty->assign('storeCatID', $this->displays['storeCatID']);											
				}
				
				$this->mysmarty->assign('ebupd', TRUE);
				$this->mysmarty->assign('displays', $this->displays);
				//$this->mysmarty->assign('categories', $this->Myebay_model->GetEbayDataCategories((int)$catID));
				$distinctcats = $this->Myebay_model->GetDistinctUsedEbayCategories();
				//printcool ($this->session->userdata['gotcats']);
				//printcool ($distinctcats);
				if (isset($this->session->userdata['gotcats'])) 
				{
					$distinctcats[0] = CleanCatDups(array_merge($this->session->userdata['gotcats'], $distinctcats[0]));
				}
				
				//printcool ($distinctcats);
				$this->mysmarty->assign('categories', $distinctcats);
				
				//$this->mysmarty->assign('categories', $this->Myebay_model->GetDistinctUsedEbayCategories());
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->assign('taxonomy', $this->_gTaxonomy());
				$this->mysmarty->view('myebay/myebay_editnew.html');
				exit();
			}
			else 
			{					
				if (isset($_POST['shipping']) && $_POST['shipping']['domestic'][1]['ShippingService'] == '' && $_POST['shipping']['domestic'][2]['ShippingService'] == '' && $_POST['shipping']['domestic'][3]['ShippingService'] == '' && $_POST['shipping']['domestic'][4]['ShippingService'] == '' && $_POST['shipping']['international'][1]['ShippingService'] == '' && $_POST['shipping']['international'][2]['ShippingService'] == '' && $_POST['shipping']['international'][3]['ShippingService'] == '' && $_POST['shipping']['international'][4]['ShippingService'] == '')
				{
					 echo "You must specify atleast one shipping method. <a href=\"javascript:history.back()\">Back</a>";
					 exit();
					 }
					 
					$this->db_data = array(												 
											'e_title' => $this->form_validation->set_value('e_title'),
											'e_sef' => $this->_CleanSef($this->form_validation->set_value('e_title')),
											'e_manuf' => $this->form_validation->set_value('e_manuf'),
											'e_model' => $this->form_validation->set_value('e_model'),
											'e_part' => $this->_SerialSave($this->form_validation->set_value('e_part')),
											'e_qpart' => $this->_RealCount($this->_SerialSave($this->form_validation->set_value('e_part'))),
											'e_compat' => $this->form_validation->set_value('e_compat'),
											'e_package' => $this->form_validation->set_value('e_package'),
											'e_condition' => $this->form_validation->set_value('e_condition'),
											'e_shipping' => $this->form_validation->set_value('e_shipping'),
											'e_notice_header' => (int)$this->input->post('e_notice_header', TRUE),
											'e_notice_shipping' => (int)$this->input->post('e_notice_shipping', TRUE),
											'e_desc' => $this->form_validation->set_value('e_desc'),
											
											'listingType' => $this->form_validation->set_value('listingType'),
											'primaryCategory' => (int)$this->form_validation->set_value('primaryCategory'),
											'pCTitle' => $this->Myebay_model->GetEbayCategoryTitle((int)$this->form_validation->set_value('primaryCategory')),
											'listingDuration' => $this->form_validation->set_value('listingDuration'),
											'startPrice' => $this->form_validation->set_value('buyItNowPrice'),
											'buyItNowPrice' => $this->form_validation->set_value('buyItNowPrice'),
											'quantity' => (int)$this->form_validation->set_value('quantity'),
											'PaymentMethod' => serialize($this->input->post('PaymentMethods', TRUE)),
											'Subtitle' => $this->form_validation->set_value('Subtitle'),
											'Condition' => $this->form_validation->set_value('Condition'),
											'upc' => $this->form_validation->set_value('upc'),
											'location' => $this->form_validation->set_value('location'),
											'shipping' => serialize($_POST['shipping']),
											'gtaxonomy' => addslashes($this->input->post('gtaxonomy')),
											'weight_lbs' => $this->input->post('weight_lbs'),
											'weight_oz' => $this->input->post('weight_oz'),
											'weight_kg' => lbsoz2kg($this->input->post('weight_lbs'), $this->input->post('weight_oz'))
											);
					$this->db_data['e_qpart'] = $this->_RealCount((string)$this->db_data['e_part']);	
											
					if (isset($sc[$this->form_validation->set_value('storecat')])) 
					{
					$this->db_data['storeCatID'] = $this->form_validation->set_value('storecat');
					$this->db_data['storeCatTitle'] = $sc[(int)$this->form_validation->set_value('storecat')];
					}						
											
					if ($this->db_data['PaymentMethod'] == 'b:0;') $this->db_data['PaymentMethod'] ='';
					
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
						$this->displays = $this->Myebay_model->GetItem($this->id);
						$this->Myebay_model->Update((int)$this->id,$this->db_data);
						
						foreach ($this->db_data as $k => $v)
						{
							if (isset($this->displays[$k]) && $this->displays[$k]) $olddata = (string)$this->displays[$k];
							else $olddata = '';							
							if ($k != 'PaymentMethod' && $k != 'shipping' && $k != 'startPrice') 
							{
								if ($k == 'e_part') $latp = 'B';
								elseif ($k = 'e_qpart') $latp = 'B';
								elseif ($k = 'quantity') $latp = 'Q';
								else $latp = 'M';
								$this->_logaction('Edit', $latp ,array($k => $olddata), array($k => $v), (int)$this->id, $this->displays['ebay_id'], 0);
								
							}
						}
						$this->session->unset_userdata('gotcats');
						$this->session->set_flashdata('success_msg', '"'.$this->db_data['e_title'].'" Updated');
						$this->session->set_flashdata('action', (int)$this->id);
						if ($watermark) Redirect('Myebay/DoWaterMark/'.(int)$this->id);
						else redirect("Myebay#".(int)$this->id);					
			}
	}
	else {
			redirect("Myebay");
	}
}


function _UpdateCategories($catID = 0)
{
	$loop = $this->Myebay_model->GetEbayDataCategories((int)$catID);
	$main = $this->Myebay_model->GetEbayCategoryTitle((int)$catID);
	if ((int)$catID != 0) $html = $main.'&nbsp;&nbsp;&nbsp;<a style="font-size:10px;" id="aprimaryCategory" onClick="catupdt(0);"><img src="'.Site_url().'images/admin/delete.png" /> CLEAR</a><br><br>';	
	else $html = 'Select Main Category:<br><br>';	
	
	if (!$loop) $html .= '<span style="color:red; font-size:10px;">No more sub categories</span><br><br><select name="primaryCategory" >';	
	
	else $html .="<select id=\"primaryCategory\" name=\"primaryCategory\" onchange=\"var catid = document.getElementById('primaryCategory').value; catupdt(catid);\">";	
	
	if ($loop[$catID]) foreach ($loop[$catID] as $k => $v) $html .= '<option value="'.$v['catID'].'">'.$v['catName'].'</option>';
	else $html .= '<option value="'.$catID.'">'.$main.'</option>';
	
	$html .= '</select>';
	echo $html;
}
function _DoWaterMark($id, $place = 1)
	{
		$img = $this->Myebay_model->GetOldEbayImage((int)$id, $place);
		if ($img)
		{
			if (!copy($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).$img, $this->config->config['paths']['imgebay'].'/'.idpath((int)$id).'Original_'.$img)) {
				echo "failed to copy Original_file...\n";
				break;
			}
			
			if (!copy($this->config->config['paths']['imgebay'].'/'.idpath((int)$id).$img, $this->config->config['paths']['imgebay'].'/'.idpath((int)$id).'Ebay_'.$img)) {
				echo "failed to copy Ebay_file...\n";
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
				
			$this->_WaterMark('bottom', 'right', 'wm_original_bottom.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), $img);
			$this->_WaterMark('middle', 'center', 'wm_original_center.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), $img);
			$this->_WaterMark('bottom', 'right', 'wm_bigtn_bottom.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), 'thumb_main_'.$img);
			$this->_WaterMark('middle', 'center', 'wm_bigtn_center.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), 'thumb_main_'.$img);
			$this->_WaterMark('bottom', 'right', 'wm_smalltn_bottom.png', $this->config->config['paths']['imgebay'].'/'.idpath((int)$id), 'thumb_'.$img);	
		}
	$place++;
	
	if ($place >4) redirect("/Myebay#".(int)$id);
	else Redirect('Myebay/DoWaterMark/'.(int)$id.'/'.$place);
	
	}
	
function _Add($catID = 0, $itemID = 0)
	{	//printcool ($_POST);

		if (isset($_POST['catsearch']) && (trim($_POST['catsearch']) != '')) $this->mysmarty->assign('searchcat', trim($_POST['catsearch']));
 	else $this->mysmarty->assign('searchcat', '');
	
		$this->shiponly = false;
		/*if ($itemID == 0)
		{
			$itemID = $this->Myebay_model->GetFirstProduct();	
			if ($itemID) $this->shiponly = true;
			
		}*/
		$this->mysmarty->assign('shipcount',array(1,2,3,4));
		
		$this->load->helper('directory');
						$this->load->helper('file');
		
		
						$sresponseXml = read_file($this->config->config['ebaypath'].'/shipping.txt');
						$shxml = simplexml_load_string($sresponseXml);
						$this->mysmarty->assign('ShippingOptions', $shxml->ShippingServiceDetails);
		
						
						$responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
						$store = simplexml_load_string($responseXml);						
					
						$sc = array();
						if (isset($store->Store->CustomCategories->CustomCategory))
						{
							foreach ($store->Store->CustomCategories->CustomCategory as $s)
							{
								$a = (array)$s;
								$sc[$a['CategoryID']] = $a['Name'];	
							}			
						}
						$this->mysmarty->assign('store', $sc);
							
					
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
		
		$this->form_validation->set_rules('listingType', 'Listing Type', 'trim|xss_clean');
		$this->form_validation->set_rules('primaryCategory', 'Primary Category', 'trim|xss_clean');
		//$this->form_validation->set_rules('pCTitle', 'Description', 'trim|xss_clean');
		$this->form_validation->set_rules('listingDuration', 'Duration', 'trim|required|xss_clean');
		$this->form_validation->set_rules('startPrice', 'Start Price', 'trim|xss_clean');
		$this->form_validation->set_rules('buyItNowPrice', 'Price', 'trim|required|xss_clean');
		$this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|xss_clean');
		$this->form_validation->set_rules('PaymentMethods', 'Payment method', 'required');
		$this->form_validation->set_rules('Subtitle', 'Subtitle', 'trim|xss_clean');
		$this->form_validation->set_rules('Condition', 'Condition', 'trim|required|xss_clean');
		$this->form_validation->set_rules('upc', 'UPC No.', 'trim|xss_clean');
		$this->form_validation->set_rules('location', 'Location', 'trim|xss_clean');
		$this->form_validation->set_rules('storecat', 'Store Category', 'trim|required|xss_clean');

		if ($this->form_validation->run() == FALSE)
			{				
				if ((int)$itemID > 0)
				{
					
						set_time_limit(90);
						ini_set('mysql.connect_timeout', 90);
						ini_set('max_execution_time', 90);  
						ini_set('default_socket_timeout', 90); 
						require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= '<ItemID>'.(int)$itemID.'</ItemID>
						</GetItemRequest>';
						$verb = 'GetItem';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);

						//printcool ($xml);
						
						
					
					
						
					$this->inputdata = array(										
									'e_title' => $this->input->post('e_title', TRUE),
									'e_manuf' => $this->input->post('e_manuf', TRUE),
									'e_model' => $this->input->post('e_model', TRUE),
									'e_part' => $this->_SerialSave($this->input->post('e_part', TRUE)),
									'e_compat' => $this->input->post('e_compat', TRUE),
									'e_package' => $this->input->post('e_package', TRUE),
									'e_condition' => $this->input->post('e_condition', TRUE),
									'e_shipping' => $this->input->post('e_shipping', TRUE),
									'e_notice_header' => (int)$this->input->post('e_notice_header', TRUE),
									'e_notice_shipping' => (int)$this->input->post('e_notice_shipping', TRUE),
									'e_desc' => $this->input->post('e_desc', TRUE),
									'upc' => $this->input->post('upc', TRUE),
									
									'listingType' => $xml->Item->ListingType,
									'primaryCategory' => (int)$xml->Item->PrimaryCategory->CategoryID,
									'listingDuration' => $xml->Item->ListingDuration				
									
									);
									
							
					if (is_array($xml->Item->PaymentMethods))
					{
						foreach ($xml->Item->PaymentMethods as $p) $this->inputdata['PaymentMethods'][$p] = 'on';						
					}
					else 
					{	
						$p = str_replace(' ', '', trim((string)$xml->Item->PaymentMethods));
						$this->inputdata['PaymentMethods'][$p] = 'on';
					}
						if (!$this->shiponly) 
						{						
						$this->mysmarty->assign('catname', $xml->Item->PrimaryCategory->CategoryName);
						$this->mysmarty->assign('catID', $xml->Item->PrimaryCategory->CategoryID);
						$this->mysmarty->assign('storecat', $xml->Item->Storefront->StoreCategoryID);		
						$this->mysmarty->assign('itemID', (int)$itemID);
						}
						else
						{
						unset($this->inputdata['primaryCategory']);
						}
					
					//printcool ($xml->Item);
					$sd = array();
					if (isset($xml->Item->ShippingDetails->ShippingServiceOptions))
					{
						foreach ($xml->Item->ShippingDetails->ShippingServiceOptions as $s)
							{
								$sd[(int)$s->ShippingServicePriority] = array('ShippingService' => (string)$s->ShippingService, 'ShippingServiceCost' => (float)$s->ShippingServiceCost, 'ShippingServiceAdditionalCost' => (float)$s->ShippingServiceAdditionalCost,  'FreeShipping' => (string)$s->FreeShipping											
											);								
							}
					
					}
					$is = array();
					if (isset($xml->Item->ShippingDetails->InternationalShippingServiceOption))
					{
						foreach ($xml->Item->ShippingDetails->InternationalShippingServiceOption as $s)
							{
								$is[(int)$s->ShippingServicePriority] = array('ShippingService' => (string)$s->ShippingService, 'ShippingServiceCost' => (float)$s->ShippingServiceCost, 'ShippingServiceAdditionalCost' => (float)$s->ShippingServiceAdditionalCost, 'ShipToLocation' => (string)$s->ShipToLocation);								
							}
								}
					$this->mysmarty->assign('ShippingServices', $sd);
					
					$this->mysmarty->assign('IntlShippingServices', $is);

					$this->mysmarty->assign('SellerExcludeShipToLocationsPreference',(string)$xml->Item->ShippingDetails->SellerExcludeShipToLocationsPreference);
					$this->mysmarty->assign('ExcludeShipToLocation', (array)$xml->Item->ShippingDetails->ExcludeShipToLocation);					
						
					//printcool ($shxml->ShippingServiceDetails);
					//$this->mysmarty->assign('ShippingDetails', printcool ($xml->Item->ShippingDetails, TRUE));
					//$this->mysmarty->assign('ReturnPolicy',	printcool ($xml->Item->ReturnPolicy, TRUE));
	
				}
				else
				{	

				$this->inputdata = array(										
									'e_title' => $this->input->post('e_title', TRUE),
									'e_manuf' => $this->input->post('e_manuf', TRUE),
									'e_model' => $this->input->post('e_model', TRUE),
									'e_part' => $this->_SerialSave($this->input->post('e_part', TRUE)),
									'e_compat' => $this->input->post('e_compat', TRUE),
									'e_package' => $this->input->post('e_package', TRUE),
									'e_condition' => $this->input->post('e_condition', TRUE),
									'e_shipping' => $this->input->post('e_shipping', TRUE),
									'e_notice_header' => (int)$this->input->post('e_notice_header', TRUE),
									'e_notice_shipping' => (int)$this->input->post('e_notice_shipping', TRUE),
									'e_desc' => $this->input->post('e_desc', TRUE),
									
									'listingType' => $this->input->post('listingType', TRUE),
									'primaryCategory' => (int)$this->input->post('primaryCategory', TRUE),
									'listingDuration' => $this->input->post('listingDuration', TRUE),									
									'buyItNowPrice' => $this->input->post('buyItNowPrice', TRUE),
									'quantity' => (int)$this->input->post('quantity', TRUE),
									'PaymentMethods' => $this->input->post('PaymentMethods', TRUE),
									'Subtitle' => $this->input->post('Subtitle', TRUE),
									'Condition' => $this->input->post('Condition', TRUE),
									'upc' => $this->input->post('upc', TRUE),
									'location' => $this->input->post('location', TRUE),									
									'storecat' =>  $this->input->post('storecat', TRUE),
									'gtaxonomy' => addslashes($this->input->post('gtaxonomy')),
									'weight_lbs' => $this->input->post('weight_lbs'),
									'weight_oz' => $this->input->post('weight_oz')														
									);
				//printcool ($this->inputdata);
								if (isset($_POST['shipping'])) $this->inputdata['shipping'] = $_POST['shipping'];
								else $this->inputdata['shipping'] = array();
									//printcool ($this->inputdata['shipping']);
									
				}
				
				if (count($_POST) == 0) $this->inputdata['e_shipping'] = 'United States Postal Service.
We ship Internationally.
We use primarily USPS and FedEx';

								
				$this->mysmarty->assign('inputdata', $this->inputdata);	
					
				$this->mysmarty->assign('ebupd', TRUE);	
				//$this->mysmarty->assign('categories', FALSE);	
				//printcool ($this->Myebay_model->GetEbayDataCategories((int)$catID));
				//$this->mysmarty->assign('categories', $this->Myebay_model->GetEbayDataCategories((int)$catID));	
				$distinctcats = $this->Myebay_model->GetDistinctUsedEbayCategories();
				//printcool ($this->session->userdata['gotcats']);
				//printcool ($distinctcats);
				if (!is_array($distinctcats[0])) $distinctcats[0] = array();
				if (is_array($distinctcats[0]) && count ($distinctcats[0]) == 0) $distinctcats[0] = array();
				if (isset($this->session->userdata['gotcats'])) 
				{
					$distinctcats[0] = CleanCatDups(array_merge($this->session->userdata['gotcats'], $distinctcats[0]));
				}
				
				//printcool ($distinctcats);
				$this->mysmarty->assign('categories', $distinctcats);
				$errors = $this->form_validation->_error_array;
				//printcool ($_POST['PaymentMethods']);
				//if (!isset($_POST['PaymentMethod'])) $errors['PaymentMethod'] = 'Please select payment method';
				$this->mysmarty->assign('errors', $errors);
				$this->mysmarty->assign('taxonomy', $this->_gTaxonomy());
				$this->mysmarty->view('myebay/myebay_add.html');				
				exit();
			}
			else 
			{					
				if (isset($_POST['shipping']) && $_POST['shipping']['domestic'][1]['ShippingService'] == '' && $_POST['shipping']['domestic'][2]['ShippingService'] == '' && $_POST['shipping']['domestic'][3]['ShippingService'] == '' && $_POST['shipping']['domestic'][4]['ShippingService'] == '' && $_POST['shipping']['international'][1]['ShippingService'] == '' && $_POST['shipping']['international'][2]['ShippingService'] == '' && $_POST['shipping']['international'][3]['ShippingService'] == '' && $_POST['shipping']['international'][4]['ShippingService'] == '')
				{
					 echo "You must specify atleast one shipping method. <a href=\"javascript:history.back()\">Back</a>";
					 exit();
					 }
					 
						
					$this->db_data = array(												 
											'e_title' => $this->form_validation->set_value('e_title'),
											'e_sef' => $this->_CleanSef($this->form_validation->set_value('e_title')),
											'e_manuf' => $this->form_validation->set_value('e_manuf'),
											'e_model' => $this->form_validation->set_value('e_model'),
											'e_part' => $this->_SerialSave($this->form_validation->set_value('e_part')),
											'e_qpart' => $this->_RealCount($this->_SerialSave($this->form_validation->set_value('e_part'))),
											'e_compat' => $this->form_validation->set_value('e_compat'),
											'e_package' => $this->form_validation->set_value('e_package'),
											'e_condition' => $this->form_validation->set_value('e_condition'),
											'e_shipping' => $this->form_validation->set_value('e_shipping'),
											'e_notice_header' => (int)$this->input->post('e_notice_header', TRUE),
											'e_notice_shipping' => (int)$this->input->post('e_notice_shipping', TRUE),
											'e_desc' => $this->form_validation->set_value('e_desc'),
											'admin_id' => (int)$this->session->userdata['admin_id'],
											'created' => CurrentTimeR(),
											
											'listingType' => $this->form_validation->set_value('listingType'),
											'primaryCategory' => (int)$this->form_validation->set_value('primaryCategory'),
											'pCTitle' => $this->Myebay_model->GetEbayCategoryTitle((int)$this->form_validation->set_value('primaryCategory')),
											'listingDuration' => $this->form_validation->set_value('listingDuration'),
											'startPrice' => $this->form_validation->set_value('buyItNowPrice'),
											'buyItNowPrice' => $this->form_validation->set_value('buyItNowPrice'),
											'quantity' => (int)$this->form_validation->set_value('quantity'),
											'PaymentMethod' => serialize($this->input->post('PaymentMethods', TRUE)),
											'Subtitle' => $this->form_validation->set_value('Subtitle'),
											'Condition' => $this->form_validation->set_value('Condition'),
											'upc' => $this->form_validation->set_value('upc'),
											'location' => $this->form_validation->set_value('location'),
											'shipping' => serialize($_POST['shipping']),
											'storeCatID' => $this->form_validation->set_value('storecat'),
											'storeCatTitle' => $sc[(int)$this->form_validation->set_value('storecat')],
											'gtaxonomy' => addslashes($this->input->post('gtaxonomy')),
											'weight_lbs' => $this->input->post('weight_lbs'),
											'weight_oz' => $this->input->post('weight_oz'),
											'weight_kg' => lbsoz2kg($this->input->post('weight_lbs'), $this->input->post('weight_oz'))
											);
											
					$this->db_data['e_qpart'] = $this->_RealCount((string)$this->db_data['e_part']);
					
					if ($this->db_data['PaymentMethod'] == 'b:0;') $this->db_data['PaymentMethod'] ='';
					
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
						
						$this->session->unset_userdata('gotcats');
						$this->session->set_flashdata('success_msg', '"'.$this->db_data['e_title'].'" Created');
						$this->session->set_flashdata('action', (int)$this->newid);
						if ($watermark) Redirect('Myebay/DoWaterMark/'.(int)$this->newid);
						else Redirect ('Myebay#'.(int)$this->newid); //redirect("Myebay/GetSource/".(int)$this->newid);							
			}
}
function _ReSubmitEbay($id = 0)
{
	$this->resubmit = TRUE;
	$this->SubmitEbay($id);
}
function _SubmitEbay($id = 0)
{
	if ((int)$id > 0)
	{
		log_message('error', 'SUBMIT START '.(int)$id.' @ '.CurrentTime());

		$this->session->set_flashdata('action', (int)$id);					
		set_time_limit(90); 
		
		$this->ReWaterMark((int)$id);
		$item = $this->Myebay_model->GetItem((int)$id);	
		$zip = $this->Myebay_model->GetSetting('EbayLocationZIP');
		$ppmail = $this->Myebay_model->GetSetting('EbayPayPalMAIL');
		
		if (!$item) { echo 'Item not found!'; exit(); }
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
			 	ini_set('magic_quotes_gpc', false);   
				 
				$this->mysmarty->assign('displays', $item);
				$listDescHtml = htmlspecialchars($this->mysmarty->fetch('myebay/myebay_sendsource.html'));
		
				$listingType     = $item['listingType'];
				$primaryCategory = $item['primaryCategory'];
				$itemTitle       = str_replace('&', '&amp;', $item['e_title']);
				if(get_magic_quotes_gpc()) $itemDescription = stripslashes(str_replace('&', '&amp;', $item['e_desc']));
				else $itemDescription = str_replace('&', '&amp;', $item['e_desc']);				
				$listingDuration = $item['listingDuration'];
				$startPrice      = $item['startPrice'];
				$buyItNowPrice   = $item['buyItNowPrice'];
				$quantity        = $item['quantity'];
				$PaymentMethods  = $item['PaymentMethod'];
				$upc 			 = $item['upc'];
				$partno			 = $item['e_compat'];
				$shipping		 = $item['shipping'];
				$storecat = $item['storeCatID'];

				if ($listingType == 'StoresFixedPrice')
				{
				  $buyItNowPrice = 0.0;   // don't have BuyItNow for SIF
				  $listingDuration = 'GTC';
				}
				
				if ($listingType == 'Dutch') $buyItNowPrice = $buyItNowPrice;   // don't have BuyItNow for Dutch
				$conditiondescription = '';
				if ($item['Condition'] != 1000) $conditiondescription = "<ConditionDescription>".$item['e_condition']."</ConditionDescription>";
				if (isset($PaymentMethods))
				{
					$paymentsnippet = '';
					if ($PaymentMethods != '') foreach ($PaymentMethods as $k => $v)
					{
						$paymentsnippet .= '<PaymentMethods>'.$k.'</PaymentMethods>';						
					}
					if (isset($PaymentMethods['PayPal'])) $paymentsnippet .= '<PayPalEmailAddress>'.$ppmail.'</PayPalEmailAddress>';
				}
				$verb = 'AddItem';
				$upcsnippet = '';
				$true = TRUE;
				//if ($upc != '' || ($item['e_compat'] != '' && $item['e_manuf'] != '')) $upcsnippet .= '<ProductListingDetails><IncludePrefilledItemInformation>'.$true.'</IncludePrefilledItemInformation>';
				if ($upc != '')	$upcsnippet .= '<ProductListingDetails><IncludePrefilledItemInformation>'.$true.'</IncludePrefilledItemInformation><UPC>'.$upc.'</UPC></ProductListingDetails>';
				//if ($item['e_compat'] =! '' && $item['e_manuf'] != '') $upcsnippet .= '<BrandMPN><Brand>'.$item['e_manuf'].'</Brand><MPN>'.$item['e_compat'].'</MPN></BrandMPN>';
				//if ($upc != '' || ($item['e_compat'] != '' && $item['e_manuf'] != '')) $upcsnippet .= '</ProductListingDetails>';

				$imgsnippet = '<PictureDetails>';
				if ($item['e_img1'] != '') $imgsnippet .= '<PictureURL>'.Site_url().'ebay_images/'.$item['idpath'].'/Ebay_'.$item['e_img1'].'</PictureURL>';
				if ($item['e_img2'] != '') $imgsnippet .= '<PictureURL>'.Site_url().'ebay_images/'.$item['idpath'].'/Ebay_'.$item['e_img2'].'</PictureURL>';
				if ($item['e_img3'] != '') $imgsnippet .= '<PictureURL>'.Site_url().'ebay_images/'.$item['idpath'].'/Ebay_'.$item['e_img3'].'</PictureURL>';
				if ($item['e_img4'] != '') $imgsnippet .= '<PictureURL>'.Site_url().'ebay_images/'.$item['idpath'].'/Ebay_'.$item['e_img4'].'</PictureURL>';
				
				$imgsnippet .= '</PictureDetails>';
				$requestXmlBodySTART  = '<?xml version="1.0" encoding="utf-8" ?><AddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				
				$requestXmlBody = "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				
				// <BuyItNowPrice>".$buyItNowPrice."</BuyItNowPrice>
				$requestXmlBody .= "<Item>
			<Title>".$itemTitle."</Title>
			<Description>".$listDescHtml."</Description>
			<PrimaryCategory>
			  <CategoryID>".$primaryCategory."</CategoryID>
			</PrimaryCategory>
			<ConditionID>".$item['Condition']."</ConditionID>
			".$conditiondescription."		    
			<StartPrice>".$startPrice."</StartPrice>
			".$upcsnippet."			
			<Country>US</Country>
			<Currency>USD</Currency>
			<DispatchTimeMax>1</DispatchTimeMax>
			<ListingDuration>".$item['listingDuration']."</ListingDuration>
			<ListingType>StoresFixedPrice</ListingType>
		    ".$paymentsnippet."
			".$imgsnippet."
			<PostalCode>".$zip."</PostalCode>
			<Quantity>".$quantity."</Quantity>
			<Storefront><StoreCategoryID>".$storecat."</StoreCategoryID></Storefront>
			 <ListingDesigner>
			  <LayoutID>10000</LayoutID>
			  <ThemeID>10</ThemeID>
			</ListingDesigner>
	 <ReturnPolicy>
      <ReturnsAcceptedOption>ReturnsAccepted</ReturnsAcceptedOption>
      <RefundOption>MoneyBack</RefundOption>
      <ReturnsWithinOption>Days_30</ReturnsWithinOption>
      <Description>If you are not satisfied, return for refund.</Description>
      <ShippingCostPaidByOption>Buyer</ShippingCostPaidByOption>
    </ReturnPolicy>";
	
	if (is_array($shipping))
	{
		$requestXmlBody .= "<ShippingDetails>";
		if (isset($shipping['domestic'])) foreach ($shipping['domestic'] as $k => $s) 
			{
				if ($s['ShippingService'] != '')
				{
					if (isset($s['FreeShipping']) && $s['FreeShipping'] == 'on') $fssnip = "<FreeShipping>".true."</FreeShipping>";
					else $fssnip = "";
					$requestXmlBody .= "<ShippingServiceOptions>
       				 <ShippingServicePriority>".$k."</ShippingServicePriority>
        			 <ShippingService>".$s['ShippingService']."</ShippingService>
      				 <ShippingServiceCost>".(float)$s['ShippingServiceCost']."</ShippingServiceCost>
					 <ShippingServiceAdditionalCost currencyID=\"USD\">".(float)$s['ShippingServiceAdditionalCost']."</ShippingServiceAdditionalCost>
					".$fssnip."					
      				</ShippingServiceOptions>";				
				}			
			 }
		
		if (isset($shipping['international'])) foreach ($shipping['international'] as $k => $s) 
			{
				if ($s['ShippingService'] != '')
				{
		$requestXmlBody .= "<InternationalShippingServiceOption>
		 <ShippingService>".$s['ShippingService']."</ShippingService>
        <ShippingServiceAdditionalCost currencyID=\"USD\">".(float)$s['ShippingServiceAdditionalCost']."</ShippingServiceAdditionalCost>
        <ShippingServiceCost currencyID=\"USD\">".(float)$s['ShippingServiceCost']."</ShippingServiceCost>
        <ShippingServicePriority>".$k."</ShippingServicePriority>
        <ShipToLocation>".$s['ShipToLocation']."</ShipToLocation>
  	    </InternationalShippingServiceOption>";				
				}			
			 }
			$requestXmlBody .= "</ShippingDetails>";
	}	

	$requestXmlBody .= "<Site>US</Site>
		  </Item>";
				$requestXmlBodyEND = '</AddItemRequest>';
				
					log_message('error', 'SUBMITTED '.(int)$id.' @'.CurrentTime());					
					
				$vrequestXmlBodySTART  = '<?xml version="1.0" encoding="utf-8" ?><VerifyAddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$vrequestXmlBodyEND = '</VerifyAddItemRequest>';
				
				//printcool (simplexml_load_string($vrequestXmlBodySTART.$requestXmlBody.$vrequestXmlBodyEND));
				
				$vsession = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'VerifyAddItem');
				
				//send the request and get response
				$vresponseXml = $vsession->sendHttpRequest($vrequestXmlBodySTART.$requestXmlBody.$vrequestXmlBodyEND);
				if(stristr($vresponseXml, 'HTTP 404') || $vresponseXml == '')
					die('<P>Error sending request');
				
				//Xml string is parsed and creates a DOM Document object
				//$vresponseDoc = new DomDocument();
				//$vresponseDoc->loadXML($vresponseXml);
				$xmlvresp = simplexml_load_string($vresponseXml);
					log_message('error', 'SUBMIT STEP 1 '.(int)$id.' @'.CurrentTime());					
				if (isset($xmlvresp->Errors))
				{	log_message('error', 'SUBMIT STEP 2 '.(int)$id.' @'.CurrentTime());					
					$estr = '';
					foreach ($xmlvresp->Errors as $e)
					{
						$estr .= '<div style="color:red;">ERROR:<BR>'.$e->ShortMessage.' | '.$e->LongMessage.' | '.$e->ErrorCode.' | '.$e->SeverityCode.' | '.$e->ErrorClassification.'</div><br />';
						
						log_message('error', 'SUBMIT STEP 3 '.(int)$id.' @'.CurrentTime());					
					}
					
					$this->_recordsubmiterror(array ('msg_title' => 'SUBMITTED ERRORS '.(int)$id.' @'.CurrentTime(), 'msg_body' => $estr.printcool($xmlvresp, TRUE).printcool(htmlentities($vrequestXmlBodySTART.$requestXmlBody.$vrequestXmlBodyEND), TRUE), 'msg_date' => CurrentTime()));
					
					
					
					foreach ($xmlvresp->Errors as $e)
					{log_message('error', 'SUBMIT STEP 4 '.(int)$id.' @'.CurrentTime());					
						if ((string)$e->SeverityCode !== 'Warning')
						{log_message('error', 'SUBMIT STEP 5 '.(int)$id.' @'.CurrentTime());					
							
							$this->_recordsubmiterror(array ('msg_title' => 'SUBMITTED ERRORS ECHO\'d '.(int)$id.' @'.CurrentTime(), 'msg_body' => 'SUBMITTED ERRORS ECHO\'d '.(int)$id, 'msg_date' => CurrentTime()));
							echo $estr;
							echo '<a href="javascript:history.back()">Back</a>';
							log_message('error', 'SUBMIT STEP 6 '.(int)$id.' @'.CurrentTime());					
							exit();
							log_message('error', 'SUBMIT STEP 7 '.(int)$id.' @'.CurrentTime());					
						}
					}

				}log_message('error', 'SUBMIT STEP 8 '.(int)$id.' @'.CurrentTime());					
			//	printcool ($xmlvresp);

//exit('TESTING ERROR FIXES. FEW MINS. PLEASE WAIT');
				//printcool($xmlvresp);
				//printcool($vrequestXmlBodySTART.$requestXmlBody.$vrequestXmlBodyEND);
				//exit();
				
				///if found error, echo with back
				
/////////////////////

				//Create a new eBay session with all details pulled in from included keys.php
				$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
				//send the request and get response
				$responseXml = $session->sendHttpRequest($requestXmlBodySTART.$requestXmlBody.$requestXmlBodyEND);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				//Xml string is parsed and creates a DOM Document object
				$responseDoc = new DomDocument();
				$responseDoc->loadXML($responseXml);
				
				$sxml = simplexml_load_string($responseXml);
				$aresponse = $this->_XML2Array($sxml);
				
				//get any error nodes
				$errors = $responseDoc->getElementsByTagName('Errors');
				log_message('error', 'SUBMIT STEP 9 '.(int)$id.' @'.CurrentTime());					
				//if there are error nodes
				
				if($errors->length > 0)
				{log_message('error', 'SUBMIT STEP 10 '.(int)$id.' @'.CurrentTime());					
					//echo '<P><B>eBay returned the following error(s):</B>';
					//display each error
					//Get error code, ShortMesaage and LongMessage
					$code     = $errors->item(0)->getElementsByTagName('ErrorCode');
					$shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
					$longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
					$severity = $errors->item(0)->getElementsByTagName('SeverityCode');
					//Display code and shortmessage
					log_message('error', 'SUBMIT STEP 11 '.(int)$id.' @'.CurrentTime());					
					//echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
					//if there is a long message (ie ErrorLevel=1), display it
										
					if(count($longMsg) > 0)
					{
						if ((string)$severity->item(0)->nodeValue !== 'Warning')
						{log_message('error', 'SUBMIT STEP 12 '.(int)$id.' @'.CurrentTime());					
							echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
							
							log_message('error', 'SUBMIT STEP 13 '.(int)$id.' @'.CurrentTime());					
							
							$this->_recordsubmiterror(array ('msg_title' => 'EBAY API ECHOed ERRORS '.(int)$id.' @'.CurrentTime(), 'msg_body' => 'EBAY API ECHOed ERRORS '.(int)$id.' - '.str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)), 'msg_date' => CurrentTime()));
							
							exit();
						}
						else
						{	log_message('error', 'SUBMIT STEP 14 '.(int)$id.' @'.CurrentTime());					
							$this->_recordsubmiterror(array ('msg_title' => 'EBAY API SUBMITTED ERRORS '.(int)$id.' @'.CurrentTime(), 'msg_body' => $longMsg->item(0)->nodeValue, 'msg_date' => CurrentTime()));							
						}
					}
			log_message('error', 'SUBMIT STEP 15 '.(int)$id.' @'.CurrentTime());					
				}
				
				 //else { //no errors
					log_message('error', 'SUBMIT STEP 16 '.(int)$id.' @'.CurrentTime());					
					//get results nodes
					$responses = $responseDoc->getElementsByTagName("AddItemResponse");

					foreach ($responses as $response) {
					  $acks = $response->getElementsByTagName("Ack");
/*				*/ 	  $ack   = $acks->item(0)->nodeValue;

					  $this->session->set_flashdata('success_msg', 'Result: '.$ack);
					  
					  /*$endTimes  = $response->getElementsByTagName("EndTime");
					  $endTime   = $endTimes->item(0)->nodeValue;
					  echo "endTime = $endTime <BR />\n";
					  */
					 
					  $itemIDs  = $response->getElementsByTagName("ItemID");
					  $itemID = 0;
/*				*/   if($itemIDs->length > 0) $itemID  = $itemIDs->item(0)->nodeValue;

					//if ($id == 11382)
					//{
						if (isset($this->resubmit) && (isset($longMsg) && count($longMsg) > 0))
						{
								echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
								log_message('error', 'RESUBMIT STEP 14.1 '.(int)$id.' @'.CurrentTime());					
								$this->_recordsubmiterror(array ('msg_title' => 'EBAY API RESUBMITTED ERRORS '.(int)$id.' @'.CurrentTime(), 'msg_body' => $longMsg->item(0)->nodeValue, 'msg_date' => CurrentTime()));
								exit();
						}
					
					//}
					  
/*				*/ 	  $linkBase = "http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=";
					 
					  $lfee = ''; $ifee = '';
					  $feeNodes = $responseDoc->getElementsByTagName('Fee');
					  foreach($feeNodes as $feeNode) {
						$feeNames = $feeNode->getElementsByTagName("Name");
						if ($feeNames->item(0)) {
							$feeName = $feeNames->item(0)->nodeValue;
							$fees = $feeNode->getElementsByTagName('Fee');  // get Fee amount nested in Fee
							$fee = $fees->item(0)->nodeValue;
							if ($fee > 0.0) {
								if ($feeName == 'ListingFee') {
								  $lfee = $fee; 
								} else {
								  $ifee = $fee;
								}      
							}  // if $fee > 0
						} // if feeName
					  } // foreach $feeNode
					
					} // foreach response
				if ($itemID > 0) $this->db->update('ebay', array('ebay_submitted' => CurrentTimeR(), 'ebay_id' => $itemID, 'Ack' => $ack, 'link' => $linkBase.$itemID, 'InsertionF' => $ifee, 'ListingF' => $lfee, 'ebayquantity' => $quantity, 'unsubmited' => 1), array('e_id' => (int)$id));	
				
				if (isset($this->resubmit) && $itemID > 0) $this->db->update('ebay', array('ebended' => NULL, 'submitlog' => 'Resubmited @ '.CurrentTime().' by '.$this->session->userdata['ownnames'].' - Previous submit: '.$item['ebay_submitted'].' ID '.$item['ebay_id'].'<br>'.$item['submitlog']), array('e_id' => (int)$id));
				
				log_message('error', 'SUBMIT SUCCESS RESPONSE '.(int)$id.' ['.$ack.'] @ '.CurrentTime());					
				//$this->_recordsubmiterror(array ('msg_title' => 'SUBMIT SUCCESS RESPONSE '.(int)$id.' @'.CurrentTime(), 'msg_body' => $ack, 'msg_date' => CurrentTime()));
				$this->session->set_flashdata('action', (int)$id);
				$this->session->set_flashdata('gotoebay', $linkBase.$itemID);
				
				if($errors->length == 0)
				{
					//Redirect ('Myebay#'.(int)$id);
					
					echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay#'.(int)$id.'\';",4000);
-->
</script>';

				}
				else
				{					
				
					
					if (isset($aresponse['Errors']['SeverityCode']))
					{
						echo '<strong>'.$aresponse['Errors']['SeverityCode'].':</strong>';
						unset($aresponse['Errors']['SeverityCode']);
					}
					if (isset($aresponse['Errors']['LongMessage']))
					{
						echo $aresponse['Errors']['LongMessage'].'<br><br>';
						unset($aresponse['Errors']['LongMessage']);
					}
					if (isset($aresponse['Errors']['ShortMessage'])) unset($aresponse['Errors']['ShortMessage']);
					if (isset($aresponse['Errors']['ErrorCode'])) unset($aresponse['Errors']['ErrorCode']);
					if (isset($aresponse['Errors']['ErrorParameters'])) unset($aresponse['Errors']['ErrorParameters']);
					if (isset($aresponse['Errors']['ErrorClassification'])) unset($aresponse['Errors']['ErrorClassification']);
					
					if (count($aresponse['Errors']) > 0)
					{						
						foreach ($aresponse['Errors'] as $d) 
						{
							log_message('error', 'SUBMITION ERRORS '.(int)$id.' ['.$ack.'] @ '.CurrentTime().' - '.$d['LongMessage']);
							
							echo '<span style="color:red; font-weight:strong;">'.$d['LongMessage'].'</span><br>';
							if (isset($d['ErrorParameters']['Value'])) 
							{
								echo $d['ErrorParameters']['Value'].'<Br><br>';
								
								GoMail(array ('msg_title' => 'Submit Error for '.(int)$id.' @ '.CurrentTime(), 'msg_body' => $d['LongMessage'].'<br><br>'. $d['ErrorParameters']['Value'], 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
								
								GoMail(array ('msg_title' => 'Submit Error for '.(int)$id.' @ '.CurrentTime(), 'msg_body' => $d['LongMessage'].'<br><br>'. $d['ErrorParameters']['Value'], 'msg_date' => CurrentTime()), 'mitko@rusev.me', $this->config->config['no_reply_email']);
								
							}
							else
							{
								GoMail(array ('msg_title' => 'Submit Error for '.(int)$id.' @ '.CurrentTime(), 'msg_body' => $d['LongMessage'], 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);	
								
								GoMail(array ('msg_title' => 'Submit Error for '.(int)$id.' @ '.CurrentTime(), 'msg_body' => $d['LongMessage'], 'msg_date' => CurrentTime()), 'mitko@rusev.me', $this->config->config['no_reply_email']);
							}
							
							
						}
					}
	
					echo '<br><br>YOU MAY CONTINUE THROUGH <a href="'.site_url().'Myebay#'.(int)$id.'">THIS LINK</a>.<Br><br> SOME ERROR MESSAGES ARE NOTICES AND THE LOCAL PROCESSING MAY HAVE BEEN COMPLETED.';
				}
				//Redirect ('Myebay#'.(int)$id);
				//} // if $errors->length > 0		
	}
}

function _ReviseEbay($id = 0)
{
	//echo $id;
	if ((int)$id > 0)
	{
		$this->session->set_flashdata('action', (int)$id);
		//redirect("Myebay");					
		set_time_limit(90); 
		
		$item = $this->Myebay_model->GetItem((int)$id);	
		$zip = $this->Myebay_model->GetSetting('EbayLocationZIP');
		$ppmail = $this->Myebay_model->GetSetting('EbayPayPalMAIL');
		
		if (!$item) exit('Item not found!');
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
	
				 ini_set('magic_quotes_gpc', false);  						
				 
				$this->mysmarty->assign('displays', $item);
				$listDescHtml = htmlspecialchars($this->mysmarty->fetch('myebay/myebay_sendsource.html'));
		
				$listingType     = $item['listingType'];
				$primaryCategory = $item['primaryCategory'];
				$itemTitle       = str_replace('&', '&amp;', $item['e_title']);
				if(get_magic_quotes_gpc()) $itemDescription = stripslashes($item['e_desc']);
				else $itemDescription = $item['e_desc'];				
				$listingDuration = $item['listingDuration'];
				$startPrice      = $item['startPrice'];
				$buyItNowPrice   = $item['buyItNowPrice'];
				$quantity        = $item['quantity'];
				$PaymentMethods  = $item['PaymentMethod'];
				$upc 			 = $item['upc'];
				$shipping		 = $item['shipping'];
				$storecat = $item['storeCatID'];
				
				if ($listingType == 'StoresFixedPrice')
				{
				  $buyItNowPrice = 0.0;   // don't have BuyItNow for SIF
				  $listingDuration = 'GTC';
				}
				
				if ($listingType == 'Dutch') $buyItNowPrice = $buyItNowPrice;   // don't have BuyItNow for Dutch
				$conditiondescription = '';
				if ($item['Condition'] != 1000) $conditiondescription = "<ConditionDescription>".$item['e_condition']."</ConditionDescription>";
				if (isset($PaymentMethods))
				{
					$paymentsnippet = '';
					if ($PaymentMethods != '') foreach ($PaymentMethods as $k => $v)
					{
						$paymentsnippet .= '<PaymentMethods>'.$k.'</PaymentMethods>';						
					}
					if (isset($PaymentMethods['PayPal'])) $paymentsnippet .= '<PayPalEmailAddress>'.$ppmail.'</PayPalEmailAddress>';
				}
				
				$verb = 'ReviseItem';
				
				if ($upc != '') $upcsnippet = '<ProductListingDetails><IncludePrefilledItemInformation>'.TRUE.'</IncludePrefilledItemInformation><UPC>'.$upc.'</UPC></ProductListingDetails>';
				else $upcsnippet = '';
						
				$imgsnippet = '<PictureDetails>';
				if ($item['e_img1'] != '') $imgsnippet .= '<PictureURL>'.Site_url().'ebay_images/'.$item['idpath'].'/Ebay_'.$item['e_img1'].'</PictureURL>';
				if ($item['e_img2'] != '') $imgsnippet .= '<PictureURL>'.Site_url().'ebay_images/'.$item['idpath'].'/Ebay_'.$item['e_img2'].'</PictureURL>';
				if ($item['e_img3'] != '') $imgsnippet .= '<PictureURL>'.Site_url().'ebay_images/'.$item['idpath'].'/Ebay_'.$item['e_img3'].'</PictureURL>';
				if ($item['e_img4'] != '') $imgsnippet .= '<PictureURL>'.Site_url().'ebay_images/'.$item['idpath'].'/Ebay_'.$item['e_img4'].'</PictureURL>';
				
				$imgsnippet .= '</PictureDetails>';
				$requestXmlBodySTART  = '<?xml version="1.0" encoding="utf-8"?>
<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';				
				$requestXmlBody = "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				//<VerifyOnly>".TRUE."</VerifyOnly>
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				
				$requestXmlBody .= "<Item>
    <Description>".$listDescHtml."</Description>
    <DescriptionReviseMode>Replace</DescriptionReviseMode>
    <ItemID>".$item['ebay_id']."</ItemID>
    <ListingDuration>".$item['listingDuration']."</ListingDuration>   
    
    <ConditionID>".$item['Condition']."</ConditionID>
	".$conditiondescription."		    
	<StartPrice>".$startPrice."</StartPrice>
	".$upcsnippet."	
	<Quantity>".$quantity."</Quantity>
    <StartPrice>".$startPrice."</StartPrice>
    <Storefront><StoreCategoryID>".$storecat."</StoreCategoryID></Storefront>
    <Title>".$itemTitle."</Title>
	".$paymentsnippet."
	".$imgsnippet;
	
	if (is_array($shipping))
	{
		$requestXmlBody .= "<ShippingDetails>";
		if (isset($shipping['domestic'])) foreach ($shipping['domestic'] as $k => $s) 
			{
				if ($s['ShippingService'] != '')
				{
					if (isset($s['FreeShipping']) && $s['FreeShipping'] == 'on') $fssnip = "<FreeShipping>".true."</FreeShipping>";
					else $fssnip = "";
					$requestXmlBody .= "<ShippingServiceOptions>
       				 <ShippingServicePriority>".$k."</ShippingServicePriority>
        			 <ShippingService>".$s['ShippingService']."</ShippingService>
      				 <ShippingServiceCost>".(float)$s['ShippingServiceCost']."</ShippingServiceCost>
					 <ShippingServiceAdditionalCost currencyID=\"USD\">".(float)$s['ShippingServiceAdditionalCost']."</ShippingServiceAdditionalCost>
					".$fssnip."					
      				</ShippingServiceOptions>";				
				}			
			 }
		
		if (isset($shipping['international'])) foreach ($shipping['international'] as $k => $s) 
			{
				if ($s['ShippingService'] != '')
				{
		$requestXmlBody .= "<InternationalShippingServiceOption>
		 <ShippingService>".$s['ShippingService']."</ShippingService>
        <ShippingServiceAdditionalCost currencyID=\"USD\">".(float)$s['ShippingServiceAdditionalCost']."</ShippingServiceAdditionalCost>
        <ShippingServiceCost currencyID=\"USD\">".(float)$s['ShippingServiceCost']."</ShippingServiceCost>
        <ShippingServicePriority>".$k."</ShippingServicePriority>
        <ShipToLocation>".$s['ShipToLocation']."</ShipToLocation>
  	    </InternationalShippingServiceOption>";				
				}			
			 }
		$requestXmlBody .= "</ShippingDetails>";
	}
			
				$requestXmlBody .= "</Item>";
				$requestXmlBodyEND = '</ReviseItemRequest>';
				
				
				//GoMail(array ('msg_title' => 'REVISED '.(int)$id.' @'.CurrentTime(), 'msg_body' => $requestXmlBody, 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
								
				$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
								
				$responseXml = $session->sendHttpRequest($requestXmlBodySTART.$requestXmlBody.$requestXmlBodyEND);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				$responseDoc = new DomDocument();
				$responseDoc->loadXML($responseXml);
								
				$errors = $responseDoc->getElementsByTagName('Errors');
								
				if($errors->length > 0)
				{
					echo '<P><B>eBay returned the following error(s):</B>';
					$code     = $errors->item(0)->getElementsByTagName('ErrorCode');
					$shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
					$longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
					echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
					if(count($longMsg) > 0) echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));	
					
					$this->_recordsubmiterror(array ('msg_title' => 'REVISE ERRORS '.(int)$id.' @'.CurrentTime(), 'msg_body' => printcool(str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)), TRUE), 'msg_date' => CurrentTime()));
					echo '<a href="javascript:history.back()">Back</a>';		
				} 
				// else { //no errors
					
					//get results nodes
					$responses = $responseDoc->getElementsByTagName("ReviseItemResponse");
					if ($responses) foreach ($responses as $response) 
					{
					  $acks = $response->getElementsByTagName("Ack");
 	  $ack   = $acks->item(0)->nodeValue;				  
					   $this->session->set_flashdata('success_msg', 'Result: '.$ack);
					} // foreach response
				
				$this->db->update('ebay', array('ebayquantity' => $quantity, 'submitlog' => 'Revised @ '.CurrentTime().' by '.$this->session->userdata['ownnames'].'<br>'.$item['submitlog']), array('e_id' => (int)$id));
				$this->session->set_flashdata('action', (int)$id);
				$this->session->set_flashdata('gotoebay', "http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=".$item['ebay_id']);
				
				if($errors->length == 0) Redirect ('Myebay#'.(int)$id);
				else echo '<br><br>YOU MAY CONTINUE THROUGH <a href="'.site_url().'Myebay#'.(int)$id.'">THIS LINK</a>.<Br><br> SOME ERROR MESSAGES ARE NOTICES AND THE LOCAL PROCESSING MAY HAVE BEEN COMPLETED.';

				//}
	} 		

}







function _ReviseEbayDescription($id = 0, $page = false, $save = false)
{
	if ((int)$id > 0)
	{
		$this->session->set_flashdata('action', (int)$id);
		//redirect("Myebay");					
		set_time_limit(90); 
		
		$item = $this->Myebay_model->GetItem((int)$id);	
		
		if (!$item) { echo 'Item not found!'; exit(); }
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
	
				 ini_set('magic_quotes_gpc', false);  						
				 
				$this->mysmarty->assign('displays', $item);
				$listDescHtml = htmlspecialchars($this->mysmarty->fetch('myebay/myebay_sendsource.html'));
		
				$requestXmlBodySTART  = '<?xml version="1.0" encoding="utf-8"?>
<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';				
				$requestXmlBody = "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				//<VerifyOnly>".TRUE."</VerifyOnly>
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				
				$requestXmlBody .= "<Item>
    <Description>".$listDescHtml."</Description>
    <DescriptionReviseMode>Replace</DescriptionReviseMode>
    <ItemID>".$item['ebay_id']."</ItemID>";
	
			
				$requestXmlBody .= "</Item>";
				$requestXmlBodyEND = '</ReviseItemRequest>';
							
				$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'ReviseItem');
								
				$responseXml = $session->sendHttpRequest($requestXmlBodySTART.$requestXmlBody.$requestXmlBodyEND);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				$responseDoc = new DomDocument();
				$responseDoc->loadXML($responseXml);
								
				$errors = $responseDoc->getElementsByTagName('Errors');
								
				if($errors->length > 0)
				{
					echo '<P><B>eBay returned the following error(s):</B>';
					$code     = $errors->item(0)->getElementsByTagName('ErrorCode');
					$shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
					$longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
					echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
					if(count($longMsg) > 0) echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));	
					
					$this->_recordsubmiterror(array ('msg_title' => 'REVISE DESCRIPTION ERRORS '.(int)$id.' @'.CurrentTime(), 'msg_body' => printcool(str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)), TRUE), 'msg_date' => CurrentTime()));
					//if ($save) $this->db->update('ebay', array('autorev' => -1, 'autorevtxt' => str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)).' @ '.CurrentTime()), array('e_id' => (int)$id));		
				} 
				 else 
				{ //no errors
					
					
					
					//get results nodes
					$responses = $responseDoc->getElementsByTagName("ReviseItemResponse");
					$txtresp = '';
					foreach ($responses as $response) 
					{
					  $acks = $response->getElementsByTagName("Ack");
 	  $ack   = $acks->item(0)->nodeValue;				  
					  $txtresp .= 'Result: '.$ack.'<br>';
					} // foreach response
					
					//GoMail(array ('msg_title' => 'REVISED DESCRIPTION '.(int)$id.' @'.CurrentTime(), 'msg_body' => $txtresp, 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
					
					//if ($save) $this->db->update('ebay', array('autorev' => 1, 'autorevtxt' => '@ '.CurrentTime()), array('e_id' => (int)$id));
				}

				
				/*
				
				$this->db->select("e_id");	
				$this->db->where("autorev", 0);
				$this->db->where("ebay_id > ", 0);
				$this->db->order_by("e_id", "DESC");	
				$this->query = $this->db->get('ebay');
				if ($this->query->num_rows() > 0) { $goto = $this->query->row_array();
echo $goto['e_id'];
				echo '<script type="text/JavaScript">
<!--
setTimeout("location.href = \'http://www.la-tronics.com/Myebay/ReviseEbayDescription/'.$goto['e_id'].'\';",30000);
-->
</script>'



}
		;*/		
				
	} 
	
	if ($page) {
		//Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);
	echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/ListItems/'.(int)$page.'#'.(int)$id.'\';",4000);
-->
</script>';

	}

}

function _UpdateQuantityAndBCN($id = 0, $page = '')
{

	$this->UpdateQuantity((int)$id, (int)$page, TRUE);
	$this->skipreq = true;
	$this->UpdateBCN((int)$id, (int)$page, TRUE);
	$this->ReviseEbayDescription((int)$id, false, false);
	$this->EbayInventoryUpdate((int)$id, false);
	
	echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/ListItems/'.(int)$page.'#'.(int)$id.'\';",4000);
-->
</script>';
	
	//Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);

}
function _UpdateQuantity($id = 0, $page = '', $noredirect = FALSE)
{

if ((int)$id != 0 && $page != '' && isset($_POST['quantity']))
{
	$this->db->update('ebay', array('quantity' => (int)$_POST['quantity']), array('e_id' => (int)$id));
	//		$this->Myebay_model->EbayFromID((int)$id);
	$this->_logaction('UpdateQuantity', 'Q',array('quantity' => $_POST['oldquantity']), array('quantity' => $_POST['quantity']), $id, (int)$_POST['itemid'], 0);	
	
	$this->session->set_flashdata('action', (int)$id);
}

if (!$noredirect) Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);

}

function _UpdateBCN($id = 0, $page = '', $noredirect = FALSE)
{ 
if ((int)$id != 0 && $page != '' && isset($_POST['bcn']))
{
	$this->db->update('ebay', array('e_part' => commasep(commadesep((string)$_POST['bcn'])), 'e_qpart' => $this->_RealCount(commasep(commadesep((string)$_POST['bcn'])))), array('e_id' => (int)$id));
	
	$this->_logaction('UpdateBCN', 'B',array('BCN' => commasep(commadesep($_POST['oldbcn']))), array('BCN' => commasep(commadesep((string)$_POST['bcn']))), $id, (int)$_POST['itemid'], 0);	
	$this->_logaction('UpdateBCN', 'B',array('BCN Count' => $this->_RealCount(commasep(commadesep((string)$_POST['oldbcn'])))), array('BCN Count' => $this->_RealCount(commasep(commadesep((string)$_POST['bcn'])))), (int)$id, $_POST['itemid'], 0);	
	
	$this->session->set_flashdata('action', (int)$id);
}

if (!$noredirect) Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);

}
/*function _test1()
{

		$submitbcns = '';
		$submitbcnscount = $this->_RealCount($submitbcns);			
		$submitbcns = explode(',', $submitbcns);
		
		$oldtransactionbcn = '';
		$oldtransactionbcn = explode(',', rtrim(',', $oldtransactionbcn));	
		
		$listingbcns = '';
		$listingbcnsoldcount = $this->_RealCount($listingbcns);
		$listingbcnsold = $listingbcns;		
		$listingbcns = explode(',', $listingbcns);
		
		if ($listingbcnsoldcount > 0)
		{
			foreach ($listingbcns as $k => $v)
			{
				foreach ($submitbcns as $rk => $rv)
				{
					if (trim($rv) == trim($v)) unset($listingbcns[$k]);
					//LOG MATCHED
				}
			}
		}
		
		if ($submitbcnscount > 0)
		{
			foreach ($oldtransactionbcn as $k => $v)
			{
				foreach ($submitbcns as $rk => $rv)
				{
					if (trim($rv) == trim($v)) unset($oldtransactionbcn[$k]);
					//LOG RETURNED
				}
			}
			
			foreach ($oldtransactionbcn as $v) $listingbcns[] = $v;
		}
		
		$oldtransactionbcn = implode(',', array_map('trim', $oldtransactionbcn));
		$submitbcns = implode(',', array_map('trim', $submitbcns));
		$listingbcns = implode(',', array_map('trim', $listingbcns));	
		$listingbcncount = $this->_RealCount($listingbcns);
		
		//DIMITRI - We must keep what's been submited, even if it's not matched in the listing. There will be cases this will be needed.
		$this->db->update('ebay_transactions', array('sn' => $submitbcns, 'mark' => 1), array('rec' => (int)$rec));
		$this->_logaction('TransactionView', 'B',array('Transaction BCN' => $oldtransactionbcn), array('Transaction BCN' => $submitbcns), $res['e_id'], $res['ebay_id'], $rec);

		$this->db->update('ebay', array('e_part' => $listingbcns, 'e_qpart' => $listingbcncount), array('e_id' => $res['e_id']));
		$this->_logaction('TransactionView', 'B',array('BCN' => $listingbcnsold), array('BCN' => $listingbcns), $res['e_id'], $res['ebay_id'], $rec);
		$this->_logaction('TransactionView', 'B',array('BCN Count' => $listingbcnsoldcount), array('BCN Count' => $listingbcncount), $res['e_id'], $res['ebay_id'], $rec);
				
		//DIMITRI 22.07.2014 - KHIM, i've commented your code because some of thing here shouldn't be done. Unique - let the administrators see if there are duplicates. No piece of data, even duplicate should be automatically removed. Sorting aswell. There's a log of changes in which it's preferable for visual purposes to have everything like it originally was, with just the difference in data. At some point the admins will have a hard time tracking changes if we reorder the bcns, or if were trying to find a missing bcn by hand. 
		
		//if($bcns != ''){
		//	$bcns = explode(",", $bcns);
		//	array_push($bcns, $_POST['oldbcn']);
		//	$bcns = array_filter(array_map('trim', $bcns));
		//	//$bcns = array_diff($bcns, array(trim($_POST['oldbcn'], " ")));
		//	$bcns = array_unique($bcns);
		//	sort($bcns);
		//	$bcns = implode(", ", $bcns);
		//	$bcns = $bcns . ", ";
		//}else{
		//	$bcns = $_POST['oldbcn'];
		//}
	
}*/
function _NoBCNReq($rec = 0)
{
	if ((int)$rec != 0)
	{	
	
	$this->db->select('e.e_id, e.ebay_id, t.admin, t.revs');
	$this->db->where('t.itemid = e.ebay_id');
	$this->db->where('t.rec', (int)$rec);
	$q = $this->db->get('ebay as e, ebay_transactions as t');
	if ($q->num_rows() > 0) $res = $q->row_array();
	
	else { echo 'Transaction data not found. Contact administrator'; exit(); }
	
		$res['revs']++;										 
		if ($res['admin'] == '') $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'];
		else $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'].', '.$res['admin'];
		//DIMITRI - We must keep what's been submited, even if it's not matched in the listing. There will be cases this will be needed.
		$this->db->update('ebay_transactions', array('mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('rec' => (int)$rec));
	
			$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Updated</span> to <span style="color:#FF9900;">"Does not require BCN"</span>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => ''));
	
	}
	
	$this->session->set_flashdata('action', (int)$rec);
	
	$sortstring = $this->session->userdata['sortstring'];
	
	if ($sortstring != '') echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/SortOrders/'.$sortstring.'#'.(int)$rec.'\';",4000);
-->
</script>'; 
	else echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/GetOrders/#'.(int)$rec.'\';",4000);
-->
</script>';
	
}
function _TransactionBCNUpdate($rec = 0, $isarray = false)
{

// DIMITER CHANGES HERE, IMPLODING POSTED BCNS BEFORE PROCESSING CONTINUES, USING $isarray TO DEFIN 17.7.2014 -> 30.8.2014

if ((int)$rec != 0 && isset($_POST['bcn']))
{
	$this->db->select('qty');
	$this->db->where('rec', (int)$rec);
	$t = $this->db->get('ebay_transactions');
	if ($t->num_rows() > 0) $tr = $t->row_array();
	else { echo 'Transaction data not found. Contact administrator'; exit(); }
	
	
	/////////////
	if ($isarray) $submitbcns = commadesep(implode(',', $_POST['bcn']));    
	else $submitbcns = commadesep(trim((string)$_POST['bcn']));                   
	$submitbcnscount = $this->_RealCount($submitbcns);			
	$submitbcns = explode(',', $submitbcns);
		
	$oldtransactionbcn = commadesep(trim((string)$_POST['oldbcn']));
	$oldtransactionbcn = explode(',', $oldtransactionbcn);
	//////////
	if ($submitbcnscount == 0) $this->db->insert('admin_history', array ('msg_type' => 1, 
												  'msg_title' => '<span style="color:blue;">Transaction Submitted BCN Value</span> <span style="color:red;">IS EMPTY</span>', 
												  'msg_body' => '', 
												  'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1												  
												  ));
	
	if ($submitbcnscount != $tr['qty']) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction Submitted BCN Value</span> <span style="color:red;">DOES NOT MATCH QUANTITY ('.$submitbcnscount.'/'.$tr['qty'].')</span> on record.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));

	$this->db->select('e.e_id, e.e_part, e.ebay_id, t.admin, t.revs');
	$this->db->where('t.itemid = e.ebay_id');
	$this->db->where('t.rec', (int)$rec);
	$q = $this->db->get('ebay as e, ebay_transactions as t');
	if ($q->num_rows() == 0) 
	{
		$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Update</span> found <span style="color:red;">no ItemID match</span> in eBay listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));
	}
	else
	{	
		$res = $q->row_array();
		 
		$listingbcns = commadesep($res['e_part']);
		$listingbcnsoldcount = $this->_RealCount($listingbcns);
		$listingbcnsold = $listingbcns;		
		$listingbcns = explode(',', $listingbcns);
				
		$matched = array();
		
		
		if ($listingbcnsoldcount > 0)
		{
			foreach ($listingbcns as $k => $v)
			{
				foreach ($submitbcns as $rk => $rv)
				{
					if (trim($rv) == trim($v)) 
					{
						$matched[] = trim($listingbcns[$k]);
						//LOG MATCHED
						unset($listingbcns[$k]);
					}
					
				}
			}
			
				
		}
		
		if ($listingbcnsoldcount == 0 || (count($matched) == 0))
		{
			$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Update</span> found <span style="color:red;">no available BCNs</span> in listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));			
		}	
		
		if ($submitbcnscount > 0)
		{
			foreach ($oldtransactionbcn as $k => $v)
			{
				foreach ($submitbcns as $rk => $rv)
				{
					if (trim($rv) == trim($v)) unset($oldtransactionbcn[$k]);
				}
			}
			
			if (count($oldtransactionbcn) > 0)
				{
					foreach ($oldtransactionbcn as $v)
						{
							$listingbcns[] = $v;
						}
				}
		}
		
		sort($matched);
		$matched = implode(', ', $matched);

		$oldtransactionbcn = rtrim(implode(', ', array_map('trim', $oldtransactionbcn)), ',');
		sort($submitbcns);
		$submitbcns = rtrim(implode(', ', array_map('trim', $submitbcns)), ',');
		sort($listingbcns);
		$listingbcns = rtrim(implode(', ', array_map('trim', $listingbcns)), ',');	
		$listingbcncount = $this->_RealCount($listingbcns);
		
		$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Updated</span> to <span style="color:#FF9900;">"'.$submitbcns.'"</span> (Matched: <span style="color:#FF9900;">'.$matched.'</span>) [Old value: <span style="color:#FF9900;">'.$oldtransactionbcn.'</span>]', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => ''));
												 
		$res['revs']++;										 
		if ($res['admin'] == '') $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'];
		else $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'].', '.$res['admin'];
		//DIMITRI - We must keep what's been submited, even if it's not matched in the listing. There will be cases this will be needed.
		$this->db->update('ebay_transactions', array('sn' => commasep(commadesep($submitbcns)), 'mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('rec' => (int)$rec));
		$this->_logaction('TransactionView', 'B', array('Transaction BCN' => commasep(commadesep($oldtransactionbcn))), array('Transaction BCN' => commasep(commadesep($submitbcns))), $res['e_id'], $res['ebay_id'], $rec);

		$this->db->update('ebay', array('e_part' => commasep(commadesep($listingbcns)), 'e_qpart' => $listingbcncount), array('e_id' => $res['e_id']));
		$this->_logaction('TransactionView', 'B',array('BCN' => commasep(commadesep($listingbcnsold))), array('BCN' => commasep(commadesep($listingbcns))), $res['e_id'], $res['ebay_id'], $rec);
		$this->_logaction('TransactionView', 'B',array('BCN Count' => $listingbcnsoldcount), array('BCN Count' => $listingbcncount), $res['e_id'], $res['ebay_id'], $rec);
				
		//DIMITRI 22.07.2014 - KHIM, i've commented your code because some of thing here shouldn't be done. Unique - let the administrators see if there are duplicates. No piece of data, even duplicate should be automatically removed. Sorting aswell. There's a log of changes in which it's preferable for visual purposes to have everything like it originally was, with just the difference in data. At some point the admins will have a hard time tracking changes if we reorder the bcns, or if were trying to find a missing bcn by hand. 
		
		//if($bcns != ''){
		//	$bcns = explode(",", $bcns);
		//	array_push($bcns, $_POST['oldbcn']);
		//	$bcns = array_filter(array_map('trim', $bcns));
		//	//$bcns = array_diff($bcns, array(trim($_POST['oldbcn'], " ")));
		//	$bcns = array_unique($bcns);
		//	sort($bcns);
		//	$bcns = implode(", ", $bcns);
		//	$bcns = $bcns . ", ";
		//}else{
		//	$bcns = $_POST['oldbcn'];
		//}
	
		//echo "bcnsRecycled: " . $bcns . "\n\n\n";	  	
		//$this->db->update('ebay', array('e_part' => $bcns, 'e_qpart' => $this->_RealCount($bcns)), array('e_id' => $res['e_id']));	
		
		 //$this->_logaction('TransactionView', 'B',array('BCN' => $_POST['oldbcn']), array('BCN' => $bcns), $res['e_id'], $res['ebay_id'], $rec);
		 //$this->_logaction('TransactionView', 'B',array('BCN Count' => $this->_RealCount($_POST['oldbcn'])), array('BCN Count' => $this->_RealCount($bcns)), $res['e_id'], $res['ebay_id'], $rec);

		if ($listingbcncount > 1) $this->ReviseEbayDescription($res['e_id']);
	}	

	$this->session->set_flashdata('action', (int)$rec);
	
	$sortstring = $this->session->userdata['sortstring'];
	
	if ($sortstring != '') echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/SortOrders/'.$sortstring.'#'.(int)$rec.'\';",4000);
-->
</script>';

	else echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/GetOrders/#'.(int)$rec.'\';",4000);
-->
</script>';
	
	
	//Redirect('Myebay/GetEbayTransactions/#'.(int)$rec);

}

}



function _OrderBCNUpdate($rec = 0, $ebid = 0, $isarray = false)
{
	if ((int)$rec != 0 && $ebid != 0 && isset($_POST['bcn']))
	{
		$this->db->select('order');
		$this->db->where('oid', (int)$rec);
		$t = $this->db->get('orders');
		if ($t->num_rows() == 0) { echo 'Order data not found. Contact administrator'; exit(); }
		else $tr = $t->row_array();
		
		$matchproduct = false;
		$tr['order'] = unserialize($tr['order']);
		foreach ($tr['order'] as $k => $v)
		{
			if ($k == $ebid)
			{				
				$matchproduct = true;
				
					if ($isarray) $submitbcns = commadesep(implode(',', $_POST['bcn']));    
					else $submitbcns = commadesep(trim((string)$_POST['bcn']));                   
					$submitbcnscount = $this->_RealCount($submitbcns);			
					$submitbcns = explode(',', $submitbcns);
						
					$oldtransactionbcn = commadesep(trim((string)$_POST['oldbcn']));
					$oldtransactionbcn = explode(',', $oldtransactionbcn);
				
					if ($submitbcnscount == 0) $this->db->insert('admin_history', array ('msg_type' => 1, 
																  'msg_title' => '<span style="color:blue;">Order Submitted BCN Value</span> <span style="color:red;">IS EMPTY</span>', 
																  'msg_body' => '', 
																  'msg_date' => CurrentTime(),
																  'e_id' => 0,
																  'itemid' => 0,
																  'trec' => $rec,
																  'opr' => $ebid,
																  'admin' => $this->session->userdata['ownnames'],
																  'sev' => 1												  
																  ));
					
					if ($submitbcnscount != $v['quantity']) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Order Submitted BCN Value</span> <span style="color:red;">DOES NOT MATCH QUANTITY ('.$submitbcnscount.'/'.$v['quantity'].')</span> on record.', 'msg_body' => '', 'msg_date' => CurrentTime(),
																  'e_id' => 0,
																  'itemid' => 0,
																  'trec' => $rec,
																  'opr' => $ebid,
																  'admin' => $this->session->userdata['ownnames'],
																  'sev' => 1));
				
					$this->db->select('e_id, e_part, ebay_id');
					$this->db->where('e_id', (int)$ebid);
					$q = $this->db->get('ebay');
					if ($q->num_rows() == 0) 
					{
						$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Order BCN Update</span> found <span style="color:red;">no ItemID match</span> in eBay listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
																  'e_id' => 0,
																  'itemid' => 0,
																  'trec' => $rec,
																  'opr' => $ebid,
																  'admin' => $this->session->userdata['ownnames'],
																  'sev' => 1));
					}
					else
					{	
						$res = $q->row_array();
						 
						$listingbcns = commadesep($res['e_part']);
						$listingbcnsoldcount = $this->_RealCount($listingbcns);
						$listingbcnsold = $listingbcns;		
						$listingbcns = explode(',', $listingbcns);
								
						$matched = array();
						
						
						if ($listingbcnsoldcount > 0)
						{
							foreach ($listingbcns as $lk => $lv)
							{
								foreach ($submitbcns as $rk => $rv)
								{
									if (trim($rv) == trim($lv)) 
									{
										$matched[] = trim($listingbcns[$lk]);
										unset($listingbcns[$lk]);
									}
									
								}
							}
							
								
						}
						
						if ($listingbcnsoldcount == 0 || (count($matched) == 0))
						{
							$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Order BCN Update</span> found <span style="color:red;">no available BCNs</span> in listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
																  'e_id' => $res['e_id'],
																  'itemid' => $res['ebay_id'],
																  'trec' => $rec,
																  'opr' => $ebid,
																  'admin' => $this->session->userdata['ownnames'],
																  'sev' => 1));			
						}	
						
						if ($submitbcnscount > 0)
						{
							foreach ($oldtransactionbcn as $ok => $ov)
							{
								foreach ($submitbcns as $rk => $rv)
								{
									if (trim($rv) == trim($ov)) unset($oldtransactionbcn[$ok]);
								}
							}
							
							if (count($oldtransactionbcn) > 0)
								{
									foreach ($oldtransactionbcn as $otv)
										{
											$listingbcns[] = $otv;
										}
								}
						}
						
						sort($matched);
						$matched = implode(', ', $matched);
				
						$oldtransactionbcn = rtrim(implode(', ', array_map('trim', $oldtransactionbcn)), ',');
						sort($submitbcns);
						$submitbcns = rtrim(implode(', ', array_map('trim', $submitbcns)), ',');
						sort($listingbcns);
						$listingbcns = rtrim(implode(', ', array_map('trim', $listingbcns)), ',');	
						$listingbcncount = $this->_RealCount($listingbcns);
						
						$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Order BCN Updated</span> to <span style="color:#FF9900;">"'.$submitbcns.'"</span> (Matched: <span style="color:#FF9900;">'.$matched.'</span>) [Old value: <span style="color:#FF9900;">'.$oldtransactionbcn.'</span>]', 'msg_body' => '', 'msg_date' => CurrentTime(),
																  'e_id' => $res['e_id'],
																  'itemid' => $res['ebay_id'],
																  'trec' => $rec,
																  'opr' => $ebid,
																  'admin' => $this->session->userdata['ownnames'],
																  'sev' => ''));									 
						$v['revs']++;										 
						if ($v['admin'] == '') $v['admin'] = '('.$v['revs'].') '.$this->session->userdata['ownnames'];
						else $v['admin'] = '('.$v['revs'].') '.$this->session->userdata['ownnames'].', '.$v['admin'];
				
				
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				
				$tr['order'][$k]['sn'] = commasep(commadesep($submitbcns));
				$tr['order'][$k]['mark'] = 1;
				$tr['order'][$k]['admin'] = $v['admin'];
				$tr['order'][$k]['revs'] = $v['revs'];
				 
				$tr['order'] = serialize($tr['order']);
				$this->db->update('orders', array('order' => $tr['order'], 'mark' => 1), array('oid' => (int)$rec));
						
				
				
				
				
						$this->_logaction('TransactionView', 'B', array('Order BCN' => commasep(commadesep($oldtransactionbcn))), array('Order BCN' => commasep(commadesep($submitbcns))), $res['e_id'], $res['ebay_id'], $rec, $k);
				
				
				
				
				$this->db->update('ebay', array('e_part' => commasep(commadesep($listingbcns)), 'e_qpart' => $listingbcncount), array('e_id' => $res['e_id']));
						
						//function _logaction($location = '', $type = 'M', $datafrom = '', $datato = '', $eid = '', $itemid = '', $transid = '', $key = '')					
						
						
						$this->_logaction('TransactionView', 'B', array('BCN' => commasep(commadesep($listingbcnsold))), array('BCN' => commasep(commadesep($listingbcns))), $res['e_id'], $res['ebay_id'], $rec, $k);
						$this->_logaction('TransactionView', 'B', array('BCN Count' => $listingbcnsoldcount), array('BCN Count' => $listingbcncount), $res['e_id'], $res['ebay_id'], $rec, $k);
						
						
						
						
				if ($listingbcncount > 1) $this->ReviseEbayDescription($res['e_id']);
				else $this->_EndeBayListing($res['ebay_id'], $res['e_id'], (int)$rec);
				}	
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
					$this->session->set_flashdata('action', (int)$rec);
					
					$sortstring = $this->session->userdata['sortstring'];
					
					if ($sortstring != '') echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
				<!--
				setTimeout("location.href = \''.Site_url().'Myebay/SortOrders/'.$sortstring.'#'.(int)$rec.'\';",4000);
				-->
				</script>';
				
					else echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
				<!--
				setTimeout("location.href = \''.Site_url().'Myebay/GetOrders/#'.(int)$rec.'\';",4000);
				-->
				</script>';
					
					
					//Redirect('Myebay/GetEbayTransactions/#'.(int)$rec);
				
				
				
				
				
				
				
				
				
				
				
				
				
				
	
		}
		if ($matchproduct == false)  { echo 'Order data not found. Contact administrator'; exit(); }
	
	
	
	
	
	
	
		
	
	}
/*
if ((int)$rec != 0 && isset($_POST['bcn']))
{
	$this->db->select('qty');
	$this->db->where('rec', (int)$rec);
	$t = $this->db->get('ebay_transactions');
	if ($t->num_rows() > 0) $tr = $t->row_array();
	else { echo 'Transaction data not found. Contact administrator'; exit(); }
	
	

	if ($isarray) $submitbcns = commadesep(implode(',', $_POST['bcn']));    
	else $submitbcns = commadeseptrim((string)$_POST['bcn']);                     
	$submitbcnscount = $this->_RealCount($submitbcns);			
	$submitbcns = explode(',', $submitbcns);
		
	$oldtransactionbcn = commadesep(trim((string)$_POST['oldbcn']));
	$oldtransactionbcn = explode(',', $oldtransactionbcn);

	if ($submitbcnscount == 0) $this->db->insert('admin_history', array ('msg_type' => 1, 
												  'msg_title' => '<span style="color:blue;">Transaction Submitted BCN Value</span> <span style="color:red;">IS EMPTY</span>', 
												  'msg_body' => '', 
												  'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1												  
												  ));
	
	if ($submitbcnscount != $tr['qty']) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction Submitted BCN Value</span> <span style="color:red;">DOES NOT MATCH QUANTITY ('.$submitbcnscount.'/'.$tr['qty'].')</span> on record.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));

	$this->db->select('e.e_id, e.e_part, e.ebay_id, t.admin, t.revs');
	$this->db->where('t.itemid = e.ebay_id');
	$this->db->where('t.rec', (int)$rec);
	$q = $this->db->get('ebay as e, ebay_transactions as t');
	if ($q->num_rows() == 0) 
	{
		$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Update</span> found <span style="color:red;">no ItemID match</span> in eBay listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));
	}
	else
	{	
		$res = $q->row_array();
		 
		$listingbcns = commadesep($res['e_part']);
		$listingbcnsoldcount = $this->_RealCount($listingbcns);
		$listingbcnsold = $listingbcns;		
		$listingbcns = explode(',', $listingbcns);
				
		$matched = array();
		
		
		if ($listingbcnsoldcount > 0)
		{
			foreach ($listingbcns as $k => $v)
			{
				foreach ($submitbcns as $rk => $rv)
				{
					if (trim($rv) == trim($v)) 
					{
						$matched[] = trim($listingbcns[$k]);
						unset($listingbcns[$k]);
					}
					
				}
			}
			
				
		}
		
		if ($listingbcnsoldcount == 0 || (count($matched) == 0))
		{
			$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Update</span> found <span style="color:red;">no available BCNs</span> in listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));			
		}	
		
		if ($submitbcnscount > 0)
		{
			foreach ($oldtransactionbcn as $k => $v)
			{
				foreach ($submitbcns as $rk => $rv)
				{
					if (trim($rv) == trim($v)) unset($oldtransactionbcn[$k]);
				}
			}
			
			if (count($oldtransactionbcn) > 0)
				{
					foreach ($oldtransactionbcn as $v)
						{
							$listingbcns[] = $v;
						}
				}
		}
		
		sort($matched);
		$matched = implode(', ', $matched);
		
		$oldtransactionbcn = rtrim(implode(', ', array_map('trim', $oldtransactionbcn)), ',');
		sort($submitbcns);
		$submitbcns = rtrim(implode(', ', array_map('trim', $submitbcns)), ',');
		sort($listingbcns);
		$listingbcns = rtrim(implode(', ', array_map('trim', $listingbcns)), ',');	
		$listingbcncount = $this->_RealCount($listingbcns);
		
		$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Updated</span> to <span style="color:#FF9900;">"'.$submitbcns.'"</span> (Matched: <span style="color:#FF9900;">'.$matched.'</span>) [Old value: <span style="color:#FF9900;">'.$oldtransactionbcn.'</span>]', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => ''));
												 
		$res['revs']++;										 
		if ($res['admin'] == '') $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'];
		else $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'].', '.$res['admin'];

		$this->db->update('ebay_transactions', array('sn' => commasep(commadesep($submitbcns)), 'mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('rec' => (int)$rec));
		$this->_logaction('TransactionView', 'B', array('Transaction BCN' => commasep(commadesep($oldtransactionbcn))), array('Transaction BCN' => commasep(commadesep($submitbcns))), $res['e_id'], $res['ebay_id'], $rec);

		$this->db->update('ebay', array('e_part' => commasep(commadesep($listingbcns)), 'e_qpart' => commasep(commadesep($listingbcncount))), array('e_id' => $res['e_id']));
		$this->_logaction('TransactionView', 'B',array('BCN' => commasep(commadesep($listingbcnsold))), array('BCN' => commasep(commadesep($listingbcns))), $res['e_id'], $res['ebay_id'], $rec);
		$this->_logaction('TransactionView', 'B',array('BCN Count' => $listingbcnsoldcount), array('BCN Count' => $listingbcncount), $res['e_id'], $res['ebay_id'], $rec);
		
		if ($listingbcncount > 1) $this->ReviseEbayDescription($res['e_id']);
	}	

	$this->session->set_flashdata('action', (int)$rec);
	
	$sortstring = $this->session->userdata['sortstring'];
	if ($sortstring != '') echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/SortOrders/'.$sortstring.'#'.(int)$rec.'\';",4000);
-->
</script>';
	else echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/GetOrders/#'.(int)$rec.'\';",4000);
-->
</script>';
	*/

}

}



function _TransactionBCNUpdateOldVersion($rec = 0, $isarray = false)
{
exit();
// DIMITER CHANGES HERE, IMPLODING POSTED BCNS BEFORE PROCESSING CONTINUES, USING $isarray TO DEFIN 17.7.2014
	
if ((int)$rec != 0 && isset($_POST['bcn']))
{
	$this->db->select('qty');
	$this->db->where('rec', (int)$rec);
	$t = $this->db->get('ebay as e, ebay_transactions as t');
	if ($t->num_rows() > 0) $tr = $t->row_array();
	else { echo 'Transaction data not found. Contact administrator'; exit(); }
	
	$oldbcn = (string)$_POST['oldbcn'];                     /// <---
	if ($isarray) $remove = implode(',', $_POST['bcn']);    /// <---
	else $remove = (string)$_POST['bcn'];                   /// <---
	$removecount = $this->_RealCount($remove);
	
	if ($removecount == 0) $this->db->insert('admin_history', array ('msg_type' => 1, 
												  'msg_title' => '<span style="color:blue;">Transaction Submitted BCN Value</span> <span style="color:red;">IS EMPTY</span>', 
												  'msg_body' => '', 
												  'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1												  
												  ));
	
	if ($removecount != $tr['qty']) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction Submitted BCN Value</span> <span style="color:red;">DOES NOT MATCH QUANTITY ('.$removecount.'/'.$tr['qty'].')</span> on record.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));

	$this->db->select('e.e_id, e.e_part, e.ebay_id');
	$this->db->where('t.itemid = e.ebay_id');
	$this->db->where('t.rec', (int)$rec);
	$q = $this->db->get('ebay as e, ebay_transactions as t');
	if ($q->num_rows() == 0) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Update</span> found <span style="color:red;">no ItemID match</span> in eBay listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));
		
	else
	{	
		$res = $q->row_array();
		 
		$remove = explode(',', rtrim($remove,','));
		$bcns = rtrim($res['e_part'],',');
		$bcncount = $this->_RealCount($bcns);			
		$bcns = explode(',', $bcns);
		$matched = array();				
		if ($bcncount > 0)
		{
			foreach ($bcns as $k => $v)
			{
				foreach ($remove as $rk => $rv)
				{
					if (trim($rv) == trim($v))
					{
						unset($bcns[$k]);
						$matched[$rk] = trim($rv);
					}
				}
			}		
		$matched = implode(',', $matched);		
		}
		else 
		{
			$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Update</span> found <span style="color:red;">no available BCNs</span> in listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));						
			$matched = '<span style="color:red">NONE</span>';			
		}		
		$remove = implode(',', $remove);
		$bcns = implode(',', $bcns);	
		if ($matched == '') $matched = '<span style="color:red">NONE</span>';
		$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Updated</span> to <span style="color:#FF9900;">"'.$remove.'"</span> (Matched: <span style="color:#FF9900;">'.$matched.'</span>)', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => ''));
		/* put the old bcn back into the available bucket */	

		/** 
		 * beautify the BCN list
		 * add the old bcn back to the available list 
		 ***/	
		if($bcns != ''){
			$bcns = explode(",", $bcns);
			array_push($bcns, $_POST['oldbcn']);
			$bcns = array_filter(array_map('trim', $bcns));
			//$bcns = array_diff($bcns, array(trim($_POST['oldbcn'], " ")));
			$bcns = array_unique($bcns);
			sort($bcns);
			$bcns = implode(", ", $bcns);
			$bcns = $bcns . ", ";
		}else{
			$bcns = $_POST['oldbcn'];
		}
	
		//echo "bcnsRecycled: " . $bcns . "\n\n\n";	  	
		$this->db->update('ebay', array('e_part' => $bcns, 'e_qpart' => $this->_RealCount($bcns)), array('e_id' => $res['e_id']));	
		
		 $this->_logaction('TransactionView', 'B',array('BCN' => $_POST['oldbcn']), array('BCN' => $bcns), $res['e_id'], $res['ebay_id'], $rec);
		 $this->_logaction('TransactionView', 'B',array('BCN Count' => $this->_RealCount($_POST['oldbcn'])), array('BCN Count' => $this->_RealCount($bcns)), $res['e_id'], $res['ebay_id'], $rec);

		$this->ReviseEbayDescription($res['e_id'], false, false);
	}	

	

	
	//$updateBcn = 
	
	$this->db->update('ebay_transactions', array('sn' => $remove, 'mark' => 1), array('rec' => (int)$rec));
	$this->_logaction('TransactionView', 'B',array('Transaction BCN' => $oldbcn), array('Transaction BCN' => $remove), $res['e_id'], $res['ebay_id'], $rec);
	$this->session->set_flashdata('action', (int)$rec);
}

//Redirect('Myebay/GetEbayTransactions/#'.(int)$rec);

	echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/GetEbayTransactions/#'.(int)$rec.'\';",4000);
-->
</script>';
}

function _RealCount($array)
{

	if ($array != '') return count(explode(',', $array));
	else return 0;
}

function _EbayInventoryUpdate($id = 0, $page = false)
{	
	if ((int)$id > 0)
	{
		$this->session->set_flashdata('action', (int)$id);
		//redirect("Myebay");					
		set_time_limit(90); 
		
		$item = $this->Myebay_model->GetItem((int)$id);	
		
		if (!$item) { echo 'Item not found!'; exit(); }
		

		require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
	
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
		$requestXmlBody .= '<ItemID>'.(int)$item['ebay_id'].'</ItemID></GetItemRequest>';
		$verb = 'GetItem';
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
		$xml = simplexml_load_string($responseXml);
		if ((string)$xml->Item->ItemID == '') { echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>'; exit();}
		
 		$oldebayvalue = (string)$xml->Item->Quantity;
					
				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8"?>
<ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">';				
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>
						";				
				$requestXmlBody .= "<InventoryStatus>
			    <ItemID>".$item['ebay_id']."</ItemID>
			    <Quantity>".$item['quantity']."</Quantity>
				</InventoryStatus>
				</ReviseInventoryStatusRequest>";				
				
				//GoMail(array ('msg_title' => 'INVENTORY UPDATED '.(int)$id.' @'.CurrentTime(), 'msg_body' => $requestXmlBody, 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
								
				$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'ReviseInventoryStatus');
								
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				$responseDoc = new DomDocument();
				$responseDoc->loadXML($responseXml);
								
				$errors = $responseDoc->getElementsByTagName('Errors');
								
				if($errors->length > 0)
				{
					echo '<P><B>eBay returned the following error(s):</B>';
					$code     = $errors->item(0)->getElementsByTagName('ErrorCode');
					$shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
					$longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
					echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
					if(count($longMsg) > 0) echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));			
				} 
				 else { //no errors
					
					//get results nodes
					$responses = $responseDoc->getElementsByTagName("ReviseInventoryStatusResponse");
					foreach ($responses as $response) 
					{
					  $acks = $response->getElementsByTagName("Ack");
/*				*/ 	  $ack   = $acks->item(0)->nodeValue;				  
					   $this->session->set_flashdata('success_msg', 'Result: '.$ack);
					} // foreach response

				$this->db->update('ebay', array('ebayquantity' => $item['quantity']), array('e_id' => (int)$id));	
				
			
				$linkBase = "http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=";
				 
				$this->session->set_flashdata('action', (int)$id);
				$this->session->set_flashdata('gotoebay', $linkBase.$item['ebay_id']);

				$this->_logaction('EbayInventoryUpdate', 'Q',array('Quantity @ eBay' => $oldebayvalue), array('Quantity @ eBay' => $item['quantity']), $id, $item['ebay_id'], 0);
				$this->_logaction('EbayInventoryUpdate', 'Q',array('Local eBay Quantity' => $item['ebayquantity']), array('Local eBay Quantity' => $item['quantity']), $id, $item['ebay_id'], 0);
				
				//Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);
				
				if ($page) echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/ListItems/'.(int)$page.'#'.(int)$id.'\';",4000);
-->
</script>';
			}
	} 		

}
function _RefreshLocalEbayValue($itemid = 0, $id = 0, $page = '')
{		
	if ((int)$id > 0 && $itemid > 0)
	{
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
		$requestXmlBody .= '<ItemID>'.(int)$itemid.'</ItemID></GetItemRequest>';
		$verb = 'GetItem';
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
		$xml = simplexml_load_string($responseXml);
		if ((string)$xml->Item->ItemID == '') { echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>'; exit();}
		
 				$this->db->select('e_id, ebayquantity, quantity');
				$this->db->where('e_id', (int)$id);
				$this->db->where('ebay_id', (string)$xml->Item->ItemID);
				$query = $this->db->get('ebay');
				if ($query->num_rows() > 0) 
				{
					
					$ebr = $query->row_array();					
										
					$this->db->update('ebay', array('ebayquantity' => ((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold), 'quantity' => ((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold)), array('e_id' => $ebr['e_id']));			
					
					$this->_logaction('RefreshLocalEbayValue', 'Q',array('Local eBay Quantity' => $ebr['ebayquantity']), array('Local eBay Quantity' => ((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold)), $ebr['e_id'], (int)$itemid, 0);
					
					$this->_logaction('RefreshLocalEbayValue', 'Q',array('Local Quantity' => $ebr['quantity']), array('Local Quantity' => ((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold)), $ebr['e_id'], (int)$itemid, 0);
										
					$hmsg = array ('msg_title' => 'Local Listing Ebay & Quantity Refreshed to '.((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold), 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => (int)$id,
												  'itemid' => (int)$itemid,
												  'trec' => 0,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => '');
					
					$this->db->insert('admin_history', $hmsg); 
					
					//GoMail($hmsg, '365@1websolutions.net', $this->config->config['no_reply_email']);
					
					$this->session->set_flashdata('success_msg', 'Item '.(int)$id.' Local Ebay & Quantity Refreshed to '.((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold));
					$this->session->set_flashdata('action', (int)$id);
					Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);
				}
				else echo 'ERROR. LOCAL ID DOES NOT MATCH eBAY ITEMID';
		
	}
}

function _CheckEbayValue($itemid = 0, $id = 0, $actionlog = 0)
{		
	if ((int)$id > 0 && $itemid > 0 && $actionlog > 0)
	{
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
		$requestXmlBody .= '<ItemID>'.(int)$itemid.'</ItemID></GetItemRequest>';
		$verb = 'GetItem';
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
		$xml = simplexml_load_string($responseXml);
		if ((string)$xml->Item->ItemID == '') { echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>'; exit();}
		
 				$this->db->select('ebayquantity, ebay_id');
				$this->db->where('e_id', (int)$id);
				$this->db->where('ebay_id', (string)$xml->Item->ItemID);
				$query = $this->db->get('ebay');
				if ($query->num_rows() > 0) 
				{
					
					$ebr = $query->row_array();					
					
					if ($ebr['ebayquantity'] == (string)$xml->Item->Quantity) echo '<span style="color: green;">LOCAL VALUE IS CORRECT! - Local eBay Value: '.$ebr['ebayquantity'].' - @ eBay Value: '.(string)$xml->Item->Quantity;
					else 
					{
						echo '<span style="color: red;">LOCAL VALUE IS INCORRECT! - Local eBay Value: '.$ebr['ebayquantity'].' - @ eBay Value: '.(string)$xml->Item->Quantity.'<br><br>Record now updated';
						
						$this->db->update('ebay', array('ebayquantity' => (string)$xml->Item->Quantity), array('e_id' => (int)$id));
						$this->db->update('ebay_actionlog', array('datato' => (string)$xml->Item->Quantity), array('al_id' => (int)$actionlog, 'e_id' => (int)$id, 'ebay_id' => (int)$itemid));
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
function _GetOnlineListings($page = 1, $cat = false)
{
		set_time_limit(1500); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		$dates = array('from' => date('Y-m-d H:i:s', strtotime("-30 days")), 'to' => date("Y-m-d H:i:s"));
		if ($cat) $requestXmlBody .= '<CategoryID>'.(int)$cat.'</CategoryID>';
		$requestXmlBody .= '
		<GranularityLevel>Coarse</GranularityLevel>
		<StartTimeFrom>'.$dates['from'].'</StartTimeFrom>
		<StartTimeTo>'.$dates['to'].'</StartTimeTo>
		<Pagination>
		<EntriesPerPage>200</EntriesPerPage>
		<PageNumber>'.(int)$page.'</PageNumber>
		</Pagination>
		</GetSellerListRequest>';
		
		$verb = 'GetSellerList';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);

		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$xml = simplexml_load_string($responseXml);
		
		$pagearray = false;

		for ($counter = 1; $counter <= $xml->PaginationResult->TotalNumberOfPages; $counter++) $pagearray[] = $counter;
		
		$this->mysmarty->assign('page', (int)$page);
		$this->mysmarty->assign('pages', $pagearray);
		$this->mysmarty->assign('total', $xml->PaginationResult->TotalNumberOfEntries);
				//printcool($xml->ItemArray->Item);
				
		$this->mysmarty->assign('cat', $cat);
		
		$this->mysmarty->assign('dates', $dates);
		//printcool ($xml->ItemArray->Item);
		
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetStoreRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";		
		$requestXmlBody .= '<CategoryStructureOnly>TRUE</CategoryStructureOnly>
		<UserID>'.$ebayuserid.'</UserID></GetStoreRequest>';
		
		$verb = 'GetStore';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$sxml = simplexml_load_string($responseXml);
		$sc = array();
		if (isset($sxml->Store->CustomCategories->CustomCategory))
		{
			foreach ($sxml->Store->CustomCategories->CustomCategory as $s)
			{
				$a = (array)$s;
				$sc[$a['CategoryID']] = $a['Name'];	
			}			
		}

		$this->mysmarty->assign('store', $sc);	

		$k = 1;
		foreach($xml->ItemArray->Item as $i)
		{
			$l[$k] = $i;
			$l[$k]->storecat = $sc[(int)$i->Storefront->StoreCategoryID];
			$k++;	
		}
		$this->mysmarty->assign('list', $l);
		$this->mysmarty->view('myebay/myebay_onlinelistings.html');	
}
// KHIM change query to return different result set
function _SortOrders($type = '')
{
	
	$this->sortstring = $type;
	$this->sorttype = false;	
	switch ($type)
	{		
		case 'Ebay':	
		$this->sorttype = 2;
		break;
		case 'Site':	
		$this->sorttype = 3;
		break;
		case 'Paid':	
		$this->sorttype = 4;
		break;
		case 'Processed':	
		$this->sorttype = 5;
		break;
		case 'NoProcessed':	
		$this->sorttype = 6;
		break;
		case 'Asc':	
		$this->sorttype = 7;
		break;
		case 'NoAsc':	
		$this->sorttype = 8;
		break;
		case 'NoPaid':	
		$this->sorttype = 9;
		break;
		default: 
		$this->sorttype = 1;
		$this->sortstring = 'All';		
		
		
		
	}
	
	
	$this->mysmarty->assign('sorttype', $this->sorttype);
	$this->mysmarty->assign('sortstring', $this->sortstring);

	$this->session->set_userdata('sorttype', $this->sorttype);	
	$this->session->set_userdata('sortstring', $this->sortstring);	
	
	$this->GetOrders();
	
}
function _OrderDatesClean()
{
	$this->session->unset_userdata('dfrom');
	$this->session->unset_userdata('dto');
	Redirect('Myebay/GetOrders');	
}
function _GetOrders($highlight = '')
{
		$this->mysmarty->assign('cal', TRUE);
		$tdf =46800; 
		$ofrom = mktime()+$tdf;
		$oto = (mktime()+$tdf)-2592000;
		$dfrom = date('m/j/Y');
		$dto = date('m/j/Y', strtotime("-30 days"));
		
		$this->mysmarty->assign('d1from', date('m/j/Y', strtotime("-30 days")));	
		$this->mysmarty->assign('d1to', date('m/j/Y', strtotime("-60 days")));	
		
		$this->mysmarty->assign('d2from', date('m/j/Y', strtotime("-60 days")));	
		$this->mysmarty->assign('d2to', date('m/j/Y', strtotime("-90 days")));	
		
		$this->mysmarty->assign('d3from', date('m/j/Y', strtotime("-90 days")));	
		$this->mysmarty->assign('d3to', date('m/j/Y', strtotime("-120 days")));	
		
		$sesfrom = $this->session->userdata('dfrom');
		$sesto = $this->session->userdata('dto');
		
		if ($sesfrom || $sesto) $this->mysmarty->assign('dateclean', TRUE);	 
		
		if (isset($_POST['ofrom']) || $sesfrom)
		{
			if (isset($_POST['ofrom']))
			{
			$dfrom = trim($_POST['ofrom']);
			$this->session->set_userdata('dfrom', $dfrom);	
			}
			else $dfrom = $sesfrom;
			$postfrom = explode('/', $dfrom);
			$ofrom = mktime(23, 59, 59, $postfrom[0], $postfrom[1], $postfrom[2])+$tdf;
			$this->mysmarty->assign('dateclean', TRUE);	
		}
		if (isset($_POST['oto']) || $sesto)	
		{
			if (isset($_POST['oto']))	
			{
			$dto = trim($_POST['oto']);
			$this->session->set_userdata('dto', $dto);	
			}
			else $dto = $sesto;
			
			$postto = explode('/', $dto);			
			$oto = mktime(0, 0, 0, $postto[0], $postto[1], $postto[2])+$tdf;
			$this->mysmarty->assign('dateclean', TRUE);	
		}
	
		$this->mysmarty->assign('dfrom', $dfrom);	
		$this->mysmarty->assign('dto', $dto);	
		
		if (!isset($this->sorttype))
		{
			$this->session->set_userdata('sortstring', FALSE);
			 $this->sorttype = 0;
		}
		$oldtrestentry = mktime()+$tdf;
		$oldorestentry = mktime()+$tdf;
		
		
		$list = array();
		$orders = array();

		$this->load->model('Myorders_model'); 
		if ($this->sorttype != 3)
		{
		
		$this->mysmarty->assign('area', 'Transactions');
		$list = array();

		$this->db->select("distinct t.*, e_part, e_id, e_title, idpath, e_img1", false);
		$this->db->where('t.mkdt <= ', $ofrom);
		$this->db->where('t.mkdt >= ', $oto);

		if (isset($this->sorttype))
		{			
				switch ($this->sorttype)
						{		
							
							case 4:	
							$this->db->where('t.paidtime !=', '');
							
							break;
							case 9:	
							$this->db->where('t.paidtime', '');							
							break;
							case 5:	
							$this->db->where('t.mark !=', 0);
							break;
							case 6:	
							$this->db->where('t.mark', 0);
							break;
							case 7:	
							$this->db->where('t.cascupd !=', 0);
							break;
							case 8:	
							$this->db->where('t.cascupd', 0);
							break;
											
						}	
			
		}
		//$this->db->limit(500);
		
		$this->db->order_by("rec", "DESC");
		$this->db->join('ebay e', 't.itemid = e.ebay_id', 'LEFT');
		$q = $this->db->get('ebay_transactions t');
		
		$mkdtdupcheck = 0;
		
		if ($q->num_rows() > 0) 
		{
			foreach ($q->result_array() as $k=>$v)
			{
				if ((int)$v['mkdt'] == (int)$mkdtdupcheck) $v['mkdt'] = $v['mkdt']-1;
				$mkdtdupcheck = $v['mkdt'];
				if ($v['mkdt'] < $oldtrestentry) $oldtrestentry = $v['mkdt'];
				if (strlen($v['paydata']) > 10) 
				{				
					$v['paydata'] = unserialize($v['paydata']);
					if (isset($v['paydata'])) unset($v['paydata']['PaidTime']);
				}
				else $v['paydata'] = false;
				$list[$v['mkdt'].'E'] = $v;
			}
		}
		
		}
		if ($this->sorttype != 2 && $this->sorttype != 7 && $this->sorttype != 8)
		{
		
		
		if (isset($this->sorttype))
		{			
				switch ($this->sorttype)
						{							
							case 4:	
							$this->db->where('complete', 1);							
							break;
							case 9:	
							$this->db->where('complete !=', 1);							
							break;
							case 5:	
							$this->db->where('mark !=', 0);
							break;
							case 6:	
							$this->db->where('mark', 0);
							break;											
						}	
									
		}
		
		$this->db->where('submittime <= ', $ofrom);
		$this->db->where('submittime >= ', $oto);
		
		$this->db->order_by("submittime", "DESC");
		
		$this->query = $this->db->get('orders');
		$orders = array();
		if ($this->query->num_rows() > 0) 
			{
				
			$nowmk = (int)mktime(0, 0, 0, date('m'), date('d'), date('Y'));			
			$os = array();
			foreach ($this->query->result_array() as $k => $v)	
				{
				if ($v['status'] != '' && $v['status'] != ' ') {
										$v['status'] = unserialize($v['status']);
										//$v['origstatus'] = $v['status'][0];										
										$v['status'] = end($v['status']);
										}
				
				
				if (strlen($v['order']) > 9) 
				{ 
					$v['order'] = unserialize($v['order']); 
					if (is_array($v['order']))
					foreach ($v['order'] as $k => $ov) 
					{
						$os[$ov['e_id']] = $ov['quantity']; 
						if (!isset($ov['sn'])) $v['order'][$k]['sn'] = '';
						if (!isset($ov['admin'])) $v['order'][$k]['admin'] = '';
					}
				}

				if (strlen($v['CheckoutStatus']) > 9) $v['CheckoutStatus'] = unserialize($v['CheckoutStatus']);
		
				$v['mktime'] = explode(' ', $v['time']);
				$v['mktime'] = explode('-', $v['mktime'][0]);			
				if (isset($v['mktime'][1]) && isset($v['mktime'][2]) && isset($v['mktime'][0])) $v['mktime'] = (int)mktime(0, 0, 0, $v['mktime'][1], $v['mktime'][2], $v['mktime'][0]);
				else $v['mktime'] = false;				
				
				if ($v['submittime'] < $oldorestentry) $oldorestentry = $v['submittime'];
				
				$orders[$v['submittime'].'O'] = $v;	
				
				}
			}
		}
		$olist = array_merge($list, $orders);
		krsort($olist);

		if (count($os) > 0)
		{
		$this->db->select("e_part, e_qpart, e_id, quantity, ebayquantity, ebay_id");
		$st = 0;

		foreach($os as $k => $v)
		{
			if ($st == 0) { $this->db->where('e_id', $k); $st++; }
			else $this->db->or_where('e_id', $k);
		}
				
		$q = $this->db->get('ebay');
		$ebl = false;
			if ($q->num_rows() > 0) 
			{
				foreach ($q->result_array() as $k=>$v)
				{
					$ebl[$v['e_id']] = $v;
				}		
			}
		}
		$this->mysmarty->assign('ebl', $ebl);
		//printcool ($olist);
		//break;
		
		if (isset($this->sortstring)) $this->mysmarty->assign('sortstring', $this->sortstring);
		if (isset($this->sortpage)) $this->mysmarty->assign('sortpage', $this->sortpage);
		$this->mysmarty->assign('list', $olist);
		$this->mysmarty->assign('highlight', $highlight);	
		
		
		if ($oldtrestentry > $oldorestentry) $this->session->set_userdata('next', $oldtrestentry);
		else $this->session->set_userdata('next', $oldorestentry);
			
		$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());	
		
		$this->mysmarty->view('myebay/myebay_orders.html');
}
function _CompleteStatus($id = 0)
	{
		
		if ((int)$id == 0) Redirect("Myebay/SortOrders/Site#".(int)$id);		
		$this->load->model('Myorders_model');
		$this->order = $this->Myorders_model->GetItem((int)$id);
		if (!$this->order) Redirect("Myebay/SortOrders/Site#".(int)$id);
				
		if ($this->order['payproc'] == 1)
		{
			if ($this->order['complete'] == 0 || $this->order['complete'] > 4)
				{
				$this->Myorders_model->CompleteStatus((int)$this->order['oid']);
				}
		}
		elseif ($this->order['payproc'] == 2)
		{
			if ($this->order['complete'] == 0 || $this->order['complete'] == 5 || $this->order['complete'] == 6 || $this->order['complete'] == 0 || $this->order['complete'] > 12)
				{
				$this->Myorders_model->CompleteStatus((int)$this->order['oid']);
				}
		}
		
		$this->admindata['msg_date'] = CurrentTime();			
							$this->admindata['msg_title'] = 'Order Manual Complete';
							$this->admindata['msg_body'] = 'Order '.$id.' Completed Manualy by Admin '.$this->session->userdata['name'].' @ '.FlipDateMail($this->admindata['msg_date']);
							$this->load->model('Login_model');
							$this->Login_model->InsertHistoryData($this->admindata);
							//$this->mailid = 9;
							GoMail ($this->admindata);
					
					
		Redirect("Myebay/SortOrders/Site#".(int)$id);
		
	}
function _OrderComm($oid)
{
		$this->db->select('oid, email, comm');
		$this->db->where('oid', (int)$oid);
		$query = $this->db->get('orders');
		if ($query->num_rows() > 0)
			{
				$o = $query->row_array();	
				if (strlen($o['comm']) > 15) $o['comm'] = unserialize($o['comm']);
				else $o['comm'] = FALSE;
				$this->mysmarty->assign('oid', $o['oid']);
				$this->mysmarty->assign('comm', $o['comm']);
				
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				$this->editor = new FCKeditor('msg');				
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				$this->editor->Value = '';
				$this->mysmarty->assign('editormsg', $this->editor->CreateHtml());
	
				if (isset($_POST['msg']))
				{
					$title = $this->input->post('titlemsg', TRUE);
					$body = $this->input->post('msg', TRUE);
					if (strlen($title) < 10) $title = 'Regarding your Order No.'.$oid.' at '.$this->config->config['sitename'];
					$o['comm'][] = array('titlemsg' => $title, 'msg' => $body, 'time' => CurrentTime(), 'admin' => $this->session->userdata['name']);
					if (count($o['comm'] > 0)) $o['comm'] = serialize($o['comm']);
					else $o['comm'] = NULL;
					$this->db->update('orders', array('comm' => $o['comm']), array('oid' => (int)$o['oid']));
					$this->session->set_flashdata('success_msg', 'Message sent and saved');
					GoMail (array('msg_title' => $title, 'msg_body' => $body), $o['email']);
					Redirect('Myebay/OrderComm/'.$oid);
				}
				//printcool ($o);
				$this->mysmarty->view('myebay/myebay_comm.html');
			}
		else exit('Invalid Order');		
}
function _EndeBayListing($itemid = '', $eid = '', $oid = '')
{

	
	//http://developer.ebay.com/Devzone/xml/docs/Reference/ebay/EndFixedPriceItem.html
	
	if ((int)$itemid > 0 && (int)$eid > 0 && (int)$oid > 0)
		{
			

		set_time_limit(120);
		ini_set('mysql.connect_timeout', 120);
		ini_set('max_execution_time', 120);  
		ini_set('default_socket_timeout', 120); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'EndFixedPriceItem';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>
<EndFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				$requestXmlBody .= '<EndingReason>NotAvailable</EndingReason>';
				$requestXmlBody .= '<ItemID>'.(int)$itemid.'</ItemID>';
				$requestXmlBody .= '</EndFixedPriceItemRequest>';

				//send the request and get response
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				printcool($responseXml);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
			
				 $xml = $this->_XML2Array(simplexml_load_string($responseXml));
				//printcool ($xml);
				if (isset($xml['EndTime']))
				{
					 $ended = CleanBadDate((string)$xml['EndTime']);
					
					 $this->db->update('ebay', array('ebended' => $ended, 'endedreason' => 'Ended from order '.(int)$oid), array('ebay_id' => (int)$itemid, 'e_id' => (int)$eid));
					 
					 $updatestring = 'Ebay Listing <a href="'.Site_url().'Myebay/Search/'.(int)$eid.'" target="_blank" style="color: #419aff; font-size:10px;"><img src="'.Site_url().'images/admin/b_search.png" class="linkicon" />'.(int)$eid.'</a> - ItemID: <a href="http://www.ebay.com/itm/'.$itemid.'" target="_blank" style="color: #419aff; font-size:10px;"><img src="'.Site_url().'images/admin/b_search.png" class="linkicon"/>'.$itemid.'</a> ended from order '.(int)$oid.' at '.$ended.'<br>';
		
					$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => 'Order Ended Listing', 'msg_body' => $updatestring, 'msg_date' => CurrentTime(),
							  'e_id' => (int)$eid,
							  'itemid' => (int)$itemid,
							  'trec' => 0,
							  'admin' => $this->session->userdata['ownnames'],
							  'sev' => 0));
				}		
		}
}
function _GetEbayTransactions($highlight = '')
{
		if ($highlight == '') Redirect('Myebay/GetOrders/');
		else Redirect('Myebay/GetOrders/#'.(int)$highlight);
		$this->mysmarty->assign('area', 'Transactions');	
//DIMITRI - 16.7.2014, added more fields to select (e_id, e_title, idpath, e_img1):
		$this->db->select("distinct t.*, e_part, e_id, e_title, idpath, e_img1", false);

		$this->db->where('t.mkdt >= ', mktime()-2592000);
		//$this->db->limit(500);
		$this->db->order_by("rec", "DESC");
		$this->db->join('ebay e', 't.itemid = e.ebay_id', 'LEFT');
		$q = $this->db->get('ebay_transactions t');
		$list = false;
		if ($q->num_rows() > 0) 
		{
			foreach ($q->result_array() as $k=>$v)
			{
				if (strlen($v['paydata']) > 10) 
				{				
					$v['paydata'] = unserialize($v['paydata']);
					if (isset($v['paydata'])) unset($v['paydata']['PaidTime']);
				}
				else $v['paydata'] = false;
				$list[$k] = $v;
			}
		}
		$this->mysmarty->assign('list', $list);	

		$this->mysmarty->assign('highlight', $highlight);	

		$this->mysmarty->view('myebay/myebay_transactions.html');
}
function _UpdateCurrentTransaction($rec = 0)
{
	if ((int)$rec > 0)
		{
		set_time_limit(120);
		ini_set('mysql.connect_timeout', 120);
		ini_set('max_execution_time', 120);  
		ini_set('default_socket_timeout', 120); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetSellingManagerSaleRecord';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
		$this->db->select('rec, et_id, itemid, transid, asc, paydata, paidtime');	
		$this->db->where('rec', (int)$rec);
		$q = $this->db->get('ebay_transactions');
		
		if ($q->num_rows() > 0) 
		{
			$t = $q->row_array();

				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetSellingManagerSaleRecordRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
				$requestXmlBody .= "<ItemID>$t[itemid]</ItemID>";
				$requestXmlBody .= "<TransactionID>$t[transid]</TransactionID>";
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				$requestXmlBody .=  '</GetSellingManagerSaleRecordRequest>';			

				//send the request and get response
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				$xml = simplexml_load_string($responseXml);
				$item = $xml->SellingManagerSoldOrder;			
				if ($item)
				{
				if ((string)$item->ActualShippingCost != $t['asc'])
				{
					
					$this->db->update('ebay_transactions', array('asc' => (string)$item->ActualShippingCost, 'cascupd' => 2), array('et_id' => $t['et_id']));
					$this->_logaction('Transactions', 'B', array('ActShipCost' => $t['asc']), array('ActShipCost' => (string)$item->ActualShippingCost), 0, $t['itemid'], $t['rec']);		
				}				 

			 $ar = $this->_XML2Array($item->OrderStatus);
			 $ar = $ar['OrderStatus'];

			 if (isset($ar['PaidTime']) && (CleanBadDate((string)$ar['PaidTime']) != $t['paidtime']) && (CleanBadDate((string)$ar['PaidTime']) != ''))
				{
					
					$this->db->update('ebay_transactions', array('paidtime' => CleanBadDate((string)$ar['PaidTime'])), array('et_id' => $t['et_id']));
					$this->_logaction('Transactions', 'B', array('PaidTime' => $t['paidtime']), array('PaidTime' => CleanBadDate((string)$ar['PaidTime'])), 0, $t['itemid'], $t['rec']);		
				}	
			 unset($ar['paidtime']);
			 $pd = serialize($ar);
			  if ($item && ($pd != $t['paydata']))
				{					
					$this->db->update('ebay_transactions', array('paydata' => $pd), array('et_id' => $t['et_id']));					
				}
				}
        
			
		}	
	}	
	
	
	$sortstring = $this->session->userdata['sortstring'];
	if ($sortstring != '') Redirect('Myebay/SortOrders/'.$sortstring.'#'.(int)$rec);
	else Redirect('Myebay/GetOrders/#'.(int)$rec);
	//Redirect('Myebay/GetEbayTransactions/#'.(int)$rec);		
}

function _XML2Array($parent)
{
    $array = array();

    foreach ($parent as $name => $element) {
        ($node = & $array[$name])
            && (1 === count($node) ? $node = array($node) : 1)
            && $node = & $node[];

        $node = $element->count() ? $this->_XML2Array($element) : trim($element);
    }

    return $array;
}
function _GetEbayTransactionsLive()
{
		$this->mysmarty->assign('area', 'Transactions');	
		set_time_limit(1500); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		$dates = array('from' => date('Y-m-d H:i:s', strtotime("4 Days")), 'to' => date('Y-m-d H:i:s', strtotime("6	Days")));
		$requestXmlBody .= '
		 <IncludeCodiceFiscale>'.TRUE.'</IncludeCodiceFiscale>
		 <IncludeContainingOrder>'.TRUE.'</IncludeContainingOrder>
		 <IncludeFinalValueFee>'.TRUE.'</IncludeFinalValueFee>
		<ModTimeFrom>'.$dates['from'].'</ModTimeFrom>
 		<ModTimeTo>'.$dates['to'].'</ModTimeTo>
  		<NumberOfDays>8</NumberOfDays>
		<Pagination>
		<EntriesPerPage>100</EntriesPerPage>
		<PageNumber>2</PageNumber>
		</Pagination>
		</GetSellerTransactionsRequest>';				
		
		$verb = 'GetSellerTransactions';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$xml = simplexml_load_string($responseXml);
		
		printcool($xml);
		break;
		$this->mysmarty->assign('list', $xml->TransactionArray->Transaction);
		$this->mysmarty->assign('dates', $dates);
		
		$this->mysmarty->view('myebay/myebay_transactions.html');
		//printcool ($xml->TransactionArray);		
}


function _GetEbayTransactionsLiveNew()
{
		$this->mysmarty->assign('area', 'Transactions');	
		set_time_limit(1500); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		$dates = array('from' => date('Y-m-d H:i:s', strtotime("-24 Hours")), 'to' => date("Y-m-d H:i:s"));
		$requestXmlBody .= '
		 <IncludeCodiceFiscale>'.TRUE.'</IncludeCodiceFiscale>
		 <IncludeContainingOrder>'.TRUE.'</IncludeContainingOrder>
		 <IncludeFinalValueFee>'.TRUE.'</IncludeFinalValueFee>
		<ModTimeFrom>'.$dates['from'].'</ModTimeFrom>
 		<ModTimeTo>'.$dates['to'].'</ModTimeTo>
  		<NumberOfDays>2</NumberOfDays>
		<Pagination>
		<EntriesPerPage>100</EntriesPerPage>
		</Pagination>
		</GetSellerTransactionsRequest>';				
		
		$verb = 'GetSellerTransactions';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$xml = simplexml_load_string($responseXml);
		foreach ($xml->TransactionArray->Transaction as $t)
		{
		
		
					$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
					$requestXmlBody .= '<GetSellingManagerSaleRecordRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
					$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
					$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
					$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
					$requestXmlBody .= '<ItemID>'.(int)$t->Item->ItemID.'</ItemID>
  <TransactionID>'.(int)$t->TransactionID.'</TransactionID>
  </GetSellingManagerSaleRecordRequest>';					
					$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'GetSellingManagerSaleRecord');
					$responseXml = $session->sendHttpRequest($requestXmlBody);
					if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
						die('<P>Error sending request');
					
					$xmlt = simplexml_load_string($responseXml);
					
					printcool ($xmlt);
					break;
		
		
		
		
		}
		printcool($xml);
		break;
		$this->mysmarty->assign('list', $xml->TransactionArray->Transaction);
		$this->mysmarty->assign('dates', $dates);
		
		$this->mysmarty->view('myebay/myebay_transactions.html');
		//printcool ($xml->TransactionArray);		
}


function _GetEbayOrdersLive()
{
		$this->mysmarty->assign('area', 'Transactions');	
		set_time_limit(1500); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		$dates = array('from' => date('Y-m-d H:i:s', strtotime("-10 Days")), 'to' => date("Y-m-d H:i:s"));
		printcool ($dates);
		$requestXmlBody .= '
		<CreateTimeFrom>'.$dates['from'].'</CreateTimeFrom>
		<CreateTimeTo>'.$dates['to'].'</CreateTimeTo>
		<OrderRole>Seller</OrderRole>
		<OrderStatus>Completed</OrderStatus>
		<NumberOfDays>2</NumberOfDays>
		</GetOrdersRequest>';				
		
		$verb = 'GetOrders';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$xml = simplexml_load_string($responseXml);
		
		printcool($xml);
		break;
		$this->mysmarty->assign('list', $xml->TransactionArray->Transaction);
		$this->mysmarty->assign('dates', $dates);
		
		$this->mysmarty->view('myebay/myebay_transactions.html');
		//printcool ($xml->TransactionArray);		
}


function _GetMyeBaySelling()
{
		$this->mysmarty->assign('area', 'Transactions');	
		set_time_limit(1500); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		$dates = array('from' => date('Y-m-d H:i:s', strtotime("-10 Days")), 'to' => date("Y-m-d H:i:s"));
		printcool ($dates);
		$requestXmlBody .= '
		</GetMyeBaySellingRequest>';				
		
		$verb = 'GetMyeBaySelling';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$xml = simplexml_load_string($responseXml);
		
		printcool($xml);
		break;
		$this->mysmarty->assign('list', $xml->TransactionArray->Transaction);
		$this->mysmarty->assign('dates', $dates);
		
		$this->mysmarty->view('myebay/myebay_transactions.html');
		//printcool ($xml->TransactionArray);		
}


/*function GetEbayStore($display = TRUE)
{
		if ($display)
		{
		set_time_limit(1500); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		}
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetStoreRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";		
		$requestXmlBody .= '<CategoryStructureOnly>TRUE</CategoryStructureOnly>
		<UserID>la.tronics</UserID></GetStoreRequest>';
		
		$verb = 'GetStore';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$xml = simplexml_load_string($responseXml);
		if ($display)
		{
			$this->mysmarty->assign('list', $xml->Store->CustomCategories->CustomCategory);
			$this->mysmarty->view('myebay/myebay_store.html');		
		}
		else return $xml->Store->CustomCategories->CustomCategory;
}*/

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

function _DeleteImageInEbay($id = '', $place = '', $nogo = FALSE)
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
	

function _CleanSef ($string) {

	$string = str_replace(" ", "-", $string);
	$string = str_replace("_", "-", $string);
	$cyrchars = array('��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��');							 
	$latinchars = array('A','B','V','G','D','E','J','Z','I','I','K','L','M','N','O','P','R','S','T','U','F','H','CH','TS','SH','SHT','U','U','JU','YA','a','b','v','g','d','e','j','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','ch','ts','sh','sht','u','u','ju','ya');							 
	$string = str_replace($cyrchars, $latinchars, $string);	
	$string = str_replace('---', '-', $string);	
	$string = str_replace('--', '-', $string);
	$string =  preg_replace('/[^A-Za-z0-9\-]/', '',$string);	

	return $string;
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
	
	
	
function _SerialUpdate()
{
	if ($this->input->post('serial') != '')
	{
		$ser = 	$ser = $this->input->post('serial', TRUE);
		$ser = str_replace(' ','',$ser);
		$str = $ser;
		$ser = explode(',', $ser);	
		if (is_array($ser))
		{	
			foreach ($ser as $ks => $s)
			{		
				$st = explode('_', $s);		
				if (is_array($st) && count($st) > 1)
				{
					$tmp = explode ('-', $st[1]);
					$tmpA[0] =  ereg_replace("[^A-Za-z]", "", $tmp[0]);
					$tmpA[1] =  ereg_replace("[^A-Za-z]", "", $tmp[1]);
					$tmp[0] =  ereg_replace("[^0-9]", "", $tmp[0]);
					$tmp[1] =  ereg_replace("[^0-9]", "", $tmp[1]);
					if (isset($tmp[0]) && isset($tmp[1]) && is_numeric($tmp[0]) && is_numeric($tmp[1]) && $tmp[1] > $tmp[0]) 
					{
						$ser[$ks] = array();
						while ($tmp[0] <= $tmp[1])
								{
									$ser[$ks][] = $st[0].$tmp[0].$tmpA[0];								
									$tmp[0]++;	
								}
						$ser[$ks] = implode(', ', $ser[$ks]);
					}
					else
					{ 
						if (is_array($st)) $ser[$ks] = implode('_', $st);
						else $ser[$ks] = $st; 
					}
				}			
			}
		}
		$ser = implode(', ', $ser);
		//if (substr($str, -2) == ', ') echo substr($str, 0, -2);
		echo ((string)$ser);	
		
		/*
			foreach ($st as $stt)
			{
			 $se = explode('-', $stt[1]);
			 printcool ($se);
			 if (is_array($se))
				{
					if ($se[0] == $s)
					{
						$str .= $se[0].', ';
					}
					else
					{
					//printcool ($se);
						foreach ($se as $ks => $ss)
						{	
							$sstr = preg_replace("/[^a-zA-Z]/", "", $ss);		
							$ss = preg_replace("/[^0-9]/", "", $ss); //printcool ($ss); printcool ($s[$ks]);
							
							if (isset($se[$ks+1]) && ($ss < preg_replace("/[^0-9]/", "", str_replace(' ','', $se[$ks+1])))) 
							{
								while ($ss <= preg_replace("/[^0-9]/", "", str_replace(' ','', $se[$ks+1])))
								{
									$str .= $sstr.$ss.', ';								
									$ss++;	
								}	
							}						
						}
					}
				}
				}*/		
	}
}
	


function _SerialSave($ser = '')
{
	if ($ser != '')
	{
		$ser = str_replace(' ','',$ser);
		$str = $ser;
		$ser = explode(',', $ser);	
		if (is_array($ser))
		{	
			foreach ($ser as $ks => $s)
			{		
				$st = explode('_', $s);		
				if (is_array($st) && count($st) > 1)
				{

					$tmp = explode ('-', $st[1]);
					$tmpA[0] =  ereg_replace("[^A-Za-z]", "", $tmp[0]);
					$tmpA[1] =  ereg_replace("[^A-Za-z]", "", $tmp[1]);
					$tmp[0] =  ereg_replace("[^0-9]", "", $tmp[0]);
					$tmp[1] =  ereg_replace("[^0-9]", "", $tmp[1]);
					if (isset($tmp[0]) && isset($tmp[1]) && is_numeric($tmp[0]) && is_numeric($tmp[1]) && $tmp[1] > $tmp[0]) 
					{
						$ser[$ks] = array();
						while ($tmp[0] <= $tmp[1])
								{
									$ser[$ks][] = $st[0].$tmp[0].$tmpA[0];								
									$tmp[0]++;	
								}
						$ser[$ks] = implode(', ', $ser[$ks]);
					}
					else
					{ 
						if (is_array($st)) $ser[$ks] = implode('_', $st);
						else $ser[$ks] = $st; 
					}
				}			
			}
		}
		$ser = implode(', ', $ser);
		return (string)$ser;
	}		
}
	
////////////TEST


function _test()
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




function _GetCats()
{

		set_time_limit(120);
		ini_set('mysql.connect_timeout', 120);
		ini_set('max_execution_time', 120);  
		ini_set('default_socket_timeout', 120); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetStoreRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";		
		$requestXmlBody .= '<CategoryStructureOnly>TRUE</CategoryStructureOnly>
		<UserID>la.tronics</UserID></GetStoreRequest>';

		$verb = 'GetStore';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		//printcool ($responseXml);
		$this->load->helper('directory');
		$this->load->helper('file');
		if ($responseXml)
			{
				if (!write_file($this->config->config['ebaypath'].'/cats.txt', $responseXml)) 
				{
					GoMail(array ('msg_title' => 'Unable to write Cats.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
					echo 'Unable to update Cats.';
				}
				else
				{
					GoMail(array('msg_title' => 'Cats written @ '.CurrentTime(), 'msg_body' => $responseXml, 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']); 
					echo 'Cats updated. Refresh the admin view for the product now and close this window.';
				}
			}
}


function _GetShipping()
	{
	
						set_time_limit(1500); 
						require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
						
						
						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GeteBayDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
						$requestXmlBody .= '<DetailName>ShippingServiceDetails</DetailName>';
						$requestXmlBody .= '</GeteBayDetailsRequest>';
						$verb = 'GeteBayDetails';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');											
						$this->load->helper('directory');
						$this->load->helper('file');
						if ($responseXml)
							{
								if (!write_file($this->config->config['ebaypath'].'/shipping.txt', $responseXml)) GoMail(array ('msg_title' => 'Unable to write Shippinh.txt @'.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
								else GoMail(array ('msg_title' => 'Shipping written @'.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
							}
	
						Redirect('Myebay');
	
	}

function _UpdateFromEbay($id, $page = 1, $save = false)
{
	if ($_POST['itemid'] == '') { echo 'ERROR: Empty Item ID...<a href="javascript:history.back()">Back</a>'; exit();}
	
	set_time_limit(90);
						ini_set('mysql.connect_timeout', 90);
						ini_set('max_execution_time', 90);  
						ini_set('default_socket_timeout', 90); 
						require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= '<ItemID>'.(int)$_POST['itemid'].'</ItemID>
						</GetItemRequest>';
						$verb = 'GetItem';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);
						
						if ((string)$xml->Item->ItemID == '') { echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>'; exit();}
	
						
						if(!$save)
						{
							$this->db->select('e_title');
							$this->db->where('e_id', (int)$id);
							$query = $this->db->get('ebay');
							if ($query->num_rows() > 0)  $etitle = $query->row_array();	
							else
							{
								echo 'ERROR: Invalid Site ID...<a href="javascript:history.back()">Back</a>'; 
								exit();
							}
				
						echo '
						<table cellpadding="2" cellspacing="2" border="0">
						<tr><td><strong>LaTronics Title:</strong></td><td>'.$etitle['e_title'].'</td></tr>
						<tr><td><strong>eBay Title:</strong></td><td>'.(string)$xml->Item->Title.'</td></tr>
						<tr><td colspan="2"><br><strong>These values will be updated:</strong><Br></td></tr>
						<tr><td>eBay Title:</td><td>'.(string)$xml->Item->Title.'</td></tr>
						<tr><td>eBay Item ID:</td><td>'.(string)$xml->Item->ItemID.'</td></tr>
						<tr><td>eBay Quantity (Quantity - Sold):</td><td>'.((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold).' ( Quantity: '.(int)$xml->Item->Quantity.' / Sold: '.(int)$xml->Item->SellingStatus->QuantitySold.' )</td></tr>
						<tr><td>eBay Price:</td><td>'.(string)$xml->Item->StartPrice.'</td></tr>
						<tr><td>eBay Primary Category:</td><td>'.(string)$xml->Item->PrimaryCategory->CategoryName.'</td></tr>
						
						';
						
						}									
						
						$this->load->helper('directory');
						$this->load->helper('file');
						
						$responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
						$store = simplexml_load_string($responseXml);						
					
						$sc = array();
						if (isset($store->Store->CustomCategories->CustomCategory))
						{
							foreach ($store->Store->CustomCategories->CustomCategory as $s)
							{
								$a = (array)$s;
								$sc[$a['CategoryID']] = $a['Name'];									
							}		
							if (!$save && isset($sc[(string)$xml->Item->Storefront->StoreCategoryID])) echo '<tr><td>eBay Store Category:</td><td>'.$sc[(string)$xml->Item->Storefront->StoreCategoryID].'</td></tr>';
							elseif (!$save && !isset($sc[(string)$xml->Item->Storefront->StoreCategoryID]))	echo '<tr><td>eBay Store Category:</td><td><span style="color:red;">NOT FOUND IN LOCAL STORE CATS (ID MISMATCH)</span> - '.(string)$xml->Item->Storefront->StoreCategoryID.' (StoreCat ID will NOT be updated. Please edit manually.)</td></tr>';
						}
						if(!$save) echo '</table>';
						
	if(!$save) echo '<br><br><span style="color:red;">IS THIS CORRECT ?</span><br><form method="post" action="'.Site_url().'Myebay/UpdateFromEbay/'.(int)$id.'/'.(int)$page.'/TRUE"><input type="hidden" name="itemid" value="'.(string)$xml->Item->ItemID.'" /><input type="submit" value="YES" />&nbsp;&nbsp;<a href="'.Site_url().'Myebay/ListItems/'.(int)$page.'/#'.(int)$id.'">NO</a></form>';
	else {
			
			$data = array('ebay_id' => (string)$xml->Item->ItemID, 
							'e_title' => (string)$xml->Item->Title,
							'ebay_submitted' => 'Manual @ '.CurrentTime().' by '.$this->session->userdata['ownnames'],							
							'pCTitle' => (string)$xml->Item->PrimaryCategory->CategoryName, 
							'PrimaryCategory' => (string)$xml->Item->PrimaryCategory->CategoryID,
							'quantity' => ((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold),
							'ebayquantity' => ((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold),
							'startPrice' => (string)$xml->Item->StartPrice,
							'buyItNowPrice' => (string)$xml->Item->StartPrice			
							);
			if ($save && isset($sc[(string)$xml->Item->Storefront->StoreCategoryID]))
			{
				$data['storeCatID'] = (string)$xml->Item->Storefront->StoreCategoryID;
				$data['storeCatTitle'] = $sc[(string)$xml->Item->Storefront->StoreCategoryID];
			}
			
			if ($save)
			{
				$this->ReWaterMark((int)$id);
				
				$this->db->select('e_id, quantity, ebayquantity, buyItNowPrice');
				$this->db->where('e_id', (int)$id);
				//$this->db->where('ebay_submitted', NULL);
				//$this->db->where('ebay_id', 0);
				$query = $this->db->get('ebay');
				if ($query->num_rows() > 0) 
				{
					$ebr = $query->row_array();	
										
					$this->db->update('ebay', $data, array('e_id' => $ebr['e_id']));			
										
					$hmsg = array ('msg_title' => 'Item '.(int)$id.' Manualy Linked (ItemID/Quantity/Price/Categories/StoreCat) with eBay ItemID', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $ebr['e_id'],
												  'itemid' => $data['ebay_id'],
												  'trec' => 0,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => '');
												  
					//$this->_logaction('UpdateFromEbay', 'Q',array('quantity' => $ebr['quantity']), array('quantity' => (string)$xml->Item->Quantity), $ebr['e_id'], $data['ebay_id'], 0);
					//$this->_logaction('UpdateFromEbay', 'Q',array('ebayquantity' => $ebr['quantity']), array('ebayquantity' => (string)$xml->Item->Quantity), $ebr['e_id'], $data['ebay_id'], 0);
					//$this->_logaction('UpdateFromEbay', 'M',array('price' => $ebr['buyItNowPrice']), array('price' => (string)$xml->Item->StartPrice), $ebr['e_id'], $data['ebay_id'], 0);
					
					foreach ($data as $k => $v)
						{
							if (isset($ebr[$k]) && $ebr[$k]) $olddata = (string)$ebr[$k];
							else $olddata = '';		
							
							if ($k == 'e_part') $latp = 'B';
								elseif ($k == 'e_qpart') $latp = 'B';
								elseif ($k == 'quantity') $latp = 'Q';
								else $latp = 'M';
													
							if ($k != 'PaymentMethod' && $k != 'shipping' && $k != 'startPrice' && $k != 'Submitted') $this->_logaction('RelinkFromEbay', $latp,array($k => $olddata), array($k => $v), (int)$ebr['e_id'], $data['ebay_id'], 0);
						}
						
					$this->db->insert('admin_history', $hmsg); 
					
					GoMail($hmsg, '365@1websolutions.net', $this->config->config['no_reply_email']);
					
					$this->session->set_flashdata('success_msg', 'Item '.(int)$id.' Manualy Linked with eBay ItemID '.$data['ebay_id']);
					$this->session->set_flashdata('action', (int)$id);
					
					if ((int)$page = 1)
					{
						$this->session->set_userdata('last_string', (int)$id);	
						$this->session->set_userdata('last_where', 3);
					}
					Redirect ('Myebay#'.(int)$id);
				}
				else echo 'ERROR. Not found';
		
			}
	}

}
function _BCNFromEbay($id, $page = 1, $save = false)
{
		$this->db->select("ebay_id, e_part, e_qpart");
		$this->db->where('e_id', (int)$id);	;
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0) 
		{
		
		$r = $q->row_array();
		if ((int)$r['ebay_id'] == 0)
		{
			echo 'No Item ID';
			exit();
		}
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
		$requestXmlBody .= '<ItemID>'.(int)$r['ebay_id'].'</ItemID></GetItemRequest>';
		$verb = 'GetItem';
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
		$xml = simplexml_load_string($responseXml);
		if ((string)$xml->Item->ItemID == '') { echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>'; exit();}
		
		
		$txtdescrp = str_replace('<br clear="all" />', '', str_replace('<br>', '', str_replace('<br />', '', (string)$xml->Item->Description)));
		
		$bcn = explode('<span class="ebay_bold">BCN:</span>', $txtdescrp);	
		$bcn = explode('</div>', $bcn[1]);
		
		
		
		
		/*
		if (!isset($bcn[1])) 
		{
			
				if($id == 14033) printcool ($bcn);
			//$bcn = explode('<span class="ebay_bold">BCN:</span><br>', (string)$xml->Item->Description);	
			
			
				
						/*if (isset($bcn[1])) 
							{printcool ($bcn);
								$bcn = explode('<br clear="all">', $bcn[0]);
								if (is_array($bcn))
								{
									printcool ($bcn);
									
									
								
								}
							}
						else 
						{
								$hmsg = array ('msg_title' => 'BCN PARSE FAIL', 'msg_body' => $bcn, 'msg_date' => CurrentTime());
								GoMail($hmsg, '365@1websolutions.net', $this->config->config['no_reply_email']);	
						}*/
		//}
		//else $bcn = explode('<br clear="all" />', $bcn[1]);
		//*/
		$bcn = trim(commasep(commadesep($bcn[0])));
		$bcnt = explode(',', $bcn);
		if (count ($bcnt) > 1)
		{

			foreach ($bcnt as $v)
			{
					$bcnstr[] = trim(ltrim(rtrim($v)));				
			}
			
			if (count($bcnstr) > 0) $bcn = rtrim(implode(', ', $bcnstr));
		}
				//if($id == 14033) printcool ($bcn);
						if(!$save) echo '
						<table cellpadding="2" cellspacing="2" border="0">
						<tr><td>Local BCN:</td><td>'.$r['e_part'].'</td></tr>
						<tr><td>Local BCN Count:</td><td>'.$r['e_qpart'].'</td></tr>
						<tr><td>eBay BCN:</td><td>'.$bcn.'</td></tr>
						<tr><td>eBay BCN Count:</td><td>'.$this->_RealCount($bcn).'</td></tr>						
						</table>';
						
	if(!$save) echo '<br><br><span style="color:red;">IS THIS CORRECT ?</span><br><form method="post" action="'.Site_url().'Myebay/BCNFromEbay/'.(int)$id.'/'.(int)$page.'/TRUE"><input type="submit" value="YES" />&nbsp;&nbsp;<a href="'.Site_url().'Myebay/ListItems/'.(int)$page.'/#'.(int)$id.'">NO</a></form>';
	else {
			
			$data = array('e_part' => $bcn, 'e_qpart' => $this->_RealCount($bcn));
										
			$this->db->update('ebay', $data, array('e_id' => (int)$id));			
										
			$hmsg = array ('msg_title' => 'Item BCNs Updated from eBay @ AdminID '.(int)$id, 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => (int)$id,
												  'itemid' => $r['ebay_id'],
												  'trec' => 0,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => '');
						
			$this->_logaction('UpdateBCN', 'B',array('BCN' => commasep(commadesep($r['e_part']))), array('BCN' => commasep(commadesep($bcn))), $id, (int)$r['ebay_id'], 0);	
			$this->_logaction('UpdateBCN', 'B',array('BCN Count' => $this->_RealCount(commasep(commadesep($r['e_qpart'])))), array('BCN Count' => $this->_RealCount(commasep(commadesep($bcn)))), (int)$id, (int)$r['ebay_id'], 0);
						
						
					$this->db->insert('admin_history', $hmsg); 
					
					$this->session->set_flashdata('success_msg', 'Item BCNs for '.(int)$id.' taken from eBay');
					$this->session->set_flashdata('action', (int)$id);
					
					Redirect ('Myebay#'.(int)$id);
				}
		
		
	}

}

function _RefreshFromEbay($id, $itemid, $save = false)
{
	if ($itemid == '') { echo 'ERROR: Empty Item ID...<a href="javascript:history.back()">Back</a>'; exit();}
	
	set_time_limit(90);
						ini_set('mysql.connect_timeout', 90);
						ini_set('max_execution_time', 90);  
						ini_set('default_socket_timeout', 90); 
						require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= '<ItemID>'.(int)$itemid.'</ItemID>
						</GetItemRequest>';
						$verb = 'GetItem';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);
						
						if ((string)$xml->Item->ItemID == '') { echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>'; exit();}
						
						if(!$save)
						{
							$this->db->where('e_id', (int)$id);
							$query = $this->db->get('ebay');
							if ($query->num_rows() > 0)  $e = $query->row_array();	
							else
							{
								echo 'ERROR: Invalid Site ID...<a href="javascript:history.back()">Back</a>'; 
								exit();
							}
						
						$this->load->helper('directory');
						$this->load->helper('file');
						
						$responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
						$store = simplexml_load_string($responseXml);						

						echo 'Compare and confirm:
						<table cellpadding="2" cellspacing="2" border="1">
						<tr><th>Field</th><th>La-tronics Database</th><th>eBay Database</th></tr>
						
						<tr>
						<td>Title</td>
						<td>'.$e['e_title'].'</td>
						<td>'.(string)$xml->Item->Title.'</td>
						</tr>
						<tr>
						<td>Quantity</td>
						<td>'.$e['quantity'].'</td>
						<td>'.(string)$xml->Item->Quantity.'</td>
						</tr>
						<tr>
						<td>Price:</td>
						<td>'.$e['buyItNowPrice'].'</td>
						<td>'.(string)$xml->Item->StartPrice.'</td>
						</tr>
						<tr>
						<td>Primary Category</td>
						<td>'.$e['pCTitle'].' ('.$e['primaryCategory'].')</td>
						<td>'.(string)$xml->Item->PrimaryCategory->CategoryName.' ('.(string)$xml->Item->PrimaryCategory->CategoryID.')</td>
						</tr>
						<tr>
						<td>Store Category</td>
						<td>'.$e['storeCatID'].'</td>
						<td>'.(string)$xml->Item->Storefront->StoreCategoryID.'</td>
						</tr>
						<tr>
						<td>ListingDuration</td>
						<td>'.$e['listingDuration'].'</td>
						<td>'.(string)$xml->Item->ListingDuration.'</td>
						</tr>
						<!--<tr>
						<td>PaymentMethods</td>
						<td>'.$e['PaymentMethod'].'</td>
						<td>'.$xml->Item->PaymentMethods.'</td>
						</tr>
						<tr>
						<td>Shipping</td>
						<td>'.$e['shipping'].'</td>
						<td>'.$xml->Item->ShippingDetails.'</td>
						</tr>-->
				</table>
						';
						
						/*
						//ShippingDetails
[ShippingServiceOptions] => Array
                (
                    [0] => SimpleXMLElement Object
                        (
                            [ShippingService] => USPSPriorityFlatRateEnvelope
                            [ShippingServiceCost] => 0.0
                            [ShippingServicePriority] => 1
                            [ExpeditedService] => false
                            [ShippingTimeMin] => 2
                            [ShippingTimeMax] => 3
                            [FreeShipping] => true
                        )

                    [1] => SimpleXMLElement Object
                        (
                            [ShippingService] => USPSExpressFlatRateEnvelope
                            [ShippingServiceCost] => 35.0
                            [ShippingServicePriority] => 2
                            [ExpeditedService] => true
                            [ShippingTimeMin] => 1
                            [ShippingTimeMax] => 1
                        )

                )

            [InternationalShippingServiceOption] => Array
                (
                    [0] => SimpleXMLElement Object
                        (
                            [ShippingService] => USPSPriorityMailInternationalFlatRateEnvelope
                            [ShippingServiceCost] => 35.0
                            [ShippingServicePriority] => 1
                            [ShipToLocation] => Worldwide
                        )

                    [1] => SimpleXMLElement Object
                        (
                            [ShippingService] => USPSPriorityMailInternationalFlatRateEnvelope
                            [ShippingServiceCost] => 30.0
                            [ShippingServicePriority] => 2
                            [ShipToLocation] => CA
                        )

                    [2] => SimpleXMLElement Object
                        (
                            [ShippingService] => USPSExpressMailInternationalFlatRateEnvelope
                            [ShippingServiceCost] => 47.0
                            [ShippingServicePriority] => 3
                            [ShipToLocation] => Worldwide
                        )

                    [3] => SimpleXMLElement Object
                        (
                            [ShippingService] => USPSExpressMailInternationalFlatRateEnvelope
                            [ShippingServiceCost] => 35.0
                            [ShippingServicePriority] => 4
                            [ShipToLocation] => CA
                        )

                )*/

 echo '<br><br><span style="color:red;">IS THIS CORRECT ?</span><br><form method="post" action="'.Site_url().'Myebay/UpdateFromEbay/'.(int)$id.'/'.(int)$itemid.'/TRUE"><input type="submit" value="YES" />&nbsp;&nbsp;<a href="'.Site_url().'Myebay#'.(int)$id.'">NO</a></form>';
 	}
	else {			
			
			$data = array(
							'pCTitle' => (string)$xml->Item->PrimaryCategory->CategoryName, 
							'PrimaryCategory' => (string)$xml->Item->PrimaryCategory->CategoryID,
							'quantity' => (string)$xml->Item->Quantity,
							'ebayquantity' => (string)$xml->Item->Quantity,
							'startPrice' => (string)$xml->Item->StartPrice,
							'buyItNowPrice' => (string)$xml->Item->StartPrice,
							'e_title' => (string)$xml->Item->Title,
							'quantity' => (string)$xml->Item->Quantity,
							'ebayquantity' => (string)$xml->Item->Quantity,
							'primaryCategory' => (string)$xml->Item->PrimaryCategory->CategoryID,
							'pCTitle' => (string)$xml->Item->PrimaryCategory->CategoryName,
							'storeCatID' => (string)$xml->Item->Storefront->StoreCategoryID,
							'listingDuration' => (int)$xml->Item->ListingDuration,
							'PaymentMethod' => $xml->Item->PaymentMethods,
							'shipping' => $xml->Item->ShippingDetails							
							);
							
			// GET STORE CATEGORIES				
			if (isset($sc[(string)$xml->Item->Storefront->StoreCategoryID]))
			{
				$data['storeCatID'] = (string)$xml->Item->Storefront->StoreCategoryID;
				$data['storeCatTitle'] = $sc[(string)$xml->Item->Storefront->StoreCategoryID];
			}
			
			if ($save)
			{
			
					$this->db->where('e_id', (int)$id);
				$query = $this->db->get('ebay');
				if ($query->num_rows() > 0) 
				{
					$ebr = $query->row_array();	
					
					$this->db->update('ebay', $data, array('e_id' => $ebr['e_id'], 'ebay_id' => (string)$xml->Item->ItemID));			
					
					foreach ($data as $k => $v)
						{
							if (isset($ebr[$k]) && $ebr[$k]) $olddata = (string)$ebr[$k];
							else $olddata = '';	
								if ($k == 'e_part') $latp = 'B';
								elseif ($k == 'e_qpart') $latp = 'B';
								elseif ($k == 'quantity') $latp = 'Q';
								else $latp = 'M';					
							if ($k != 'PaymentMethod' && $k != 'shipping' && $k != 'startPrice') $this->_logaction('RefreshFromEbay', $latp,array($k => $olddata), array($k => $v), (int)$ebr['e_id'], $ebr['ebay_id'], 0);
						}
						
										//UPDATE WHERE ITEM ID = ITEM ID
					$hmsg = array ('msg_title' => 'Listing Refreshed from eBay', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $ebr['e_id'],
												  'itemid' => (string)$xml->Item->ItemID,
												  'trec' => 0,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => '');
					
					$this->db->insert('admin_history', $hmsg); 
					
					GoMail($hmsg, '365@1websolutions.net', $this->config->config['no_reply_email']);
					
					$this->session->set_flashdata('success_msg', 'Item '.(int)$id.' Refreshed from eBay ItemID '.$data['ebay_id']);
					$this->session->set_flashdata('action', (int)$id);
				
				}
				else
				{
					$this->session->set_flashdata('error_msg', 'Item '.(int)$id.' NOT Refreshed');
				}
					Redirect ('Myebay#'.(int)$id);
		
			}
	}

}



function _UpdateASC($rec = '', $itemid = '')
{
	
	set_time_limit(180);
		ini_set('mysql.connect_timeout', 180);
		ini_set('max_execution_time', 180);  
		ini_set('default_socket_timeout', 180); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetSellingManagerSaleRecord';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetSellingManagerSaleRecordRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
				$requestXmlBody .= "<ItemID>".(int)$itemid."</ItemID>";
				$requestXmlBody .= "<TransactionID>".(int)$rec."</TransactionID>";
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				$requestXmlBody .=  '</GetSellingManagerSaleRecordRequest>';			

				//send the request and get response
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				$xml = simplexml_load_string($responseXml);
				$item = $xml->SellingManagerSoldOrder;	
				printcool ($xml);
				break;
				//printcool ($t);
				$cascupd = 0;
				if ($item)
				{
					if ((string)$item->ActualShippingCost != $t['asc'])
					{
						//$echo .= "Updating   $t[itemid]   $t[et_id]   ";
						//$echo .= (string)$item->ActualShippingCost . ' - '.$t['asc'].' <br/>';
						$this->db->update('ebay_transactions', array('asc' => (string)$item->ActualShippingCost), array('et_id' => $t['et_id']));
						$this->_logaction('Transactions', 'B', array('ActShipCost' => $t['asc']), array('ActShipCost' => (string)$item->ActualShippingCost), 0, $t['itemid'], $t['rec']);
						$updatedIds .= $t['rec'].', ';
						$change++;
						
						$cascupd = 1;
						
					}
					$ar = $this->_XML2Array($item->OrderStatus);
					if (isset($ar['OrderStatus'])) $ar = $ar['OrderStatus'];
				
					 if (isset($ar['PaidTime']) && (CleanBadDate((string)$ar['PaidTime']) != $t['paidtime']) && (CleanBadDate((string)$ar['PaidTime']) != ''))
						{
							
							$this->db->update('ebay_transactions', array('paidtime' => CleanBadDate((string)$ar['PaidTime'])), array('et_id' => $t['et_id']));
							$this->_logaction('Transactions', 'B', array('PaidTime' => $t['paidtime']), array('PaidTime' => CleanBadDate((string)$ar['PaidTime'])), 0, $t['itemid'], $t['rec']);		
							
						}	
					 if (isset($ar['paidtime'])) unset($ar['paidtime']);
					 $pd = serialize($ar);
					  if ($pd != $t['paydata']) $this->db->update('ebay_transactions', array('paydata' => $pd), array('et_id' => $t['et_id']));					

				if ($cascupd == 1) $this->db->update('ebay_transactions', array('cascupd' => 1), array('et_id' => $t['et_id']));					
				unset($item);
				}


			if ($change > 0) $this->db->insert('admin_history', array('msg_type' => 1, 'msg_title' => 'Actual Shipping Cost Updated', 
											'msg_body' => 'Updated: '.rtrim($updatedIds, ', '), 
											'msg_date' => CurrentTime(),											
										  	'admin' => 'Auto',
										  	'sev' => '')); 

			//$echo .= "</body></html>";
			// write_file($file, $echo);					  			 
			// $this->mysmarty->assign("info", $echo);
			// $this->mysmarty->assign("ids", $updatedIds);
	

		//$this->mysmarty->view('myebay/khim.html');	
}


function _logaction($location = '', $type = 'M', $datafrom = '', $datato = '', $eid = '', $itemid = '', $transid = '', $key = '')
{

		foreach ($datato as $k => $v)
		{
			if ($v != $datafrom[$k])
			{
				if (isset($this->session->userdata['ownnames'])) $admin = $this->session->userdata['ownnames'];
				else $admin = 'Cron';
				
					
					$hmsg = array ('msg_title' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_body' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_date' => CurrentTime());
					
					//GoMail($hmsg, '365@1websolutions.net', $this->config->config['no_reply_email']);
				
				if ($key == '') $this->db->insert('ebay_actionlog', array('atype' => $type, 'e_id' => (int)$eid, 'ebay_id' => (int)$itemid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom[$k], 'datato' => $v, 'field' => $k, 'admin' => $admin, 'trans_id' => (int)$transid, 'ctrl' => $location)); 			
				else $this->db->insert('ebay_actionlog', array('atype' => $type, 'e_id' => (int)$eid, 'ebay_id' => (int)$itemid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom[$k], 'datato' => $v, 'field' => $k, 'admin' => $admin, 'oid' => (int)$transid, 'okey' => $key, 'ctrl' => $location)); 		
			}
		}
}
function _recordsubmiterror($err = array())
{
					//GoMail($err, '365@1websolutions.net', $this->config->config['no_reply_email']);
					$err['admin'] = $this->session->userdata['ownnames'];
					$this->db->insert('ebay_submitlog', $err);
}

function _TestCategories()
{

exit();
/*$catid = '16145';

		$this->db->select('catID, catName, parentID, CategoryLevel');
		$this->db->where('catID', (int)$catid);
		$q = $this->db->get('ebaydata_categories');
		if ($q->num_rows() > 0) 
		{
			$c = $q->row_array();	
			$string = $c['catName'];
			
			while ($c['CategoryLevel'] > 1)
			{
				$this->db->select('catID, catName, parentID, CategoryLevel');			
				$this->db->where('catID', (int)$c['parentID']);
				$q = $this->db->get('ebaydata_categories');
				if ($q->num_rows() > 0) 
					{
					$c = $q->row_array();
					$string = $c['catName'].' / '.$string;
					}			
			}
		}
		
		echo $string;
		*/
		
		$this->db->select("e_id, primaryCategory, pCTitle");		
		//$this->db->limit(1000);
		$this->db->where('primaryCategory > ' , 0);
		//$this->db->where('e_id > ' , 6000);
		$this->db->order_by("e_id", "DESC");
		$this->query = $this->db->get('ebay');
		$count = 0;
		if ($this->query->num_rows() > 0)
		{
			foreach ($this->query->result_array() as $r)
			{
				if (strlen($r['pCTitle']) < 61)
				{
					$count++;
					printcool ($r);
					//printcool ($this->Myebay_model->GetEbayCategoryTitle($r['primaryCategory']));
					//$this->db->update('ebay', array('pCTitle' => $this->Myebay_model->GetEbayCategoryTitle($r['primaryCategory'])), array('e_id' => (int)$r['e_id']));
				 }
				}
		 
		 }
		 echo $count;
}

function _CleanUpActionLog()
{

	$this->db->select('al_id, field, datafrom, datato');
	//$this->db->like('field', 'part');
	$this->db->where('field','sold');
	$query = $this->db->get('ebay_actionlog');
	if ($query->num_rows() > 0) 
	{
		$e = $query->result_array();
		foreach($e as $m)
		{
			//$this->db->update('ebay_actionlog', array('atype' => 'B'), array('al_id' => (int)$m['al_id']));
		}
		printcool ($e);	
	}
}





function _GetSuggestedCategories($searchstring = '')
{
if (isset($_POST['src'])) $searchstring = trim($_POST['src']);
if ($searchstring == '') return 'No search string inputed';
	
	//echo '<input id="catsearch" name="catsearch" value="'.$searchstring.'" style="width:250px;">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="var csrc = document.getElementById(\'catsearch\').value; SelectShipping(csrc)"><img src="'.base_url().'images/admin/b_search.png" /> Get eBay Suggested</a><br><br>';
	
	set_time_limit(180);
		ini_set('mysql.connect_timeout', 180);
		ini_set('max_execution_time', 180);  
		ini_set('default_socket_timeout', 180); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetSuggestedCategories';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetSuggestedCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
				$requestXmlBody .= '<Query>'.$searchstring.'</Query>'; 
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				$requestXmlBody .=  '</GetSuggestedCategoriesRequest>';			
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
				$xml = simplexml_load_string($responseXml);
				$cats = $xml->SuggestedCategoryArray->SuggestedCategory;
					echo '<select id="primaryCategory" name="primaryCategory">';
				foreach ($cats as $c)
				{
					
					$c = $this->_XML2Array($c);
					$c = $c['Category'];
					$cgcstr = '';
				echo '<option value="'.$c['CategoryID'].'">';
					if(isset($c['CategoryParentID']) && (count($c['CategoryParentID']) > 0))
						{
							/*foreach ($c['CategoryParentID'] as $k => $v)
							{
								echo $c['CategoryParentName'][$k].' <strong>&rArr;</strong> ';		//('.$v.')						
							}*/							
							if (is_array($c['CategoryParentID'])) foreach ($c['CategoryParentName'] as $v)
							 {
								$cgcstr .= $v.' <strong>&rArr;</strong> ';
								echo $v.' <strong>&rArr;</strong> ';	
							 }//('.$v.')	
							else 
							{
								$cgcstr .= $c['CategoryParentName'].' <strong>&rArr;</strong> ';
								echo $c['CategoryParentName'].' <strong>&rArr;</strong> ';	
							}
						}
					
				//echo '<strong><input onlick="javascript:void(0)" onClick="SaveShipping('.(int)$c['CategoryID'].', '.$c['CategoryName'].', '.$searchstring.')" type="radio" id="'.(int)$c['CategoryID'].'" value="'.$c['CategoryName'].'" name="primaryCategory" /> <label for="'.(int)$c['CategoryID'].'"></label></strong>
				$cgcstr .= $c['CategoryName'];
				echo '<strong>'.$c['CategoryName'].'</strong>';
				echo '</option>';
				
				$gotcats[] = array('catID' => $c['CategoryID'], 'catName' => $cgcstr);
				
				/*
				&nbsp;&nbsp;
				<input type="hidden" id="id'.$c['CategoryID'].'" value="'.$c['CategoryID'].'">
				<input type="hidden" id="name'.$c['CategoryID'].'" value="'.$c['CategoryName'].'">
				<input type="hidden" id="ss'.$c['CategoryID'].'" value="'.$searchstring.'">
				
				&nbsp;&nbsp;<a href="javascript:void(0)" onclick="var cid = document.getElementById(id'.$c['CategoryID'].').value; var cname = document.getElementById(name'.$c['CategoryID'].').value; var css = document.getElementById(\ss'.$c['CategoryID'].').value; SaveShipping(cid, cname, css)" style=" color:#0099FF;">SELECT</a><Br><br>';*/
					// ('.$c['CategoryID'].')
				}
				echo '</select>';
				//printcool ($xml);
				
				if (isset($gotcats) && (count($gotcats) > 0)) $this->session->set_userdata(array('gotcats' => $gotcats));
	
}

function _SaveSuggestedCategories($catid, $catname, $searchstring)
{
	echo '<input id="catsearch" name="catsearch" value="'.$searchstring.'" style="width:250px;">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="var csrc = document.getElementById(\'catsearch\').value; SelectShipping(csrc)"><img src="'.base_url().'images/admin/b_search.png" /> Get eBay Suggested</a><br><br>';
	
	echo '<select id="primaryCategory" name="primaryCategory">
      <option value="'.$catid.'">'.$catname.'</option></select>';	
}



function _ReWaterMark($id = '')
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


function _GetSiteXML($display = false)
	{
		
		
		$this->load->library('htmltotext');
			
		$this->productlist = $this->Myebay_model->ListXMLItems();		
		$feed = '<?xml version="1.0"?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
<channel>
<title>'.$this->config->config['sitename'].'</title>
<link>'.$this->config->config['base_url'].'</link>
<description>La-Tronics</description>';	

if ($display) $this->mysmarty->assign('total', '<strong>'.count($this->productlist).' total products in feed</strong><br><br>');
//$countnodesc = 0;
//$countdeschtml = '';

$this->err = false;

foreach ($this->productlist as $key => $value)
{
$feedarray[$key] = "
<item>
<g:id>".$value['e_id']."</g:id>
<g:title>".substr(CleanXML(htmlspecialchars($value['e_title'])), 0, 150)."</g:title>
";
$dsc = htmlspecialchars(substr($this->htmltotext->go($value['e_desc']),0,5000));
$this->productlist[$key]['dscl'] =  $value['dscl'] = strlen($dsc);

$feedarray[$key] .= "<g:description>".$dsc."</g:description>
";


$value['e_part'] = bcndelim($value['e_part']);

$feedarray[$key] .= "<g:link>".Site_url().'storeitem/'.$value['e_id']."</g:link>
<g:image_link>".Site_url().$this->config->config['wwwpath']['imgebay']."/".$value['idpath']."/Ebay_".$value['e_img1']."</g:image_link>
<g:condition>".googlecondition($value['Condition'])."</g:condition>
<g:availability>".googleavailability($value['quantity'])."</g:availability>
<g:price>".$value['buyItNowPrice']." USD</g:price>
<g:brand>".htmlspecialchars(trim($value['e_manuf']))."</g:brand>
<g:mpn>".htmlspecialchars(trim($value['e_part']))."</g:mpn>
<g:gtin>".htmlspecialchars(trim($value['upc']))."</g:gtin>
";

$snm = 0;
if (trim($value['e_manuf']) == '') $snm++;
if (trim($value['e_part']) == '') $snm++;
if (trim($value['upc']) == '') $snm++;
if ($snm >= 2) $feedarray[$key] .= "<g:identifier_exists>FALSE</g:identifier_exists>";

$feedarray[$key] .= "<g:google_product_category>".$value['gtaxonomy']."</g:google_product_category>	
<g:product_type>".$value['gtaxonomy']."</g:product_type>	
<g:shipping_weight>".$value['weight_kg']." kg</g:shipping_weight>
</item>";


$move = 0;

if ($snm >= 2)	{
					$this->err['brandmpcupc'][] = $value;
					$move = 1;	
				}				
if ($dsc == '')
				{ 					
					$this->err['desc'][] = $value;
					$move = 1;	
				}
if ($move > 0) { unset($this->productlist[$key]); unset($feedarray[$key]); }
}
foreach ($feedarray as $f)
{
	$feed .= $f;
}
		$feed .= '
		</channel>
				</rss>';		
		//$feed = str_replace('{br}', '<br>', $feed);
		//$feed = $this->htmltotext->go($feed); 
		//$feed = str_replace('&lt;', '<', $feed);
		//$feed = str_replace('&lt;', '<', $feed);
		//$feed = str_replace('&lt;br&gt;', '<br>', $feed);
		//$feed = str_replace('&quot;', '"', $feed);
		$this->load->helper('file');
		//delete_files($this->config->config['paths']['xml']);
		if (file_exists($this->config->config['paths']['feeds'].'/feed.xml')) unlink($this->config->config['paths']['feeds'].'/feed.xml');
		write_file($this->config->config['paths']['feeds'].'/feed.xml', $feed);
		if (!file_exists($this->config->config['paths']['feeds'].'/index.html')) write_file($this->config->config['paths']['feeds'].'/index.html', ' :) ');
		
		if ($display)
		{
			//echo '<strong>Products without description: '.$countnodesc.'</strong><br><br>';
			//echo $countdeschtml;
			
			$this->mysmarty->assign('list', $this->productlist);
			$this->mysmarty->assign('err', $this->err);
			$this->mysmarty->view('myebay/myebay_xmldebug.html');
		}
		//$this->load->helper('download');
		//$name = 'products_'.(int)$catid.'.xml';
		//force_download($name, $feed);		
	}
	
	function _gTaxonomy()
	{
	$list = 'Electronics
Electronics > Computers
Electronics > Computers > Barebone Computers
Electronics > Computers > Computer Accessories
Electronics > Computers > Computer Accessories > Computer Risers & Stands
Electronics > Computers > Computer Accessories > Handheld Device Accessories
Electronics > Computers > Computer Accessories > Handheld Device Accessories > E-Book Reader Accessories
Electronics > Computers > Computer Accessories > Handheld Device Accessories > E-Book Reader Accessories > E-Book Reader Cases
Electronics > Computers > Computer Accessories > Handheld Device Accessories > PDA Accessories
Electronics > Computers > Computer Accessories > Handheld Device Accessories > PDA Accessories > PDA Cases
Electronics > Computers > Computer Accessories > Keyboard & Mouse Wrist Rests
Electronics > Computers > Computer Accessories > Keyboard Trays & Platforms
Electronics > Computers > Computer Accessories > Laptop Accessories
Electronics > Computers > Computer Accessories > Laptop Accessories > Laptop Docking Stations
Electronics > Computers > Computer Accessories > Mouse Pads
Electronics > Computers > Computer Accessories > Stylus Pen Nibs & Refills
Electronics > Computers > Computer Accessories > Stylus Pens
Electronics > Computers > Computer Accessories > Tablet Computer Accessories
Electronics > Computers > Computer Accessories > Tablet Computer Accessories > Tablet Computer Replacement Parts
Electronics > Computers > Computer Accessories > Tablet Computer Accessories > Tablet Computer Replacement Parts > Tablet Computer Digitizers
Electronics > Computers > Computer Accessories > Tablet Computer Accessories > Tablet Computer Stands
Electronics > Computers > Computer Components
Electronics > Computers > Computer Components > Computer Cases
Electronics > Computers > Computer Components > Computer Power Supplies
Electronics > Computers > Computer Components > Computer Processors
Electronics > Computers > Computer Components > Computer Racks & Mounts
Electronics > Computers > Computer Components > Computer Starter Kits
Electronics > Computers > Computer Components > Computer System Cooling
Electronics > Computers > Computer Components > Input Devices
Electronics > Computers > Computer Components > Input Devices > Barcode Scanners
Electronics > Computers > Computer Components > Input Devices > Computer Keyboards
Electronics > Computers > Computer Components > Input Devices > Digital Note Taking Pens
Electronics > Computers > Computer Components > Input Devices > Fingerprint Readers
Electronics > Computers > Computer Components > Input Devices > Game Controllers
Electronics > Computers > Computer Components > Input Devices > Graphics Tablets
Electronics > Computers > Computer Components > Input Devices > KVM Switches
Electronics > Computers > Computer Components > Input Devices > Keyboard & Mouse Sets
Electronics > Computers > Computer Components > Input Devices > Memory Card Readers
Electronics > Computers > Computer Components > Input Devices > Mice & Trackballs
Electronics > Computers > Computer Components > Input Devices > Numeric Keypads
Electronics > Computers > Computer Components > Input Devices > Smart Card Readers
Electronics > Computers > Computer Components > Input Devices > Touchpads
Electronics > Computers > Computer Components > Laptop Parts
Electronics > Computers > Computer Components > Laptop Parts > Laptop Hinges
Electronics > Computers > Computer Components > Laptop Parts > Laptop Replacement Cables
Electronics > Computers > Computer Components > Laptop Parts > Laptop Replacement Keyboards
Electronics > Computers > Computer Components > Laptop Parts > Laptop Replacement Screens
Electronics > Computers > Computer Components > Laptop Parts > Laptop Shells
Electronics > Computers > Computer Components > Motherboards
Electronics > Computers > Computer Components > Output Devices
Electronics > Computers > Computer Components > Storage Devices
Electronics > Computers > Computer Components > Storage Devices > Hard Drives
Electronics > Computers > Computer Components > Storage Devices > Hard Drives > Solid State Drives
Electronics > Computers > Computer Components > Storage Devices > Storage Drive Accessories
Electronics > Computers > Computer Components > Storage Devices > Storage Drive Accessories > Hard Drive Caddies
Electronics > Computers > Computer Components > Storage Devices > Storage Drive Accessories > Hard Drive Carrying Cases
Electronics > Computers > Computer Components > Storage Devices > Storage Drive Accessories > Hard Drive Docks
Electronics > Computers > Computer Components > Storage Devices > Storage Drive Accessories > Hard Drive Enclosures
Electronics > Computers > Computer Components > Storage Devices > Storage Drive Accessories > Hard Drive Mounts
Electronics > Computers > Computer Components > Storage Devices > Tape Drives
Electronics > Computers > Computer Components > Storage Devices > USB Flash Drives
Electronics > Computers > Computer Servers
Electronics > Computers > Desktop Computers
Electronics > Computers > Handheld Devices
Electronics > Computers > Handheld Devices > Data Collection Terminals
Electronics > Computers > Handheld Devices > E-Book Readers
Electronics > Computers > Handheld Devices > PDAs
Electronics > Computers > Laptops
Electronics > Computers > Laptops > Netbooks
Electronics > Computers > Tablet Computers
Electronics > Electronics Accessories
Electronics > Electronics Accessories > Adapters
Electronics > Electronics Accessories > Adapters > Memory Adapters
Electronics > Electronics Accessories > Adapters > Storage Adapters
Electronics > Electronics Accessories > Adapters > USB Adapters
Electronics > Electronics Accessories > Cable Management
Electronics > Electronics Accessories > Cable Management > Cable Clips
Electronics > Electronics Accessories > Cable Management > Cable Trays
Electronics > Electronics Accessories > Cable Management > Patch Panels
Electronics > Electronics Accessories > Cable Management > Wire & Cable Sleeves
Electronics > Electronics Accessories > Cable Management > Wire & Cable Ties
Electronics > Electronics Accessories > Cables
Electronics > Electronics Accessories > Cables > Audio & Video Cables
Electronics > Electronics Accessories > Cables > Audio & Video Cables > DVI Cables
Electronics > Electronics Accessories > Cables > Audio & Video Cables > HDMI Cables
Electronics > Electronics Accessories > Cables > Audio & Video Cables > SCART Cables
Electronics > Electronics Accessories > Cables > Audio & Video Cables > Speaker Cables
Electronics > Electronics Accessories > Cables > Data Transfer Cables
Electronics > Electronics Accessories > Cables > Data Transfer Cables > FireWire Cables
Electronics > Electronics Accessories > Cables > Data Transfer Cables > USB Cables
Electronics > Electronics Accessories > Cables > Data Transfer Cables > iOS Cables
Electronics > Electronics Accessories > Cables > KVM Cables
Electronics > Electronics Accessories > Cables > Network Cables
Electronics > Electronics Accessories > Cables > Network Cables > Ethernet Cables
Electronics > Electronics Accessories > Cables > Network Cables > Serial Cables
Electronics > Electronics Accessories > Cables > Optical Cables
Electronics > Electronics Accessories > Cables > RCA Cables
Electronics > Electronics Accessories > Cables > Storage Cables
Electronics > Electronics Accessories > Cables > Storage Cables > IDE Cables
Electronics > Electronics Accessories > Cables > Storage Cables > SAS Cables
Electronics > Electronics Accessories > Cables > Storage Cables > SATA Cables
Electronics > Electronics Accessories > Cables > Storage Cables > SCSI Cables
Electronics > Electronics Accessories > Cables > System & Power Cables
Electronics > Electronics Accessories > Cables > Telephone Cables
Electronics > Electronics Accessories > Electronics Cleaners
Electronics > Electronics Accessories > Electronics Cleaners > Audio & Video Cleaners
Electronics > Electronics Accessories > Electronics Cleaners > Camera Cleaners
Electronics > Electronics Accessories > Electronics Cleaners > Screen Cleaners
Electronics > Electronics Accessories > Electronics Films & Shields
Electronics > Electronics Accessories > Electronics Films & Shields > Electronics Stickers & Decals
Electronics > Electronics Accessories > Electronics Films & Shields > Electronics Stickers & Decals > Computer Keyboard Stickers
Electronics > Electronics Accessories > Electronics Films & Shields > Electronics Stickers & Decals > Game Console Stickers & Decals
Electronics > Electronics Accessories > Electronics Films & Shields > Electronics Stickers & Decals > Laptop Stickers & Decals
Electronics > Electronics Accessories > Electronics Films & Shields > Electronics Stickers & Decals > MP3 Player Stickers & Decals
Electronics > Electronics Accessories > Electronics Films & Shields > Electronics Stickers & Decals > Mobile Phone Stickers & Decals
Electronics > Electronics Accessories > Electronics Films & Shields > Electronics Stickers & Decals > Tablet Computer Stickers & Decals
Electronics > Electronics Accessories > Electronics Films & Shields > Keyboard Protectors
Electronics > Electronics Accessories > Electronics Films & Shields > Privacy Filters
Electronics > Electronics Accessories > Electronics Films & Shields > Screen Protectors
Electronics > Electronics Accessories > Memory
Electronics > Electronics Accessories > Memory > Cache Memory
Electronics > Electronics Accessories > Memory > Flash Memory
Electronics > Electronics Accessories > Memory > Flash Memory > Flash Memory Cards
Electronics > Electronics Accessories > Memory > RAM
Electronics > Electronics Accessories > Memory > ROM
Electronics > Electronics Accessories > Memory > Video Memory
Electronics > Electronics Accessories > Memory Accessories
Electronics > Electronics Accessories > Memory Accessories > Memory Cases
Electronics > Electronics Accessories > Power
Electronics > Electronics Accessories > Power > Batteries
Electronics > Electronics Accessories > Power > Batteries > Camera Batteries
Electronics > Electronics Accessories > Power > Batteries > Cordless Phone Batteries
Electronics > Electronics Accessories > Power > Batteries > E-Book Reader Batteries
Electronics > Electronics Accessories > Power > Batteries > General Purpose Batteries
Electronics > Electronics Accessories > Power > Batteries > Laptop Batteries
Electronics > Electronics Accessories > Power > Batteries > Mobile Phone Batteries
Electronics > Electronics Accessories > Power > Batteries > PDA Batteries
Electronics > Electronics Accessories > Power > Batteries > Tablet Computer Batteries
Electronics > Electronics Accessories > Power > Batteries > UPS Batteries
Electronics > Electronics Accessories > Power > Batteries > Video Camera Batteries
Electronics > Electronics Accessories > Power > Battery Accessories
Electronics > Electronics Accessories > Power > Battery Accessories > Camera Battery Chargers
Electronics > Electronics Accessories > Power > Battery Accessories > General Purpose Battery Chargers
Electronics > Electronics Accessories > Power > Chargers
Electronics > Electronics Accessories > Power > Chargers > E-Book Reader Chargers
Electronics > Electronics Accessories > Power > Chargers > MP3 Player Chargers
Electronics > Electronics Accessories > Power > Chargers > Mobile Phone Chargers
Electronics > Electronics Accessories > Power > Chargers > PDA Chargers
Electronics > Electronics Accessories > Power > Chargers > Solar Chargers
Electronics > Electronics Accessories > Power > Chargers > Tablet Computer Chargers
Electronics > Electronics Accessories > Power > Fuel Cells
Electronics > Electronics Accessories > Power > Power Adapter Accessories
Electronics > Electronics Accessories > Power > Power Adapters
Electronics > Electronics Accessories > Power > Power Adapters > Laptop Power Adapters
Electronics > Electronics Accessories > Power > Power Adapters > Power Converters
Electronics > Electronics Accessories > Power > Power Adapters > Power Inverters
Electronics > Electronics Accessories > Power > Power Adapters > Travel Adapters
Electronics > Electronics Accessories > Power > Power Conditioners
Electronics > Electronics Accessories > Power > Power Control Units
Electronics > Electronics Accessories > Power > Power Enclosures
Electronics > Electronics Accessories > Power > Power Injectors & Splitters
Electronics > Electronics Accessories > Power > Power Strips & Surge Suppressors
Electronics > Electronics Accessories > Power > Surge Protection Devices
Electronics > Electronics Accessories > Power > UPS
Electronics > Electronics Accessories > Power > UPS Accessories
Electronics > Electronics Accessories > Power > Voltage Converters
Electronics > Electronics Accessories > Remote Controls
Electronics > Electronics Accessories > Signal Boosters
Electronics > GPS
Electronics > GPS > Automotive GPS
Electronics > GPS > Aviation GPS
Electronics > GPS > Sport GPS
Electronics > GPS Accessories
Electronics > GPS Accessories > GPS Cases
Electronics > GPS Accessories > GPS Mounts
Electronics > GPS Trackers
Electronics > Marine Electronics
Electronics > Marine Electronics > Fish Finders
Electronics > Marine Electronics > Marine Chartplotters & GPS
Electronics > Marine Electronics > Marine Radar
Electronics > Marine Electronics > Marine Radios
Electronics > Networking
Electronics > Networking > Bridges & Routers
Electronics > Networking > Bridges & Routers > Network Bridges
Electronics > Networking > Bridges & Routers > Network Bridges > Wireless Bridges
Electronics > Networking > Bridges & Routers > VoIP Gateways & Routers
Electronics > Networking > Bridges & Routers > Wireless Access Points
Electronics > Networking > Bridges & Routers > Wireless Routers
Electronics > Networking > Concentrators & Multiplexers
Electronics > Networking > Hubs & Switches
Electronics > Networking > Modem Accessories
Electronics > Networking > Modems
Electronics > Networking > Network Cards & Adapters
Electronics > Networking > Network Cards & Adapters > XBox 360 Network Cards
Electronics > Networking > Network Security & Firewall Devices
Electronics > Networking > Power Line Network Adapters
Electronics > Networking > Print Servers
Electronics > Networking > Repeaters & Transceivers
Electronics > Plug & Play TV Games
Electronics > Video
Electronics > Video > Computer Monitors
Electronics > Video > Video Accessories
Electronics > Video > Video Accessories > 3D Glasses
Electronics > Video > Video Accessories > Computer Monitor Accessories
Electronics > Video > Video Accessories > Computer Monitor Accessories > Color Calibrators';
	
		return explode("\n", $list);

	}











function _unzipebay()
{
	
	$zip = new ZipArchive;
	$res = $zip->open('ebay_images/ebi1.zip');
	var_dump ($res);
	if ($res === TRUE) 
	{
  	$zip->extractTo('ebay_images/');
  	$zip->close();
  	echo 'DONE!';
	} 
	else  echo 'Error';
}



function _getnullidpath()
{
		$this->db->select("e_id, e_img1, e_img2, e_img3, e_img1, idpath");	
		$this->db->where('idpath', NULL);
		$this->query = $this->db->get('ebay');		
		printcool ($this->query->result_array());
}


function _testorderdata()
{

$d= 'a:1:{i:14346;a:12:{s:8:"quantity";i:1;s:7:"e_title";s:72:"Acer Aspire 3000 3500 5000 CPU Cooling Fan + Heatsink 36ZL5TATN01 Tested";s:5:"e_sef";s:71:"Acer-Aspire-3000-3500-5000-CPU-Cooling-Fan--Heatsink-36ZL5TATN01-Tested";s:6:"e_img1";s:83:"14346_Acer-Aspire-3000-3500-5000-CPU-Cooling-Fan--Heatsink-36ZL5TATN01-Tested_1.JPG";s:6:"idpath";s:3:"144";s:2:"sn";s:0:"";s:4:"revs";i:0;s:5:"admin";s:0:"";s:13:"buyItNowPrice";d:500;s:8:"shipping";a:4:{s:7:"exclude";s:3:"Yes";s:15:"locationexclude";s:7:"Africa,";s:8:"domestic";a:4:{i:1;a:4:{s:19:"ShippingServiceCost";s:1:"0";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:12:"FreeShipping";s:2:"on";s:15:"ShippingService";s:14:"USPSFirstClass";}i:2;a:3:{s:19:"ShippingServiceCost";s:4:"4.95";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:15:"ShippingService";s:12:"USPSPriority";}i:3;a:3:{s:19:"ShippingServiceCost";s:2:"21";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:15:"ShippingService";s:15:"USPSExpressMail";}i:4;a:3:{s:19:"ShippingServiceCost";s:0:"";s:29:"ShippingServiceAdditionalCost";s:0:"";s:15:"ShippingService";s:0:"";}}s:13:"international";a:4:{i:1;a:4:{s:19:"ShippingServiceCost";s:2:"10";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:14:"ShipToLocation";s:9:"Worldwide";s:15:"ShippingService";s:31:"USPSFirstClassMailInternational";}i:2;a:4:{s:19:"ShippingServiceCost";s:2:"25";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:14:"ShipToLocation";s:9:"Worldwide";s:15:"ShippingService";s:45:"USPSPriorityMailInternationalFlatRateEnvelope";}i:3;a:4:{s:19:"ShippingServiceCost";s:2:"22";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:14:"ShipToLocation";s:2:"CA";s:15:"ShippingService";s:45:"USPSPriorityMailInternationalFlatRateEnvelope";}i:4;a:4:{s:19:"ShippingServiceCost";s:0:"";s:29:"ShippingServiceAdditionalCost";s:0:"";s:14:"ShipToLocation";s:0:"";s:15:"ShippingService";s:0:"";}}}s:18:"totalweight_custom";i:0;s:5:"total";d:500;}}';

printcool (unserialize($d));
}

function _testkeys()

{
	require_once($this->config->config['ebaypath'].'get-common/keys.php');
}
}