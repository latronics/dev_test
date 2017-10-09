
<?php
$this->db->select("*");
$this->db->from("ip_agreement_client_on_turn");
$get_ticket_on_turn = $this->db->get();
$ticket_on_turn = $get_ticket_on_turn->result_array();




$client_id = $data;
$this->db->select("*");
$this->db->from("ip_agreement_terms_x_client");
$this->db->join("ip_clients", "ip_clients.client_id = ip_agreement_terms_x_client.id_client");
$this->db->join("ip_quotes", "ip_quotes.quote_id = ip_agreement_terms_x_client.id_ticket");
$this->db->where("ip_agreement_terms_x_client.id_client", $client_id);
$this->db->where("ip_agreement_terms_x_client.id_ticket", $ticket_on_turn[0]['ticket_id']);



//$this->db->where("ip_agreement_terms_x_client.id_ticket", $ticket_on_turn[0]['id_ticket']);

$client_id_query = $this->db->get();
$ip_agreement_terms_x_client = $client_id_query->result_array();
$rows = $client_id_query->num_rows();
$count = 0;
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="<?php echo base_url('assets/default/js/signature_pad.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="../../../../assets/default/css/style.css" />

<script type="text/javascript">
    
 $(document).ready(function () {
        
        setInterval(function () {
            $.post("<?php echo site_url('clients/clients/update_agreements_terminal'); ?>", {
                ticket_id: 0

            },
                    function (data) {
                        if (data == "true")
                        {
                            location.reload();
                        }
                    });
        }, 1000);



    });

   function reload_page()
   {
       location.reload();
   }
    
    window.onload = function canvas_signature()
    {

        var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });
        var saveButton = document.getElementById('save');
        var cancelButton = document.getElementById('clear');
        var form = document.getElementById("sigform");
        var input = document.getElementById("siginput");


        saveButton.addEventListener('click', function (event) {
            var signature = signaturePad.toDataURL('image/png');
            if (signaturePad.isEmpty()) {
                alert("Please provide your signature first.");
            } else {
                input.value = signature;
                //window.open(signature);
                form.submit();


            }





            //setTimeout("window.open('<?php echo site_url('clients/clients/eraseagreements'); ?>', '_self')",3000);
        });

        cancelButton.addEventListener('click', function (event) {
            signaturePad.clear();
        });
    };


</script>
<!--<table  cellpadding="2" cellspacing="2" border="0" style="font-size:13px;  font-family: Tahoma, Geneva, sans-serif; margin-top:10px; " align="center" >
    <tr><td><button id="reload" onclick="reload_page()" class="btn btn-success" style="background-color:#00CC00; width: 200px; height:80px;">RELOAD</button></td></tr>
</table>-->
<table  cellpadding="2" cellspacing="2" border="0" style="font-size:13px;  font-family: Tahoma, Geneva, sans-serif;" align="center" >

    <tr style="padding:10px; text-align: center; background: #2093c4;"><td  colspan="2"><b>365 Laptop Repair - (STORE HERE) - Order: <?php echo $ip_agreement_terms_x_client[$count]['quote_number']; ?></b></td></tr><br>
    <br>
    <tr style="padding:10px; text-align: center; background: #2093c4;"><td colspan="2"><b>(STORE, ADDRESS AND PHONENUMBER HERE)</b></td></tr><br>



    <tr><td colspan="2">

            <span style="font-size:12px;"><em>A new account has been created for you. You can view the status of your order from the members area.</em></span><br /><br />
            <div style="background:#EFEFEF; padding:5px;"><strong>Username:</strong> <?php echo $ip_agreement_terms_x_client[$count]['client_email']; ?>&nbsp;&nbsp;/&nbsp;&nbsp;
                <strong>Password:</strong> (YOUR PHONE NUMBER)</div>


        </td></tr><tr>
        <td colspan="2"><strong>Customers Name:</strong> <?php echo $ip_agreement_terms_x_client[$count]['client_name']; ?></td>
    </tr>
    <tr>
        <td width="50%"><strong>Phone Number:</strong> <?php echo $ip_agreement_terms_x_client[$count]['client_phone']; ?></td>
        <td width="50%"><strong>Email:</strong> <?php echo $ip_agreement_terms_x_client[$count]['client_email']; ?></td>
    </tr>



    <tr>
        <td colspan="2"><strong>Accessories included:</strong> <?php echo $ip_agreement_terms_x_client[$count]['accessories_included']; ?></td>
    </tr>
    <tr>
        <td colspan="2"><strong>Data Recovery:</strong> <?php if ($ip_agreement_terms_x_client[$count]['data_recovery'] == 0) {
    echo "No";
} else {
    echo "Yes";
} ?></td>
    </tr>
    <tr>
        <td colspan="2" style="font-size:10px;"><em>We are not responsible under any circumstances for any loss, alteration, or corruption of any software, data, or files.</em></td>
    </tr>
    <tr>
        <td><strong>Computer Brand:</strong> <?php echo $ip_agreement_terms_x_client[$count]['brand']; ?></td>
        <td><strong>Computer Model:</strong> <?php echo $ip_agreement_terms_x_client[$count]['model']; ?></td>
    </tr>
    <tr>
        <td><strong>Serial Number:</strong> <?php echo $ip_agreement_terms_x_client[$count]['serial_number']; ?></td>
        <td><strong>Cost:</strong> <?php echo $ip_agreement_terms_x_client[$count]['amount']; ?></td>
    </tr>
    <tr>
        <td><strong>Problem Description:</strong> <?php echo $ip_agreement_terms_x_client[$count]['problem_description_product']; ?></td>
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
        <td align="right"><strong>Date:</strong> <?php echo date_from_mysql($ip_agreement_terms_x_client[$count]['quote_date_created']); ?> / Order: <?php echo $ip_agreement_terms_x_client[$count]['quote_number']; ?></td>


    </tr></table>
<br><br>









<table align="center"><tr><td align="center"> <h3>
                Please sign here.
            </h3>
            <div class="wrapper" >
                <img src="../../../../assets/default/img/page-white.png" width=400 height=200 />
                <canvas id="signature-pad" class="signature-pad" width=400 height=200></canvas>
            </div>
            <div>


            </div></td></tr></table>

<table><tr><td><hr style="width: 100%;"/></td></tr></table>


<table align = "center"><tr><td><button id="save"  class="basic-grey button" style="width: 200px; height: 100px; font-size: 22px; font-weight: bold;" >Submit</button></td><td>&nbsp;</td>
        <td><button id="clear" class="basic-grey button" style="width: 200px; height: 100px; font-size: 22px; font-weight: bold;">Clear</button></td></tr>
</table>

<table><tr><td>&nbsp;</td></tr></table>
<table><tr><td>&nbsp;</td></tr></table>
<form id="sigform" action="<?php echo site_url('clients/receive_signature/' . $client_id . '/' . $ip_agreement_terms_x_client[0]['quote_id']) ?>" method="post">
    <input id="siginput" type="hidden" name="sig" value="" />
</form>
