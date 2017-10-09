<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo lang('payment_method_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
            <?php if ($this->mdl_payment_methods->form_value('is_update')) {
                echo 'value="1"';
            } else {
                echo 'value="0"';
            } ?>
        >

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_method_name" class="control-label">
                    <?php echo lang('payment_method'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="payment_method_name" id="payment_method_name" class="form-control"
                       value="<?php echo $this->mdl_payment_methods->form_value('payment_method_name'); ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs" style="margin-right:15px;">
                <label for="payment_method_type" class="control-label">
                    <?php echo lang('payment_type'); ?>:
                </label>
            </div>
            
               
            <select name="payment_method_type" class="input-sm form-control" style="width:auto;">
                    
                    <option value="card" <?php if($this->mdl_payment_methods->form_value('payment_method_type') == "card") {echo "selected"; } ?>>Card</option>
                  <option value="check" <?php if($this->mdl_payment_methods->form_value('payment_method_type') == "check") {echo "selected"; } ?>>Check</option>
                  <option value="paypal" <?php if($this->mdl_payment_methods->form_value('payment_method_type') == "paypal") {echo "selected"; } ?>>Paypal</option>
                  <option value="cash" <?php if($this->mdl_payment_methods->form_value('payment_method_type') == "cash") {echo "selected"; } ?>>Cash</option>
                </select>
            
        </div>

    </div>

</form>