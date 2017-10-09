<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myreports_model extends Model 
{
    function Myreports_model()
    {
        parent::Model();
    }

    function nuemp($var)
	{
		if (is_null($var)) return '';
		else return $var;
	}

    function SumSales($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
	{
		//echo '<p style="color:black">From '.date('m/j/Y   H:i:s',$datefrom).' to '.date('m/j/Y   H:i:s',$dateto).'&#8595;';
        if($channel==1)//eBay
		{
			if(isset($sold_id))
			{
				  $this->db->select('sum(paid+ssc) as Revenue',FALSE)
						->from('ebay_transactions')

						->where_in('et_id', $sold_id);
				  
                    $query = $this->db->get();

			}
			else
			{
				     //1. eBay count not paid transactions by fieled sellingstatus'=="Unpaid". These transactions in ebay reports are shown with sale = 0. We have another fieled notpaid which is useless here.
                     //2. The PartiallyPaid is needed to subtract partially refund amount from the paid sum by customer
                    $query = $this->db->query('select sum(paid+ssc) as Revenue
                                              from ebay_transactions where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'
                                                and (refunded=0 or sellingstatus="PartiallyPaid") and COALESCE(sellingstatus,"")<>"Unpaid"');


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
				$this->db->select('sum(endprice) as Revenue')
						->from('orders')
						 ->where_in('oid', $sold_id);
				  $query = $this->db->get();
			}
			else
			{
				$query = $this->db->query('select sum(endprice) as Revenue from orders
									where
									 complete<>-1 and submittime >= '.$datefrom.' and
									submittime <= '.$dateto);
			}

            echo '<p>'.$this->db->last_query();
		}
		elseif(isset($subchannel))//Warehouse
		{
			if(count($sold_id)>0)
			{		 
				$this->db->select('sum(paid) as Revenue')
						->from('warehouse_orders')
						->where('subchannel', 0)//We dont use subchanel here because the id-s in warehouse_orders are unique and there is no same ids for different subchannels.
                        ->where_in('sold_id', $sold_id);//We have id-s here
				
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

    function SumRefundedAmountEbayStyle($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
	{
		if($channel==1)
		{
            if(isset($sold_id))
            {
                    $this->db->select('SUM(IF(ebayRefundAmount > 0, ebayRefundAmount,
                                            IF(ssc = 0, coalesce(ssc_old,0) + eachpaid, ssc + eachpaid))) AS Refunded')
						        ->from('ebay_transactions')
                                ->where('sellingstatus <>', "PartiallyPaid")
						         ->where_in('et_id', $sold_id);
				  
                    $query = $this->db->get();
             }  
             else
             {
                $query = $this->db->query('select SUM(IF(ebayRefundAmount > 0,
                                            ebayRefundAmount,
                                            IF(ssc = 0, coalesce(ssc_old,0) + eachpaid, ssc + eachpaid))) AS Refunded from ebay_transactions
				       where refunded=1 and sellingstatus <> "PartiallyPaid" and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
                   
			    
                
             }

             //echo '<p>'.$this->db->last_query();

             $row  = $query->first_row();
		    return (float)$row->Refunded;
		}
        else
        {
            return 0;

        }
	
	}

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
                   
			   
                
             }

            // echo '<p>'.$this->db->last_query();

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

    //$refunded can be 0 or 1 or null !
    function SumActualShipping($datefrom, $dateto, $selector, $sold_id=null, $refunded=null)
	{
		if($selector==1)//eBay
		{
			if(count($sold_id)>0)
			{
				  if(!isset($refunded)) //all transactions, refunded included
                  {
                    $this->db->select('sum(`asc`) as "ActualShippingPrice"')
						->from('ebay_transactions')
						 ->where_in('et_id', $sold_id);
				  
                    $query = $this->db->get();
                  }
                  elseif($refunded == 0)//without refunded
                  {
                    $this->db->select('sum(`asc`) as "ActualShippingPrice"')
						->from('ebay_transactions')
                        ->where('(refunded = 0 or sellingstatus = "PartiallyPaid")')
						 ->where_in('et_id', $sold_id);
				  
                    $query = $this->db->get();
                  }
                  else //with refunded
                  {
                    $this->db->select('sum(`asc`) as "ActualShippingPrice"')
						->from('ebay_transactions')
                        ->where('refunded = 1 and sellingstatus <> "PartiallyPaid"')
						 ->where_in('et_id', $sold_id);
				  
                    $query = $this->db->get();
                  }
			}
			else
			{
				 if(!isset($refunded)) //all transactions, refunded included
                 {
                    $query = $this->db->query('select sum(`asc`) as "ActualShippingPrice"
					    from ebay_transactions
				     where and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
                 }
                 elseif($refunded == 0) //without refunded
                 {
                      $query = $this->db->query('select sum(`asc`) as "ActualShippingPrice"
					    from ebay_transactions
				     where (refunded = 0 or sellingstatus = "PartiallyPaid") and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
                 }
                 else //with refunded
                 {
                     $query = $this->db->query('select sum(`asc`) as "ActualShippingPrice"
					    from ebay_transactions
				     where refunded = 1 and sellingstatus <> "PartiallyPaid" and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
                  
                 }

			}
			//echo '<p>'.$this->db->last_query();
 
		}
		elseif($selector==2)//LATRWebsiteOrders
		{
			$query = $this->db->query('select sum(endprice_delivery) as "ActualShippingPrice" from orders
									where
                                     complete<>-1 and
									submittime >= '.$datefrom.' and
									submittime <= '.$dateto);
		}
		 elseif($selector==3)//Warehouse subchannel=0
		{
			 $query = $this->db->query('SELECT sum(shipped_actual) AS "ActualShippingPrice"
									FROM warehouse  LEFT JOIN warehouse_orders ON sold_id=woid
							   WHERE channel = 4 AND sold_id IN
							(SELECT woid FROM warehouse_orders where subchannel=0 and
							timemk >= '.$datefrom.' and timemk <= '.$dateto.')');

		}
		elseif($selector==4)//365 Web subchannel=1
		{
		 $query = $this->db->query('SELECT shipped_actual AS "ActualShippingPrice"
									FROM warehouse  LEFT JOIN warehouse_orders ON sold_id=woid
							   WHERE channel = 4 AND sold_id IN
							(SELECT woid FROM warehouse_orders where subchannel=1 and
							timemk >= '.$datefrom.' and timemk <= '.$dateto.')');
		}

		$result_row = $query->first_row();
		return (float)$result_row->ActualShippingPrice;
	}

    function SumLostShippingExpences($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
    {
        if($channel==1)
        {
           
        //If transaction has multiple items and just one or more are refunded we have to find their shipping cost.
        // Mind that column data `asc`, ebayreturnshipment are for the whole transaction so for one item 
        //formula should be (`asc` + ebayreturnshipment) / num_items) * returnQuantity

            if(isset($sold_id) and count($sold_id)>0)
            {
                    $this->db->select(' IF(qty = 1, SUM(`asc` + ebayreturnshipment),
                    SUM(((`asc` + ebayreturnshipment) / qty) * coalesce(returnQuantity,1))) AS LostAsc', FALSE)
						->from('ebay_transactions')
                        ->where('refunded =',1)
                        ->where_in('et_id', $sold_id);
				  
                    $query = $this->db->get();
             }  
             else
             {
                    $query = $this->db->query('select  IF(revs = 1, SUM(`asc` + ebayreturnshipment),
                            SUM(((`asc` + ebayreturnshipment) / qty) * coalesce(returnQuantity,1))) AS LostAsc from ebay_transactions where refunded = 1 and 
                     mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
                    
            }

            //echo "<p>".$this->db->last_query();

            $row = $query->first_row();
		    return (float)$row->LostAsc;
        }
        else
        {
            return 0;
        }
    }

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

//Returns sold price of specific wid (product).If it is sold again in new transaction. It is used for refunded transactions.
//Result gives us sold prise of the sold again product. Product can be sold only once,
//functions skips the refunded transactions and searches for real sale. Provide either period or array of transaction numbers as functions argument.
    function SumReturnsRecoupedRevenue($datefrom, $dateto, $channel=null, $sold_id=null)
    {
        if(isset($sold_id))
		{
				$this->db->select('w_id, sold_id, uts')
					->from('transaction_details')
                        ->where(channel, $channel)
						->where_in('sold_id', $sold_id);
				  
                $query = $this->db->get();

		}
		else
		{
		        $query = $this->db->query('select w_id, sold_id, uts from transaction_details where 
                                                channel = '.$channel.' and sold_id IN
                            (select et_id from ebay_transactions 
				                    where refunded = 1 and mkdt <= '.$dateto.' and mkdt >= '.$datefrom.')');
                    //echo "<p>".$this->db->last_query();
		
        }

        //$sold_id_array = array();
        //$w_id_array = array();
        
        $Revenue = 0.0;

        foreach ($query->result_array() as $row)
        {
                //$sold_id_array[]= $row['sold_id'];
                //$w_id_array[]= $row['w_id'];

                    $this->db->select('sum(a.paid) as Revenue',FALSE)
                ->from('transaction_details a')
                ->join('ebay_transactions', 'sold_id = et_id', 'left outer')
                ->where('sold_id <>',$row['sold_id'])
                ->where('uts >',$row['uts'])
                ->where('w_id', $row['w_id'])
                ->where('refunded', 0);
        
                $queryRevenue = $this->db->get();

                //echo "<p>".$this->db->last_query();

                $row = $queryRevenue->first_row();

                $Revenue+=(float)$row->Revenue;
        }

        // echo '<p><font size="2" color="blue">Revenue var1 = '.$Revenue;

        //printcool($sold_id_array);
        //printcool($w_id_array);

        //$this->db->select('sum(paid) as Revenue')
        //        ->from('transaction_details')
        //       // ->where('uts >',$dateto)
        //        ->where_not_in('sold_id',$sold_id_array)
        //        ->where_in('w_id', $w_id_array);
        
        //$queryRevenue2 = $this->db->get();

        //echo "<p>".$this->db->last_query();

        //$row = $queryRevenue2->first_row();

            //echo '<p><font size="2" color="blue">Revenue var2 = '.(float)$row->Revenue;

        return (float)$Revenue;

    }

/* OLD VARIANT not so precise as new one above
    function SumReturnsRecoupedRevenue($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
    {
         if($subchannel!=null)
		 {
			$query = $this->db->query('select w_id, sold_id from transaction_details where 
                   channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom.' and sold_id in
                (select woid from warehouse_orders where subchannel='.$subchannel.' and return_id > 0)');
		 }
         else
         {
            if(isset($sold_id))
			{
				  $this->db->select('w_id, sold_id')
						->from('transaction_details')
						 ->where_in('sold_id', $sold_id);
				  
                    $query = $this->db->get();

			}
			else
			{
				$query = $this->db->query('select w_id, sold_id from transaction_details where 
                                            channel = '.$channel.' and sold_id IN
                        (select et_id from ebay_transactions 
				                where refunded = 1 and mkdt <= '.$dateto.' and mkdt >= '.$datefrom.')');
                //echo "<p>".$this->db->last_query();
			}
         }
        
        

        $sold_id_array = array();
        $w_id_array = array();
        
        foreach ($query->result_array() as $row)
        {
                $sold_id_array[]= $row['sold_id'];
                $w_id_array[]= $row['w_id'];



        }

        //printcool($sold_id_array);
        //printcool($w_id_array);

        $this->db->select('sum(paid) as Revenue')
                ->from('transaction_details')
               // ->where('uts >',$dateto)
                ->where_not_in('sold_id',$sold_id_array)
                ->where_in('w_id', $w_id_array);
        
        $queryRevenue = $this->db->get();

        //echo "<p>".$this->db->last_query();

        $row = $queryRevenue->first_row();

        return (float)$row->Revenue;

    }
*/

//Returns transaction number of specific wid (product) if it is sold again in new transaction. It is used for refunded transaction.
//Result gives us transaction number of sold again product. Return only number one transaction per product  because a product can be sold only once,
//functions skips the refunded transactions and searches for real sale.
    function SoldAgainTransNumber($channel=null, $sold_id=null, $wid=null)
    {
         //echo '<p>'.$wid.' '.$sold_id;

        if(isset($sold_id) and isset($wid) and isset($channel))
		{
				$this->db->select('uts')
					->from('transaction_details')
                        ->where(channel, $channel)
						->where('sold_id', $sold_id);
				  
                $query = $this->db->get();

		        $row = $query->first_row();


                $this->db->select('sold_id',FALSE)
                ->from('transaction_details a')
                ->join('ebay_transactions', 'sold_id = et_id', 'left outer')
                ->where('sold_id <>',$sold_id)
                ->where('uts >',$row->uts)
                ->where('w_id', $wid)
                ->where('refunded', 0);
        
                $querySoldId = $this->db->get();
                
               // echo "<p>".$this->db->last_query();


                $row_res = $querySoldId->first_row();

                return (int)$row_res->sold_id;
         }
         else
         {
                return 0;
        }
          
    }

  
    //Returns the sum of refunded amount of refunded transactions for a period or for particular transactions
    //passed as argument $sold_id as array of transaction numbers. Provide as argument period or array with $sold_id
    //argument. 
    function SumCostRefunded($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
	{
		if($channel==1)
		{
			if(isset($sold_id))
			{
                $SumCost = 0;
                $this->db->select('wid as wid, transaction_details.channel, transaction_details.sold_id,  warehouse.bcn, title, `status`, transaction_details.paid as revenue, cost, transaction_details.fee, transaction_details.returned_amount, return_total_qty, qty, transaction_details.return_id, transaction_details.returnID, cost')
											->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
                                    ->join('ebay_transactions','transaction_details.sold_id=ebay_transactions.et_id')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 1')
									->where_in('transaction_details.sold_id', $sold_id)
                                            ->order_by('sold_id', 'ASC');
                 $query = $this->db->get();

                
                
                    
            }              
            else
            {
               $this->db->select('wid as wid, transaction_details.channel, transaction_details.sold_id,  warehouse.bcn, title, `status`, transaction_details.paid as revenue, cost, transaction_details.fee, transaction_details.returned_amount, return_total_qty, qty, transaction_details.return_id, transaction_details.returnID, cost')
											->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
                                    ->join('ebay_transactions','transaction_details.sold_id=ebay_transactions.et_id')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 1')
									->where('mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and refunded=1')
                                            ->order_by('sold_id', 'ASC');
                 $query = $this->db->get();

                 //echo '<p>'.$this->db->last_query();
            }

            foreach ($query->result_array() as $row)
            {
                if($row['qty']==1)
                {
                    
                      $SumCost += (float)$row['cost'];

                }
                elseif((float)$row['returned_amount']>0 or $row['return_id']>0 or isset($row['returnID'])) 
                {
                       $SumCost += (float)$row['cost'];
                   
                }
            }

            return (float)$SumCost;

		}

		elseif($channel==2)//LATRWebsiteOrders
		{
            $query = $this->db->query('select sum(cost) as Cost from warehouse where sold_id is not null AND channel = 2 AND wid IN
            (select w_id from transaction_details  where uts <= '.$dateto.' and uts >= '.$datefrom.' and channel = 2 and return_id > 0)');

            //$query = $this->db->query('select sum(cost) as Cost from warehouse where sold_id is not null AND channel = 2 AND wid IN
            //(select w_id from orders where submittime <= '.$dateto.' and submittime >= '.$datefrom.')');
            $result = $query->first_row();
            return (float)$result->Cost;


		}
		elseif($subchannel!=null)//Warehouse subchannel=0, 365 Web subchannel=1
		{
			 $query = $this->db->query('select sum(cost) as Cost
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = '.$channel.' and return_id > 0 AND transaction_details.sold_id IN 
			(select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.' and subchannel='.$subchannel.')');

			 $result = $query->first_row();
             return (float)$result->Cost;

		}
		
    
		
	}

    //Scrap cost loss" is for items that have been returned and are not able to be resold so the status would be set as scrap. Would be the same for "defective lost" but for Defective status.
	function SumScrapCostLostRefunded($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id)
	{
		
        if($channel==1)
        {
            if(isset($sold_id))
			{
                $this->db->select('w_id', FALSE)
									->from('transaction_details')
									->where('sold_id is not null AND channel = 1')
									->where_in('sold_id', $sold_id);

                $query = $this->db->get();

                //there is transactions without attached products to them in transaction details table. We have to check:
                if($query->num_rows()>0)
                {

                //echo "<p>".$this->db->last_query();

                    $wid_array = Array();
       
                    foreach ($query->result_array() as $row)
                    {
                   
                       $wid_array[]=$row['w_id'];
                    }

                    //printcool($wid_array);

                     $this->db->select('sum(cost) as CostLost', FALSE)
                                        ->from('warehouse')
                                        ->where('sold_id is not null AND status = "Scrap" ')
                                        ->where_in('wid', $wid_array);
                     $query = $this->db->get();
                 }
                 else
                 {
                        return (float)0;
                 }   
            }
            else
            {
                $query = $this->db->query('select sum(cost) as CostLost from warehouse
					where status = "Scrap" and wid IN
					(select distinct w_id  from transaction_details where channel = '.$channel.' and sold_id IN
                    (select et_id from ebay_transactions 
				                where refunded = 1 and mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'))');
            }
        }
        elseif($channel==2)
		{
			$query = $this->db->query('select sum(cost) as CostLost from warehouse
					where status = "Scrap" and wid IN
					(select distinct w_id  from transaction_details where (return_id > 0 or returnID is not null)  and channel = '.$channel.
                        ' and uts <= '.$dateto.' and uts >= '.$datefrom.')');
			//echo '<p>'.$this->db->last_query();
		}
        elseif($subchannel!=null)
		{
			$query = $this->db->query('select sum(cost) as CostLost from warehouse
					where status = "Scrap" and wid IN
					(select distinct w_id  from transaction_details where (return_id > 0 or returnID is not null) 
						and channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom.'
				and sold_id in (select woid from warehouse_orders where subchannel='.$subchannel.'))');
		}
		
        
		$sum_refunds = $query->first_row();
		return (float)$sum_refunds->CostLost;
	}
      //Sell price for one product in particular transaction. Note that is transaction has many products in it the function
      //returns the sell prise of one product!
    function  SumSellPriseByWID($wid=null, $sold_id=null, $channel=null)
	{
	
		if(isset($wid) and isset($sold_id) and isset($channel))
		{
			$this->db->select('transaction_details.paid', FALSE)
									->from('transaction_details')
                                    //->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id = '.$sold_id.' AND transaction_details.channel = '.$channel.' and transaction_details.w_id = '.$wid);
                                    //->where_in('transaction_details.sold_id', $eid_array);
			$result = $this->db->get();
		   //echo '<p>'.$this->db->last_query();
			$row = $result->first_row();

                       
            return (float)$row->paid;
            
		}
		else
		{
			return 0;
		}
			
	}


    //---------------------  CURRENTLY NOT IN USE ----------------------------------------
    //Returns the number of products with no cost in warehouse. Arguments are period or for particular transactions
    //in $sold_id as array of transaction numbers. Provide as argument period or array with $sold_id
    //argument. 
    function CountMissingCost($datefrom, $dateto, $channel=null, $subchannel=null, $sold_id=null)
	{
		if($channel==1)
		{
			if(isset($sold_id) and count($sold_id>0))
			{
                   
                    $this->db->select('count(*) as Cost', FALSE)
									->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid', 'left')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 1')
                                    ->where('cost',0)
                                    ->where('coalesce(transaction_details.channel, 1) = 1', NULL, FALSE)
									->where_in('transaction_details.sold_id', $sold_id);				

					
			        $result = $this->db->get();
		            //echo '<p>'.$this->db->last_query();
			        $row = $result->first_row();

                    // If the data is missing in the transaction_details table. 
                    // Go directly in warehouse and try to find the cost
                    // of the products in transaction. Not 100% reliable information because if the product is been sold 
                    // in other transaction later the data from first transaction is replaced in the warehouse!

                    if($row->Cost==0)
                    {
                         $this->db->select('count(*) as Cost', FALSE)
									->from('warehouse')
									->where('sold_id is not null AND channel = 1')
                                     ->where('cost',0)
									->where_in('sold_id', $sold_id);

                        $result = $this->db->get();
                        $row= $result->first_row();
                    }
                    
                    //echo '<p>'.$this->db->last_query();


			        return (float)$row->Cost;

            }
            else
            {
                $query = $this->db->query('select count(*) as Cost from warehouse where cost=0 and sold_id is not null AND channel = 1 AND wid IN
			    (select w_id from transaction_details where channel = 1 and sold_id in
                     (select et_id from ebay_transactions 
				                where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'))');

                 //echo '<p>'.$this->db->last_query();
            }

		}

		elseif($channel==2)//LATRWebsiteOrders
		{
            $query = $this->db->query('select count(*) as Cost from warehouse where cost=0 and sold_id is not null AND channel = 2 AND wid IN
            (select w_id from transaction_details  where uts <= '.$dateto.' and uts >= '.$datefrom.' and channel = 2)');

		}
		elseif($subchannel!=null)//Warehouse subchannel=0, 365 Web subchannel=1
		{
			 $query = $this->db->query('select count(*) as Cost
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where cost=0 and 
									transaction_details.sold_id is not null AND transaction_details.channel = '.$channel.' AND transaction_details.sold_id IN 
			  (select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.' and subchannel='.$subchannel.')');

		}
		
        $result = $query->first_row();
        return (int)$result->Cost;
		
	}
 
    //$refunded can be 0 or 1 or null !    
    function SumSSC($datefrom, $dateto, $sold_id_array=null, $refunded=null)
    {
        if(count($sold_id_array)>0)
        {
             if(!isset($refunded)) //all transactions, refunded included
             {
                 //$this->db->select('coalesce(sum(if(ssc > 0, ssc, ssc_old)),0) as SSC_Profit')
                 $this->db->select('coalesce(sum(ssc),0) as SSC_Profit')
                                          ->from('ebay_transactions')
                                          ->where_in('et_id',$sold_id_array,FALSE);

                 $query_ssc = $this->db->get();
            }
            elseif($refunded == 0)//without refunded
            {
                //$this->db->select('coalesce(sum(if(ssc > 0, ssc, ssc_old)),0) as SSC_Profit')
                $this->db->select('coalesce(sum(ssc),0) as SSC_Profit')
                                                 ->from('ebay_transactions')
                                                 ->where('(refunded = 0 or sellingstatus = "PartiallyPaid")')
                                                 ->where_in('et_id',$sold_id_array,FALSE);
                 $query_ssc = $this->db->get();
            }
            else //only refunded
            {
                //$this->db->select('coalesce(sum(if(ssc > 0, ssc, ssc_old)),0) as SSC_Profit')
                $this->db->select('coalesce(sum(ssc),0) as SSC_Profit')
                                            ->from('ebay_transactions')
                                            ->where('refunded = 1 and sellingstatus <> "PartiallyPaid"')
                                            ->where_in('et_id',$sold_id_array,FALSE);
                 $query_ssc = $this->db->get();

            }

            //echo '<p>'.$this->db->last_query();

            $row = $query_ssc->first_row();
            return (float)$row->SSC_Profit;
        }
        else
        {
            if(!isset($refunded)) //all transactions, refunded included
            {
               //$query = $this->db->query('select coalesce(sum(if(ssc > 0, ssc, ssc_old)),0) as SSC_Profit
               $query = $this->db->query('select coalesce(sum(ssc),0) as SSC_Profit
					                    from ebay_transactions
				                where mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
		    }
            elseif($refunded == 0)//without refunded
            {
               // $query = $this->db->query('select coalesce(sum(if(ssc > 0, ssc, ssc_old)),0) as SSC_Profit
                $query = $this->db->query('select coalesce(sum(ssc),0) as SSC_Profit
					                    from ebay_transactions
				                where mkdt <= '.$dateto.' and mkdt >= '.$datefrom .' and (refunded = 0 or sellingstatus = "PartiallyPaid")');
                //echo '<p>'.$this->db->last_query();
            }
            else  //only refunded
            {
                 //$query = $this->db->query('select coalesce(sum(if(ssc > 0, ssc, ssc_old)),0) as SSC_Profit
                 $query = $this->db->query('select coalesce(sum(ssc),0) as SSC_Profit
					                    from ebay_transactions
				                where mkdt <= '.$dateto.' and mkdt >= '.$datefrom .' and refunded = 1 and sellingstatus <> "PartiallyPaid"');

            }
            // echo '<p>'.$this->db->last_query();
             $row = $query->first_row();
            return (float)$row->SSC_Profit;
		}
       
    }

    function  AvgRefundAmount($datefrom, $dateto)
	{
        $query = $this->db->query('select  AVG(ebayRefundAmount) as AvRet from ebay_transactions
                                  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and refunded=1');
        $row = $query->first_row();
		return $row->AvRet;
	}

    function  EbayRefundTransID($datefrom, $dateto)
	{
        $query = $this->db->query('select  et_id  from ebay_transactions
                                  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'
        and refunded=1');

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

    function  CountRefundedTransactions($datefrom, $dateto)
	{
        $query = $this->db->query('select  Count(et_id) as NumRefTrans from ebay_transactions
                                  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and (refunded=1 or COALESCE(sellingstatus,"")="Unpaid")');

		$row = $query->first_row();
		return (int)$row->NumRefTrans;
	}

    	//Scrap cost loss" is for items that have been returned and are not able to be resold so the status would be set as scrap. Would be the same for "defective lost" but for Defective status.
	function SumDefectiveCostLost($datefrom, $dateto, $channel=null, $subchannel=null)
	{
		if($subchannel!=null)
		{
			$query = $this->db->query('select sum(cost) as CostLost from warehouse
					where status = "Defective" and wid IN
					(select distinct w_id  from transaction_details where (return_id > 0 or returnID is not null)  
						and channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom.'
				and sold_id in (select woid from warehouse_orders where subchannel='.$subchannel.'))');
		}
		else
		{
			$query = $this->db->query('select sum(cost) as CostLost from warehouse
					where status = "Defective" and wid IN
					(select distinct w_id  from transaction_details where (return_id > 0 or returnID is not null)  and channel = '.$channel.' and uts <= '.$dateto.' and uts >= '.$datefrom.')');
			//echo '<p>'.$this->db->last_query();
		}

		$sum_refunds = $query->first_row();
		return (float)$sum_refunds->CostLost;
	}

    function SumNetProfit($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id=null)
	{
		if($channel==1)//eBay
		{
			if(isset($sold_id))
			{
                  //$this->db->select('sum(transaction_details.paid+ssc)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit', FALSE)
                  //                  ->from('transaction_details')
                  //                  ->join('warehouse','transaction_details.w_id=warehouse.wid')
                  //                  ->where('transaction_details.sold_id is not null AND transaction_details.channel = 1')
                  //                  ->where_in('transaction_details.sold_id', $sold_id);
                  

                  $this->db->select('sum(transaction_details.paid  + ssc) - (sum(cost)+sum(transaction_details.fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit', FALSE)
                                    ->from('ebay_transactions')
                                    ->join('transaction_details','transaction_details.sold_id = ebay_transactions.et_id', 'left')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 1 and (ebay_transactions.refunded = 0 or sellingstatus = "PartiallyPaid")')
									->where_in('transaction_details.sold_id', $sold_id);
                    $result = $this->db->get();

                    //echo '<p>'.$this->db->last_query();
			        $row = $result->first_row();

			        return ((float)$row->Profit);
                   //return (float)$row->Profit;
			}
			else
			{
                    //$query = $this->db->query('select sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit
                    //                          from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
                    //                    transaction_details.sold_id is not null AND transaction_details.channel = 1  AND transaction_details.sold_id IN 
                    //(select et_id from ebay_transactions  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and notpaid = 0 and ebayRefundAmount<paid)');

                    $query = $this->db->query('select sum(transaction_details.paid + ssc) - (sum(cost)+sum(transaction_details.fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit
                                          from ebay_transactions left join transaction_details on et_id = sold_id left join warehouse on transaction_details.w_id=warehouse.wid where
                                    transaction_details.sold_id is not null AND transaction_details.channel = 1  AND  mkdt <= '.$dateto.' and mkdt >= '.$datefrom.' and (refunded = 0 or sellingstatus = "PartiallyPaid")');

			        $result = $query->first_row();
                    //echo '<p>'.$this->db->last_query();
                     //return (float)$result->Profit;
                    
  
                    $profit = ((float)$result->Profit)-($this->SumRefundsAllExtraCosts($datefrom, $dateto, $sold_id) + $this->SumLostShippingExpences($datefrom, $dateto, $channel=1, $subchannel=null, $sold_id=null));
                   
                    //$profit = (float)$result->Profit;

                    return (float)$profit;
			}

		}

		elseif($channel==2)//LATRWebsiteOrders
		{
			if(isset($sold_id))
			{
				  $this->db->select('sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit', FALSE)
									->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 2')
									->where_in('transaction_details.sold_id', $sold_id);
			        $result = $this->db->get();
		            //echo '<p>'.$this->db->last_query();
			        $row = $result->first_row();

			        return (float)$row->Profit;

			}
			else
			{

                    $query = $this->db->query('select sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)) as Profit
										          from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									        transaction_details.sold_id is not null AND transaction_details.channel = 2  AND transaction_details.sold_id IN 
			        (select oid from orders  where submittime <= '.$dateto.' and submittime >= '.$datefrom.' and complete<>-1)');

			        $result = $query->first_row();

			        return (float)$result->Profit;
            }
		}
		elseif(isset($subchannel))//Warehouse subchannel=0, 365 Web subchannel=1, warehouse_hawthorne_365 ->  subchanel=7, warehouse_venice_365 ->  subchanel=8
		{
            if(isset($sold_id))
			{
				  $this->db->select('sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)+sum(transaction_details.extra_cost)) as Profit', FALSE)
									->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 4')
									->where_in('transaction_details.sold_id', $sold_id);
			        $result = $this->db->get();
		            //echo '<p>'.$this->db->last_query();
			        $row = $result->first_row();

			        return (float)$row->Profit;

			}
			else
			{

                    $query = $this->db->query('select sum(transaction_details.paid)-(sum(cost)+sum(fee)+sum(shipped_actual)+sum(transaction_details.paypal_fee)) as Profit
										          from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									        transaction_details.sold_id is not null AND transaction_details.channel = 4  AND transaction_details.sold_id IN 
			        (select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.' and subchannel='.$subchannel.')');

			        $result = $query->first_row();

			        return (float)$result->Profit;
            }
	

		}
				
	}

    //$refunded can be 0 or 1 or null !
	function SumFees($datefrom, $dateto, $selector, $sold_id=null, $refunded=null)
	{
		if($selector==1)//eBay
		{
			if(count($sold_id)>0)
			{
                  if(!isset($refunded)) //all transactions, refunded included
                  {
                      $this->db->select('sum(fee) as Fee')
						->from('ebay_transactions')
					  ->where_in('et_id', $sold_id);
				  
                     $query = $this->db->get();
                  }
                  elseif($refunded == 0)//without refunded
                  {
                      $this->db->select('sum(fee) as Fee')
						->from('ebay_transactions')
                        ->where('(refunded = 0 or sellingstatus = "PartiallyPaid")')
					 ->where_in('et_id', $sold_id);
				  
                     $query = $this->db->get();
                  }
                  else //with refunded
                  {
        	
				     $this->db->select('sum(fee) as Fee')
						->from('ebay_transactions')
                        ->where('refunded = 1 and sellingstatus <> "PartiallyPaid"')
					 ->where_in('et_id', $sold_id);

                    $query = $this->db->get();
                  }
			}
			else
			{
				
                if(!isset($refunded)) //all transactions, refunded included
                 {
                       $query = $this->db->query('select sum(fee) as Fee
					    from ebay_transactions
				     where mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
                 }
                 elseif($refunded == 0) //without refunded
                 {
                     $query = $this->db->query('select sum(fee) as Fee
	    				from ebay_transactions
    				 where  (refunded = 0 or sellingstatus = "PartiallyPaid") and  mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
                 }
                 else //with refunded
                 {
                     $query = $this->db->query('select sum(fee) as Fee
					    from ebay_transactions
				     where refunded = 1 and sellingstatus <> "PartiallyPaid" and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
                 }
			}
			//echo '<p>'.$this->db->last_query();
 
		}
		elseif($selector==2)//LATRWebsiteOrders
		{
			$query = $this->db->query('select 0 as Fee');
		}
		elseif($selector==3)//Warehouse subchannel=0
		{
			 $query = $this->db->query('SELECT sum(sellingfee) as Fee
									FROM warehouse  LEFT JOIN warehouse_orders ON sold_id=woid
							   WHERE channel = 4 AND sold_id IN
							(SELECT woid FROM warehouse_orders where subchannel=0 and
							timemk >= '.$datefrom.' and timemk <= '.$dateto.')');

		}
		elseif($selector==4)//365 Web subchannel=1
		{
			$query = $this->db->query('SELECT sum(sellingfee) as Fee
									FROM warehouse  LEFT JOIN warehouse_orders ON sold_id=woid
							   WHERE channel = 4 AND sold_id IN
							(SELECT woid FROM warehouse_orders where subchannel=1 and
							timemk >= '.$datefrom.' and timemk <= '.$dateto.')');
		}

		$result_row = $query->first_row();
		return (float)$result_row->Fee;
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

    //NET Profit for one product ONLY for sold in eBay!
    function  SumNetProfitFromEbaybyWID($wid=null, $sold_id=null)
	{
        //if(!empty($wid))
        //{
        //    echo "<p>wid ".$wid.' '.'yes';
                    
        //}
        //else
        //{
        //     echo "<p>wid ".$wid.' '.'no';
        //}

        //return 0;
       //   if($wid == 144778)  echo '<p>wid 144778 is here';
		if(!empty($wid) and !empty($sold_id))
		{
			//$this->db->select('IF(cost > 0 AND transaction_details.paid>0, transaction_details.paid-(cost+fee+shipped_actual+transaction_details.paypal_fee+transaction_details.extra_cost), 0) as Profit', FALSE)
				//					->from('transaction_details')
					//				->join('warehouse','transaction_details.w_id=warehouse.wid')
						//			->where('transaction_details.sold_id = '.$sold_id.' AND transaction_details.channel = 1 and transaction_details.w_id = '.$wid);
                                    //->where_in('transaction_details.sold_id', $eid_array);

            $this->db->select('(transaction_details.paid+ssc/qty)-(cost+transaction_details.fee+shipped_actual+transaction_details.paypal_fee+transaction_details.extra_cost) as Profit', FALSE)
                                    ->from('ebay_transactions')
                                    ->join('transaction_details','transaction_details.sold_id = ebay_transactions.et_id','left')
                                    ->join('warehouse','transaction_details.w_id=warehouse.wid','left')
                                    ->where('transaction_details.sold_id = '.$sold_id.' AND transaction_details.channel = 1 and (sellingstatus="PartiallyPaid" or refunded = 0) and transaction_details.w_id = '.$wid);

            //$this->db->select('(warehouse.paid+ssc/qty)-(cost+warehouse.sellingfee+warehouse.shipped_actual+warehouse.paypal_fee+transaction_details.extra_cost) as Profit', FALSE)
            //                        ->from('warehouse')
            //                        ->join('transaction_details','transaction_details.sold_id = warehouse.sold_id','left')
            //                        ->join('ebay_transactions','transaction_details.sold_id = ebay_transactions.et_id','left')
            //                        ->where('warehouse.sold_id = '.$sold_id.' AND warehouse.channel = 1 and (sellingstatus="PartiallyPaid" or ebay_transactions.refunded = 0) and warehouse.wid = '.$wid);


			$result = $this->db->get();
		   
           // if($wid == 144778)  echo '<p>'.$this->db->last_query();



			$row = $result->first_row();

            return (float)$row->Profit;
        }
		else
		{
			return 0;
		}
			
	}

    function  SumNetProfitFromByWID($wid=null, $sold_id=null, $channel)
	{
	
		if(isset($wid) and isset($sold_id) and isset($channel) and $channel != 1)
		{
			$this->db->select('IF(cost > 0 AND transaction_details.paid>0, transaction_details.paid-(cost+fee+shipped_actual+transaction_details.paypal_fee+transaction_details.extra_cost), 0) as Profit', FALSE)
									->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id = '.$sold_id.' AND transaction_details.channel = '.$channel.' and transaction_details.w_id = '.$wid);
                                    //->where_in('transaction_details.sold_id', $eid_array);
			$result = $this->db->get();
		   //echo '<p>ATTENTION '.$this->db->last_query();
			$row = $result->first_row();

            return (float)$row->Profit;
           
		}
		elseif($channel==1 and isset($wid) and isset($sold_id) )
		{
			$this->SumNetProfitFromEbaybyWID($row['wid'], $row['sold_id']);
		}
        else
        {
            return 0;
        }
			
	}

 	// Cost for sold for a period
	function SumCostWithoutRefunded($datefrom, $dateto, $selector,$sold_id=null)
	{
		if($selector==1)
		{
			if(isset($sold_id))
			{
                   $this->db->select('et_id, cost, wid, qty', FALSE)
									->from('transaction_details')
									->join('warehouse','transaction_details.w_id=warehouse.wid')
									->where('transaction_details.sold_id is not null AND transaction_details.channel = 1')
									->where_in('transaction_details.sold_id', $sold_id);				

					
			        $query = $this->db->get();
		            //echo '<p>'.$this->db->last_query();
			        //$query = $this->db->query("select title, name, body from orders");
                  

         //---------------------------- BUG FIX (esacape to sum cost twise )-------------------------------------------------//

              //This if check for duplicated transactions (transaction with one item has two items in transaction_details table). If  
              //there are duplicates the second one is not entered in detailed_array. Example transaction 52820!


                    $cost = 0;
                    $count_num_prod=0;
                    $sold_id = 0;
                    foreach ($query->result_array() as $row)
                    {
                        if($sold_id == $row['et_id'] and ((int)$count_num_prod+1) > (int)$row['qty'])
                        {
                            continue;
                        }
                        else
                        {
                            if($sold_id == $row['et_id'] or $sold_id==0) $count_num_prod++;
                            else  $count_num_prod=1;
                            $cost += $row['cost'];
                            $sold_id = $row['et_id'];
                       
                        }    
                    }

                 //---------------------------- END BUG FIX  (esacape to sum cost twise )-------------------------------------------------//
                    return (float)$cost;                        
                     //$row = $result->first_row();
                    //echo '<p>'.$this->db->last_query();
			        //return (float)$row->Cost;

            }
            else
            {
                //The query is constructed like the query in function SelectDetailedDataFromWarehouseForEbay in application_vic\controllers\Myreports2.php
                //in order to give exactly the same data for cost for a period.
               // $query = $this->db->query('select sum(cost) as Cost from ebay_transactions
                $query = $this->db->query('select et_id, cost, wid, qty from ebay_transactions
                            LEFT JOIN transaction_details ON transaction_details.sold_id=ebay_transactions.et_id 
                            LEFT JOIN warehouse ON transaction_details.w_id=warehouse.wid 
                            WHERE coalesce(transaction_details.channel, 1) = 1 AND (refunded=0 or sellingstatus="PartiallyPaid") 
                                    and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);


         //---------------------------- BUG FIX  (esacape to sum cost twise ) -------------------------------------------------//
                
                //This if check for duplicated transactions (transaction with one item has two items in transaction_details table). If  
                //there are duplicates the second one is not entered in detailed_array. Example transaction 52820!

                
                //echo "<p>".$this->db->last_query();
                $cost = 0;
                $count_num_prod=0;
                $sold_id = 0;
                foreach ($query->result_array() as $row)
                {
                    //if($row['et_id']==52820)
                    //{
                    //  echo "<p> sold_id".$sold_id;
                    //  echo "<p> count_num_prod ". $count_num_prod;
                    //  echo "<p> cost ". $cost;
                    //  echo "<p> et_id ". $row['et_id'];
                      
                    //}


                    if($sold_id == $row['et_id'] and ((int)$count_num_prod+1) > (int)$row['qty'])
                    {
                        continue;
                    }
                    else
                    {
                        if($sold_id == $row['et_id'] or $sold_id==0) $count_num_prod++;
                        else  $count_num_prod=1;
                        $cost += $row['cost'];
                        $sold_id = $row['et_id'];
                       
                    }    
                }

          //---------------------------- END BUG FIX  (esacape to sum cost twise ) -------------------------------------------------//
               return (float)$cost;

            }

		}

		elseif($selector==2)//LATRWebsiteOrders
		{
            $query = $this->db->query('select sum(cost) as Cost from warehouse where sold_id is not null AND channel = 2 AND wid IN
            (select w_id from transaction_details  where uts <= '.$dateto.' and uts >= '.$datefrom.' and channel = 2)');

            //$query = $this->db->query('select sum(cost) as Cost from warehouse where sold_id is not null AND channel = 2 AND wid IN
            //(select w_id from orders where submittime <= '.$dateto.' and submittime >= '.$datefrom.')');



		}
		elseif($selector==3)//Warehouse subchannel=0
		{
			 $query = $this->db->query('select sum(cost) as Cost
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 4  AND transaction_details.sold_id IN 
			(select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.' and subchannel=0)');

			

		}
		elseif($selector==4)//365 Web subchannel=1
		{
            $query = $this->db->query('select sum(cost) as Cost
										  from transaction_details inner join warehouse on transaction_details.w_id=warehouse.wid where
									transaction_details.sold_id is not null AND transaction_details.channel = 4  AND transaction_details.sold_id IN 
			(select woid from warehouse_orders  where timemk <= '.$dateto.' and timemk >= '.$datefrom.' and subchannel=1)');

		}

        //echo "<p>".$this->db->last_query();

        $result = $query->first_row();
        return (float)$result->Cost;
		
	}

    function  EbayFullRefundTransID($datefrom, $dateto)
	{
        //eBay puts the transactions with sellingstatus="Unpaid" to refund transactions, so we want to show the same thing
        //here.
        //We have flag for not paid transactions in table ebay_transactions column "not paid" = 1, we don't use it here because eBay approach is deifferent.


        $query = $this->db->query('select  et_id  from ebay_transactions
                                  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'
        and (sellingstatus NOT LIKE \'PartiallyPaid\'  or sellingstatus is null) and (refunded=1  or COALESCE(sellingstatus,"")="Unpaid")');

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

    // NEVER USED FUNCTION
    //eBay puts the transactions with sellingstatus="Unpaid" to refund transactions, so we want to show the same thing here.
    //We have flag for not paid transactions in table ebay_transactions column "not paid" = 1, we don't use it here because eBay approach is deifferent.
    function  NotPaidBySellingStatus($datefrom, $dateto)
	{
        $query = $this->db->query('select  et_id  from ebay_transactions
                                  where mkdt <= '.$dateto.' and mkdt >= '.$datefrom.'
        and COALESCE(sellingstatus,"")="Unpaid"');

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

    function TitleFromEbayTable($e_id)
	{
        if($e_id !== null)
        { 
            $query = $this->db->query('select e_title from ebay where e_id = '.$e_id);
            if ($query->num_rows() >0 ) 
            {
               //$query_warehouse_expences = $this->db->get();
                $row = $query->first_row();
                //echo '<p>'.$this->db->last_query();
                return $row->e_title;
            }
			else 
            {
                return FALSE;
            }
        }
        else 
        {
           return FALSE;
        }
    }

    function SumCanceledRefundsEbay($datefrom, $dateto, $sold_id=null)
	{
		if(isset($sold_id))
        {
            $this->db->select('sum(if(paid = 0, eachpaid, paid)) as Refunded')
				->from('ebay_transactions')
                ->where('paidtime','')
					->where_in('et_id', $sold_id);
				  
            $query = $this->db->get();
        }  
        else
        {
            $query = $this->db->query('select sum(if(paid = 0, eachpaid, paid)) as Refunded from ebay_transactions
		    where refunded=1 and paidtime = "" and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
        }

        //echo '<p>'.$this->db->last_query();

        $row  = $query->first_row();
	    return (float)$row->Refunded;
    }

    function SumPartiallyPaidPartiallyRefundedEbay($datefrom, $dateto, $sold_id=null)
	{
		if(isset($sold_id))
        {
            $this->db->select('sum(if(ebayRefundAmount) as Refunded')
				        ->from('ebay_transactions')
                        ->where('sellingstatus', 'PartiallyPaid')
					->where_in('et_id', $sold_id);
				  
            $query = $this->db->get();
        }  
        else
        {
            $query = $this->db->query('select sum(ebayRefundAmount) as Refunded from ebay_transactions
		    where refunded=1 and sellingstatus="PartiallyPaid" and mkdt <= '.$dateto.' and mkdt >= '.$datefrom);
        }

        //echo '<p>'.$this->db->last_query();

        $row  = $query->first_row();
	    return (float)$row->Refunded;
    }

  
        
}
?>