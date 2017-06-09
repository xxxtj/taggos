<?$c = new Controller;?>
<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Project report</h4> 
                            </div>  
                            <div class="card"> 
                                <div class="card-content">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <ul class="nav nav-stacked" role="tablist">
                                                <?$i=1;?>
                                                <?php foreach ($render->elements as $element): ?>
                                                    <li class="<?=($i ==1) ? 'active' : ''?>">
                                                        <a href="#element<?=$i++;?>" role="tab" data-toggle="tab" aria-expanded="false"><?=$element->selector?> (<?=$element->action?>)</a>
                                                    </li>
                                                <?php endforeach ?>    
                                            </ul>
                                        </div>
                                        <div class="col-md-8">
                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <?$x=1;?>
                                                <?php foreach ($render->elements as $element): ?> 
                                                    <div class="tab-pane <?=($x==1) ? 'active' : ''?>" id="element<?=$x++;?>"> 
                                                        <table class="table table-striped">
                                                            <thead> 
                                                                <?$attr_array = array();?>
                                                                <?$attr = $c->db->getAll("SELECT * FROM attributes WHERE element_id = ?i ORDER BY id ASC", $element->id); ?>
                                                                <th>Date</th> 
                                                                <?foreach ($attr as $a) :?> 
                                                                    <?$attr_array[] = $a->id?>
                                                                    <th><?=$a->attribute_memo?></th>  
                                                                <?php endforeach ?>  
                                                            </thead>
                                                            <tbody> 
                                                            <?//$ids = implode(",",$attr_array);?>
                                                            <?  $activity = $c->db->getAll("SELECT action_group_name FROM  activity WHERE element_id = ?i GROUP by(action_group_name) ORDER BY date DESC",$element->id);
                                                                    
                                                                    ?> 

                                                                    <?foreach($activity as $act ){?>
                                                                         <?$val = $c->db->getAll("SELECT * FROM activity WHERE action_group_name = ?s AND element_id = ?i ORDER BY attribute_id ASC", $act->action_group_name, $element->id); 
                                                                             ?>
                                                                            <tr>
                                                                            <td><?=publish_date($val->{0}->date)?></td>
                                                                            <?foreach ($val as $value) {
                                                                               
                                                                               if(in_array($value->attribute_id, $attr_array)){
                                                                               echo'<td>';  echo $value->data; echo '</td>'; }else{
                                                                                    
                                                                                } 
                                                                            }

                                                                         ?>
                                                                            </tr>

                                                                    <?}?>


                                                            
                                                            </tbody>
                                                        </table>
                                                        
                                                    </div>
                                                    <?php endforeach ?> 
                                                  
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                                

                                 
                        </div>
                    </div> 
                </div>
            </div>
        </div>

    