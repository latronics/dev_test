<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myorders extends Controller {

	function Myorders()
	{
		parent::Controller();
		$this->load->model('Myorders_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
			
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('area', 'Orders');
		
		$this->mysmarty->assign('subtypeslist', array('p' => '[P]', 'r' => '[R]', 'pr' => '[P&amp;R]', 'u' => '[U]', 'e' => '[E]'));
		
	}
	
	function index()
	{	
		$this->_FilterTypes();
		
		$this->mysmarty->assign('counted', $this->Myorders_model->CountUncomplete());		
		$this->mysmarty->assign('list', $this->Myorders_model->ListItems());		
		$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());	
		$this->mysmarty->view('myorders/myorders_main.html');
	}
	function Sort($type = '')
	{	
		$this->_FilterTypes($type);
		
		$this->mysmarty->assign('counted', $this->Myorders_model->CountUncomplete());		
		$this->mysmarty->assign('list', $this->Myorders_model->ListItems());		
		$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());	
		$this->mysmarty->view('myorders/myorders_main.html');
	}
	function Uncomplete($type = '')
	{
		$this->_FilterTypes($type);
		$this->mysmarty->assign('uncomplete', TRUE);		
		$this->mysmarty->assign('list', $this->Myorders_model->ListUncomplete());		
		$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());	
		$this->mysmarty->view('myorders/myorders_main.html');
		
	}
	function _FilterTypes($type ='')
	{
		
	if($_POST)
		{
			
		if (isset($_POST['1'])) $this->orderselect['1'] = TRUE;	
		if (isset($_POST['2'])) $this->orderselect['2'] = TRUE;	
		if (isset($_POST['3'])) $this->orderselect['3'] = TRUE;	
		if (isset($_POST['4'])) $this->orderselect['4'] = TRUE;	
		if (isset($_POST['5'])) $this->orderselect['5'] = TRUE;	
		if (isset($_POST['6'])) $this->orderselect['6'] = TRUE;	
		if (isset($_POST['7'])) $this->orderselect['7'] = TRUE;
		if (isset($_POST['8'])) $this->orderselect['8'] = TRUE;
		if (isset($_POST['p'])) $this->orderselect['p'] = TRUE;
		if (isset($_POST['r'])) $this->orderselect['r'] = TRUE;
		if (isset($_POST['pr'])) $this->orderselect['pr'] = TRUE;
		if (isset($_POST['u'])) $this->orderselect['u'] = TRUE;
		if (isset($this->orderselect)) 
			{
			$this->session->set_userdata(array('orderselect' => $this->orderselect));			
			$this->mysmarty->assign('session',$this->session->userdata);
			}	
			else
			{
			$this->session->unset_userdata('orderselect');		
			$this->mysmarty->assign('session',$this->session->userdata);
			}
		}
	elseif ($type != '')
		{
			if ($type == '1') $this->orderselect['1'] = TRUE;	
			if ($type == '2') $this->orderselect['2'] = TRUE;	
			if ($type == '3') $this->orderselect['3'] = TRUE;	
			if ($type == '4') $this->orderselect['4'] = TRUE;	
			if ($type == '5') $this->orderselect['5'] = TRUE;	
			if ($type == '6') $this->orderselect['6'] = TRUE;	
			if ($type == '7') $this->orderselect['7'] = TRUE;
			if ($type == '8') $this->orderselect['8'] = TRUE;
			if (isset($this->orderselect)) 
			{
				$this->orderselect['p'] = TRUE;
				$this->orderselect['r'] = TRUE;
				$this->orderselect['pr'] = TRUE;
				$this->orderselect['u'] = TRUE;	
			$this->session->set_userdata(array('orderselect' => $this->orderselect));			
			$this->mysmarty->assign('session',$this->session->userdata);
			}	
			else
			{
			$this->session->unset_userdata('orderselect');		
			$this->mysmarty->assign('session',$this->session->userdata);
			}
		}
	else 
		{
			$this->session->unset_userdata('orderselect');		
			$this->mysmarty->assign('session',$this->session->userdata);
		}
	
	}
	function CompleteStatus($id = 0)
	{
		
		//if ((int)$id == 0) Redirect("Myebay/GetOrders/Site#".(int)$id);
		echo 1;
		$this->order = $this->Myorders_model->GetItem((int)$id);
		echo 2;
		//if (!$this->order) Redirect("Myebay/GetOrders/Site#".(int)$id);
				
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
							
			echo 3;				
		//Redirect("Myebay/GetOrders/Site#".(int)$id);
		
	}
	
	function Report($type = '')
	{		
	
		if ($this->session->userdata['admin_id'] != '1' && $this->session->userdata['admin_id'] != 2 && $this->session->userdata['admin_id'] != 6) { echo 'Sorry, you don\'t have clearance for here.';exit(); }
		
		
		$this->_FilterTypes($type);
				
		$listdb = $this->Myorders_model->ListReports();
		$outlettotal = 0;
		$webtotal = 0;
		$list = array();

		foreach ($listdb as $k => $v)
			{
				if (!isset($this->orderselect) || (isset($this->orderselect[$v['buytype']]) && isset($this->orderselect[$v['subtype']])))
				{
					if ($v['buytype'] == '2' || $v['buytype'] == '4' || $v['buytype'] == '6' || $v['buytype'] == '7' || $v['buytype'] == '8')
						{
							if ($v['endprice'] > 0)
							{
							
							$date = explode(' ', $v['time']);
							$date = explode('-', $date[0]);
							$month = $date[1];
							$year = $date[0];
							$list[$year][$month][$v['oid']]['title'] = $v['fname'].' '.$v['lname'];
							$list[$year][$month][$v['oid']]['date'] = $v['time'];
							$list[$year][$month][$v['oid']]['sum'] = $v['endprice'];
							$list[$year][$month][$v['oid']]['buytype'] = $v['buytype'];
							$list[$year][$month][$v['oid']]['subtype'] = $v['subtype'];
							$list[$year][$month][$v['oid']]['admin'] = $v['admin'];
							$outlettotal = $outlettotal + $list[$year][$month][$v['oid']]['sum'];
							$monouttot[$year][$month][] = $list[$year][$month][$v['oid']]['sum'];							
							}
							
						}
					elseif ($v['buytype'] == '5')
						{
							if ($v['complete'] == '1') 
								{
									$date = explode(' ', $v['time']);
									$date = explode('-', $date[0]);
									$month = $date[1];
									$year = $date[0];								
									$list[$year][$month][$v['oid']]['title'] = 'Ammendment to repair order '.$v['oid_ref'];
									$list[$year][$month][$v['oid']]['oid_ref'] = $v['oid_ref'];
									$list[$year][$month][$v['oid']]['date'] = $v['time'];
									$list[$year][$month][$v['oid']]['sum'] = $v['endprice'];
									$list[$year][$month][$v['oid']]['buytype'] = $v['buytype'];
									$list[$year][$month][$v['oid']]['subtype'] = $v['subtype'];
									$list[$year][$month][$v['oid']]['admin'] = $v['admin'];
									$webtotal = $webtotal + $list[$year][$month][$v['oid']]['sum'];
									$monwebtot[$year][$month][] = $list[$year][$month][$v['oid']]['sum'];									
								}
						}
					else 
						{
							
							if ($v['complete'] == '1') 
								{
									$date = explode(' ', $v['time']);
									$date = explode('-', $date[0]);
									$month = $date[1];
									$year = $date[0];								
									$list[$year][$month][$v['oid']]['title'] = $v['fname'].' '.$v['lname'].' - '.$v['email'];
									$list[$year][$month][$v['oid']]['date'] = $v['time'];
									$list[$year][$month][$v['oid']]['buytype'] = $v['buytype'];
									$list[$year][$month][$v['oid']]['subtype'] = $v['subtype'];
									$list[$year][$month][$v['oid']]['admin'] = $v['admin'];
									if ($v['buytype'] == '1')
										{
											$list[$year][$month][$v['oid']]['sum'] = $v['endprice'] + $v['endprice_delivery'];
											$webtotal = $webtotal + $list[$year][$month][$v['oid']]['sum'];
											$monwebtot[$year][$month][] = $list[$year][$month][$v['oid']]['sum'];
										}
									else 
										{
											$list[$year][$month][$v['oid']]['sum'] = $v['endprice'];
											$webtotal = $webtotal + $list[$year][$month][$v['oid']]['sum'];
											$monwebtot[$year][$month][] = $list[$year][$month][$v['oid']]['sum'];
											
										}
								}
						}				
				}
			}
			if (isset($monwebtot)) foreach ($monwebtot as $mwtk => $mwtv)
				{
				foreach ($mwtv as $mwtmk => $mwtmv)
					{
					$montlytotal['web'][$mwtk][$mwtmk] = array_sum($mwtmv);						
					}
					
				}
			if (isset($monouttot)) foreach ($monouttot as $motk => $motv)
				{
				foreach ($motv as $motmk => $motmv)
					{
					$montlytotal['outlet'][$motk][$motmk] = array_sum($motmv);						
					}
					
				}

			
			$buytype = array (
							  '1' => 'WebSite Sale',
							  '2' => 'Outlet Sale',
							  '3' => 'WebSite Repair',
							  '4' => 'Outlet Repair',
							  '5' => 'Ammentment',
							  '6' => 'USC',
							  '7' => 'Hawthorne',
							  '8' => 'Venice'
							  );
			
			$subtype = array (
							  'p' => 'Product',
							  'pr' => 'Prod &amp; Rep',
							  'r' => 'Repair',
							  'u' => 'Undefined'
							  );
			
			
			$this->mysmarty->assign('buytype', $buytype);
			$this->mysmarty->assign('subtype', $subtype);
			$this->mysmarty->assign('webtotal', $webtotal);
			$this->mysmarty->assign('outlettotal', $outlettotal);
			$this->mysmarty->assign('monthlytotals', $montlytotal);
			$this->mysmarty->assign('list', $list);

	
		$this->mysmarty->view('myorders/myorders_report.html');
	}

	function Delete ($id = '')
	{
		if ((int)$id > 0) $this->Myorders_model->Delete((int)$id);
		Redirect("Myorders/Uncomplete");
	}
	function PaymentProcessorLog($id = '')
	{
		if ((int)$id == 0) { Redirect("/Myorders/"); exit;}	
		$this->mysmarty->assign('data', $this->Myorders_model->GetPPLogData((int)$id));
		$this->mysmarty->view('myorders/myorders_pplog.html');		
	}
	function UpdateOrderStatuses($id = '')
	{
		if ((int)$id == 0) {redirect("/Myorders"); exit();}
		
		$this->buytype = $this->Myorders_model->GetBuytype((int)$id);
		$this->statuses = $this->Myorders_model->GetOrderStatuses((int)$id);
		$this->statuses = unserialize($this->statuses);
		
		$isoktomark = $this->CheckOutletRepairPriceExists((int)$id);
		if ($isoktomark) 
			{
				echo '<DIV align="center" style=" background:red; color:white; text-align:center; padding:20px; font-size:25px;"><span style="font-size:55px; font-weight:bolder;">HEY!!!</span><br><br>YOU MUST INPUT <u><strong>Actual Repair Price</strong></u> TO CONTINUE WITH STATUSES OF <u><strong>REPAIR COMPLETION</strong></u><br><br>Go back and fill it in first!</div>';
				exit();
			}

			if (isset($_POST['togo']) && (int)$_POST['togo'] == '1')
				{
				//Take from pending , shipped out

				$order = $this->Myorders_model->GetOnlyOrderData((int)$id);
				$historystring = '------------------------------------<br>Quantity shipped from pending:<Br><br>';
				foreach ($order as $qk => $qv)
						{				
						$this->Myorders_model->MoveQuantityOut($qv['p_id'], $qv['quantity']);
						$historystring .= $qv['quantity'].' pcs shipped from product <a href=\"'.Site_url().'Myproducts/Edit/'.$qv['p_id'].'\" target=\"_blank\">'.$qv['p_title'].' (ID - '.$qv['p_id'].') </a><br>';
						}
						$historystring .= 'Admin responsible: '.$this->session->userdata['name'];
					
					$this->Myorders_model->MarkMovementComplete((int)$id);
				}
			elseif (isset($_POST['togo']) && (int)$_POST['togo'] == '2')	
				{
				//Take from pending and return to quantity , not shipped out	
				$order = $this->Myorders_model->GetOnlyOrderData((int)$id);
				$historystring = '------------------------------------<br>Quantity returned to warehouse:<Br><br>';
				foreach ($order as $qk => $qv)
						{				
						$this->Myorders_model->MoveQuantityBack($qv['p_id'], $qv['quantity']);
						$historystring .= $qv['quantity'].' pcs returned to product <a href=\"'.Site_url().'Myproducts/Edit/'.$qv['p_id'].'\" target=\"_blank\">'.$qv['p_title'].' (ID - '.$qv['p_id'].') </a><br>';
						}
					
					$this->Myorders_model->MarkMovementComplete((int)$id);
															
					$historystring .= 'Admin responsible: '.$this->session->userdata['name'];				
				}
		
		$statusextra = '';
		
		if (isset($historystring)) $statusextra = '<br>'.$historystring;
		
		$this->newstatus = array('status' => (int)$this->input->post('status', TRUE),
								  'comment' => ' <strong>('.$this->session->userdata['name'].')</strong>: '.$this->input->post('comment', TRUE).$statusextra,
								  'notified' => (int)$this->input->post('notified', TRUE),
								  'msgclient' => $this->input->post('msgclient', TRUE),
								  'time' => CurrentTime());
		
		$this->statuses[] = $this->newstatus;
		
		$this->statuses = serialize($this->statuses);

    	$this->Myorders_model->UpdateOrderStatuses((int)$id, array('status' => $this->statuses));
		
		
		$this->clientemail = $this->Myorders_model->GetClientData((int)$id);

		$this->mysmarty->assign('buytype', $this->buytype);
		$this->mysmarty->assign('statusdata', $this->newstatus);
		$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses($this->buytype));
		$this->mysmarty->assign('id', (int)$id);

		if ($this->newstatus['notified'] == '1') 
		{
			$this->mysmarty->assign('msgclient', $this->input->post('msgclient', TRUE));
			$this->msg['msg_title'] = 'Order Status Changed @ '.FlipDateMail(CurrentTime());
			$this->msg['msg_body'] = $this->mysmarty->fetch('emails/myorders_changestatus.html');
			
			GoMail($this->msg, $this->clientemail);
		}	
			$this->msg['msg_title'] = 'Order 000'.(int)$id.' Status Changed @ '.FlipDateMail(CurrentTime());
			
			$this->mysmarty->assign('admin', TRUE);
			
			$this->msg['msg_body'] = $this->mysmarty->fetch('emails/myorders_changestatus.html');
			$this->msg['msg_date'] = CurrentTime();
	
			$this->mailid = 10;
			GoMail($this->msg);
		
		$this->load->model('Myadmin_model');
		$this->Myadmin_model->InsertHistoryData($this->msg);
	
		switch ($this->buytype)
		{
		case "1":
		Redirect("/Myorders/Show/".(int)$id);
		break;
		
		case "2":
		Redirect("/Myorders/Show/".(int)$id);
		break;
		
		case "3":
		Redirect("/Myorders/Show/".(int)$id);
		break;
		
		case "4":
		Redirect("/Myorders/Show/".(int)$id);
		default:
		Redirect("/Myorders/ShopRepair/".(int)$id);

		}
		
		
	}
	function PrintLabel ($id = 0)
	{
		if((int)$id > 0)
		{
			$data = $this->Myorders_model->GetLabelData((int)$id); 
			
			if ($data)
			{
				
							$this->load->library('image_lib'); 
							$config['source_image']	= $this->config->config['pathtopublic']."/images/label.png";
							$config['wm_text'] = '#'.$data['oid'].' '.$data['fname'].' '.$data['lname'];
							//$config['wm_text'] = '#00156 - Evan Karadimov';
							$config['wm_type'] = 'text';
							$config['dynamic_output'] = TRUE;
							$config['wm_font_size']	= '5';
							$config['wm_font_color'] = '000000';
							$config['wm_vrt_alignment'] = 'middle';
							$config['wm_hor_alignment'] = 'center';
							$config['wm_padding'] = '0';
									
							$this->image_lib->initialize($config); 								
							$this->image_lib->watermark();
			}
		}
		
	}
	function CheckOutletRepairPriceExists ($id = '')
	{		
		if ($this->buytype == 6 || $this->buytype == 7 || $this->buytype == 8)
		{
			$endprice = $this->Myorders_model->GetEndprice((int)$id);
			if (((int)$this->input->post('status') == 11 || (int)$this->input->post('status') == 12) && $endprice < 1 && ($this->session->userdata['admin_id'] != 1 && $this->session->userdata['admin_id'] != 2))
			{
			return TRUE;
			}
			else
			{
			return FALSE;	
			}		
		}
		else return FALSE;
	}
	function Show($id = '') 
	{	
		if ((int)$id == 0) { Redirect("/Myorders/"); exit;}	
		$this->order = $this->Myorders_model->GetItem((int)$id);
		
		$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses($this->order['buytype']));
		if (!$this->order) { Redirect("/Myorders/"); exit;}			
		if ($this->order['buytype'] == 1) {
			
			$this->mysmarty->assign('amendments', $this->Myorders_model->GetAmendments($this->order['oid']));
			$this->mysmarty->assign('items', $this->Myorders_model->GetItems($this->order['oid']));
			$this->order['order'] = unserialize($this->order['order']);
			
		}
		if ($this->order['buytype'] == 3) 
			{
			$this->mysmarty->assign('amendments', $this->Myorders_model->GetAmendments($this->order['oid']));
			$this->mysmarty->assign('items', $this->Myorders_model->GetItems($this->order['oid']));
			}
		foreach ($this->order['order'] as $k => $v)
		{
			$this->order['order'][$k]['idpath'] = $this->Myorders_model->GetIDPath($this->order['order'][$k]['e_id']);
		}
		
		$this->mysmarty->assign('clientdata', $this->order);
		$this->mysmarty->assign('amenderrors', $this->session->flashdata('amenderrors'));
		$this->mysmarty->assign('itemerrors', $this->session->flashdata('itemerrors'));
		$this->mysmarty->view('myorders/myorders_show.html');
	}
	function ShopOrder($id = '')
	{
		if ((int)$id > 0) $this->displays = $this->Myorders_model->GetOrder((int)$id, '2');
		else $this->displays = FALSE;
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('fname', 'First Name', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('lname', 'Last Name', 'trim|required|min_length[2]|xss_clean');
		//$this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean|min_length[10]');		
		//$this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean|max_length[50]');
		//$this->form_validation->set_rules('state', 'State', 'trim|required|xss_clean|max_length[50]');
		//$this->form_validation->set_rules('postcode', 'PostCode', 'trim|required|xss_clean|max_length[20]');
		//$this->form_validation->set_rules('country', 'Country', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('tel', 'Telephone', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('email', 'E-Mail', 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('staffcomments', 'Staff Comments', 'trim|xss_clean');
		$this->form_validation->set_rules('order', 'Purchase Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('endprice', 'Total Price', 'trim|required|xss_clean');

		
		if ($this->form_validation->run() == FALSE)
			{	
		
				$this->inputdata['fname'] = $this->input->post('fname', TRUE);
				$this->inputdata['lname'] = $this->input->post('lname', TRUE);
				//$this->inputdata['address'] = $this->input->post('address', TRUE);				
				//$this->inputdata['city'] = $this->input->post('city', TRUE);
				//$this->inputdata['state'] = $this->input->post('state', TRUE);
				//$this->inputdata['postcode'] = $this->input->post('postcode', TRUE);
				//$this->inputdata['country'] = $this->input->post('country', TRUE);
				//$this->inputdata['daddress'] = $this->input->post('daddress', TRUE);				
				//$this->inputdata['dcity'] = $this->input->post('dcity', TRUE);
				//$this->inputdata['dstate'] = $this->input->post('dstate', TRUE);
				//$this->inputdata['dpostcode'] = $this->input->post('dpostcode', TRUE);
				//$this->inputdata['dcountry'] = $this->input->post('dcountry', TRUE);
				$this->inputdata['tel'] = $this->input->post('tel', TRUE);
				$this->inputdata['email'] = $this->input->post('email', TRUE);
				$this->inputdata['staffcomments'] = $this->input->post('staffcomments', TRUE);
				$this->inputdata['order'] = $this->input->post('order', TRUE);
				$this->inputdata['endprice'] = (float)PriceUnification($this->input->post('endprice', TRUE));
	
				$this->mysmarty->assign('displays', $this->displays);
				$this->mysmarty->assign('ctr', ReturnCountries($this->config->config['language_abbr']));
				$this->mysmarty->assign('sts', ReturnStates());
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myorders/myorders_shoporder.html');
				exit();
			}
			else 
			{	
				if ((int)$id == 0) {
				$this->dbdata['buytype'] = '2';
				$this->dbdata['status'] = '';
				$this->dbdata['time'] = CurrentTime();				
				}
				
				$this->dbdata['fname'] = $this->input->post('fname');
				$this->dbdata['lname'] = $this->form_validation->set_value('lname');
				//$this->dbdata['address'] = $this->form_validation->set_value('address');				
				//$this->dbdata['city'] = $this->form_validation->set_value('city');
				//$this->dbdata['state'] = $this->form_validation->set_value('state');
				//$this->dbdata['postcode'] = $this->form_validation->set_value('postcode');
				//$this->dbdata['country'] = $this->form_validation->set_value('country');
				//$this->dbdata['daddress'] = $this->input->post('daddress', TRUE);				
				//$this->dbdata['dcity'] = $this->input->post('dcity', TRUE);
				//$this->dbdata['dstate'] = $this->input->post('dstate', TRUE);
				//$this->dbdata['dpostcode'] = $this->input->post('dpostcode', TRUE);
				//$this->dbdata['dcountry'] = $this->input->post('dcountry', TRUE);
				$this->dbdata['tel'] = $this->form_validation->set_value('tel');
				$this->dbdata['email'] = $this->form_validation->set_value('email');
				$this->dbdata['staffcomments'] = $this->form_validation->set_value('staffcomments');
				$this->dbdata['order'] = $this->form_validation->set_value('order');
				$this->dbdata['endprice'] =(float)PriceUnification($this->form_validation->set_value('endprice'));
												
					if ((int)$id > 0) $this->Myorders_model->UpdateOrder((int)$id, $this->dbdata);
					else $this->Myorders_model->InsertOrder($this->dbdata);

					redirect("/Myorders"); exit();								
			}
	}
	
	/*
	function ShopOrder($id = '')
	{
		if ((int)$id > 0) $this->displays = $this->Myorders_model->GetOrder((int)$id, '2');
		else $this->displays = FALSE;
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('fname', 'First Name', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('lname', 'Last Name', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean|min_length[10]');		
		$this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('state', 'State', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('postcode', 'PostCode', 'trim|required|xss_clean|max_length[20]');
		$this->form_validation->set_rules('country', 'Country', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('tel', 'Telephone', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('email', 'E-Mail', 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('staffcomments', 'Staff Comments', 'trim|xss_clean');
		$this->form_validation->set_rules('order', 'Purchase Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('endprice', 'Total Price', 'trim|required|xss_clean');

		
		if ($this->form_validation->run() == FALSE)
			{	
		
				$this->inputdata['fname'] = $this->input->post('fname', TRUE);
				$this->inputdata['lname'] = $this->input->post('lname', TRUE);
				$this->inputdata['address'] = $this->input->post('address', TRUE);				
				$this->inputdata['city'] = $this->input->post('city', TRUE);
				$this->inputdata['state'] = $this->input->post('state', TRUE);
				$this->inputdata['postcode'] = $this->input->post('postcode', TRUE);
				$this->inputdata['country'] = $this->input->post('country', TRUE);
				$this->inputdata['daddress'] = $this->input->post('daddress', TRUE);				
				$this->inputdata['dcity'] = $this->input->post('dcity', TRUE);
				$this->inputdata['dstate'] = $this->input->post('dstate', TRUE);
				$this->inputdata['dpostcode'] = $this->input->post('dpostcode', TRUE);
				$this->inputdata['dcountry'] = $this->input->post('dcountry', TRUE);
				$this->inputdata['tel'] = $this->input->post('tel', TRUE);
				$this->inputdata['email'] = $this->input->post('email', TRUE);
				$this->inputdata['staffcomments'] = $this->input->post('staffcomments', TRUE);
				$this->inputdata['order'] = $this->input->post('order', TRUE);
				$this->inputdata['endprice'] = (float)PriceUnification($this->input->post('endprice', TRUE));
	
				$this->mysmarty->assign('displays', $this->displays);
				$this->mysmarty->assign('ctr', ReturnCountries($this->config->config['language_abbr']));
				$this->mysmarty->assign('sts', ReturnStates());
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myorders/myorders_shoporder.html');
				exit();
			}
			else 
			{	
				if ((int)$id == 0) {
				$this->dbdata['buytype'] = '2';
				$this->dbdata['status'] = '';
				$this->dbdata['time'] = CurrentTime();				
				}
				
				$this->dbdata['fname'] = $this->input->post('fname');
				$this->dbdata['lname'] = $this->form_validation->set_value('lname');
				$this->dbdata['address'] = $this->form_validation->set_value('address');				
				$this->dbdata['city'] = $this->form_validation->set_value('city');
				$this->dbdata['state'] = $this->form_validation->set_value('state');
				$this->dbdata['postcode'] = $this->form_validation->set_value('postcode');
				$this->dbdata['country'] = $this->form_validation->set_value('country');
				$this->dbdata['daddress'] = $this->input->post('daddress', TRUE);				
				$this->dbdata['dcity'] = $this->input->post('dcity', TRUE);
				$this->dbdata['dstate'] = $this->input->post('dstate', TRUE);
				$this->dbdata['dpostcode'] = $this->input->post('dpostcode', TRUE);
				$this->dbdata['dcountry'] = $this->input->post('dcountry', TRUE);
				$this->dbdata['tel'] = $this->form_validation->set_value('tel');
				$this->dbdata['email'] = $this->form_validation->set_value('email');
				$this->dbdata['staffcomments'] = $this->form_validation->set_value('staffcomments');
				$this->dbdata['order'] = $this->form_validation->set_value('order');
				$this->dbdata['endprice'] =(float)PriceUnification($this->form_validation->set_value('endprice'));
												
					if ((int)$id > 0) $this->Myorders_model->UpdateOrder((int)$id, $this->dbdata);
					else $this->Myorders_model->InsertOrder($this->dbdata);

					redirect("/Myorders"); exit();								
			}
	}
	*/
	
/*function ShopRepair($id = '')
	{
		if ((int)$id > 0) 
		{ 
			$this->displays = $this->Myorders_model->GetOrder((int)$id, '4');
			$this->displays['status'] = unserialize($this->displays['status']);
			$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses($this->displays['buytype']));
			$this->mysmarty->assign('items', $this->Myorders_model->GetItems($this->displays['oid']));
	
		}
		else 
		{
			$this->displays = FALSE;
		}
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('fname', 'First Name', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('lname', 'Last Name', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean|min_length[10]');		
		$this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('state', 'State', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('postcode', 'PostCode', 'trim|required|xss_clean|max_length[20]');
		$this->form_validation->set_rules('country', 'Country', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('tel', 'Telephone', 'trim|required|xss_clean|max_length[50]');
		$this->form_validation->set_rules('email', 'E-Mail', 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('staffcomments', 'Staff Comments', 'trim|xss_clean');
		$this->form_validation->set_rules('order', 'Purchase Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('endprice', 'Total Price', 'trim|required|xss_clean');

		
		if ($this->form_validation->run() == FALSE)
			{	
		
				$this->inputdata['fname'] = $this->input->post('fname', TRUE);
				$this->inputdata['lname'] = $this->input->post('lname', TRUE);
				$this->inputdata['address'] = $this->input->post('address', TRUE);				
				$this->inputdata['city'] = $this->input->post('city', TRUE);
				$this->inputdata['state'] = $this->input->post('state', TRUE);
				$this->inputdata['postcode'] = $this->input->post('postcode', TRUE);
				$this->inputdata['country'] = $this->input->post('country', TRUE);
				$this->inputdata['daddress'] = $this->input->post('daddress', TRUE);				
				$this->inputdata['dcity'] = $this->input->post('dcity', TRUE);
				$this->inputdata['dstate'] = $this->input->post('dstate', TRUE);
				$this->inputdata['dpostcode'] = $this->input->post('dpostcode', TRUE);
				$this->inputdata['dcountry'] = $this->input->post('dcountry', TRUE);
				$this->inputdata['tel'] = $this->input->post('tel', TRUE);
				$this->inputdata['email'] = $this->input->post('email', TRUE);
				$this->inputdata['staffcomments'] = $this->input->post('staffcomments', TRUE);
				$this->inputdata['order'] = $this->input->post('order', TRUE);
				$this->inputdata['endprice'] = (float)PriceUnification($this->input->post('endprice', TRUE));
	
				$this->mysmarty->assign('displays', $this->displays);
				$this->mysmarty->assign('ctr', ReturnCountries($this->config->config['language_abbr']));
				$this->mysmarty->assign('sts', ReturnStates());
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->assign('itemerrors', $this->session->flashdata('itemerrors'));
				$this->mysmarty->view('myorders/myorders_shoprepair.html');
				exit();
			}
			else 
			{	
				if ((int)$id == 0) {
				$this->dbdata['buytype'] = '4';
				$this->dbdata['time'] = CurrentTime();
				$this->dbdata['status'] = serialize(array(array('status' => 1, 'comment' => '', 'notified' => 1, 'time' => CurrentTime())));
				}
				
				$this->dbdata['fname'] = $this->input->post('fname');
				$this->dbdata['lname'] = $this->form_validation->set_value('lname');
				$this->dbdata['address'] = $this->form_validation->set_value('address');				
				$this->dbdata['city'] = $this->form_validation->set_value('city');
				$this->dbdata['state'] = $this->form_validation->set_value('state');
				$this->dbdata['postcode'] = $this->form_validation->set_value('postcode');
				$this->dbdata['country'] = $this->form_validation->set_value('country');
				$this->dbdata['daddress'] = $this->input->post('daddress', TRUE);				
				$this->dbdata['dcity'] = $this->input->post('dcity', TRUE);
				$this->dbdata['dstate'] = $this->input->post('dstate', TRUE);
				$this->dbdata['dpostcode'] = $this->input->post('dpostcode', TRUE);
				$this->dbdata['dcountry'] = $this->input->post('dcountry', TRUE);
				$this->dbdata['tel'] = $this->form_validation->set_value('tel');
				$this->dbdata['email'] = $this->form_validation->set_value('email');
				$this->dbdata['staffcomments'] = $this->form_validation->set_value('staffcomments');
				$this->dbdata['order'] = $this->form_validation->set_value('order');
				$this->dbdata['endprice'] =(float)PriceUnification($this->form_validation->set_value('endprice'));
												
					if ((int)$id > 0) $this->Myorders_model->UpdateOrder((int)$id, $this->dbdata);
					else $this->Myorders_model->InsertOrder($this->dbdata);

					redirect("/Myorders"); exit();								
			}
	}*/

function ShopRepair($id = '')
	{
		if ((int)$id > 0) 
		{ 
			$this->displays = $this->Myorders_model->GetOrder((int)$id, array(6,7,8));
			$this->displays['status'] = unserialize($this->displays['status']);
			$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses($this->displays['buytype']));
			$this->mysmarty->assign('items', $this->Myorders_model->GetItems($this->displays['oid']));	
		}
		else 
		{
			$this->displays = FALSE;
		}
		
		$this->load->library('form_validation');

		//$this->form_validation->set_rules('fname', 'First Name', 'trim|required|min_length[2]|xss_clean');
		//$this->form_validation->set_rules('lname', 'Last Name', 'trim|required|min_length[2]|xss_clean');
		//$this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean|min_length[10]');		
		//$this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean|max_length[50]');
		//$this->form_validation->set_rules('state', 'State', 'trim|required|xss_clean|max_length[50]');
		//$this->form_validation->set_rules('postcode', 'PostCode', 'trim|required|xss_clean|max_length[20]');
		//$this->form_validation->set_rules('country', 'Country', 'trim|required|xss_clean|max_length[50]');
		//$this->form_validation->set_rules('tel', 'Telephone', 'trim|required|xss_clean|max_length[50]');
		//$this->form_validation->set_rules('email', 'E-Mail', 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('staffcomments', 'Staff Comments', 'trim|xss_clean');
		//$this->form_validation->set_rules('order', 'Purchase Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('endprice', 'Actual Price', 'trim|required|xss_clean');

		
		if ($this->form_validation->run() == FALSE)
			{	
		
				$this->inputdata['fname'] = $this->displays['fname'];
				$this->inputdata['lname'] = $this->displays['lname'];
				//$this->inputdata['address'] = $this->input->post('address', TRUE);				
				//$this->inputdata['city'] = $this->input->post('city', TRUE);
				//$this->inputdata['state'] = $this->input->post('state', TRUE);
				//$this->inputdata['postcode'] = $this->input->post('postcode', TRUE);
				//$this->inputdata['country'] = $this->input->post('country', TRUE);
				//$this->inputdata['daddress'] = $this->input->post('daddress', TRUE);				
				//$this->inputdata['dcity'] = $this->input->post('dcity', TRUE);
				//$this->inputdata['dstate'] = $this->input->post('dstate', TRUE);
				//$this->inputdata['dpostcode'] = $this->input->post('dpostcode', TRUE);
				//$this->inputdata['dcountry'] = $this->input->post('dcountry', TRUE);
				$this->inputdata['tel'] = $this->displays['tel'];
				$this->inputdata['email'] =$this->displays['email'];
				$this->inputdata['staffcomments'] = $this->input->post('staffcomments', TRUE);
				$this->inputdata['order'] = $this->displays['order'];
				$this->inputdata['endprice'] = (float)PriceUnification($this->input->post('endprice', TRUE));
	
				$this->mysmarty->assign('displays', $this->displays);
				$this->mysmarty->assign('ctr', ReturnCountries($this->config->config['language_abbr']));
				$this->mysmarty->assign('sts', ReturnStates());
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->assign('itemerrors', $this->session->flashdata('itemerrors'));
				$this->mysmarty->view('myorders/myorders_shoprepair.html');
				exit();
			}
			else 
			{	
				if ((int)$id == 0) {
				$this->dbdata['buytype'] = '6';
				$this->dbdata['time'] = CurrentTime();
				$this->dbdata['status'] = serialize(array(array('status' => 1, 'comment' => '', 'notified' => 1, 'time' => CurrentTime())));
				}
				
				//$this->dbdata['fname'] = $this->input->post('fname');
				//$this->dbdata['lname'] = $this->form_validation->set_value('lname');
				//$this->dbdata['address'] = $this->form_validation->set_value('address');				
				//$this->dbdata['city'] = $this->form_validation->set_value('city');
				//$this->dbdata['state'] = $this->form_validation->set_value('state');
				//$this->dbdata['postcode'] = $this->form_validation->set_value('postcode');
				//$this->dbdata['country'] = $this->form_validation->set_value('country');
				//$this->dbdata['daddress'] = $this->input->post('daddress', TRUE);				
				//$this->dbdata['dcity'] = $this->input->post('dcity', TRUE);
				//$this->dbdata['dstate'] = $this->input->post('dstate', TRUE);
				//$this->dbdata['dpostcode'] = $this->input->post('dpostcode', TRUE);
				//$this->dbdata['dcountry'] = $this->input->post('dcountry', TRUE);
				//$this->dbdata['tel'] = $this->form_validation->set_value('tel');
				//$this->dbdata['email'] = $this->form_validation->set_value('email');
				$this->dbdata['staffcomments'] = $this->form_validation->set_value('staffcomments');
				//$this->dbdata['order'] = $this->form_validation->set_value('order');
				$this->dbdata['endprice'] =(float)PriceUnification($this->form_validation->set_value('endprice'));
												
					if ((int)$id > 0) $this->Myorders_model->UpdateOrder((int)$id, $this->dbdata);
					else $this->Myorders_model->InsertOrder($this->dbdata);
					
					/*if ($this->dbdata['endprice'] > 0 && (int)$id > 0)
					{
					$this->clientemail = $this->Myorders_model->GetClientData((int)$id);
					$this->msg['msg_title'] = 'A price for your repair '.(int)$id.'  has been set @ '.FlipDateMail(CurrentTime());
					$this->msg['msg_body'] = '');
			
					GoMail($this->msg, $this->clientemail);
					}
					*/
					redirect("/Myorders"); exit();								
			}
	}
	
function AmendOrder($id) 
{
	$this->orderdata = $this->Myorders_model->GetOrderData((int)$id);
	
	if (!isset($this->orderdata))
		{ 
		//$this->session->set_flashdata('amenderrors', array('noamend' => 'This order is completed. Amending in only allowed to unfinished orders'));
		Redirect("Myorders/Show/".(int)id); 
		exit();
		}
	
	$this->load->library('form_validation');
	$this->form_validation->set_rules('order', 'Order', 'trim|required|xss_clean');
	$this->form_validation->set_rules('endprice', 'End Price', 'trim|required|xss_clean');

	if ($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('amenderrors', $this->form_validation->_error_array);
				Redirect("Myorders/Show/".(int)$id); 
				exit();
			}
	else
	{
		
		$this->dbdata = array(
								  'oid_ref' => $this->orderdata['oid'],
								  'buytype' => '5',
								  'complete' => '0',
								  'email' => $this->orderdata['email'],
								  'comments' => $this->input->post('comments', TRUE),
								  'order' => $this->form_validation->set_value('order'),
								  'endprice' => (float)PriceUnification($this->input->post('endprice', TRUE)),
								  'time' => CurrentTime(),
								  'staffcomments' => ' <strong>('.$this->session->userdata['name'].')</strong>: '.$this->input->post('staffcomments', TRUE)
								  );
		if ($this->dbdata['endprice'] > 0) {
		$this->load->helper('arithmetic');
		$this->dbdata['code'] = rand_string(50);
		}
		
		$this->Myorders_model->Insertorder($this->dbdata);
		
						$this->msg['msg_title'] = 'New amendment to repair order - No. '.$this->orderdata['oid'].' from '.FlipDateMail(CurrentTime());
						$this->msg['msg_body'] = '<h3>New amendment to repair order - No. '.$this->orderdata['oid'].' from '.FlipDateMail(CurrentTime()).'</h3><br clear="all">';
						$this->mysmarty->assign('data', $this->dbdata);				
						$this->msg['msg_body'] .= $this->mysmarty->fetch('emails/myorders_amended.html');
	
			GoMail ($this->msg, $this->orderdata['email']);
				
						unset($this->msg['msg_body']);
						
						$this->msg['msg_body'] = '<h3>'.$this->msg['msg_title'].'</h3><br clear="all">';
						$this->mysmarty->assign('admin', TRUE);
						$this->msg['msg_body'] .= $this->mysmarty->fetch('emails/myorders_amended.html');
						$this->msg['msg_body'] .= '<br>'.$_SERVER['REMOTE_ADDR'];
						$this->msg['msg_date'] = CurrentTime();
						
			$this->mailid = 11;			
			GoMail ($this->msg);
			
			$this->load->model('Myadmin_model');
			$this->Myadmin_model->InsertHistoryData($this->msg);
		
		Redirect("Myorders/Show/".$this->orderdata['oid']); exit();
	}
	
}

function DeleteAmendment ($oid_ref = '', $oid = '')
	{
	if (((int)$oid_ref > 0) && ((int)$oid >0)) 
	{
		$this->amendmentdata = $this->Myorders_model->GetAmendmentData((int)$oid);
		
		$this->Myorders_model->DeleteAmendment((int)$oid);

		$this->orderdata = $this->Myorders_model->GetOrderData((int)$oid_ref);
		
						$this->msg['msg_title'] = 'Removed Amendment for repair order No. '.$this->orderdata['oid'].' @ '.FlipDateMail(CurrentTime());
						$this->msg['msg_body'] = '<h3>Amendment for repair order No. '.$this->orderdata['oid'].' has been removed @ '.FlipDateMail(CurrentTime()).'</h3><br clear="all">';
						$this->mysmarty->assign('data', $this->amendmentdata);				
						$this->msg['msg_body'] .= $this->mysmarty->fetch('emails/myorders_deleteamendment.html');
	
			GoMail ($this->msg, $this->orderdata['email']);
				
				unset($this->msg);
				
						$this->msg['msg_title'] = 'Deleted Amendment for repair order No. '.$this->orderdata['oid'].' from '.FlipDateMail(CurrentTime());
						$this->msg['msg_body'] = '<h3>Deleted Amendment for repair order - No. '.$this->orderdata['oid'].' from '.FlipDateMail(CurrentTime()).'</h3><br clear="all">';
						
						$this->msg['msg_date'] = CurrentTime();
						
						$this->mysmarty->assign('order', $this->orderdata['oid']);
						$this->mysmarty->assign('data', $this->amendmentdata);
						$this->mysmarty->assign('admin', TRUE);
						$this->msg['msg_body'] .= $this->mysmarty->fetch('emails/myorders_deleteamendment.html');
						$this->msg['msg_body'] .= '<br>('.$this->session->userdata['name'].') @ '.$_SERVER['REMOTE_ADDR'];
						$this->msg['msg_date'] = CurrentTime();
			
			$this->mailid = 12;
			GoMail ($this->msg);
			
			$this->load->model('Myadmin_model');
			$this->Myadmin_model->InsertHistoryData($this->msg);
			
			Redirect("Myorders/Show/".$this->orderdata['oid']); exit();
		}
		else
		{
			Redirect("Myorders");
			exit();
		}
	}
	
	
function OrderItem($oid = '', $oiid = '') 
{
	$this->orderdata = $this->Myorders_model->GetOrderData((int)$oid);

	if (!isset($this->orderdata))
		{ 
		Redirect("Myorders/Show/".(int)oid); 
		exit();
		}
	
	$this->load->library('form_validation');
	$this->form_validation->set_rules('item', 'Item', 'trim|required|xss_clean');
	$this->form_validation->set_rules('price', 'Price', 'trim|required|xss_clean');
	$this->form_validation->set_rules('labour', 'Labour Hours', 'trim|required|xss_clean');

	if ($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('itemerrors', $this->form_validation->_error_array);
				
				if ($this->orderdata['buytype'] == 4 || $this->orderdata['buytype'] == 6) Redirect("Myorders/ShopRepair/".(int)$oid); 
				else Redirect("Myorders/Show/".(int)$oid);
				exit();
			}
	else
	{
		$this->dbdata = array(
								  'oid' => $this->orderdata['oid'],
								  'item' => htmlspecialchars($this->form_validation->set_value('item', TRUE)),
								  'price' => (float)PriceUnification($this->input->post('price', TRUE)),
								  'labour' => (float)PriceUnification($this->input->post('labour', TRUE)),
								  'time' => CurrentTime(),
								  'comments' => $this->input->post('comment', TRUE)
								  );
		if ((int)$oiid > 0) 
		{
		unset($this->dbdata['oid']);
		$this->Myorders_model->UpdateItem($this->dbdata, (int)$oiid, (int)(int)$oid);	
		}
		else 
		{
		$this->Myorders_model->InsertItem($this->dbdata);
		}
		if ($this->orderdata['buytype'] == 4 || $this->orderdata['buytype'] == 6) Redirect("Myorders/ShopRepair/".(int)$oid); 
		else Redirect("Myorders/Show/".(int)$oid);
	}
	
}

function DeleteItem ($oid = '', $oiid = '')
	{
	if (((int)$oid > 0) && ((int)$oiid >0)) 
	{
		$this->orderdata = $this->Myorders_model->GetOrderData((int)$oid);
	
			if (isset($this->orderdata))
			{ 
			$this->Myorders_model->DeleteItem((int)$oiid, (int)$oid);
			if ($this->orderdata['buytype'] == 4) Redirect("Myorders/ShopRepair/".(int)$oid); 
			else Redirect("Myorders/Show/".(int)$oid);
			}
		}
		else
		{
			Redirect("Myorders");
			exit();
		}
	}
function _PriceUnification ($string) {
	$this->inputstring = str_replace(',', '.', $string);
	return $this->inputstring;
	}
	
	
}
