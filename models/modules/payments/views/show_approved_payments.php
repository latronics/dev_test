<div id="headerbar">
    <h1>Approved Payments Log</h1>
    
</div>



<div id="headerbar">

    <div name ="divtest" id = "divtest"> </div>
    


    
    <div id="content" class="table-content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="table-responsive">
            <table class="table table-striped" >

                <thead>
                    <tr>
                        <th>STATUS</th>
                        <th>Transaction ID</th>
                        <th>Card Digits</th>
                        <th>Card Type</th>
                        <th>Amount</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Market</th>
                        
                    </tr>
                </thead>

                <tbody class="sortable">




                    <?php
                   
                    foreach ($data_approved_payments as $data_approved_payments) {
                        ?>
                    <tr>
                        <td><font color='green'><?php echo $data_approved_payments['status']; ?></font></td>
                        <td><?php echo $data_approved_payments['trans_id']; ?></td>
                        <td><?php 
        
        $rest = substr($data_approved_payments['card_digits'], 0, 4);  // returna "123"
        $rest2 = substr($data_approved_payments['card_digits'], 4, 4);  // returna "123"
        $rest3 = substr($data_approved_payments['card_digits'], 8, 4);  // returna "123"
        $rest4 = substr($data_approved_payments['card_digits'], 12, 4);  // returna "123"
        
        
        echo str_replace($rest, "x","****")." ".str_replace($rest2, "x","****")." ".str_replace($rest3, "x","****")." ".$rest4; ?></td>
                        <td><?php if($data_approved_payments['card_type'] == "MC") { echo str_replace($data_approved_payments['card_type'], "MC","MASTERCARD");} else {echo $data_approved_payments['card_type']; }?></td>
                        <td><?php echo "$".number_format($data_approved_payments['amount'], 2); ?></td>
                         <td><?php echo $data_approved_payments['message']; ?></td>
                         <td><?php echo $data_approved_payments['date']; ?></td>
                         <td><?php if($data_approved_payments['message'] == "Paypal Approved") {echo "PAYPAL";} else if($data_approved_payments['website'] == 1){ echo "Website"; } else { echo "Direct Sale";} ?></td>
                    
                    
                    
                    
                    </tr>








                        <?php
}
?>




                </tbody>

            </table>
        </div>
    </div>

 


<?php

//print_r($data_approved_payments);