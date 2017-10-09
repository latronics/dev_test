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











function GetOrders($page = 1, $perpage = 100,$filtertype = false,$filtersubtype = false, $return = false, $listingid = FALSE, $onlylisting = FALSE, $getall= FALSE, $page_oficial = 0)

{
    if($page_oficial == 0)
    {
        $page_oficial == 1;
    }else
    {
        $page_oficial++;
    }
$results = 0;
  /*  if ($filtertype && $filtertype == 'FALSE') $filtertype = FALSE;

    if ($filtersubtype && $filtersubtype == 'FALSE') $filtersubtype = FALSE;

    if ($return && $return == "FALSE") { echo 1;$return = FALSE;}

    if ($listingid && $listingid == 'FALSE') $listingid = FALSE;

    if ($onlylisting && $onlylisting == 'FALSE') $onlylisting = FALSE;

    */

    $CI =& get_instance();

   

    //printcool($_POST);

        if (trim($filtertype) == 'false') $filtertype = false;

        $channel[1] = TRUE;

        $channel[2] = TRUE;

        $channel[4] = TRUE;

        $this->sortstring = $type;

	$this->spectype = false;

	$this->sorttype = false;	

	$psql[1] = '';

        $psql[2] = '';

        $psql[4] = '';

        $timeframe = TRUE;

        if ($listingid && (int)$listingid > 0) $this->mysmarty->assign('listingid', $listingid);





	if ($filtertype && !isset($_POST['osrc']) && !$listingid) switch ($filtertype)

	{		

            //CHANNEL1 = EBAY

            //CHANNEL2 = WEB

            //CHANNEL4 = WARE

		case 'Ebay':	

		$this->sorttype = 2;

                $channel[2] = FALSE;

                $channel[4] = FALSE;

		break;

		case 'EbayNotPaid':	

		$this->sorttype = 21;

                $psql[1] .=  "AND notpaid = 1 ";

                $channel[2] = FALSE;

                $channel[4] = FALSE;

		break;

		case 'EbayRefunded':	

		$this->sorttype = 22;

                $psql[1] .=  "AND (customcode = 1 OR refunded = 1) AND sellingstatus != 'PartiallyPaid' ";

                $channel[2] = FALSE;

                $channel[4] = FALSE;

		break;		

		case 'EbayPartialRefund':	

		$this->sorttype = 25;

                $psql[1] .=  "AND (customcode = 1 OR refunded = 1)AND sellingstatus = 'PartiallyPaid' ";

                $channel[2] = FALSE;

                $channel[4] = FALSE;

		break;		

		case 'EbayPendingPay':	

		$this->sorttype = 23;

                $psql[1] .=  "AND paidtime = '' AND notpaid = 0 AND refunded = 0 ";

                $channel[2] = FALSE;

                $channel[4] = FALSE;

		break;

		case 'NeedAttention':	

		$this->sorttype = 24;

                $channel[2] = FALSE;

                $channel[4] = FALSE;

		break;

		case 'Site':	

		$this->sorttype = 3;

                $channel[1] = FALSE;

                $channel[4] = FALSE;

		break;

		case 'Paid':	

		$this->sorttype = 4;

                $psql[1] .=  "AND paidtime != '' ";

                $psql[2] .=  "AND complete = 1 ";

                $channel[4] = FALSE;               

		break;

		case 'Processed':	

		$this->sorttype = 5;

                $psql[1] .=  "AND mark != 0 ";

                $psql[2] .=  "AND mark != 0 ";

                $channel[4] = FALSE;

		break;

		case 'NoProcessed':	

		$this->sorttype = 6;

                $psql[1] .=  "AND mark = 0 ";

                $psql[2] .=  "AND mark = 0 ";

                $channel[4] = FALSE;

		break;

		case 'Asc':	

		$this->sorttype = 7;

                $psql[1] .=  "AND cascupd != 0 ";

                $channel[2] = FALSE;

                $channel[4] = FALSE;

		break;

		case 'NoAsc':	

		$this->sorttype = 8;

                $psql[1] .=  "AND cascupd = 0 ";

                $channel[2] = FALSE;

                $channel[4] = FALSE;

		break;

		case 'NoPaid':	

		$this->sorttype = 9;

                $psql[1] .=  "AND paidtime = '' AND notpaid = 0 AND refunded = 0 ";

                $psql[2] .=  "AND ( complete != 1 AND complete != '-1') ";

                $channel[4] = FALSE;

		break;

		case 'Ware':	

		$this->sorttype = 10;

                $channel[1] = FALSE;

                $channel[2] = FALSE;

		break;

		default: 

		$this->sorttype = 1;

		$this->sortstring = 'All';		

	}	

	if ($filtersubtype && !isset($_POST['osrc']) && !$listingid) switch ($filtersubtype)

	{		

		case 'NotPaid':	

		$this->spectype = 1;

                $psql[1] .=  "AND paidtime = '' ";

                $psql[2] .=  "AND ( complete != 1 AND complete != '-1' ) ";

                $psql[4] .=  "";

		break;

		case 'NotShipped':	

		$this->spectype = 2;

                $psql[1] .=  "AND paidtime != '' AND mark = 0 AND notpaid = 0 AND refunded = 0 ";

                $psql[2] .=  "AND complete = 1 AND mark = 0";

                $psql[4] .=  "";    

		break;

		case 'Sold':	

		$this->spectype = 3;

                $psql[1] .=  "AND paidtime != '' AND mark != 0 ";

                $psql[2] .=  "AND complete = 1 AND mark != 0 ";

                $psql[4] .=  "";    

		break;

		case 'Fraud':	

		$this->spectype = 4;

                $psql[1] .=  "";

                $psql[2] .=  "AND complete < 0 ";

                $psql[4] .=  "";

                $channel[1] = FALSE;

                $channel[4] = FALSE;

		break;

	}



    $this->mysmarty->assign('filtertype', $filtertype);

	$this->mysmarty->assign('filtersubtype', $filtersubtype);

	$addurl = '/';

	if ($filtertype) $addurl .= $filtertype.'/';

    if ($filtersubtype) $addurl .= $filtersubtype.'/';

    $this->mysmarty->assign('addurl', $addurl);







       $this->mysmarty->assign('sorttype', $this->sorttype);

	if (isset($this->spectype)) $this->mysmarty->assign('spectype', $this->spectype);

	$this->mysmarty->assign('sortstring', $this->sortstring);

//var_dump($onlylisting);

        if ($onlylisting)

        {

                

            if (isset($this->sorttype) && $this->sorttype == 2 && isset($this->spectype) && $this->spectype == 2)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/NotShipped');            

            elseif (isset($this->sorttype) && $this->sorttype == 2)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/Ebay');

            elseif (isset($this->sorttype) && $this->sorttype == 23)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/EbayPendingPay');

            elseif (isset($this->sorttype) && $this->sorttype == 21)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/EbayNotPaid');

            elseif (isset($this->sorttype) && $this->sorttype == 25)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/EbayPartialRefund');

            elseif (isset($this->sorttype) && $this->sorttype == 24)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/NeedAttention');

            elseif (isset($this->sorttype) && $this->sorttype == 3)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/Site');

            elseif (isset($this->sorttype) && $this->sorttype == 10)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/Ware');

            elseif (isset($this->sorttype) && $this->sorttype == 4)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/Paid');

            elseif (isset($this->sorttype) && $this->sorttype == 9)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/NoPaid');

            elseif (isset($this->sorttype) && $this->sorttype == 5)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/Processed');

            elseif (isset($this->sorttype) && $this->sorttype == 6)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/NoProcessed');

            elseif (isset($this->sorttype) && $this->sorttype == 7)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/Asc');

            elseif (isset($this->sorttype) && $this->sorttype == 8)$this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/NoAsc');

            else $this->mysmarty->assign("gotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/');

                

            $this->mysmarty->assign("maingotourl", 'Myautopilot/Chart/'.(int)$onlylisting.'/');

            $this->mysmarty->assign("mainpageurl", 'Myautopilot/Chart/'.(int)$onlylisting);

        }

        else

        {

            if( !isset($_POST['osrc']) && !$listingid)

            {

            if (isset($this->sorttype) && $this->sorttype == 2 && isset($this->spectype) && $this->spectype == 2)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/NotShipped');

            elseif (isset($this->sorttype) && $this->sorttype == 2)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/Ebay');

            elseif (isset($this->sorttype) && $this->sorttype == 23)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/EbayPendingPay');

            elseif (isset($this->sorttype) && $this->sorttype == 21)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/EbayNotPaid');

            elseif (isset($this->sorttype) && $this->sorttype == 25)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/EbayPartialRefund');

            elseif (isset($this->sorttype) && $this->sorttype == 24)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/NeedAttention');

            elseif (isset($this->sorttype) && $this->sorttype == 3)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/Site');

            elseif (isset($this->sorttype) && $this->sorttype == 10)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/Ware');

            elseif (isset($this->sorttype) && $this->sorttype == 4)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/Paid');

            elseif (isset($this->sorttype) && $this->sorttype == 9)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/NoPaid');

            elseif (isset($this->sorttype) && $this->sorttype == 5)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/Processed');

            elseif (isset($this->sorttype) && $this->sorttype == 6)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/NoProcessed');

            elseif (isset($this->sorttype) && $this->sorttype == 7)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/Asc');

            elseif (isset($this->sorttype) && $this->sorttype == 8)$this->mysmarty->assign("gotourl", 'Myebay/Orders/1/'.(int)$perpage.'/NoAsc');

            else $this->mysmarty->assign("gotourl", 'Myebay/Orders');

            }

            $this->mysmarty->assign("maingotourl", 'Myebay/Orders');

            $this->mysmarty->assign("mainpageurl", 'Myebay/Orders/1/'.(int)$perpage);

        }

        

	$this->session->set_userdata('sorttype', $this->sorttype);	

	$this->session->set_userdata('sortstring', $this->sortstring);

    

        

    

    

    if (!$return) $this->mysmarty->assign('noenter', '

<script type="text/javascript"> 



function stopRKey(evt) { 

  var evt = (evt) ? evt : ((event) ? event : null); 

  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 

  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 

} 



document.onkeypress = stopRKey; 

</script> <script type="text/javascript" src="/js/warehouse.js"></script>

');

        $CI->load->model('Myseller_model');

        $CI->Auth_model->CheckOrders();

       if (!$return) $this->mysmarty->assign('floatmenu', TRUE);

	$CI->load->model('Myseller_model');

	$CI->Myseller_model->assignstatuses();

        

        $this->mysmarty->assign('cal', TRUE);

        $tdf =46800;

    	$this->ofrom = $ofrom = mktime()+$tdf;

   		$this->oto = $oto = (mktime()+$tdf)-1296000;

        $dfrom = date('m/j/Y');

        $dto = date('m/j/Y', strtotime("-15 days"));





        $this->mysmarty->assign('d1from', date('m/j/Y'));	

        $this->mysmarty->assign('d1to', date('m/j/Y', strtotime("-30 days")));	



        $this->mysmarty->assign('d2from', date('m/j/Y'));	

        $this->mysmarty->assign('d2to', date('m/j/Y', strtotime("-60 days")));	



        $this->mysmarty->assign('d3from', date('m/j/Y'));	

        $this->mysmarty->assign('d3to', date('m/j/Y', strtotime("-90 days")));	



        //$sesfrom = $this->session->userdata('dfrom');

        //$sesto = $this->session->userdata('dto');

        $sesfrom = false;

        $sesto = false;



        if (!$sesfrom && !$sesto)

        {

                $sesfrom = $this->session->userdata('dfrom');

                $this->session->set_userdata('dfrom', $sesfrom);

                $sesto = $this->session->userdata('dto');

                $this->session->set_userdata('dto', $sesto);	

                $nav = true;

        }		



        if (($sesfrom || $sesto) && !isset($nav)) $this->mysmarty->assign('dateclean', TRUE);	 



        if (isset($_POST['ofrom']) || $sesfrom)

        {

                if (isset($_POST['ofrom']))

                {

                $dfrom = trim($_POST['ofrom']);

                $this->session->set_userdata('dfrom', $dfrom);	

                }

                else $dfrom = $sesfrom;

                $postfrom = explode('/', $dfrom);

            	$this->ofrom = $ofrom = mktime(23, 59, 59, $postfrom[0], $postfrom[1], $postfrom[2])+$tdf;

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

                $this->oto = $oto = mktime(0, 0, 0, $postto[0], $postto[1], $postto[2])+$tdf;

                $this->mysmarty->assign('dateclean', TRUE);

        }



        $this->mysmarty->assign('dfrom', $dfrom);	

        $this->mysmarty->assign('dto', $dto);

        

        if (!$return) $this->mysmarty->assign('area', 'Transactions');

     

    ini_set('memory_limit','2048M');

      
    if (!$return) 

    {

    $this->mysmarty->assign('hot', TRUE);

    $csq = '';
	if ($channel[1]) 

   {

                

    $csql .= "SELECT DISTINCT e.et_id AS orderkey ";

    if ($listingid) $csql .= ' FROM (ebay_transactions e) WHERE e_id = "'.(int)$listingid.'" ';

    elseif(isset($_POST['osrc'])) $csql .= " FROM (ebay_transactions e) WHERE et_id = '".$this->input->post('osrc', TRUE)."' OR  buyerid LIKE '%".$this->input->post('osrc', TRUE)."%' OR buyeremail LIKE '%".$this->input->post('osrc', TRUE)."%' OR notes LIKE '%".$this->input->post('osrc', TRUE)."%' ";

    elseif (isset($this->sorttype) && $this->sorttype == 24)  $csql .= " FROM (ebay_transactions e) LEFT JOIN warehouse w ON e.et_id = w.sold_id WHERE (e.notpaid != 0 OR e.refunded != 0 OR e.returnnotif IS NOT NULL) AND w.vended > 0 AND w.deleted = 0 AND w.nr = 0 AND w.channel = 1 AND w.sold_id != 0";

    elseif ($timeframe) $csql .=  " FROM (ebay_transactions e) WHERE mkdt <= ".$ofrom." AND mkdt >= ".$oto." ";

    else $csql .=  " FROM (ebay_transactions e) ";



    $csql .=  $psql[1];   

   //printcool($csql);

    if ($channel[4] ||$channel[2]) $csql .= " UNION ALL ";

   }

   if ($channel[4]) 

   {

    $csql .= " SELECT DISTINCT o.woid AS orderkey ";

    //$csql .= " ";

    if ($listingid)  $csql .= ' FROM (warehouse_orders o) LEFT JOIN warehouse w ON o.woid = w.sold_id WHERE w.vended > 0 AND w.deleted = 0 AND w.nr = 0 AND w.channel = 4 AND w.listingid = "'.(int)$listingid.'" ';

    elseif(isset($_POST['osrc'])) $csql .= " FROM (warehouse_orders o) WHERE woid = '".$this->input->post('osrc', TRUE)."'  OR buyer LIKE '%".$this->input->post('osrc', TRUE)."%' OR notes LIKE '%".$this->input->post('osrc', TRUE)."%' ";

    elseif ($timeframe) $csql .=  " FROM (warehouse_orders o) WHERE timemk <= ".$ofrom." AND timemk >= ".$oto." ";

    $csql .=  $psql[4]; 

   

    if ($channel[2] ||$channel[1] ) $csql .= " UNION ALL ";

   } 

   if ($channel[2])

   {

    $csql .= "SELECT distinct oid  as orderkey ";

   // $csql .=  "";

    if ($listingid)  $csql .= ' FROM orders WHERE eids LIKE "%|'.(int)$listingid.'|%"';

    elseif(isset($_POST['osrc'])) $csql .= " FROM orders WHERE oid = '".$this->input->post('osrc', TRUE)."' OR fname LIKE '%".$this->input->post('osrc', TRUE)."%' OR lname LIKE '%".$this->input->post('osrc', TRUE)."%' OR email LIKE '%".$this->input->post('osrc', TRUE)."%' ";

    elseif ($timeframe) $csql .=  " FROM orders  WHERE submittime <= ".$ofrom." AND submittime >= ".$oto." ";

    $csql .=  $psql[2]; 

   }

   

   if(isset($_POST['osrc'])) $this->mysmarty->assign('osrc',$this->input->post('osrc', TRUE));

   else  $this->mysmarty->assign('osrc',false);   

       

  

  $this->mysmarty->assign('sqldebug',$csql);

    $cn = $this->db->query($csql);
    $results = $cn->num_rows();
    $ordercount = $cn->num_rows();

    $this->mysmarty->assign('ordercount', $ordercount);

    

    if ((int)$perpage <= 0) $perpage = 100;

    

  

    $this->mysmarty->assign('page', $page_oficial);

    

    

    //if ((int)$page > 0) $page = $page - 1;

    $tolimit = (int)$page*(int)$perpage;

    $pages = ceil($ordercount/(int)$perpage);			

   

    for ( $counter = 1; $counter <= $pages ; $counter++)

    {

        $before = 5;

        $after= 5;

        $min = (int)$page_oficial -$before;

        if ($min < 0) $after = $before - $min;

        $max = (int)$page_oficial +$after;

        if ($max > $pages) $before = $before + ($max-$pages);

        

       if ( ($counter >= ((int)$page_oficial -$before)) && ($counter <= ((int)$page_oficial +$after)) )

       {

        $pagearray[] = $counter;

       }

       

    }

    $this->mysmarty->assign('perpage', (int)$perpage);

    $this->mysmarty->assign('page', $page_oficial);

    $this->mysmarty->assign('pages', $pages);

    $this->mysmarty->assign('pagearray', $pagearray);

    }

    $sql = '';

    if ($channel[1])

    {

        $sql .= "SELECT distinct e.et_id AS orderkey,e.mkdt as timekey, 'ebay' as typekey, '1' as channel,  

e.datetime	as	created,

e.paid	as	paid,

e.buyeremail	as	buyeremail,

e.buyerid	as	buyerid,

e.e_id	as	e_id";



       if (!$return) $sql .= " 

,e.paidtime as field_timepaid, 

e.notpaid as field_notpaid, 

e.mark as field_mark,

e.customcode as field_customcode, 

e.returned as field_returned, 

e.returned_refunded as field_refunded, 

e.sellingstatus as field_sellingstatus ,



 

e.mkdt	as	createdmk,

e.rec	as	outerkey,

e.admin	as	admin,

e.revs	as	revs,

e.notes	as	notes,

e.return_id	as	return_id,
e.returnid as returnid,
e.returned_notes	as	returned_notes,

e.returned_time	as	returned_time,

e.returned_recieved	as	returned_recieved,

e.returned_amount	as	returned_amount,

e.returned_extracost	as	returned_extracost,

e.buyeraddress	as	buyeraddress,

e.returntype	as	returntype,

e.returnQuantity	as	returnQuantity,

e.cascupd	as	cascupd,

e.market	as	market,

e.autoid	as	autoid,

e.autotitle	as	autotitle,

e.contorderid	as	contorderid,

e.eachpaid	as	eachpaid,

e.fee	as	fee,

e.shipping	as	shipping,

e.tracking	as	tracking,

e.paydata	as	paydata,

e.pptransid	as	pptransid,

e.itemid	as	itemid,

e.qtyof	as	qtyof,

e.qty	as	qty,

e.sn	as          sn,

`e`.`sasc`	as	`sasc`,

`e`.`asc`	as	`asc`,

e.ssc	as	ssc,

e.ebsold	as	ebsold,

e.updated	as	updated,

e.transid	as	transid,

e.accounted	as	accounted,

e.mverif	as	mverif,

e.refunded	as	refunded,

e.pendingpay	as	pendingpay,

e.attention	as	attention,

e.gmt	as	gmt,

		

''	as	subchannel,

''	as	sc_id,

''	as	otype,

''	as	buyer,

''	as	wholeprice,

''	as	rem,

''	as	bcns,

''	as	rbcns,

		

''	as	accounted,

''	as	time,

''	as	staffcomments,

''	as	totalweight,

''	as	fid,

''	as	payproc,

''	as	payproc_data,

''	as	courier_log,

''	as	pendquant_action,

''	as	sysdata,

''	as	CheckoutStatus,

''	as	OrderStatus,

''	as	oid_ref,

''	as	buytype,

''	as	subtype,

''	as	is_special,

''	as	status,

''	as	returnedresponse,

''	as	sameadr,

''	as	tel,

''	as	comments,

''	as	`order`,

''	as	eids,

''	as	delivery,

e.pushed as pushed

  ";



      if (isset($this->sorttype) && $this->sorttype == 24)

      {

          $sql .= " FROM (ebay_transactions e) LEFT JOIN warehouse w ON e.et_id = w.sold_id WHERE (e.notpaid != 0 OR e.refunded != 0 OR e.returnnotif IS NOT NULL) AND w.vended > 0 AND w.deleted = 0 AND w.nr = 0 AND w.channel = 1 AND w.sold_id != 0";

      }

      else

      {

          $sql .= " FROM (ebay_transactions e) ";

            if ($listingid) $sql .= ' WHERE e_id = "'.(int)$listingid.'" ';

            elseif(isset($_POST['osrc'])) $sql .= " WHERE et_id = '".$this->input->post('osrc', TRUE)."' OR  buyerid LIKE '%".$this->input->post('osrc', TRUE)."%' OR buyeremail LIKE '%".$this->input->post('osrc', TRUE)."%' OR notes LIKE '%".$this->input->post('osrc', TRUE)."%' ";

            elseif ($timeframe || $onlylisting)

            {
				if ($getall) $sql .= ' WHERE e_id = "'.(int)$onlylisting.'" ';
				else
				{
               	 $sql .=  " WHERE mkdt <= ".$ofrom." AND mkdt >= ".$oto." ";

                 if ($onlylisting) $sql .= ' AND e_id = "'.(int)$onlylisting.'" ';
				}
            }

      }



// if ($q->num_rows() > 0  && ((isset($this->orderchannel) && $this->orderchannel == 1) || !isset($this->orderchannel))) 

        $sql .=  $psql[1];



    if ($channel[4]) $sql .= " UNION ALL ";

    }

    if ($channel[4])

    {

       $sql .= " SELECT distinct o.woid AS orderkey, timemk as timekey,  'warehouse' as typekey, '4' as channel,  

o.paid	as	paid,

o.time	as	created,

''	as	buyeremail,

''	as	buyerid,

''	as	e_id";



 if (!$return) $sql .= " 

,o.time as  field_timepaid, 

'' as field_notpaid, 

o.mark as field_mark, 

'' as field_customcode, 

o.returned as field_returned, 

o.returned_refunded as field_refunded,

'' as field_sellingstatus,



o.timemk	as	createdmk,

o.notes	as	notes,

o.admin	as	admin,

revs	as	revs,

o.return_id	as	return_id,
'' as returnid,
o.returned_notes	as	returned_notes,

o.returned_time	as	returned_time,

o.returned_recieved	as	returned_recieved,

o.returned_amount	as	returned_amount,

o.returned_extracost	as	returned_extracost,

o.shipped	as	shipping,

''	as	buyeraddress,

''	as	returntype,

''	as	returnQuantity,

''	as	cascupd,

''	as	market,

''	as	autoid,

''	as	autotitle,

''	as	contorderid,

''	as	eachpaid,

''	as	fee,

''	as	shipping,

''	as	tracking,

''	as	paydata,

''	as	pptransid,

''	as	itemid,

''	as	qtyof,

''	as	qty,

''	as	sn,

''	as	`sasc`,

''	as	`asc`,

''	as	ssc,

''	as	ebsold,

''	as	updated,

''	as	transid,

''	as	accounted,

''	as	mverif,

''	as	refunded,

''	as	pendingpay,

''	as	attention,

''	as	gmt,

		

subchannel	as	subchannel,

sc_id	as	sc_id,

otype	as	otype,

buyer	as	buyer,

wholeprice	as	wholeprice,

rem	as	rem,

bcns	as	bcns,

rbcns	as	rbcns,

		

''	as	accounted,

''	as	time,

''	as	staffcomments,

''	as	totalweight,

''	as	fid,

''	as	payproc,

''	as	payproc_data,

''	as	courier_log,

''	as	pendquant_action,

''	as	sysdata,

''	as	CheckoutStatus,

''	as	OrderStatus,

''	as	oid_ref,

''	as	buytype,

''	as	subtype,

''	as	is_special,

''	as	status,

''	as	returnedresponse,

''	as	sameadr,

''	as	tel,

''	as	comments,

''	as	`order`,

''	as	eids,

''	as	delivery ,

'' as pushed

  ";

    if ($listingid) $sql .= ' FROM (warehouse_orders o) LEFT JOIN warehouse w ON o.woid = w.sold_id WHERE w.vended > 0 AND w.deleted = 0 AND w.nr = 0 AND w.channel = 4 AND w.listingid = "'.(int)$listingid.'" ';

    elseif(isset($_POST['osrc'])) $sql .= " FROM (warehouse_orders o) WHERE woid = '".$this->input->post('osrc', TRUE)."' OR buyer LIKE '%".$this->input->post('osrc', TRUE)."%' OR notes LIKE '%".$this->input->post('osrc', TRUE)."%' ";

    elseif ($timeframe || $onlylisting)

    {
		if ($getall) $sql .= " FROM (warehouse_orders o) LEFT JOIN warehouse w ON o.woid = w.sold_id WHERE w.vended > 0 AND w.deleted = 0 AND w.nr = 0 AND w.channel = 4 AND w.listingid = '".(int)$onlylisting."'";
		else
		{
          if ($onlylisting) $sql .=  " FROM (warehouse_orders o) LEFT JOIN warehouse w ON o.woid = w.sold_id WHERE w.vended > 0 AND w.deleted = 0 AND w.nr = 0 AND w.channel = 4 AND w.listingid = '".(int)$onlylisting."'  AND timemk <= ".$ofrom." AND timemk >= ".$oto." ";

          else $sql .=  " FROM (warehouse_orders o)  WHERE timemk <= ".$ofrom." AND timemk >= ".$oto." ";
		}
    }

    else $sql .= ' FROM  (warehouse_orders o) ';

 //if ($q->num_rows() > 0  && ((isset($this->orderchannel) && $this->orderchannel == 1) || !isset($this->orderchannel)))

    $sql .=  $psql[4];

    }

    if ($channel[2] && ($channel[1] || $channel[4])) $sql .= " UNION ALL ";

    if ($channel[2])

    {

        $sql .= " SELECT oid AS orderkey, submittime as timekey, 'website' as typekey, '2' as channel, 

complete_time	as	created,

endprice	as	paid,

email	as	buyeremail,

concat(`fname`,' ',`lname`)	as  buyerid,

''	as	e_id";

 if (!$return) $sql .= " 

,complete as  field_timepaid, 

'' as field_notpaid, 

mark as field_mark, 

'' as field_customcode, 

returned as field_returned, 

returned_refunded as field_refunded,

'' as field_sellingstatus,



submittime	as	createdmk, 

endprice_delivery	as	shipping,

admin	as	admin,

revs	as	revs,

return_id	as	return_id,
'' as returnid,
returned_notes	as	returned_notes,

returned_time	as	returned_time,

returned_recieved	as	returned_recieved,

returned_amount	as	returned_amount,

returned_extracost	as	returned_extracost,

''	as	notes, 

concat(address,', ',city,', ',state,', ',postcode,', ',country,', ',residential,', ',daddress,' - ',dcity,', ',dstate,', ',dpostcode,', ',dcountry)	as	buyeraddress, 

''	as	returntype,

''	as	returnQuantity,

''	as	cascupd,

''	as	market,

''	as	autoid,

''	as	autotitle,

''	as	contorderid,

''	as	eachpaid,

''	as	fee,

''	as	shipping,

''	as	tracking,

''	as	paydata,

''	as	pptransid,

''	as	itemid,

''	as	qtyof,

''	as	qty,

''	as	sn,

''	as	`sasc`,

''	as	`asc`,

''	as	ssc,

''	as	ebsold,

''	as	updated,

''	as	transid,

''	as	accounted,

''	as	mverif,

''	as	refunded,

''	as	pendingpay,

''	as	attention,

''	as	gmt,

		

''	as	subchannel,

''	as	sc_id,

''	as	otype,

''	as	buyer,

''	as	wholeprice,

''	as	rem,

''	as	bcns,

''	as	rbcns,

		

accounted	as	accounted,

time	as	time,

staffcomments	as	staffcomments,

totalweight	as	totalweight,

fid	as	fid,

payproc	as	payproc,

payproc_data	as	payproc_data,

courier_log	as	courier_log,

pendquant_action	as	pendquant_action,

sysdata	as	sysdata,

CheckoutStatus	as	CheckoutStatus,

OrderStatus	as	OrderStatus,

oid_ref	as	oid_ref,

buytype	as	buytype,

subtype	as	subtype,

is_special	as	is_special,

status	as	status,

returnedresponse	as	returnedresponse,

sameadr	as	sameadr,

tel	as	tel,

comments	as	comments,

`order`	as	`order`,

eids	as	eids,

delivery	as	delivery,

'' as pushed";



$sql .= " FROM orders ";

    if ($listingid) $sql .= ' WHERE eids LIKE "%|'.(int)$listingid.'|%" ';

    elseif(isset($_POST['osrc'])) $sql .= " WHERE oid = '".$this->input->post('osrc', TRUE)."' OR fname LIKE '%".$this->input->post('osrc', TRUE)."%' OR lname LIKE '%".$this->input->post('osrc', TRUE)."%' OR email LIKE '%".$this->input->post('osrc', TRUE)."%' ";

    elseif ($timeframe || $onlylisting)

    {
		if ($getall) $sql .= ' WHERE eids LIKE "%|'.(int)$onlylisting.'|%" ';
		else
		{
			
         $sql .=  " WHERE submittime <= ".$ofrom." AND submittime >= ".$oto." ";

         if ($onlylisting) $sql .=  ' AND eids LIKE "%|'.(int)$onlylisting.'|%" ';
		}
    }



     $csql .=  $psql[2];

    }

    $sql .= " ORDER BY `timekey` DESC";



   //printcool ($sql);
   //printcool ($psql);
   //printcool ($getall);

    //var_dump ($return);

    if (!$return) $sql .= " limit ".(($perpage*$page)-$perpage).", ".$perpage;

  //printcool($sql);

//printcool ($sql);

    $po = $this->db->query($sql);
    $results = $po->num_rows();
    $idarray = array();

    $ridarray = array();
	$rfidarray = array();
    $list = array();

    if ($po->num_rows() >0)

    { 

        //printcool($po->num_rows());

       // printcool($po->result_array());

        //$mkdtdupcheck = 0;

        foreach ($po->result_array() as $v)

        {

           // if ((int)$v['createdmk'] == (int)$mkdtdupcheck) $v['createdmk'] = $v['createdmk']-1;

           // $mkdtdupcheck = $v['createdmk'];

           // if ($v['createdmk'] < $oldtrestentry) $oldtrestentry = $v['createdmk'];

            

            if (!$return) switch ($v['typekey'])

            {

                case 'ebay':                       

                       if (strlen($v['paydata']) > 10) 

                       {				

                               $v['paydata'] = unserialize($v['paydata']);

                               if (isset($v['paydata'])) unset($v['paydata']['PaidTime']);

                       }

                       else $v['paydata'] = false;

                       //$list[$v['createdmk'].'E'] = $v;                 

                       //$listings[$v['e_id']] = TRUE;

                break;

                case 'website':

                       if ($v['status'] != '' && $v['status'] != ' ')

                       {

                            $v['status'] = unserialize($v['status']);

                            //$v['origstatus'] = $v['status'][0];										

                            $v['status'] = end($v['status']);

                       }

                       $v['created'] = $v['time'];

                       $v['createdmk'] = explode(' ', $v['time']);

				$v['createdmk'] = explode('-', $v['createdmk'][0]);			

				if (isset($v['createdmk'][1]) && isset($v['createdmk'][2]) && isset($v['createdmk'][0])) $v['createdmk'] = (int)mktime(0, 0, 0, $v['createdmk'][1], $v['createdmk'][2], $v['createdmk'][0]);

				else $v['createdmk'] = false;

                        if (strlen($v['order']) > 9) 

                        { 

                                $v['order'] = unserialize($v['order']); 

                                if (is_array($v['order']))

                                foreach ($v['order'] as $k => $ov) 

                                {

                                        $os[$ov['e_id']] = $ov['quantity']; 

                                        if (!isset($ov['sn'])) $v['order'][$k]['sn'] = '';

                                        if (!isset($ov['admin'])) $v['order'][$k]['admin'] = '';

                                        $listings[$ov['e_id']] = TRUE;

                                }

                        }



                        if (strlen($v['CheckoutStatus']) > 9) $v['CheckoutStatus'] = unserialize($v['CheckoutStatus']);



                        //if ($v['createdmk'] < $oldorestentry) $oldorestentry = $v['createdmk'];



                        //$list[$v['createdmk'].'O'] = $v;                

                break;

                case 'warehouse':

                       //$list[$w['createdmk'].'W'] = $v;

                break;

              }   

              

             $list[] = $v;                 

            

             $idarray[$v['channel']][] = $v['orderkey'];

              if (!$return)

              {

                 if ((int)$v['e_id'] > 0) $listings[$v['e_id']] = TRUE;

                 if ((int)$v['return_id'] > 0) $ridarray[$v['channel']][$v['return_id']] = $v['return_id'];
				 if ((int)$v['returnid'] > 0) $rfidarray[$v['returnid']] = $v['returnid'];

              }

                       



        }

    }

    

    //printcool ($idarray);

    //printcool ($ridarray);

    //printcool ($listings);

    //printcool ($list);

    

    if (count($idarray)>0)

    {				

        $CI->load->model('Myseller_model');

        foreach ($idarray as $k => $v)

        {

           $CI->Myseller_model->getSales($v, $k);           

        }         

    }

    if (!$return)

    {

    if (count($ridarray)>0)

    {				

        $this->load->model('Myseller_model');

        foreach ($ridarray as $k => $v)

        {

           $CI->Myseller_model->getReturns($v, $k);       

        }         

    }
		if (count($rfidarray)>0)

		{

			$this->load->model('Myseller_model');

			foreach ($rfidarray as $k => $v)

			{

				$CI->Myseller_model->getNewReturns($v, $k);

			}

		}
		if (count($listings)>0)

    {	      

        $this->db->select("e_title, e_part, e_qpart, e_id, quantity, ebayquantity, ebay_id");

        $st = 0;

        foreach ($listings as $k => $v)

        {

            if ($st == 0) { $this->db->where('e_id', $k); $st++; }

            else $this->db->or_where('e_id', $k);

        }

				

	$q = $this->db->get('ebay');
    $results = $q->num_rows();
	$ebl = false;

	if ($q->num_rows() > 0) 

	{

            foreach ($q->result_array() as $k=>$v)

            {

		$ebl[$v['e_id']] = $v;

            }		

	

	}

	$this->mysmarty->assign('ebl', $ebl);  

    }         

    }

	if (!$return) $total_lines = $results / $perpage;
    else $total_lines = $results;
    $page_reference = $this->uri->segment(3);
    $page_filter = $this->uri->segment(5);
	$this->load->library('pagination');
    //CONFIG PAGINATION
    $config['uri_segment'] = 6;
    $config['base_url'] = base_url() . 'Myebay/Orders/1/100/'.$page_filter;
    $config['display_pages'] = FALSE;
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul><!--pagination-->';
    $config['first_link'] = '&laquo; First';
    $config['first_tag_open'] = '<li class="prev page">';
    $config['first_tag_close'] = '</li>' . "\n";
    $config['last_link'] = 'Last &raquo;';
    $config['last_tag_open'] = '<li class="next page">';
    $config['last_tag_close'] = '</li>' . "\n";
    $config['next_link'] = 'Next &rarr;';
    $config['next_tag_open'] = '<li class="next page">';
    $config['next_tag_close'] = '</li>' . "\n";
    $config['prev_link'] = '&larr; Previous';
    $config['prev_tag_open'] = '<li class="prev page">';
    $config['prev_tag_close'] = '</li>' . "\n";
    $config['cur_tag_open'] = '<li class="active"><a href="">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li class="page">';
    $config['num_tag_close'] = '</li>' . "\n";
    $config['page_query_string'] = FALSE;
    $config['total_rows'] = $total_lines;
    $config['per_page'] = 1;
    $config['use_page_numbers'] = TRUE;


    $this->pagination->initialize($config);
    $this->mysmarty->assign('pagination', $this->pagination->create_links());
    $this->mysmarty->assign('page_filter', $page_filter);
    $this->mysmarty->assign('page_reference', $page_reference);
    

    

   if (!$return)

   {

   $this->mysmarty->assign('list', $list);

                

   $this->mysmarty->assign('statuses', $this->GetStatuses());

   $CI->Myseller_model->getSalesListings($listings);

   $this->mysmarty->assign('gotothisurl', 'Orders/'.$page.'/'.$perpage.'/');

   $this->mysmarty->view('myebay/myebay_neworders.html');

    exit();

   }

   else 

   {

      $this->mysmarty->assign('statuses', $this->GetStatuses());

      return $list;       

   }



    

}









}

?>