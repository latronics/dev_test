<form method="post" action="<?php echo site_url('clients/register_client_terminal'); ?>" >
<br /><br />




    
  <table class="basic-grey"   border ="0" style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;">
      <tr><td colspan="4" align="center" ><br><img src="<?php echo base_url('application/images/logo.png'); ?>" />
             <strong><h3><?php greetings(); ?></h3> <br><h3>Please make your register in our system.</h3></strong></td><br><br>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
        
        <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;">E-mail:<span style="color:#F00;">*</span></td>
      <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;"><input type="text" size="20" name="Email" ></td>
      <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;">First Name:<span style="color:#F00;">*</span></td>
      <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;"><input type="text" size="20" name="FirstName" /></td>
    </tr>
    <tr>
      <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;">Phone Number:<span style="color:#F00;">*</span></td>
      <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;"><input type="text" size="20" name="PhoneNumber" /></td>
      <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;">Last Name:<span style="color:#F00;">*</span></td>
      <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;"><input type="text" size="20" name="LastName" /></td>
    </tr>
   
    
    <tr>
        <td align="center" colspan="4"><?php if($warnings!= "") { echo "<p style='color: red; font-size: 18px;'>".$warnings."</p>"; ?> <META http-equiv="refresh" content="2;URL=<?php echo base_url('index.php/clients/clientTerminal'); ?>">     <?php } else if($congrats != "") { echo "<p style='color: green; font-size: 18px;'>".$congrats."</p>"; ?> <META http-equiv="refresh" content="2;URL=<?php echo base_url('index.php/clients/clientTerminal'); ?>">  <?php } ?><br><input class="basic-grey button" type="submit" value="Submit" style="width: 200px; height: 100px;" ><br>&nbsp;</td>
    </tr>
  </table>