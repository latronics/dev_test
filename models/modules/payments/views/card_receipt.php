<?php  
$this->db->select("*");
$this->db->join("ip_stores","ip_stores.id = ip_users.user_store");
$this->db->where("user_id",$this->session->userdata('user_id'));
$store_data = $this->db->get("ip_users")->result_array();


?>

<body onload="ClosePrint()">
    <table border="1" style="font-size: 20px; width: 500px; padding-left:20px;">
        <tr><td style="border-left:1px;  border-top:0px; border-bottom:0px; border-right: 1px;"><?php echo $store_data[0]['store_name'] ?>  </td></tr>
        <tr><td  style="border-left:1px;  border-top:0px; border-bottom:0px; border-right: 1px;"><?php echo $store_data[0]['store_address'] ?> </td></tr>
        <tr><td  style="border-left:1px;  border-top:0px; border-bottom:0px; border-right: 1px;"><?php echo $store_data[0]['store_phone'] ?> </td></tr>
        <tr><td  style="border-left:1px;  border-top:0px; border-bottom:0px; border-right: 1px;"><b>AID: </b><?php echo $data_receipt_print[0]['trans_id']; ?></td></tr>
        <tr><td style="padding-bottom:20px; border-left:1px;  border-top:0px; border-bottom:0px; border-right: 1px;"><?php if($data_receipt_print[0]['card_type'] == "MC") { echo str_replace($data_receipt_print[0]['card_type'], "MC","MASTERCARD");} else echo $data_receipt_print[0]['card_type']; ?></td></tr>
        <tr style="border-left:1px;  border-top:0px; border-bottom:0px; border-right: 1px;"><td style="font-size:26px; border-left:1px;  border-top:0px; border-bottom:0px; border-right: 1px;"><b><?php if($data_receipt_print[0]['card_type'] == "MC") { echo str_replace($data_receipt_print[0]['card_type'], "MC","MASTERCARD");} else { echo $data_receipt_print[0]['card_type']; } ?></b></td></tr>
        
        
        <tr><td <td  style="border-left:1px;  border-top:0px; border-bottom:0px; border-right: 1px;"><?php 
        
        $rest = substr($data_receipt_print[0]['card_digits'], 0, 4);  // returna "123"
        $rest2 = substr($data_receipt_print[0]['card_digits'], 4, 4);  // returna "123"
        $rest3 = substr($data_receipt_print[0]['card_digits'], 8, 4);  // returna "123"
        $rest4 = substr($data_receipt_print[0]['card_digits'], 12, 4);  // returna "123"
        
        
        echo str_replace($rest, "x","****")." ".str_replace($rest2, "x","****")." ".str_replace($rest3, "x","****")." ".$rest4; ?></td></tr>
        <tr><td <td  style="border-left:1px;  border-top:0px; border-bottom:0px; border-right: 1px;"><b>Sale: </b><?php echo "$".$data_receipt_print[0]['amount']; ?></td></tr>



    </table>
    
</body>

<script>
  
    function ClosePrint() {
        window.print();
        setTimeout(function () {
            window.close();
        }, 0);
        
    }

</script>