<?php
$page = basename($_SERVER['PHP_SELF']);
require_once 'application/config/database.php';
require_once 'application/pages/feature-enable-disable.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$rwcheckUser = mysqli_fetch_assoc($chekUsr);
$getRole = mysqli_query($db_con, "select * from tbl_user_roles where role_id = '$rwcheckUser[role_id]'") or die('Error:' . mysqli_error($db_con));
$rwgetRole = mysqli_fetch_assoc($getRole);
?>
<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <!--- Divider -->
        <div id="sidebar-menu">
            <ul>
                <?php
                ?>
                <!--<li class="text-muted menu-title">Navigation</li>-->
                <li class="text-muted menu-title"><?php echo $lang['Nav']; ?></li>

                <?php
                //for user role
                if ($rwgetRole['dashboard_mydms'] == '1' || $rwgetRole['dashboard_mytask'] == '1' || $rwgetRole['dashboard_edit_profile'] == '1' || $rwgetRole['dashboard_query'] == '1' || $rwgetRole['num_of_folder'] == '1' || $rwgetRole['num_of_file'] == '1' || $rwgetRole['memory_used'] == '1') {
                    ?>
                    <li >
                        <a href="index" class="waves-effect"><i class="fa fa-home"></i> <span> <?php echo $lang['Das']; ?> </span> </a>

                    </li>
                <?php } ?>
                <!-- for user management-->
                <?php
                if ($rwgetRole['create_user'] == '1' || $rwgetRole['modify_userlist'] == '1' ||
                        $rwgetRole['delete_userlist'] == '1' || $rwgetRole['view_userlist'] == '1' ||
                        $rwgetRole['add_group'] == '1' || $rwgetRole['delete_group'] == '1' ||
                        $rwgetRole['modify_group'] == '1' || $rwgetRole['view_group_list'] == '1' ||
                        $rwgetRole['storage_auth_plcy'] == '1' || $rwgetRole['role_add'] == '1' ||
                        $rwgetRole['role_delete'] == '1' || $rwgetRole['role_modi'] == '1' || $rwgetRole['role_view'] == '1' || $rwgetRole['view_metadata'] == '1' || $rwgetRole['add_metadata'] == '1') {
                    ?>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ion-person-stalker"></i> <span><?php echo $lang['Masters']; ?> </span> <span class="menu-arrow"></span> </a>
                        <ul class="list-unstyled">
                            <?php if ($rwgetRole['add_group'] == '1' || $rwgetRole['delete_group'] == '1' || $rwgetRole['modify_group'] == '1' || $rwgetRole['view_group_list'] == '1') { ?>

                                <li>
                                    <a href="groupList" class="waves-effect"><i class="fa fa-users"></i> <span><?php echo $lang['Group_Manager']; ?></span></a>
                                </li>
                            <?php } ?> 
                            <!--authorization-->
                            <?php if ($rwgetRole['role_add'] == '1' || $rwgetRole['role_delete'] == '1' || $rwgetRole['role_modi'] == '1' || $rwgetRole['role_view'] == '1') { ?>
                                <li >
                                    <!--<a href="userRole" class="waves-effect"><i class="fa ti-user"></i> <span>User Roles</span></a> -->
                                    <a href="userRole" class="waves-effect"><i class="fa fa-user"></i> <span><?php echo $lang['User_Profile']; ?></span></a>
                                </li>
                            <?php } ?>                                 
                            <!--authorization end-->

                            <?php if ($rwgetRole['create_user'] == '1') { ?>
                                <li><a href="createUser"><i class="fa fa-user-plus"></i><?php echo $lang['Add_User']; ?></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['view_userlist'] == '1') { ?>
                                <li><a href="userList"><i class="ion-person-stalker"></i><?php echo $lang['User_List']; ?></a></li> 
                            <?php } ?>
							
							 <?php if ($rwgetRole['role_view'] == '1') { ?>
                                <li><a href="userwise-folder-permission"><i class="fa fa-eye"></i><?php echo $lang['folder_permission']; ?></a></li> 
                            <?php } ?>


                            <?php if ($rwgetRole['view_metadata'] == '1' || $rwgetRole['add_metadata'] == '1') { ?>
                                <li><a href="addFields"><i class="fa fa-plus"></i> <?php echo $lang['Add_Fields']; ?></a></li>

                                <li><a href="metadata-list"><i class="fa fa-list"></i> <?php echo $lang['metadat_list']; ?></a></li>
                                
                            <?php } ?>

                            <?php if ($rwgetRole['storage_auth_plcy'] == '1') { ?>
                                <li><a href="MultiStoragePermission"><i class="glyphicon glyphicon-hdd"></i><?php echo $lang['Storage_Policies']; ?></a></li>
                            <?php } ?>

                        </ul>
                    </li>
                <?php } ?> 

                <?php if ($rwgetRole['create_storage'] == '1' || $rwgetRole['create_child_storage'] == '1' || $rwgetRole['upload_doc_storage'] == '1' || $rwgetRole['modify_storage_level'] == '1' || $rwgetRole['delete_storage_level'] == '1' || $rwgetRole['move_storage_level'] == '1' || $rwgetRole['copy_storage_level'] == '1') { ?>    
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="glyphicon glyphicon-hdd"></i> <span><?php echo $lang['Storage_Manager'] ?></span> <span class="menu-arrow"></span> </a>
                    <?php } ?>
                    <ul class="list-unstyled">
                        <?php
                        $str = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level=0");
                        if (mysqli_num_rows($str) <= 0) {
                            ?>

                            <?php if ($rwgetRole['create_storage'] == '1') { ?>
                                <li><a href="createStorage"><i class="fa fa-user-plus"></i><?php echo $lang['Cr_strg'] ?></a></li>
                            <?php } ?>

                        <?php } ?>

                        <?php
                        $sllevelTree = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes)");
                        while ($rwSllevelTree = mysqli_fetch_assoc($sllevelTree)) {
                            $level = $rwSllevelTree['sl_depth_level'];
                            $permSlId = $rwSllevelTree['sl_id'];
                            echo'<li ><a href="storage?id=' . urlencode(base64_encode($rwSllevelTree['sl_id'])) . '"><i class="md md-storage"></i>' . $rwSllevelTree['sl_name'] . '</a></li>';
                            //storageLevel($level, $db_con, $permSlId);
                        }

                        function storageLevel($level, $db_con, $slperm) {
                            $store = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level='$level' and sl_id='$slperm'");
                            while ($rwStore = mysqli_fetch_assoc($store)) {

                                $hasSub = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$rwStore[sl_id]'");
                                if (mysqli_num_rows($hasSub) > 0) {
                                    echo'<li class="has_sub"><a href="storage?id=' . urlencode(base64_encode($slperm)) . '"><i class="md md-storage"></i> <span>' . $rwStore['sl_name'] . '</span> <span class="menu-arrow"></span></a> <ul>';
                                    //storageSubLevel($rwStore['sl_depth_level']+1, $rwStore['sl_id'],  $db_con);
                                    echo '</ul></li>';
                                } else {
                                    echo'<li ><a href="storage?id=' . urlencode(base64_encode($slperm)) . '"><i class="md md-storage"></i>' . $rwStore['sl_name'] . '</a></li>';
                                }
                            }
                        }

                        function storageSubLevel($level, $slID, $db_con) {
                            $store = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slID'");
                            while ($rwStore = mysqli_fetch_assoc($store)) {
                                $hasSub = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$rwStore[sl_id]'");
                                if (mysqli_num_rows($hasSub) > 0) {
                                    echo'<li class="has_sub"><a href="#"><i class="fa fa-folder"></i> <span>' . $rwStore['sl_name'] . '</span> <span class="menu-arrow"></span></a> <ul>';
                                    storageSubLevel($rwStore['sl_depth_level'] + 1, $rwStore['sl_id'], $db_con);
                                    echo '</ul></li>';
                                } else {
                                    echo'<li ><a href="storage?id=' . urlencode(base64_encode($slperm)) . '"><i class="fa fa-folder"></i>' . $rwStore['sl_name'] . '</a></li>';
                                }
                            }
                        }
                        ?>

                        <?php if ($rwgetRole['lock_file'] == '1') { ?>  
                            <li class="has_sub"><a href="lock_request_list" target=""><i class="fa fa-lock"></i><span><?php echo $lang['lock_file']; ?></span></a>
                            <?php } ?>
                        <?php if ($rwgetRole['lock_file'] == '1') { ?>  
                            <li class="has_sub"><a href="memory_management" target=""><i class="fa fa-lock"></i><span><?php echo 'Memory Management'; ?></span></a>
                            <?php } ?>

                    </ul>
                </li>

                <?php if ($rwgetRole['ezeescan'] == '1') { ?>
                    <li class="has_sub"><a href="#"><i class="fa fa-book"></i> <span><?php echo $lang['ezeescan'] ?></span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="ezeescan_exe/Ezee_Scan_installer.msi" download=""><i class="fa fa-book"></i> Ezeescan</a> </li>
                        </ul>
                    </li>

                <?php } ?>

                <?php if ($rwgetRole['bulk_upload'] == '1' || $rwgetRole['folder_upload'] == '1') { ?>
                    <li class="has_sub"><a href="#"><i class="md md-cloud-upload"></i> <span><?php echo $lang['Upload_Import']; ?></span> <span class="menu-arrow"></span></a>
                        <ul>
                            <?php if ($rwgetRole['bulk_upload'] == '1') { ?>
                                <li><a href="bulk_upload"><i class="fa fa-upload"></i><?php echo $lang['Bulk_Upload']; ?></a> </li>
                            <?php } if ($rwgetRole['folder_upload'] == '1') { ?>
                                <li><a href="upload_folder"><i class="fa fa-folder-open"></i><?php echo $lang['upload_folder']; ?></a> </li>
                            <?php } if ($rwgetRole['upload_files'] == '1') { ?>
                                <li><a href="upload-multiple-files"><i class="fa fa-copy"></i><?php echo $lang['upload_files']; ?></a> </li>
                            <?php } ?>
                        </ul>
                    </li>

                <?php } ?>

                <?php if ($rwgetRole['metadata_search'] == '1' || $rwgetRole['metadata_quick_search'] == '1' || $rwgetRole['advance_search'] == '1') { ?>
                    <li class="has_sub">
                        <a href="#" class="waves-effect"><i class="fa fa-search" aria-hidden="true"></i> <span><?php echo $lang['Search']; ?></span><span class="menu-arrow"></span></a>
                        <ul>
                            <?php if ($rwgetRole['advance_search'] == '1') { ?>
                                <li><a href="advance-keyword-search" class="waves-effect" aria-hidden="true"><i class="fa fa-search"></i> <span> <?php echo $lang['advance_keyword_search']; ?></span></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['metadata_search'] == '1') { ?>
                                <li><a href="metasearch" class="waves-effect" aria-hidden="true"><i class="fa fa-search"></i> <span> <?php echo $lang['MetaData_Search']; ?></span></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['metadata_quick_search'] == '1') { ?>
                                <li><a href="search" class="waves-effect" aria-hidden="true"><i class="fa fa-search"></i> <span> <?php echo $lang['quich_search']; ?></span></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['save_query'] == '1') { ?>
                                <li><a href="Frequently_queries" class="waves-effect" aria-hidden="true"><i class="fa fa-search"></i> <span> <?php echo $lang['Frequently_Queries']; ?></span></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?> 

                <?php if ($rwgetRole['password_policy'] == '1' || $rwgetRole['app_default'] == '1' || $rwgetRole['view_exten'] == '1') { ?>
                    <li class="has_sub">
                        <a href="#" class="waves-effect"><i class="fa fa-gears" aria-hidden="true"></i> <span><?= $lang['Administrative_tool']; ?></span><span class="menu-arrow"></span></a>
                        <ul>
                            <?php if ($rwgetRole['app_default'] == '1') { ?>
                                <li class="has_sub">
                                    <a href="default"><i class="fa fa-fw fa-area-chart"></i> <span><?php echo $lang['App_default']; ?></span> </a>
                                </li>
                            <?php } ?>

                            <?php if ($rwgetRole['view_exten'] == '1') { ?>
                                <li><a href="managefile_extn" class="waves-effect" aria-hidden="true"><i class="fa fa-cog"></i> <span> <?= $lang['managefile_exten']; ?></span></a></li>
                            <?php } ?>

                            <?php if ($rwgetRole['password_policy'] == '1' && $rwInfoPolicy['feature_enable_disable'] == '1') { ?>
                                <li><a href="password-policy" class="waves-effect" aria-hidden="true"><i class="fa fa-key"></i> <span> <?= $lang['Password_Policy']; ?></span></a></li>
                            <?php } ?>
                                
                            <?php if ($rwgetRole['email_credential'] == '1') { ?>
                                <li><a href="sending-email-credential" class="waves-effect" aria-hidden="true"><i class="fa fa-lock"></i> <span> <?= $lang['config_credential']; ?></span></a></li>
                            <?php } ?>
							
							<?php if ($_SESSION['cdes_user_id'] == '1') { ?>
                                <li><a href="manage-file-server-details" class="waves-effect" aria-hidden="true"><i class="fa fa-lock"></i> <span> <?= $lang['manage_fileserver_credentials']; ?></span></a></li>
                            <?php } ?>

                        </ul>
                    </li>



                <?php } ?> 

                <?php
                if ($rwgetRole['create_workflow'] == '1' || $rwgetRole['view_workflow_list'] == '1' ||
                        $rwgetRole['edit_workflow'] == '1' || $rwgetRole['delete_workflow'] == '1' ||
                        $rwgetRole['workflow_step'] == '1' || $rwgetRole['workflow_initiate_file'] == '1' ||
                        $rwgetRole['view_workflow_list'] == '1' || $rwgetRole['initiate_file'] == '1' ||
                        $rwgetRole['workflow_task_track'] == '1' || $rwgetRole['involve_workflow'] == '1' ||
                        $rwgetRole['run_workflow'] == '1' || $rwgetRole['workflow_audit'] == '1') {
                    ?>    
                    <li class="has_sub">
                        <a href="javascript:void(0);"><i class="fa fa-building" aria-hidden="true"></i><span><?php echo $lang['Workflow_management']; ?></span> <span class="menu-arrow"></span></a>
                        <ul>

                            <?php if ($rwgetRole['view_workflow_list'] == '1') { ?>
                                <li><a href="createWorkflow"><i class="fa  fa-plus-circle" aria-hidden="true"></i><span><?php echo $lang['Nw_Wrkflow']; ?></span></a></li>
                            <?php } ?> 
                            <?php if ($rwgetRole['view_workflow_list'] == '1') { ?>
                                <li><a href="addWorkflow"><i class="fa  fa-eye" aria-hidden="true"></i><span><?php echo $lang['Workflow_Designer']; ?></span></a></li>
                            <?php } ?> 

                            <?php if ($rwgetRole['workflow_initiate_file'] == '1') { ?>
                                <li class="has_sub"><a href="javascript:void(0);" class="waves-effect"><i class="fa fa-building-o" aria-hidden="true"></i><span><?php echo $lang['Initiate_WorkFlow']; ?></span> <span class="menu-arrow"></span></a>
                                    <ul style="overflow-y:auto;max-height:200px;">
                                        <?php
											$getWorkflw = mysqli_query($db_con, "call userWorflowList(" . $_SESSION['cdes_user_id'] . ", 0)") or die('Error in getWorkflw upload:' . mysqli_error($db_con));
											mysqli_next_result($db_con);
											while ($rwgetWorkflws = mysqli_fetch_assoc($getWorkflw)) {
												$wkfname = preg_replace("/[^A-Za-z0-9]/", "", strtolower($rwgetWorkflws['workflow_name']));
												if ($wkfname == 'salesrequestform') {
													?>
													<li class="has_sub"><a href="sales-request-form?wid=<?php echo urlencode(base64_encode($rwgetWorkflws['workflow_id'])); ?>"><i class="fa fa-hourglass-start" aria-hidden="true"></i> <span><?php echo $rwgetWorkflws['workflow_name']; ?></span></a></li>    
												<?php } else {
													?> 
													<li class="has_sub"><a href="createWork?wid=<?php echo urlencode(base64_encode($rwgetWorkflws['workflow_id'])); ?>"><i class="fa fa-hourglass-start" aria-hidden="true"></i> <span><?php echo $rwgetWorkflws['workflow_name']; ?></span></a></li>  
													<?php
												}
											}
											?>
                                    </ul>
                                <?php }
                                ?>
                            </li>
                            <?php if ($rwgetRole['initiate_file'] == '1') { ?>
                                <li class="has_sub"><a href="initiateFile"><i class="ti-book" aria-hidden="true"></i><span><?php echo $lang['Initiate_File']; ?></span></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['workflow_task_track'] == '1') { ?>
                                <li><a href="taskTrack"><i class="fa fa-paper-plane" aria-hidden="true"></i><span><?php echo $lang['Task_Track_Status']; ?></span></a>  
                                </li>
                            <?php } ?>
                            <?php if ($rwgetRole['involve_workflow'] == '1' || $rwgetRole['run_workflow'] == '1') { ?>
                                <li><a href="#"><i class=" fa fa-industry" aria-hidden="true"></i><span><?php echo $lang['Workflow_Reports']; ?></span><span class="menu-arrow"></span></a> 
                                    <ul>
                                        <?php if ($rwgetRole['involve_workflow'] == '1') { ?>
                                            <li><a href="involeworkflow"><i class="fa fa-user" aria-hidden="true"></i><span><?php echo $lang['Involved_WorkFlow']; ?></span></a></li>
                                        <?php } ?>
                                        <?php if ($rwgetRole['run_workflow'] == '1') { ?>
                                            <li><a href="running-workflow"><i class="fa fa-user-secret" aria-hidden="true"></i><span><?php echo $lang['running_wf']; ?></span></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if ($rwgetRole['workflow_audit'] == '1') { ?> 
                                <li><a href="AuditTrail-workflow"><i class="fa fa-building" aria-hidden="true"></i><?php echo $lang['WorkFlow_Audit']; ?></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($rwgetRole['view_user_audit'] == '1' || $rwgetRole['view_storage_audit'] == '1' || $rwgetRole['workflow_audit'] == '1') { ?>    
                    <li class="has_sub"><a href="#"><i class="fa fa-fw fa-area-chart"></i> <span><?php echo $lang['Audit_Trail']; ?></span> <span class="menu-arrow"></span></a>
                        <ul>
                            <?php if ($rwgetRole['view_user_audit'] == '1') { ?> 
                                <li><a href="AuditTrailUserWise"><i class="fa fa-user"></i><?php echo $lang['User_Audit']; ?></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['view_storage_audit'] == '1') { ?> 
                                <li><a href="AuditTrail-Storage-Wise"><i class="fa fa-hdd-o" aria-hidden="true"></i><?php echo $lang['Storage_Audit']; ?></a></li>
                            <?php } ?>


                        </ul>
                    </li>
                <?php } ?>

                <!-- sk@24918 -->
                <?php if ($rwgetRole['todo_add'] == '1' || $rwgetRole['todo_view'] == '1') { ?>    
                    <li class="has_sub"><a href="#"><i class="glyphicon glyphicon-th-list"></i> <span><?php echo $lang['to_do_list']; ?></span> <span class="menu-arrow"></span></a>
                        <ul>
                            <?php if ($rwgetRole['todo_add'] == '1') { ?>
                                <li><a href="createtodo"><i class="fa fa-plus"></i><?php echo $lang['add_to_do']; ?></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['todo_view'] == '1') { ?>
                                <li><a href="manage-todo?tddt=<?= urlencode(base64_encode('today')) ?>"><i class="fa fa-calendar-check-o"></i><?php echo $lang['today']; ?></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['todo_view'] == '1') { ?>
                                <li><a href="manage-todo?tddt=<?= urlencode(base64_encode('tomorrow')) ?>"><i class="fa fa-calendar-plus-o"></i><?php echo $lang['tomorrow']; ?></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['todo_view'] == '1') { ?>
                                <li><a href="manage-todo?tddt=<?= urlencode(base64_encode('this_week')) ?>"><i class="fa fa-calendar"></i><?php echo $lang['this_week']; ?></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['todo_view'] == '1') { ?>
                                <li><a href="manage-todo"><i class="glyphicon glyphicon-align-justify"></i><?php echo $lang['All']; ?></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>


                <?php if ($rwgetRole['appoint_add'] == '1' || $rwgetRole['appoint_view'] == '1') { ?>    
                    <li class="has_sub"><a href="#"><i class="glyphicon glyphicon-calendar"></i> <span><?php echo $lang['appointments']; ?></span> <span class="menu-arrow"></span></a>
                        <ul>
                            <?php if ($rwgetRole['appoint_add'] == '1') { ?>
                                <li><a href="add-appointment"><i class="fa fa-calendar-plus-o"></i><?php echo $lang['add_new_appointment']; ?></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['appoint_view'] == '1') { ?>
                                <li><a href="manage-appointment"><i class="fa fa-eye"></i><?php echo $lang['view_all_appointment']; ?></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- end -->
					
							<?php if ($rwgetRole['view_recycle_bin'] == '1' || $rwgetRole['restore_file'] == '1' || $rwgetRole['permanent_del'] == '1') { ?> 
								<li class="has_sub"><a href="recycle"><i class="fa fa-recycle"></i> <span> <?php echo $lang['Recycle_Bin']; ?></span></a>
								
								<li class="has_sub"><a href="recycle-page"><i class="fa fa-recycle"></i> <span> <?php echo $lang['Recycle_page']; ?></span></a>

							   <?php } ?>
						
				   
                    <?php if ($rwgetRole['doc_expiry_time'] == '1' && $rwgetexpInfo['exp_feature_enable'] == '1') { ?> 
                    <li class="has_sub"><a href="expired-document-list"><i class="fa fa-calendar-times-o"></i> <span> <?php echo $lang['expired_doc_list']; ?></span></a>
                    <?php } ?>
                    <?php if ($rwgetRole['doc_weeding_out'] == '1' && $rwgetInfo['retention_feature_enable'] == '1') { ?> 
                    <li class="has_sub"><a href="retention-period-document"><i class="fa fa-clock-o"></i> <span> <?php echo $lang['Retention_doc_list']; ?></span></a>
                    <?php } ?> 

						<?php if ($rwgetRole['share_with_me'] == '1' || $rwgetRole['shared_file'] == '1' || $rwgetRole['shared_folder_with_me'] == '1' || $rwgetRole['share_folder'] == '1') { ?>
						
							<li class="has_sub"><a href="#"><i class="fa fa-share"></i> <span><?php echo $lang['share_files_and_folder']; ?></span> <span class="menu-arrow"></span></a>
								<ul>
									<?php if ($rwgetRole['share_with_me'] == '1') { ?>
									<li class="has_sub"><a href="shared-with-me"><i class="fa fa-sign-in" aria-hidden="true"></i><span> <?php echo $lang['Shared_With_Me']; ?></span></a></li>
									<?php } if ($rwgetRole['shared_file'] == '1') { ?>  
									<li class="has_sub"><a href="shared-files"><i class="fa fa-sign-out" aria-hidden="true"></i><span> <?php echo $lang['Shared_files']; ?></span></a></li>
								<?php } if ($rwgetRole['shared_folder_with_me'] == '1') { ?>  
									<li class="has_sub"><a href="share-folder-withme"><i class="fa fa-sign-out" aria-hidden="true"></i><span> <?php echo $lang['share_folder_with_me']; ?></span></a></li>
								<?php } if ($rwgetRole['share_folder'] == '1') { ?>  
									<li class="has_sub"><a href="shared-folder"><i class="fa fa-sign-out" aria-hidden="true"></i><span> <?php echo $lang['shared_folder']; ?></span></a></li>
								<?php } ?>
								</ul>
						   </li>
					   
						<?php } ?>
					

                    <?php if ($rwgetRole['review_track'] == '1' || $rwgetRole['review_intray'] == '1') { ?> 
                    <li class="has_sub"><a href="#"><i class="fa fa fa-eye"></i> <span><?php echo $lang['reviewer']; ?></span> <span class="menu-arrow"></span></a>
                        <ul>
                            <?php if ($rwgetRole['review_track'] == '1') { ?> 
                                <li><a href="sentreview"><i class="fa fa-paper-plane-o"></i><?php echo $lang['sentreview']; ?></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['review_intray'] == '1') { ?> 
                                <li><a href="reviewintray"><i class="fa fa-dropbox" aria-hidden="true"></i><?php echo $lang['reviewintray']; ?></a></li>

                            <?php } ?>

                        </ul>
                    </li>
                <?php } ?>
                <?php if ($rwgetRole['add_holiday'] == '1' || $rwgetRole['edit_holiday'] == '1' || $rwgetRole['view_holiday'] == '1' || $rwgetRole['delete_holiday'] == '1') { ?> 
                    <li class="has_sub"><a href="#"><i class="fa fa-calendar"></i> <span><?php echo $lang['holiday_manager']; ?></span> <span class="menu-arrow"></span></a>
                        <ul>
                            <?php if ($rwgetRole['add_holiday'] == '1' || $rwgetRole['edit_holiday'] == '1' || $rwgetRole['view_holiday'] == '1' || $rwgetRole['delete_holiday'] == '1') { ?> 
                                <li class="has_sub"><a href="holiday"><i class="fa fa-list"></i> <span><?php echo $lang['holiday_list']; ?></span></a>
                                <?php } ?> 

                                <?php if ($rwgetRole['view_holiday'] == '1') { ?>  
                                <li class="has_sub"><a href="working-day"><i class="fa fa-eye"></i><span><?php echo $lang['holiday_view']; ?></span></a>
                                <?php } ?>
                        </ul>
                    </li>
                <?php } ?> 
                <?php if ($rwgetRole['view_faq'] == '1' || $rwgetRole['add_faq'] == '1' || $rwgetRole['edit_faq'] == '1' || $rwgetRole['del_faq'] == '1' || $rwgetRole['feedback_msg'] == '1') { ?> 
                    <li class="has_sub"><a href="#"><i class="fa fa-life-ring"></i> <span><?php echo $lang['supprt']; ?></span> <span class="menu-arrow"></span></a>
                        <ul>
                            <?php if ($rwgetRole['view_faq'] == '1' || $rwgetRole['add_faq'] == '1' || $rwgetRole['edit_faq'] == '1' || $rwgetRole['del_faq'] == '1') { ?> 
                                <li class="has_sub"><a href="https://ezeedigitalsolutions.in/faq.php" target="_blank"><i class="fa fa-fw fa-question-circle"></i> <span><?php echo $lang['FAQ_Help']; ?></span></a>
                                <?php } ?> 

                                <?php if ($rwgetRole['feedback_msg'] == '1') { ?>  
                                <li class="has_sub"><a href="https://ezeedigitalsolutions.in/contactus.php" target="_blank"><i class="fa fa-comments"></i><span><?php echo $lang['Fdbk']; ?></span></a>
                                <?php } ?>
                        </ul>
                    </li>
                <?php } ?> 
                <?php if (isset($rwgetRole['create_client']) && $rwgetRole['create_client'] == '1') { ?>
                    <li class="has_sub"><a href="#"><i class="fa fa-fw fa-area-chart"></i> <span>Client Creation</span> <span class="menu-arrow"></span></a>
                        <ul>

                            <li><a href="client_create"><i class="fa fa-user"></i>Create Client</a></li>
    <!--                                  <li><a href="plantype"><i class="fa fa-plus"></i>Add PlanType</a></li>-->

                            <li><a href="clientlist"><i class="fa fa-list"></i>View Client</a></li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ($rwgetRole['backup'] == '1') { ?> 
                    <li class="has_sub"><a href="#"><i class="icon-span-filestyle glyphicon glyphicon-folder-open"></i> <span>Administrative Tool</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <?php if ($rwgetRole['backup'] == '1') { ?> 
                                <li class="has_sub"><a href="backupPolicy" ><i class="fa fa-hdd-o"></i> <span>Backup Policy</span></a>    
                                <li class="has_sub"><a href="restore" ><i class="fa fa-hdd-o"></i> <span>Restore BackUp</span></a>
                                <?php } ?>
                            <?php } ?>


                            <?php if ($rwgetRole['view_apikey'] == '1') { ?>
                            <li class="has_sub"><a href="managekey"><i class="fa fa-sign-in" aria-hidden="true"></i><span>Manage API Key</span></a>
                                    <?php } ?>


                    </ul>
                    </div>
                    </div>
                    </div>

