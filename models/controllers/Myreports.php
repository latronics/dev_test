<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myreports extends Controller {

	/*

	The idea of this controller is to make report with two tables - one is the summary table and the other is the detailed table based on 
	the data in the summary table.

	Function FiveWeeksBefore calls is the start function.

	There is some difference in data between transactions tables and warehouse table because we need set shipped to be pressed and 
	only then paid is not entered in the warehouse. This means that Sales by transaction and sales by bcn are different.

	Meaning of vended filed in the warehouse table: vended = 0 - listed, vended = 2 - on hold, vended =1 - shipped

	*/

	private $table_transactions_summary='';
	private $table_groupby_status='';
    private $table_refunds_summary='';

	private $total_revenue=0.0;
	private $total_fee=0.0;
	private $total_actual_shipping=0.0;
	private $total_netprofit=0.0;
	private $total_cost=0.0;
	private $detailed_array;
	private $channel_array = array();//we need that for GroupByStatus function as parameter

	function Myreports()
	{
		parent::Controller();

		$this->load->model('Myreports_model');
		$this->load->model('Auth_model');
        

		$this->Auth_model->VerifyAdmin();
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('action',$this->session->flashdata('action'));
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Reports');
		$this->mysmarty->assign('show_all', $this->show_all);
        $this->mysmarty->assign('skutree', true);

		$this->mysmarty->assign('newlayout', TRUE);
		$this->mysmarty->assign('jslog', TRUE);

		//echo $this->show_all;
		$this->detailed_array = array();
	
        

	}
	function index()
	{

		//Keep empty for now. May be general start screen in future

		/*

		Calendar date post to timestamp:
		'.$dateto.' = explode('/', '.$datefrom.'calender);
		'.$dateto.' = mktime(0, 0, 0, '.$dateto.'[0], '.$dateto.'[1], '.$dateto.'[2])

		Loading calendar = $this->mysmarty->assign('cal', TRUE);
		Calendar current date =  date('m/j/Y');
		see views/myebay/myebay_orders.html - line 27 - 32 for use. Url for viewing - http://www.vic.la-tronics.com/Myebay/GetOrders

			Ebay Transactions Table - ebay_transactions
				Key = et_id
				user friendly date - datetime
				timestamp to use - mkdt
					$this->db->where('mkdt <= ', '.$datefrom.');
					$this->db->where('mkdt >= ', '.$dateto.');
				income - paid
				fee - fee
				actual shipping - asc

				Refunded for whole transaction, not for the one bcn:
					$this->db->where("(customcode = 1 OR refunded = 1)",null, false);
					$this->db->where('mkdt <= ', '.$datefrom.');
					$this->db->where('mkdt >= ', '.$dateto.');
					$this->db->where('sellingstatus != ', 'PartiallyPaid');

				Partially Refunded:
					$this->db->where("(customcode = 1 OR refunded = 1)",null, false);
					$this->db->where('mkdt <= ', '.$datefrom.');
					$this->db->where('mkdt >= ', '.$dateto.');
					$this->db->where('sellingstatus', 'PartiallyPaid');

				Pending Payment:
					$this->db->where('pendingpay', 1);
					$this->db->where('mkdt <= ', '.$datefrom.');
					$this->db->where('mkdt >= ', '.$dateto.');

				Not Paid:
					$this->db->where('paidtime', '');
					$this->db->where('notpaid', 0);
					$this->db->where('refunded', 0);
					$this->db->where('mkdt <= ', '.$datefrom.');
					$this->db->where('mkdt >= ', '.$dateto.');

				Not Shipped:
					$this->db->where('paidtime !=', '');
					$this->db->where('mark', 0);
					$this->db->where('mkdt <= ', '.$datefrom.');
					$this->db->where('mkdt >= ', '.$dateto.');
					$this->db->where('notpaid', 0);
					$this->db->where("refunded", 0);
					$this->db->where('pendingpay', 0);

				Paid:
					$this->db->where('paid !=', '');
					$this->db->where('paid !=', '0.0');
					$this->db->where('mark !=', 0);
					$this->db->where('mkdt <= ', '.$datefrom.');
					$this->db->where('mkdt >= ', '.$dateto.');
					$this->db->where('notpaid', 0);
					$this->db->where('refunded', 0);

			LATR Website Orders table - orders

				key - oid
				userfriendly time - complete_time
				timestamp to use - submittime
				income - endprice
				actual shipping - endprice_delivery

				notcomplete:
					
					$this->db->where('mark', 0);
					$this->db->where('submittime <= ', '.$datefrom.'); 
					$this->db->where('submittime >= ', '.$dateto.');
					'endprice_delivery'!= '' 
					'complete' == 0
				
					

				complete:
					$this->db->where('complete', 1);
					$this->db->where('mark !=', 0);
					$this->db->where('submittime <= ', '.$datefrom.');
					$this->db->where('submittime >= ', '.$dateto.');

				not shipped:

					$this->db->where('complete', 1);
					$this->db->where('mark', 0);
					$this->db->where('submittime <= ', '.$datefrom.');
					$this->db->where('submittime >= ', '.$dateto.');

			Warehouse orders Table - warehouse_orders
				Key = woid
				user friendly date - time
				timestamp to use - timemk
					$this->db->where('timemk <= ', '.$datefrom.');
					$this->db->where('timemk >= ', '.$dateto.');
				income - wholeprice
				subchannel
					Warehouse = 0
					365 Website = 1
					365 Hawthorne = 7
					365 Venice = 8

				Refunded: - We will talk later about these
				Partially Refunded: - We will talk later about these
				Pending Payment: - We will talk later about these
				Not Paid: - We will talk later about these
				Not Shipped:	 - We will talk later about these
				Paid: - We will talk later about these
			*/

		$this->mysmarty->view('myreports/myreports_main.html');
	}

	function FiveWeeksBefore()
	{
		 //if ($_POST) $this->mysmarty->assign('show_all', $this->input->post('show_all', true)); //Turns off default period
		//echo date('jS F Y H:i.s', strtotime('-5 week'));
		// echo date("d m Y",strtotime('monday -5 week'));


		//$this->show_all=0;
		unset($this->detailed_array);
		$this->detailed_array = array();

		if($_POST['submitPicker'])
		{
			  if (isset($_POST['oto']) && isset($_POST['ofrom']))
			  {
					$datefrom = explode('/', $this->input->post('ofrom', TRUE));   
					$datefrom = mktime(0, 0, 0, $datefrom[0], $datefrom[1], $datefrom[2]);
					$dateto = explode('/', $this->input->post('oto', TRUE));   
					$dateto = mktime(23, 59, 59, $dateto[0], $dateto[1], $dateto[2]);
		   
			   }

			  $this->mysmarty->assign('ofrom', date('m/j/Y', $datefrom));
			  $this->mysmarty->assign('oto', date('m/j/Y', $dateto));
		}
		//If period filter is applied we create monthly weekly or quarterly reports.
		else
		{
			if($_POST['period']=='Monthly')
			{
				//$datefrom = strtotime('monday -5 week');
				//$dateto =  $datefrom + (60*60*24*6)+86399;

				//$next_period_step = (60*60*24*7);//7 days ahead
				$datefrom = strtotime('1 January '.$_POST[year]);
				$dateto =  strtotime('last day of January '.$_POST[year]);
				$dateto =  $dateto + 86399;
				//echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';

			}
			elseif($_POST['period']=='Weekly')
			{
				$datefrom = strtotime('first day of '.$_POST[month].' '. $_POST[year]);
				$dateto =  $datefrom + (60*60*24*6)+86399;

			   //echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';

				$this->mysmarty->assign('ofrom', $datefrom, TRUE);
				$this->mysmarty->assign('oto', $dateto);

			}
			elseif($_POST['period']=='Yearly')//Not working properly, we do not need it yet.
			{
			   //$datefrom = strtotime('first day of -5 year');
			   //$dateto =  strtotime('last day of -5 year');

			   //echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';

			   //$this->mysmarty->assign('ofrom', $datefrom, TRUE);
			   //$this->mysmarty->assign('oto', $dateto);

			}
			elseif($_POST['period']=='Quarterly')
			{
			   $datefrom = strtotime('1 January '.$_POST[year]);
			   $dateto =  strtotime('last day of March '.$_POST[year]);

			   //$datefrom = $datefrom - 172799;
			   $dateto =  $dateto + 86399;
				//echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';

			   //$this->mysmarty->assign('ofrom', $datefrom, TRUE);
			   //$this->mysmarty->assign('oto', $dateto);

			}
			else
			{
				////Default is weekly
				//$datefrom = strtotime('monday -5 week');
				//$dateto =  $datefrom + (60*60*24*6);

				//$next_period_step = (60*60*24*7);

			   $datefrom = strtotime('1 January '.date("Y"));
			   $dateto =  strtotime('last day of March '.date("Y"));
			}

			// Just for initialisation of the calendar when the page is opened
			//$datefrom=mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
			//$dateto=mktime(23, 59, 59, date('m'), date('d'), date('Y'));

			$this->mysmarty->assign('ofrom', date('m/j/Y', $datefrom));
			$this->mysmarty->assign('oto', date('m/j/Y'));
			//echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';

		}//end of -> If period filter is applied.


		
		$start_date = $datefrom;//we need that for status function
 
		//$this->mysmarty->assign('ofrom', date('m/j/Y', $datefrom));
		//$this->mysmarty->assign('oto', date('m/j/Y'));

		// echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
		
		if($_POST['submitPicker'])
		{
			$msg_period='From '.date('m/j/Y',$datefrom).' to '.date('m/j/Y',$dateto);
		}
		else
		{
			$msg_period=$_POST['period'];
		}

		// We add 1 hour to the filter in order to have equal date frame with eBay. The reason for that is not clear!
        //$datefrom=$datefrom+(60*60);
        //$dateto=$dateto+(60*60);

		 //echo '<p>'.date('m/j/Y   H:i:s');
		 //echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
		 //echo '<p style="color:blue">From '.$datefrom.' to '.$dateto;


		//echo '<p>'.mk2date(1483254987);
		$this->total_revenue=0.0;
		$this->total_fee=0.0;
		$this->total_actual_shipping=0.0;
		$this->total_netprofit=0.0;
		$this->total_cost=0.0;



		$this->table_transactions_summary.= '<p><table  class="transpode" border="1" style="color:blue"; font-size:"16px";>
										 <tr>
											<th>Period - '.$msg_period.'</th>
										   <th>Revenue <br><font size="1" color="red">(when Refunded is checked it shows the refunded amount)</font></th>
											<!-- <th>Fee</th> -->
											<th>Actual Shipping</th>
											<th>Net Profit<br><font size="1" color="red">(Sales + SSC) - (Costs + Shipping + PayPal Fee + Extra Costs + Return Shipping + Other Return Expenses)</font></th>
											<th>Cost and Selling Fee <br><font size="1" color="red">(when Refunded is checked it shows only the cost)</font></th>
										 </tr>';

		// How many peiods we will show. Now 5 rows.
		for($counter = 0;$counter<12;$counter++)
		{
			   
			   //If dates are selected from the calendar. We create just one row in summary table.
			   if($_POST['submitPicker'])
			   {
					$this->CreateRow($datefrom, $dateto);
					break;
			   }
			   //If period filter is applied we create monthly weekly or quarterly reports.
			   if($_POST['period']=='Weekly')
			   {
					//$datefrom = strtotime('next month', $datefrom);
					//$dateto = strtotime('last day of next month', $dateto);
					if($counter>0 and $counter<5)
					{
						$datefrom = $datefrom  + (60*60*24*7);
						
						if($counter==4)
						{
							$days = cal_days_in_month(CAL_GREGORIAN, date('m',$datefrom), date('Y',$datefrom)); //How many days in month ? 
							$dateto =  mktime(0, 0, 0, (int)date('m',$datefrom), $days, date('Y',$datefrom))+86399;

						   //echo '<p style="color:blue">From '.date('m/j/Y  H:i:s',$datefrom).' to '.date('m/j/Y  H:i:s',$dateto).'&#8595;';
						}
						else
						{
							$dateto = $dateto + (60*60*24*6)+86400;
						}
					}
					elseif($counter>4)
					{
						 break;
					}
			   }
			   elseif($_POST['period']=='Monthly')
			   {
					if($counter!=0)
					{
						$datefrom = strtotime('+1 day', $dateto-86399);
						$dateto = strtotime('+1 month', $datefrom);
						$dateto = strtotime('-1 day', $dateto);

						$dateto = $dateto+86399;

						//echo '<p style="color:blue">From '.date('m/j/Y  H:i:s',$datefrom).' to '.date('m/j/Y  H:i:s',$dateto).'&#8595;';
					}
			   }
			   elseif($_POST['period']=='Yearly')//We do not need it yet. Deaktivated
			   {
					$datefrom = strtotime('first day of next year', $datefrom);
					$dateto = strtotime('last day of next year', $dateto);
			   }
			   elseif($_POST['period']=='Quarterly')
			   {
					if($counter>3) continue;//We dont need more than 4 periods quarterly. We jump out of loop we have just 5 itterations.
					if($counter!=0)
					{
						$datefrom = $dateto+1;
						$dateto = strtotime('+3 month', $datefrom);
						$dateto = strtotime('-1 day', $dateto+86399);
						//echo '<p style="color:blue">From '.$datefrom.' to '.$dateto.'&#8595;';
					}
			   }
			   else
			   {
					//Quarterly is default
					if($counter!=0)
					{
						$datefrom = $dateto+1;
						$dateto = strtotime('+3 month', $datefrom);
						$dateto = strtotime('-1 day', $dateto+86399);
					}
			   }
			   //echo '<p style="color:blue">From '.date('m/j/Y  H:i:s',$datefrom).' to '.date('m/j/Y  H:i:s',$dateto).'&#8595;';
			   
			   if(isset($_POST['period']))
			   {
					$this->CreateRow($datefrom, $dateto);
			   }
			  // Extract data from log
			  //if(isset($_POST['ebay_partially_refunded']) OR isset($_POST['ebay_refunded'])) $this->FindRefundedFromLog($datefrom, $dateto);
			  //This is for example just -> if(isset($_POST['ebay_partially_refunded']) OR isset($_POST['ebay_refunded'])) $this->FindRefundedFromLog();
		}//for

		$this->table_transactions_summary.= '<tr><td align="right" style="font-weight:bold">Total</td>
											<td align="right" style="font-weight:bold">'.money_format('%(#10n', $this->total_revenue).'</td>
										   <!-- <td align="right" style="font-weight:bold">'.money_format('%(#10n', $this->total_fee).'</td> -->
											<td align="right" style="font-weight:bold">'.money_format('%(#10n', $this->total_actual_shipping).'</td>
											<td align="right" style="font-weight:bold">'.money_format('%(#10n', $this->total_netprofit).'</td>
											<td align="right" style="font-weight:bold">'.money_format('%(#10n', $this->total_cost).'</td></tr></table>';

		$this->mysmarty->assign('cal', true);
		$this->mysmarty->assign('show_all', $this->input->post('show_all', true));
		$this->mysmarty->assign('ebay_all', $this->input->post('ebay_all', true));
		$this->mysmarty->assign('ebay_refunded', $this->input->post('ebay_refunded', true));
		$this->mysmarty->assign('ebay_partially_refunded', $this->input->post('ebay_partially_refunded', true));
		$this->mysmarty->assign('ebay_pending_payment', $this->input->post('ebay_pending_payment', true));
		$this->mysmarty->assign('ebay_notcomplete', $this->input->post('ebay_notcomplete', true));
		$this->mysmarty->assign('ebay_complete', $this->input->post('ebay_complete', true));
		$this->mysmarty->assign('ebay_notshipped', $this->input->post('ebay_notshipped', true));
		$this->mysmarty->assign('latr_all', $this->input->post('latr_all', true));
		$this->mysmarty->assign('latr_notcomplete', $this->input->post('latr_notcomplete', true));
		$this->mysmarty->assign('latr_complete', $this->input->post('latr_complete', true));
		$this->mysmarty->assign('latr_notshipped', $this->input->post('latr_notshipped', true));
		$this->mysmarty->assign('warehouse_all', $this->input->post('warehouse_all', true));
		$this->mysmarty->assign('warehouse', $this->input->post('warehouse', true));
		$this->mysmarty->assign('warehouse_website_365', $this->input->post('warehouse_website_365', true));
		$this->mysmarty->assign('warehouse_hawthorne_365', $this->input->post('warehouse_hawthorne_365', true));
		$this->mysmarty->assign('warehouse_venice_365', $this->input->post('warehouse_venice_365', true));

		$this->mysmarty->assign('det_table_message', 'Detailed Table  <font size=\'1\' color=\'blue\'>column Net Profit is equal to Sales - (Costs + Shipping + PayPal Fee + Extra Costs). Please, note that detailed table is product (bcn) based not transaction based and one transaction could appear on several row depending on the number of products it has. SSC is NOT included here! Return Shipping, Other Return Expenses are NOT included as well, they are subtracted from the Net Profit in the summary table above only. </font>', true);
		$this->mysmarty->assign('curr_year', date('Y'));
		$this->mysmarty->assign('table_transactions_summary',$this->table_transactions_summary);
		

		//echo $this->table_transactions_summary;
		$fieldset = array(
		'headers' => "'id', 'channel','sold_id','bcn', 'title', 'status', 'revenue', 'cost','net profit'",
		'width' => "80, 60, 60, 120, 220, 90, 80, 120, 80",
		'startcols' => 9,
		'startrows' => count($this->detailed_array),
		'colmap' => '{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}');
	

		$this->mysmarty->assign('headers', $fieldset['headers']);
		$this->mysmarty->assign('rowheaders', $fieldset['rowheaders']);
		$this->mysmarty->assign('width', $fieldset['width']);
		$this->mysmarty->assign('startcols', $fieldset['startcols']);
		$this->mysmarty->assign('startrows', $fieldset['startrows']);
		$this->mysmarty->assign('colmap', $fieldset['colmap']);

		//printcool($this->detailed_array);

        //SelectLastStatusFromLog searches the statuses from log for the period and if there is data I put in detailed_array!
		//$this->SelectLastStatusFromLog($start_date, $dateto);
		$this->mysmarty->assign('loaddata', json_encode($this->detailed_array));
		$this->mysmarty->assign('hot', TRUE);

        

		$this->channel_array=(array_unique($this->channel_array));//$this->channel_array has some dublicate channels so we make them unique
		//echo '<p> After unique '.count($this->channel_array);
		$this->GroupByStatus($this->channel_array);//Function shows number of product in the detailed table grouped by their statuses

        

		$this->mysmarty->assign('table_groupby_status', $this->table_groupby_status);

        if ((isset($_POST['ebay_refunded']) OR isset($_POST['ebay_partially_refunded'])) AND !isset($_POST['ebay_pending_payment'])  
            AND !isset($_POST['ebay_notcomplete'])  AND !isset($_POST['ebay_complete'])  AND !isset($_POST['ebay_notshipped']) 
            AND !isset($_POST['latr_all'])  AND !isset($_POST['latr_notcomplete'])  AND !isset($_POST['latr_complete'])  
            AND !isset($_POST['latr_notshipped'])  AND !isset($_POST['warehouse_all'])  AND !isset($_POST['warehouse'])  
            AND !isset($_POST['warehouse_website_365'])  AND !isset($_POST['warehouse_hawthorne_365']) AND !isset($_POST['warehouse_venice_365']))
        {
		    $this->RefundsSummaryTable($datefrom, $dateto);
        }

		//echo $this->table_groupby_status;

		$this->mysmarty->view('myreports/myreports_report2.html');
	}

	function nuemp($var)
	{
		if (is_null($var)) return '';
		else return $var;
	}

	// For each row of the table
	function CreateRow($datefrom, $dateto)
	{
		//if (isset($_POST['oto']) && isset($_POST['ofrom']))
		//{
		//    $datefrom = explode('/', $this->input->post('ofrom', TRUE));
		//    $datefrom = mktime(0, 0, 0, $datefrom[0], $datefrom[1], $datefrom[2]);
		//    $dateto = explode('/', $this->input->post('oto', TRUE));
		//    $dateto = mktime(0, 0, 0, $dateto[0], $dateto[1], $dateto[2]);

		//    $this->mysmarty->assign('ofrom', $this->input->post('ofrom', TRUE));
		//    $this->mysmarty->assign('oto', $this->input->post('oto', TRUE));
		//}
		//else
		//{
		//    $datefrom=mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
		//    $dateto=mktime(0, 0, 0, date('m'), date('d'), date('Y'));

		//    $this->mysmarty->assign('ofrom', date('m/j/Y', $datefrom));
		//    $this->mysmarty->assign('oto', date('m/j/Y'));

		//}

		//echo $this->input->post('ofrom', TRUE);

		setlocale(LC_MONETARY, 'en_US');
		
	//////////////////// Ebay /////////////////////////////////////////////////////////

		$query=$this->SelectDataFromEbayTransactions($datefrom, $dateto);

		$revenue = 0.0;
		$fee =0.0;
		$actual_shipping = 0.0;
		$netprofit=0.0;

		$cost = 0.0; //from warehouse
		$eid_array=array();

		foreach ($query->result_array() as $row)
		{
		   if((isset($_POST['ebay_all']) OR isset($_POST['show_all'])) AND $row['Channel']='eBay' and ($row['refunded']==0 or $row['sellingstatus']=="PartiallyPaid"))
		   {
				 $revenue = ($revenue + (float)$row['Revenue']+(float)$row['ssc']);
				 $fee = $fee + (float)$row['Fee'];
				 $actual_shipping =$actual_shipping + (float)$row['Actual Shipping Price'];
					
				 continue;
		   }

			//Pending Payment:
		   if(isset($_POST['ebay_pending_payment']))
		   {
				//Pending Payment:
				//         $this->db->where('pendingpay', 1);
				//         $this->db->where('mkdt <= ', '.$datefrom.');
				//         $this->db->where('mkdt >= ', '.$dateto.');

				if($row['pendingpay'] == 1 AND $row['Channel']='eBay')
				{
					 $revenue = $revenue + (float)$row['Revenue'];
					 $fee = $fee + (float)$row['Fee'];
					 $actual_shipping =$actual_shipping + (float)$row['Actual Shipping Price'];

					 $eid_array[]=(int)$row['et_id'];

					
				}
		   }

		   //Not Paid:
		   if(isset($_POST['ebay_notcomplete']))
		   {
				//Not Paid:
				   //         $this->db->where('paidtime', '');
				   //         $this->db->where('notpaid', 0);
				   //         $this->db->where('refunded', 0);
				   //         $this->db->where('mkdt <= ', '.$datefrom.');
				   //         $this->db->where('mkdt >= ', '.$dateto.');

				if($row['paidtime'] == '' AND $row['notpaid'] == 0 /* AND $row['refunded'] == 0 */ AND $row['Channel']='eBay')
				{
				   $revenue = $revenue + (float)$row['Revenue'];
				   $fee = $fee + (float)$row['Fee'];

				   $eid_array[]=(int)$row['et_id'];
				
				}
		   }

		   //Paid:
		   if(isset($_POST['ebay_complete']))
		   {
				//Paid:
				   //         $this->db->where('paid !=', '');
				   //         $this->db->where('paid !=', '0.0');
				   //         $this->db->where('mark !=', 0);
				   //         $this->db->where('mkdt <= ', '.$datefrom.');
				   //         $this->db->where('mkdt >= ', '.$dateto.');
				   //         $this->db->where('notpaid', 0);
				   //         $this->db->where('refunded', 0);

				if($row['paid'] != '' AND $row['paid'] != 0 AND $row['mark'] != 0 AND $row['notpaid'] == 0  /* AND $row['refunded'] == 0 */   AND $row['Channel']='eBay')
				{
					$revenue = $revenue + (float)$row['Revenue'];
					$fee = $fee + (float)$row['Fee'];
					$actual_shipping = $actual_shipping + (float)$row['Actual Shipping Price'];

					$eid_array[]=(int)$row['et_id'];
				}
		   }

		   //Not Shipped:
		   if(isset($_POST['ebay_notshipped']))
		   {
				//Not Shipped:
				//    $this->db->where('paidtime !=', '');
				//    $this->db->where('mark', 0);
				//    $this->db->where('mkdt <= ', '.$datefrom.');
				//    $this->db->where('mkdt >= ', '.$dateto.');
				//    $this->db->where('notpaid', 0);
				//    $this->db->where("refunded", 0);
				//    $this->db->where('pendingpay', 0);

				if($row['paidtime'] != '' AND $row['notpaid'] == 0 AND $row['mark'] == 0   /* AND $row['refunded'] == 0 */     AND $row['pendingpay'] == 0  AND $row['Channel']='eBay')
				{
				   $revenue = $revenue + (float)$row['Revenue'];
				   $fee = $fee + (float)$row['Fee'];
				   $actual_shipping = $actual_shipping + (float)$row['Actual Shipping Price'];

				   $eid_array[]=(int)$row['et_id'];
				}
		   }

		}//foreach

	//////////////////// LATR Website /////////////////////////////////////////////////////////

		$query=$this->SelectDataFromLATRWebsiteOrders($datefrom, $dateto);

		$oid_array=array();

		foreach ($query->result_array() as $row)
		{

		   if((isset($_POST['latr_all']) OR isset($_POST['show_all'])  /* or $this->show_all==1*/)  AND $row['Channel']='LATRWebsite')
		   {
				 $revenue = $revenue + (float)$row['Revenue'];
				 $fee = $fee + (float)$row['Fee'];
				 $actual_shipping = $actual_shipping + (float)$row['Actual Shipping Price'];

				 $oid_array[]=(int)$row['oid'];

				 //select "LATRWebsite" as Channel, endprice as revenue, endprice_delivery as "Actual Shipping Price", complete, mark, oid from orders
				 //$this->detailed_array['Channel'][] = $row['Channel'];
				 //$this->detailed_array['revenue'][]  = $row['Revenue'];
				 //$this->detailed_array['Fee'][]  ='';
				 //$this->detailed_array['Actual Shipping Price'][] = $row['Actual Shipping Price'];
				 // $this->detailed_array['Order Date'][] = date('m/j/Y',$row['Order Date']);
				 //$this->detailed_array['customcode'][]  = '';
				 //$this->detailed_array['refunded'][]  = '';
				 //$this->detailed_array['sellingstatus'][]  = '';
				 //$this->detailed_array['pendingpay'][]  = '';
				 //$this->detailed_array['paidtime'][]  = '';
				 //$this->detailed_array['notpaid'][]  = '';
				 //$this->detailed_array['mark'][]  = $row['mark'];
				 //$this->detailed_array['paid'][]  = $row['complete'];
				 //$this->detailed_array['id'][]  = $row['oid'];

			 //    //$this->FillDetailedArray($row);

				 continue;
		   }
			//Not Complete
		   if(isset($_POST['latr_notcomplete']))
		   {
			   //endprice_delivery <>"" and
			   //complete=0 and
			   //mark=0

				if($row['endprice_delivery'] != '' AND $row['complete'] == 0 AND $row['mark'] == 0   AND $row['Channel']='LATRWebsite')
				{
				   $revenue = $revenue + (float)$row['Revenue'];
				   $fee = $fee + (float)$row['Fee'];
				   $actual_shipping = $actual_shipping + (float)$row['Actual Shipping Price'];

				   $oid_array[]=(int)$row['oid'];

				}
		   }

		   //Complete
		   if(isset($_POST['latr_complete']))
		   {
				//complete=1 and
				//mark<>0

				if($row['complete'] == 1 AND $row['mark'] != 0   AND $row['Channel']='LATRWebsite')
				{
				   $revenue = $revenue + (float)$row['Revenue'];
				   $fee = $fee + (float)$row['Fee'];
				   $actual_shipping = $actual_shipping + (float)$row['Actual Shipping Price'];

				   $oid_array[]=(int)$row['oid'];

				}
		   }

		   //Not shipped
		   if(isset($_POST['latr_notshipped']))
		   {
				//complete=1 and
				//mark<>0

				if($row['complete'] == 1 AND $row['mark'] == 0  AND $row['Channel']='LATRWebsite')
				{
				   $revenue = $revenue + (float)$row['Revenue'];
				   $fee = $fee + (float)$row['Fee'];
				   $actual_shipping = $actual_shipping + (float)$row['Actual Shipping Price'];

				   $oid_array[]=(int)$row['oid'];

				}
		   }

		}//foreach

		
	//////////////////// Warehouse ///////////////////////////////////////////////////////////
		$query=$this->SelectDataFromWarehouseOrders($datefrom, $dateto);

		$woid_array = array();

		foreach ($query->result_array() as $row)
		{
		   //All warehouse
		   if((isset($_POST['warehouse_all']) OR isset($_POST['show_all'])  /* or $this->show_all==1*/) AND $row['Channel']='Warehouse')
		   {
				 $revenue = $revenue + (float)$row['Revenue'];
				 $fee = $fee + (float)$row['Fee'];
				 $actual_shipping = $actual_shipping + (float)$row['Actual Shipping Price'];

				 continue;
		   }
			// Warehouse = 0
		   if(isset($_POST['warehouse']))
		   {

				if($row['subchannel'] == 0 AND $row['Channel']='Warehouse')
				{
				   $revenue = $revenue + (float)$row['Revenue'];
				   $fee = $fee + (float)$row['Fee'];
				   $actual_shipping =$actual_shipping + (float)$row['Actual Shipping Price'];

				   $woid_array []=(int)$row['woid'];

				}
		   }

			// 365 Website = 1
		   if(isset($_POST['warehouse_website_365']))
		   {

				if($row['subchannel'] == 1 AND $row['Channel']='Warehouse')
				{
				   $revenue = $revenue + (float)$row['Revenue'];
				   $fee = $fee + (float)$row['Fee'];
				   $actual_shipping = $actual_shipping + (float)$row['Actual Shipping Price'];

				   $woid_array []=(int)$row['woid'];

				}
		   }

		   // 365 Hawthorne = 7
		   if(isset($_POST['warehouse_hawthorne_365']))
		   {

				if($row['subchannel'] == 7 AND $row['Channel']='Warehouse')
				{
				   $revenue = $revenue + (float)$row['Revenue'];
				   $fee = $fee + (float)$row['Fee'];
				   $actual_shipping = $actual_shipping + (float)$row['Actual Shipping Price'];

				   $woid_array[]=(int)$row['woid'];

				}
		   }

		   // 365 Venice = 8
		   if(isset($_POST['warehouse_venice_365']))
		   {

				if($row['subchannel'] == 8 AND $row['Channel']='Warehouse')
				{
				   $revenue = $revenue + (float)$row['Revenue'];
				   $fee = $fee + (float)$row['Fee'];
				   $actual_shipping = $actual_shipping + (float)$row['Actual Shipping Price'];

				   $woid_array[]=(int)$row['woid'];

				}
		   }
		}//foreach

		//If $eid_array has values it means that filters are selected and $eid_array is filled only with transactions
		//which match selected filters like 'not paid' or 'paid' etc. for eBay then we show the results.

		if(isset($_POST['ebay_all']) OR isset($_POST['show_all']) /* or $this->show_all==1*/
                    //OR isset($_POST['ebay_refunded'])
                    //OR isset($_POST['ebay_partially_refunded'])
					OR isset($_POST['ebay_pending_payment'])
					OR isset($_POST['ebay_notcomplete'])
					OR isset($_POST['ebay_complete'])
					OR isset($_POST['ebay_notshipped']))
		{
			$netprofit = $netprofit+(float)$this->SumNetProfitFromEbayBySoldId($datefrom, $dateto, $eid_array);
            //echo "<p>NET PROFIT ".$netprofit;
			$cost = $cost+(float)$this->SumCostAndFeeFromEbay($datefrom, $dateto, $eid_array);

			$array_temp = array();
			$array_temp = $this->SelectDetailedDataFromWarehouseForEbay($datefrom, $dateto, $eid_array);

			//printcool($array_temp);

			$this->AppendArray($array_temp,$datefrom, $dateto);
		
           

			$this->channel_array[]=1;
		   //echo '<p>We are here 1';
           
		}

		
		if(isset($_POST['latr_notcomplete']) OR isset($_POST['latr_complete']) OR isset($_POST['latr_notshipped']) OR isset($_POST['latr_all']) OR isset($_POST['show_all'])  /* or $this->show_all==1*/)
		{
		  $netprofit = (float)$this->SumNetProfitFromLATRWebsite($datefrom, $dateto, $oid_array);

         //echo '<p>'.$this->db->last_query();
         // echo '<p>NET PROFIT '.$netprofit;

		  $cost = $cost+(float)$this->SumCostAndFeeFromLATRWebsite($datefrom, $dateto, $oid_array);

		  $array_temp = array();
		  $array_temp = $this->SelectDetailedDataFromWarehouseForLATRWebsite($datefrom, $dateto, $eid_array);
		  $this->AppendArray($array_temp,$datefrom, $dateto);

			
		  $this->channel_array[]=2;

		  //echo '<p>Channel array '.count($this->channel_array);
	 
		}

		if(isset($_POST['warehouse_venice_365']) OR isset($_POST['warehouse_hawthorne_365']) OR isset($_POST['warehouse_website_365']) OR isset($_POST['warehouse_all']) OR isset($_POST['show_all']) OR isset($_POST['warehouse']))
		{
		   $netprofit = $netprofit+(float)$this->SumNetProfitFromWarehous($datefrom, $dateto, $woid_array);
		   $cost = $cost+(float)$this->SumCostFromWarehouse($datefrom, $dateto, $woid_array);

		   $array_temp = array();
		   $array_temp = $this->SelectDetailedDataFromWarehouseForWarehouse($datefrom, $dateto, $woid_array);
		   $this->AppendArray($array_temp,$datefrom, $dateto);
	 
		   $this->channel_array[]=4;//We dont need subchannel because in warehouse_orders id of transaction are unique and we give as an argument array with ids

		}

	   
		if(isset($_POST['ebay_refunded']) and !isset($_POST['ebay_all']) and !isset($_POST['show_all'])) 
		{
			$revenue = $revenue + $this->SumEbayFullRefundAmount($datefrom, $dateto, $channel=1);
		   //$fee = $fee + $this->SumEbayRefundedFees($datefrom, $dateto, $channel=1);
			$cost = $cost + $this->SumEbayFullRefundCost($datefrom, $dateto, $channel=1);
            $actual_shipping = $actual_shipping + $this->SumEbayFullRefundedActualShipping($datefrom, $dateto);
			$cost = $cost + $this->SumEbayPartillyRefundCost($datefrom, $dateto);
			$netprofit = $netprofit + $this->SumEbayPartialRefundNetprofit($datefrom, $dateto, $channel=1);

            $sold_id_array = array();
			$sold_id_array = $this->EbayFullRefundTransID($datefrom, $dateto);

           // printcool($sold_id_array);

			$array_temp = array();
			$array_temp = $this->SelectDetailedDataFromWarehouseForEbay($datefrom, $dateto, $sold_id_array);


			$this->AppendArray($array_temp,$datefrom, $dateto);
		
			$this->channel_array[]=1;

		}

		if(isset($_POST['ebay_partially_refunded']) and !isset($_POST['ebay_all']) and !isset($_POST['show_all'])) 
		{
			
            $revenue = $revenue + $this-> SumEbayPartialRefundAmount($datefrom, $dateto, $channel=1);
		    $actual_shipping = $actual_shipping + $this->SumEbayPartiallyRefundedActualShipping($datefrom, $dateto);
			$cost = $cost + $this->SumEbayPartillyRefundCost($datefrom, $dateto);
			$netprofit = $this->SumEbayPartialRefundNetprofit($datefrom, $dateto, $channel=1);

			//I have to take detailed data from transaction_details table

			$sold_id_array = array();

			$sold_id_array = $this->EbayPartialRefundTransID($datefrom, $dateto, $channel=1);

            //printcool($sold_id_array);

			$array_temp = array();
			$array_temp = $this->SelectDetailedDataFromWarehouseForEbay($datefrom, $dateto, $sold_id_array);


			$this->AppendArray($array_temp,$datefrom, $dateto);
		
			$this->channel_array[]=1;
		}
	   

		$this->total_revenue=$this->total_revenue+$revenue;
		$this->total_fee=$this->total_fee+$fee;
		$this->total_actual_shipping=$this->total_actual_shipping+$actual_shipping;
		$this->total_netprofit=$this->total_netprofit+$netprofit;
		$this->total_cost=$this->total_cost+$cost;

		

		$this->table_transactions_summary.= '<tr>
											<td style="font-weight:bold">From '.date('m/j/Y',$datefrom).' to '.date('m/j/Y',$dateto).'</td>';
		$this->table_transactions_summary.= '<td align="right">'.money_format('%(#10n', $revenue).'</td>';
		//$this->table_transactions_summary.= '<td align="right">'.money_format('%(#10n', $fee).'</td>';
		$this->table_transactions_summary.= '<td align="right">'.money_format('%(#10n', $actual_shipping).'</td>';
		$this->table_transactions_summary.= '<td align="right">'.money_format('%(#10n', $netprofit).'</td>';
		$this->table_transactions_summary.= '<td align="right">'.money_format('%(#10n', $cost).'</td></tr>';

	 }

	function SelectDataFromEbayTransactions($datefrom, $dateto)
	{
		$query = $this->db->query('select "eBay" as Channel, paid as Revenue, fee as Fee, `asc` as "Actual Shipping Price", ssc,
				 customcode, refunded, sellingstatus, pendingpay, paidtime, notpaid, `mark`, paid, et_id, mkdt  as "Order Date",
                  return_id, ebayRefundAmount
					from ebay_transactions
				 where mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
	//	echo '<p>'.$this->db->last_query();

		 return $query;
	}

	function SelectDataFromLATRWebsiteOrders($datefrom, $dateto)
	{
		$query = $this->db->query('select "LATRWebsite" as Channel, endprice as Revenue, endprice_delivery as "Actual Shipping Price", 
									complete, mark, oid, submittime as "Order Date"  from orders
									where
									submittime >= '.$datefrom.' and
									submittime <= '.$dateto.' and complete<>-1');
		//complete<>-1 means do not select fraud orders
		//echo '<p>'.$this->db->last_query();

		return $query;
	}

	function  SelectDataFromWarehouseOrders($datefrom, $dateto)
	{
		 
		//subchannel is applied later in the loop!


        $query = $this->db->query('select "Warehouse" AS Channel, 
                                            warehouse_orders.subchannel, 
                                            shipped_actual AS "Actual Shipping Price",
                                            transaction_details.fee as Fee,    
                                            transaction_details.paid as Revenue, 
                                            transaction_details.sold_id as woid,
                                            timemk as "Order Date"
                                    from transaction_details inner join 
                                        warehouse on transaction_details.w_id=warehouse.wid left join 
                                        warehouse_orders on transaction_details.sold_id=warehouse_orders.woid
                        where
                        transaction_details.sold_id is not null AND 
                     transaction_details.channel = 4 AND timemk <= '.$dateto.' and timemk >= '.$datefrom);
					   
	   return $query;
	}

	//NET Profit for the whole transaction. The transactions have one or more products attached.
	function  SumNetProfitFromEbayBySoldId($datefrom, $dateto, $eid_array=null)
	{
		 //$data['netprofit'] = ((float)$data['paid'])-((float)$wid['cost']+(float)$wid['sellingfee']+
		//(float)$data['shipped_actual']);


        //echo '<br>EID ARR '.count($eid_array);
		if(count($eid_array)>0)
		{
			$this->db->select('sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit', FALSE)
									->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 1')
									->where_in('transaction_details.sold_id', $eid_array);
			$result = $this->db->get();
		    //echo '<p>'.$this->db->last_query();
			$row = $result->first_row();

			return (float)$row->Profit+$this->SumSSCProfit($datefrom, $dateto, $eid_array);
           
		}
		elseif(isset($_POST['ebay_all']) OR isset($_POST['show_all']) /* or $this->show_all==1*/)
		{
			$query = $this->db->query('select sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 1  AND transaction_details.sold_id IN 
			(select et_id from ebay_transactions  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and notpaid = 0 and ebayRefundAmount<paid)');


			$row = $query->first_row();
            //echo '<p>'.$this->db->last_query();
			return ((float)$row->Profit+$this->SumSSCProfit($datefrom, $dateto))-$this->SumRefundsAllExtraCosts($datefrom, $dateto);
        }
		else
		{
			return 0;
		}
			
	}
    //NET Profit for one product.
    function  SumNetProfitFromEbaybyWID($datefrom, $dateto, $wid=null, $sold_id=null)
	{
	
		if(isset($wid) and isset($sold_id))
		{
			$this->db->select('IF(cost > 0, transaction_details.paid-(cost+fee+shipped_actual+transaction_details.paypal_fee+transaction_details.extra_cost), 0) as Profit', FALSE)
									->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id = '.$sold_id.' AND transaction_details.channel = 1 and transaction_details.w_id = '.$wid);
                                    //->where_in('transaction_details.sold_id', $eid_array);
			$result = $this->db->get();
		   //echo '<p>ATTENTION '.$this->db->last_query();
			$row = $result->first_row();

            // Start check is it refunded transaction (not partially refunded). Is it is full refund Net profit is 0
            $this->db->select("refunded, sellingstatus")
                       ->from('ebay_transactions')
                         ->where('et_id = '.$sold_id);

            $result_ebaytrans = $this->db->get();            
            $row_ebaytrans = $result_ebaytrans->first_row();
            

            if($row_ebaytrans->refunded==1 and $row_ebaytrans->sellingstatus!="PartiallyPaid")
            {
                return 0;
            }//End check
            else
            {
                //echo '<br>Profit for '.$wid.' is '.$row->Profit;;
                return (float)$row->Profit;
            }
		}
		else
		{
			return 0;
		}
			
	}

function  SumNetProfitFromLATRWebsite($datefrom, $dateto, $eid_array=null)
	{

		//$data['netprofit'] = ((float)$data['paid'])-((float)$wid['cost']+(float)$wid['sellingfee']+
		//(float)$data['shipped_actual']);

		if(count($eid_array)>0)
		{
			$this->db->select('sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)) as Profit', FALSE)
											 ->from('transaction_details')
											 ->join('warehouse','transaction_details.w_id=warehouse.wid')
											 ->where('transaction_details.sold_id is not null AND transaction_details.channel = 2')
											 ->where_in('transaction_details.sold_id', $eid_array);



			$result = $this->db->get();
		   // echo '<p>'.$this->db->last_query();
			$row = $result->first_row();

            //echo '<p>'.$row->Profit;

			return (float)$row->Profit;
		}
		elseif(isset($_POST['latr_all']) OR isset($_POST['show_all'])  /* or $this->show_all==1*/)
		{
			$query = $this->db->query('select sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)) as Profit
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 2  AND transaction_details.sold_id IN 
			(select oid from orders  where submittime <= '.$dateto.' and submittime >= '.$datefrom.' and complete<>-1)');

			$result = $query->first_row();

			return (float)$result->Profit;
		}
		else
		{
			return 0;
		}
		//echo '<p>'.$row_ebay->ebay_transactions_revenue.' '.(float)$row_warehause->warehouse_expences;
	}

	function  SumNetProfitFromWarehous($datefrom, $dateto, $eid_array=null)
	{
		//$data['netprofit'] = ((float)$data['paid'])-((float)$wid['cost']+(float)$wid['sellingfee']+
		//(float)$data['shipped_actual']);

		if(count($eid_array)>0)
		{
			$this->db->select('sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)) as Profit', FALSE)
											 ->from('transaction_details')
											 ->join('warehouse','transaction_details.w_id=warehouse.wid')
											 ->where('transaction_details.sold_id is not null AND transaction_details.channel = 4')
											 ->where_in('transaction_details.sold_id', $eid_array);
			$result = $this->db->get();
		   //echo $this->db->last_query();
			$row = $result->first_row();

			return (float)$row->Profit;

		}
		elseif(isset($_POST['warehouse_all']) OR isset($_POST['show_all'])  /* or $this->show_all==1*/)
		{
			$query = $this->db->query('select sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)) as Profit
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 4  AND transaction_details.sold_id IN 
			(select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.')');

			$result = $query->first_row();

			return (float)$result->Profit;

		 }

		 // echo '<p>'.$this->db->last_query();
		 return 0;

	}

	/* TAKE PROFIT FROM TRANSACTIONS FOR WAREHOUSE ORDERS*/

	//function  SumNetProfitFromWarehous($datefrom, $dateto, $eid_array=null)
	//{

	//    //$data['netprofit'] = ((float)$data['paid'])-((float)$wid['cost']+(float)$wid['sellingfee']+
	//    //(float)$data['shipped_actual']);

	//    if(count($eid_array)>0)
	//    {
	//        $this->db->select('sum(cost)+sum(sellingfee)+sum(shipped_actual) as warehouse_expences')
	//                                         ->from('warehouse')
	//                                         ->where('sold_id is not null AND channel = 4 AND vended=2')
	//                                         ->where_in('sold_id', $eid_array);
	//        $query_warehouse_expences = $this->db->get();
	//       //echo $this->db->last_query();
	//        $row_warehause = $query_warehouse_expences->first_row();

	//       $this->db->select('sum(wholeprice)  as transactions_revenue')
	//                    ->from('warehouse_orders')
	//                     ->where_in('woid', $eid_array);
	//        $query_WO_transactions_profit_substracted_asc = $this->db->get();
	//        //echo $this->db->last_query();
	//        $row_WO = $query_WO_transactions_profit_substracted_asc->first_row();
	//    }
	//    else
	//    {
	//        $query_warehouse_expences = $this->db->query('select sum(cost)+sum(sellingfee)+sum(shipped_actual)
	//                                     as warehouse_expences from warehouse where
	//                                sold_id is not null AND channel = 4  AND vended=2 AND sold_id IN
	//        (select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.')');
	//        $row_warehause = $query_warehouse_expences->first_row();

	//       //echo '<p>'.$this->db->last_query();

	//        $query_WO_transactions_profit_substracted_asc = $this->db->query('select sum(wholeprice) as transactions_revenue  from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom);
	//        $row_WO = $query_WO_transactions_profit_substracted_asc->first_row();

	//        // echo '<p>'.$this->db->last_query();
	//     }
	//     //echo '<p>'.$row_WO->transactions_revenue.' '.(float)$row_warehause->warehouse_expences;
	//     return (float)$row_WO->transactions_revenue-(float)$row_warehause->warehouse_expences;

	//}

	//COSTS and FEE SELECTS
	function  SumCostAndFeeFromEbay($datefrom, $dateto, $eid_array=null)
	{
		if(count($eid_array)>0)
		{
			//sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)
            $this->db->select('(sum(cost)+sum(fee)) as CostAndFee', FALSE)
									->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 1')
									->where_in('transaction_details.sold_id', $eid_array);

			$query = $this->db->get();
			$row = $query->first_row();
			 //echo '<p>'.$this->db->last_query();
                       
			return (float)$row->CostAndFee;
		}
		elseif(isset($_POST['ebay_all']) OR isset($_POST['show_all']) /* or $this->show_all==1*/)
		{
			$query = $this->db->query('select (sum(cost)+sum(fee)) as CostAndFee
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 1  AND transaction_details.sold_id IN 
			(select et_id from ebay_transactions  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.')');

			$row = $query->first_row();
		    //echo $this->db->last_query();
                          
			return (float)$row->CostAndFee;
		}
		else
		{
			return 0;
		}
	}

	function  SumCostAndFeeFromLATRWebsite($datefrom, $dateto, $eid_array=null)
	{
		

		if(count($eid_array)>0)
		{
			$this->db->select('(sum(cost)+sum(fee)) as CostAndFee', FALSE)
											 ->from('transaction_details')
											 ->join('warehouse','transaction_details.w_id=warehouse.wid')
											 ->where('transaction_details.sold_id is not null AND transaction_details.channel = 2')
											 ->where_in('transaction_details.sold_id', $eid_array);
			$result = $this->db->get();
		   //echo $this->db->last_query();
			$row = $result->first_row();

			return (float)$row->CostAndFee;

		}
		elseif(isset($_POST['latr_all']) OR isset($_POST['show_all'])  /* or $this->show_all==1*/)
		{
			$query = $this->db->query('select (sum(cost)+sum(fee)) as CostAndFee
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 2  AND transaction_details.sold_id IN 
			(select oid from orders  where submittime <= '.$dateto.' and submittime >= '.$datefrom.' and complete<>-1)');

			$row = $query->first_row();

			return (float)$row->CostAndFee;
		}
		else
		{
			return 0;
		}
	}

	function  SumCostFromWarehouse($datefrom, $dateto, $eid_array=null)
	{
		

		if(count($eid_array)>0)
		{
			$this->db->select('sum(cost) as Cost', FALSE)
											 ->from('transaction_details')
											 ->join('warehouse','transaction_details.w_id=warehouse.wid')
											 ->where('transaction_details.sold_id is not null AND transaction_details.channel = 4')
											 ->where_in('transaction_details.sold_id', $eid_array);
			$result = $this->db->get();
		   //echo $this->db->last_query();
			$row = $result->first_row();

			return (float)$row->Cost;    
		}
		elseif(isset($_POST['warehouse_all']) OR isset($_POST['show_all'])  /* or $this->show_all==1*/)
		{
            $query = $this->db->query('select sum(cost) as Cost
                                          from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
                                    transaction_details.sold_id is not null AND transaction_details.channel = 4  AND transaction_details.sold_id IN 
            (select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.')');

            $row = $query->first_row();

            return (float)$row->Cost;
    	 }
		 //echo '<p>'.$this->db->last_query();
		 
		 return 0;

	}

	

	function  SelectDetailedDataFromWarehouseForEbay($datefrom, $dateto, $sold_id_array=null)
	{
       // echo '<p>Selecting Detailed Data from Ebay. Sold Id array e '+ count($sold_id_array);
		//printcool($sold_id_array);
        //echo '<p> COUNT = '.count($sold_id_array);
		if(count($sold_id_array)>0)
		{
			//echo '<p>1';

            $this->db->select('wid as wid, transaction_details.channel, transaction_details.sold_id,  warehouse.bcn, title, `status`, transaction_details.paid as revenue, cost, transaction_details.fee, returned_amount')
											->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 1')
									->where_in('transaction_details.sold_id', $sold_id_array)
                                            ->order_by('sold_id', 'ASC');
                                           
			$q =  $this->db->get();
		   //echo '<p>NUM ROWS='.$q->num_rows();
		//echo '<p>'.$this->db->last_query();
           //echo '<p>NUM ROWS '.$q->num_rows();     
		   if ($q->num_rows() >0 ) return $q->result_array();
		   else return array();
		}
		elseif(isset($_POST['ebay_all']) OR isset($_POST['show_all']) /* or $this->show_all==1*/)
		{
			
            $query = $this->db->query('select wid as wid, transaction_details.channel, transaction_details.sold_id,  warehouse.bcn, title, status, transaction_details.paid as revenue, cost, transaction_details.fee, returned_amount
															from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 1  AND transaction_details.sold_id IN 
			(select et_id from ebay_transactions  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and notpaid = 0 and ebayRefundAmount<paid) order by sold_id ASC');
            //echo '<p>NUM ROWS FROM EBAY '.$query->num_rows();
            //echo $this->db->last_query();
			if ($query->num_rows() >0 ) return $query->result_array();
			else return array();
		}
		else
		{
            //echo '<p>3';
			return array();
		}

	}

	function  SelectDetailedDataFromWarehouseForLATRWebsite($datefrom, $dateto, $sold_id_array=null)
	{
		//printcool($sold_id_array);

		if(count($sold_id_array)>0)
		{
			$this->db->select('wid as wid, transaction_details.channel, transaction_details.sold_id,  warehouse.bcn, title, status, transaction_details.paid as revenue, cost, returned_amount')
											->from('transaction_details')
											 ->join('warehouse','transaction_details.w_id=warehouse.wid')
											 ->where('transaction_details.sold_id is not null AND transaction_details.channel = 2')
											 ->where_in('transaction_details.sold_id',$sold_id_array)
											->order_by('sold_id', 'ASC');
			$q =  $this->db->get();
		   // echo '<p>NUM ROWS='.$q->num_rows();
		  // echo '<p>'.$this->db->last_query();
		   if ($q->num_rows() >0 ) return $q->result_array();
		   else return array();
		}
		elseif(isset($_POST['latr_all']) OR isset($_POST['show_all'])  /* or $this->show_all==1*/)
		{
			$query = $this->db->query('select wid as wid, transaction_details.channel, transaction_details.sold_id,  warehouse.bcn, title, status, transaction_details.paid as revenue, cost, returned_amount
															 from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 2  AND transaction_details.sold_id IN 
			(select oid from orders  where submittime <= '.$dateto.' and submittime >= '.$datefrom.' and complete<>-1) order by sold_id ASC');

		   //echo $this->db->last_query();
			if ($query->num_rows() >0 ) return $query->result_array();
			else return array();
		}
		else
		{
			return array();
		}

	}

	function  SelectDetailedDataFromWarehouseForWarehouse($datefrom, $dateto, $sold_id_array=null)
	{
		if(count($sold_id_array)>0)
		{
			$this->db->select('wid as wid, transaction_details.channel, transaction_details.sold_id, warehouse.bcn, title, status, transaction_details.paid as revenue, cost, transaction_details.returned_amount')
											->from('transaction_details')
											 ->join('warehouse','transaction_details.w_id=warehouse.wid')
											 ->where('transaction_details.sold_id is not null AND transaction_details.channel = 4')
											 ->where_in('transaction_details.sold_id', $sold_id_array)
											->order_by('sold_id', 'ASC');
			$q =  $this->db->get();
		   // echo '<p>NUM ROWS='.$q->num_rows();
		 //  echo '<p>'.$this->db->last_query();
		   if ($q->num_rows()>0)
		   {
			
				return $q->result_array();
		   }
		   else
		   {
		   
			return array();
		   }
		}
		elseif(isset($_POST['warehouse_all']) OR isset($_POST['show_all'])  /* or $this->show_all==1*/)
		{
			$query = $this->db->query('select wid as wid, transaction_details.channel, transaction_details.sold_id, transaction_details.w_id, title, status, transaction_details.paid as revenue, cost, transaction_details.returned_amount
											 from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 4  AND transaction_details.sold_id IN 
			(select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.') order by sold_id ASC');

		   // echo $this->db->last_query();
			if ($query->num_rows() >0 ) 
			{
			   return $query->result_array();
			}
			else
			{
		   
				 return array();
			}
		}
		else
		{
		   
			return array();
		}

	}
	//We have to append several arrays for each shown period in order to fill detailed table with all rows selected for the period

	function  AppendArray($array_temp, $datefrom, $dateto)
	{
        //printcool($array_temp);
	   //echo '<p>array temp '.count($array_temp);
		if(count($array_temp)>0)
		{
			$k=count($this->detailed_array);
			foreach ($array_temp as $row)
			{
                    //This forech check for duplicated transactions (when one bcn is sold twice in one transaction). If  
                    //there are duplicates the second one is not entered in detailed_array.
                    /* foreach ($this->detailed_array as $dt)
			        {
                        //echo '<p>WID - '.$dt[0];
                       // echo '<p>Sold - '.$dt[2];
                       $flag_repeated = false;
                       if($row['wid']==$dt[0] and $row['sold_id']==$dt[2]) 
                       {
                            //echo '<p>Sold - id '.$dt[2];
                            //echo '<p>WID - '.$dt[0];
                            $flag_repeated = true;
                            break;

                       }
                    }
            		
                    if($flag_repeated==true) continue;*/

                    $this->detailed_array[$k][0] = $row['wid'];

					//1 ebay, 4 Warehouse, 2 LATR
					switch($row['channel'])
					{
						case 1:
						  $this->detailed_array[$k][1] = 'eBay';
						  break;
						case 4:
						  $this->detailed_array[$k][1] = 'Warehouse';
						  break;
						case 2:
						  $this->detailed_array[$k][1] = 'LATR Website';
						  break;
						default:
							 $this->detailed_array[$k][1] = 'Unknown Channel Number '.$row['channel'];
						  break;
					}
					//$this->detailed_array[$k][1] = $row['channel'];
					$this->detailed_array[$k][2] = $row['sold_id'];//str_replace('/','-',$l['OrderDate']);
					$this->detailed_array[$k][3] = $row['bcn'];
					$this->detailed_array[$k][4] = substr($row['title'],0,35);
					
                    //Comment the row below if you want to see last status from log. Now showing current status
                    $this->detailed_array[$k][5] = $row['status'];
                    
                    if(isset($_POST['ebay_refunded']) or isset($_POST['ebay_partially_refunded']))
                    {
                    //if($row['returned_amount']>0)//NOT WORKING
                    //{
                       // $this->detailed_array[$k][6] = money_format('%(#10n', (float)$row['returned_amount']*(-1));
                       $this->detailed_array[$k][6] = money_format('%(#10n', (float)$row['revenue']);
                    }
                    else
                    {
                        $this->detailed_array[$k][6] = money_format('%(#10n', (float)$row['revenue']);
                    }
					$this->detailed_array[$k][7] = money_format('%(#10n', (float)$this->nuemp($row['cost']));

					if($this->detailed_array[$k][1] == 'eBay')
					{
						if(isset($row['wid']))
                        {
							
                            $temp_netprof1 = $this->SumNetProfitFromEbaybyWID($datefrom, $dateto, $row['wid'], $row['sold_id']);
							$this->detailed_array[$k][8] = money_format('%(#10n', (float)$temp_netprof1);
                        }
					}

					elseif($this->detailed_array[$k][1] == 'LATR Website')
					{
						if(isset($row['sold_id']))
                        {
							//$temp_netprof1 = $this->NetProfitFromLATRWebsiteForOneProduct($datefrom, $dateto, $row['sold_id']);
							$latr_sold_id_array = array();
							$latr_sold_id_array[] = $row['sold_id'];//we need array with one product because function needs array
							$temp_netprof1 = $this->SumNetProfitFromLATRWebsite($datefrom, $dateto, $latr_sold_id_array);


							$this->detailed_array[$k][8] = money_format('%(#10n', (float)$temp_netprof1);
                        }
					}
					elseif($this->detailed_array[$k][1] == 'Warehouse')
					{
						if(isset($row['sold_id']))
                        {
							//$temp_netprof1 = $this->NetProfitFromWarehouseForOneProduct($datefrom, $dateto, $row['sold_id']);
							$warehouse_sold_id_array = array();
							$warehouse_id_array[] = $row['sold_id'];//we need array with one product because function needs array
							$temp_netprof1 = $this->SumNetProfitFromWarehous($datefrom, $dateto, $warehouse_sold_id_array);

							$this->detailed_array[$k][8] = money_format('%(#10n', (float)$temp_netprof1);
                        }
					}
					else
					{
							$this->detailed_array[$k][8] =money_format('%(#10n', 0.00);
					}

					//if(isset($row['sold_id']))
					// {
						   //$temp_netproft = $this->SumNetProfitFromEbayBySoldId$datefrom, $dateto, $row['sold_id']);
						   // $this->detailed_array[$k][9] = money_format('%(#10n', (float)$temp_netproft);
							//echo '<p>'.$row['sold_id'].';'.$temp_netprof1.';'.$temp_netproft;
					 //}
					$k++;
			}
	   }
	}

	


	

	function GroupByStatus($channel_array=null)
	{
	   //echo '<p>Deatiled array is '.count($this->detailed_array);
	   //echo '<p>Channel array is '.count($channel);
		if(count($this->detailed_array)>0 and is_array($channel_array) and count($channel_array)>0)
		{
			//printcool($this->detailed_array);
			 $bcn_array = array();
			 $k=0;
        //1. We take all transaction numbers

			 foreach($this->detailed_array as $dt)
			 {
				 //echo '<p>'.printcool($dt);
				 $bcn_array[] = $dt[0];
				 $k++;
			  }

               //printcool($sold_id_array);
       
        //2. We select all statuses for the products selected in array $wid_array
            $this->db->select('status, count(*) as numbers')
                     ->from('warehouse')
                     ->where_in('channel',$channel_array)
                     ->where_in('wid', $bcn_array)
                     ->group_by('`status`');

			$query = $this->db->get();

			    //echo '<p>'.$this->db->last_query();

			$this->table_groupby_status = '<p><p><p><table border="0" style="color:blue"; font-size:"16px";>
												 <tr>
													<th>Status</th>
												  <!--  <th>Numbers</sup></th> -->
												 </tr>';
				foreach ($query->result() as $row)
				{
				   //$this->table_groupby_status.= '<tr>
				   //                                 <td style="font-weight:bold">'.$row->status.'</td>';
				   //$this->table_groupby_status.= '<td align="right">'.$row->numbers.'</td></tr>';
				   $this->table_groupby_status.= '<tr><td style="font-weight:bold">'.$row->status.'<sup style="color:red;">'.$row->numbers.'</sup></td></tr>';
				}
				$this->table_groupby_status.='</table>';
			}//if
		 }

		 

		 //function SelectDetailedDataForRefunded($datefrom, $dateto, $channel, $sold_id=null)
		 //{
			
		 //   if(!isset($sold_id) or !is_array($sold_id) and count($sold_id)<1 and isset($datefrom) and isset($dateto)  and isset($channel))
		 //   {
		 //       $query = $this->db->query('select sold_id, channel, transaction_details.fee, transaction_details.paid, cost, title, bcn, return_id from transaction_details
		 //               LEFT JOIN warehouse ON w_id = wid  
		 //               where uts <= '.(int)$dateto.' and uts >= '.(int)$datefrom.' and channel='.(int)$channel.' and return_id>0 and return_id is not null');
		
		 //       $query = $this->db->get();
		 //      //echo '<p>'.$this->db->last_query();
		 //      //echo '<p>'.$sold_id;

				
		 //       return $query;
		 //   }
		 //   else
		 //   {
		 //       $this->db->select('sold_id, channel, transaction_details.fee, transaction_details.paid, cost, title, bcn, return_id ')
		 //               ->from('transaction_details')
		 //               ->join('warehouse', 'w_id = wid', 'left')
		 //               ->where('channel',$channel)
		 //                ->where_in('sold_id', $sold_id);
					   
		 //       $query = $this->db->get();
		 //       return $query;
		 //   }

		 //}
		 //The idea is that $this->detailed_arrayis already filled and the function below just adds the last status of bcn for the period from warehouse_log table.
		 function SelectLastStatusFromLog($datefrom, $dateto)
		 {
			ini_set('memory_limit','2048M');
			set_time_limit(600);
			ini_set('mysql.connect_timeout', 600);
			ini_set('max_execution_time', 600);  
			ini_set('default_socket_timeout', 600);


			//echo '<p>SelectLastStatusFromLog';
			$year_from = (int)date('Y', $datefrom);// Not needed just in case
			$month_from = (int)date('m', $datefrom);
			$day_from = (int)date('d', $datefrom);// Not needed just in case

			$year_to = (int)date('Y', $dateto);
			$month_to = (int)date('m', $dateto);
			$day_to = (int)date('d', $dateto);// Not needed just in case


			$query = $this->db->query('select wid, bcn, datato from warehouse_log where field = "status" and year='.$year_to.' and month between '.$month_from.' and '. $month_to.' and day between '.$day_from.' and '. $day_to.' order by bcn ASC, year ASC, month ASC, day ASC');
			
			//$query = $this->db->query('select bcn, datato from warehouse_log A where `field`="status" and year='.$year_to.' and month between '.$month_from.' and '. $month_to.' and day between '.$day_from.' and '. $day_to.' and year+month+day=
			//(SELECT max(year+month+day) from warehouse_log where `field`="status" and year='.$year_to.' and month between '.$month_from.' and '. $month_to.' and day between '.$day_from.' and '. $day_to.'  and bcn=A.bcn) order by bcn ASC, year ASC, month ASC, day ASC');

			/*
			echo '<p>'.$this->db->last_query();
			$counter=count($this->detailed_array);
			echo '<p>Counter '.$counter;
			  //echo '<p>'.date("d m Y", $datefrom).'.........'.date("d m Y", $dateto);
			  //echo '<p>num rows '.$query->num_rows();
			for($i=0;$i<$counter;$i++)    
			{
				foreach($query->result_array() as $row)
				{
					//echo '<p>'.$row['bcn'];
					if($this->detailed_array[$i][3]==$row['bcn'])
					{
						$this->detailed_array[$i][5]=$row['datato'];
						//echo '<p>We found match '.$row['datato'];
					}

				}
			   
			}*/


			foreach($query->result_array() as $fieldrow)
			{
				$fielddata[$fieldrow['wid']] = array('bcn' => $fieldrow['bcn'], 'datato' => $fieldrow['datato']);
			  //echo '<p>'.$fieldrow['wid'];
			}

			//printcool($fielddata);
	 
			foreach($this->detailed_array as $k => $v)    
			{ 
				if (isset($fielddata[$v[0]]))   
				{
					$this->detailed_array[$k][5]=$fielddata[$v[0]]['datato'];
				}  
					  //  $this->detailed_array[$k][5]=$fielddata[$v[0]]['datato'];
					  //   $this->detailed_array[$k][5]= 'proba';
			}
						//echo '<p>We found match '.$row['datato'];
		
  
		 }

	

	function  SumEbayFullRefundAmount($datefrom, $dateto, $channel=null)
	{
        //$sold_id_array = array();
        //$sold_id_array = $this->EbayFullRefundTransID($datefrom, $dateto);

        //if(count($sold_id_array)>0)
        //{
        //    $this->db->select('sum(paid) as Refunded')
        //                                     ->from('transaction_details')
        //                                     ->where('sold_id is not null')
        //                                     ->where_in('sold_id',$sold_id_array,FALSE);
        //    $query_warehouse_expences = $this->db->get();
        //    $row_warehause = $query_warehouse_expences->first_row();
        //    echo '<p>'.$this->db->last_query();
        //    return (float)$row_warehause->Refunded;
        // }
        // else return 0;
    
        //REAL DATA

         $query = $this->db->query('select sum(ebayRefundAmount) as Refunded  from ebay_transactions
                                  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'
                                    and refunded=1 AND (sellingstatus NOT LIKE \'PartiallyPaid\' or sellingstatus is null)');

        // CHECK SQL
        //select sellingstatus, returnid, paid, ebayRefundAmount from ebay_transactions where refunded = 1 and  sellingstatus="PartiallyPaid"
      
		//echo '<p>'.$this->db->last_query();

		$sum_refunds = $query->first_row();
		return (float)$sum_refunds->Refunded;
	}

	function SumEbayFullRefundCost($datefrom, $dateto, $channel=null)
	{
        $sold_id_array = array();
        $sold_id_array = $this->EbayFullRefundTransID($datefrom, $dateto);

        if(count($sold_id_array)>0)
        {
            $this->db->select('sum(cost) as Cost')
                                             ->from('warehouse')
                                             ->where('sold_id is not null AND channel = 1')
                                             ->where_in('sold_id',$sold_id_array,FALSE);
            $query_warehouse_expences = $this->db->get();
            $row_warehause = $query_warehouse_expences->first_row();
            //echo '<p>'.$this->db->last_query();
            return (float)$row_warehause->Cost;
         }
         else return 0;
	}

    function SumEbayFullRefundedActualShipping($datefrom, $dateto)
    {
        $sold_id_array = array();
        $sold_id_array = $this->EbayFullRefundTransID($datefrom, $dateto);

        if(count($sold_id_array)>0)
        {
            $this->db->select('sum(`asc`) as ActSC')
                                             ->from('ebay_transactions')
                                             ->where('et_id is not null')
                                             ->where_in('et_id',$sold_id_array,FALSE);
            $query_warehouse_expences = $this->db->get();
            $row_warehause = $query_warehouse_expences->first_row();
           //echo '<p>'.$this->db->last_query();
            return (float)$row_warehause->ActSC;
         }
         else return 0;
    }


    function  EbayFullRefundTransID($datefrom, $dateto)
	{
          $query = $this->db->query('select  et_id  from ebay_transactions
                                  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'
        and (sellingstatus NOT LIKE \'PartiallyPaid\'  or sellingstatus is null) and refunded=1');

		$temp_array = array();

		if($query->num_rows()>0)
		{
			foreach($query->result_array() as $row)
			{
				$temp_array[] = (int)$row['et_id'];
               
			}
		}
      
		return $temp_array;
	}

    //PARTIALLY REFUNDED FUNCTIONS
    function  SumEbayPartialRefundAmount($datefrom, $dateto, $channel=null)
	{
        
        ////FROM TRANSACTION_DETAILS

        //$sold_id_array = array();
        //$sold_id_array = $this->EbayPartialRefundTransID($datefrom, $dateto);

        //if(count($sold_id_array)>0)
        //{
        //    $this->db->select('sum(paid) as Refunded')
        //                                     ->from('transaction_details')
        //                                     ->where('sold_id is not null')
        //                                     ->where_in('sold_id',$sold_id_array,FALSE);
        //    $query_warehouse_expences = $this->db->get();
        //    $row_warehause = $query_warehouse_expences->first_row();
        //    echo '<p>'.$this->db->last_query();
        //    return (float)$row_warehause->Refunded;
        // }
        // else return 0;


        //REAL DATA

        $query = $this->db->query('select sum(ebayRefundAmount) as Refunded  from ebay_transactions
                                  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'
        and sellingstatus=\'PartiallyPaid\' and refunded=1');

        // CHECK SQL
        //select sellingstatus, returnid, paid, ebayRefundAmount from ebay_transactions where refunded = 1 and  sellingstatus="PartiallyPaid"
        //echo '<p>'.$this->db->last_query();

		$sum_refunds = $query->first_row();
		return (float)$sum_refunds->Refunded; 
	}
	function SumEbayPartillyRefundCost($datefrom, $dateto, $channel=null)
	{
      
		$sold_id_array = array();
        $sold_id_array = $this->EbayPartialRefundTransID($datefrom, $dateto);

        if(count($sold_id_array)>0)
        {
            $this->db->select('sum(cost) as Cost')
                                             ->from('warehouse')
                                             ->where('sold_id is not null AND channel = 1')
                                             ->where_in('sold_id',$sold_id_array,FALSE);
            $query_warehouse_expences = $this->db->get();
            $row_warehause = $query_warehouse_expences->first_row();
            //echo '<p>'.$this->db->last_query();
            return (float)$row_warehause->Cost;
         }
         else return 0;
	}

	function  SumEbayPartialRefundNetProfit($datefrom, $dateto, $channel=null)
	{
        $query = $this->db->query('select sum(paid)-sum(ebayRefundAmount) as Refunded  from ebay_transactions
                                  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'
                                 and sellingstatus=\'PartiallyPaid\' and refunded=1');

           //echo '<p>'.$this->db->last_query();

		$sum_refunds = $query->first_row();
		return (float)$sum_refunds->NetProfit-$this->SumEbayPartillyRefundCost($datefrom, $dateto);
	}

	function  EbayPartialRefundTransID($datefrom, $dateto)
	{
          $query = $this->db->query('select  et_id  from ebay_transactions
                                  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'
        and sellingstatus=\'PartiallyPaid\' and refunded=1');

		$temp_array = array();

		if($query->num_rows()>0)
		{
			foreach($query->result_array() as $row)
			{
				$temp_array[] = (int)$row['et_id'];
               
			}
		}
      
		return $temp_array;
	}

    function SumEbayPartiallyRefundedActualShipping($datefrom, $dateto)
    {
        $sold_id_array = array();
        $sold_id_array = $this->EbayPartialRefundTransID($datefrom, $dateto);

        if(count($sold_id_array)>0)
        {
            $this->db->select('sum(`asc`) as ActSC')
                                             ->from('ebay_transactions')
                                             ->where('et_id is not null')
                                             ->where_in('et_id',$sold_id_array,FALSE);
            $query_warehouse_expences = $this->db->get();
            $row_warehause = $query_warehouse_expences->first_row();
            //echo '<p>'.$this->db->last_query();
            return (float)$row_warehause->ActSC;
         }
         else return 0;
    }

    function SumSSCProfit($datefrom, $dateto, $sold_id_array=null)
    {
        if(count($sold_id_array)>0)
        {
             $this->db->select('coalesce(sum(ssc),0) as SSC_Profit')
                                             ->from('ebay_transactions')
                                             ->where('et_id is not null and ssc>0')
                                             ->where_in('et_id',$sold_id_array,FALSE);
            $query_ssc = $this->db->get();

            //echo '<p>'.$this->db->last_query();

            $row = $query_ssc->first_row();
            return (float)$row->SSC_Profit;
        }
         else
        {
            //'select coalesce(sum(ssc),0)-coalesce(sum(`asc`),0) as SSC_Profit
			$query = $this->db->query('select coalesce(sum(ssc),0) as SSC_Profit
					                from ebay_transactions
				            where ssc>0 and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
		
            //echo '<p>'.$this->db->last_query();

             $row = $query->first_row();
            return (float)$row->SSC_Profit;
		}
       
    }

    //Refund are not included in the revenue. So in order to calculate the profit from given revenue we need to subtract
    //from net profit expenses made from the refunds during the same period when revenue is given.
    function SumRefundsAllExtraCosts($datefrom, $dateto, $sold_id=null)
    {
         if(isset($sold_id))
         {
                    $this->db->select('sum(returned_extracost)+sum(ebayreturnshipment)+sum(`asc`) as ExtraCost')
						->from('ebay_transactions')
                        ->where('refunded',1)
                    	->where_in('et_id', $sold_id);
				  
                    $query = $this->db->get();
         }  
         else
         {
                   $query = $this->db->query('select sum(returned_extracost)+sum(ebayreturnshipment)+sum(`asc`) as ExtraCost from ebay_transactions where refunded=1 and 
                             mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
        }
             
        $row = $query->first_row();
        return (float)$row->ExtraCost;
    }

    function RefundsSummaryTable($datefrom, $dateto, $sold_id=null)
    {
       //I take sold_id from detailed_array in order to provide sold_id of fully refunded to all the functions below.
       //If I call the functions with just period parameters they will include partially refunded transactions.
        
       $sold_id_array = Array(); 
       foreach($this->detailed_array as $dt)
       {
            $sold_id_array[]= $dt[2];

       }

       //printcool($sold_id_array);
        $this->table_refunds_summary = '<table border="0" style="color:blue" ; font-size:"8px";>
                                           <tr>
                                                <th colspan="2">Refunds Summary</th>
                                        </tr>
                                        <tr>
                                            <td>Refunded Amount</td>
                                            <td align=right>'.money_format('%.2n', (float)$this->Myreports_model->SumRefundedAmount($datefrom, $dateto, 1, null,  $sold_id_array)).'</td>
                                        </tr>
                                        <tr>
                                            <td>Lost Shipping</td>
                                            <td align=right>'.money_format('%.2n', (float)$this->Myreports_model->SumLostShippingExpences($datefrom, $dateto, 1, null,  $sold_id_array)).'</td>
                                        </tr>
                                        <tr>
                                            <td>Other Return Expenses</td>
                                            <td align=right>'.money_format('%.2n', (float)$this->Myreports_model->SumOtherReturnExpences($datefrom, $dateto, 1, null,  $sold_id_array)).'</td>
                                        </tr>
                                        <tr>
                                            <td>Sold Again Revenue</td>
                                            <td align=right>'.money_format('%.2n', (float)$this->Myreports_model->SumReturnsRecoupedRevenue($datefrom, $dateto, 1, $sold_id_array)).'</td>
                                        </tr>
                                        <tr>
                                            <td>Cost Refunded</td>
                                            <td align=right>'.money_format('%.2n', (float)$this->Myreports_model->SumCostRefunded($datefrom, $dateto, 1, $sold_id_array)).'</td>
                                        </tr>
                                        <tr>
                                            <td>Cost Scrapped</td>
                                            <td align=right>'.money_format('%.2n', (float)$this->Myreports_model->SumScrapCostLostRefunded($datefrom, $dateto, 1, null, $sold_id_array)).'</td>
                                        </tr>
                                    </table>';
            $this->mysmarty->assign('table_refunds_summary', $this->table_refunds_summary);
        
    }
  
  
}