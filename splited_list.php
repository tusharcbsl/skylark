<div class="col-md-12">
    <?php
    $where = '';
    $document_name=base64_decode($_REQUEST[id]);
    if (isset($_GET['quicksearch']) && !empty($_GET['quicksearch'])) {
        $user_id1 = $_SESSION['cdes_user_id'];
        $chekUsr1 = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id1', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
        $rwcheckUser1 = mysqli_fetch_assoc($chekUsr1);
        if ($rwcheckUser1['role_id'] == 1) {
            $where = "where old_doc_name LIKE '%$_GET[quicksearch]%' and doc_name = '$document_name'";
        } else {
            $where = "where old_doc_name LIKE '%$_GET[quicksearch]%' and doc_name = '$document_name' and flag_multidelete=1";
        }
    } else {
        $user_id1 = $_SESSION['cdes_user_id'];
        $chekUsr1 = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id1', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
        $rwcheckUser1 = mysqli_fetch_assoc($chekUsr1);
        if ($rwcheckUser1['role_id'] == 1) {
            $where = "where doc_name = '$document_name' and flag_multidelete=1";
        } else {
            $where = "where doc_name = '$document_name' and flag_multidelete=1";
        }
    }
    // print_r("select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id1', user_ids) > 0");
   $constructs = "SELECT doc_id,flag_multidelete FROM tbl_document_master_for_split_pdf $where";
//   die("rrrrr");
    $run = mysqli_query($db_con, $constructs) or die('Error' . mysqli_error($db_con));
    $foundnum = mysqli_num_rows($run);
    if ($foundnum > 0) {
        if (is_numeric($_GET['limit'])) {
            $per_page = $_GET['limit'];
        } else {
            $per_page = 10;
        }
        $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
        $max_pages = ceil($foundnum / $per_page);
        if (!$start) {
            $start = 0;
        }
        $getTpages = "SELECT SUM(noofpages) as totalPages FROM(SELECT noofpages FROM tbl_document_master_for_split_pdf $where LIMIT $start, $per_page) tbl_document_master_for_split_pdf";
        $totalp = mysqli_query($db_con, $getTpages) or die("Error: " . mysqli_error($db_con));
        $rowT = mysqli_fetch_assoc($totalp);
        $rowT['totalPages'];
        if (isset($_GET['dtype']) and $_GET['dtype'] == '1') {
            $cond = " dateposted ASC";
        } else {
            $cond = " dateposted DESC";
        }

        $allot_query = mysqli_query($db_con, "select * from tbl_document_master_for_split_pdf $where order by $cond LIMIT $start, $per_page") or die("Error: " . mysqli_error($db_con));

    ?>
        <div class="box box-primary">
            <h4 id="event_result" class="header-title" style="display: inline-block;"><?php echo "Listing"; ?> </h4>
            <div class="box-body">
                <!-- <div class="col-sm-12">
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-danger waves-effect btn-xs" data-placement="left" data-toggle="tooltip" title="Retention Document">Retention</button>
                        <button type="button" class="btn btn-default waves-effect btn-xs" data-toggle="tooltip" title="Primary Document"> Primary</button>
                        <button type="button" class="btn btn-success waves-effect btn-xs" data-toggle="tooltip" title="Checkout Document"> Checkout</button>
                        <button type="button" class="btn btn-warning waves-effect btn-xs" data-toggle="tooltip" title="Expired Document"> Expired</button>
                        <button type="button" class="btn btn-info waves-effect btn-xs" data-toggle="tooltip" data-placement="left" title="Expiry & Retention Document"> Both</button>

                    </div>
                </div> -->
                <label><?php echo $lang['Show']; ?></label> <select id="limit" class="input-sm">
                    <option value="10" <?php
                                        if ($_GET['limit'] == 10) {
                                            echo 'selected';
                                        }
                                        ?>>10</option>
                    <option value="30" <?php
                                        if ($_GET['limit'] == 30) {
                                            echo 'selected';
                                        }
                                        ?>>30</option>
                    <option value="50" <?php
                                        if ($_GET['limit'] == 50) {
                                            echo 'selected';
                                        }
                                        ?>>50</option>
                    <option value="250" <?php
                                        if ($_GET['limit'] == 250) {
                                            echo 'selected';
                                        }
                                        ?>>250</option>
                    <option value="500" <?php
                                        if ($_GET['limit'] == 500) {
                                            echo 'selected';
                                        }
                                        ?>>500</option>
                    <option value="1000" <?php
                                            if ($limit == 1000) {
                                                echo 'selected';
                                            }
                                            ?>>1000</option>
                </select> <label><?php echo ' ' . $lang['Documents']; ?></label>
                <div class="pull-right record">
                    <label> <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                                                if (($start + $per_page) > $foundnum) {
                                                                                    echo $foundnum;
                                                                                } else {
                                                                                    echo ($start + $per_page);
                                                                                }
                                                                                ?> <span> <?php echo $lang['Ttal_Rcrds']; ?> : <?php echo $foundnum; ?> </span>

                    

                </div>
            </div>
            <table class="table table-striped table-bordered js-sort-table">
                <thead>
                    <tr>
                        <th class="js-sort-none">
                            <div class="checkbox checkbox-primary"><input type="checkbox" class="checkbox-primary" id="select_all"> <label for="checkbox6"> <strong><?php echo $lang['All']; ?></strong></label></div>
                        </th>
                        <th class="js-sort-none"><?php echo $lang['File_Name']; ?> <i <?php if ($_GET['stype'] == '2') { ?> class="fa fa-sort" <?php } else { ?> class="fa fa-sort" <?php } ?> id="nmae_car"></i></th>
                        <!-- <th class="js-sort-none"><?php echo $lang['File_Size']; ?> </th>
                        <th class="js-sort-none"><?php echo $lang['No_of_Pages']; ?> </th> -->
                        <!-- <th><?php echo $lang['Upld_By']; ?> <i class="fa fa-sort"></i></th>
                       
                        <th class="js-sort-none" id="date_col"><?php echo $lang['Upld_Date']; ?> <i class="fa fa-sort"></i></th> -->
                        <th class="js-sort-none"><?php echo $lang['Actions']; ?></th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $n = $start + 1;
                    while ($file_row = mysqli_fetch_assoc($allot_query)) {
                        
                           ?>
                            <td>
                                <div class="checkbox checkbox-primary m-r-15"> <input type="checkbox" class="checkbox-primary emp_checkbox" data-doc-id="<?php echo $file_row['doc_id']; ?>" id="shreId"> <label for="checkbox6"> <?php echo $n . '.'; ?> </label></div>


                                <?php
                                $shareDid = mysqli_query($db_con, "select doc_ids from tbl_document_share_for_split_pdf where doc_ids= '$file_row[doc_id]'") or die("Error: " . mysqli_error($db_con));
                                $shreCount = mysqli_num_rows($shareDid);
                                if ($shreCount > 0) {
                                ?>
                                    <span class="md md-share" style="font-size: 15px; color: #193860;" title="Shared document"></span>
                                <?php } ?>
                                <?php
                                if ($subsCountId > 0) {
                                ?>
                                    <span class="fa fa-bell-o" style="font-size: 15px; color: #193860;" title="Subscribe document"></span>
                                <?php } ?>
                            </td>
                            <td>
                                <div style="overflow: hidden; max-width:200px;" title="<?php echo $file_row['old_doc_name']; ?>"><?php if (file_exists('thumbnail/' . base64_encode($file_row['doc_id']) . '.jpg')) { ?><div> <img class="thumb-image" src="thumbnail/<?= base64_encode($file_row['doc_id']) ?>.jpg"> </div>
                                    <?php }
                                                                                                                                              echo $file_row['old_doc_name']; ?></div>
                                <?php if (mysqli_num_rows($isLinkFile) > 0) { ?><a href="javascript:void(0)" data-toggle="modal" data-target="#linkedfiles" onclick="return getLinkedFiles(<?php echo $file_row['doc_id'] ?>, <?php echo $slid; ?>);" title="Linked Document"><i class="fa fa-link"></i></a><?php } ?>

                            </td>

                            <!-- <td><?php
                               // echo formatSizeUnits($file_row['doc_size']);
                                ?> </td>
                            <td><?php
                               // echo $file_row['noofpages'];
                                 ?></td> -->
                            <?php
                            mysqli_set_charset($db_con, "utf8");
                            $userName = "SELECT first_name,last_name FROM tbl_user_master WHERE user_id = '$file_row[uploaded_by]'";
                            $userName_run = mysqli_query($db_con, $userName) or die("Error: " . mysqli_error($db_con));
                            $rwuserName = mysqli_fetch_assoc($userName_run)
                            ?>
                            <!-- <td><?php //echo $rwuserName['first_name'] . " " . $rwuserName['last_name']; ?></td> -->

                            <?php if ($rwgetRole['add_loc'] == 1 || $rwgetRole['edit_loc'] == 1 || $rwgetRole['view_loc'] == 1) { ?>

                              
                            <?php } ?>
                            <!-- <td><?php //echo date('d-m-Y h:i A', strtotime($file_row['dateposted'])); ?></td> -->

                            <td>
                                <li class="dropdown top-menu-item-xs">
                                    <?php
                                    $checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$file_row[doc_id]' and is_active='1'");
                                    if (mysqli_num_rows($checkfileLockqry) > 0) {
                                        $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$file_row[doc_id]' and user_id='" . $_SESSION['cdes_user_id'] . "' and is_active='1'");
                                        if (mysqli_num_rows($checkfileLock) > 0) {

                                    ?>
                                            <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-gear" title="<?php echo $lang['view_actions']; ?>"></i></a>

                                            <ul class="dropdown-menu pdf gearbody">
                                                <li>
                                                    <?php
                                                    if ($file_row['checkin_checkout'] == 1) {

                                                        require 'view-handler.php';
                                                    ?>
                                                </li>
                                                <?php if (($rwFolder['is_protected'] == 0 || $_SESSION['pass'] == $rwFolder['password']) && $status == 1) { ?>

                                                    <li>
                                                        <?php
                                                            /* ------Lock file code----- */
                                                            if ($rwgetRole['lock_file'] == '1') {
                                                                $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$file_row[doc_id]' and user_id='" . $_SESSION['cdes_user_id'] . "' and is_active='1'");
                                                                if (mysqli_num_rows($checkfileLock) > 0) {
                                                                    $fetchdatalock = mysqli_fetch_assoc($checkfileLock);
                                                                    if ($fetchdatalock['is_locked'] == "1") {
                                                        ?>
                                                                    <a href="javascript:void(0)" class="unlock_file" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-unlock" title="<?php echo $lang['unlock_file']; ?>"></i> <?php echo $lang['unlock_file']; ?></a>
                                                                <?php
                                                                    }
                                                                } else {
                                                                ?>
                                                                <a href="javascript:void(0)" class="lock_file" data="<?php echo $file_row['doc_id'] ?>"> <i class="fa fa-lock" title="<?php echo $lang['lock_file']; ?>"></i> <?php echo $lang['lock_file']; ?></a>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </li>
                                                    <?php if ($rwgetRole['link_document'] == '1') { ?>
                                                        <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#linkedDocument" onclick="return setLinkFileDetails('<?php echo $file_row['doc_id'] ?>', '<?php echo $file_row['old_doc_name'] ?>')"><i class="fa fa-external-link"></i> <?php echo $lang['link_document']; ?></a></li>
                                                    <?php }
                                                            if ($rwgetRole['view_metadata'] == '1') { ?>
                                                        <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#filemeta-modal" data="metaData<?php echo $n; ?>" id="viewMeta" onclick="getFileMetaData(<?php echo $file_row['doc_id'] ?>,<?php echo $slid ?>);"><i class="fa fa-eye"></i> <?php echo $lang['View_MetaData']; ?></a></li>

                                                        <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal-history" onclick="return getFileHistory(<?php echo $file_row['doc_id'] ?>,<?php echo $slid ?>)"><i class="fa fa-history"></i> <?php echo $lang['history']; ?></a></li>

                                                    <?php }
                                                            if ($rwgetRole['file_review'] == '1') { ?>
                                                        <?php if ((strtolower($file_row['doc_extn']) == 'pdf' || strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
                                                            <li> <a href="reviewers?i=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])) ?>&doc_Id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="moveTorw" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa fa-send"></i> <?php echo $lang['Review']; ?></a></li><?php } ?>

                                                    <?php }
                                                            if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                                        <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#assign-workflow" id="moveToWf" data="<?php echo $file_row['doc_id']; ?>"> <i class="fa fa-plus"></i> <?php echo $lang['Workflow']; ?></a></li>
                                                    <?php }
                                                            if (strtolower($file_row['doc_extn']) == 'pdf' && $rwgetRole['pdf_annotation']) { ?>
                                                        <li> <a href="anott/add-delete-page?id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1" target="_blank"> <i class="fa fa-plus"></i> <?php echo $lang['add_delete_pages']; ?></a></li>
                                                    <?php } ?>
                                                    <?php if ($rwgetRole['checkin_checkout'] == '1') { ?>
                                                        <li><a href="javascript:void(0)" id="checkout" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-out"></i> <?php echo $lang['Chk_Out']; ?></a></li>
                                                    <?php
                                                            }
                                                            if ($rwgetRole['file_delete'] == '1') {
                                                    ?>
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#con-close-modal2" id="removeRow" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-trash"></i> <?php echo $lang['Delete']; ?> </a></li>
                                            <?php
                                                            }
                                                        }
                                                    } else {
                                                        require 'checkout-action.php';
                                                    }
                                            ?>
                                            </ul>
                                        <?php } else {

                                        ?>
                                            <a href="javascript:void(0)" data="<?php echo $file_row['doc_id']; ?>" class="send_lock_request dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="md md-lock" title="<?php echo $lang['lock_file']; ?>" style="font-size: 18px;"></i> <?php echo $lang['lock_file']; ?></a>

                                        <?php
                                        }
                                    } else {
                                        ?>
                                      
                                               <div>
                                                    <a href="flipflop-viewer-splt?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank"><i class="fa fa-eye" title="<?php echo "Flip-Flop view"; ?>"></i></a>
                                                </div>
                                                    <span>
                                                    <a href="viewer-splt?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank">
                                                    <i class="fa fa-file-pdf-o" data-toggle="tooltip" title="<?php echo $lang['pdf_file']; ?>"></i></a>
                                                 </span>
                                              
                                          
                                    <?php }
                                    ?>
                                </li>
                            </td>
                            </tr>
                        <?php
                        $n++;
                    }
                        ?>

                </tbody>
            </table>
            <ul class="delete_export pull-right">
            <li>
                <button class="rows_selected btn btn-primary btn-sm" id="shareFiles1" data-toggle="modal" data-target="#share-selected-files11"><i data-toggle="tooltip" title="<?php echo $lang['Share_files']; ?>" class="fa fa-share-alt"></i></button>
                </li>
                <li>
                <button class="rows_selected btn btn-primary btn-sm" id="mailFiles1" data-toggle="modal" data-target="#mail-selected-files"><i data-toggle="tooltip" title="<?php echo $lang['mail_files']; ?>" class="fa fa-envelope-o"></i></button>       
            </li>
            </ul>
            <?php
            $paginationslid = urlencode(base64_encode($slid));
            echo "<center>";
            $prev = $start - $per_page;
            $next = $start + $per_page;
            $adjacents = 3;
            $last = $max_pages - 1;
            if ($max_pages > 1) {
                if (isset($_GET['stype']) and $_GET['stype'] != '') {
                    $stype = "&stype=" . $_GET['stype'];
                } else {
                    $stype = '';
                }
                if (isset($_GET['dtype']) and $_GET['dtype'] != '') {
                    $dtype = "&dtype=" . $_GET['dtype'];
                } else {
                    $dtype = '';
                }
            ?>
                <ul class='pagination strgePage'>
                    <?php
                    //previous button
                    if (!($start <= 0))
                        echo " <li><a href='?id=" . $paginationslid . "&start=$prev&limit=$per_page" . $stype . $dtype . "'>" . $lang['Prev'] . "</a> </li>";
                    else
                        echo " <li class='disabled'><a href='javascript:void(0)'>" . $lang['Prev'] . "</a> </li>";
                    //pages 
                    if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                        $i = 0;
                        for ($counter = 1; $counter <= $max_pages; $counter++) {
                            if ($i == $start) {
                                echo " <li class='active'><a href='?id=" . $paginationslid . "&start=$i&limit=$per_page" . $stype . $dtype . "'><b>$counter</b></a> </li>";
                            } else {
                                echo "<li><a href='?id=" . $paginationslid . "&start=$i&limit=$per_page" . $stype . $dtype . "'>$counter</a></li> ";
                            }
                            $i = $i + $per_page;
                        }
                    } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                        //close to beginning; only hide later pages
                        if (($start / $per_page) < 1 + ($adjacents * 2)) {
                            $i = 0;
                            for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                if ($i == $start) {
                                    echo " <li class='active'><a href='?id=" . $paginationslid . "&start=$i&limit=$per_page" . $stype . $dtype . "'><b>$counter</b></a></li> ";
                                } else {
                                    echo "<li> <a href='?id=" . $paginationslid . "&start=$i&limit=$per_page" . $stype . $dtype . "'>$counter</a> </li>";
                                }
                                $i = $i + $per_page;
                            }
                        }
                        //in middle; hide some front and some back
                        elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                            echo " <li class='active'><a href='?id=" . $paginationslid . "&start=0'>1</a></li> ";
                            echo "<li><a href='?id=" . $paginationslid . "&start=$per_page&limit=$per_page" . $stype . $dtype . "'>2</a></li>";
                            echo "<li><a href='javascript:void(0)'>...</a></li>";

                            $i = $start;
                            for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                if ($i == $start) {
                                    echo " <li class='active'><a href='?id=" . $paginationslid . "&start=$i&limit=$per_page" . $stype . $dtype . "'><b>$counter</b></a></li> ";
                                } else {
                                    echo " <li><a href='?id=" . $paginationslid . "&start=$i&limit=$per_page" . $stype . $dtype . "'>$counter</a> </li>";
                                }
                                $i = $i + $per_page;
                            }
                        }
                        //close to end; only hide early pages
                        else {
                            echo "<li> <a href='?id=" . $paginationslid . "&start=0'>1</a> </li>";
                            echo "<li><a href='?id=" . $paginationslid . "&start=$per_page&limit=$per_page" . $stype . $dtype . "'>2</a></li>";
                            echo "<li><a href='javascript:void(0)'>...</a></li>";

                            $i = $start;
                            for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                if ($i == $start) {
                                    echo " <li class='active'><a href='?id=" . $paginationslid . "&start=$i&limit=$per_page" . $stype . $dtype . "'><b>$counter</b></a></li> ";
                                } else {
                                    echo "<li> <a href='?id=" . $paginationslid . "&start=$i&limit=$per_page" . $stype . $dtype . "'>$counter</a></li> ";
                                }
                                $i = $i + $per_page;
                            }
                        }
                    }
                    //next button
                    if (!($start >= $foundnum - $per_page))
                        echo "<li><a href='?id=" . $paginationslid . "&start=$next&limit=$per_page" . $stype . $dtype . "'>" . $lang['Next'] . "</a></li>";
                    else
                        echo "<li class='disabled'><a href='javascript:void(0)'>" . $lang['Next'] . "</a></li>";
                    ?>
                </ul>
            <?php
            }
            echo "</center>";
            ?>
        </div>
</div>

<?php } else {
?>
    <div class="row p-b-60">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><strong><?php echo $lang['SNO']; ?></strong></th>
                    <th><?php echo $lang['File_Name']; ?></th>
                   
                    <th><?php echo $lang['Actions']; ?></th>
                </tr>
            </thead>
            <tr>
                <td colspan="3">
                    <center><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></center>
                </td>
            </tr>
        </table>
    </div>
<?php }
?>