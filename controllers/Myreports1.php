<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myreports1 extends Controller {

	//All Items
								  
	private $sales=0.0;                          
	private $shipping=0.0;   
    private $ssc_profit=0.0;   
               
	private $fees=0.0; 
    private $pay_pal_fees=0.0;                           
	private $total_profit=0.0;                   
	private $inventory_cost=0.0;                 
	private $new_inventory_cost=0.0;             
	private $net_profit=0.0;                     
											 
	//All Items - Status                         
											 
	private $status_listed=0.0;                  
	private $status_sold=0.0;                    
	private $status_not_tested=0.0;              
	private $status_ready_to_sell=0.0;           
	private $status_returned=0.0;                
	private $status_scrapped=0.0;                
	private $status_with_location=0.0;           
	private $status_no_location=0.0;             
	private $status_with_sn=0.0;                 
	private $status_no_sn=0.0;                   
											 
	//Sold Only                                  
	private $sold_only_sales=0.0;                
	private $sold_only_shippnig=0.0;             
	private $sold_only_fees=0.0;                 
	private $sold_only_total_profit=0.0;         
	private $sold_only_inventory_cost=0.0;       
	private $sold_only_singleBCN_partial_ref=0.0;
	private $sold_only_multiBCN_partial_ref=0.0; 
											 
	//Returns                                    
	private $returns_total_returns=0.0;          
	private $ssc_refunded=0.0;     
	private $returns_lost_shipping_expences=0.0; 
	private $returns_other_returns_expences=0.0; 
	private $returns_scrap_cost_loss=0.0;        
	private $returns_defective_loss=0.0;         
	private $returns_recouped_revenue=0.0; 
    private $returns_cost=0.0;       
	
   
	function Myreports1()
	{
		parent::Controller();

		$this->load->model('Myreports_model');
		$this->load->model('Auth_model');
        $this->load->helper('lazy_helper');

		$this->Auth_model->VerifyAdmin();
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('action',$this->session->flashdata('action'));
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Reports');
        $this->mysmarty->assign('hicharts', TRUE);
		

		$this->mysmarty->assign('newlayout', TRUE);
		$this->mysmarty->assign('jslog', TRUE);

		$calendar = $this->mysmarty->assign('cal', TRUE);
		$current_date =  date('m/j/Y');
		//$this->mysmarty->assign('ofrom', $dfrom);	
		//$this->mysmarty->assign('oto', $dto);
	   

	}

	function index()
	{
		
		$this->Main();

		/*

		Calendar date post to timestamp:
		'.$dateto.' = explode('/', '.$datefrom.'calender);
		'.$dateto.' = mktime(0, 0, 0, '.$dateto.'[0], '.$dateto.'[1], '.$dateto.'[2])

		
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

				Refunded:
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
					$this->db->where('paidtime !=', ''); NIAMA TAKAVA
					$this->db->where('mark', 0);
					$this->db->where('mkdt <= ', '.$datefrom.'); ZA submittime li stava duma
					$this->db->where('mkdt >= ', '.$dateto.');ZA submittime li stava duma
					$this->db->where('notpaid', 0); NIAMA TAKAVA
					$this->db->where("refunded", 0); NIAMA TAKAVA
					$this->db->where('pendingpay', 0); NIAMA TAKAVA

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
	   // echo '<p>Duration - '.$_POST['duration'];
		$this->mysmarty->view('myreports/myreports_report1.html');
	}

    function Main() //Monthly data is not equal to Five weeks before because not all month have 4 week, some have more but we have only four rows there
	{
		 print_r($_POST);
		//echo '<p style="color:blue">Test '.date('F j, Y, g:i a',1482688673);

		if (isset($_POST['OneMonth']))
		 {
			$datefrom=mktime(0, 0, 0, date('m')-1, date('d'), date('Y'));
			$dateto=mktime(23, 59, 59, date('m'), date('d'), date('Y'));

		 }
		 elseif(isset($_POST['TwoMonth']))
		 {
			$datefrom=mktime(0, 0, 0, date('m')-2, date('d'), date('Y'));
			$dateto=mktime(23, 59, 59, date('m'), date('d'), date('Y'));

		 }
		 elseif(isset($_POST['ThreeMonth']))
		 {
			$datefrom=mktime(0, 0, 0, date('m')-3, date('d'), date('Y'));
			$dateto=mktime(23, 59, 59, date('m'), date('d'), date('Y'));

		 }
		 elseif(isset($_POST['SixMonth']))
		 {
			
			$datefrom=mktime(0, 0, 0, date('m')-6, date('d'), date('Y'));
			$dateto=mktime(23, 59, 59, date('m'), date('d'), date('Y'));

		 }
		  elseif(isset($_POST['OneYear']))
		 {
			
			$datefrom=mktime(0, 0, 0, date('m'), date('d'), date('Y')-1);
			$dateto=mktime(23, 59, 59, date('m'), date('d'), date('Y'));

		 }
		 elseif (isset($_POST['oto']) && isset($_POST['ofrom']))
		 {
			$datefrom = explode('/', $this->input->post('ofrom', TRUE));   
			$datefrom = mktime(0, 0, 0, $datefrom[0], $datefrom[1], $datefrom[2]);

			$dateto = explode('/', $this->input->post('oto', TRUE));   
			$dateto = mktime(23, 59, 59, $dateto[0], $dateto[1], $dateto[2]);
		}
		else
		{
			$datefrom=mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
			$dateto=mktime(23, 59, 59, date('m'), date('d'), date('Y'));
		  
		}

		$this->mysmarty->assign('ofrom', date('m/j/Y', $datefrom));
		$this->mysmarty->assign('oto', date('m/j/Y', $dateto));
		

		
		//echo '<p style="color:red">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
		
		//We add 1 hour to the filter in order to have equal date frame with eBay. The reason for that is not clear!
         //$datefrom=$datefrom+(60*60);
         //$dateto=$dateto+(60*60);

	   
		 //echo '<p>'.date('m/j/Y   H:i:s');
		 //echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
		 //echo '<p style="color:blue">From '.$datefrom.' to '.$dateto;
		
		  //All Items

		//1. Sales = Revenue                 
		if($_POST['ebay'])
		{
			//When is entered eBay transaction Id we calculate here one transaction data
             if($_POST['soldid'])
             { 
                
               	$this->sales = $this->Myreports_model->SumSales($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id=(int)$_POST['soldid']);
                $this->shipping = $this->Myreports_model->SumActualShipping($datefrom, $dateto, $selector=1,(int)$_POST['soldid']);  
                $this->fees = $this->Myreports_model->SumFees($datefrom, $dateto, $selector=1,(int)$_POST['soldid'], $refunded=null);
                $this->ssc =  $this->Myreports_model->SumSSC($datefrom, $dateto, (int)$_POST['soldid'], $sold_id_array=null, $refunded=null); 

                

                $this->pay_pal_fees = $this->SumPayPalFees($datefrom, $dateto, $channel=1, $subchannel=null, (int)$_POST['soldid']);
                
                
                $this->inventory_cost = $this->Myreports_model->SumCostWithoutRefunded($datefrom, $dateto, $channel=1, (int)$_POST['soldid']);

                //Calculate Net profit if we have cost of product entered
                if($this->inventory_cost > 0)
                {
                    $this->net_profit = $this->Myreports_model->SumNetProfit($datefrom, $dateto, $channel=1, (int)$_POST['soldid']);
                    $this->sold_only_total_profit = $this->SumTotalProfit($datefrom, $dateto, $selector=1, (int)$_POST['soldid']);
                }
                else
                {
                    $this->net_profit = 0;
                    $this->sold_only_total_profit = 0;
                }

                $this->returns_total_returns = $this->SumRefundedAmount($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id=(int)$_POST['soldid']);
                $this->returns_other_returns_expences+=$this->SumOtherReturnExpences($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id=(int)$_POST['soldid']);
                
                $this->returns_recouped_revenue = (float)$this->Myreports_model->SumReturnsRecoupedRevenue($datefrom, $dateto, $channel=1, (int)$_POST['soldid']);
                $this->returns_lost_shipping_expences = $this->SumLostShippingExpences($datefrom, $dateto, $channel=1, $subchannel=null, (int)$_POST['soldid']); 

                if($this->IsRefunded($_POST['soldid']) and $this->returns_total_returns==0)
                {
                    $message = '<font color="red">The transaction is refunded but there is no refund amount entered!';

                    $may_be_refunded = $this->SumSalesTransactionDetails($datefrom, $dateto, 1, $subchannel=null, $_POST['soldid']);

                    if($may_be_refunded>0)
                    {
                        $message .= ' May be the refunded amount is like transaction amount $'.$may_be_refunded; 
                    }

                    $message .= '</font>';
                    $this->mysmarty->assign('message', $message);

                    //For one transaction info especially refunded ones we will try to find revenue information from another table
                    //because it is deleted in ebay_transactions.
                    if($this->sales == $this->ssc) $this->sales += $may_be_refunded;

                }            
             }
             else
             {
			    $this->sales = $this->Myreports_model->SumSales($datefrom, $dateto, $channel=1);

         

                $this->shipping = $this->Myreports_model->SumActualShipping($datefrom, $dateto, $selector=1, $sold_id=null, $refunded = 0); 
                $this->fees = $this->Myreports_model->SumFees($datefrom, $dateto, $selector=1, $sold_id=null, $refunded = 0); 
                $this->ssc =  $this->Myreports_model->SumSSC($datefrom, $dateto, $sold_id_array=null, $refunded=0); 
                $this->pay_pal_fees += $this->SumPayPalFees($datefrom, $dateto, $channel=1, $subchannel=null);
                $this->net_profit = $this->Myreports_model->SumNetProfit($datefrom, $dateto, $channel=1, null, null);
                $this->sold_only_total_profit = $this->SumTotalProfit($datefrom, $dateto, $selector=1);
                $this->inventory_cost = $this->Myreports_model->SumCostWithoutRefunded($datefrom, $dateto, $channel=1); 
                $this->returns_total_returns += $this->SumRefundedAmount($datefrom, $dateto, $channel=1);
                $this->ssc_refunded = $this->Myreports_model->SumSSC($datefrom, $dateto, $sold_id_array=null, $refunded=1);
                $this->returns_other_returns_expences+=$this->SumOtherReturnExpences($datefrom, $dateto, $channel=1, $subchannel=null);
                $this->returns_recouped_revenue = (float)$this->Myreports_model->SumReturnsRecoupedRevenue($datefrom, $dateto, $channel=1, null);

                $this->sold_only_singleBCN_partial_ref+=$this->SumSingleBCNPartialRefund($datefrom, $dateto, 1, $subchannel=null);
        	    $this->sold_only_multiBCN_partial_ref+=$this->SumMultiBCNPartialRefund($datefrom, $dateto, $channel=1, $subchannel=null); 

               // $query = $this->GroupByStatusOfReturned($datefrom, $dateto, $channel=1);

                $this->GroupByStatusOfReturned($datefrom, $dateto);
                //foreach ($query->result_array() as $row)
                //{
                //    echo "<p>Status ->".$row['status']. 'Numbers->'.$row['numbers'];

                //    if($row['status']=='Listed') $this->returns_statuses_listed+=(int)$row['numbers'];
                //    if($row['status']=='Sold') $this->returns_status_sold+=(int)$row['numbers'];
                //    if($row['status']=='Ready to Sell') $this->returns_status_ready_to_sell+=(int)$row['numbers'];
                //    if($row['status']=='Not tested') $this->returns_status_not_tested+=(int)$row['numbers'];                 
                //    if($row['status']=='Returned') $this->returns_status_returned+=(int)$row['numbers'];          
                //    if($row['status']=='Scrap') $this->returns_status_scraped+=(int)$row['numbers'];     
                //}

                $this->returns_cost = (float)$this->Myreports_model->SumCostRefunded($datefrom, $dateto, 1, $sold_id_array);
                $this->returns_lost_shipping_expences = $this->SumLostShippingExpences($datefrom, $dateto, $channel=1, $subchannel=null);

                $this->returns_scrap_cost_loss = $this->SumScrapCostLostRefunded($datefrom, $dateto, $channel=1, $subchannel=null);
			    $this->returns_defective_loss = $this->Myreports_model->SumDefectiveCostLost($datefrom, $dateto, $channel=1, $subchannel=null);

              

               
             }

             //echo "<p>POST[sold_id] ".$_POST['soldid'];
			 
             $this->FillChartData($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id=$_POST['soldid']); 

			 $this->sold_only_sales=$this->sales;
			 $this->sold_only_shippnig = $this->shipping;
			 $this->sold_only_fees = $this->fees;
             $this->total_profit=$this->sold_only_total_profit;
			 $this->sold_only_inventory_cost = $this->inventory_cost;
             $this->returns_revenue_to_returns = $this->returns_total_returns;
 		
		}
		
		if($_POST['latrweb'])
		{
			$this->sales += $this->Myreports_model->SumSales($datefrom, $dateto, $channel=2, null, null); 
			$this->shipping+= $this->Myreports_model->SumActualShipping($datefrom, $dateto, 2, $sold_id=null, $refunded=null);
			$this->fees+= $this->Myreports_model->SumFees($datefrom, $dateto, 2, $sold_id=null, $refunded=null);
			$this->net_profit+= $this->Myreports_model->SumNetProfit($datefrom, $dateto, 2, null, null); 
			
			$this->sold_only_inventory_cost+= $this->Myreports_model->SumCostWithoutRefunded($datefrom, $dateto, 2); 

            //echo '<p>Cost = '.$this->SumCostWithoutRefunded($datefrom, $dateto, 2);

			$this->sold_only_sales+=$this->Myreports_model->SumSales($datefrom, $dateto, 2);//same as sales $this->sales

			//echo '<p>Sales for Sold'.$this->Myreports_model->SumSales($datefrom, $dateto, 2);

			$this->sold_only_shippnig += $this->shipping;
			$this->sold_only_fees += $this->fees;
            $this->pay_pal_fees += $this->SumPayPalFees($datefrom, $dateto, $channel=2, $subchannel=null);
            $this->sold_only_total_profit += $this->SumTotalProfit($datefrom, $dateto, $selector=2);
            $this->total_profit=$this->sold_only_total_profit;
			$this->sold_only_inventory_cost += $this->inventory_cost;

            //$this->sold_only_singleBCN_partial_ref+=$this->SumSingleBCNPartialRefund($datefrom, $dateto, $channel=2, $subchannel=null);
            //$this->sold_only_multiBCN_partial_ref+=$this->SumMultiBCNPartialRefund($datefrom, $dateto, $channel=2, $subchannel=null);

			$this->returns_total_returns += $this->SumRefundedAmount($datefrom, $dateto, $channel=2);
			$this->returns_revenue_to_returns += $this->SumRevenueLostToReturns($datefrom, $dateto, $channel=2, $subchannel=null);
            $this->returns_lost_shipping_expences += $this->SumLostShippingExpences($datefrom, $dateto, $channel=2, $subchannel=null);
            $this->returns_other_returns_expences+=$this->SumOtherReturnExpences($datefrom, $dateto, $channel=2, $subchannel=null);
			
            //$query = $this->GroupByStatusOfReturned($datefrom, $dateto, $channel=2, $subchannel=null);
            //foreach ($query->result_array() as $row)
            //{
            //    if($row['status']=='Listed') $this->returns_statuses_listed+=(int)$row['numbers'];
            //    if($row['status']=='Sold') $this->returns_status_sold+=(int)$row['numbers'];
            //    if($row['status']=='Ready to Sell') $this->returns_status_ready_to_sell+=(int)$row['numbers'];
            //    if($row['status']=='Not tested') $this->returns_status_not_tested+=(int)$row['numbers'];                 
            //    if($row['status']=='Returned') $this->returns_status_returned+=(int)$row['numbers'];          
            //    if($row['status']=='Scrap') $this->returns_status_scraped+=(int)$row['numbers'];     
            //}

			$this->returns_scrap_cost_loss+=$this->SumScrapCostLostRefunded($datefrom, $dateto, $channel=2, $subchannel=null);
			$this->returns_defective_loss+=$this->Myreports_model->SumDefectiveCostLost($datefrom, $dateto, $channel=2, $subchannel=null);
		}  

		if($_POST['warehouse'])
		{
			

			$this->sales += $this->Myreports_model->SumSales($datefrom, $dateto, $channel=4, $subchannel=0); 
			$this->shipping+= $this->Myreports_model->SumActualShipping($datefrom, $dateto, 3);
			$this->fees+= $this->Myreports_model->SumFees($datefrom, $dateto, 3, $sold_id=null, $refunded=null);
			$this->net_profit+= $this->Myreports_model->SumNetProfit($datefrom, $dateto, $channel=4, $subchannel=0, null);  
			$this->inventory_cost+= $this->Myreports_model->SumCostWithoutRefunded($datefrom, $dateto, 3);
			$this->sold_only_sales+=$this->Myreports_model->SumSales($datefrom, $dateto, $channel=4, $subchannel=0);
			
			$this->sold_only_shippnig += $this->shipping;
			$this->sold_only_fees += $this->fees;
            $this->pay_pal_fees += $this->SumPayPalFees($datefrom, $dateto, $channel=4, $subchannel=null);
			$this->sold_only_total_profit += $this->SumTotalProfit($datefrom, $dateto, $selector=3);
            $this->total_profit=$this->sold_only_total_profit;
			$this->sold_only_inventory_cost += $this->inventory_cost;

            //$this->sold_only_singleBCN_partial_ref+=$this->SumSingleBCNPartialRefund($datefrom, $dateto, $channel=4, $subchannel=0);
            //$this->sold_only_multiBCN_partial_ref+=$this->SumMultiBCNPartialRefund($datefrom, $dateto, $channel=4, $subchannel=0);

            //echo '<p>'.$this->returns_total_returns;

			$this->returns_total_returns += $this->SumRefundedAmount($datefrom, $dateto, $channel=4,$subchannel=0);

             //echo '<p>'.$this->returns_total_returns;

			$this->returns_revenue_to_returns += $this->SumRevenueLostToReturns($datefrom, $dateto, $channel=4, $subchannel=0);
            $this->returns_lost_shipping_expences += $this->SumLostShippingExpences($datefrom, $dateto, $channel=4, $subchannel=0);
            $this->returns_other_returns_expences+=$this->SumOtherReturnExpences($datefrom, $dateto, $channel=4, $subchannel=0);

            //$query = $this->GroupByStatusOfReturned($datefrom, $dateto, $channel=2, $subchannel=0);

            //foreach ($query->result_array() as $row)
            //{
            //    if($row['status']=='Listed') $this->returns_statuses_listed+=(int)$row['numbers'];
            //    if($row['status']=='Sold') $this->returns_status_sold+=(int)$row['numbers'];
            //    if($row['status']=='Ready to Sell') $this->returns_status_ready_to_sell+=(int)$row['numbers'];
            //    if($row['status']=='Not tested') $this->returns_status_not_tested+=(int)$row['numbers'];                 
            //    if($row['status']=='Returned') $this->returns_status_returned+=(int)$row['numbers'];          
            //    if($row['status']=='Scrap') $this->returns_status_scraped+=(int)$row['numbers'];     
            //}

			$this->returns_scrap_cost_loss+=$this->SumScrapCostLostRefunded($datefrom, $dateto, $channel=4, $subchannel=0);
			$this->returns_defective_loss+=$this->Myreports_model->SumDefectiveCostLost($datefrom, $dateto, $channel=4, $subchannel=0);
		}

		if($_POST['365web'])
		{
			

			$this->sales += $this->Myreports_model->SumSales($datefrom, $dateto, $channel=4, $subchanel=1); 
			$this->shipping+= $this->Myreports_model->SumActualShipping($datefrom, $dateto, 4);
            $this->fees+= $this->Myreports_model->SumFees($datefrom, $dateto, 4, $sold_id=null, $refunded=null);
		    $this->net_profit+= $this->Myreports_model->SumNetProfit($datefrom, $dateto, 4); 
			$this->inventory_cost+= $this->Myreports_model->SumCostWithoutRefunded($datefrom, $dateto, 4); 
			$this->sold_only_sales+=$this->Myreports_model->SumSales($datefrom, $dateto, $channel=4, $subchanel=1);

			$this->sold_only_shippnig += $this->shipping;
			$this->sold_only_fees += $this->fees;
            $this->pay_pal_fees += $this->SumPayPalFees($datefrom, $dateto, $channel=4, $subchannel=8);
			$this->sold_only_total_profit += $this->SumTotalProfit($datefrom, $dateto, $selector=4);
            $this->total_profit=$this->sold_only_total_profit;
			$this->sold_only_inventory_cost += $this->inventory_cost;

            //$this->sold_only_singleBCN_partial_ref+=$this->SumSingleBCNPartialRefund($datefrom, $dateto, $channel=4, $subchannel=8);
            //$this->sold_only_multiBCN_partial_ref+=$this->SumMultiBCNPartialRefund($datefrom, $dateto, $channel=4, $subchannel=8);

			$this->returns_total_returns += $this->SumRefundedAmount($datefrom, $dateto, $channel=4,$subchannel=8);
			$this->returns_revenue_to_returns += $this->SumRevenueLostToReturns($datefrom, $dateto, $channel=4, $subchannel=8);
            $this->returns_lost_shipping_expences += $this->SumLostShippingExpences($datefrom, $dateto, $channel=4, $subchannel=8);
            $this->returns_other_returns_expences+=$this->SumOtherReturnExpences($datefrom, $dateto, $channel=4, $subchannel=8);

            //$query = $this->GroupByStatusOfReturned($datefrom, $dateto, $channel=4, $subchannel=8);
			

            //foreach ($query->result_array() as $row)
            //{
            //    if($row['status']=='Listed') $this->returns_statuses_listed+=(int)$row['numbers'];
            //    if($row['status']=='Sold') $this->returns_status_sold+=(int)$row['numbers'];
            //    if($row['status']=='Ready to Sell') $this->returns_status_ready_to_sell+=(int)$row['numbers'];
            //    if($row['status']=='Not tested') $this->returns_status_not_tested+=(int)$row['numbers'];                 
            //    if($row['status']=='Returned') $this->returns_status_returned+=(int)$row['numbers'];          
            //    if($row['status']=='Scrap') $this->returns_status_scraped+=(int)$row['numbers'];     
            //}

			$this->returns_scrap_cost_loss+=$this->SumScrapCostLostRefunded($datefrom, $dateto, $channel=4, $subchannel=8);
			$this->returns_defective_loss+=$this->Myreports_model->SumDefectiveCostLost($datefrom, $dateto, $channel=4, $subchannel=8);
		}  

        if(!isset($_POST['soldid']) or $_POST['soldid']=="")
        {
		    $query = $this->GroupByStatus();
		    $query=$this->db->query('select status, count(*) as numbers from warehouse group by status');
		    foreach ($query->result_array() as $row)
		    {
			    if($row['status']=='Listed') $this->status_listed=(int)$row['numbers'];
			    if($row['status']=='Sold') $this->status_sold=(int)$row['numbers'];
			    if($row['status']=='Ready to Sell') $this->status_ready_to_sell=(int)$row['numbers'];
			    if($row['status']=='Not tested') $this->status_not_tested=(int)$row['numbers'];                 
			    if($row['status']=='Returned') $this->status_returned=(int)$row['numbers'];          
			    if($row['status']=='Scrap') $this->status_scrapped=(int)$row['numbers'];     
		    }

            $this->status_with_location=$this->CountWithOrWithoutLocation(1);      
		    $this->status_no_location=$this->CountWithOrWithoutLocation(0);               
		    $this->status_with_sn=$this->CountWithOrWithoutSN($withSN=1);                
		    $this->status_no_sn=$this->CountWithOrWithoutSN($withSN=0); 
		    $this->new_inventory_cost=$this->SumProductsBought($datefrom, $dateto);
        }

		setlocale(LC_MONETARY, 'en_US');
	   
		//echo '<p>'.money_format('%(#10n', (float)$this->sales);  
		//echo '<p style="color:blue">From '.date('m/j/Y  H:i:s',$datefrom).' to '.date('m/j/Y  H:i:s',$dateto).'&#8595;';

		$this->mysmarty->assign('sales', money_format('%.2n', (float)$this->sales), true);

        $this->mysmarty->assign('sales_for_chart', (int)$this->sales);

		$this->mysmarty->assign('shipping', money_format('%.2n', (float)$this->shipping), true);
        $this->mysmarty->assign('ssc_profit', money_format('%.2n', (float)$this->ssc_profit), true);
        $this->mysmarty->assign('ssc', money_format('%.2n', (float)$this->ssc), true);
		$this->mysmarty->assign('fees', money_format('%.2n', (float)$this->fees), true);
        $this->mysmarty->assign('pay_pal_fees', money_format('%.2n', (float)$this->pay_pal_fees), true);
		$this->mysmarty->assign('net_profit', money_format('%.2n', (float)$this->net_profit), true);

        $this->mysmarty->assign('net_profit_for_chart', (int)$this->net_profit);



    	$this->mysmarty->assign('total_profit', money_format('%.2n', (float)$this->total_profit), true);
	
		$this->mysmarty->assign('inventory_cost', money_format('%.2n', (float)$this->inventory_cost), true);

		$this->mysmarty->assign('status_listed', number_format($this->status_listed), true);
		$this->mysmarty->assign('status_sold', number_format($this->status_sold), true);
		$this->mysmarty->assign('status_ready_to_sell', number_format($this->status_ready_to_sell), true);
		$this->mysmarty->assign('status_not_tested', number_format($this->status_not_tested), true);
		$this->mysmarty->assign('status_returned', number_format($this->status_returned), true);
		$this->mysmarty->assign('status_scrapped', number_format($this->status_scrapped), true);

		$this->mysmarty->assign('status_with_location', number_format($this->status_with_location), true);
		$this->mysmarty->assign('status_no_location', number_format($this->status_no_location), true);
		$this->mysmarty->assign('status_with_sn', number_format($this->status_with_sn), true);
		$this->mysmarty->assign('status_no_sn', number_format($this->status_no_sn), true);

		$this->mysmarty->assign('sold_only_sales', money_format('%.2n', (float)$this->sold_only_sales), true);
		$this->mysmarty->assign('sold_only_shippnig', money_format('%.2n', (float)$this->sold_only_shippnig), true);
		$this->mysmarty->assign('sold_only_fees', money_format('%.2n', (float)$this->sold_only_fees), true);
		$this->mysmarty->assign('sold_only_total_profit', money_format('%.2n', (float)$this->sold_only_total_profit), true);
		$this->mysmarty->assign('sold_only_inventory_cost', money_format('%.2n', (float)$this->sold_only_inventory_cost), true);
		$this->mysmarty->assign('new_inventory_cost', money_format('%.2n', (float)$this->new_inventory_cost), true);
		
		$this->mysmarty->assign('sold_only_singleBCN_partial_ref', money_format('%.2n', (float)$this->sold_only_singleBCN_partial_ref), true);
		$this->mysmarty->assign('sold_only_multiBCN_partial_ref', money_format('%.2n', (float)$this->sold_only_multiBCN_partial_ref), true);
		
		$this->mysmarty->assign('returns_total_returns', money_format('%.2n', (float)$this->returns_total_returns), true);
        $this->mysmarty->assign('returns_total_returns_for_chart', (int)$this->returns_total_returns);

		$this->mysmarty->assign('ssc_refunded', money_format('%.2n',(float)$this->ssc_refunded), true);
        $this->mysmarty->assign('returns_cost', money_format('%.2n',(float)$this->returns_cost), true);
		$this->mysmarty->assign('returns_lost_shipping_expences', money_format('%.2n', (float)$this->returns_lost_shipping_expences), true);
		$this->mysmarty->assign('returns_other_returns_expences', money_format('%.2n', (float)$this->returns_other_returns_expences), true);
		$this->mysmarty->assign('returns_scrap_cost_loss', money_format('%.2n', (float)$this->returns_scrap_cost_loss), true);
		$this->mysmarty->assign('returns_defective_loss', money_format('%.2n', (float)$this->returns_defective_loss), true);
		$this->mysmarty->assign('returns_recouped_revenue', money_format('%.2n', (float)$this->returns_recouped_revenue), true);

		$this->mysmarty->assign('returns_statuses_listed', number_format($this->returns_statuses_listed), true);
		$this->mysmarty->assign('returns_status_sold', number_format($this->returns_status_sold), true);
		$this->mysmarty->assign('returns_status_not_tested', number_format($this->returns_status_not_tested), true);
		$this->mysmarty->assign('returns_status_ready_to_sell',number_format($this->returns_status_ready_to_sell), true);
		$this->mysmarty->assign('returns_status_returned', number_format($this->returns_status_returned), true);
		$this->mysmarty->assign('returns_status_scraped', number_format($this->returns_status_scraped), true);
		$this->mysmarty->assign('returns_status_returned',number_format($this->returns_status_returned), true);

        $this->mysmarty->assign('chart_title', 'Sales, net ptofit, returns for '.$ofrom.' to '.$oto);

      
		//1
		//$this->shipping                 
		//$this->fees                     
		//$this->total_profit             
		//$this->inventory_cost           
		//$this->new_inventory_cost       
		//$this->net_profit               
								
		////All Items - Status 
								
		//$this->status_listed            
		//$this->status_sold              
		//$this->status_not_tested        
		//$this->status_ready_to_sell     
		//$this->status_returned          
		//$this->status_scrapped          
		//$this->status_with_location     
		//$this->status_no_location       
		//$this->status_with_sn           
		//$this->status_no_sn             
								
		////Sold Only               
		//$this->sold_only_sales          
		//$this->sold_only_shippnig       
		//$this->sold_only_fees           
		//$this->sold_only_total_profit   
		//$this->sold_only_inventory_cost 
		//$this->sold_only_singleBCN_parti
		//$this->sold_only_multyBCN_partia
								
		////Returns                       
		//$this->returns_total_returns    
		//$this->returns_revenue_to_return
		//$this->returns_lost_shipping_exp
		//$this->returns_other_returns_exp
		//$this->returns_scrap_cost_loss  
		//$this->returns_defective_loss   
		//$this->returns_recouped_revenue  
								
		////Returns - Statuses            
		//$this->returns_statuses_listed  
		//$this->returns_status_sold      
		//$this->returns_status_not_tested
		//$this->returns_status_ready_to_s
		//$this->returns_status_returned  
		//$this->returns_status_scraped   
	}

    function SumTotalProfit($datefrom, $dateto, $selector, $sold_id=null)
	{
		if($selector==1)//eBay
		{
			if(isset($sold_id))
			{
				  $this->db->select('(sum(transaction_details.paid)-sum(cost)) as Profit', FALSE)
									->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 1')
									->where_in('transaction_details.sold_id', $sold_id);
			        $result = $this->db->get();
		            //echo '<p>'.$this->db->last_query();
			        $row = $result->first_row();

			        return (float)$row->Profit;

			}
            else
            {
                   $query = $this->db->query('select (sum(transaction_details.paid)-sum(cost)) as Profit
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 1  AND transaction_details.sold_id IN 
			            (select et_id from ebay_transactions  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'  and notpaid = 0 and ebayRefundAmount<paid)');

			        $result = $query->first_row();

                    //echo $this->db->last_query();           
			        return (float)$result->Profit;
            }
		}

		elseif($selector==2)//LATRWebsiteOrders
		{
			$query = $this->db->query('select (sum(transaction_details.paid)-sum(cost)) as Profit
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 2  AND transaction_details.sold_id IN 
			(select oid from orders  where submittime <= '.$dateto.' and submittime >= '.$datefrom.' and complete<>-1)');

			$result = $query->first_row();

             //echo $this->db->last_query();

			return (float)$result->Profit;
		}
		elseif($selector==3)//Warehouse subchannel=0
		{
            $query = $this->db->query('select (sum(transaction_details.paid)-sum(cost)) as Profit
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 4  AND transaction_details.sold_id IN 
			(select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.' and subchannel=0)');


			$result = $query->first_row();

            //echo $this->db->last_query();

			return (float)$result->Profit;
	

		}
		elseif($selector==4)//365 Web subchannel=1
		{
             $query = $this->db->query('select (sum(transaction_details.paid)-sum(cost)) as Profit
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 4  AND transaction_details.sold_id IN 
			(select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.' and subchannel=1)');

            //echo $this->db->last_query();

			$result = $query->first_row();

			return (float)$result->Profit;

		}

		
	}
    	

	function GroupByStatus()
	{
		$query=$this->db->query('select status, count(*) as numbers from warehouse group by status');
		return query;
	}

	function GroupByStatusOfReturned($datefrom, $dateto)
	{
				 
            // LONG QUERY TO ESKAPE BUG and Give same data as report3 and report2
            //if in transaction with many product we do not have particular product marked as refunded
            //we have to remove the row. We must know exactly which item is refunded. For transaction with no details (no products attached)
            //do not delete row in order to see transaction at least. The isset($row['wid']) prevents it.

            $query = $this->db->query('select a.status, sum(a.numbers) as numbers from (
                    SELECT coalesce(warehouse.status, "No Status") as status, count(coalesce(warehouse.status, "No Status")) as numbers 
                    FROM (ebay_transactions) LEFT JOIN transaction_details ON transaction_details.sold_id=ebay_transactions.et_id 
                    LEFT JOIN warehouse ON transaction_details.w_id=warehouse.wid 
                    WHERE coalesce(transaction_details.channel, 1) = 1 AND ebay_transactions.refunded=1 AND qty=1 AND
                    mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' Group By warehouse.status
                    UNION ALL
                    SELECT coalesce(warehouse.status, "No Status") as status, count(coalesce(warehouse.status, "No Status")) as numbers 
                    FROM (ebay_transactions) LEFT JOIN transaction_details ON transaction_details.sold_id=ebay_transactions.et_id 
                    LEFT JOIN warehouse ON transaction_details.w_id=warehouse.wid 
                    WHERE coalesce(transaction_details.channel, 1) = 1 AND ebay_transactions.refunded=1 AND qty>1 AND
                    (transaction_details.returned_amount > 0 or transaction_details.return_id > 0 or transaction_details.returnID is not null)                    
                    and mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' Group By warehouse.status) a GROUP BY a.status');

			//echo '<p>'.$this->db->last_query();

        /*    $table_groupby_status = '<table border="0" style="color:blue"; font-size:"16px";>';
			
            $counter = 0;
			foreach ($query->result() as $row)
			{
			  $table_groupby_status.= '<tr><td>'.$row->status.'<sup style="color:red;">'.$row->numbers.'</sup></td></tr>';
              $counter+=$row->numbers;
			}

        
            $table_groupby_status.='</table>';

            $this->mysmarty->assign('table_groupby_status', $table_groupby_status); */

           $table_groupby_status = '<ul>';
			
            $counter = 0;
			foreach ($query->result() as $row)
			{
			  $table_groupby_status.= '<li>'.$row->status.'<sup style="color:red;">'.$row->numbers.'</sup></li>';
              $counter+=$row->numbers;
			}

        
            $table_groupby_status.='</ul>';

            $this->mysmarty->assign('table_groupby_status', $table_groupby_status); 
            
   }
	
   function CountWithOrWithoutLocation($withLocation=1)
   {		
		if($withLocation)
		{
			 $query = $this->db->query('select count(*) as Location from warehouse where Location is not null and location<>"" and location<>0');
		}
		else
		{
			 $query = $this->db->query('select count(*)  as Location from warehouse where Location is null or location="" or location=0');  
		}

		$result_row = $query->first_row();
		return (int)$result_row->Location;	
		
	}

	function CountWithOrWithoutSN($withSN=1)
	{	
		if($withSN)
		{
			 $query = $this->db->query('select count(*) as SN from warehouse where sn is not null and sn<>"" and sn<>0');

		}
		else
		{
			 $query = $this->db->query('select count(*)  as SN from warehouse where sn is null or sn="" or sn=0');  
		}

		$result_row = $query->first_row();
		return (int)$result_row->SN;	
	}

	function SumProductsBought($datefrom, $dateto)
	{
		$query = $this->db->query('SELECT sum(warehouse.cost) as Cost
									FROM warehouse  WHERE 
							createddatemk >= '.$datefrom.' and createddatemk <= '.$dateto);


                            	//echo '<p>'.$this->db->last_query();
		$result_row = $query->first_row();
		return (float)$result_row->Cost;
	}

    //It is in model
	function SumRefundedAmount($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
	{
		if($channel==1)
		{
            if(isset($sold_id))
            {
                    $this->db->select('sum(ebayRefundAmount) as Refunded')
						->from('ebay_transactions')
						 ->where_in('et_id', $sold_id);
				  
                    $query = $this->db->get();
             }  
             else
             {
                $query = $this->db->query('select sum(ebayRefundAmount) as Refunded from ebay_transactions
				    where refunded=1 and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
                   
			    //echo '<p>'.$this->db->last_query();
                
             }
            $row  = $query->first_row();
            
		    return (float)$row->Refunded;
		}
        else
        {
            return 0;

        }
		

    /*  FROM TRANSACTION DETAILS  



        if($subchannel!=null)
		{
			$query = $this->db->query('select sum(returned_amount) as Refunded from transaction_details
				where (refunded = 1 or returnID is not null) and channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom.'
				and sold_id in (select woid from warehouse_orders where subchannel='.$subchannel.')');
		}
		else
		{
			$query = $this->db->query('select sum(returned_amount) as Refunded from transaction_details
					where (return_id > 0 or returnID is not null) and channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom);
			//echo '<p>'.$this->db->last_query();
		}

		$sum_refunds = $query->first_row();
		return (float)$sum_refunds->Refunded; */
	}

	function SumRefundedFees($datefrom, $dateto, $channel=null)
	{
		$query = $this->db->query('select sum(fee) as Fee from transaction_details
				 where (refunded=1 or sellingstatus="PartiallyPaid") and channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom);
		//echo '<p>'.$this->db->last_query();
		
		$sum_refunds = $query->first_row();
		return (float)$sum_refunds->Fee;
	}
	
	//Revenue Lost To returns
	function SumRevenueLostToReturns($datefrom, $dateto, $channel=null, $subchannel=null)
	{
		if($channel==1)
		{
			$query = $this->db->query('select sum(ebayRefundAmount) as Refunded from ebay_transactions
				where refunded=1 and  mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
			//echo '<p>'.$this->db->last_query();
            $row  = $query->first_row();
		    return (float)$row->Refunded;
		}
        else
        {
            return 0;

        }    


        /* FROM TRANSACTION_DETAILS table

        if($subchannel!=null)
		{
			$query = $this->db->query('select sum(returned_amount) as RefLost from transaction_details
				where refunded = 1 and channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom.'
				and sold_id in (select woid from warehouse_orders where subchannel='.$subchannel.')');
		}
		else
		{
			$query = $this->db->query('select sum(returned_amount) as RefLost from transaction_details
					where refunded = 1 and channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom);
			//echo '<p>'.$this->db->last_query();
		}

		$sum_refunds = $query->first_row();
		return (float)$sum_refunds->RefLost;*/
	}

	/*Single BCN Partial Refund will be when the transaction ID has only one BCN attached to it. Multi will be the case 
	where one transaction has 5 BCN's and only one or portion of the transaction was returned for refund. Example 1 sold 1 iPhone for $100 and we issued partial refund of $10 because the charger was not working properly. 
	Example 2: We sold 5 iPhones and they returned 2 of them for $200 partial refund. */
	function SumSingleBCNPartialRefund($datefrom, $dateto, $channel=null, $subchannel=null)
	{
	   
      if($channel==1)
		{
			$query = $this->db->query('select sum(ebayRefundAmount) as Refunded from ebay_transactions
				where refunded=1 and  qty=1 and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
			//echo '<p>'.$this->db->last_query();
            $row  = $query->first_row();
		    return (float)$row->Refunded;
		}
        else
        {
            return 0;

        }
            //$query = $this->db->query('select sum(ebayRefundAmount) as Refunded from ebay_transactions where 
            //                                where refunded = 1 and  mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and et_id IN
            //                                             (select sold_id
            //                                 from transaction_details 
            //    where channel='.$channel.' and refunded = 1 
            //    and uts <= '.$dateto.' and uts >= '.$datefrom.' GROUP BY sold_id HAVING count(sold_id)=1)');

                //    select sum(ebayRefundAmount) as Refunded from ebay_transactions
                //where refunded = 1 and  mkdt <= '.$dateto.' and mkdt >= '.$datefrom           
	      		
	}

	/*Single BCN Partial Refund will be when the transaction ID has only one BCN attached to it. Multi will be the case 
	where one transaction has 5 BCN's and only one or portion of the transaction was returned for refund. Example 1 sold 1 iPhone for $100 and we issued partial refund of $10 because the charger was not working properly. 
	Example 2: We sold 5 iPhones and they returned 2 of them for $200 partial refund. */
	function SumMultiBCNPartialRefund($datefrom, $dateto, $channel=null, $subchannel=null)
	{
		 if($channel==1)
		{
			$query = $this->db->query('select sum(ebayRefundAmount) as Refunded from ebay_transactions
				where refunded=1 and  qty>1 and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
			//echo '<p>'.$this->db->last_query();
            $row  = $query->first_row();
		    return (float)$row->Refunded;

		}
        else
        {
            return 0;

        }
	}

   //IN MODEL
	//Scrap cost loss" is for items that have been returned and are not able to be resold so the status would be set as scrap. Would be the same for "defective lost" but for Defective status.
	function SumScrapCostLostRefunded($datefrom, $dateto, $channel=null, $subchannel=null)
	{
		if($subchannel!=null)
		{
			$query = $this->db->query('select sum(cost) as CostLost from warehouse
					where status = "Scrap" and wid IN
					(select distinct w_id  from transaction_details where (return_id > 0 or returnID is not null) 
						and channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom.'
				and sold_id in (select woid from warehouse_orders where subchannel='.$subchannel.'))');
		}
		elseif($channel>1)
		{
			$query = $this->db->query('select sum(cost) as CostLost from warehouse
					where status = "Scrap" and wid IN
					(select distinct w_id  from transaction_details where (return_id > 0 or returnID is not null)  and channel = '.$channel.
                        ' and uts <= '.$dateto.' and uts >= '.$datefrom.')');
			//echo '<p>'.$this->db->last_query();
		}
        elseif($channel==1)
        {
            $query = $this->db->query('select sum(cost) as CostLost from warehouse
					where status = "Scrap" and wid IN
					(select distinct w_id  from transaction_details where channel = '.$channel.' and sold_id IN
                    (select et_id from ebay_transactions 
				                where refunded = 1 and mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'))');
        }

		$sum_refunds = $query->first_row();
		return (float)$sum_refunds->CostLost;
	}


    //It is in model
    function SumLostShippingExpences($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
    {
        if($channel==1)
        {
            if(isset($sold_id))
            {
                    $this->db->select('if(sellingstatus="PartiallyPaid",
                                            sum(`asc`/((paid+ebayRefundAmount)/eachpaid)+ebayreturnshipment), sum(`asc`+ebayreturnshipment)) as LostAsc')
						->from('ebay_transactions')
                        ->where('refunded =',1)
                        ->where_in('et_id', $sold_id);
				  
                    $query = $this->db->get();
             }  
             else
             {
                    $query = $this->db->query('select if(sellingstatus="PartiallyPaid",
                                            sum(`asc`/((paid+ebayRefundAmount)/eachpaid)+ebayreturnshipment), sum(`asc`+ebayreturnshipment)) as LostAsc from ebay_transactions where refunded = 1 and 
                     mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
                    
            }

            $row = $query->first_row();
		    return (float)$row->LostAsc;
        }
        else
        {
            return 0;
        }
    }

    //In the model
    function SumOtherReturnExpences($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
    {
        if($channel==1)
        {
            if(isset($sold_id))
            {
                    $this->db->select(' sum(returned_extracost) as ExtraCost')
						->from('ebay_transactions')
                    	->where_in('et_id', $sold_id);
				  
                    $query = $this->db->get();
             }  
             else
             {
                    $query = $this->db->query('select sum(returned_extracost) as ExtraCost from ebay_transactions where refunded > 0 and 
                             mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
             }
             
             $row = $query->first_row();
             return (float)$row->ExtraCost;
             
        }
        else
        {
            return 0;
        }
    }
    //IN the model
    function SumReturnsRecoupedProfit($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
    {
         if($subchannel!=null)
		 {
			$query = $this->db->query('select wid, sold_id from transaction_details where 
                   channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom.' and sold_id in
                (select woid from warehouse_orders where subchannel='.$subchannel.')');
		 }
         else
         {
            if(isset($sold_id))
			{
				  $this->db->select('wid, sold_id')
						->from('transaction_details')
						 ->where_in('sold_id', $sold_id);
				  
                    $query = $this->db->get();

			}
			else
			{
				$query = $this->db->query('select wid, sold_id from transaction_details where 
                                            channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom);

			}
         }
        
        $sold_id_array = array();
        $wid_array = array();

        foreach ($query->result_array() as $row)
        {
                $sold_id_array[]= $row['sold_id'];
                $wid_array[]= $row['wid'];
                
        }

        $this->db->select('sum(paid) as Revenue')
                ->from('ebay_transactions')
                ->where_not_in('sold_id',$sold_id_array)
                ->where_in('w_id', $wid_array);
        
        $queryRevenue = $this->db->get();
        $row = $queryRevenue->FirstRow();

        return (float)$row->Revenue;

    }

    //$pay_pal_fees
	function SumPayPalFees($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null )
    {
        if($subchannel!=null)
		{
			$query = $this->db->query('select sum(paypal_fee) as Paypal_fees from transaction_details where 
                   channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom.' and sold_id in
                (select woid from warehouse_orders where subchannel='.$subchannel.')');
		}
        else
        {
            if(isset($sold_id))
			{
				  $this->db->select('sum(paypal_fee) as Paypal_fees')
						->from('transaction_details')
						 ->where_in('sold_id', $sold_id);
				  
                    $query = $this->db->get();

			}
			else
			{
				$query = $this->db->query('select sum(paypal_fee) as Paypal_fees from transaction_details where 
                                            channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom);

			}
        }
        
        $row = $query->first_row();
        return (float)$row->Paypal_fees;
 
    }

   
    
    
    function IsRefunded($sold_id=null)
	{
        if(isset($sold_id))
        {
            $query = $this->db->query("select refunded from ebay_transactions where et_id = ".$sold_id);
            $result_row = $query->first_row();

            if($result_row->refunded>0) 
            {
                return true;
            }
            else
            {
		        return false;
            }

        }
        else
        {
            return false;
        }
    }

    function SumSalesTransactionDetails($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
	{
		if($channel==1)//eBay
		{
			if(isset($sold_id))
			{
				  $this->db->select('sum(paid) as Revenue',FALSE)
						->from('transaction_details')
                        ->where('channel',1)
						->where_in('sold_id', $sold_id);
				  
                    $query = $this->db->get();

			}
			else
			{
				    //The PartiallyPaid is needed to subtract partially refund amount from the paid sum by customer
                    $query = $this->db->query('select sum(paid) as Revenue
                                              from transaction_details where uts <= '.$dateto.' and uts >= '.$datefrom.'
                                                and channel = 1');


                                        //$query = $this->db->query('select sum(IF(ebayRefundAmount < paid, (paid-ebayRefundAmount)+ssc, paid+ssc)) 
                    //                          as Revenue from ebay_transactions where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and (ebayRefundAmount=0 or ebayRefundAmount < paid) and paid>0');



            // if we add refunded <> 1 we remove some refunded from sales
            //select * from ebay_transactions where 
            //mkdt <= 1491289199 and mkdt >= 1491030000 and (ebayRefundAmount=0 or ebayRefundAmount < paid or paid>0) and refunded <> 1 order by et_id

			}
			//echo '<p>'.$this->db->last_query();
 
		}

		elseif($channel==2)//LATRWebsiteOrders
		{
			if(isset($sold_id))
			{			
				$this->db->select('sum(paid) as Revenue',FALSE)
						->from('transaction_details')
                        ->where('channel',2)
						->where_in('et_id', $sold_id);

				  $query = $this->db->get();
			}
			else
			{
				      $query = $this->db->query('select sum(paid) as Revenue
                                              from transaction_details where uts <= '.$dateto.' and uts >= '.$datefrom.'
                                                and channel = 2');
			}

           // echo '<p>'.$this->db->last_query();
		}
		elseif(isset($subchannel))//Warehouse
		{
			if(count($sold_id)>0)
			{		 
				$this->db->select('sum(paid) as Revenue')
						->from('warehouse_orders')
						->where('subchannel', 0)//We dont use subchanel here because the id-s in warehouse_orders are unique and there is no same ids for different subchannels.
                        ->where_in('sold_id', $sold_id);//We have id-s here
				$query = $this->db->get();
			}
			else
			{
                //$query = $this->db->query('SELECT
                //                    sum(warehouse_orders.paid) as Revenue
                //                    FROM warehouse_orders 
                //               WHERE subchannel='.$subchannel.' and
                //            timemk >= '.$datefrom.' and timemk <= '.$dateto);

                //I select data from transactions_details because for same sold_id there is amount paid. In warehouse_orders it is 0.
                $query = $this->db->query('select sum(transaction_details.paid) as Revenue from transaction_details inner join 
                     warehouse on transaction_details.w_id=warehouse.wid where transaction_details.sold_id is not null AND 
                     transaction_details.channel = 4 AND transaction_details.sold_id
                    IN (select woid from warehouse_orders where timemk <= '.$dateto.' and timemk >= '.$datefrom.' and subchannel='.$subchannel.')');
			}
		}

		//echo '<p>'.$this->db->last_query();
		$result_row = $query->first_row();

		return (float)$result_row->Revenue;
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
     // echo "<p>Months = ".$numMonths;

        if($numDays<=61) //daily period
        {
            

            $x_categories = Array();
            $y_revenue = Array();
            $y_net_profit = Array();
            $y_refunds = Array();
            $y_avg_revenue = Array();

            for ($i = 0; $i < $numDays; $i++) 
            {
                $x_categories[] = date('m/j/Y', $datefrom);

                $dateto = strtotime("+1 day", $datefrom-1); //23:59:59
				
                $y_revenue[] = (int)$this->Myreports_model->SumSales($datefrom, $dateto, $channel, $subchannel, null);

                $y_avg_revenue[] = (int)(array_sum($y_revenue) / count($y_revenue)); 

                $y_net_profit[] = (int)$this->Myreports_model->SumNetProfit($datefrom, $dateto, $channel, $subchannel, null);
                $y_refunds[] = (int)$this->Myreports_model->SumRefundedAmount($datefrom, $dateto, $channel, $subchannel, null);

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

            for ($i = 1; $i < $numMonths; $i++) 
            {
                if(date("d", $datefrom)==1)
                {
                    $x_categories[] = date('M', $datefrom);
                    $dateto = strtotime("first day of next month", $datefrom);//23:59:59
                    //$dateto = strtotime("+1 month", $datefrom -1);//23:59:59
                    //$dateto1 = date('m/j/Y', $datefrom);
                    //$date = new DateTime();
                    //$date->setTimestamp($datefrom);
                    //$datetemp = $date->modify( 'first day of next month' );
                    //$dateto = $datetemp->setTimestamp($datetemp);

                    if($dateto > $const_dateto) $dateto = $const_dateto;

                    $y_revenue[] = (int)$this->Myreports_model->SumSales($datefrom, $dateto, $channel, $subchannel, null);
                    $y_avg_revenue[] = (int)(array_sum($y_revenue) / count($y_revenue)); 
                    $y_net_profit[] = (int)$this->Myreports_model->SumNetProfit($datefrom, $dateto, $channel, $subchannel, null);  
                    $y_refunds[] = (int)$this->Myreports_model->SumRefundedAmount($datefrom, $dateto, $channel, $subchannel, null);
                    //echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
                    $datefrom = $dateto+1; //00:00:00

                     if($dateto == $const_dateto) break;

                }
                else
                {
                    $x_categories[] = date('m/j/Y', $datefrom);

                    $dateto = strtotime("+1 month", $datefrom -1);//23:59:59
				
                    if($dateto > $const_dateto) $dateto = $const_dateto;


                    $y_revenue[] = (int)$this->Myreports_model->SumSales($datefrom, $dateto, $channel, $subchannel, null);
                    $y_avg_revenue[] = (int)(array_sum($y_revenue) / count($y_revenue)); 
                    $y_net_profit[] = (int)$this->Myreports_model->SumNetProfit($datefrom, $dateto, $channel, $subchannel, null);  
                    $y_refunds[] = (int)$this->Myreports_model->SumRefundedAmount($datefrom, $dateto, $channel, $subchannel, null);
                    //echo '<p style="color:blue">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
                    $datefrom = $dateto+1; //00:00:00

                    if($dateto == $const_dateto) break;

                }
            }

        } 
        
        $this->mysmarty->assign('x_categories', json_encode($x_categories));
        $this->mysmarty->assign('y_revenue', json_encode($y_revenue));
        $this->mysmarty->assign('y_avg_revenue', json_encode($y_avg_revenue));
        $this->mysmarty->assign('y_net_profit', json_encode($y_net_profit));
        $this->mysmarty->assign('y_refunds', json_encode($y_refunds));
        
    }      
    

            //<td class="tg-yw4l">Sales</td>
            //<td class="tg-yw4l"><b>{$sales}</b></td>
            //<td class="tg-yw4l">Listed</td>
            //<td class="tg-yw4l">{$status_listed}</td>
            //<td class="tg-yw4l">Sales</td>
            //<td class="tg-yw4l">{$sold_only_sales}</td>
            //<td class="tg-yw4l">Total Returns<br></td>
            //<td class="tg-yw4l"><b>{$returns_total_returns}</b></td>  
}

