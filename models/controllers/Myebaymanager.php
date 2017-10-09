<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Myebaymanager extends Controller
{
	function Myebaymanager()
	{
		parent::Controller();
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'EbayManager');

		if (isset($this->session->userdata['sandbox']) && $this->session->userdata['sandbox'] == 1) $this->categorydb = 'warehouse_sku_categories_sandbox';
		else $this->categorydb = 'warehouse_sku_categories';

	}
	function index()
	{
		/*
			$this->load->helper('directory');
			$this->load->helper('file');
			$store = simplexml_load_string(read_file($this->config->config['ebaypath'].'/cats.txt'));
			$this->cs = array();
			if (isset($store->Store->CustomCategories->CustomCategory)) $this->_storecatting($store->Store->CustomCategories->CustomCategory);
			$this->mysmarty->assign('store', $this->cs);
			*/

		$this->db->where("notebay", 0);
		$this->db->orderby('listorder', 'ASC');
		$categories = $this->db->get($this->categorydb)->result_array();
		$this->mysmarty->assign('dbstore', $categories);

		$this->mysmarty->view('myebay/myebay_manager.html');
	}
	function Sandbox()
	{
		$this->session->set_userdata('sandbox', 1);
		Redirect('Myebaymanager');
	}
	function Live()
	{
		$this->session->set_userdata('sandbox', 0);
		Redirect('Myebaymanager');
	}
	function RefreshStoreCategories()
	{
		$this->Auth_model->CheckListings();
		if (isset($this->session->userdata['sandbox']) && $this->session->userdata['sandbox'] == 1) $production = false;
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
		<UserID>'.$ebayuserid.'</UserID></GetStoreRequest>';

		$verb = 'GetStore';
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		$this->load->helper('directory');
		$this->load->helper('file');
		if ($responseXml)
		{
			$store = simplexml_load_string($responseXml);

			$this->cs = array();
			$this->dbs = array();
			$this->listorder = 0;
			//printcool($store);
			if (isset($store->Store->CustomCategories->CustomCategory)) $this->_storecatting($store->Store->CustomCategories->CustomCategory);
			//printcool ($this->cs);
			//printcool ($this->dbs);

			$this->db->where("wsc_title <>", "ACTIONS");
			$this->db->orderby('listorder', 'ASC');
			$categories = $this->db->get($this->categorydb)->result_array();

			if (count($categories) > 0)
			{
				foreach ($categories as $c)
				{
					if (isset($this->dbs[$c['wsc_id']]))
					{
						if ($c['wsc_title'] != $this->dbs[$c['wsc_id']]['name'] || (int)$c['wsc_parent'] != (int)$this->dbs[$c['wsc_id']]['parent'] || (int)$c['leaf'] != (int)$this->dbs[$c['wsc_id']]['leaf'] || (int)$c['listorder'] != (int)$this->dbs[$c['wsc_id']]['listorder'])
						{
							$this->db->update($this->categorydb, array('wsc_title' => $this->dbs[$c['wsc_id']]['name'], 'wsc_parent' => (int)$this->dbs[$c['wsc_id']]['parent'], 'leaf' => (int)$this->dbs[$c['wsc_id']]['leaf'], 'path' => $this->dbs[$c['wsc_id']]['path'],'listorder' => $this->dbs[$c['wsc_id']]['listorder'],'notebay'=> 0),array('wsc_id' => $c['wsc_id']));

							GoMail(array ('msg_title' => 'Store Category Updated @ '.CurrentTime(), 'msg_body' => printcool($this->dbs[$c['wsc_id']],'UPDATE', TRUE).printcool($c,'WAS IN DB', TRUE), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
						}
					}
					else
					{
						//$this->db->where('wsc_id', (int)$c['wsc_id']);
						//$this->db->delete($this->categorydb);
						$this->db->update($this->categorydb, array('notebay' => 1, 'path' => $c['wsc_title']),array('wsc_id' => $c['wsc_id']));
						GoMail(array ('msg_title' => 'Store Category Deleted @ '.CurrentTime(), 'msg_body' => printcool($this->dbs,'DBS', TRUE).printcool($c,'WAS IN DB', TRUE), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
					}
					unset($this->dbs[$c['wsc_id']]);
				}
			}
			if (count($this->dbs) > 0)
			{
				foreach($this->dbs as $k => $v)
				{
					//printcool ($this->dbs);
					$this->db->insert($this->categorydb, array('wsc_id' => (int)$k, 'wsc_title' => $v['name'], 'wsc_parent' => (int)$v['parent'], 'leaf' => (int)$v['leaf'], 'path' => $v['path'],'listorder' => $v['listorder']));
					GoMail(array ('msg_title' => 'Store Category Inserted @ '.CurrentTime(), 'msg_body' => printcool($v,'INSERT', TRUE), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
				}
			}


			/*if (!write_file($this->config->config['ebaypath'].'/cats.txt', $responseXml))
			{
				GoMail(array ('msg_title' => 'Unable to write Cats.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
				echo 'Unable to update Store Categories.';
			}
			else
			{
				//GoMail(array('msg_title' => 'Cats written @ '.CurrentTime(), 'msg_body' => $responseXml, 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);

			}*/
			$this->session->set_flashdata('success_msg', 'Categories Updated - '. count($categories).' in eBay');
			Redirect('Myebaymanager');
		}
	}

	function SetStore($action = 'Add', $categoryID = 0)
	{
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/SetStoreCategories.html#Request.ItemDestinationCategoryID
		switch ($action)
		{
			case 'Add':
			case 'Delete':
			case 'Move':
			case 'Rename': continue;
				break;
			default: $action = 'Add';
		}

		$this->Auth_model->CheckListings();
		if (isset($this->session->userdata['sandbox']) && $this->session->userdata['sandbox'] == 1) $production = false;
		set_time_limit(120);
		ini_set('mysql.connect_timeout', 120);
		ini_set('max_execution_time', 120);
		ini_set('default_socket_timeout', 120);
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$name = $this->input->post('name',true);
		$parent = $this->input->post('to');
		//printcool ($_POST);


		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<SetStoreCategories xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		$requestXmlBody .= "<Action>".$action."</Action>";
		if ($action == "Add" || $action == "Move") $requestXmlBody .= "<DestinationParentCategoryID>".$parent."</DestinationParentCategoryID>";
		$requestXmlBody .= "<StoreCategories>
							<CustomCategory>";
		if ($action !== "Add")$requestXmlBody .= "<CategoryID>".$categoryID."</CategoryID>";
		if ($action == "Add" || $action == "Rename") $requestXmlBody .= "<Name>".$name."</Name>";
		$requestXmlBody .= "</CustomCategory>
  							</StoreCategories>";
		$requestXmlBody .= ' </SetStoreCategories>';


		$verb = 'SetStoreCategories';
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		$responseXml = simplexml_load_string($session->sendHttpRequest($requestXmlBody));
		$status = (string)$responseXml->Status;
		if ($status == 'Complete') $this->session->set_flashdata('success_msg', 'SUCCESS');

		else { printcool ($responseXml); exit(); }

		Redirect('Myebaymanager/RefreshStoreCategories');
	}

	function _catstruct($obj, $lvl = 1,$parent = '', $parentid = 0)
	{//echo 123;
		$this->listorder++;
		$a = array();
		//printcool($this->cs);
		$a = (array)$obj;
		$cnt = 1;
		$indent = '';
		//$txtindent = '';
		//if ($lvl > 2)  printcool($a,false,'lvl-'.$lvl);

		//while ($cnt < $lvl)
		//{
		//	$txtindent .= $indent;
		//	$cnt++;
		//}
		if (isset($a['Name']) && $a['Name'] !='')
		{
			if(!isset($this->cs[$a['CategoryID']]))
			{	 if ($parent != '') $parent = $parent.' > ';
				$parent = $this->cs[$a['CategoryID']] = $parent.$a['Name'];
				$a['parent'] = $parent;
				if (isset($a['ChildCategory']) && count($a['ChildCategory']) > 0) $leaf = 1;
				else $leaf  =0;
				$this->dbs[$a['CategoryID']] = array('name' => $a['Name'], 'parent' => (int)$parentid,'leaf' => $leaf, 'path' => $parent,'listorder'=> $this->listorder);

			}
			//$parent = $this->cs[$a['CategoryID']] = $parent.$a['Name'];

		}
		if (isset($a['ChildCategory']))
		{
			//printcool ( $parent,false,'0 '.$a['CategoryID'].' '.$a['Name']);
			//printcool($lvl);
			$this->_catstruct($a['ChildCategory'],($lvl+1), $parent, $a['CategoryID']);
			//printcool ( $parent,false,'ppp '.$a['CategoryID']).' '.$a['Name'];

			if(count($a['ChildCategory']) > 0)
			{
				//printcool($parent,false,'parent1 '.$a['CategoryID'].' '.$a['Name']);
				//$parent = $parent.$a['Name'];
				foreach($a['ChildCategory'] as $s1)
				{
					$s1 = (array)$s1;
					//printcool ($s1,false,'s1');
					if (isset($s1['Name']) && $s1['Name'] !='')
					{
						$s1['parent'] = $parent.' > ';
						$s1['parentid'] = $a['ChildCategory'];
					}
					//printcool ($s1['parent'],false,'S1 2');

					$this->_catstruct($s1,($lvl+2),$parent, $a['CategoryID']);
				}
			}

		}
	}
	function _storecatting($cc)
	{
		$lvl = 1;
		foreach ($cc as $s)
		{
			$this->_catstruct($s,$lvl);
		}
		//printcool($this->cs);
	}


	function UPCManager($return = false)
	{

		if(isset($_POST) && $_POST)
		{
			$this->db->select('upc');
			$this->db->order_by('upc','ASC');
			$list = $this->db->get("warehouse_upc")->result_array();
			$colMap = array(
				0 => 'upc',
				1 => 'title',
				2 => 'asin',
				3 => 'ebay_id',
				4 => 'e_id'
			);

			$bcolMap = array(
				0 => 'UPC',
				1 => 'Title',
				2 => 'ASIN',
				3 => 'eBayID',
				4 => 'ListingID'

			);
			$out = '';
			$sout = '';



			foreach($_POST as $d)

			{
				foreach($d as $dd)
				{
					if ($dd[2] != $dd[3])
					{


						$out .= ' "'.$bcolMap[(int)$dd[1]].'" for UPC '.$list[(int)$dd[0]]['upc'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
						$sout .= $saverel[(int)$dd[0]]['upc'].'/"'.$bcolMap[(int)$dd[1]].'" Changed ';

						$this->db->update('warehouse_upc', array($colMap[(int)$dd[1]] => $dd[3]), array('upc' => $list[(int)$dd[0]]['upc']));


						$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
					}
				}
			}
			echo json_encode($out);

		}
		else
		{
			$this->db->order_by('upc','ASC');
			$list = $this->db->get("warehouse_upc")->result_array();

			$fieldset =  array(
				'headers' => "'UPC','Title', 'ASIN','eBayID', 'ListingID'",
				'width' => "100,500,80,80,50",
				'startcols' => 5,
				'startrows' => count($list),
				'autosaveurl' => "/Myebaymanager/UPCManager",
				'reloadurl' => "/Myebaymanager/UPCManager",
				'colmap' => '{readOnly: true},{},{},{},{},');


			$loaddata = '';
			if (count($list) > 0)
			{
				foreach ($list as $kr => $r)
				{
					$loaddata .= "[";
					foreach ($r as $krr => $rr)
					{
						$loaddata .= "'".addslashes($rr)."',";
						$returndata[$kr][$krr]= stripslashes($rr);
					}
					$loaddata .= "],";

				}
			}
			if ($return)
			{
				echo json_encode($returndata);

				exit();
			}

			$this->mysmarty->assign('headers', $fieldset['headers']);
			$this->mysmarty->assign('rowheaders', $fieldset['rowheaders']);
			$this->mysmarty->assign('width', $fieldset['width']);
			$this->mysmarty->assign('startcols', $fieldset['startcols']);
			$this->mysmarty->assign('startrows', $fieldset['startrows']);
			$this->mysmarty->assign('autosaveurl', $fieldset['autosaveurl']);
			$this->mysmarty->assign('reloadurl', $fieldset['reloadurl']);
			$this->mysmarty->assign('colmap', $fieldset['colmap']);
			$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));

			$this->mysmarty->assign('copyrows', count($list['data']));

			$this->mysmarty->assign('hot', TRUE);


			$this->mysmarty->view('myebay/myebay_manager_upc.html');

		}



	}
	function getebaycategoryspecifics($id = 0)
	{
		ini_set('mysql.connect_timeout',120);
		ini_set('max_execution_time',120);
		ini_set('default_socket_timeout',120);
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$this->load->model('Myebay_model');
		$distinctcats=$this->Myebay_model->GetDistinctUsedEbayCategories();

		/*$begin='<?xml version="1.0" encoding="utf-8"?>
<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">

  <CategorySiteID>0</CategorySiteID>

  <ViewAllNodes>TRUE</ViewAllNodes>


 <RequesterCredentials><eBayAuthToken>'.$userToken.'</eBayAuthToken></RequesterCredentials>
  <DetailLevel>ReturnAll</DetailLevel>
			<ErrorLanguage>en_US</ErrorLanguage>
			<Version>'.$compatabilityLevel.'</Version>
</GetCategoriesRequest>';
		//printcool($distinctcats[0]);
		$verb='GetCategories';
		$session=new eBaySession($userToken,$devID,$appID,$certID,$serverUrl,$compatabilityLevel,$this->config->config['ebaysiteid'],$verb);
		$responseXml=simplexml_load_string($session->sendHttpRequest($begin));
		foreach($responseXml->CategoryArray->Category as $cc)
		{
			if(isset($cc->LeafCategory)) $leaf=1;
			else $leaf=0;
			$this->db->insert('ebaydata_categories_new',array('catID'=> (string)$cc->CategoryID,
				'catName'=> (string)$cc->CategoryName,
				'CategoryLevel'=>(string)$cc->CategoryLevel,
				'parentID'=>(string)$cc->CategoryParentID,
				'LeafCategory'=>$leaf));
			$livecats[(string)$cc->CategoryID]=true;
		}
		//printcool($responseXml);

		foreach($distinctcats[0] as $c)
		{
			if (!isset($livecats[$c['catID']]))	echo '<br> NOT SET';
			else echo '<br> IS SET';
		}
		exit();
*/
		$cn =1;
/*
		$requestXmlBody2='<?xml version="1.0" encoding="utf-8"?>
				<GetCategorySpecificsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
					<RequesterCredentials><eBayAuthToken>'.$userToken.'</eBayAuthToken></RequesterCredentials>
					<CategorySpecific>
    				<CategoryID>53159</CategoryID>
  					</CategorySpecific>
				</GetCategorySpecificsRequest>';
		//<CategorySpecificsFileInfo> boolean </CategorySpecificsFileInfo>
		$verb='GetCategorySpecifics';
		$session=new eBaySession($userToken,$devID,$appID,$certID,$serverUrl,$compatabilityLevel,$this->config->config['ebaysiteid'],$verb);
		$request = $session->sendHttpRequest($requestXmlBody2);
		$responseXml2=simplexml_load_string($request);
		*/
		//printcool($responseXml2);echo $request;

		/*
		 *
		 * https://developer.ebay.com/devzone/XML/docs/Reference/eBay/GetCategorySpecifics.html#Samples
		 *
This sample retrieves recommended Item Specifics for a category on the eBay US site. If recommendations are available for the category, each NameRecommendation node returns the recommended name, validation rules, and recommended values (if any). (Categories that support custom Item Specifics do not necessarily have recommendations. So, this call does not always return data.)

		*/


		$this->load->helper('exploreoriginal');
		ini_set('memory_limit','4096M');
		set_time_limit(900);

		foreach ($distinctcats[0] as $c)
		{
			$usablecats[(int)$c['catID']] = $c['catName'];
			if($cn < 2)
		{
			$requestXmlBody='<?xml version="1.0" encoding="utf-8"?>
		<GetCategoryFeaturesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
			<RequesterCredentials><eBayAuthToken>'.$userToken.'</eBayAuthToken></RequesterCredentials>
			<AllFeaturesForCategory>TRUE</AllFeaturesForCategory>
			<CategoryID>'.(int)$c['catID'].'</CategoryID>
			
			<DetailLevel>ReturnAll</DetailLevel>	
			<ErrorLanguage>en_US</ErrorLanguage>	
			<Version>'.$compatabilityLevel.'</Version>
		</GetCategoryFeaturesRequest>
		';
			$verb='GetCategoryFeatures';
			$session=new eBaySession($userToken,$devID,$appID,$certID,$serverUrl,$compatabilityLevel,$this->config->config['ebaysiteid'],$verb);
			$responseXml=simplexml_load_string($session->sendHttpRequest($requestXmlBody));
			//echo '<strong>'.$c['catName'].'</strong>';
			//echo '<br> Variations Enabled: '.(string)$responseXml->SiteDefaults->VariationsEnabled;

			if((string)$responseXml->SiteDefaults->ItemSpecificsEnabled == 'Enabled')
			{
				//echo '<br> Item Specifics: '.(string)$responseXml->SiteDefaults->ItemSpecificsEnabled;
				//echo '<br>';
				$usablecats[(int)$c['catID']] = TRUE;

				$cn++;
			}
		}
		}


		$requestXmlBody2='<?xml version="1.0" encoding="utf-8"?>
				<GetCategorySpecificsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
					<RequesterCredentials><eBayAuthToken>'.$userToken.'</eBayAuthToken></RequesterCredentials>
					<CategoryID>'.(int)$c['catID'].'</CategoryID>
					  <IncludeConfidence>TRUE</IncludeConfidence>
					   <CategorySpecificsFileInfo>TRUE</CategorySpecificsFileInfo>
				</GetCategorySpecificsRequest>';
		//<CategorySpecificsFileInfo> boolean </CategorySpecificsFileInfo>
		$verb='GetCategorySpecifics';
		$session=new eBaySession($userToken,$devID,$appID,$certID,$serverUrl,$compatabilityLevel,$this->config->config['ebaysiteid'],$verb);
		$responseXml2=simplexml_load_string($session->sendHttpRequest($requestXmlBody2));
		//printcool($responseXml2,false,'GetCategorySpecifics');
		//$this->_DownloadBulkFile($responseXml2->TaskReferenceID, $responseXml2->FileReferenceID,(int)$c['catID']);

		$dir = $this->config->config['ebaypath'].'Cats_specs/';
		foreach (glob($dir."*.xml") as $file) {
			$handle = fopen($file, "r");
			$allcats = simplexml_load_string(fread($handle, filesize($file)));
			fclose($handle);
			$recomendatons = $allcats->GetCategorySpecificsResponse->Recommendations;
			foreach ($allcats->Recommendations as $rc)
			{

				if (isset($usablecats[(int)$rc->CategoryID]))
				{
					echo '<div style="border:1px solid; padding:5px;"> <h1>CATEGORY: '.$usablecats[(int)$rc->CategoryID].' ('.(int)$rc->CategoryID.'):</h1> ';
					foreach($rc->NameRecommendation as $nr)
					{
						echo '<div style="border:1px solid; padding:5px;"> <h2>ItemSpecific: <strong>"'.(string)$nr->Name.'"</strong></h2>'; ;

						echo '<strong>Validation Rules: </strong> ';
						echo 'MaxValues: ';
						echo (string)$nr->ValidationRules->MaxValues;
						echo ' | ';
						echo 'SelectionMode: ';
						echo (string)$nr->ValidationRules->SelectionMode;
						echo ' | ';
						echo 'VariationSpecifics: ';
						echo (string)$nr->ValidationRules->VariationSpecifics;
						echo ' | ';

						echo '<br><strong>Recomendations:</strong> ';
						foreach($nr->ValueRecommendation as $vr)
						{

							echo $vr->Value.' | ';
						}
						echo "</div>";

					}echo "</div><br>";
				}

			}
		}
	}


	function _DownloadBulkFile($taskReferenceId, $fileReferenceId, $categoryid)
	{
		require_once($this->config->config['ebaypath'].'LMS/ServiceEndpointsAndTokens.php');
		require_once($this->config->config['ebaypath'].'LMS/LargeMerchantServiceSession.php');
		require_once($this->config->config['ebaypath'].'LMS/DOMUtils.php') ;
		require_once($this->config->config['ebaypath'].'LMS/PrintUtils.php');
		$session = new LargeMerchantServiceSession('XML','XML', ENV_PRODUCTION);
		$request  = '<downloadFileRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">';
		$request .= '<taskReferenceId>' . (int)$taskReferenceId . '</taskReferenceId>';
		$request .= '<fileReferenceId>' . (int)$fileReferenceId . '</fileReferenceId>';
		$request .= '</downloadFileRequest>';
		$response = $session->sendFileTransferServiceDownloadRequest($request);
		//$debug = $response;
		$responseXML = $this->parseForResponseXML($response);
		$responseDOM = DOMUtils::createDOM($responseXML);
		//PrintUtils::printDOM($responseDOM);
		$uuid = $this->parseForXopIncludeUUID($responseDOM);
		$fileBytes = $this->parseForFileBytes($uuid, $response);
		//printcool(simplexml_load_string($fileBytes));
		$this->writeZipFile($fileBytes, $this->config->config['ebaypath'].'Cats_specs/'.$categoryid.'.zip');
		$zip = new ZipArchive;
		$res = $zip->open($this->config->config['ebaypath'].'Cats_specs/'.$categoryid.'.zip');
		if ($res === TRUE) {
			$zip->extractTo($this->config->config['ebaypath'].'Cats_specs/');
			$zip->close();

		} else {
			echo 'doh!';
		}
	}








	function parseForErrorMessage($response)
	{
		$beginErrorMessage = strpos($response, '<?xml');
		$endErrorMessage = strpos($response, '</errorMessage>', $beginErrorMessage);
		$endErrorMessage += strlen('</errorMessage>');

		return substr($response, $beginErrorMessage, $endErrorMessage - $beginErrorMessage);
	}

	/**
	 * Parses for the XML Response in the MIME multipart message.
	 * @param string $response MIME multipart message
	 * @return string XML Response
	 */
	function parseForResponseXML($response)
	{
		$beginResponseXML = strpos($response, '<?xml');

		$endResponseXML = strpos($response, '</downloadFileResponse>',
			$beginResponseXML);

		//Assume a service level error and die.
		if($endResponseXML === FALSE) {
			$errorXML = $this->parseForErrorMessage($response);
			PrintUtils::printXML($errorXML);
			die();
		}

		$endResponseXML += strlen('</downloadFileResponse>');

		return substr($response, $beginResponseXML,
			$endResponseXML - $beginResponseXML);
	}

	/**
	 * Parses for the file bytes between the MIME boundaries.
	 * @param $uuid UUID corresponding to the Content-ID of the file bytes.
	 * @param string $response MIME multipart message
	 * @return string bytes of the file
	 */
	function parseForFileBytes($uuid, $response)
	{
		$contentId = 'Content-ID: <' . $uuid . '>';

		$mimeBoundaryPart = strpos($response,'--MIMEBoundaryurn_uuid_');

		$beginFile = strpos($response, $contentId, $mimeBoundaryPart);
		$beginFile += strlen($contentId);

		//Accounts for the standard 2 CRLFs.
		$beginFile += 4;

		$endFile = strpos($response,'--MIMEBoundaryurn_uuid_',$beginFile);

		//Accounts for the standard 1 CRLFs.
		$endFile -= 2;

		$fileBytes = substr($response, $beginFile, $endFile - $beginFile);

		return $fileBytes;
	}

	/**
	 * Parses the XML Response for the UUID to ascertain the
	 * index of the file bytes in the MIME Message.
	 * @param DomDocument $responseDOM DOM of the XML Response.
	 * @return string UUID referring to the message body
	 */
	function parseForXopIncludeUUID($responseDOM)
	{
		$xopInclude = $responseDOM->getElementsByTagName('Include')->item(0);
		$uuid = $xopInclude->getAttributeNode('href')->nodeValue;
		$uuid = substr($uuid, strpos($uuid,'urn:uuid:'));

		return $uuid;
	}

	/**
	 * Writes the response file's bytes to disk.
	 * @param string $bytes bytes comprising a file
	 * @param string $zipFilename name of the zip to be created
	 */
	function writeZipFile($bytes, $zipFilename)
	{
		echo "<p><b>Writing File to $zipFilename : ";

		$handler = fopen($zipFilename, 'wb')
		or die("Failed. Cannot Open $zipFilename to Write!</b></p>");
		fwrite($handler, $bytes);
		fclose($handler);

		echo 'Success.</b></p>';
	}
}