<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mycompetitor extends Controller
{
    function Mycompetitor()
	{
		parent::Controller();
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		$this->load->model('Mycompetitor_model');
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Competitor');
	}
    function index()
	{
       
    
	}
	
    function RuleMod($eid=0,$cid=0)
    {
			//echo "<p>enter RuleMod";

            $update = 0;
            if ((int)$cid > 0) $update = $cid;

            $price_change_value = (float)$this->input->post('price_change_value', true);
            $competitor_item_id = (int)$this->input->post('competitor_item_id', true);
            $time_delay = $this->input->post('time_delay', true);
		    $inform = (int)$this->input->post('inform', true);
            $notes = $this->input->post('notes', true);
            $changetype = $this->input->post('changetype', true);

            if ((int)$inform != 1) $inform = 0;
            else $inform = 1;
            		
            //if ((int)$time_delay != 1) $time_delay = 0;
            //else $time_delay = 1;

            //if($changevalue<0.01 or ((int)$eid == 0 and (int)$storecat == 0 and $predefined == 0) or ((int)$eid>0 and (int)$storecat>0) or (int)$daystocheck==0)
            //{ 
            //    exit('Wrong parameter in RuleMod function!');
            //}
           
            //echo "<p>Competitor Item Id = ".$competitor_item_id;
            //echo "<p>Competitor eid = ".$eid;
           // echo "<p>".$_POST['competitor_item_id'];
            //echo "<p>Time delay - ".$time_delay;
            //echo "<p>Inform - ".$inform;
            //echo "<p>Notes - ".$notes;
            //echo "<p>Changetype - ".$changetype;

             $array = array(
				'e_id' => (int)$eid,
                'adminassigned' => $this->session->userdata['admin_id'],
                'competitor_item_id'=> $competitor_item_id,
                'price_change_value' => (float)$price_change_value,
                'changetype' => (int)$changetype,
                //'competitor_price' => 
                'time_delay' => $time_delay,
                //'hasrun' => 
                'rulecreateddate' => CurrentTimeR(),		    
                //'last_applied_lower_price'
                //'last_applied_lower_price_mk'				
				'inform' => $inform,			
				'notes' => $notes,
			
                    );
				
			/*	if ((int)$hours == 1)	
				{
				 $array['runnext'] = date("Y-m-d H:i:s", time()+((int)$daystocheck*3600));
				 $array['runnextmk'] = mktime()+((int)$daystocheck*3600);	
				}
				else
				{
					
				 $array['runnext'] = date('Y-m-d', strtotime("+".(int)$daystocheck." days"));
				 $array['runnextmk'] = mktime()+((int)$daystocheck*3600*24);
				}
				*/
            if ((int)$update == 0)
		    {
			    $this->db->insert('competitor_rules', $array);
			    $array['cid'] = $this->db->insert_id();
		    }
            else
		    {
				unset($array['rulecreateddate']);	
			    $this->db->update('competitor_rules', $array, array('cid' => (int)$update));
			    $array['cid'] = $update;
		    }
		    $this->load->model('Myebay_model');
		    $this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
       	    $this->mysmarty->assign('ar', $array);
	   	   echo $this->mysmarty->fetch('myebay/myebay_show_loop_competitor.html');
    }
	function Predefined()
	{
		  $array = array(
		    'rid' => 0,
            'e_id' => 0,
            'storecat_id' => 0,
            'changevalue' => 0,
            'daystocheck' => 0,
            'isamount' => 0,
            'rununtil' => 0,
			'runtimes' => NULL,
            'adminassigned' => $this->session->userdata['admin_id'],
            'rulecreateddate' => '',
			'inform' => 0,
			'hours' => 0,
			'notes' => '',
			'dispose' => 0
			);
        $this->mysmarty->assign('ar', $array);
        $this->mysmarty->assign('prules', $this->Myautopilot_model->GetPredefined());
		$this->load->model('Myebay_model');
		$this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
		$this->mysmarty->view('myautopilot/myautopilot_predefined.html');
	}
    function NewMod($eid=0,$competitor_item_id=0, $changetype=0, $price_change_value=0 )
    {
	    $array = array(
		    'cid' => 0,
            'e_id' => (int)$eid,
            'adminassigned' => $this->session->userdata['admin_id'],
            'competitor_item_id' => (int)$competitor_item_id,
            'changetype' => 0,
            //competitor_price
            'price_change_value' => 0,
            'time_delay' => 0,
            'hasrun' => 0,
            'rulecreateddate' => '',
            'runnext' => '',
			'runtimes' => NULL,
            'inform' => 0,
            'last_applied_lower_price' => '',
            'last_applied_lower_price_mk' => '',
			'hours' => 0,
			'notes' => '');

	    $this->load->model('Myebay_model');
		$this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
        $this->mysmarty->assign('ar', $array);
        echo $this->mysmarty->fetch('myebay/myebay_show_loop_competitor.html');
    }
    function ShowRules($page = '')
	{
        $rules = $this->Mycompetitor_model->GetRules((int)$page);
        $this->mysmarty->assign('rules', $rules['results']);
        $this->mysmarty->assign('pages', $rules['pages']);
        $this->mysmarty->assign('page', (int)$page);
        $this->mysmarty->assign('nosmallmenu', TRUE);
        $this->load->model('Myebay_model');
        $this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
        $this->mysmarty->view('mycompetitor/mycompetitorrules.html');
	}
    function DeleteRule($cid)
	{
        $this->db->query("DELETE FROM competitor_rules WHERE cid = ".(int)$cid);
	}
	function Logs($page = 0, $listingid = 0)
	{
		$logs =  $this->Mycompetitor_model->Logs((int)$page, $listingid);
		$this->mysmarty->assign('logs', $logs['results']);
		$this->mysmarty->assign('pages', $logs['pages']);
		$this->mysmarty->assign('page', (int)$page);
		$this->session->set_userdata('page', (int)$page);
		$this->mysmarty->view('mycompetitor/mycompetitor_log.html');
	}	
	
	
	
	
	
	
	///////AUTO
	
	function DoComparePrices()
    {
        //   
        // This CRON job updates (if its lower ) competitor's price for an item based on given ebay id (entered manualy by the staff)!
        //
        require($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $this->load->model('Myebay_model');
       /* $this->db->select('ebay.e_id, buyItNowPrice, ebay_id, competitor_rules.competitor_item_id, ebended, price_ch1, 	price_change_value, competitor_price, changetype');
      //  $this->db->from('ebay');
        $this->db->join('competitor_rules', 'ebay.e_id = competitor_rules.e_id');
        $this->db->where("ebended = ''");
        $this->db->where("competitor_rules.competitor_item_id > 0");		
        $res = $this->db->get('ebay');
		*/
		$res = $this->db->query('SELECT distinct c.*, e.e_id, e.buyItNowPrice, e.ebay_id, e.ebended, e.price_ch1 FROM (ebay e) LEFT JOIN competitor_rules c ON e.e_id = c.e_id WHERE e.e_id = c.e_id AND e.ebended IS NULL AND e.ebay_id > 0'); 
		//printcool ($res->result_array());exit();
        //echo $this->db->last_query();
        if ($res->num_rows() > 0) {
			
			GoMail(array ('msg_title' => 'Competitor DoComparePrices Run ('.$res->num_rows().') @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
			
           // echo "<p>works";
           // $rev = $res->result_array();
       // } else {
        //    echo "<p>exit";
       //     $rev = false;
       //     log_message('error', 'Cron job DoComparePrices() - Did not find any listings to check for lower competitors prices!' . CurrentTime());
       //     exit;
        //}
        // First Test
        //$id = '172381819562';
        //$this->load->model('Myebay_model');
        //$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?\>';
        //$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        //$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        //$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        //$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        //$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
        //$requestXmlBody .= '<ItemID>'.(int)$id.'</ItemID></GetItemRequest>';
        //$verb = 'GetItem';
        //$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        //$responseXml = $session->sendHttpRequest($requestXmlBody);
        //if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
        //$xml = simplexml_load_string($responseXml);	
        //echo 'Test->'.(float)$xml->Item->StartPrice;
        //if ($r->num_rows() > 0)
        //{
        //    set_time_limit(600);
        //    ini_set('mysql.connect_timeout', 600);
        //    ini_set('max_execution_time', 600);  
        //    ini_set('default_socket_timeout', 600); 
        //    $revs = $r->result_array();
        //    require($this->config->config['ebaypath'].'get-common/keys.php');
        //    require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
        //    $this->load->model('Myebay_model');
        //    $id = '201696022610';				
        //    $item = $this->Myebay_model->GetItem((int)$id);	
        //    log_message('error', 'REVISE START '.(int)$id.' @ '.CurrentTime());
        //    if (!$item) 
        //    { 
        //        echo 'Item not found!';  
        //    }
        //    elseif ((int)$item['ebay_id'] == 0) $this->db->insert('ebay_revise_log', array('eid'=>$id,'type'=>$rev['e_type'],'value'=>'X','oldvalue'=>'X','attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => 'Not Commited, Listing Not Submited To eBay', 'place'=> $rev['place'], 'admin' => $rev['admin']));
        //    elseif ($item['ebended'] != '') $this->db->insert('ebay_revise_log', array('eid'=>$id,'type'=>$rev['e_type'],'value'=>'X','oldvalue'=>'X','attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => 'Not Commited, Listing Is Ended', 'place'=> $rev['place'], 'admin' => $rev['admin']));
        //    else
        //    {			
        //            $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        //            $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        //            $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        //            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        //            $requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
        //            $requestXmlBody .= '<ItemID>'.(int)$item['ebay_id'].'</ItemID></GetItemRequest>';
        //            $verb = 'GetItem';
        //            $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        //            $responseXml = $session->sendHttpRequest($requestXmlBody);
        //            if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
        //            $xml = simplexml_load_string($responseXml);
        //            log_message('error', 'REVISE 1 '.(int)$id.' @ '.CurrentTime());
        //            if ((string)$xml->Item->ItemID == '') 
        //            { 
        //                log_message('error', 'ERROR: Invalid Item ID... '.(int)$id.' @ '.CurrentTime()); 
        //                echo 'ERROR: Invalid Item ID...'; 
        //                if ($rev['e_type'] == 'p') $newebayvalue = $item['price_ch1']; 
        //                else  $newebayvalue = (int)$item['qn_ch1'];
        //                //$this->db->insert('ebay_revise_log', array('eid'=>$id,'type'=>$rev['e_type'],'value'=>$newebayvalue,'oldvalue'=>"?",'attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => 'ERROR: Invalid Item ID...', 'sev' => 1, 'place'=> $rev['place'], 'admin' => $rev['admin']));
        //                //GoMail(array ('msg_title' => 'ERROR: Invalid Item ID... '.(int)$id.' / '.$item['ebay_id'].' @'.CurrentTime(), 'msg_body' => explore($xml,false).explore($item, false).explore($requestXmlBody, false), 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
        //            }
        //            else 
        //            {
        //                if ($rev['e_type'] == 'p') 
        //                {
        //log_message('error', 'REVISE 2p '.(int)$id.' @ '.CurrentTime());
        //                    $oldebayvalue = (string)$xml->Item->StartPrice;
        //                    $newebayvalue = $item['price_ch1'];
        //                }
        //                else
        //                {
        //                    //log_message('error', 'REVISE 2q '.(int)$id.' @ '.CurrentTime());
        //                     $oldebayvalue = (int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold;
        //                     $newebayvalue = (int)$item['qn_ch1'];	
        //                     if((int)$item['qn_ch1'] == 0 && $item['ebended'] == '') $this->db->update('ebay', array('ostock' => CurrentTime()), array('e_id' => (int)$id));				 
        //                }
        //            }
        //        }	
        //    }
       // if ($rev) {
            //echo "<p>Enter if";
			$echo = '';
            set_time_limit(600);
            ini_set('mysql.connect_timeout', 600);
            ini_set('max_execution_time', 600);
            ini_set('default_socket_timeout', 600);
            //UPDATE `ebay` SET `competitor_item_id` =172381812796 where ebay_id>0 and buyItNowPrice>0 LIMIT 100 
            //SELECT count( * ) FROM `ebay` WHERE `competitorLowerPrice` != ''
            //SELECT `buyItNowPrice` , `competitorLowerPrice` FROM `ebay` WHERE `competitorLowerPrice` != ''
            $count_lowest_prices = 0;
            $count_higher_prices = 0;
            foreach ($res->result_array() as $k => $revs)
			{
                $id                 = $revs['e_id'];
                $ebay_id            = $revs['ebay_id'];
                $competitor_item_id = $revs['competitor_item_id'];
                $changetype         = $revs['changetype'];
                $price_ch1          = $revs['price_ch1'];
                $price_change_value = $revs['price_change_value'];
                $competitor_price   = $revs['competitor_price'];
				$cid = $revs['cid'];
				$admin = $revs['adminassigned'];
				$hours = $revs['time_delay'];
				$from = 0;
				$to = 0;
				$echo .= '<h4><strong>'.($k+1).'.</strong> Starting Check From Listing <strong>'.$id.'</strong> to Competitor ItemID <strong>'.$competitor_item_id.'</strong></h4>';
                if ($ebay_id == '' || $competitor_item_id == '')
                    continue;
                $item           = $this->Myebay_model->GetItem((int) $id);
                //$item = $this->Myebay_model->GetItem((int)172186226202);	
                $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>
';
                $requestXmlBody .= '
<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
                $requestXmlBody .= "
  <RequesterCredentials>
    <eBayAuthToken>$userToken</eBayAuthToken>
  </RequesterCredentials>
  ";
                $requestXmlBody .= '
  <DetailLevel>ItemReturnAttributes</DetailLevel>
  ';
                $requestXmlBody .= '
  <ErrorLanguage>en_US</ErrorLanguage>
  ';
                $requestXmlBody .= "
  <Version>$compatabilityLevel</Version>
  ";
                //TODO: Change $ebay_id with $competitor_item_id when the project is in production
                $requestXmlBody .= '
  <ItemID>' . (int) $competitor_item_id . '</ItemID>
</GetItemRequest>
';
                $verb        = 'GetItem';
                $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
                $responseXml = $session->sendHttpRequest($requestXmlBody);
                if (stristr($responseXml, 'HTTP 404') || $responseXml == '') {
                    log_message('error', 'DoComparePrices() - GetItem doesn\'t return eBay competitor\'s id of the listing ' . (int) $id . ' @ ' . CurrentTime());
                    continue;
					$echo .= '<Br>No competitor ID returned by Ebay for listing '.(int)$id;
                }
                $xml                = simplexml_load_string($responseXml);
                $competitorNewPrice = (float) $xml->Item->StartPrice;
                //if((float)$xml->Item->StartPrice < (float)$item['buyItNowPrice'])
                //echo "<br>eBay Price comp () - ".(float)$xml->Item->StartPrice.". Our price is ".(float)$price_ch1;
                //Our price will be reduced
                if ((float) $xml->Item->StartPrice < (float) $price_ch1 && (float) $price_ch1 > 0) {
                    $count_lowest_prices++;
                    //amount 
					$echo .= '<strong>&darr; CASE IS LOWER PRICE &darr;</strong><br><br>';
					
                    if ($changetype == 0) {
                        $echo .= "<strong>Rule is Amount.</strong> <br><span style='color:red;'>Competitor price is $" . $competitorNewPrice . ".</span><br><span style='color:blue;'>Our price is $" . (float) $price_ch1 . ".</span><br><span style='color:green;'>Our new price will be $" . ((float)$competitorNewPrice - (float) $price_change_value) . "." . "</span><br>Action amount is $" . (float) $price_change_value;
						$from = $price_ch1;
						$to = ((float)$competitorNewPrice - (float) $price_change_value); 
                    }
                    //margin 
                    elseif ($changetype == 1) {
                        // echo "<br>Margin.Competitor price is ".(float)$competitorNewPrice;
                        if ((float) $competitorNewPrice < (float) $competitor_price AND isset($competitor_price) AND (float) $competitor_price > 0) {
                            $echo .= "<strong>Rules is Margin. </strong><br><span style='color:red;'>Competitor price is $" . (float) $competitorNewPrice . "</span><br><span style='color:blue;'>Our price is $" . (float) $price_ch1 . ".</span><br><span style='color:green;'>Our new price will be $" . ((float) $price_ch1 - ((float) $competitor_price - (float) $competitorNewPrice)) . ".</span> Competitor Margin Is ($" . (float) $competitor_price . " - $".(float) $competitorNewPrice." = $".((float) $competitor_price - (float) $competitorNewPrice);
							
							$from = $price_ch1;
							$to = ((float) $price_ch1 - ((float) $competitor_price - (float) $competitorNewPrice)); 

                        }
						elseif ((float) $competitor_price = 0)
						{
							echo "<br><span style='color:red;'>We do not have old competitor price value in order to calculate margin</span>";								
						}
                    }
                    //fixed 
                    else {
                        $echo .= "<br>
                            <strong>Rule is Fixed Value</strong><br><span style='color:red;'>Competitor price is  $" . (float) $competitorNewPrice . "</span><br>
							 <span style='color:blue;'>Our price is $" . (float) $price_ch1 . ".</span><br>
							 <span style='color:green;'>Our new price will be fixed to $" . (float) $price_change_value."</span>";
							 
							 $from = $price_ch1;
							$to = (float) $price_change_value; 
                    }
					$echo .= "<br><br><span style='color:orange'>Saving Competitor Price Now!</span>";
                    $this->db->query('UPDATE competitor_rules SET competitor_price = '.$competitorNewPrice.'
                                        ,last_applied_lower_price=\''.CurrentTime().'\''.
                                        ',last_applied_lower_price_mk='.mktime().
                                        ' WHERE e_id = '.(int)$id);    
                    
                }
                //Our price will be lifted
                if ((float) $xml->Item->StartPrice > (float) $item['price_ch1']) {
                    $count_higher_prices++;
                    //echo '<p>Price is higher or equal, not implemented functionality yet!';
                    //amount
					$echo .= '<strong>&uarr; CASE IS HIGHER PRICE &uarr;</strong><br><br>';
                    if ($changetype == 0) {
                        
						/*$echo .= "<br>
  Amount. Competitor price is " . $competitorNewPrice . ", our price is " . (float) $price_ch1 . ". Our new price is " . (float) $price_change_value . "." . " Amount, amount = " . (float) $price_change_value;*/
  						
						$echo .= "<strong>Rule is Amount.</strong> <br><span style='color:red;'>Competitor price is $" . $competitorNewPrice . ".</span><br><span style='color:blue;'>Our price is $" . (float) $price_ch1 . ".</span><br><span style='color:green;'>Our new price will be $" . ((float)$competitorNewPrice + (float) $price_change_value) . "." . "</span><br>Action amount is $" . (float) $price_change_value;
						
						$from = $price_ch1;
						$to = ((float)$competitorNewPrice + (float) $price_change_value); 
                    }
                    //margin
                    elseif ($changetype == 1) {
                        // echo "<br>Margin.Competitor price is ".(float)$competitorNewPrice;
                        if ((float) $competitorNewPrice > (float) $competitor_price AND isset($competitor_price) AND (float) $competitor_price > 0) {
                        
						   /* $echo .= "<br>
  Margin. Competitor price is " . (float) $competitorNewPrice . ", our price is " . (float) $price_ch1 . ". Our new price is " . ((float) $price_ch1 + ((float) $competitorNewPrice - (float) $competitor_price)) . ". Margin. Old competitor price is " . (float) $competitor_price . ". New competitor price is " . (float) $competitorNewPrice;*/
  
   							 $echo .= "<strong>Rules is Margin. </strong><br><span style='color:red;'>Competitor price is $" . (float) $competitorNewPrice . "</span><br><span style='color:blue;'>Our price is $" . (float) $price_ch1 . ".</span><br><span style='color:green;'>Our new price will be $" . ((float) $price_ch1 + ((float) $competitorNewPrice-(float) $competitor_price)) . ".</span> Competitor Margin Is ($" . (float) $competitorNewPrice . " - $".(float) $competitor_price." = $".((float) $competitorNewPrice - (float) $competitor_price);
							 
							 $from = $price_ch1;
								$to = ((float) $price_ch1 + ((float) $competitorNewPrice-(float) $competitor_price)); 
						
                        }
						elseif ((float) $competitor_price = 0)
						{
							echo "<br><span style='color:red;'>We do not have old competitor price value in order to calculate margin</span>";								
						}
                    }
                    //fixed
                    else { $echo .= "<br><strong>Rule is Fixed Value</strong><br><span style='color:red;'>Scenario is unavailable for price rising</span>";
                        //$echo .= "<p>Fixed. Competitor price is  " . (float) $competitorNewPrice . ", our price is " . (float) $price_ch1 . ". Our new price is " . ((float) $price_ch1 + (float) $price_change_value) . ". Fixed, amount to subtract = " . (float) $price_change_value.'</p>';
                    }
                }
            }
            $echo .= "<h3 style='color:purple;'>Competitor's have <span style='font-size:25px;'>$count_lowest_prices</span> <strong>LOWER</strong> prices.</h3>";
            $echo .= "<h3 style='color:purple;'>Competitor's have <span style='font-size:25px;'>$count_higher_prices</span> <strong>HIGHER</strong> prices.</h3>";
            $echo .= "<h2>Have a nice day!  &#9786;</h2>";
			
			GoMail(array ('msg_title' => 'Competitor Rule Run @ '.CurrentTime(), 'msg_body' => $echo , 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
			
			//echo $echo;
			
			//SEE IF DOESNT EXIST FIRST BY EID,CID,ITEMID
			$this->db->insert('competitor_que', array(			
			'cq_eid' => (int)$id,
			'cq_itemid' =>  $ebay_id,
			'cq_cid' => $cid,
			'cq_from' => $from ,
			'cq_to' => $to,
			'cq_created' => CurrentTime(),
			'cq_createdmk' => mktime(),
			'cq_admin' => $admin,
			'cq_runat' => mktime()+(3600*$hours)			
			));
			
			
			
			
			
			
			
        }
    }
	function WorkCompetitorQue()
	{
		$nowmk = (mktime()-300);		
		$que = $this->db->query("SELECT * FROM competitor_que WHERE cq_runat >= '".$nowmk."' AND cq_runat <= '".($nowmk+3000));	
		if ($que->num_rows() > 0)
		{
			/*
			'cq_eid' => (int)$id,
			'cq_itemid' =>  $ebay_id,
			'cq_cid' => $cid,
			'cq_from' => $from ,
			'cq_to' => $to,
			'cq_created' => CurrentTime(),
			'cq_createdmk' => mktime(),
			'cq_admin' => $admin,
			'cq_runat' => mktime()+(3600*$hours)			
			*/
			
			$this->db->select("admin_id, email, ownnames");
			$query = $this->db->get('administrators');
			
			if ($query->num_rows() > 0) 
			{
				foreach ($query->result_array() as $a) $adm[$a['admin_id']] = $a;	
			}
		
			foreach ($que->result_array() as $q)
			{
				$this->db->query('UPDATE ebay SET `price_ch1` = '.$q['cq_to'].' WHERE `e_id` = '.(int)$q['cq_eid']);
				
				$this->db->insert('competitor_rules_log', array('cl_listingid' =>(int)$q['cq_eid'], 'cl_from' =>$q['cq_from'], 'cl_to' =>$q['cq_to'], 'cl_rid' =>$q['cq_cid'], 'cl_adminid' => $q['cq_admin'], 'cl_time' =>CurrentTime(), 'cl_tstime' =>mktime()));
				
					$this->load->model('Myseller_model');   
                    $this->Myseller_model->que_rev((int)$q['cq_eid'], 'p', $q['cq_to']); 
                    $ra['admin'] = $adms[$q['cq_admin']]['ownnames'];  
                    $ra['time'] = CurrentTime();  
                    $ra['ctrl'] = 'CompetitorRule';  
                    $ra['field'] = 'price_ch1';  
                    $ra['atype'] = 'M';  
                    $ra['e_id'] = (int)$q['cq_eid'];  
                    $ra['ebay_id'] = $q['cq_itemid'];  
                    $ra['datafrom'] = $q['cq_from'];  
                    $ra['datato'] = $q['cq_to'];
					$this->db->insert('ebay_actionlog', $ra);
				
				$this->db->where('cq_id', $q['cq_id']);
				$this->db->delete('competitor_que');
				
				GoMail(array ('msg_title' => 'WorkCompetitorQue Run @ '.CurrentTime(), 'msg_body' => printcool ($q, TRUE, 'CQ').printcool ($ra, TRUE, 'RA') , 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']); 
				
			}			
		}		
		$que = $this->db->query("SELECT * FROM competitor_que");
		if ($que->num_rows() == 0)
		{
			$this->db->truncate('competitor_que');
			
			GoMail(array ('msg_title' => ' Truncate CompetitorQue @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']); 
			
		}
	}
	
	
	
	
	
	
	
    function UpdateCompetitorListing($eid)
    {
        if ((isset($_POST['competitor_item_id']) && (int) $_POST['competitor_item_id'] > 0) && isset($_POST['price_change_value']) && isset($_POST['delay'])) {
            $this->db->query('UPDATE ebay SET 
  
  competitor_item_id = ' . (int) $this->input->post('competitor_item_id', TRUE) . ',price_change_value = ' . (float) $this->input->post('lower_by') . ',time_delay = ' . (int) $this->input->post('delay') . ' WHERE e_id = ' . (int) $eid);
            //competitorLowerPrice, are entered by function DoComparePrices()
            //competitor_item_id there is no enter functionality yet
        }
    }
	
	
}