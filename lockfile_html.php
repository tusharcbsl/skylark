        <!--lock files with users-->
<div id="lock-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="panel panel-color panel-primary"> 
            <div class="panel-heading"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h2 class="panel-title" id="ezeelockfile"> <i class="fa fa-lock" aria-hidden="true"></i> <?php echo $lang['lock_file']; ?></h2>
                <h2 class="panel-title" style="display:none;" id="ezeelocktitle"> <?php echo $lang['lock_file']; ?></h2> 
            </div>
            <div id="">
                <form method="post">
                    <div class="panel-body" >
                        <div class="row">
                            <input type="checkbox" id="lockforall" > <label class="text-primary">Select All</label>
                        </div>
                        <br>
                        <div class="row">
                            <label class="text-primary"><?php echo $lang['sh_lock_file']; ?> </label>
                            <select class="select2 select2-multiple" multiple data-placeholder="<?php echo $lang['sh_lock_file']; ?>" name="userid[]" id="lockusers" required >
                                <?php
                                $user = mysqli_query($db_con, "select * from tbl_user_master  order by first_name,last_name asc");
                                while ($rwUser = mysqli_fetch_assoc($user)) {
                                    if ($rwUser['user_id'] != 1 && $rwUser['user_id'] != $_SESSION['cdes_user_id']) {
                                        echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div> 
                    <div class="modal-footer">
                        <input type="hidden" id="lock_docid" name="lockdoc_id">
                        <a type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></a>

                        <button type="submit" name="lockfile" class="btn btn-primary"> <i class="fa fa-lock"></i> <?php echo $lang['lock_file'] ?></button>

                    </div>
                </form>
            </div>
        </div> 
    </div>
</div><!-- /.modal -->
<!--lock files with users-->

<!--lock files with users-->
<div id="unlock-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="panel panel-color panel-primary"> 
            <div class="panel-heading"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h2 class="panel-title" id="ezeeunlockfile"> <i class="fa fa-lock" aria-hidden="true"></i> <?php echo $lang['unlock_file']; ?></h2>
                <h2 class="panel-title" style="display:none;" id="ezeeuntitle"> <?php echo $lang['unlock_file']; ?></h2> 
            </div>
            <div id="">
                <form method="post">
                    <div class="panel-body" >
                        <div class="row">
                            <label class="text-primary"><?php echo $lang['do_u_want_to_unlock_file']. '?'; ?> </label>

                        </div>
                    </div> 
                    <div class="modal-footer">
                        <input type="hidden" id="unlock_docid" name="unlockdoc_id">
                        <a type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></a>

                        <button type="submit" name="unlockfile" class="btn btn-primary"> <i class="fa fa-lock"></i> <?php echo $lang['unlock_file'] ?></button>

                    </div>
                </form>
            </div>
        </div> 
    </div>
</div><!-- /.modal -->
<!--lock files with users-->
<!--lock files with users-->
<div id="request-unlock-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="panel panel-color panel-primary"> 
            <div class="panel-heading"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h2 class="panel-title" id="ezeeunreq"> <i class="fa fa-unlock" aria-hidden="true"></i> <?php echo $lang['unlock_file']; ?></h2>
                <h2 class="panel-title" style="display:none;" id="ezeeuntitle"> <?php echo $lang['unlock_file']; ?></h2> 
            </div>
            <div id="">
                <form method="post">
                    <div class="panel-body" >
                        <div class="row">
                            <label class="text-primary"><?php echo $lang['do_u_want_to_request_unlock_file']; ?> </label>

                        </div>
                    </div> 
                    <div class="modal-footer">
                        <input type="hidden" id="req_unlockdoc_id" name="req_unlockdoc_id">
                        <a type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></a>

                        <button type="submit" name="req_unlockfile" class="btn btn-primary"> <i class="fa fa-unlock"></i> <?php echo $lang['send_request'] ?></button>

                    </div>
                </form>
            </div>
        </div> 
    </div>
</div><!-- /.modal -->
<!--lock files with users-->
<script>
        $(".lock_file").click(function(){
       var id=$(this).attr("data"); 
       console.log(id);
       $("#lock-selected-files").modal("show");
       $("#lock_docid").val(id);
    });
    $(".unlock_file").click(function(){
       var id=$(this).attr("data"); 
       $("#unlock-selected-files").modal("show");
       $("#unlock_docid").val(id);
    });
    $(".send_lock_request").click(function(){
       var id=$(this).attr("data"); 
       console.log(id);
       $("#request-unlock-selected-files").modal("show");
       $("#req_unlockdoc_id").val(id);
    });


    $("#lockforall").click(function(){
        if($("#lockforall").is(':checked') ){
            $("#lockusers > option").prop("selected","selected");
            $("#lockusers").trigger("change");
        }else{
            $("#lockusers > option").removeAttr("selected");
             $("#lockusers").trigger("change");
         }
    });
</script>