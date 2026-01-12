 
<!-- for lock folder-->
<div id="lock-folder" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h4 class="modal-title"><?php echo $lang['lock_folder']; ?></h4> 
            </div> 
            <form method="post">

                <div class="modal-body">

                    <div class="row">
                        <div class="form-group">
                            <label><?= $lang['Slct_Folder']; ?><span class="text-alert">*</span></label>
                            <input type="text" class="form-control" placeholder="<?= $lang['Slct_Folder']; ?>" id="selected_lock_folder" name="selected_folder" value="<?php echo $rwmoveFolderName['sl_name']; ?>" readonly >
                        </div>
                        <div class="form-group">
                            <label><?= $lang['pwd']; ?><span class="text-alert">*</span></label>

                            <input type="password" class="form-control" placeholder="<?= $lang['pwd']; ?>" name="lockfolder"  required autocomplete="off"/>
                        </div>
                    </div>      

                </div>
                <div class="modal-footer"> 
                    <input value="<?php echo $rwFolder['sl_id']; ?>" name="lockslId" type="hidden" >
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?= $lang['Close']; ?></button> 
                    <button type="submit" name="lock" class="btn btn-primary"><?= $lang['Submit']; ?></button>
                </div>
            </form>
        </div>
    </div> 
</div>
<?php
$slid_lock = mysqli_query($db_con, "select * from `tbl_storage_level` where sl_id='$slid'")or die('Error DBBBB : ' . mysqli_error($db_con));
$abs = mysqli_fetch_assoc($slid_lock);
?>
<!-- for unlock folder-->
<div id="unlock-folder" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h4 class="modal-title"><?php echo $lang['unlock_folder']; ?></h4> 
            </div> 
            <form method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label><?= $lang['Slct_Folder']; ?><span class="text-alert">*</span></label>
                            <input type="text" class="form-control" id="selected_unlock_folder" name="selected_folder" value="<?php echo $rwmoveFolderName['sl_name']; ?>" readonly />
                        </div></div>
                    <div class="row">
                        <div class="form-group">
                            <label><?= $lang['old_Pwd']; ?><span class="text-alert">*</span></label>
                            <input type="password" class="form-control" id="unlockfolder" name="unlockfolder"  required autocomplete="off"/>
                        </div></div>
                </div>
                <div class="modal-footer"> 
                    <input value="<?php echo $rwFolder['sl_id']; ?>" name="lockslId" type="hidden" >
                    <button type="button" class="btn btn-default waves-effect"  data-dismiss="modal" ><?= $lang['Close']; ?></button>
                    <button type="submit" id="unlock" name ="unlock" class="btn btn-info"><?= $lang['Unlock']; ?></button> 
                </div>
            </form>
        </div>
    </div> 
</div>
<!-- for update password lock -->
<div id="update-folder-password" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h4 class="modal-title"><?php echo $lang['update_folder_password']; ?></h4> 
            </div> 
            <form method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label><?= $lang['Slct_Folder']; ?><span class="text-alert">*</span></label>
                            <input type="text" class="form-control" id="selected_update_fol_pass" name="selected_folder" value="<?php echo $rwmoveFolderName['sl_name']; ?>"/>
                        </div></div>
                    <div class="row">
                        <div class="form-group">
                            <label><?= $lang['old_Pwd']; ?><span class="text-alert">*</span></label>
                            <input type="password" class="form-control" id="old_pass"  required name="old_pass" autocomplete="off"/>
                        </div></div>
                    <div class="row">
                        <div class="form-group">
                            <label><span class="text-alert">*</span></label>
                            <input type="password" class="form-control" id="new_pass"  required name="new_pass" autocomplete="off"/>
                        </div></div>
                </div>
                <div class="modal-footer"> 
                    <input value="<?php echo $rwFolder['sl_id']; ?>" name="lockslId" type="hidden" >
                    <button type="button" class="btn btn-default waves-effect"  data-dismiss="modal" >Close</button>
                    <button type="submit" name="update_folder_pass" class="btn btn-primary" >Update</button>
                </div>
            </form>
        </div>
    </div> 
</div>

<!-- for forget folder password-->
<div id="forgot-password" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-heading"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h4 class="modal-title"><?php echo $lang['forgot_pass']; ?> </h4> 
            </div> 
            <form method="post">
                <div class="modal-body">
                    <div class="row">
                        <h5> <?php echo str_replace('folder_name',$rwFolder['sl_name'], $lang['forgot_folder_pass_confirm']);?> ?</h5>
                    </div>
                    <div class="modal-footer"> 
                        <input value="<?php echo $rwFolder['sl_id']; ?>" name="forgotPassId" type="hidden" >
                        <button type="button" class="btn btn-default waves-effect"  data-dismiss="modal"><?php echo $lang['Close']; ?></button>

                        <button type="submit" name="forgotPassword" class="btn btn-primary"><?php echo $lang['confirm']; ?></button>
                    </div>
            </form>
        </div>
    </div> 
</div>	
</div>
<!-- for forget folder password reset-->
<div id="resetpassword" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h4 class="modal-title"><?php echo $lang['reset_ur_password']; ?></h4> 
            </div> 
            <form method="post" id="resetPassForm">

                <div class="modal-body">

                    <div class="row">
                        <div class="form-group">
                            <label for="pass1"><?= $lang['otp']; ?><span style="color:red;">*</span></label>
                            <input  name="otp" type="text"  placeholder="<?= $lang['Etr_OTP_hre']; ?>" id="otps" required class="form-control" style="background:#fff; color: #565656;">

                        </div>
                        <div class="form-group">
                            <label for="pass1"><?= $lang['Password'] ?> <span style="color:red;">*</span></label>
                            <input id="pass1" name="paswd" type="password"  placeholder="<?= $lang['Password'] ?>" required class="form-control">

                        </div>
                        <div class="form-group">
                            <label for="passWord2"><?= $lang['Confirm_Password'] ?><span style="color:red;">*</span></label>
                            <input data-parsley-equalto="#pass1" type="password" required placeholder="<?= $lang['Confirm_Password'] ?>" class="form-control" id="passConfirm" style="background:#fff; color: #565656;">
                        </div>
                    </div>
                    <div class="modal-footer"> 

                        <input value="<?php echo $rwFolder['sl_id']; ?>" name="lockslId" type="hidden" >
                        <input value="<?php echo $rwFolder['sl_name']; ?>" name="folder" type="hidden" >
                        <button type="button" class="btn btn-default waves-effect"  data-dismiss="modal" ><?= $lang['Close']; ?></button>

                        <button type="submit" name="resetPass" class="btn btn-primary" ><?= $lang['Submit']; ?></button>
                    </div>
            </form>
        </div>
    </div> 
</div>
</div>

