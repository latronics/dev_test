<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myreports3 extends Controller {

	/*

	The idea of this controller is to make report with two tables - one is the summary table and the other is the detailed table based on 
	the data in the summary table.

	Function FiveWeeksBefore calls is the start function.

	There is some difference in data between transactions tables and warehouse table because we need set shipped to be pressed and 
	only then paid is not entered in the warehouse. This means that Sales by transaction and sales by bcn are different.

	Meaning of vended filed in the warehouse table: vended = 0 - listed, vended = 2 - on hold, vended =1 - shipped

	*/

	private $table_transactions_summary='';
	public $table_groupby_status='';
    private $table_refunds_summary='';
    private $table_groupby_return_category='';
    private $table_groupby_return_reason='';

    private $ebay_stayle_refunds=0;


	private $revenue=0.0;
	private $total_fee=0.0;
	private $lost_shipping=0.0;
	private $refunded_amount=0.0;
	private $cost=0.0;
    private $SumSoldAgainNetProfit=0.0;
    private $sumSSCRefunded=0.0;
	private $detailed_array;
	private $channel_array = array();//we need that for GroupByStatus function as parameter


	function Myreports3()
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
        $this->mysmarty->assign('hicharts', TRUE);
		//echo $this->show_all;
		$this->detailed_array = array();
	
        setlocale(LC_MONETARY, 'en_US');

	}
	function index()
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
		else
		{
           // $datefrom = strtotime('1 January '.date("Y"));
           //$dateto =  strtotime('last day of March '.date("Y"));
           // Just for initialisation of the calendar when the page is opened
			//$datefrom=mktime(0, 0, 0, date('m'), date('d')-31, date('Y'));
			//$dateto=mktime(23, 59, 59, date('m'), date('d')-20, date('Y'));

            //echo '<p>'.$datefrom.' '.$dateto;

            $datefrom = mktime(0, 0, 0, 5, 1, date('Y'));
			$dateto = mktime(23, 59, 59, 5, 31, date('Y'));

            $this->mysmarty->assign('ofrom', date('m/j/Y', $datefrom));
		    $this->mysmarty->assign('oto', date('m/j/Y',$dateto));
		}

			
         //echo '<p>'.$datefrom.' '.$dateto;
	
			//echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';

		


		
		$start_date = $datefrom;//we need that for status function
 
		//$this->mysmarty->assign('ofrom', date('m/j/Y', $datefrom));
		//$this->mysmarty->assign('oto', date('m/j/Y'));

		//echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
		
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




		$this->table_transactions_summary.= '<p><table  class="transpode" border="1" style="color:blue"; font-size:"16px";>
										         <tr>
											        <th>Period - '.$msg_period.'</th>
										            <th>Revenue</th>
											        <th>Lost Shipping</th>
											        <th>Refunded Amount</th>
											        <th>Cost</th>
										         </tr>';

		//If dates are selected from the calendar. We create just one row in summary table.
        //if($_POST['submitPicker'])
        //{
			$this->Main($datefrom, $dateto);
            //echo '<p style="color:blue">From '.date('m/j/Y  H:i:s',$datefrom).' to '.date('m/j/Y  H:i:s',$dateto).'&#8595;';
			

			

		    $this->table_transactions_summary.= '<tr><td align="right" style="font-weight:bold">Total</td>
											    <td align="right" style="font-weight:bold">'.money_format('%(#10n', $this->revenue).'</td>
										        <td align="right" style="font-weight:bold">'.money_format('%(#10n', $this->lost_shipping).'</td>
											    <td align="right" style="font-weight:bold">'.money_format('%(#10n', $this->refunded_amount).'</td>
											    <td align="right" style="font-weight:bold">'.money_format('%(#10n', $this->cost).'</td></tr></table>';

		    $this->mysmarty->assign('cal', true);
		
		    $this->mysmarty->assign('ebay_refunded', $this->input->post('ebay_refunded', true));
		    $this->mysmarty->assign('ebay_partially_refunded', $this->input->post('ebay_partially_refunded', true));
		
            $this->FillChartData($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id=null);

		    //$this->mysmarty->assign('det_table_message', 'Detailed Table  <font size=\'1\' color=\'blue\'>column Net Profit is equal to Sales - (Costs + Shipping + PayPal Fee + Extra Costs). Please, note that detailed table is product (bcn) based not transaction based and one transaction could appear on several row depending on the number of products it has. SSC is NOT included here! Return Shipping, Other Return Expenses are NOT included as well, they are subtracted from the Net Profit in the summary table above only. </font><font size=\'1\' color=\'red\'>S/A means SOLD AGAIN</font>', true);
		    $this->mysmarty->assign('curr_year', date('Y'));
		    //$this->mysmarty->assign('table_transactions_summary',$this->table_transactions_summary);
		
    
		    $this->RefundsSummaryTable($datefrom, $dateto);
            $this->NotPaidPartiallyPaidRefundsSummaryTable($datefrom, $dateto, $sold_id=null);


            

            $this->table_groupby_return_reason = $this->GroupByReturnReason($datefrom, $dateto, $sold_id_array=null);
            $this->table_groupby_return_category = $this->GroupByStoreCategory($datefrom, $dateto, $sold_id_array=null, $limitRows=10);
            $this->mysmarty->assign('table_groupby_return_reason', $this->table_groupby_return_reason);
            $this->mysmarty->assign('table_groupby_return_category', $this->table_groupby_return_category);
        //}
        $fieldset = array(
		'headers' => "'id', 'channel','trans <br> id','bcn', 'title', 'status', 'sold <br> price', 'cost', 'return <br> reason', 'refund <br> amount', 's/a <br> trans',  's/a <br> price', 's/a <br> net <br> profit', 'ssc', 'return<br>id','trans<br>date','returned<br>date','admin'",
		'width' => "40, 60, 50, 50, 220, 60, 60, 60, 80, 60, 60, 60, 60, 60, 60, 60, 60, 60",
		'startcols' => 18,
		'startrows' => count($this->detailed_array),
		'colmap' => '{readOnly: true},{readOnly: true},{renderer: customRenderer},{renderer: customRenderer},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}');
       
	
            //$this->detailed_array[$k][13] = $row['returnQuantity'];  
            //        $this->detailed_array[$k][14] = $row['returnid'];  
            //        $this->detailed_array[$k][15] = $row['datetime'];
            //        $this->detailed_array[$k][16] = $row['returned_time'];
            //        $this->detailed_array[$k][17] = $row['admin'];


		$this->mysmarty->assign('headers', $fieldset['headers']);
		$this->mysmarty->assign('rowheaders', $fieldset['rowheaders']);
		$this->mysmarty->assign('width', $fieldset['width']);
		$this->mysmarty->assign('startcols', $fieldset['startcols']);
		$this->mysmarty->assign('startrows', $fieldset['startrows']);
		$this->mysmarty->assign('colmap', $fieldset['colmap']);

		//printcool($this->detailed_array);


		$this->mysmarty->assign('loaddata', json_encode($this->detailed_array));
		$this->mysmarty->assign('hot', TRUE);

        

		$this->channel_array=(array_unique($this->channel_array));//$this->channel_array has some dublicate channels so we make them unique
		//echo '<p> After unique '.count($this->channel_array);
		$this->GroupByStatus($this->channel_array);//Function shows number of product in the detailed table grouped by their statuses

        

		$this->mysmarty->assign('table_groupby_status', $this->table_groupby_status);
        //echo $this->table_groupby_status;

		$this->mysmarty->view('myreports/myreports_report3.html');

	}

   
    function Main($datefrom, $dateto)
    {
    	//echo '<p>eBay_refunded = '.	isset($_POST['ebay_refunded']).', eBay_partially_refunded='.isset($_POST['ebay_partially_refunded']);	
	   
       
          $this->revenue = $this->Myreports_model->SumSales($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id=null);

        //if(isset($_POST['ebay_refunded']) or (!isset($_POST['ebay_refunded']) and !isset($_POST['ebay_partially_refunded']))) 
        //{
			$sold_id_array = array();



			$sold_id_array = $this->Myreports_model->EbayFullRefundTransID($datefrom, $dateto);

            //echo '<p>Sold Id array refunded '.count($sold_id_array);
            //echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
		
         
		   
			$this->cost += $this->Myreports_model->SumEbayFullRefundCost($datefrom, $dateto, $channel=1);
            $this->lost_shipping += $this->Myreports_model->SumLostShippingExpences($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id_array);
			$this->refunded_amount += $this->SumEbayFullRefundAmount($datefrom, $dateto, $channel=1);


           // printcool($sold_id_array);

			$array_temp = array();
			$array_temp = $this->SelectDetailedDataFromWarehouseForEbay($datefrom, $dateto, $sold_id_array);

			$this->AppendArray($array_temp,$datefrom, $dateto);
			$this->channel_array[]=1;

		//}

        //if(isset($_POST['ebay_partially_refunded'])) 
        //{
		    $sold_id_array = array();
            $sold_id_array = $this->EbayPartialRefundTransID($datefrom, $dateto, $channel=1);	
        
            //echo '<p>Sold Id array partially refunded '.count($sold_id_array);

            $this->revenue += 0;
		    $this->lost_shipping +=$this->Myreports_model->SumLostShippingExpences($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id_array);
			$this->cost = $this->SumEbayPartillyRefundCost($datefrom, $dateto);
			$this->refunded_amount += $this->SumEbayPartialRefundAmount($datefrom, $dateto, $channel=1);

			//I have to take detailed data from transaction_details table
            //printcool($sold_id_array);

			$array_temp = array();
			$array_temp = $this->SelectDetailedDataFromWarehouseForEbay($datefrom, $dateto, $sold_id_array);

			$this->AppendArray($array_temp,$datefrom, $dateto);
			$this->channel_array[]=1;
		//}
	   


		$this->table_transactions_summary.= '<tr>
											<td style="font-weight:bold">From '.date('m/j/Y',$datefrom).' to '.date('m/j/Y',$dateto).'</td>';
		$this->table_transactions_summary.= '<td align="right">'.money_format('%(#10n', $this->revenue).'</td>';
		$this->table_transactions_summary.= '<td align="right">'.money_format('%(#10n', $this->lost_shipping).'</td>';
		$this->table_transactions_summary.= '<td align="right">'.money_format('%(#10n', $this->refunded_amount).'</td>';
		$this->table_transactions_summary.= '<td align="right">'.money_format('%(#10n', $this->cost).'</td></tr>';

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

			return (float)$row->Profit+$this->Myreports_model->SumSSC($datefrom, $dateto, $eid_array);
           
		}
		elseif(isset($_POST['ebay_all']) OR isset($_POST['show_all']) /* or $this->show_all==1*/)
		{
			$query = $this->db->query('select sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 1  AND transaction_details.sold_id IN 
			(select et_id from ebay_transactions  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and notpaid = 0 and ebayRefundAmount<paid)');


			$row = $query->first_row();
            //echo '<p>'.$this->db->last_query();
			return ((float)$row->Profit+$this->Myreports_model->SumSSC($datefrom, $dateto))-$this->SumRefundsAllExtraCosts($datefrom, $dateto);
        }
		else
		{
			return 0;
		}
			
	}
  	
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

	

	function  SelectDetailedDataFromWarehouseForEbay($datefrom, $dateto, $sold_id_array=null)
	{
       // echo '<p>Selecting Detailed Data from Ebay. Sold Id array e '+ count($sold_id_array);
		//printcool($sold_id_array);
        //echo '<p> COUNT = '.count($sold_id_array);
		if(count($sold_id_array)>0)
		{
			//echo '<p>1';

            $this->db->select("wid as wid, coalesce(transaction_details.channel, 1)  as channel, 
                                et_id as sold_id, warehouse.bcn, title, if(notpaid=1, 'No Status (Not Paid)', status) as status, if(coalesce(transaction_details.paid,0) = 0, 
                                ebay_transactions.eachpaid, ebay_transactions.paid) as revenue, cost, if(ssc = 0, ssc_old, ssc) as ssc, coalesce(transaction_details.fee,
                                ebay_transactions.fee) as fee, transaction_details.returned_amount, ebayRefundAmount, 
                                coalesce(returnreason, 'No Reason') as returnreason, returnQuantity, 
                                ebay_transactions.returned_time as returned_time, ebay_transactions.returnid,
                                `datetime`, admin, return_total_qty, qty, transaction_details.return_id, 
                                transaction_details.returnID, e_id", FALSE)		
							->from('ebay_transactions')
                                    ->join('transaction_details','transaction_details.sold_id=ebay_transactions.et_id', 'left')
                                    ->join('warehouse','transaction_details.w_id=warehouse.wid', 'left')
                                    	->where('coalesce(transaction_details.channel, 1) = 1', NULL, FALSE)
                                        ->where('(refunded=1  or COALESCE(sellingstatus,"")="Unpaid")')
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
            
            $query = $this->db->query('SELECT wid as wid, coalesce(transaction_details.channel, 1) as channel, et_id as sold_id, warehouse.bcn, title, if(notpaid=1, "No Status (Not Paid)", status) as status, 
                        if(coalesce(transaction_details.paid,0) = 0, 
                                ebay_transactions.eachpaid, ebay_transactions.paid) as revenue, if(ssc = 0, ssc_old, ssc) as ssc, cost, coalesce(transaction_details.fee, 
                        ebay_transactions.fee) as fee, transaction_details.returned_amount, ebayRefundAmount, 
                    coalesce(returnreason, "No Reason") as returnreason, returnQuantity, ebay_transactions.returned_time as returned_time,
                    ebay_transactions.returnid, `datetime`, admin, return_total_qty, qty, transaction_details.return_id,
                            transaction_details.returnID, e_id 
                    FROM (ebay_transactions) LEFT JOIN transaction_details ON transaction_details.sold_id=ebay_transactions.et_id 
                    LEFT JOIN warehouse ON transaction_details.w_id=warehouse.wid 
                    WHERE coalesce(transaction_details.channel, 1) = 1 AND 
                    mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and (refunded=1  or COALESCE(sellingstatus,"")="Unpaid") ORDER BY et_id ASC');


            //echo '<p>NUM ROWS FROM EBAY '.$query->num_rows();
            //echo $this->db->last_query();
			if ($query->num_rows() >0 ) return $query->result_array();
			else return array();
		}
	}	

	

	
	//We have to append several arrays for each shown period in order to fill detailed table with all rows selected for the period

	function  AppendArray($array_temp, $datefrom, $dateto)
	{
        //printcool($array_temp);
	   //echo '<p>array temp '.count($array_temp);

        $this->soldAgainCounter = 0;

        $temp_soldid = 0;
        $temp_refunded_amount = 0;

		if(count($array_temp)>0)
		{
			$k=count($this->detailed_array);
			foreach ($array_temp as $row)
			{
                    //This forech check for duplicated transactions (when one bcn is sold twice in one transaction). If  
                    //there are duplicates the second one is not entered in detailed_array.
                   
                    if(isset($row['wid']))
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
					//$this->detailed_array[$k][1] = $row['channel'];
					//$this->detailed_array[$k][2] = $row['sold_id'];
                    $this->detailed_array[$k][2] = anchor_popup("Myebay/ShowOrder/".$row['sold_id']."/".(int)$row['channel'],  $row['sold_id'], $atts);
                    //$this->detailed_array[$k][3] = $row['bcn'];
                    $this->detailed_array[$k][18] = $row['sold_id'];//I need an array with sold_ids for RefundsSummaryTable() and for the JS export function JSONToCSVExporter().              					
                    $this->detailed_array[$k][19] = $row['bcn'];//I need an array with bcns for the JS exportfunction JSONToCSVExporter() and GroupByStatus function.          					



                    if($row['sold_id']!=$temp_soldid)
                    {
                        $temp_soldid=$row['sold_id'];
                        $temp_refunded_amount = 0;
                       
                    }

					

                    
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
                    
                        
                    // $this->detailed_array[$k][6] = money_format('%.2n', (float)$row['revenue']);
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


                    $this->detailed_array[$k][7] = money_format('%.2n', (float)$this->Myreports_model->nuemp($row['cost']));




                    $this->detailed_array[$k][8] = $row['returnreason'];

                    
                    //If transaction has one product in it we can use ebayRefundAmount from ebay_transactions table which is the sum of all
                    //refunds of transaction.
                    if($row['qty']==1)
                    {
                              
                        $this->detailed_array[$k][9] = money_format('%.2n', (float)$row['ebayRefundAmount']);
                        //$this->detailed_array[$k][9] = (float)$row['ebayRefundAmount'];
                    }
                    //if in transaction with many product we do not have particular product marked as refunded
                    //we have to remove the row. We must know exactly which item is refunded.
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
                       $this->SumSoldAgainNetProfit+=$NetProfit;
                       $this->detailed_array[$k][11] = money_format('%.2n', (float)$SellPrise);
                       $this->detailed_array[$k][12] = money_format('%.2n', (float)$NetProfit);
                                              
                    }
                    else
                    {
                       $this->detailed_array[$k][11] = money_format('%.2n', (float)0);
                       $this->detailed_array[$k][12] = money_format('%.2n', (float)0);
                    }
                    
                    if((float)$row['ssc'] > 0)  $this->detailed_array[$k][13] = money_format('%.2n', (float)$row['ssc']);  
                    else $this->detailed_array[$k][13] = money_format('%.2n', (float)$row['ssc']);  
                    
                    $this->detailed_array[$k][14] = $row['returnid'];  
                    $this->detailed_array[$k][15] = substr($row['datetime'], 0, 10); 
                    $this->detailed_array[$k][16] = substr($row['returned_time'], 0, 10); 
                    $this->detailed_array[$k][17] = $row['admin'];
                    //$this->detailed_array[$k][18] = $row['ssc'];
                    					
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

	function SumEbayFullRefundedActualShipping($datefrom, $dateto)
    {
        $sold_id_array = array();
        $sold_id_array = $this->Myreports_model->EbayFullRefundTransID($datefrom, $dateto);

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
                $sold_id_array[]= $dt[18];

           }

           //printcool($sold_id_array);
           $this->sumSSCRefunded = (float)$this->Myreports_model->SumSSC($datefrom, $dateto, $sold_id_array=$this->Myreports_model->EbayRefundTransID($datefrom, $dateto));

           $this->ebay_stayle_refunds = (float)$this->Myreports_model->SumRefundedAmountEbayStyle($datefrom, $dateto, 1, null,  $sold_id_array);

           $this->table_refunds_summary = '<table border="0" style="color:blue" ; font-size:"8px";>
                                               <tr>
                                                    <th colspan="2">Refunds Summary</th>
                                            </tr>
                                            <tr>
                                                <td>Refunded Amount</td>
                                                <td align=right style="color:red"><b>'.money_format('%.2n', (float)$this->Myreports_model->SumRefundedAmount($datefrom, $dateto, 1, null,  $sold_id_array)).'</b></td>
                                            </tr>
                                            <tr>
                                                <td>eBay Way Refunded Amount</td>
                                                <td align=right style="color:red"><b>'.money_format('%.2n', (float)$this->Myreports_model->SumRefundedAmountEbayStyle($datefrom, $dateto, 1, null,  $sold_id_array)).'</b></td>
                                            </tr>
                                            <tr>
                                                <td>Lost Shipping</td>
                                                <td align=right style="color:red">'.money_format('%.2n', (float)$this->Myreports_model->SumLostShippingExpences($datefrom, $dateto, 1, null,  $sold_id_array)).'</td>
                                            </tr>
                                            <tr>
                                                <td>Sold Again Revenue</td>
                                                <td align=right style="color:blue"><b>'.money_format('%.2n', (float)$this->Myreports_model->SumReturnsRecoupedRevenue($datefrom, $dateto, 1, $sold_id_array)).'</b></td>
                                            </tr>
                                            <tr>
                                                <td>Sold Again Net Profit</td>
                                                <td align=right style="color:blue">'.money_format('%.2n', (float)$this->SumSoldAgainNetProfit).'</td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Sold Again Number</td>
                                                <td align=right>'.number_format((float)$this->CountSoldAgain()).'</td>
                                            </tr>
                                            <tr>
                                                <td>PayPal refunded to La-Tronics fee</td>
                                                <td align=right  style="color:blue">'.money_format('%.2n', (float)$this->Myreports_model->SumRefundedAmount($datefrom, $dateto, 1, null,  $sold_id_array)/100*2.6).'</td>
                                            </tr>
                                            <tr>
                                                <td>SSC of Returned is Refunded</td>
                                                <td align=right style="color:red">'.money_format('%.2n', $this->sumSSCRefunded).'</td>
                                            </tr>
                                             <tr>
                                                <td>Returned % from Revenue</td>

                                                <td align=right>'.number_format((float)$this->refunded_amount/($this->revenue+$this->refunded_amount+$this->sumSSCRefunded)*100).'%</td>
                                            </tr>
                                            <tr>
                                                <td>AVG return amount</td>
                                                <td align=right>'.money_format('%.2n', (float)$this->Myreports_model->AvgRefundAmount($datefrom, $dateto)).'</td>
                                            </tr>
                                             <tr>
                                                <td>Number of Refunded Trans.</td>
                                                <td align=right>'.(int)$this->Myreports_model->CountRefundedTransactions($datefrom, $dateto).'</td>
                                            </tr>
                                             <tr>
                                                <td>Number of Refunded Items</td>
                                                <td align=right>'.(int)count($this->detailed_array).'</td>
                                            </tr>
                                            <tr>
                                                <td>Cost Refunded</td>
                                                <td align=right>'.money_format('%.2n', (float)$this->Myreports_model->SumCostRefunded($datefrom, $dateto, 1, $sold_id_array)).'</td>
                                            </tr>
                                            <tr>
                                                <td>Cost Scrapped</td>
                                                <td align=right>'.money_format('%.2n', (float)$this->Myreports_model->SumScrapCostLostRefunded($datefrom, $dateto, 1, null, $sold_id_array)).'</td>
                                            </tr>
                                               <tr>
                                                <td>Defective Loss</td>
                                                <td align=right>'.money_format('%.2n', (float)$this->Myreports_model->SumDefectiveCostLost($datefrom, $dateto, $channel=1, $subchannel=null)).'</td>
                                            </tr>
                                            <tr>
                                                <td>Other Return Expenses</td>
                                                <td align=right>'.money_format('%.2n', (float)$this->Myreports_model->SumOtherReturnExpences($datefrom, $dateto, 1, null,  $sold_id_array=null)).'</td>
                                            </tr>
                                        </table>';
                $this->mysmarty->assign('table_refunds_summary', $this->table_refunds_summary);
        }//if
    }

    function NotPaidPartiallyPaidRefundsSummaryTable($datefrom, $dateto, $sold_id=null)
    {
             $canceled =  (float)$this->Myreports_model->SumCanceledRefundsEbay($datefrom, $dateto, $sold_id=null);
             $partiallyPaid = (float)$this->Myreports_model->SumPartiallyPaidPartiallyRefundedEbay($datefrom, $dateto, $sold_id=null);


             $table = '<table border="0" style="color:blue" ; font-size:"8px";>
                           <tr>
                            <th colspan="2">Refunds Summary</th>
                           </tr>
                           <tr>
                            <td>Not Paid (Canceled)</td>
                            <td>'.money_format('%.2n', $canceled);
            if($canceled>0) $table.= ' not included in refunded amount for La-Tronics. eBay includes this transactions in refunded.';

                       $table.= '</td>
                           </tr>
                           <tr>
                            <td>Partially Paid, Partially Refunded</td>
                            <td>'.money_format('%.2n', $partiallyPaid);

            if($partiallyPaid>0) $table.= ' included in refunded amount La-Tronics. eBay exclude this transactions from refunded.';  
                        
                       $table.= '</td>
                           </tr>
                       </table>';


             $this->mysmarty->assign('table_NotPaidPartiallyPaid', $table);
     }


    function CountSoldAgain()
    {
        $counter = 0; 

        if(count($this->detailed_array)>0)
		{
			
            $k=0;
			foreach ($this->detailed_array as $row)
			{
                 if((int)$this->detailed_array[$k][10] > 0)
                 {  
                    $counter++;
                 }
                 $k++;
            }
         }

         return $counter;

    }
  
    function GroupByReturnReason($datefrom, $dateto, $sold_id_array=null)
	{
         
         //printcool($sold_id_array);
         if(isset($sold_id_array))
         {
                    $this->db->select('* from
                                    (select coalesce(returnreason, "No Reason") as returnreason, count(*) as NumReturns, SUM(ebayRefundAmount) as RefundAmount ')
						->from('ebay_transactions')
                        ->where('refunded',1)
                    	->where_in('et_id', $sold_id_array)
				        ->group_by('returnreason) a')
                        ->order_by('a.NumReturns', "desc");  
                    
                    $query = $this->db->get();
         }  
         else
         {
            $query = $this->db->query('select * from
            (select coalesce(returnreason, "No Reason") as returnreason, count(*) as NumReturns, SUM(ebayRefundAmount) as RefundAmount 
            from ebay_transactions
            where refunded=1 and mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'
            Group by returnreason) a
            Order BY a.NumReturns DESC'); 
         }

         if($query->num_rows()>0)
         {
             $table_groupby_return_reason = "<table border='0' style='color:blue'; font-size:'16px';>
                                                        <tr>
													    <th>Reason</th>
    												    <th>Number of<br>returns</th>
                                                        <th>Refunded<br>Amount</th>
                                                        </tr>";
             foreach ($query->result() as $row)
             {
               

                     $table_groupby_return_reason.= '<tr><td>'.$row->returnreason.'</td>
                                                           <td>'.$row->NumReturns.'</td>     
                                                           <td>'.money_format('%(#10n', (float)$row->RefundAmount).'</td>    
                                                           </tr>';
             }
             // echo "<p>".$this->db->last_query();
             $table_groupby_return_reason.='</table>';
             return $table_groupby_return_reason;
            
          }//if
          else
          {
             $table_groupby_return_reason.='<p>No data!';
             return $table_groupby_return_reason;
          }
    }

    function GroupByStoreCategory($datefrom, $dateto, $sold_id_array=null, $limitRows=1000)
	{
         if(isset($sold_id_array))
         {
                    $this->db->select('* from
                                   (select coalesce(storeCatTitle, "No category") as Category, count(*) as NumReturns, SUM(ebayRefundAmount) as RefundAmount')
						->from('ebay_transactions')
                        ->join('ebay', 'ebay_transactions.e_id=ebay.e_id', 'left')
						->where('refunded',1)
                    	->where_in('et_id', $sold_id_array)
				        ->group_by('Category) a')
                        ->order_by('a.NumReturns', "desc")  
                        ->limit((int)$limitRows);
                        
                  //  $query = $this->db->get();
         }  
         else
         {
                   $query = $this->db->query('select * from
                       (select coalesce(storeCatTitle, "No category")  as Category, count(*) as NumReturns, SUM(ebayRefundAmount) as RefundAmount from ebay_transactions left join ebay on ebay_transactions.e_id=ebay.e_id
                        where refunded=1 and mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'
                        Group by Category) a
                        Order BY a.NumReturns DESC
                        Limit '.(int)$limitRows); 

                  
         }
         //echo "<p>".$this->db->last_query();
         if($query->num_rows()>0)
         {
             $table_groupby_return_category = "<table border='0' style = 'float:left';'color:blue'; font-size:'16px'; >
                                                        <tr>
													    <th>Store Category</th>
    												    <th>Number of<br>returns</th>
                                                        <th>Refunded<br>Amount</th>
                                                        </tr>";
             foreach ($query->result() as $row)
             {
               

                     $table_groupby_return_category.= '<tr><td>'.$row->Category.'</td>
                                                           <td>'.$row->NumReturns.'</td>     
                                                           <td>'.money_format('%(#10n', (float)$row->RefundAmount).'</td>    
                                                           </tr>';
             }

             $table_groupby_return_category.='</table>';
             return $table_groupby_return_category;
          }//if
          else
          {
             $table_groupby_return_category.='<p>No data!';
             return $table_groupby_return_category;
          }
    }

    function FillChartData($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
	{
        $const_datefrom = $datefrom;
        $const_dateto = $dateto;


        $dateto = $dateto - 1;


        $numDays = abs($datefrom - $dateto)/60/60/24;
        $numDays = (int)($numDays+1);

        $numMonths = (abs($datefrom - $dateto)/60/60/24)/30;
        $numMonths = (int)$numMonths+2;

      // echo "<p>Days = ".$numDays;

        if($numDays<=31) //daily period
        {
            

            $x_categories = Array();
            $y_revenue = Array();
            $y_net_profit = Array();
            $y_avg_refunds = Array();

            for ($i = 0; $i < $numDays; $i++) 
            {
                $x_categories[] = date('m/j/Y', $datefrom);

                $dateto = strtotime("+1 day", $datefrom-1); //23:59:59
				
                $y_revenue[] = (int)$this->Myreports_model->SumRefundedAmount($datefrom, $dateto, $channel=1, $subchannel, null);
                $y_net_profit[] = (int)$this->Myreports_model->SumLostShippingExpences($datefrom, $dateto, $channel=1, $subchannel, null);  
                //$y_avg_refunds[] = (array_sum($y_revenue) / count($y_revenue)); 

                //echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
                $datefrom = $dateto+1; //00:00:00
              
            }

        }
        else //monthly period
        {
            $x_categories = Array();
            $y_revenue = Array();
            $y_net_profit = Array();
            $y_refunds = Array();
            $y_avg_refunds = Array();

            for ($i = 1; $i < $numMonths; $i++) 
            {
                if(date("d", $datefrom)==1)
                {
                    $x_categories[] = date('M', $datefrom);
                    $dateto = strtotime("first day of next month", $datefrom);//23:59:59
     
                    if($dateto > $const_dateto) $dateto = $const_dateto;

                    $y_revenue[] = (int)$this->Myreports_model->SumRefundedAmount($datefrom, $dateto, $channel=1, $subchannel, null);
                    $y_net_profit[] = (int)$this->Myreports_model->SumLostShippingExpences($datefrom, $dateto, $channel=1, $subchannel, null); 
                    //$y_avg_refunds[] = (array_sum($y_revenue) / count($y_revenue)); 
                    //echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
     
                     

                    $datefrom = $dateto+1; //00:00:00
                    if($dateto == $const_dateto) break;

                }
                else
                {
                    $x_categories[] = date('m/j/Y', $datefrom);
                    $dateto = strtotime("+1 month", $datefrom -1);//23:59:59
				
                    if($dateto > $const_dateto) $dateto = $const_dateto;

                    $y_revenue[] = (int)$this->Myreports_model->SumRefundedAmount($datefrom, $dateto, $channel=1, $subchannel, null);
                    $y_net_profit[] = (int)$this->Myreports_model->SumLostShippingExpences($datefrom, $dateto, $channel=1, $subchannel, null); 
                    //$y_avg_refunds[] = (array_sum($y_revenue) / count($y_revenue)); 

                  

                    //echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
                    $datefrom = $dateto+1; //00:00:00

                    if($dateto == $const_dateto) break;

                }
            }

        } 
        
        $this->mysmarty->assign('x_categories', json_encode($x_categories));
        $this->mysmarty->assign('y_revenue', json_encode($y_revenue));
        $this->mysmarty->assign('y_net_profit', json_encode($y_net_profit));
        //$this->mysmarty->assign('y_avg_refunds', json_encode($y_avg_refunds));
       
    }
    // -------------- NOT IN USE YET ---------------------------------------------------
    function EbayRefundStyleTable($datefrom, $dateto, $sold_id=null)
    {
       //I take sold_id from detailed_array in order to provide sold_id of fully refunded to all the functions below.
       //If I call the functions with just period parameters they will include partially refunded transactions.
        if(count($this->detailed_array)>0)
        {
        

           //printcool($sold_id_array);
        

            $able_refunds_summary = '<table border="0" style="color:blue" ; font-size:"8px";>
                                              <tr>
                                                <td>eBay Way Refunded Amount</td>
                                                <td align=right style="color:red"><b>'.money_format('%.2n', $this->ebay_stayle_refunds).'</b></td>
                                              </tr>
                                            
                                              <tr>
                                                <td>Note:</td>
                                                <td align=right>This sum EXcludes transactions which are Partially Paid and there are refunds in them but and includes all cancelled, not paid sums of the transactions.</td>
                                            </tr>
                                        </table>';
                $this->mysmarty->assign('table_refunds_summary', $this->table_refunds_summary);
        }//if
    }      
}