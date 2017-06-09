<div class="content">
            <div class="container-fluid">
                <div class="row"> 
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title"><?=$render->title?></h4>
                            </div>
                            <form method="POST" id='ss' action="/create_project">
                            <input type="text" hidden name="project_id" value="<?=(isset($render->project->name)) ? $render->project->id : "0"?>">
                            <div class="content"> 
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Project name</label>
                                                <input type="text" class="form-control border-input" name="project_name" placeholder="Your Project" value="<?=(isset($render->project->name)) ? $render->project->name : ""?>">
                                            </div>
                                        </div>
                                         
                                    </div>

                                 	 
                                    <div class="elements create_element">
                                        
                                    <?if(isset($render->project->name)){?> 
                                        <?if(how_many_items($render->elements) > 0){?>
                                            <?foreach($render->elements as $element){?>
                                            <?$uniq = _uniq();?>
                                               <div class="element">
                                                  <div class="row">
                                                     <hr>
                                                     <div class="col-md-4">
                                                        <div class="form-group"><a class="del-element"><i class="ti-close"></i></a><label>Element selector</label><input type="text" class="form-control border-input" name="elements[<?=$uniq?>_<?=$element->id?>][selector]" placeholder=".class-name" value="<?=$element->selector?>"></div>
                                                     </div>
                                                  </div>
                                                  <div class="row">
                                                     <div class="col-md-4 col-xs-12">
                                                        <div class="form-group">
                                                           <label>Action</label>
                                                           <select name="elements[<?=$uniq?>_<?=$element->id?>][action]" class="form-control border-input">
                                                              <option value="click" <?=($element->action == "click") ? "selected='selected'" : ""?>>Click</option>
                                                              <option value="change" <?=($element->action == "change") ? "selected='selected'" : ""?>>Change</option>
                                                              <option value="onload" <?=($element->action == "onload") ? "selected='selected'" : ""?>>Onload</option>
                                                           </select>
                                                        </div>
                                                     </div>
                                                  </div>
                                                  <?$controller = new Controller;?>
                                                  <?$attributes = $controller->db->getAll("SELECT * FROM `attributes` WHERE project_id = ?i AND element_id = ?i ORDER BY id ASC", $render->project->id, $element->id);?>

                                                  <div class="attributes">
                                                     <div class="att">
                                                     <?if(how_many_items($attributes) > 0){?>
                                                         <?$i = 1;?>
                                                         <?foreach($attributes as $attribute){?>
                                                         <?$i++;?>
                                                            <div class="row">
                                                               <div class="col-md-4  col-xs-12 third-level">
                                                                  <div class="form-group"><label>Attribute selector</label><input type="text" class="form-control border-input" placeholder="data-product-id" name="elements[<?=$uniq;?>_<?=$element->id?>][attributes][<?=$i?>_<?=$attribute->id?>][attribute_selector]" value="<?=$attribute->attribute_selector?>"></div>
                                                               </div>
                                                               <div class="col-md-4 col-xs-10">
                                                                  <div class="form-group"><label><span class="mandatory">*</span>Memo</label><input type="text" class="form-control border-input" name="elements[<?=$uniq;?>_<?=$element->id?>][attributes][<?=$i?>_<?=$attribute->id?>][attribute_memo]" placeholder="Some details such a purpose" value="<?=$attribute->attribute_memo?>"></div>
                                                               </div>
                                                               <div class="col-md-1 col-xs-2">
                                                                  <div class="form-group"><a class="text-center del"><i class="ti-close"></i></a></div>
                                                               </div>
                                                            </div>
                                                        <?}?>
                                                     <?}?> 
                                                     </div>
                                                     <a id="new_attribute" class="third-level" data-hierarchy="<?=$uniq;?>_<?=$element->id?>"><i class="ti-plus"></i> Add attribute that you want tracking</a>
                                                  </div>
                                               </div>
                                            <?}?>
                                        <?}?>
                                    <?}?>
                                            
                                    </div>
                                     

                                    
                                    <div class="">
                                    <hr>
                                    	<a id="new_element" class="btn btn-info btn-fill btn-wd ">New element</a>
                                    	<a id="create" type="submit" class="btn btn-info btn-fill btn-wd ">Next</a>
                                        <button id="save" type="submit" class="btn btn-info btn-fill btn-wd"><i class="ti-save"></i> Save!</button>
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
			.has-error .form-control::-webkit-input-placeholder { color: white; }
.has-error .form-control:-moz-placeholder { color: white; }
.has-error .form-control::-moz-placeholder { color: white; }
.has-error .form-control:-ms-input-placeholder { color: white; }
        	.create_element{
        		display: none;
        	}
            .mandatory{
                color: #f74539;
            }
            #save{
                display: none;
            }
        	.create_element .third-level{
        		margin-left: 80px;
        	} 
        	.create_element > div{
        		margin-left: 40px;
        	} 
        	.del{
        		display: block;
        		cursor: pointer;
        		padding-top: 33px;
        		color: black;
        	}
        	.del-element{ 
        		cursor: pointer;
        		padding-right: 5px;
        		color: black;
        	}
        	#new_element{  
        		display: none;
    			background-color: #ff832b;
    			border-color: #ff832b;
       			cursor: pointer;
        	}
        	#new_attribute{   
       			cursor: pointer;
        	}
          @media (max-width: 992px) {
            .third-level{
              margin-left: 0px !important;
            } 
          }
          <?if(isset($render->project->name)){?> 
            .create_element, #new_element, #save{
                display: unset;
            }
            #create{
                display: none;
            }
          <?}?>
        </style>
        <script type="text/javascript">
		$(document).ready(function() {

			function stringGen(len){
			    var text = "";
			    
			    var charset = "abcdefghijklmnopqrstuvwxyz0123456789";
			    
			    for( var i=0; i < len; i++ )
			        text += charset.charAt(Math.floor(Math.random() * charset.length));
			    
			    return text;
			}

			function create_element(id){
				var element = '<div class="element"><div class="row"><hr><div class="col-md-4"><div class="form-group"><a class="del-element"><i class="ti-close"></i></a><label>Element selector</label><input type="text" class="form-control border-input" name="elements['+id+'_0][selector]" placeholder=".class-name"></div></div></div><div class="row"><div class="col-md-4 col-xs-12"><div class="form-group"><label>Action</label><select name="elements['+id+'_0][action]" class="form-control border-input"><option value="click">Click</option><option value="change">Change</option><option value="onload">Onload</option></select></div></div></div><div class="attributes"><div class="att"><div class="row"><div class="col-md-4  col-xs-12 third-level"><div class="form-group"><label>Attribute selector</label><input type="text" class="form-control border-input" placeholder="data-product-id" name="elements['+id+'_0][attributes][1_0][attribute_selector]"></div></div><div class="col-md-4 col-xs-10"><div class="form-group"><label><span class="mandatory">*</span>Memo</label><input type="text" class="form-control border-input" name="elements['+id+'_0][attributes][1_0][attribute_memo]" placeholder="Some details such a purpose"></div></div><div class="col-md-1 col-xs-2"><div class="form-group"><a class="text-center del"><i class="ti-close"></i></a></div></div></div></div><a id="new_attribute" class="third-level" data-hierarchy="'+id+'_0"><i class="ti-plus"></i> Add attribute that you want tracking</a></div></div>';
				return element;
			}
        	
        	function create_attribute (hierarchy, id){
        		var attribute = '<div class="row"><div class="col-md-4 col-xs-12 third-level"><div class="form-group"><label>Attribute selector</label><input type="text" class="form-control border-input" placeholder="data-product-id" name="elements['+hierarchy+'][attributes]['+id+'_0][attribute_selector]"></div></div><div class="col-md-4 col-xs-10"><div class="form-group"><label><span class="mandatory">*</span>Memo</label><input type="text" class="form-control border-input" placeholder="Some details such a purpose" name="elements['+hierarchy+'][attributes]['+id+'_0][attribute_memo]"></div></div><div class="col-md-1 col-xs-2"><div class="form-group"><a class="text-center del"><i class="ti-close"></i></a></div></div></div>';
        		return attribute;
        	}
        	
        	
        	function check(){ 
        		$('form').find('.form-group').removeClass("has-error");
        		var emptyTextBoxes = $('form').find('input:text').filter(function() { return this.value == ""; });
        		emptyTextBoxes.each(function() {
        			$(this).parent().addClass("has-error");
			    }); 
        	}
        	$("#ss").submit(function(e){
        		var btn = $("#save"); 
        		check();  
        		if(btn.is(":visible") && $('.has-error').length == 0){ 
        		}else{
        			e.preventDefault();   
        		}
        	});

        	$("body").on("keypress", "input:text", function() { 
    			   $(this).parent().removeClass("has-error");;
    			});


    	$("body").on("click", "#create", function() {
      	  var pn_element = $("input[name=project_name]");
		      var project_name = $.trim(pn_element.val());
			  if(project_name == ""){ 
			  	pn_element.parent().addClass("has-error");
			  }else{ 
			  	$(this).hide();
			  	pn_element.parent().removeClass("has-error");;
			  	$(".create_element").html(create_element(stringGen(18)));
			  	$(".create_element").fadeIn();
			  	$("#new_element").fadeIn();
			  	$("#save").show();
			  }
			});
			$("body").on("click", ".del", function() {
        	   $(this).parents().eq(2).remove();
			});

			$("body").on("click", "#new_attribute", function() {
			   var h = $(this).attr('data-hierarchy'); 
        	   $(this).parent().find('.att').append(create_attribute(h, stringGen(28)));
			});

			$("body").on("click", "#new_element", function() {  
        	   $(".elements").append(create_element(stringGen(28)));
			});

			$("body").on("click", ".del-element", function() { 
        	   $(this).parents().eq(3).remove();
        	   if($('.element').length == 0){
        				$("#create").show();
        				$("#save").hide();
        				$("#new_element").hide();
        	   }
			});
		});
        </script>