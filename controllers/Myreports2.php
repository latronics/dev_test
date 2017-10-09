<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myreports2 extends Controller {

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

	function Myreports2()
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
        $this->load->helper('url');
	
        

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
					$this->db->where('notpaid', 1);
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
        		 //if ($_POST) $this->mysmarty->assign('show_all', $this->input->post('show_all', true)); //Turns off default period
		//echo date('jS F Y H:i.s', strtotime('-5 week'));
		// echo date("d m Y",strtotime('monday -5 week'));


		//$this->show_all=0;

         // FOR REFUNDED
        if ((isset($_POST['ebay_refunded']) OR isset($_POST['ebay_partially_refunded'])) and !isset($_POST['ebay_all']))
        {
            $_POST['ebay_pending_payment'] = null;  
            $_POST['ebay_notcomplete']= null;  
            $_POST['ebay_complete']= null;  
            $_POST['ebay_notshipped'] = null;  
            $_POST['latr_all']= null;  
            $_POST['latr_notcomplete']= null;  
            $_POST['latr_complete']  = null;  
            $_POST['latr_notshipped']= null;  
            $_POST['warehouse_all']= null;  
            $_POST['warehouse']  = null;  
            $_POST['warehouse_website_365']= null;  
            $_POST['warehouse_hawthorne_365']= null;  
            $_POST['warehouse_venice_365']= null;  
        }

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

        if((isset($_POST['ebay_refunded']) OR isset($_POST['ebay_partially_refunded']))
             and !isset($_POST['ebay_all']))
        {
           $salesOrRefundsTitle = 'Refunded Amount';
        }
        else
        {
            $salesOrRefundsTitle = 'Sales (without refunded)';
        }


		$this->table_transactions_summary.= '<p><table  class="transpode" border="1" style="color:blue"; font-size:"16px";>
										 <tr>
											<th>Period - '.$msg_period.'</th>
										   <th>'.$salesOrRefundsTitle.'<br><font size="1" color="red">(Sales + SSC)
                                        <br>(when Refunded is checked it shows the refunded amount)</font></th>
											<!-- <th>Fee</th> -->
											<th>Actual Shipping<br><font size="1" color="red">(when Refunded is checked it shows the shipping amount + refund shipping amount)</font></th>
											<th>Net Profit<br><font size="1" color="red">(Sales + SSC) - (Costs + Shipping + PayPal Fee + Extra Costs + <font color="yellow">Other Return Expenses  + Refunds Lost Shipping Expences</font>)</th>
											<th>Cost and Selling Fee <br><font size="1" color="red">(when Refunded is checked it shows only the cost)</font></th>
										 </tr>';

		// How many peiods we will show. Now 5 rows.
		for($counter = 0;$counter<12;$counter++)
		{
			   
			   //If dates are selected from the calendar. We create just one row in summary table.
			   if($_POST['submitPicker'])
			   {
					$this->CreateRow($datefrom, $dateto);
                    //echo '<p style="color:blue">From '.date('m/j/Y  H:i:s',$datefrom).' to '.date('m/j/Y  H:i:s',$dateto).'&#8595;';
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
			   // echo '<p style="color:blue">From '.date('m/j/Y  H:i:s',$datefrom).' to '.date('m/j/Y  H:i:s',$dateto).'&#8595;';
			   
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

//		$this->mysmarty->assign('det_table_message', 'Detailed Table <br> <font size=\'1\' color=\'blue\'>column Net Profit is equal to Sales - (Costs + Shipping + PayPal Fee + Extra Costs). Please, note that detailed table is product (bcn) based not transaction based and one transaction could appear on several row depending on the number of products it has. SSC is NOT included here in the revenue! Return Shipping, Other Return Expenses are NOT included as well, they are subtracted from the Net Profit in the summary table above only. </font>', true);
		$this->mysmarty->assign('curr_year', date('Y'));
		$this->mysmarty->assign('table_transactions_summary',$this->table_transactions_summary);
		
       
        if ((isset($_POST['ebay_refunded']) OR isset($_POST['ebay_partially_refunded'])) AND !isset($_POST['ebay_all']) AND !isset($_POST['ebay_pending_payment'])  
            AND !isset($_POST['ebay_notcomplete'])  AND !isset($_POST['ebay_complete'])  AND !isset($_POST['ebay_notshipped']) 
            AND !isset($_POST['latr_all'])  AND !isset($_POST['latr_notcomplete'])  AND !isset($_POST['latr_complete'])  
            AND !isset($_POST['latr_notshipped'])  AND !isset($_POST['warehouse_all'])  AND !isset($_POST['warehouse'])  
            AND !isset($_POST['warehouse_website_365'])  AND !isset($_POST['warehouse_hawthorne_365']) AND !isset($_POST['warehouse_venice_365']))
        {
		    //printcool($this->detailed_array);

            $this->RefundsSummaryTable($datefrom, $dateto);

            $fieldset = array(
		    'headers' => "'id', 'channel','trans <br> id','bcn', 'title', 'status', 'sold <br> price', 'cost','net <br> profit', 'refund <br> amount', 's/a <br> trans',  's/a <br> price', 's/a <br> net <br> profit', 'ssc'",
		    'width' => "50, 60, 50, 60, 220, 90, 60, 60, 60, 60, 60, 60, 60, 60",
		    'startcols' => 14,
		    'startrows' => count($this->detailed_array),
		    'colmap' => '{readOnly: true},{readOnly: true},{renderer: customRenderer},{renderer: customRenderer},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}');
        }
        else
        {
                //echo $this->table_transactions_summary;
		    $fieldset = array(
		    'headers' => "'id', 'channel','trans id','bcn', 'title', 'status', 'revenue', 'cost','net profit', 'ssc'",
		    'width' => "50, 60, 50, 60, 220, 90, 80, 120,80, 60",
		    'startcols' => 10,
		    'startrows' => count($this->detailed_array),
		    //'colmap' => '{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}');
            'colmap' => '{readOnly: true},{readOnly: true},{renderer: customRenderer},{renderer: customRenderer},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}');

        }

		
	
        


		$this->mysmarty->assign('headers', $fieldset['headers']);
		$this->mysmarty->assign('rowheaders', $fieldset['rowheaders']);
		$this->mysmarty->assign('width', $fieldset['width']);
		$this->mysmarty->assign('startcols', $fieldset['startcols']);
		$this->mysmarty->assign('startrows', $fieldset['startrows']);
		$this->mysmarty->assign('colmap', $fieldset['colmap']);

		//printcool($this->detailed_array);

        //SelectLastStatusFromLog searches the statuses from log for the period and if there is data I put in detailed_array!
		//$this->SelectLastStatusFromLog($start_date, $dateto);

        $this->str_replace_json("\"", "", $this->detailed_array);
        $this->str_replace_json("$", "", $this->detailed_array);
        $this->str_replace_json("'", "", $this->detailed_array);
        $this->str_replace_json("[", "", $this->detailed_array);
        $this->str_replace_json("]", "", $this->detailed_array);

       // printcool($this->detailed_array);

		$this->mysmarty->assign('loaddata', json_encode($this->detailed_array));
		$this->mysmarty->assign('hot', TRUE);

        

		$this->channel_array=(array_unique($this->channel_array));//$this->channel_array has some dublicate channels so we make them unique
		//echo '<p> After unique '.count($this->channel_array);
		$this->GroupByStatus($this->channel_array);//Function shows number of product in the detailed table grouped by their statuses

        

		$this->mysmarty->assign('table_groupby_status', $this->table_groupby_status);

        

		//echo $this->table_groupby_status;

		$this->mysmarty->view('myreports/myreports_report2.html');


		//$this->mysmarty->view('myreports/myreports_main.html');
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
		   
        //eBay count not paid transactions by fieled sellingstatus'=="Unpaid". These transactions in ebay reports are shown with sale = 0. 
           if((isset($_POST['ebay_all']) OR isset($_POST['show_all'])) AND $row['Channel']='eBay' and ($row['refunded']==0 or $row['sellingstatus']=="PartiallyPaid") and $row['sellingstatus']!="Unpaid")
		   {
				 $revenue = ($revenue + (float)$row['Revenue']+(float)$row['ssc']);
				 $fee = $fee + (float)$row['Fee'];
				 $actual_shipping = $actual_shipping + (float)$row['Actual Shipping Price'];
					
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

				if($row['paidtime'] == '' AND $row['notpaid'] == 1 AND $row['Channel']='eBay')
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
		  $array_temp = $this->SelectDetailedDataFromWarehouseForLATRWebsite($datefrom, $dateto, $oid_array);
          //$array_temp = $this->SelectDetailedDataFromWarehouseForLATRWebsite($datefrom, $dateto);
		  $this->AppendArray($array_temp,$datefrom, $dateto);

			
		  $this->channel_array[]=2;

		  //echo '<p>Channel array '.count($this->channel_array);
	 
		}

		if(isset($_POST['warehouse_venice_365']) OR isset($_POST['warehouse_hawthorne_365']) OR isset($_POST['warehouse_website_365']) OR isset($_POST['warehouse_all']) OR isset($_POST['show_all']) OR isset($_POST['warehouse']))
		{
		   $netprofit = $netprofit+(float)$this->SumNetProfitFromWarehouse($datefrom, $dateto, $woid_array);
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
			//$cost = $cost + $this->SumEbayFullRefundCost($datefrom, $dateto, $channel=1);



           
	

            //$actual_shipping = $actual_shipping + $this->SumEbayFullRefundedActualShipping($datefrom, $dateto);
			//$cost = $cost + $this->SumEbayPartillyRefundCost($datefrom, $dateto);
			$netprofit = $netprofit + 0;

            $sold_id_array = array();
			$sold_id_array = $this->Myreports_model->EbayFullRefundTransID($datefrom, $dateto);
            
            
            if(count($sold_id_array)>0)
            {
                $actual_shipping = $actual_shipping + (float)$this->Myreports_model->SumLostShippingExpences($datefrom, $dateto, 1, null,  $sold_id_array);

                $cost = $cost + $this->Myreports_model->SumCostRefunded($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id_array);

                $netprofit = $netprofit + $this->SumNetProfitFromEbayBySoldId($datefrom, $dateto, $sold_id_array);
                $netprofit =  $netprofit - $this->Myreports_model->SumRefundedAmount($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id_array);
            
                //printcool($sold_id_array);
            }

             //    echo "<p>ASC ".$actual_shipping;
			$array_temp = array();
			$array_temp = $this->SelectDetailedDataFromWarehouseForEbay($datefrom, $dateto, $sold_id_array);


			$this->AppendArray($array_temp,$datefrom, $dateto);
		
			$this->channel_array[]=1;

		}

		if(isset($_POST['ebay_partially_refunded']) and !isset($_POST['ebay_all']) and !isset($_POST['show_all'])) 
		{
			
            $revenue = $revenue + $this-> SumEbayPartialRefundAmount($datefrom, $dateto, $channel=1);
		    //$actual_shipping = $actual_shipping + $this->SumEbayPartiallyRefundedActualShipping($datefrom, $dateto);
			//$cost = $cost + $this->SumEbayPartillyRefundCost($datefrom, $dateto);
			//$netprofit = $this->SumEbayPartialRefundNetprofit($datefrom, $dateto, $channel=1);

			//I have to take detailed data from transaction_details table

			$sold_id_array = array();

			$sold_id_array = $this->EbayPartialRefundTransID($datefrom, $dateto, $channel=1);
            // printcool($sold_id_array);

            if(count($sold_id_array)>0)
            {

                $actual_shipping = $actual_shipping + (float)$this->Myreports_model->SumLostShippingExpences($datefrom, $dateto, 1, null, $sold_id_array);

                $netprofit = $netprofit + $this->SumNetProfitFromEbayBySoldId($datefrom, $dateto, $sold_id_array);
                $netprofit =  $netprofit - $this->Myreports_model->SumRefundedAmount($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id_array);
           
                $cost = $cost + $this->Myreports_model->SumCostRefunded($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id_array);
                //printcool($sold_id_array);
                //echo "<p>ASC ".$actual_shipping;
            }

			$array_temp = array();
			$array_temp = $this->SelectDetailedDataFromWarehouseForEbay($datefrom, $dateto, $sold_id_array);


			$this->AppendArray($array_temp,$datefrom, $dateto);
		
			$this->channel_array[]=1;
		}//foreach
	   
         if($netprofit<0) $netprofit=0;

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
		// echo '<p>'.$this->db->last_query();

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
		
        // echo '<p>'.$this->db->last_query();
			   
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
            //$this->db->select('sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit', FALSE)
            //                        ->from('transaction_details')
            //                        ->join('warehouse','transaction_details.w_id=warehouse.wid')
            //                        ->where('transaction_details.sold_id is not null AND transaction_details.channel = 1')
            //                        ->where_in('transaction_details.sold_id', $eid_array);

            	$this->db->select('sum(transaction_details.paid+ssc) - (sum(cost)+sum(transaction_details.fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit', FALSE)
                                    ->from('ebay_transactions')
                                    ->join('transaction_details','transaction_details.sold_id = ebay_transactions.et_id', 'left')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 1 and (ebay_transactions.refunded = 0 or sellingstatus = "PartiallyPaid")')
									->where_in('transaction_details.sold_id', $eid_array);

			$result = $this->db->get();
		    //echo '<p>'.$this->db->last_query();
			$row = $result->first_row();

			//return (float)$row->Profit+$this->Myreports_model->SumSSC($datefrom, $dateto, $eid_array);
            return (float)$row->Profit;
           
		}
		elseif(isset($_POST['ebay_all']) OR isset($_POST['show_all']) /* or $this->show_all==1*/)
		{
            //$query = $this->db->query('select sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit
            //                              from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
            //                        transaction_details.sold_id is not null AND transaction_details.channel = 1  AND transaction_details.sold_id IN 
            //(select et_id from ebay_transactions  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and notpaid = 0 and ebayRefundAmount<paid)');
            

            //sum(if(transaction_details.paid > 0, transaction_details.paid, ebay_transaction.paid))
            //            $query = $this->db->query('select sum(if(transaction_details.paid > 0, transaction_details.paid, if(qty = 1,ebay_transactions.paid,0))) - (sum(cost)+sum(transaction_details.fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit
            //                              from ebay_transactions left join transaction_details on et_id = sold_id inner join warehouse on transaction_details.w_id=warehouse.wid where
            //                        transaction_details.sold_id is not null AND transaction_details.channel = 1  AND transaction_details.sold_id IN 
            //(select et_id from ebay_transactions  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and (refunded = 0 or sellingstatus = "PartiallyPaid"))');

            $query = $this->db->query('select sum(transaction_details.paid+ssc) - (sum(cost)+sum(transaction_details.fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit
                                          from ebay_transactions left join transaction_details on et_id = sold_id left join warehouse on transaction_details.w_id=warehouse.wid where
                                    transaction_details.sold_id is not null AND transaction_details.channel = 1  AND  mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and (refunded = 0 or sellingstatus = "PartiallyPaid")');



			$row = $query->first_row();
            //echo '<p>'.$this->db->last_query();
           
			return (float)$row->Profit-($this->SumRefundsAllExtraCosts($datefrom, $dateto)+$this->Myreports_model->SumLostShippingExpences($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id=null));
            //return ((float)$row->Profit+$this->Myreports_model->SumSSC($datefrom, $dateto));
            //return (float)$row->Profit;
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

	function  SumNetProfitFromWarehouse($datefrom, $dateto, $eid_array=null)
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
		   
			$row = $result->first_row();
                //echo '<p>'.$this->db->last_query();

			return (float)$row->Profit;

		}
		elseif(isset($_POST['warehouse_all']) OR isset($_POST['show_all'])  /* or $this->show_all==1*/)
		{
			$query = $this->db->query('select sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)) as Profit
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 4  AND transaction_details.sold_id IN 
			(select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.')');

			$result = $query->first_row();
                //echo '<p>'.$this->db->last_query();

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
            $this->db->select("wid as wid, coalesce(transaction_details.channel, 1)  as channel, 
                                et_id as sold_id, warehouse.bcn, title, if(notpaid=1, 'No Status (Not Paid)', status) as status, IF(ebay_transactions.paid > 0 AND qty = 1, ebay_transactions.paid,       
        transaction_details.paid) AS revenue, ebay_transactions.paid as paid, eachpaid, ssc, coalesce(transaction_details.fee,
                                ebay_transactions.fee) as fee, transaction_details.returned_amount, ebayRefundAmount, 
                                coalesce(returnreason, 'No Reason') as returnreason, returnQuantity, 
                                ebay_transactions.returned_time as returned_time, ebay_transactions.returnid,
                                `datetime`, admin, return_total_qty, qty, transaction_details.return_id, sellingstatus
                                transaction_details.returnID, e_id", FALSE)		
							->from('ebay_transactions')
                                    ->join('transaction_details','transaction_details.sold_id=ebay_transactions.et_id', 'left')
                                    ->join('warehouse','transaction_details.w_id=warehouse.wid', 'left')
                                    	->where('coalesce(transaction_details.channel, 1) = 1', NULL, FALSE)
									    ->where_in('ebay_transactions.et_id', $sold_id_array)
                                        ->order_by('et_id', 'ASC');


           
            //"SELECT `wid` as wid, `transaction_details`.`channel`, `et_id`, `warehouse`.`bcn`, `title`, `status`, coalesce(`transaction_details`.`paid`, `ebay_transactions`.paid) as revenue, `cost`, coalesce(transaction_details.fee,ebay_transactions.fee) as fee, `transaction_details`.`returned_amount`, `ebayRefundAmount`, coalesce(`returnreason`,'No Reason') as returnreason, `returnQuantity`, `ebay_transactions`.`returned_time` as returned_time, `ebay_transactions`.`returnid`, `datetime`, `admin`, `return_total_qty`, `qty`, `transaction_details`.`return_id`, `transaction_details`.`returnID`, e_id
            //FROM (`ebay_transactions`)
            //left join transaction_details on `transaction_details`.`sold_id`=`ebay_transactions`.`et_id`
            //Left JOIN `warehouse` ON `transaction_details`.`w_id`=`warehouse`.`wid` 
            //WHERE `ebay_transactions`.`et_id` IN (52546, 52421, 52450, 52471, 52483, 52497, 52522, 52535, 52542, 52552, 52587, 52604, 52613) ORDER BY `et_id` ASC"

                                           
			$q =  $this->db->get();
		   //echo '<p>NUM ROWS='.$q->num_rows();
        	//echo '<p>'.$this->db->last_query();

		   if ($q->num_rows() >0 ) return $q->result_array();
		   else return array();
		}
		else
		{
			
            //$query = $this->db->query('select wid as wid, transaction_details.channel, transaction_details.sold_id,  warehouse.bcn, title, status, transaction_details.paid as revenue, cost, transaction_details.fee, returned_amount
            //                                                from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
            //                        transaction_details.sold_id is not null AND transaction_details.channel = 1  AND transaction_details.sold_id IN 
            //(select et_id from ebay_transactions  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and notpaid = 0 and ebayRefundAmount<paid) order by sold_id ASC');
            
            $query = $this->db->query('SELECT wid as wid, coalesce(transaction_details.channel, 1) as channel, 
                    et_id as sold_id, warehouse.bcn, title, if(notpaid=1, "No Status (Not Paid)", status) as status, 
                        IF(ebay_transactions.paid > 0 AND qty = 1, ebay_transactions.paid,       
        transaction_details.paid) AS revenue, ebay_transactions.paid as paid, eachpaid, cost, ssc,
                        coalesce(transaction_details.fee, 
                        ebay_transactions.fee) as fee, transaction_details.returned_amount, ebayRefundAmount, 
                    coalesce(returnreason, "No Reason") as returnreason, returnQuantity, ebay_transactions.returned_time as returned_time,
                    ebay_transactions.returnid, `datetime`, admin, return_total_qty, qty, transaction_details.return_id, 
                            transaction_details.returnID, e_id, sellingstatus  
                    FROM (ebay_transactions) LEFT JOIN transaction_details ON transaction_details.sold_id=ebay_transactions.et_id 
                    LEFT JOIN warehouse ON transaction_details.w_id=warehouse.wid 
                    WHERE coalesce(transaction_details.channel, 1) = 1 AND (refunded=0 or sellingstatus="PartiallyPaid") and COALESCE(sellingstatus,"")<>"Unpaid" AND 
                    mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' ORDER BY et_id ASC');


            //echo '<p>NUM ROWS FROM EBAY '.$query->num_rows();
            //echo $this->db->last_query();
			if ($query->num_rows() >0 ) return $query->result_array();
			else return array();
		}
	}	

	

	function  SelectDetailedDataFromWarehouseForLATRWebsite($datefrom, $dateto, $sold_id_array=null)
	{
		  //LATR web site missing transaction data in warehouse table and transaction details table. Made a complicated query which inserts on the missing bcn data the data from transaction in order 
            //to be visible the transaction at least in Report2. You can see the code for the fix on row 1418 on file Myreports2.php
            //transaction is 384.
            //Made a second query which fixes the same problem on row 1472. this is the case when the function has to work wit dates as a parameters

		if(count($sold_id_array)>0)
		{
            //$this->db->select('wid as wid, transaction_details.channel, oid as sold_id,  warehouse.bcn, title, 
            //                           warehouse.status, coalesce(transaction_details.paid, endprice) as revenue, cost, 
            //                           orders.returned_amount')
            //                                ->from('orders')
            //                                ->join('transaction_details','oid = sold_id', 'left')
            //                                 ->join('warehouse','transaction_details.w_id=warehouse.wid', 'left')
            //                                ->where('coalesce(transaction_details.channel, 2) = 2', NULL, FALSE)
            //                                 ->where_in('oid',$sold_id_array)
            //                                ->order_by('oid', 'ASC');
			//$q =  $this->db->get();
             $query = $this->db->query("SELECT wid as wid, transaction_details.channel, oid as sold_id, warehouse.bcn, title, warehouse.status, 
                                        coalesce(transaction_details.paid, endprice) as revenue, cost, orders.returned_amount 
                                        FROM orders 
                                        left JOIN transaction_details ON oid = sold_id 
                                        left JOIN warehouse ON transaction_details.w_id=warehouse.wid 
                                                      WHERE
                                        oid IN (".implode(",", $sold_id_array).")
                                        and 
                                        transaction_details.channel=2
                                        union ALL
                                        SELECT '' as wid, 2 as channel, oid as sold_id, '' as bcn, '' as title, '' as status, 
                                        endprice as revenue, '' as cost, orders.returned_amount 
                                        FROM (orders) where
                                        oid IN (".implode(",", $sold_id_array).") and 
                                        oid not IN
                                        (SELECT sold_id FROM transaction_details WHERE transaction_details.channel=2 AND sold_id IN  (".implode(",", $sold_id_array)."))");		   

		   // echo '<p>NUM ROWS='.$q->num_rows();
           //echo '<p>'.$this->db->last_query();
          
                //printcool($q->result_array());

		   if ($query->num_rows() >0 )
           {
              return $query->result_array();
           }
         
		}
		elseif(isset($_POST['latr_all']) OR isset($_POST['show_all'])  /* or $this->show_all==1*/)
		{
            //$query = $this->db->query('select wid as wid, coalesce(transaction_details.channel,2), oid as sold_id,  warehouse.bcn, title, 
            //                           warehouse.status, coalesce(transaction_details.paid, endprice) as revenue, cost, 
            //                           orders.returned_amount
            //                                from orders
            //                                left join
            //                        transaction_details on oid = sold_id
            //                        left join warehouse on transaction_details.w_id=warehouse.wid 
            //                        where
            //                        coalesce(transaction_details.channel,2) = 2 
            //                        and submittime <= '.$dateto.' and submittime >= '.$datefrom.' and complete<>-1 order by oid ASC');

          


            $query = $this->db->query("SELECT 
                                            wid AS wid,
                                            transaction_details.channel,
                                            oid AS sold_id,
                                            warehouse.bcn,
                                            title,
                                            warehouse.status,
                                            COALESCE(transaction_details.paid, endprice) AS revenue,
                                            cost,
                                            orders.returned_amount
                                        FROM
                                            orders
                                                LEFT JOIN
                                            transaction_details ON oid = sold_id
                                                LEFT JOIN
                                            warehouse ON transaction_details.w_id = warehouse.wid
                                        WHERE
                                            submittime <= ".$dateto." and submittime >= ".$datefrom." and complete<>-1 
                                                AND transaction_details.channel = 2 
                                        UNION ALL SELECT 
                                            '' AS wid,
                                            2 AS channel,
                                            oid AS sold_id,
                                            '' AS bcn,
                                            '' AS title,
                                            '' AS status,
                                            endprice AS revenue,
                                            '' AS cost,
                                            orders.returned_amount
                                        FROM
                                            (orders)
                                        WHERE
                                            submittime <= ".$dateto." and submittime >= ".$datefrom." and complete<>-1 
                                                AND oid NOT IN (SELECT 
                                                    sold_id
                                                FROM
                                                    transaction_details
                                                WHERE
                                                    transaction_details.channel = 2
                                                    AND sold_id IN (select oid from orders where submittime <= ".$dateto." and submittime >= ".$datefrom." and complete<>-1 ))");		   


                //echo $this->db->last_query();

                if ($query->num_rows() > 0)
                {
                   return $query->result_array();
                }
           
         }//elseif
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
		   // echo '<p>'.$this->db->last_query();
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
			$query = $this->db->query('select wid as wid, transaction_details.channel, transaction_details.sold_id, transaction_details.w_id,  warehouse.bcn, title, status, transaction_details.paid as revenue, cost, transaction_details.returned_amount
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
	//We have to append several arrays for each shown period in order to fill detailed table with all rows selected for the period.
    //If it is one period we call this function once.

    //ATTENTION:
    // the detailed_array structure is fixed. If you change the order of indexes of the array you have to adjust
    // the export function in myreports_report1.html view JSONToCSVExporter(), function RefundsSummaryTable(), function GroupByStatus()
	function  AppendArray($array_temp, $datefrom, $dateto)
	{
        //printcool($array_temp);
	   //echo '<p>array temp '.count($array_temp);
        $atts = array(
        'width'       => 800,
        'height'      => 600,
        'scrollbars'  => 'yes',
        'status'      => 'yes',
        'resizable'   => 'yes',
        'screenx'     => 0,
        'screeny'     => 0,
        'window_name' => '_blank'
        );
        //only for refunded.
        $temp_soldid = 0;
        $temp_refunded_amount = 0;


        $previous_sold_id = '';
        $previous_channel = '';


		if(count($array_temp)>0)
		{
			$k=count($this->detailed_array);
			foreach ($array_temp as $row)
			{
                    //This if check for duplicated transactions (transaction with one item has two items in transaction_details table). If  
                    //there are duplicates the second one is not entered in detailed_array. Example transaction 52820!
                   if($previous_sold_id == $row['sold_id'] and $previous_channel = $row['channel'] and $row['qty']==1)
                   {
                        continue;

                   }
                                   

                   if(isset($row['wid']) and !empty($row['wid']))
                   {
                        //$this->detailed_array[$k][0] = anchor_popup("Mywarehouse/bcndetails/".$row['wid'],  $row['wid'], $atts);
                        $this->detailed_array[$k][0] = $row['wid'];
                        $this->detailed_array[$k][3] = anchor_popup("Mywarehouse/bcndetails/".$row['wid'],  $row['bcn'], $atts);
                        //$this->detailed_array[$k][0] = '<a href="'.site_url($uri = 'Mywarehouse/bcndetails/'.$row['wid'], $protocol = NULL).'" target="_blank">'.$row['wid'].'</a>';
                       // echo '<p>'.anchor_popup("Mywarehouse/bcndetails/".$row['wid'],  $row['wid'], $atts);
                   }
                   else
                   {
                        $this->detailed_array[$k][3] = $row['bcn'];
                    }
                   

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
	                if($row['channel']==1)
                    {
                        $this->detailed_array[$k][2] = anchor_popup("Myebay/ShowOrder/".$row['sold_id']."/".(int)$row['channel'],  $row['sold_id'], $atts);
                    }
                    else
                    {
                        $this->detailed_array[$k][2] = $row['sold_id'];
                    }

                    $this->detailed_array[$k][14] = $row['sold_id'];//I need an array with sold_ids for RefundsSummaryTable() and for the JS export function JSONToCSVExporter().              					
                    $this->detailed_array[$k][15] = $row['bcn'];//I need an array with bcns for the JS exportfunction JSONToCSVExporter() and GroupByStatus function.          					

					 //If we have not products in table transaction_details attached to transaction in table ebay_transactions. We
                    //try to find at least item name in listings table 'ebay'. Channel == 1 means only for eBay.
                    if($row['title'] === NULL AND (int)$row['channel']===1)
                    {
                        $this->detailed_array[$k][4] = substr($this->Myreports_model->TitleFromEbayTable($row['e_id']),0,35);
                    }
                    else
                    {
					    $this->detailed_array[$k][4] = substr($row['title'],0,35);
                    }
					
                    //Comment the row below if you want to see last status from log. Now showing current status
                    
                    
                    $this->detailed_array[$k][5] = $row['status'];
                    
                    // FOR REFUNDED
                     if ((isset($_POST['ebay_refunded']) OR isset($_POST['ebay_partially_refunded'])) AND !isset($_POST['ebay_all']) AND !isset($_POST['ebay_pending_payment'])  
                        AND !isset($_POST['ebay_notcomplete'])  AND !isset($_POST['ebay_complete'])  AND !isset($_POST['ebay_notshipped']) 
                        AND !isset($_POST['latr_all'])  AND !isset($_POST['latr_notcomplete'])  AND !isset($_POST['latr_complete'])  
                        AND !isset($_POST['latr_notshipped'])  AND !isset($_POST['warehouse_all'])  AND !isset($_POST['warehouse'])  
                        AND !isset($_POST['warehouse_website_365'])  AND !isset($_POST['warehouse_hawthorne_365']) AND !isset($_POST['warehouse_venice_365']))
                    {
                           

                            if($row['sold_id']!=$temp_soldid)
                            {
                                $temp_soldid=$row['sold_id'];
                                $temp_refunded_amount = 0;
                       
                            }

                            //If revenue is 0 find the sell price through eachpaid field.
                            if($row['qty'] == 1 and (float)$row['revenue'] == 0)
                            {
                                $this->detailed_array[$k][6] = money_format('%(#10n', (float)$row['eachpaid']);
                            }
                            elseif($row['qty']>1 and (float)$row['revenue'] == 0)
                            {
                                $this->detailed_array[$k][6] = money_format('%(#10n', (float)$row['eachpaid']*$row['qty']);
                            }
                            else
                            {
                                $this->detailed_array[$k][6] = money_format('%(#10n', (float)$row['revenue']);
                            }


                            // If transaction has one product in it we can use ebayRefundAmount from ebay_transactions table 
                            // which is the sum of all refunds of transaction.
                            if($row['qty']==1)
                            {
                              
                                $this->detailed_array[$k][9] = money_format('%.2n', (float)$row['ebayRefundAmount']);
                                //$this->detailed_array[$k][9] = (float)$row['ebayRefundAmount'];
                            }
                            else
                            {
                                if((float)$row['returned_amount']>0)
                                {
                                    $this->detailed_array[$k][9] = money_format('%.2n', (float)$row['returned_amount']); 
                                //$this->detailed_array[$k][9] = (float)$row['returned_amount']; 
                                }
                                else
                                {
                                    $this->detailed_array[$k][9] = money_format('%.2n', (float)$row['ebayRefundAmount']);
                                }

                                 //if in transaction with many product we do not have particular product marked as refunded
                                 //we have to remove the row. We must know exactly which item is refunded. For transaction with no details (no products attached)
                                 //do not delete row in order to see transaction at least. The isset($row['wid']) prevents it.
                                if((float)$row['returned_amount']==0 and $row['return_id']==0 and !isset($row['returnID']) and isset($row['wid'])) 
                                {
                                        unset($this->detailed_array[$k]);
                                        continue;
                                }
                            }
                    
                            //We have bug! Doubled Refund Amount! Example transaction 53071. It is been refunded with 299.95$ 
                            //for the one product in the transaction. Transaction has two items and in the transaction_details table 
                            //both are marked as refunded with sum 299.95$. it must be one item market as refunded.
                            //So here I try to avoid Doubled Refund Amount!
                            if((int)$temp_soldid == (int)$row['sold_id'])
                            {
                                $temp_refunded_amount += (float)str_replace("$","",$this->detailed_array[$k][9]);

                                if($temp_refunded_amount > (float)$row['ebayRefundAmount'])
                                {
                                    //echo "<p>Temp Sold id ".$temp_soldid;
                                     $this->detailed_array[$k][9]=0;
                                }

                            }
                           



                            $this->detailed_array[$k][10] = $this->Myreports_model->SoldAgainTransNumber($channel=1, $row['sold_id'], $row['wid']);

                           

                            if($this->detailed_array[$k][10]>0)
                            {
                                 $SellPrise = $this->Myreports_model->SumSellPriseByWID($row['wid'], $this->detailed_array[$k][10], 1);
                                 $NetProfit = $this->Myreports_model->SumNetProfitFromEbaybyWID($row['wid'], $this->detailed_array[$k][10]);

                                

                                 $this->detailed_array[$k][11] = money_format('%(#10n', (float)$SellPrise);
                                 $this->detailed_array[$k][12] = money_format('%(#10n', (float)$NetProfit);

                            }
                            else
                            {
                                $this->detailed_array[$k][11] = money_format('%(#10n', (float)0);
                                $this->detailed_array[$k][12] = money_format('%(#10n', (float)0);
                            }

                            $this->detailed_array[$k][13] = money_format('%(#10n', (float)$row['ssc']/$row['qty']);
                          
                    }//end if is refunded
                                          
        //------------------- REVENUE BUG FIXES -------------------------------------//
  
                    //BUG FIX : different sums of one transaction in ebay_transaction table and transaction_details table!!!
                    // There are many cases that is why here are many ifs
                     


                     if($row['channel']==1)
                     {
                        //if($row['sold_id']==52905)
                        //{
                        //        echo "<p>trans 52905";
                        //        echo "<p>row[revenue]=".$row['revenue'];
                        //        echo "<p>row[paid]=".$row['paid'];
                        //        echo "<p>row[eachpaid]=".$row['eachpaid'];
                        //        echo "<p>row['ssc']=".$row['ssc'];
                        //        echo "<p>row[channel]=".$row['channel'];
                        //        echo "<p>row[sellingstatus]=".$row['sellingstatus'];

 
                        //}


                        if($row['qty']==1 or empty($row['wid']) or $row['wid']=='')
                        {
                            $this->detailed_array[$k][6] = money_format('%(#10n', (float)$row['paid']+(float)$row['ssc']);
                        }
                        elseif((float)$row['paid'] == (float)$row['revenue'] || $row['sellingstatus'] == 'PartiallyPaid')
                        {
                            
                        //This "if" is for PartiallyPaid with wrong amount in transaction_details. The idea is if amount is wrong
                        //(float)$row['eachpaid'] !== (float)$row['revenue'] and we have more than 1 product in transaction
                        //put eachpaid field. Must $row['revenue']>0 be grater than 0 because some refund could appear here
                        //52905 transaction is example.
                        
                       

                           



                            if((float)$row['eachpaid'] !== (float)$row['revenue'] and (float)$row['revenue']>0)
                            {
                                  $this->detailed_array[$k][6] = money_format('%(#10n', (float)$row['eachpaid']+(float)$row['ssc']/$row['qty']);
                            }
                            else
                            {
                                $this->detailed_array[$k][6] = money_format('%(#10n', (float)$row['revenue']+(float)$row['ssc']/$row['qty']);
                            }
                          
                        }
                        //we have different sums of one transaction in ebay_transaction table and transaction_details table!!!
                        //to fix this bug here I take the value of field eachpaid. It is not clear for me how this field behaves
                        //when we have multy (not many but multy) product transaction.
                        else
                        {
                          // if($row['wid']==166941) echo '<p> Wrong place '.$row['sellingstatus'] ;
                              $this->detailed_array[$k][6] = money_format('%(#10n', (float)$row['eachpaid']+(float)$row['ssc']/$row['qty']);
                        }
         //-------------------END  REVENUE BUG FIXES -------------------------------------//

                        $this->detailed_array[$k][9] = money_format('%(#10n', (float)$row['ssc']/$row['qty']);
                    }//if($row['channel']==1)
                    else
                    {
                         $this->detailed_array[$k][6] = money_format('%(#10n', (float)$row['revenue']);
                    }


                    $this->detailed_array[$k][7] = money_format('%(#10n', (float)$this->Myreports_model->nuemp($row['cost']));
              

                    //echo '<p>'.$row['wid'];
                    
					if($this->detailed_array[$k][1] == 'eBay')
					{
                        //echo '<p>'.$row['wid'];
                        if(((int)$row['wid']))
                        {
							//if((int)$row['wid'] == 144778)  echo '<p>wid 144778 is here';

                            $temp_netprof1 = $this->Myreports_model->SumNetProfitFromEbaybyWID((int)$row['wid'], (int)$row['sold_id']);
                                                   

							$this->detailed_array[$k][8] = money_format('%(#10n', (float)$temp_netprof1);
                        }
                        else
                        {
                            $this->detailed_array[$k][8] = money_format('%(#10n', (float)0);
                            //$this->detailed_array[$k][8] = money_format('%(#10n', (float)$this->SumNetProfitFromEbayBySoldId($datefrom=null, $dateto=null, $eid_array=$row['sold_id']));
                        }
					}

					elseif($this->detailed_array[$k][1] == 'LATR Website')
					{
						if(!empty($row['sold_id']))
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
						if(!empty($row['sold_id']) AND !empty($row['wid']))
                        {
							//$temp_netprof1 = $this->NetProfitFromWarehouseForOneProduct($datefrom, $dateto, $row['sold_id']);
							$warehouse_sold_id_array = array();
							$warehouse_sold_id_array[] = $row['sold_id'];//we need array with one product because function needs array
							$temp_netprof1 = $this->Myreports_model->SumNetProfitFromByWID($row['wid'], $row['sold_id'], $channel=4);



							$this->detailed_array[$k][8] = money_format('%(#10n', (float)$temp_netprof1);
                        }
					}
					else
					{
							$this->detailed_array[$k][8] =money_format('%(#10n', 0.00);
					}
                   
                     //This variabled are for a if check (at the beginning of the loop) for duplicated transactions (transaction with one item has two items in transaction_details table). If  
                    //there are duplicates the second one is not entered in detailed_array.
				    $previous_sold_id = $row['sold_id'];
                    $previous_channel = $row['channel'];
					$k++;
			}//foreach
	   }
	}

	
    function str_replace_json($search, $replace, $subject)
    {
         return json_decode(str_replace($search, $replace,  json_encode($subject)));

    } 

	

    //function GroupByStatus($channel_array=null)
    //{
    //   //echo '<p>Deatiled array is '.count($this->detailed_array);
    //   //echo '<p>Channel array is '.count($channel);
    //    if(count($this->detailed_array)>0 and is_array($channel_array) and count($channel_array)>0)
    //    {
    //        //printcool($this->detailed_array);
    //         $bcn_array = array();
    //         $sold_id_array = array();
    //         $k=0;
    //    //1. We take all transaction numbers

    //         foreach($this->detailed_array as $dt)
    //         {
    //             //echo '<p>'.$dt[2];
    //             if(!empty(trim($dt[0])) and isset($dt[0])) $bcn_array[] = $dt[0];
    //             if(!empty(trim($dt[14])) and isset($dt[14])) $sold_id_array[] = $dt[14];
    //             $k++;
    //          }

    //           //printcool($sold_id_array);
       
    //    //2. We select all statuses for the products selected in array $wid_array
            

    //        if(count($bcn_array)>0)
    //        {
    //            $this->db->select('status, count(*) as numbers')
    //                     ->from('warehouse')
    //                     ->where_in('channel', $channel_array)
    //                     ->where_in('wid', $bcn_array)
    //                     ->group_by('`status`');

    //            $query = $this->db->get();

    //              //  echo '<p>'.$this->db->last_query();

    //            $this->table_groupby_status = '<p><p><p><table border="0" style="color:blue"; font-size:"16px";>
    //                                                 <tr>
    //                                                    <th>Status</th>
    //                                                  <!--  <th>Numbers</sup></th> -->
    //                                                 </tr>';
    //                foreach ($query->result() as $row)
    //                {
    //                   //$this->table_groupby_status.= '<tr>
    //                   //                                 <td style="font-weight:bold">'.$row->status.'</td>';
    //                   //$this->table_groupby_status.= '<td align="right">'.$row->numbers.'</td></tr>';
    //                   $this->table_groupby_status.= '<tr><td style="font-weight:bold">'.$row->status.'<sup style="color:red;">'.$row->numbers.'</sup></td></tr>';
    //                }
    //                //We will show data only for one channel, for many channel it will be missleding
    //                if(count($channel_array)==1)
    //                {
                       
    //                    $this->table_groupby_status.= '<tr><td style="font-weight:bold">Cost Missing<sup style="color:red;">'.
    //                    //$this->Myreports_model->CountMissingCost($datefrom=null, $dateto=null, $channel=$channel_array[0], $subchannel=null, $sold_id_array)
    //                    $this->CountMissingCost()
    //                    .'</sup></td></tr>';
    //                }

    //                $this->table_groupby_status.='</table>';
    //           }//if(count($bcn_array)>0)
    //        }//if
    //     }

	function GroupByStatus($channel_array=null)
	{
	   //echo '<p>Deatiled array is '.count($this->detailed_array);
	   //echo '<p>Channel array is '.count($channel);
		if(count($this->detailed_array)>0 and is_array($channel_array) and count($channel_array)>0)
		{
			//printcool($this->detailed_array);
			 $bcn_array = array();
             $sold_id_array = array();
			 $k=0;
         
        //1. We take all transaction numbers

			 foreach($this->detailed_array as $dt)
			 {
				 //echo '<p>'.$dt[2];
				 $bcn_array[] = $dt[0];
                 $sold_id_array[] = $dt[2];
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

			   // echo '<p>'.$this->db->last_query();

			$this->table_groupby_status = '<table border="0" style="color:blue"; font-size:"16px";>
												 <tr>
													<th>Status</th>
												 </tr>';

            $counter = 0;
			foreach ($query->result() as $row)
			{
				   //$this->table_groupby_status.= '<tr>
				   //                                 <td style="font-weight:bold">'.$row->status.'</td>';
				   //$this->table_groupby_status.= '<td align="right">'.$row->numbers.'</td></tr>';
				   $this->table_groupby_status.= '<tr><td style="font-weight:bold">'.$row->status.'<sup style="color:red;">'.$row->numbers.'</sup></td></tr>';
                    $counter+=$row->numbers;
			}

             //echo '<p>num '.$counter;
             //echo '<p>num '.count($bcn_array);


            if($counter != count($bcn_array))
            {
                $this->table_groupby_status.= '<tr><td style="font-weight:bold">No Status<sup style="color:red;">'.abs($counter - count($bcn_array)).'</sup></td></tr>';

            }
            $this->table_groupby_status.='</table>';

            //$this->table_groupby_status.= '<p><p><p><table border="0" style="color:blue"; font-size:"16px";>
            //                                     <tr>
            //                                        <th>Cost Mising</th>
            //                                     </tr>';
            
            //$this->table_groupby_status.= '<tr><td style="font-weight:bold">Cost Missing<sup style="color:red;">'.$this->Myreports_model->CountMissingCost($datefrom=null, $dateto=null, $channel=1, $subchannel=null, $sold_id_array).'</sup></td></tr>';
            //$this->table_groupby_status.='</table>';
				
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
                                    and refunded=1 AND (sellingstatus NOT LIKE \'PartiallyPaid\')');
                                    // or sellingstatus is null
        // CHECK SQL
        //select sellingstatus, returnid, paid, ebayRefundAmount from ebay_transactions where refunded = 1 and  sellingstatus="PartiallyPaid"
      
		//echo '<p>'.$this->db->last_query();

		$sum_refunds = $query->first_row();
		return (float)$sum_refunds->Refunded;
	}

	function SumEbayFullRefundCost($datefrom, $dateto, $channel=null)
	{
        $sold_id_array = array();
        $sold_id_array = $this->Myreports_model->EbayFullRefundTransID($datefrom, $dateto);

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

    //function SumEbayFullRefundedActualShipping($datefrom, $dateto)
    //{
    //    $sold_id_array = array();
    //    $sold_id_array = $this->EbayFullRefundTransID($datefrom, $dateto);

    //    if(count($sold_id_array)>0)
    //    {
    //        $this->db->select('sum(`asc`) as ActSC')
    //                                         ->from('ebay_transactions')
    //                                         ->where('et_id is not null')
    //                                         ->where_in('et_id',$sold_id_array,FALSE);
    //        $query_warehouse_expences = $this->db->get();
    //        $row_warehause = $query_warehouse_expences->first_row();
    //       //echo '<p>'.$this->db->last_query();
    //        return (float)$row_warehause->ActSC;
    //     }
    //     else return 0;
    //}
     

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
        //sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit    


          $query = $this->db->query('select sum(ebay_transactions.paid)-(sum(ebayRefundAmount)+sum(ebay_transactions.fee)+sum(ebay_transactions.`asc`))) as NetProfit from ebay_transactions
                                        left join transaction_details on ebay_transactions.et_id=transaction_details.sold_id
                                  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and transaction_details.channel=1
                                 and sellingstatus=\'PartiallyPaid\' and refunded=1');

           //echo '<p>'.$this->db->last_query();

		$sum_refunds = $query->first_row();

        
		return (float)$sum_refunds->NetProfit-$this->SumEbayPartillyRefundCost($datefrom, $dateto);;
        //return (float) 0;
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

    //function SumEbayPartiallyRefundedActualShipping($datefrom, $dateto)
    //{
    //    $sold_id_array = array();
    //    $sold_id_array = $this->EbayPartialRefundTransID($datefrom, $dateto);

    //    if(count($sold_id_array)>0)
    //    {
    //        $this->db->select('sum(`asc`) as ActSC')
    //                                         ->from('ebay_transactions')
    //                                         ->where('et_id is not null')
    //                                         ->where_in('et_id',$sold_id_array,FALSE);
    //        $query_warehouse_expences = $this->db->get();
    //        $row_warehause = $query_warehouse_expences->first_row();
    //        //echo '<p>'.$this->db->last_query();
    //        return (float)$row_warehause->ActSC;
    //     }
    //     else return 0;
    //}


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
       
       if(count($this->detailed_array)>0)
       { 
           $sold_id_array = Array(); 
           foreach($this->detailed_array as $dt)
           {
                
                //echo '<p>'.(int)$dt[2];
                $sold_id_array[]= $dt[14];

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
       }//if 
    }
  
    function CountMissingCost()
    {    
        $counter=0;
        
        if(count($this->detailed_array) > 1)
        {
            
            foreach($this->detailed_array as $dt)
            {
                if((float)str_replace("$","",$dt[7]) == 0)
                 {
                     $counter ++;
                 }
            }
         }   
         return $counter;
    }

  
}