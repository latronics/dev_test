<div class="loader"></div>
<div id ="general">
        <div id="headerbar">
            <h1>Process Payment Form</h1>
            <div class="pull-right btn-group">
                <button id="btn-submit" name="btn_submit" class="btn btn-success btn-sm " value="1">
                    <i class="fa fa-check"></i> <?php echo lang('save'); ?>
                </button>
                <button id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm" value="1">
                    <i class="fa fa-times"></i> <?php echo lang('cancel'); ?>
                </button>
            </div>
        </div>

    <div id ="first_name_validate" class="alert alert-danger" hidden style="margin-top:10px;">First Name is Required.</div>
    <div id ="routing_number_validate" class="alert alert-danger" hidden style="margin-top:10px;">Routing Number is Required.</div>
    <div id ="account_number_validate" class="alert alert-danger" hidden style="margin-top:10px;">Account Number is Required.</div>
    <div id ="amount_validate" class="alert alert-danger" hidden style="margin-top:10px;">Amount is Required.</div>
    
        <table border="0" align="center" style="margin-top:10px;"> 
            <tr><td colspan="4" style="padding-bottom: 10px;" >
                    <h3 class="control-label" align="center">Personal information</h3>

                </td></tr>
            <tr><td style="padding-left:18px;">
                    <label class="control-label">First Name<font color="red">*</font></label> 
                    
                </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="fname" class="form-control" style="width:300px; "/>

                    </div></td>


                <td style="padding-left:18px;">
                    <label class="control-label">Last Name</label> </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="lname" class="form-control" style="width:300px; "/>

                    </div></td>
            </tr>
            <tr><td style="padding-left:18px;">
                    <label class="control-label">First Address</label> </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="faddress" class="form-control" style="width:300px; "/>

                    </div></td>


                <td style="padding-left:18px;">
                    <label class="control-label">Second Address</label> </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="saddress" class="form-control" style="width:300px; "/>

                    </div></td>
            </tr>
            <tr><td style="padding-left:18px;">
                    <label class="control-label">City</label> </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="city" class="form-control" style="width:300px; "/>

                    </div></td>


                <td style="padding-left:18px;">
                    <label class="control-label">State</label> </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="state" class="form-control" style="width:300px; "/>

                    </div></td>

            <tr><td style="padding-left:18px;">
                    <label class="control-label">Zip</label> </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="zip" class="form-control" style="width:300px; "/>

                    </div></td>


                <td style="padding-left:18px;">
                    <label class="control-label">Country</label> </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="country" class="form-control" style="width:300px; "/>

                    </div></td>

            </tr>
            <tr><td style="padding-left:18px;">
                    <label class="control-label">Phone</label> </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="phone" class="phone form-control" style="width:300px; "/>

                    </div></td>


                <td style="padding-left:18px;">
                    <label class="control-label">Email</label> </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="email" class="form-control" style="width:300px;"/>

                    </div></td>

            </tr>
            <tr><td colspan="4" style="padding-bottom: 10px; padding-top: 10px;" >
                    <h3 class="control-label" align="center">Check information</h3></tr>
            <tr><td style="padding-left:18px;">
                    <label class="control-label">Routing Number<font color="red">*</font></label> 
                
                </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="routing_number" class="routing_number form-control" style="width:300px; "/>

                    </div></td>


                <td style="padding-left:18px;">
                    <label class="control-label">Account Number<font color="red">*</font></label> 
                
                </td>
                <td style="padding-bottom:5px;">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="account_number" class="account_number form-control" style="width:300px;"/>

                    </div></td>



            </tr>
            
            <tr><td colspan="4" style="padding-bottom: 10px; padding-top: 10px;" >
                    <h3 class="control-label" align="center">Amounts</h3></tr>
            <tr><td style="padding-left:18px;" >
                    <label class="control-label">Total<font color="red">*</font></label> 
                
                </td>
                <td style="padding-bottom:5px;" colspan="2">
                    <div class="col-xs-12 col-sm-6">


                        <input type="text" id ="amount" class="form-control" style="width:300px;" value="<?php echo $_POST['AMOUNT']; ?>"/>
                        <input type="text" id = "note" value="<?php echo $_POST['note']; ?>" hidden/>
                        <input type="text" id = "invoice_id" value="<?php echo $_POST['invoice_id_bluepay']; ?>" hidden/>
                    </div></td></tr>
            </td></tr>
        </table>
    </div>
    <div id = "result_payment_process"></div>