 <!--Edit metadata-->
       <div id="editmetadata" class="modal fade bs-example-modal-lg" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content"> 
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $lang['Edit_MetaData']; ?></h4> 
                    </div>

                    <div class="modal-body">
                        <div class="row" id="modalModifyMvalue">
                            <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" width="50px"/>
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                        <button type="submit" name="editMetaValue" class="btn btn-primary"><?php echo $lang['Save_checkout']; ?></button> 
                    </div>
                </form>

            </div> 
        </div>
    </div>
        <!--modify starts-->
         <!--start delete model-->
        <div id="con-close-modal21" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h4 class="modal-title"><?php echo $lang['Dlt_Docment']; ?></h4> 
                    </div> 
                    <form method="post">
                        <div class="modal-body">
                            <p style="color: red;"><?php echo $lang['r_u_sr_tht_u_wnt_to_dl_ts_Dc']; ?></p>
                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="uid" name="uid">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['close']; ?></button>
                            <input type="submit" name="deleteDoc" class="btn btn-danger" value="<?php echo $lang['Delete']; ?>">
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal -->
         <!--start delete Version of Document model-->
        <div id="deleteVersion" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; z-index: 100000;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title"><?php echo $lang['Dlt_Vrsn_of_Docment']; ?></h2> 
                    </div> 
                    <form method="post">
                        <div class="panel-body">
                            <p style="color: red;"><?php echo $lang['r_u_sr_tht_u_wt_to_dl_ts_vsn_of_Dc_th_dc_wl_b_dlt_pnt']; ?></p>
                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="docidversion" name="docid" value="">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            <input type="submit" name="deleteVersionDoc" class="btn btn-danger" value="<?php echo $lang['Delete']; ?>">
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal -->
        <!---assign workflow---->
        <div id="assign-workflow" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title"><?php echo $lang['Asgn_in_Wrk_flow']; ?></h4> 
                    </div>
                    <form method="post" class="form-inline" id="wfasign">
                        <div class="modal-body">
                            <label><?php echo $lang['Assign_To']; ?></label>
                            <select class="form-control select2" id="wfid" name="wfid">
                                <option selected disabled style="background: #808080; color: #fff;"><?php echo $lang['Slt_Wrkflw']; ?></option>
                                <?php
                                $sameGroupIDs = array();
                                $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error dddd' . mysqli_error($db_con));
                                while ($rwGroup = mysqli_fetch_assoc($group)) {
                                    $sameGroupIDs[] = $rwGroup['group_id'];
                                }
                                $sameGroupIDs = array_unique($sameGroupIDs);
                                sort($sameGroupIDs);
                                $getWfID = mysqli_query($db_con, "select workflow_id,group_id from tbl_workflow_to_group") or die("Error xxxxc" . mysqli_error($db_con));
                                while ($RwgetWfID = mysqli_fetch_assoc($getWfID)) {
                                    $WFId = $RwgetWfID['workflow_id'];
                                    $group_ids = explode(',', $RwgetWfID["group_id"]);
                                    if (array_intersect($sameGroupIDs, $group_ids)) {

                                        $fetchWorkflow = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$WFId' order by workflow_name asc") or die('Error in fetchworkflow:' . mysqli_error($db_con));
                                        if (mysqli_num_rows($fetchWorkflow) > 0) {
                                            $rwfetchWorkflow = mysqli_fetch_assoc($fetchWorkflow);
                                            ?>
                                            <option value="<?php echo $rwfetchWorkflow['workflow_id']; ?>" name="wrkname"><?php echo $rwfetchWorkflow['workflow_name']; ?></option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>

                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="mTowf" name="mTowf">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            <input type="submit" name="assignTo" class="btn btn-primary" value="<?php echo $lang['Submit'] ?>" >
                        </div>
                    </form>
                </div>
            </div>
        </div>
         <div id="con-close-modal-history" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg"> 
                <div class="panel panel-color panel-primary"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title" id="unexport"><?php echo $lang['history']; ?></h2>
                    </div>
                    <div class="panel-body">
                        <div class="row" id="history-modal-content"></div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                    </div>
                </div> 
            </div>
        </div>
        <div id="con-close-modal2" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <h2 class="panel-title"><?php echo $lang['Dlt_Docment']; ?></h2> 
                    </div> 
                    <form method="post">
                        <div class="panel-body">
                            <p class="text-alert"><?php echo $lang['r_u_sr_tht_u_wnt_to_dl_ts_Dc']; ?></p>
                        </div>
                        <div class="modal-footer"> 
                            <input type="hidden" id="uidd" name="uid">
                            <?php
                            if ($rwgetRole['role_id'] == 1) {
                                ?>
                                <button type="submit" id="yes" name="deleteDoc" class="btn btn-danger" value="Yes"> <i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button>
                                <?php
                            }
                            ?>
                            <button type="submit" id="no" name="deleteDoc" class="btn btn-danger"> <i class="fa fa-recycle"></i>
                                <?php
                                if ($rwgetRole['role_id'] == 1) {
                                    echo $lang['Recycle'];
                                } else {
                                    echo $lang['Delete'];
                                }
                                ?>
                            </button> 
                        </div>
                    </form>
                </div> 
            </div>
        </div><!--ends delete modal -->
<div id="downloadfile" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="panel panel-color panel-danger"> 
            <div class="panel-heading"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h2 class="panel-title" id="download1"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                <h2 class="panel-title" style="display:none;" id="download2"> <?php echo $lang['download_selected_file']; ?></h2> 
            </div>
            <div id="unselectfile">
                <div class="panel-body">
                    <h5 class="text-alert"><?php echo $lang['Pls_slct_Fles_for_download']; ?></h5>
                </div>
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                </div>
            </div>
            <div id="filedownload">
                <form method="post">
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group">
                                <label><?php echo $lang['Wte_Rson_fr_Dnldng_fles']; ?><span class="text-alert">*</span></label>
                                <input type="text" name="reason" id="reason" class="form-control translatetext specialchaecterlock" required="" placeholder="<?php echo $lang['Wte_Rson_fr_Dnldng_fles']; ?>">
                            </div>
                        </div>
                       
                    </div> 
                    <div class="modal-footer">
                        <input type="hidden" id="totaldocId" name="totalfiledownload">
                         <input value="<?php echo $slid; ?>" name="slid" type="hidden">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                        <button type="submit" name="downloadselectedfile" id="downloadselectedfile" class="btn btn-primary"><i class="ti-import"></i> <?php echo $lang['Download'] ?></button>
                    </div>
                </form>
            </div>
        </div> 
    </div>
</div><!-- /.modal -->


<div id="del_send_to_recycle" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h2 class="panel-title" style="display:none;" id="hid"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php echo $lang['Hre_msge']; ?></h2>
                        <h2 class="panel-title" id="confirm"><?php echo $lang['Are_u_confirm']; ?></h2> 
                    </div>
                    <form method="post">
                        <div class="panel-body">
                            <span id="errmessage" style="display:none;"> <h5 class="text-alert"><?php echo $lang['Pls_slt_fles_for_Del']; ?></h5></span>
                            <label class="text-danger" id="hide"><?php echo $lang['r_u_sue_wnt_to_Del_tis_Docs'] ?> ?</label>
                        </div> 
                        <div class="modal-footer">
                            <input type="hidden" id="sl_id1" name="sl_id1">
                            <input type="hidden" id="reDel" name="DelFile">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?= $lang['Close']; ?></button> 
                            <?php
                            if ($rwgetRole['role_id'] == 1) {
                                ?>
                                <button type="submit" id="delyes" name="Delmultiple" class="btn btn-danger" value="Yes"> <i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button>
                                <?php
                            }
                            ?>
                            <button type="submit" id="delno" name="Delmultiple" class="btn btn-danger"> <i class="fa fa-recycle"></i>
                                <?php
                                if ($rwgetRole['role_id'] == 1) {
                                    echo $lang['Recycle'];
                                } else {
                                    echo $lang['Delete'];
                                }
                                ?>
                            </button> 
                        </div>
                    </form>

                </div> 
            </div>
        </div>


        <div id="subscribefile" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="panel panel-color panel-danger"> 
            <div class="panel-heading"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h2 class="panel-title" id="subscribe1"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                <h2 class="panel-title" style="display:none;" id="subscribe2"> <?php echo $lang['are_sure_want_subscribe']; ?>?</h2> 
            </div>
            <div id="unsubcribefile">
                <div class="panel-body">
                    <h5 class="text-danger"><?php echo $lang['Pls_slct_Fles_for_subscribe']; ?></h5>
                </div>
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                </div>
            </div>
            <div id="filesubcrbre">
                <form method="post">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="privilege"><?php echo $lang['select_notification_when']; ?><span style="color:red;">*</span></label>
                            <select class="select2 select2-multiple" data-live-search="true"  name="fileactions[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['chos_actn']; ?>" required="">
                                <option value="1">Delete file</option>  
                                <option value="2">Modify keywords & add versioning file</option>  
                                <option value="3">Sent file in workflow</option>  
                                <option value="4">Sent file for review</option>  
                                <option value="5">Share file with other</option>  
                                <option value="6">Move file to other folder</option>  
                                <option value="7">Copy file to other folder</option>  
                                <option value="8">E-mail file outside from DMS</option>  
                            </select>
                        </div>
                    </div> 
                    <div class="modal-footer">
                        <input type="hidden" id="totalsubsdocId" name="totalfilesubscribe">
                        <input value="<?php echo $slid; ?>" name="slid" type="hidden">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                        <button type="submit" name="subscribenow" class="btn btn-primary"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe'] ?></button>
                    </div>
                </form>
            </div>
        </div> 
    </div>
</div><!-- /.modal -->

<div id="subscribe" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="panel panel-color panel-danger"> 
            <div class="panel-heading"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
               <h2 class="panel-title" > <?php echo 'Are you sure subscribe this file'; ?>?</h2> 
            </div>
            <div id="filesubcrbre">
                <form method="post">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="privilege"><?php echo $lang['select_notification_when']; ?><span style="color:red;">*</span></label>
                            <select class="select2 select2-multiple" data-live-search="true"  name="fileactions[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['chos_actn']; ?>" required="">
                                <option value="1">Delete file</option>  
                                <option value="2">Modify keywords & add versioning file</option>  
                                <option value="3">Sent file in workflow</option>  
                                <option value="4">Sent file for review</option>  
                                <option value="5">Share file with other</option>  
                                <option value="6">Move file to other folder</option>  
                                <option value="7">Copy file to other folder</option>
                                <option value="8">E-mail file outside from DMS</option>  
                            </select>
                        </div>
                    </div> 
                    <div class="modal-footer">
                        <input type="hidden" id="singlesubsdocId" name="singlesubsdocId">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                        <button type="submit" name="subscribe" class="btn btn-primary"><i class="fa fa-bell-o"></i> <?php echo $lang['subscribe'] ?></button>
                    </div>
                </form>
            </div>
        </div> 
    </div>
</div><!-- /.modal -->


<!-- for export OCR folder-->
<div id="exportocr-modal" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h4 class="modal-title">Export OCR</h4> 
            </div> 
            <form method="post" action="exportocr">

                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label>Select Format<span class="text-alert">*</span></label>
                           <select class="form-control select2" name="exaction" id="exaction" required >
								<option value="">Select Format</option>
								<option value="HTML">HTML</option>
								<option value="RTF">RTF</option>
								<option value="XML">XML</option>
						   </select>
                        </div>
                    </div>      

                </div>
                <div class="modal-footer"> 
                    <input value="<?php echo $rwFolder['sl_id']; ?>" name="exslid" type="hidden" >
					<input  name="exdocid" type="hidden" id="exdocid" >
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?= $lang['Close']; ?></button> 
                    <button type="submit" name="exportocr" class="btn btn-primary"><?= $lang['Submit']; ?></button>
                </div>
            </form>
        </div>
    </div> 
</div>

<div id="filemeta-modal" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h4 class="modal-title">File MetaData</h4> 
            </div> 

                <div class="modal-body">
                    <div class="row" id="filemetadata">
                       
                    </div>      
                </div>
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?= $lang['Close']; ?></button> 
                </div>
        </div>
    </div> 
</div>
<!-- //ANKIT 02 june 2023 -->
<div id="changemultFleame" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="panel panel-color panel-primary">

            <div class="panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?> </h2>
            </div>
            <div class="modal-footer" id="unselectdocument">

                <div class="panel-body">
                    <h5 class="text-alert text-left"><?php echo $lang['Pls_slct_Fles_for_renm']; ?></h5>
                </div>

                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>

            </div>
            <div id="filedocument">
                <form method="post">
                    <div class="panel-body" id="multirefilee">

                    </div>
                    <div class="modal-footer">

                        <input type="hidden" id="multidocid" name="docmultiId">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                        <button type="submit" name="multirename" class="btn btn-primary"> <?php echo $lang['Submit']; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- //Ankit end 02 june 2023 -->