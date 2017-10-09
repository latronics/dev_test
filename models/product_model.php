<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends Model 
{
    function Product_model()
    {
        parent::Model();
		
		
		
		
    }
function GetStoreCart()
{
	
	$this->db->select("svalue");
		$this->db->where('skey', 'StoreCart');
		$q = $this->db->get('settings');
		$storecart = 0;
		if ($q->num_rows() > 0) 
			{
				$res = $q->row_array();
				$storecart = $res['svalue'];
			}
		return (int)$storecart;
}
function RecordAction($eid = 0, $ebayid = 0, $datafrom = '', $datato = '', $adminid = 0, $transid = 0, $ctrl = '')
{
	$this->db->insert('ebay_actionlog',array('e_id' => $eid, 'ebay_id' => $ebayid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom, 'datato' => $datato, 'admin_id' => $adminid, 'trans_id' => $transid, 'ctrl' => $ctrl));
}
function TakeQuantity($id, $quantity)
	{
		$this->db->set('p_quantity', 'p_quantity-'.(int)$quantity, FALSE);
		$this->db->set('p_pendquant', 'p_pendquant+'.(int)$quantity, FALSE);
		$this->db->where('p_id', (int)$id);
		$this->db->update('products');		
	}
function GetTopSpecials () 
	{
			$this->db->select('p_id, p_sef, p_title, p_toptxt, p_img1, p_ad');
			$this->db->where('p_cat', '34');
			$this->db->where('p_visibility', '1');
			$this->db->where('p_top', '1');
			$this->db->limit(4);
			$this->db->order_by("p_order", "ASC");
			$this->pquery = $this->db->get('products');
			if ($this->pquery->num_rows() > 0) 
				{
					$this->presult = $this->pquery->result_array();
					foreach ($this->presult as $key => $value)	
						{
						$this->presult[$key]['p_title'] = htmlspecialchars($value['p_title']);
						}
					return $this->presult;
				}
	
	}
function GetSpecial ($id) 
	{
			$this->db->select('p_id, p_sef, p_title, p_toptxt, p_img1, p_price, p_freegrship');
			$this->db->where('p_cat', '34');
			$this->db->where('p_visibility', '1');
			$this->db->where('p_id', (int)$id);
			$this->pquery = $this->db->get('products');
			if ($this->pquery->num_rows() > 0) 
				{
					$this->presult = $this->pquery->row_array();
					/*foreach ($this->presult as $key => $value)	
						{
						$this->presult[$key]['p_toptxt'] = strip_tags($value['p_toptxt']);
						}*/
					return $this->presult;
				}
	
	}
function UpdataAuthorizeNetPayment($data)
	{
		//$this->db->select("returnedresponse");
		$this->db->select("complete");
		$this->db->where('oid', (int)$data['x_invoice_num']);		
		$this->db->where('payproc', '1');
		$this->db->limit(1);
		$this->query = $this->db->get('orders');

		if ($this->query->num_rows() > 0) 
		{
			$returneddata = $this->query->row_array();
			if ($returneddata['complete'] != 1)
				{
					$insertdata = array(
										'complete' => (int)$data['x_response_code'],
										'complete_time' => CurrentTime(),
										'payproc_data' => serialize($data),
										'returnedresponse' => '1'
										);
						
								$this->db->where('oid', (int)$data['x_invoice_num']);
								$this->db->update('orders', $insertdata); 
								
								return 1;
				}
				else
				{
				return 0;	
				}			
		}
		else
		{
			return '-1';
		}
	}
function MatchAuthorizeNetPayment($data)
	{
		$this->db->select("buytype, endprice, endprice_delivery");
		$this->db->where('oid', (int)$data['x_invoice_num']);
		$this->db->where('payproc', '1');
		$this->db->limit(1);
		$this->query = $this->db->get('orders');

		if ($this->query->num_rows() > 0) 
		{
			$returneddata = $this->query->row_array();
			
			if ($returneddata['buytype'] == 3) $returneddata['x_amount'] = $returneddata['endprice'];
			else $returneddata['x_amount'] = $returneddata['endprice']+$returneddata['endprice_delivery'];			
		
			$returneddata['x_amount'] = $returneddata['endprice']+$returneddata['endprice_delivery'];
			if ((float)$data['x_amount'] == (float)$returneddata['x_amount'])
				{
				return 1;
				}
				else
				{
					$admindata['msg_date'] = CurrentTime();			
					$admindata['msg_title'] = 'Post & DB Price Checkup for Order: '.(int)$data['x_invoice_num'];
					$admindata['msg_body'] = "
												data x_invoice_num  = ".(int)$data['x_invoice_num']."<br>
												returneddata endprice  = ".$returneddata['endprice']."<br>
												returneddata endprice_delivery = ".$returneddata['endprice_delivery']."<br>
												returneddata x_amount  = ".(float)$returneddata['x_amount']."<br>
												data x_amount = ".(float)$data['x_amount']."<Br>
												
												";		
					GoMail ($admindata, $this->config->config['support_email']);	
					
				return 0;	
				}			
		}
	}
function MatchPaypalPayment($data)
	{
		$this->db->select("buytype, endprice, endprice_delivery");
		$this->db->where('oid', (int)$data['item_number']);
		$this->db->where('payproc', '2');
		$this->query = $this->db->get('orders', 1);

		if ($this->query->num_rows() > 0) 
		{
			$returneddata = $this->query->row_array();
			
			if ($returneddata['buytype'] == 3) $returneddata['payment_gross'] = $returneddata['endprice'];
			else $returneddata['payment_gross'] = $returneddata['endprice']+$returneddata['endprice_delivery'];
			
			if ((float)$data['payment_gross'] == (float)$returneddata['payment_gross'])
				{
				return 1;
				}
				else
				{
				return 0;	
				}			
		}
	}
function UpdataPayPalPayment($data)
	{
		//$this->db->select("returnedresponse");
		$this->db->select("complete");
		$this->db->where('oid', (int)$data['item_number']);
		$this->db->where('payproc', '2');
		$this->query = $this->db->get('orders', 1);

		if ($this->query->num_rows() > 0) 
		{
			$returneddata = $this->query->row_array();
			if ($returneddata['complete'] != 1)
				{
					$paypalstats = array(
										 '1' => 'Completed',
										 '2' => 'Denied',
										 '3' => 'Expired',
										 '4' => 'Failed',
										 '5' => 'In-Progress',
										 '6' => 'Pending',
										 '7' => 'Processed',
										 '8' => 'Voided',
										 '9' => 'Partially_Refunded',
										 '10' => 'Canceled_Reversal',
										 '11' => 'Reversed',
										 '12' => 'Refunded'
										 );

					foreach ($paypalstats as $key => $value)
					{
						if ($data['payment_status']	== $value) $payment_status = $key;
					}
					$insertdata = array(
										'complete' => (int)$payment_status,
										'complete_time' => CurrentTime(),
										'payproc_data' => serialize($data),
										'returnedresponse' => '1'
										);
						
								$this->db->where('oid', (int)$data['item_number']);
								$this->db->update('orders', $insertdata); 
								
								return 1;
				}
				else
				{
				return 0;	
				}			
		}
		else
		{
			return '-1';
		}
	}
function CheckValidOrder($id, $sesdata, $code, $update = '')
	{
	
		$this->db->select("oid, buytype, endprice, endprice_delivery, payproc, fname, lname , address, city, state, postcode, country, daddress, dcity, dpostcode, dstate, dcountry, tel, email");
		$this->db->where('oid', (int)$id);
		if ($update == '') {
		$this->db->where('fname', $sesdata['fname']);
		$this->db->where('lname', $sesdata['lname']);
		$this->db->where('email', $sesdata['email']);
		$this->db->where('time', $sesdata['time']);
		$this->db->where('buytype', (int)$sesdata['buytype']);
		}
		$this->db->where('code', $code);
		if ((int)$update > 0) $this->db->where('complete != 1');
 
		$this->query = $this->db->get('orders', 1);

		if ($this->query->num_rows() > 0) return $this->query->row_array();	
	}

function CheckValidAmendment($oidref, $oid, $code, $payproc)
	{	
		$this->db->where('oid_ref', (int)$oidref);
		$this->db->where('oid', (int)$oid);
		$this->db->where('code', $code);
		$this->db->where('buytype', '5');
		$this->db->where('endprice > 0');
		$this->db->where('complete != 1');
		$this->db->limit(1);
		$this->db->update('orders', array('payproc' => (int)$payproc));
		
		$this->db->select("oid, oid_ref, buytype, endprice, payproc");
		$this->db->where('oid_ref', (int)$oidref);
		$this->db->where('oid', (int)$oid);
		$this->db->where('code', $code);
		$this->db->where('buytype', '5');
		$this->db->where('endprice > 0');
		$this->db->where('complete != 1');
		//$this->db->where('returnedresponse', '0');
		$this->query = $this->db->get('orders', 1);

		if ($this->query->num_rows() > 0) 
		{
				$this->amendment = $this->query->row_array();	
				$this->db->select("fname, lname , address, city, state, postcode, country, daddress, dcity, dpostcode, dstate, dcountry, tel, email");
				$this->db->where('oid', (int)$this->amendment['oid_ref']);
				$this->query = $this->db->get('orders', 1);
				
				if ($this->query->num_rows() > 0) 
					{
						$this->orderparent = $this->query->row_array();	
						$this->result = array_merge($this->amendment, $this->orderparent);
					
						return $this->result;
					}
		}
		
	}
function UpdatePayProcessor($id, $code, $pp)
	{
		$this->db->update('orders', array('payproc' => (int)$pp), array('oid' => (int)$id, 'code' => $code));
	}
function ListMyOrders($email)
	{
		$this->db->select("oid, oid_ref, complete, time, order, buytype, subtype, status, endprice, payproc");
		$this->db->order_by("time", "DESC");
		$this->db->where('email', $email);
		$this->query = $this->db->get('orders');

		if ($this->query->num_rows() > 0) 
			{
			foreach ($this->query->result_array() as $k => $v)	
				{
				if ($v['status'] != '') {
										$v['status'] = unserialize($v['status']);
										$v['status'] = end($v['status']);
										}
				if ($v['oid_ref'] == 0) { unset($v['order']); $this->orders[$v['oid']] = $v;}
				else $this->amendments[$v['oid_ref']][] = $v;
				}
				
				if (isset($this->amendments)) 
					{
						foreach ($this->amendments as $ak => $av)
						{
						if (isset($this->orders[$ak])) $this->orders[$ak]['amendments'] = $av;
						$this->orders[$ak]['amendments'] = array_reverse($this->orders[$ak]['amendments']);
						}
					}
			return $this->orders;
			}	
	}
	
function GetOrder($id, $email)
	{
		$this->db->where('oid', (int)$id);
		$this->db->where('email', $email); 
		$this->query = $this->db->get('orders', 1);
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			if ($this->result['status'] != '') $this->result['status'] = unserialize($this->result['status']);
			else $this->result['status'] = false;
			
			return $this->result;
			}
	}	

function GetOrderForResult($id, $email)
	{
		$this->db->where('oid', (int)$id);
		//$this->db->where('email', $email); 
		$this->query = $this->db->get('orders', 1);
		if ($this->query->num_rows() > 0) 
			{
			$result = $this->query->row_array();
			if ($result['status'] != '') $result['status'] = unserialize($result['status']);
			else $result['status'] = false;
			//$isser = is_serialized($result['order']);
			if (strlen($result['order'] < 9)) $result['order'] = unserialize($result['order']);
			return $result;
			}
	}
	
function GetOrderAmendments($id)
	{	
		$this->db->where('oid_ref', (int)$id);
		$this->db->where('buytype', '5');
		$this->db->order_by("time", "ASC");
		$this->query = $this->db->get('orders');

		if ($this->query->num_rows() > 0) return $this->query->result_array();

	}
	
function Search($string = '')
	{
		$this->db->select('p_id, p_sef, p_title, p_cat, p_price, p_img1');
		$this->allstrings = explode (" ", $string);

		$countlikes = 0;
		foreach ($this->allstrings as $value) {
			if ($countlikes == 0) 
			{
				$this->db->like('p_title', $value, 'both');
			}
			else
			{
				$this->db->orlike('p_title', $value, 'both');
			}
		$this->db->orlike('p_desc', $value, 'both');	
		
		$countlikes++;
		}



		$this->db->distinct();
	
	    $this->db->order_by("p_order", "ASC");
		$this->nquery = $this->db->get('products');


			if ($this->nquery->num_rows() > 0) 
			{

			foreach ($this->nquery->result_array() as $key => $value)
				{
				$this->found[$value['p_id']] = $value;					
				}
			
			return $this->found;
			}	
	}
	
function GetRepairPrices($country = '')
	{
	$this->db->where('country', $country);	
	$this->prices = $this->db->get('delivery_rates', 1);
		if ($this->prices ->num_rows() > 0) 
			{
				return $this->prices->row_array();
		
			}
	}
function GetProductCategories($id = 0)
	{
		if ((int)$id > 0) $this->db->where('p_parent', (int)$id);	
		$this->db->order_by("p_order", "ASC");
		$this->pcs = $this->db->get('product_categories');
		
		if ($this->pcs ->num_rows() > 0) 
			{
				return $this->pcs->result_array();
			
			}
	}

function GetCategoryData($sef = '', $id = '') 
	{	
		if ((int)$id > 0) {
		$this->db->where('p_catid', $id);
		}
		else {
		$this->db->where('p_sef', $sef);
		}
		$this->query = $this->db->get('product_categories', 1);
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->row_array();
			}
	}
function GetProductList($id = '') 
	{
		//if ((int)$id > 0) 
		//{
			$this->db->select('p_id, p_sef, p_title, p_price, p_img1, p_type');
			$this->db->where('p_cat', (int)$id);
			$this->db->where('p_visibility', '1');
			$this->db->order_by("p_order", "ASC");
			$this->pquery = $this->db->get('products');
			if ($this->pquery->num_rows() > 0) 
				{
					$this->presult = $this->pquery->result_array();
					return $this->presult;
				}
		//}
	}



function GetProduct($sef = '')
	{	
		$this->db->select('p_id, p_price, p_type, p_cat, p_sef, p_title, p_desc, p_img1, p_img2, p_img3, p_img4, p_availability, p_lbs, p_oz, p_ad, p_shipping, p_freegrship, p_quantity, p_condition');
		$this->db->where('p_sef', $sef);
		$this->db->where('p_visibility', '1');
		$this->query = $this->db->get('products', 1);
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();	
			return $this->result;
			}
	}
function GetEbayListings($page)
	{
		$this->db->select('e_id, e_title, e_img1, idpath');
		$this->db->where('nwm', 1);
		if ((int)$page > 0) $page = $page - 1;
		$this->db->limit(15, (int)$page*15);
		$this->db->order_by('e_id', 'desc');		
		$this->query = $this->db->get('ebay');
		$this->db->where('nwm', 1);
		$this->countall = $this->db->count_all_results('ebay');
			$this->pages = ceil($this->countall/15);
			for ( $counter = 1; $counter <= $this->pages ; $counter++) 
			{
			$this->pagearray[] = $counter;
			}
		if ($this->query->num_rows() > 0) 
			{
			return array('pages' => $this->pagearray,
						'result' => $this->query->result_array()
						);
			}
	}
	
	
	function GetLatestEbayListings($search = '')
	{
		$this->db->select('e_id, e_title, e_img1, idpath, qn_ch1, price_ch1');
		if ($search == '')$this->db->limit(15);
		$this->db->where('sitesell', 1);
		$this->db->where('nwm', 1);
		if ($search != '') 
		{
			$search = explode(' ',trim($search));
				//$c=1;
					foreach ($search as $s)
					{
					$this->db->like('e_title', $s);	

					/*
					if ($c == 1) $this->db->like($wl, $s);	
					else $this->db->or_like($wl, $s);	*/
					//$c++;
					}			
			//$this->db->like('e_title', $search);			
		}
		$this->db->order_by('e_id', 'desc');		
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) return $this->query->result_array();
	}
	function GetStoreEbayListings($ordering, $id, $page)
	{
		$this->db->select('e_id, e_title, e_img1, idpath, price_ch1, qn_ch1');
		$string = trim($this->input->post('srcme', TRUE));
		if ((int)$id > 0) $this->db->where('storeCatId', (int)$id);
		$this->db->where('sitesell', 1);
		$this->db->where('nwm', 1);
		if ($string != '')
		{
			$this->mysmarty->assign('srcme', htmlspecialchars($string)); 
			$string = str_replace("'", "", $string);
			$string = str_replace('"', "", $string);
			$string = str_replace('&quot;', "", $string);
			$es = explode(' ', trim($string));
			$sql = '(';
			$cn = 1;
			foreach ($es as $e)
			{
				if ($cn == 1)  $sql .= '`e_title` LIKE "%'.trim($e).'%"';
				else $sql .= ' AND `e_title` LIKE "%'.trim($e).'%"';
				$cn++;
			}
			$sql .= ')';
			$this->db->where($sql,null, false);			
		}
		if ((int)$page > 0) $page = $page - 1;
		$this->db->limit(15, (int)$page*15);
		
		if ($ordering == 'listasc') { /*echo 'listasc';*/ $this->db->order_by('e_id', 'asc'); }
		elseif ($ordering == 'priceasc') { /*echo 'priceasc';*/ $this->db->order_by('price_ch1', 'asc');		}
		elseif ($ordering == 'pricedesc') { /*echo 'pricedesc';*/ $this->db->order_by('price_ch1', 'desc');		}
		else { $this->db->order_by('e_id', 'desc'); /*echo 'listdesc';*/		}
		
		//printcool ($ordering);
		
		$this->query = $this->db->get('ebay');
		
		if ((int)$id > 0) $this->db->where('storeCatId', (int)$id);
		$this->db->where('sitesell', 1);
		if ($string != '') $this->db->where($sql,null, false);		
		$this->countall = $this->db->count_all_results('ebay');
		$this->pages = ceil($this->countall/15);
		for ( $counter = 1; $counter <= $this->pages ; $counter++) 
		{
		$this->pagearray[] = $counter;
			}
		if ($this->query->num_rows() > 0) return array('pages' => $this->pagearray,
						'result' => $this->query->result_array());
	}
function GetEbayGallery($sef = '')
	{
		$this->db->select('e_id, e_title, e_img1, e_img2, e_img3, e_img4, idpath');
		$this->db->where('e_sef', $sef);
		$this->db->where('nwm', 1);

		$this->query = $this->db->get('ebay', 1);
		if ($this->query->num_rows() > 0)  return $this->query->row_array();
	}
function CheckProduct($id = '')
	{	
		$this->db->select('e_id, e_title, e_sef, idpath, e_img1, price_ch1 AS buyItNowPrice, qn_ch1 AS quantity, PaymentMethod, shipping');
		$this->db->where('e_id', (int)$id);
		//$this->db->where('p_visibility', '1');
		$this->query = $this->db->get('ebay', 1);
		$s = $this->query->row_array();
		if ($this->query->num_rows() > 0) return $this->query->row_array();			
	}
function RefreshProduct($id = '') 
	{
	if ((int)$id > 0 ) 
		{
		$this->db->select('e_id, e_title, e_sef, idpath, e_img1, price_ch1 AS buyItNowPrice, qn_ch1 AS quantity, PaymentMethod, shipping');
		$this->db->where('e_id', (int)$id);
		//$this->db->where('p_visibility', '1');
		$this->query = $this->db->get('ebay', 1);
		if ($this->query->num_rows() > 0) return $this->query->row_array();			
		}
		
	}

function GetFormPersonalData($confirmcode, $id)
	{
		$this->db->select('fid, fname, lname, tel, email');
		$this->db->where('type', '1');
		$this->db->where('code', $confirmcode);
		$this->db->where('fid', (int)$id);
		$this->query = $this->db->get('forms_request', 1);
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();			
			return $this->result;
			}	
	}

function GetFormData($confirmcode, $id)
	{
		$this->db->select('fid, brand, model, item, fname, lname, tel, email');
		$this->db->where('type', '1');
		$this->db->where('code', $confirmcode);
		$this->db->where('fid', (int)$id);
		$this->query = $this->db->get('forms_request', 1);
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();			
			return $this->result;
			}	
	}
function MatchConfirmCode($confirmcode, $id)
	{
		$this->db->select('email');
		$this->db->where('type', '1');
		$this->db->where('code', $confirmcode);
		$this->db->where('fid', (int)$id);
		$this->query = $this->db->get('forms_request', 1);
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->row_array();			
			}	
	}
function InsertOrder($data) 
	{
	$this->db->insert('orders',$data);		
	}
function InsertRequestForm($data)
	{
	$this->db->insert('forms_request',$data);		
	}
function GetSolution($id) 
	{
	if ((int)$id > 0 ) 
		{
		$this->db->where('sid', (int)$id);
		$this->db->where('visible', '1');
		$this->query = $this->db->get('solutions');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();			
			return $this->result;
			}	
		}
	}
function GetSolutionsList()
	{
			$this->db->select("sid, title, image, desc");	
			$this->db->where('visible', '1');
			$this->db->order_by("ordering", "ASC");
			$this->pquery = $this->db->get('solutions');
			if ($this->pquery->num_rows() > 0) 
				{
					foreach ($this->pquery->result_array() as $k => $v){
						
					$r[$k]['sid'] = $v['sid'];
					$r[$k]['title'] = $v['title'];
					$r[$k]['image'] = $v['image'];
					$r[$k]['desc'] = strip_tags($v['desc']);
					
					}
				return $r;
				}
	}
	
function GetDIY($id) 
	{
	if ((int)$id > 0 ) 
		{
		$this->db->where('d_id', (int)$id);
		$this->db->where('d_visibility', '1');
		$this->query = $this->db->get('diy');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();			
			return $this->result;
			}	
		}
	}
function GetDIYList()
	{
			$this->db->select("d_id, d_title");	
			$this->db->where('d_visibility', '1');
			$this->db->order_by("d_order", "ASC");
			$this->pquery = $this->db->get('diy');
			if ($this->pquery->num_rows() > 0) 
				{
				return $this->pquery->result_array();
				}
	}
function GetWhitePaper($id) 
	{
	if ((int)$id > 0 ) 
		{
		$this->db->where('wpid', (int)$id);
		$this->db->where('visible', '1');
		$this->query = $this->db->get('whitepapers');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();			
			return $this->result;
			}	
		}
	}
	
function GetWhitePapersFile($id) 
	{
	if ((int)$id > 0 ) 
		{
		$this->db->select('file');	
		$this->db->where('wid', (int)$id);
		$this->db->where('visible', '1');
		$this->query = $this->db->get('whitepapers');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();			
			return $this->result['file'];
			}	
		}
	}
function GetWhitePapersList()
	{
			$this->db->select("wid, title, file");	
			$this->db->where('visible', '1');
			$this->db->order_by("ordering", "ASC");
			$this->pquery = $this->db->get('whitepapers');
			if ($this->pquery->num_rows() > 0) 
				{
				return $this->pquery->result_array();
				}
	}
	
function GetPartners()
	{
			$this->db->select("rid, title, logo, url, cat");	
			$this->db->order_by("ordering", "ASC");
			$this->pquery = $this->db->get('partners');
			if ($this->pquery->num_rows() > 0) 
				{
				return $this->pquery->result_array();
				}
	}
	
function GetAllCategories() 
	{
	$this->db->select('p_catid, p_cattitle, p_sef, p_parent');
	$this->db->where('p_vis', '1');
	$this->db->order_by("p_order", "ASC");
	$this->query = $this->db->get('product_categories');
		if ($this->query->num_rows() > 0) 
			{
				/*$this->db->distinct();
				$this->db->select("p_cat");
				$query = $this->db->get('products');
				if ($query->num_rows() > 0) 
					{
						foreach ($query->result_array() as $pv) 
							{
								$cat[$pv['p_cat']] = $pv;
							}
					}*/

			foreach ($this->query->result_array() as $key =>$value) {
			$this->allcats[$value['p_parent']][$value['p_catid']] = $value;		
			
			}
			return $this->allcats;
			}
	}
function GetAllTopCategories() 
	{
	$this->db->select('p_catid, p_cattitle, p_sef, p_parent');
	$this->db->where('p_top', '1');
	$this->db->where('p_vis', '1');
	$this->db->order_by("p_order", "ASC");
	$this->query = $this->db->get('product_categories');
		if ($this->query->num_rows() > 0) 
			{
			
				/*$this->db->distinct();
				$this->db->select("p_cat");
				$query = $this->db->get('products');
				if ($query->num_rows() > 0) 
					{
						foreach ($query->result_array() as $pv) 
							{
								$cat[$pv['p_cat']] = $pv;
							}
							
					}*/
					
			foreach ($this->query->result_array() as $key =>$value) {
			$this->allcats[$value['p_parent']][$value['p_catid']] = $value;	
			}
			return $this->allcats;
			}
	}
function GetSolutionProducts($id)	
	{
	if ((int)$id > 0) 
		{
			$this->db->select('p.p_id, p.p_sef, p.p_title, p.p_price, p.p_img1');
			$this->db->where('p.p_id = sp.p_id');
			$this->db->where('sp.sid', (int)$id);
			$this->db->where('p.p_visibility', '1');
			$this->db->order_by("p.p_order", "ASC");
			$this->pquery = $this->db->get('products AS p, solution_products AS sp');
			if ($this->pquery->num_rows() > 0) 
				{
					$this->presult = $this->pquery->result_array();
					return $this->presult;
				}
		}
		
	}
function GetCommData($confirmcode ='', $id = '')
	{
			$this->db->select('c.f_owner, c.f_time, c.f_msg');
			$this->db->where('f.code', $confirmcode);
			$this->db->where('f.fid', (int)$id);
			$this->db->where('f.fid = c.f_id');
			$this->db->order_by("c.fc_id", "ASC");
			$fquery = $this->db->get('forms_request AS f, forms_request_comm AS c');
			if ($fquery->num_rows() > 0) 
				{
					return $fquery->result_array();
				}
	}

/////Structure get all function

function GetStructure($type = '') {
		if ($type == 'top') $this->mysmarty->assign('catlist', $this->GetAllTopCategories());
		else $this->mysmarty->assign('catlist', $this->GetAllCategories());
}


}
?>