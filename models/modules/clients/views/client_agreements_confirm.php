<?php
$this->db->select("*");
$this->db->from("ip_agreement_terms_x_client");
$this->db->where("id_client", $agreements_data[0]['client_id']);
$this->db->where("id_ticket", $agreements_data[0]['quote_id']);
$get_signature = $this->db->get();
$signature = $get_signature->result_array();
$signature_ready = str_replace("[removed]", "", $signature[0]['signature']);

$date = date("m-d-Y", strtotime($agreements_data[0]['quote_date_created']));
?>
 <style type="text/css">
    .wrapper {
  position: relative;
  width: 400px;
  height: 200px;
  -moz-user-select: none;
  -webkit-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
img {
  position: absolute;
  left: 0;
  top: 0;
}

.signature-pad {
  position: absolute;
  left: 0;
  top: 0;
  width:400px;
  height:200px;
}
  </style>


 <link rel="stylesheet" type="text/css" href="../../../../assets/default/css/style.css" />

   
 
    
<table  cellpadding="2" cellspacing="2" border="0" style="font-size:13px;  font-family: Tahoma, Geneva, sans-serif;" align="center" >
    
    <tr style="padding:10px; text-align: center; background: #2093c4;"><td  colspan="2"><b>365 Laptop Repair - (STORE HERE) - Order: <?php  echo $agreements_data[0]['quote_number']; ?></b></td></tr><br>
<br>
<tr style="padding:10px; text-align: center; background: #2093c4;"><td colspan="2"><b>(STORE, ADDRESS AND PHONENUMBER HERE)</b></td></tr><br>


  
  <tr><td colspan="2">
  
<span style="font-size:12px;"><em>A new account has been created for you. You can view the status of your order from the members area.</em></span><br /><br />
<div style="background:#EFEFEF; padding:5px;"><strong>Username:</strong> <?php  echo $agreements_data[0]['client_email']; ?>&nbsp;&nbsp;/&nbsp;&nbsp;
<strong>Password:</strong> (YOUR PHONE NUMBER)</div>


</td></tr><tr>
    <td colspan="2"><strong>Customers Name:</strong> <?php  echo $agreements_data[0]['client_name']; ?></td>
  </tr>
  <tr>
    <td width="50%"><strong>Phone Number:</strong> <?php  echo $agreements_data[0]['client_phone']; ?></td>
    <td width="50%"><strong>Email:</strong> <?php  echo $agreements_data[0]['client_email']; ?></td>
  </tr>
  
 
 
  <tr>
    <td colspan="2"><strong>Accessories included:</strong> <?php  echo $agreements_data[0]['accessories_included']; ?></td>
  </tr>
  <tr>
    <td colspan="2"><strong>Data Recovery:</strong> <?php  if($agreements_data[0]['data_recovery'] == 0){ echo "No"; } else { echo "Yes"; } ?></td>
  </tr>
  <tr>
    <td colspan="2" style="font-size:10px;"><em>We are not responsible under any circumstances for any loss, alteration, or corruption of any software, data, or files.</em></td>
  </tr>
  <tr>
    <td><strong>Computer Brand:</strong> <?php  echo $agreements_data[0]['brand']; ?></td>
    <td><strong>Computer Model:</strong> <?php  echo $agreements_data[0]['model']; ?></td>
  </tr>
  <tr>
    <td><strong>Serial Number:</strong> <?php  echo $agreements_data[0]['serial_number']; ?></td>
      <td><strong>Cost:</strong> <?php  echo $agreements_data[0]['amount']; ?></td>
    </tr>
    <tr>
    <td><strong>Problem Description:</strong> <?php echo $agreements_data[0]['problem_description_product'];  ?></td>
  </tr>
  
  </table><br /><br />
 
 
  <table width="630" cellpadding="2" cellspacing="2" border="0" style="font-size:13px;  font-family: Tahoma, Geneva, sans-serif;" align="center">
  <tr>
    <td colspan="2" style="font-size:12px;"><br />
      <br />
      <strong>1.Diagnostic</strong> - I understand, that there is no  diagnostics fee charged only if the proposed estimated price to complete the  repair is approved. If i decline the proposed repair cost or any additional  repair(s) needed, 365LaptopRepair.com will refund the service fee minus a $49  diagnostic fee. If parts were ordered to determine the problem with my  computer, the cost associated with them is not refundable.<br />
      <br />
      <strong>2.Customers Data</strong>- 365LaptopRepair.com strives to protect customer data. However, I  understand that as customer, I am solely responsible for my data, including  backing up all data prior to sending my laptop to 365LaptopRepair.com for  repair. I understand that if I am unable to back up the data, I will notify  365LaptopRepair.com.365LaptopRepair.com is not responsible for any software or  data.<br />
      <br />
      <strong>3.Parts</strong> - New, used, or refurbished parts may  be used in the repair of my laptop. All parts will have a 30day limited  warranty from 365LaptopRepair.com or more if specified. I understand  365LaptopRepair.com is not responsible for keeping or returning defective parts  that are replaced as parts of the repair, unless specifically requested prior  to part ordering.<br />
      <br />
      <strong>4.Limitation of Liability</strong> - To the extent permitted by law you agree that 365LaptopRepair.com's total  liability for damages related to its services is limited to the total amount  you pay for these services (plus parts if applicable), and you release  365LaptopRepair.com from liability for any indirect, incidental, special or  consequential damages. 365LAPTOPREPAIR.COM IS NOT LIABLE FOR LOSS, ALTERATION,  AND ORCORRUPTION OF ANY DATA OR FOR YOUR INABILITY TO USE YOUR COMPUTER  EQUIPMENT OR OTHER PRODUCT.<br />
      <br />
      <strong>5.Abandonment of  Property</strong> - If you fail to make contact with  365LaptopRepair.com for 60 consecutive days regarding the disposition of your  laptop, 365LaptopRepair.com will consider your inaction as abandonment of  property. You agree that 365LaptopRepair.com may take ownership and/ or dispose  of such abandoned property and hereby release 365LaptopRepair.com and waive any  claims regarding such disposal.<br />
      <br />
      <strong>6.System Warranty</strong> - A laptop system  purchased from 365 Laptop Repair can't be returned. The product has a 90 days  warranty and if something happens, 365 Laptop Repair will repair the laptop at  no additional cost. The accessories with the laptop do not carry any warranty.  That would be laptop bags recovery CD's or AC adapter/charger. <br />
      <br />
      I understand some service  related problems, including viruses; spyware, adware and other software issues  may not be removable or repairable. In the event the computer's operating  system may need to be reloaded, I will include the installation discs/recovery  media that came with my computer. I understand 365LaptopRepair.com's 100%  satisfaction guarantee does not apply to software issues. While most repairs  fall under &quot;flat rate&quot; service offered by 365LaptopRepair.com, I  understand that some repairs are not covered. Such repairs include, but are not  limited to; multiple defective parts, motherboard replacement and LCD display  replacement. In such case, 365LaptopRepair.com will notify me with a quotation  of the additional repair costs.<br />
      <br /><br /><br /><br /></td>
  </tr>
  <tr>
    <td align="right"><strong>Date:</strong> <?php echo $date;  ?> / Order: <?php  echo $agreements_data[0]['quote_number']; ?></td>
    
    
  </tr></table>
<br><br>

      
   
	
	
  


      
<table align="center"><tr><td align="center"> <h3>
Please sign here.
</h3>
<div class="wrapper" >
    <img src="data:image/png;base64,<?php echo $signature_ready ?>" width=400 height=200 />
  
</div>
<div>
 
  
</div></td></tr></table>

        <table><tr><td><hr style="width: 100%;"/></td></tr></table>
  
 
    
