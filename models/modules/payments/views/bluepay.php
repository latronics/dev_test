<?php
//GET TICKET NUMBER FROM INVOICE
$this->db->select("*");
$this->db->join("ip_quotes", "ip_quotes.quote_id = ip_invoices.ticket_id");
$this->db->where("ip_invoices.invoice_id", $_POST['invoice_id_bluepay']);
$ticket_number = $this->db->get("ip_invoices")->result_array();


$this->db->select("*");
$this->db->where("payment_method_id", $_POST['payment_type']);
$data_payment_type = $this->db->get("ip_payment_methods")->result_array();

if ($data_payment_type[0]['payment_method_type'] == "card") {

    $this->load->view("process_cards");
} else if ($data_payment_type[0]['payment_method_type'] == "check") {
    $this->load->view("process_checks");
}
?>

<script src="../../../../assets/default/js/jquery.maskMoney.js" type="text/javascript"></script>
<script>
 $("input.ccv_code").keyup(function () {
        var jThis = $(this);
        var notNumber = new RegExp("[^0-9]", "g");
        var val = jThis.val();

        //Math before replacing to prevent losing keyboard selection 
        if (val.match(notNumber))
        {
            jThis.val(val.replace(notNumber, ""));
        }
    }).keyup(); //Trigger on page load to sanitize values set by server




    $("input.card_number").keyup(function () {
        var jThis = $(this);
        var notNumber = new RegExp("[^0-9]", "g");
        var val = jThis.val();

        //Math before replacing to prevent losing keyboard selection 
        if (val.match(notNumber))
        {
            jThis.val(val.replace(notNumber, ""));
        }
    }).keyup(); //Trigger on page load to sanitize values set by server

    $("input.phone").keyup(function () {
        var jThis = $(this);
        var notNumber = new RegExp("[^0-9]", "g");
        var val = jThis.val();

        //Math before replacing to prevent losing keyboard selection 
        if (val.match(notNumber))
        {
            jThis.val(val.replace(notNumber, ""));
        }
    }).keyup(); //Trigger on page load to sanitize values set by server

    $("input.expire_date").keyup(function () {
        var jThis = $(this);
        var notNumber = new RegExp("[^0-9]", "g");
        var val = jThis.val();

        //Math before replacing to prevent losing keyboard selection 
        if (val.match(notNumber))
        {
            jThis.val(val.replace(notNumber, ""));
        }
    }).keyup(); //Trigger on page load to sanitize values set by server

    $("input.routing_number").keyup(function () {
        var jThis = $(this);
        var notNumber = new RegExp("[^0-9]", "g");
        var val = jThis.val();

        //Math before replacing to prevent losing keyboard selection 
        if (val.match(notNumber))
        {
            jThis.val(val.replace(notNumber, ""));
        }
    }).keyup(); //Trigger on page load to sanitize values set by server

    $("input.account_number").keyup(function () {
        var jThis = $(this);
        var notNumber = new RegExp("[^0-9]", "g");
        var val = jThis.val();

        //Math before replacing to prevent losing keyboard selection 
        if (val.match(notNumber))
        {
            jThis.val(val.replace(notNumber, ""));
        }
    }).keyup(); //Trigger on page load to sanitize values set by server

    function limitarInput(box, limit) {

        var contagem_carac = box.length;
        if (contagem_carac >= limit) {


            document.getElementById("ccv_code").maxLength = "3";
            document.getElementById("card_number").maxLength = "16";
            document.getElementById("expire_date").maxLength = "4";

        }
    }
   
    $(function () {
$("#amount").maskMoney();

        
        $("#btn-submit").click(function () {
           
            if ($("#fname").val() === "") {

                $("#first_name_validate").show();
                setTimeout(function () {
                    location.reload();
                }, 2000);
            } else if ($("#card_number").val() === "")
            {
                $("#card_number_validate").show();
                setTimeout(function () {
                    location.reload();
                }, 2000);
            } else if ($("#expire_date").val() === "")
            {
                $("#expire_date_validate").show();
                setTimeout(function () {
                    location.reload();
                }, 2000);
            } else if ($("#ccv_code").val() === "")
            {
                $("#ccv_validate").show();
                setTimeout(function () {
                    location.reload();
                }, 2000);
            } else if ($("#amount").val() === "")
            {
                $("#amount_validate").show();
                setTimeout(function () {
                    location.reload();
                }, 2000);
            } else if ($("#routing_number").val() === "")
            {
                $("#routing_number_validate").show();
                setTimeout(function () {
                    location.reload();
                }, 2000);
            } else if ($("#account_number").val() === "")
            {
                $("#account_number_validate").show();
                setTimeout(function () {
                    location.reload();
                }, 2000);
            } else

            {

                $.post("<?php echo site_url('payments/process_bluepay'); ?>", {
                    fname: $("#fname").val(),
                    lname: $("#lname").val(),
                    faddress: $("#faddress").val(),
                    saddress: $("#saddress").val(),
                    city: $("#city").val(),
                    state: $("#state").val(),
                    zip: $("#zip").val(),
                    country: $("#country").val(),
                    phone: $("#phone").val(),
                    email: $("#email").val(),
                    card_number: $("#card_number").val(),
                    expire_date: $("#expire_date").val(),
                    ccv_code: $("#ccv_code").val(),
                    amount: $("#amount").val(),
                    ticket_id: <?php if($ticket_number[0]['quote_number'] == "") {echo "0";} else {echo $ticket_number[0]['quote_number'];} ?>,
                    invoice_id: $("#invoice_id").val(),
                    payment_method_id: <?php echo $data_payment_type[0]['payment_method_id']; ?>,
                    note: $("#note").val(),
                    routing_number: $('#routing_number').val(),
                    account_number: $('#account_number').val()




                },
                        function (data) {

                            //alert(data);
                            $("#general").hide();
                            $("#result_payment_process").html(data);
                        });

            }
        });



        $("#btn-cancel").click(function () {


            window.open('form', '_self');


        });

    });


</script>