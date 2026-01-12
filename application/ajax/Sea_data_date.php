<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once './loginvalidate.php';
require_once './application/config/database.php';
if (isset($_SESSION['lang'])){
     $file = "../../".$_SESSION['lang'].".json";
 } else {
     $file = "../../English.json";
 }
 //for user role
$data = file_get_contents($file);
 $lang = json_decode($data, true);
$allot = "select * from tbl_document_master $where order by old_doc_name LIMIT $start, $per_page";
 $allot_query = mysqli_query($db_con, $allot) or die("Error: " . mysqli_error($db_con));
?>
<table class="table table-striped" >
                                                       
                                                       
                                                            <?php
                                                            $n = $start + 1;
                                                            while ($file_row = mysqli_fetch_assoc($allot_query)) {
                                                                ?>
                                                                <tr class="gradeX">
                                                                    <td> 

                                                                        <input  type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $file_row['doc_id']; ?>">
                                                                        <?php echo $n; ?>
                                                                    </td>
                                                                    <td> <div style="overflow: hidden; max-width:200px;" title="<?php echo $file_row['old_doc_name']; ?>"><?php echo $file_row['old_doc_name']; ?></div></td>
                                                                    <td ><?php
                                                                        $size = round($file_row['doc_size'] / 1024 / 1024, 2);
                                                                        if ($size <= 0) {
                                                                            echo $file_row['doc_size'] / 1024;
                                                                        } else {
                                                                            echo $size;
                                                                        }
                                                                        ?> MB</td>
                                                                    <td><?php echo $file_row['noofpages']; ?></td>
                                                                    <?php
                                                                    $userName = "SELECT first_name,last_name FROM tbl_user_master WHERE user_id = '$file_row[uploaded_by]'";
                                                                    $userName_run = mysqli_query($db_con, $userName) or die("Error: " . mysqli_error($db_con));

                                                                    $rwuserName = mysqli_fetch_assoc($userName_run)
                                                                    ?>
                                                                    <td><?php echo $rwuserName['first_name'] . " " . $rwuserName['last_name']; ?></td>
                                                                    <td><?php echo $file_row['dateposted']; ?></td>

                                                                    <td>

                                                            <li class="dropdown top-menu-item-xs">
                                                                <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gear"></i></a>
                                                                 <ul class="dropdown-menu pdf gearbody">
                                                                    
                                                                    <?php if($file_row['checkin_checkout']==1){
                                                                        if($file_row['doc_extn'] == 'pdf') { ?>
                                                                    <li>    <?php if ($rwgetRole['pdf_file'] == '1') { ?>
                                                                            <a href="flipflop-viewer?file=extract-here/<?php echo $file_row['doc_path']; ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank">
                                                                                    <i class="ti-book" style="font-size: 18px;"></i></a>

                                                                                <a href="viewer?i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank">
                                                                                    <i class="fa fa-file-pdf-o"></i></a>
                                                                            <?php } ?>
                                                                            <!--for tooltip on pdf-->   
                                                                            <?php if ($rwgetRole['pdf_annotation'] == '1') { ?>
                                                                                <a href="anott/index.php?file=extract-here/<?php echo $file_row['doc_path']; ?>&id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" class="pdfview" target="_blank">
                                                                                    <i class="fa fa fa-file-text-o"></i>
                                                                                </a> 
                                                                            <?php
                                                                        }
                                                                    } else if ($file_row['doc_extn'] == 'jpg' || $file_row['doc_extn'] == 'png' || $file_row['doc_extn'] == 'gif') {
                                                                        ?>
                                                                         <a href="#" data-toggle="modal" data-target="#full-width-modal" id="showPic" data="extract-here/<?php echo $file_row['doc_path']; ?>" >
                                                                                <?php if ($rwgetRole['image_file'] == '1') { ?>
                                                                                    <i class="fa fa-file-image-o"></i><?php echo $lang['Image'];?></a>
                                                                            <?php } ?>
                                                                    <?php } else if ($file_row['doc_extn'] == 'tif' || $file_row['doc_extn'] == 'tiff') { ?>
                                                                         <a href="file?file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" >
                                                                                <?php if ($rwgetRole['tif_file'] == '1') { ?>
                                                                                    <i class="fa fa-picture-o"></i>
                                                                                </a>
                                                                            <?php } ?>
                                                                        
                                                                    <?php } else if ($file_row['doc_extn'] == 'xlsx' || $file_row['doc_extn'] == 'xls') {
                                                                        ?>
                                                                        <a href="excel?file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank">
                                                                                <?php if ($rwgetRole['excel_file'] == '1') { ?>
                                                                                    <i class="fa fa-file-excel-o"></i> <?php echo $lang['Execl_file'];?></a>
                                                                            <?php } ?>
                                                                        
                                                                    <?php } else if ($file_row['doc_extn'] == 'doc' || $file_row['doc_extn'] == 'docx') { ?>
                                                                         <a href="docx?file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank">
                                                                                <?php if ($rwgetRole['doc_file'] == '1') { ?>
                                                                                    <i class="fa fa-file-word-o"></i><?php echo $lang['Word_file'];?></a>
                                                                            <?php } ?>
                                                                        
                                                                    <?php } else if ($file_row['doc_extn'] == 'mp3' || $file_row['doc_extn'] == 'wav') { ?>
                                                                      <!--a class="" href="#modal-audio" data-uk-modal=""><i class="fa fa-music"></i> </a-->
                                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-audio" data="<?php echo $file_row['doc_id']; ?>" id="audio">
                                                                                <?php if ($rwgetRole['audio_file'] == '1') { ?>
                                                                                    <i class="fa fa-music"></i> <?php echo $lang['Audio'];?> </a>
                                                                            <?php } ?>
                                                                        
                                                                    <?php } else if ($file_row['doc_extn'] == 'mp4' || $file_row['doc_extn'] == '3gp') { ?>
                                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video" data="<?php echo $file_row['doc_id']; ?>" id="video">
                                                                                <?php if ($rwgetRole['video_file'] == '1') { ?>
                                                                                    <i class="fa fa-video-camera"></i><?php echo $lang['Video'];?></a>
                                                                            <?php } ?>                                                                        
                                                                    <?php } else {
                                                                        ?>
                                                                        <a href="extract-here/<?php echo $file_row['doc_path']; ?>" id="fancybox-inner" target="_blank"> <i class="fa fa-download"></i> <?php echo $file_row['old_doc_name']; ?>
                                                                            </a>
                                                                    <?php }
                                                                    ?>
                                                                </li>

                                                                    

                                                                    <?php if ($rwgetRole['file_edit'] == '1') { ?>
                                                                        <!--<li> <a href="javascript:void(0)" data-toggle="modal" data-target="#editmetadata" id="editMdata" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-edit"></i> Edit MetaData</a></li>-->
                                                                    <?php } ?>
                                                                    <li> <a href="javascript:void(0)" data="metaData<?php echo $n; ?>" id="viewMeta"><i class="fa fa-eye"></i><?php echo $lang['View_MetaData'];?></a></li>
                                                                    <?php if ($rwgetRole['file_delete'] == '1') { ?>
                                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete'];?></a></li>
                                                                    <?php } ?>
                                                                    <?php if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                                        <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-plus"></i><?php echo $lang['Workflow'];?></a></li>
                                                                        <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-download"></i> <?php echo $lang['Chk_In'];?></a></li>
                                                                        <?php
                                                                    }
                                                                    }else{
                                                                        ?>
                                                                        <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#editmetadata" id="editMdata" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-edit"></i><?php echo $lang['Chk_In'];?></a></li>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </li>
                                                            </td>
                                                            </tr>
                                                            <tr>

                                                                <td colspan="20">
                                                                    <div id="metaData<?php echo $n; ?>"  class="metadata">
                                                                        <?php
                                                                        // echo "SELECT * FROM tbl_document_master where substring_index(doc_name,'_',-1)='$file_row[doc_id]' and substring_index(doc_name,'_',1)='$slid'";
                                                                        //view version
                                                                        $versionView = mysqli_query($db_con, "SELECT * FROM tbl_document_master where substring_index(doc_name,'_',-1)='$file_row[doc_id]' and substring_index(doc_name,'_',1)='$slid' ") or die("Error: " . mysqli_error($db_con));
                                                                        if (mysqli_num_rows($versionView) > 0) {

                                                                            $i = 1.0;
                                                                            while ($rwView = mysqli_fetch_assoc($versionView)) {
                                                                                if ($rwgetRole['file_version'] == '1') {
                                                                                    if ($i > 0) {

                                                                                        echo 'Version ' . $i . '-';
                                                                                    }
                                                                                    ?>

                                                                                    <?php if ($rwView['doc_extn'] == 'pdf') { ?>


                                                                                        <a href="viewer?file=extract-here/<?php echo $rwView['doc_path']; ?>&i=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" id="fancybox-inner" target="_blank">
                                                                                            <?php echo $rwView['old_doc_name']; ?>

                                                                                        </a>

                                                                                    <?php } else if ($rwView['doc_extn'] == 'jpg' || $rwView['doc_extn'] == 'png' || $rwView['doc_extn'] == 'gif') { ?>
                                                                                        <a href="#" data-toggle="modal" data-target="#full-width-modal" id="showPic" data="extract-here/<?php echo $rwView['doc_path']; ?>" >
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>
                                                                                    <?php } else if ($rwView['doc_extn'] == 'tif' || $rwView['doc_extn'] == 'tiff') { ?>
                                                                                        <a href="file?file=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" target="_blank" >
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>
                                                                                    <?php } else if ($rwView['doc_extn'] == 'xlsx' || $rwView['doc_extn'] == 'xls') { ?>
                                                                                        <a href="excel?file=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" target="_blank">
                                                                                            <?php echo $rwView['old_doc_name']; ?></a> 
                                                                                        <?php } else if ($rwView['doc_extn'] == 'doc' || $rwView['doc_extn'] == 'docx') { ?>
                                                                                        <a href="docx?file=<?php echo urlencode(base64_encode($rwView['doc_id'])); ?>" target="_blank">
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>
                                                                                    <?php } else if ($rwView['doc_extn'] == 'mp3') { ?>
                                                                                       <!--a class="" href="#modal-audio" data-uk-modal=""><i class="fa fa-music"></i> </a-->
                                                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-audio" data="<?php echo $rwView['doc_id']; ?>" id="audio">
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>
                                                                                    <?php } else if ($rwView['doc_extn'] == 'mp4') { ?>
                                                                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video" data="<?php echo $rwView['doc_id']; ?>" id="video">
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>

                                                                                    <?php } else {
                                                                                        ?>
                                                                                        <a href="extract-here/<?php echo $rwView['doc_path']; ?>" id="fancybox-inner" target="_blank">
                                                                                            <?php echo $rwView['old_doc_name']; ?>
                                                                                        </a>
                                                                                        <?php
                                                                                    }
                                                                                }
                                                                                if ($rwgetRole['delete_version'] == '1') {
                                                                                    ?>
                                                                                    <a href="javascript:void(0)" data="<?php echo $rwView['doc_id']; ?>" data-toggle="modal" data-target="#deleteVersion" id="deleteVersionDoc"><i class="fa fa-trash"></i></a>
                                                                                    <?php
                                                                                }
                                                                                $i = $i + 0.1;
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <?php
                                                                        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$file_row[doc_name]'") or die('Error:' . mysqli_error($db_con));

                                                                        while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                                                            $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                                                            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                                                                                $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$file_row[doc_id]'");
                                                                                $rwMeta = mysqli_fetch_array($meta);
                                                                                if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                                                    if ($rwgetMetaName['field_name'] == 'noofpages' || $rwgetMetaName['field_name'] == 'filename') {
                                                                                        
                                                                                    } else {
                                                                                        echo "<label>" . $rwgetMetaName['field_name'] . "</label> : ";

                                                                                        echo $rwMeta[$rwgetMetaName['field_name']];
                                                                                        echo " | ";
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <?php
                                                            $n++;
                                                        }
                                                        ?>

                                                        

                                                  