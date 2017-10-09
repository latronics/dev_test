<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myorders_model extends Model 
{
    function Myorders_model()
    {
        parent::Model();
    }
function GetEndprice($id)
	{
		$this->db->select("endprice");
		$this->db->where('oid', (int)$id);
		$this->query = $this->db->get('orders');

		if ($this->query->num_rows() > 0) 
			{
			 $found = $this->query->row_array();
			 return $found['endprice'];
			}		
	}
function GetIDPath($id)
{
		$this->db->select("idpath");
		$this->db->where('e_id', (int)$id);
		$this->query = $this->db->get('ebay');

		if ($this->query->num_rows() > 0) 
			{
			 $found = $this->query->row_array();
			 return $found['idpath'];
			}

}
function MoveQuantityBack($id, $quantity)
	{
		$this->db->set('p_quantity', 'p_quantity+'.(int)$quantity, FALSE);
		$this->db->set('p_pendquant', 'p_pendquant-'.(int)$quantity, FALSE);
		$this->db->where('p_id', (int)$id);
		$this->db->update('products');	
	}

function MoveQuantityOut($id, $quantity)
	{
		$this->db->set('p_pendquant', 'p_pendquant-'.(int)$quantity, FALSE);
		$this->db->where('p_id', (int)$id);
		$this->db->update('products');	
	}
function MarkMovementComplete($id)
	{
		$this->db->set('pendquant_action', 1, FALSE);
		$this->db->where('oid', (int)$id);
		$this->db->update('orders');	
	}
	
function CompleteStatus($id)
	{
		$this->db->update('orders', array('complete' => 1, 'returnedresponse' => 1, 'sysdata' => 'Completed by Admin '.$this->session->userdata['name'].' @'.CurrentTime()), array('oid' => (int)$id));	
	}
function FraudStatus($id)
	{
		$this->db->update('orders', array('complete' => "-1", 'returnedresponse' => 1, 'sysdata' => 'Frauded by Admin '.$this->session->userdata['name'].' @'.CurrentTime()), array('oid' => (int)$id));	
	}
function ListItems($type = '', $sortby = '')
	{	
		//$sql = "SELECT oid, oid_ref, complete, fname, lname, city, time, buytype, subtype, status, endprice, fid, payproc, sysdata, admin, rid FROM orders WHERE (complete = 1 AND returnedresponse = 1 AND test = 0) OR (buytype = 2 OR buytype = 4 OR buytype = 6 OR buytype = 7 OR buytype = 8) ORDER BY oid DESC"; 
//returnedresponse

$sql = "SELECT *  FROM orders WHERE (complete = 1 AND returnedresponse = 1 AND test = 0) OR (buytype = 2 OR buytype = 4 OR buytype = 6 OR buytype = 7 OR buytype = 8) ORDER BY submittime DESC"; 

		$this->query = $this->db->query($sql);
/*		$this->db->select("oid, oid_ref, complete, fname, lname, city, time, buytype, status, endprice, fid, payproc");
		$this->db->where('complete', 1);
		$this->db->where('returnedresponse', 1);
		$this->db->or_where('buytype', 2);
		$this->db->where('buytype', 4);
		$this->db->order_by("time", "DESC");
		$this->query = $this->db->get('orders');*/

		if ($this->query->num_rows() > 0) 
			{
				
			$nowmk = (int)mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			
			
			
			foreach ($this->query->result_array() as $k => $v)	
				{
				if ($v['status'] != '' && $v['status'] != ' ') {
										$v['status'] = unserialize($v['status']);
										//$v['origstatus'] = $v['status'][0];										
										$v['status'] = end($v['status']);
										}
				
				
				if (strlen($v['order']) > 9) $v['order'] = unserialize($v['order']);
				if (strlen($v['CheckoutStatus']) > 9) $v['CheckoutStatus'] = unserialize($v['CheckoutStatus']);
				
				$v['mktime'] = explode(' ', $v['time']);
				$v['mktime'] = explode('-', $v['mktime'][0]);			
				if (isset($v['mktime'][1]) && isset($v['mktime'][2]) && isset($v['mktime'][0])) $v['mktime'] = (int)mktime(0, 0, 0, $v['mktime'][1], $v['mktime'][2], $v['mktime'][0]);
				else $v['mktime'] = false;
				
				///RULES
				// - 3 days - if ($v['mktime'] && (($nowmk - $v['mktime']) > 172800) $v['background'] = 'red'; 
				$v['background'] = false;
				if ((int)$v['buytype'] == 1)
					{
						if ($v['subtype'] != 'r')
							{
								if ((($nowmk - $v['mktime']) > 172800) && $v['complete'] == 1 && $v['status']['status'] == 1) $v['background'] = 'red';
							}
					}
				elseif ((int)$v['buytype'] == 6 || (int)$v['buytype'] == 7 || (int)$v['buytype'] == 8)
					{
						if ($v['subtype'] != 'p')
							{
								if ((($nowmk - $v['mktime']) > 172800) && ($v['status']['status'] == 8 || $v['status']['status'] == 1)) $v['background'] = 'red';
							}
					}
				
			
				
				
				if ($v['oid_ref'] == 0) $this->orders[$v['oid']] = $v;	
				else $this->amendments[$v['oid_ref']][$v['oid']] = $v;
				}
				
				if (isset($this->amendments)) 
					{
						foreach ($this->amendments as $ak => $av)
						{
						if (isset($this->orders[$ak]))
							{
								$this->orders[$ak]['amendments'] = $av;
								$this->orders[$ak]['amendments'] = array_reverse($this->orders[$ak]['amendments']);
							}
						}
					}
			//	printcool ($this->orders);
			return $this->orders;
			}	
	}
	
function ListUncomplete($type = '', $sortby = '')
	{	
/*	
		$this->db->select("oid, oid_ref, complete, fname, lname, city, time, buytype, status, endprice, fid, payproc, sysdata");
		$this->db->where('complete != ', 1);
		$this->db->where('buytype !=', 2);
		$this->db->where('buytype !=', 4);
		$this->db->where('buytype !=', 6);
		$this->db->where('buytype !=', 7);
		$this->db->order_by("time", "DESC");
		$this->query = $this->db->get('orders');*/


		$sql = "SELECT oid, oid_ref, complete, fname, lname, city, time, buytype,  subtype, status, endprice, fid, payproc, sysdata, admin, rid FROM orders WHERE test = 0 AND complete != 1 AND buytype != 2 AND buytype != 4 AND buytype != 6 AND buytype != 7 AND buytype != 8 ORDER BY time DESC"; 

		$this->query = $this->db->query($sql);
		
		if ($this->query->num_rows() > 0) 
			{
				$nowmk = (int)mktime(0, 0, 0, date('m'), date('d'), date('Y'));
				
			foreach ($this->query->result_array() as $k => $v)	
				{
				if ($v['status'] != '') {
										$v['status'] = unserialize($v['status']);
										$v['status'] = end($v['status']);
										}
										
										
										
										
										
				$v['mktime'] = explode(' ', $v['time']);
				$v['mktime'] = explode('-', $v['mktime'][0]);			
				if (isset($v['mktime'][1]) && isset($v['mktime'][2]) && isset($v['mktime'][0])) $v['mktime'] = (int)mktime(0, 0, 0, $v['mktime'][1], $v['mktime'][2], $v['mktime'][0]);
				else $v['mktime'] = false;
				
				///RULES
				// - 3 days - if ($v['mktime'] && (($nowmk - $v['mktime']) > 172800) $v['background'] = 'red'; 
				$v['background'] = false;
				if ((int)$v['buytype'] == 1)
					{
						if ($v['subtype'] != 'r')
							{
								if ((($nowmk - $v['mktime']) > 172800) && $v['complete'] == 1 && $v['status']['status'] == 1) $v['background'] = 'red';
							}
					}
				elseif ((int)$v['buytype'] == 6 || (int)$v['buytype'] == 7 || (int)$v['buytype'] == 8)
					{
						if ($v['subtype'] != 'p')
							{
								if ((($nowmk - $v['mktime']) > 172800) && ($v['status']['status'] == 8 || $v['status']['status'] == 1)) $v['background'] = 'red';
							}
					}
				
				
				if ($v['oid_ref'] == 0) $this->orders[$v['oid']] = $v;	
				else $this->amendments[$v['oid_ref']][$v['oid']] = $v;
				}
				
				if (isset($this->amendments) && is_array($this->amendments) && (count ($this->amendments) > 0)) 
					{
						foreach ($this->amendments as $ak => $av)
						{
						if (isset($this->orders[$ak])) 
							{
							$this->orders[$ak]['amendments'] = $av;
							$this->orders[$ak]['amendments'] = array_reverse($this->orders[$ak]['amendments']);
							}
						}
					}
		
			return $this->orders;
			}	
	}

function CountUncomplete()
	{	
		$sql = "SELECT count(oid) FROM orders WHERE test = 0 AND complete != 1 AND buytype != 2 AND buytype != 4 AND buytype != 5 AND buytype != 6 AND buytype != 7 AND buytype != 8 ORDER BY time DESC"; 

		
		$this->query = $this->db->query($sql);
		
		if ($this->query->num_rows() > 0) 
			{
				$c = $this->query->result_array();
				if (isset($c[0])) return $c[0]['count(oid)'];
			}	
	}
	
function ListReports()
	{	
		$this->db->select("oid, oid_ref, complete, fname, lname, email, buytype, subtype, admin, status, endprice, endprice_delivery, time");
		$this->db->order_by("time", "DESC");
		$this->query = $this->db->get('orders');

		if ($this->query->num_rows() > 0) 
			{
			foreach ($this->query->result_array() as $k => $v)	
				{
				if ($v['status'] != '') {
										$v['status'] = unserialize($v['status']);
										$v['status'] = end($v['status']);
										}
				$this->orders[$v['oid']] = $v;	
				}				
			return $this->orders;
			}	
	}
function GetPPLogData($id)
	{
		$this->db->select("payproc_data");
		$this->db->where('oid', (int)$id);

		$this->query = $this->db->get('orders');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return unserialize($this->result['payproc_data']);
			}
	}
function GetStatuses($buytype = '')
	{
		if ((int)$buytype > 0) $this->db->where('buytype', (int)$buytype);
		$this->db->order_by("buytype", "asc"); 
		$this->db->order_by("status_id", "asc"); 
		$this->query = $this->db->get('order_status');
			if ($this->query->num_rows() > 0) 
			{
				if ((int)$buytype == 0) 
				{
					foreach ($this->query->result_array() as $key => $value) 
					{
					$this->returnval[$value['buytype']][$value['status_id']] = $value;
					}
				}
				else 
				{
					foreach ($this->query->result_array() as $key => $value) 
					{
					$this->returnval[$value['status_id']] = $value;
					}
					
				}
				
				return $this->returnval;
			}
	}
function GetItem($id, $buytype = '')
	{
		$this->db->where('oid', (int)$id);

		$this->query = $this->db->get('orders');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			if (($this->result['buytype'] != '1') && ($this->result['buytype'] != '3'))
			{
				return false;
			}
			else
			{
				if ($this->result['status'] != '' && $this->result['status'] != ' ') $this->result['status'] = unserialize($this->result['status']);				
				return $this->result;
			}
			}
		
	}	

function GetOrder($id, $buytype = '')
	{
		$this->db->where('oid', (int)$id);
		if (is_array($buytype))
			{
				/*foreach ($buytype as $k => $b)
				{
					$this->db->where('buytype', $b);
				}*/
			}
		else
		{
			//$this->db->where('buytype', (int)$buytype);
		}
		$this->query = $this->db->get('orders');
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->row_array();
			}
		
	}
	
function GetLabelData($id)
	{
		$this->db->select("oid, fname, lname");
		$this->db->where('oid', (int)$id);
		$this->query = $this->db->get('orders');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result;
			}
		
	}
function GetOnlyOrderData($id)
	{
		$this->db->select('order');
		$this->db->where('oid', (int)$id);
		$this->query = $this->db->get('orders');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			$this->result = unserialize($this->result['order']);			
			return $this->result;
			}		
	}
function GetOrderStatuses($id)
	{
		$this->db->select('status');
		$this->db->where('oid', (int)$id);
		$this->query = $this->db->get('orders');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result['status'];
			}
	}
	
function GetClientData($id)
	{
		$this->db->select('email');
		$this->db->where('oid', (int)$id);
		$this->query = $this->db->get('orders');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result['email'];
			}
	}
function GetBuytype($id)
	{
		$this->db->select('buytype');
		$this->db->where('oid', (int)$id);
		$this->query = $this->db->get('orders');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result['buytype'];
			}
	}
function GetDeletionData($id)
	{
		$this->db->select('buytype, complete');
		$this->db->where('oid', (int)$id);
		$this->query = $this->db->get('orders');
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->row_array();
			}
	}
function UpdateOrderStatuses($id, $data)
	{
		$this->db->update('orders', $data, array('oid' => (int)$id));
	}
function Delete($id)
	{	
		$deldata = $this->GetDeletionData((int)$id);
		if ($deldata['complete'] != 1)
		{
		$this->db->where('oid', (int)$id);
		$this->db->where('complete != 1');
		$this->db->delete('orders'); 
		
		
				$this->msg['msg_date'] = CurrentTime();
				$this->msg['msg_title'] = 'Order '.$id.' Deleted @ '.FlipDateMail(CurrentTime());
				$this->msg['msg_body'] = 'Order '.$id.' Deleted by Admin Account: "'. $this->session->userdata['name'].'"';
				$this->mailid = 7;
				GoMail($this->msg);
				$this->db->insert('admin_history',$this->msg);
		}
		else 
		{
		echo '<div style="height:25px; line-height:25px; vertical-align:middle; color:#FFF; background:#F00; font-weight:bolder; font-size:14px;">ERROR: You cannot delete completed orders. Contact the administrator if you wish to remove a completed order.</div>';
		$msgdata['msg_title'] = 'Attempt to delete order.';
		$msgdata['msg_body'] = 'An attempt has been made to delete a completed order. This may just be a mistake of the click. You are being informed just incase.<br><br>IP: '.$_SERVER['REMOTE_ADDR'].', Admin Account: "'. $this->session->userdata['name'].'"';
		
		$this->mailid = 8;
		GoMail($msgdata);
		exit();
		}
	}	
function Insertorder($data)
	{	
		$this->db->insert('orders', $data);
	}
function UpdateOrder($id, $data)
	{	
		$this->db->update('orders', $data, array('oid' => (int)$id));
	}
function InsertAmendment($data)
	{	
		$this->db->insert('order_amendments', $data);
	}
function GetOrderData($id)
	{
		$this->db->select('oid, email, buytype');
		$this->db->where('oid', (int)$id);
		$this->db->where('buytype != 5');
		//$this->db->where('complete', '0');
		$this->query = $this->db->get('orders');
		
		if ($this->query->num_rows() > 0) return $this->query->row_array();
		
	}
function GetAmendments($id)
	{	
		$this->db->where('oid_ref', (int)$id);
		$this->db->where('buytype', '5');
		$this->db->order_by("time", "ASC");
		$this->query = $this->db->get('orders');

		if ($this->query->num_rows() > 0) return $this->query->result_array();

	}
function GetAmendmentData($id)
	{   
		$this->db->select('order, time');
		$this->db->where('buytype', '5');
		$this->db->where('oid', (int)$id);
		$this->query = $this->db->get('orders');

		if ($this->query->num_rows() > 0) return $this->query->row_array();
		
	}
function DeleteAmendment($id)
	{			
		$this->db->where('oid', (int)$id);
		$this->db->where('buytype', '5');
		$this->db->where('complete', '0');
		$this->db->delete('orders'); 
	}
function GetItems($oid)
	{
		$this->db->where('oid', (int)$oid);
		$this->db->order_by("time", "ASC");
		$this->query = $this->db->get('order_items');
		if ($this->query->num_rows() > 0) return $this->query->result_array();
	}
function InsertItem($data)
	{	
		$this->db->insert('order_items', $data);
	}
function UpdateItem($data, $oiid, $oid)
	{	
		$this->db->update('order_items', $data, array('oiid' => (int)$oiid, 'oid' => (int)$oid));
	}
function DeleteItem($oiid, $oid)
	{			
		$this->db->where('oiid', (int)$oiid);
		$this->db->where('oid', (int)$oid);
		$this->db->delete('order_items'); 
	}
}

?>
