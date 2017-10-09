<?php $this->layout->load_view('clients/jquery_client_lookup'); ?>

<script type="text/javascript">
    $(function () {
        // Display the create invoice modal
        $('#change-client').modal('show');
        $("#client_name").focus();
        $('#client_name').autocomplete({
            source: '<?php echo site_url('quotes/ajax/customer_search'); ?>'
        });
        $('#change-client').on('shown', function () {

        });



        // Creates the invoice
        $('#client_change_confirm').click(function () {
            // Posts the data to validate and create the invoice;
            // will create the new client if necessary
            $.post("<?php echo site_url('quotes/ajax/change_client'); ?>", {
                client_name: $('#client_name').val(),
                quote_id: $('#quote_id').val()
            },
                    function (data) {
<?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                        var response = JSON.parse(data);
                        if (response.success == '1') {
                            // The validation was successful and invoice was created
                            window.location = "<?php echo site_url('quotes/view'); ?>/" + response.quote_id;
                        } else {
                            // The validation was not successful
                            $('.control-group').removeClass('has-error');
                            for (var key in response.validation_errors) {
                                $('#' + key).parent().parent().addClass('has-error');
                            }
                        }
                    });
        });
    });

</script>

<div id="change-client" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_create_invoice" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo lang('change_client'); ?></h3>
        </div>
        <div class="modal-body">

            <div class="form-group">


            </div>
            <input type="text" name="client_name" id="client_name" class="form-control" autofocus>
            <input class="hidden" id="quote_id" value="<?php echo $quote_id; ?>">

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo lang('cancel'); ?>
                </button>
                <button class="btn btn-success" id="client_change_confirm" type="button">
                    <i class="fa fa-check"></i> <?php echo lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
