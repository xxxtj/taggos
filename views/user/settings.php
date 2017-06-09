<div class="content">
            <div class="container-fluid">
                <div class="row"> 
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title"><?=$render->title?></h4>
                            </div>
                            <form method="POST" id='ss' action="/settings"> 
                            <div class="content"> 
                            <?$saved = Handler::render_message();?>
                            <?if(!empty($saved)){echo $saved;}?>
                            <span id='error'><br><?=Handler::render_error("password");?></span>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="text" disabled class="form-control border-input" name="email" placeholder="Your Email" value="<?=$render->user->email?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Current password</label>
                                                <input type="text" class="form-control border-input" name="current_password" placeholder="Your Current Password">
                                            </div>
                                        </div>
                                         
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>New password</label>
                                                <input type="text" class="form-control border-input" name="new_password" placeholder="Your New Password">
                                            </div>
                                        </div>
                                         
                                    </div>
 
                                     

                                    
                                    <div class="">
                                    <hr>
                                    	 
                                        <button id="save" type="submit" class="btn btn-info btn-fill btn-wd">Save!</button>
                                    </div>
                                    <div class="clearfix"></div> 
                            </div>
                            </form>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <style type="text/css">
          #successMessage{
            color: #3c763d;
          }

          #error{
            color: red;
          }
        </style>
         