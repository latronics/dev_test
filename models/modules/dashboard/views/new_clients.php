 <table class="table table-condensed no-margin" >
<?php

$i = 0; 
                    
                    
                    //foreach ($result_object as $total) {   
                        
                                  
                                
                                   
                                    
                                    
                                    $this->db->select("client_name, client_id");
                                    $this->db->from("ip_clients");
                                    $this->db->where("new_client = '1'");
                                    $result_object = $this->db->get();
                                    $result = $result_object->result_array();
                                    $rows = $result_object->num_rows();
                                    if($rows == ''){
                                        echo "<tr><td align = 'center'><i><font size = '2'>No new clients</font></i></td></tr>";
                                    }
                                       while($i < $rows){  
                                           ?>
                   <tr><td>
                           <form>
                               
                                      <a href ="#" onclick = "getValue(<?php echo $result[$i]["client_id"];  ?>)" id = "client_id"> <?php echo $result[$i]["client_name"]; ?> </a>
                           </form>
                                    <?php
                                      
                                    $i++;
                                     }
                                     
                                     
                                     
                                     ?>
                       </td></tr></table>