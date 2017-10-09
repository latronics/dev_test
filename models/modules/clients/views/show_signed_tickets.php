<div class="table-responsive">
    <table class="table table-striped">
<thead>
        <tr>
            <th style="padding-left: 200px;"><?php echo lang('quote'); ?></th>
            <th style="text-align: center;"><?php echo lang('signature'); ?></th>
            
           
        </tr>
        </thead>
<?php

$this->db->select("*");
$this->db->from("ip_agreement_terms_x_client");
$this->db->where("id_client", $client->client_id);
$this->db->where("signature <> ''");
$get_tickets_id = $this->db->get();
$ticket_id = $get_tickets_id->result_array();

$i=0;
foreach($ticket_id as $x)
{
   
$this->db->select("*");
$this->db->from("ip_agreement_terms_x_client");
$this->db->join("ip_quotes", "ip_quotes.quote_id = ip_agreement_terms_x_client.id_ticket");
$this->db->order_by("ip_quotes.quote_id","DESC");
$this->db->where("ip_quotes.client_id", $client->client_id);
$this->db->where("ip_agreement_terms_x_client.signature != ''");
$get_ticket_number = $this->db->get();
$ticket_number = $get_ticket_number->result_array();
    

    

   

    ?>

        <tr><td style="padding-left: 140px; font-size:60px;  background-color: white; width: 1px; text-align: left; background-image: url('../../../../assets/default/img/ticket.png'); background-repeat: no-repeat;"><a href="<?php echo site_url('quotes/view/' . $ticket_number[$i]['quote_id']); ?>"
                       title="<?php echo lang('edit'); ?>">
                        <?php  echo $ticket_number[$i]['quote_number']."<br>"; ?>
                </a></td>
        <td style="text-align: center; background-color: white; width: 400px; height: 200px;"><img src="data:image/png;base64,<?php echo $ticket_id[$i]['signature'] = str_replace("[removed]", "", $ticket_id[$i]['signature']); ?>">
            
                 </td>
        </tr>



<?php
 $i++;
}
   
   
   

