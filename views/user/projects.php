<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Your projects</h4> 
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-striped">
                                    <thead>
                                        <th>ID</th>
                                        <th>Date</th> 
                                        <th>Name</th> 
                                        <th>Report</th>
                                        <th>Code snippet</th>
                                        <th>Edit</th>
                                    </thead>
                                    <tbody>
                                        <?if(how_many_items($render->projects) > 0){?>
                                            <?foreach($render->projects as $project){?>
                                                <tr>
                                                    <td><?=$project->id?></td>
                                                    <td><?=publish_date($project->date)?></td>
                                                    <td><?=$project->name?></td> 
                                                    <td><a href="/report<?=$project->id?>" title="See report"><i class="ti-stats-up"></i></a>&nbsp; &nbsp;&nbsp; &nbsp; &nbsp;
                                                    <a href="/download_report<?=$project->id?>" title="Download report"><i class="ti-download"></i></a>
                                                    </td>
                                                    <td><input type="text" style="width: 90%;" value='<script src="<?="//".$_SERVER['HTTP_HOST']?>/public/js/<?=$project->codename?>.js" type="text/javascript"></script>'></td>
                                                    <td><a href="/project<?=$project->id?>"><i class="ti-settings"></i></a> &nbsp; &nbsp; <a href="/delete_project<?=$project->id?>"><i class="ti-close"></i></a></td>
                                                </tr>
                                            <?}?>
                                        <?}else{  
                                            $create_project = TRUE;
                                         }?>
                                    </tbody>
                                </table>
                                <?if(isset($create_project)){?>
                                    <div class="text-center">
                                        <a href="/project" class="btn btn-info btn-fill btn-wd "><i class="ti-plus"></i> Create project</a><br><br>
                                    </div>
                                <?}?>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

   <script type="text/javascript">
      $(document).ready(function(){
    
        <?if(Handler::messageExists()){?>
         $.notify({
               icon: 'ti-check',
               message: '<?=Handler::render_message()?>'

            },{
                type: 'success',
                timer: 4000
            });
         <?}?>

      });
   </script>