<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    //require_once './application/config/database.php';
    require_once './application/pages/head.php';
    //for user role
    mysqli_set_charset($db_con, "utf8");
    $ses_val = $_SESSION;
    if ($rwgetRole['view_metadata'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="https://www.google.com/jsapi" type="text/javascript">
    </script>  

    <script type="text/javascript">

        // Load the Google Transliterate API
        google.load("elements", "1", {
            packages: "transliteration"
        });

        function onLoad() {

            var langcode = '<?php echo $rwgetRole['langCode']; ?>';



            var options = {
                sourceLanguage: 'en',
                destinationLanguage: [langcode],
                shortcutKey: 'ctrl+g',
                transliterationEnabled: true
            };
            // Create an instance on TransliterationControl with the required
            // options.
            var control =
                    new google.elements.transliteration.TransliterationControl(options);

            // Enable transliteration in the text fields with the given ids.
            var ids = ["metadata"];
            control.makeTransliteratable(ids);


            // Show the transliteration control which can be used to toggle between
            // English and Hindi and also choose other destination language.
            // control.showControl('translControl');

        }
        google.setOnLoadCallback(onLoad);

    </script> 
    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <!-- ========== Left Sidebar Start ========== 1001/10556/00959 12/12/2011 14:33:58-->
            <?php require_once './application/pages/sidebar.php'; ?>
            <!-- Left Sidebar End --> 
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->                      
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">
                        <!-- Page-Title -->
                        <div class="row">
                            <ol class="breadcrumb">
                                <li><a href="#"><?php echo $lang['Masters']; ?></a></li>
                                <li class="active"><?php echo $lang['metadat_list']; ?></li>
                                <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="42" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="col-sm-2">
                                        <h4 class="header-title"><?php echo $lang['metadat_list']; ?></h4>
                                    </div>
                                    <form method="get">
                                        <div class="col-md-4">
                                            <input type="text"  id="metadata" name="metadata" value="<?php echo preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#. -]+/u", "", $_GET['metadata']); ?>"class="form-control" placeholder="<?php echo $lang['Search']; ?>.." data-parsley-required-message="Enter metadata name for search" required>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit"  class="btn btn-primary"><?php echo $lang['Search']; ?> <i class="fa fa-search"></i></button>
                                            <a  href="metadata-list" class="btn btn-warning"> <?php echo $lang['Reset']; ?> </a>
                                        </div>
                                        <?php if ($rwgetRole['add_metadata'] == '1') { ?>
                                            <div class="col-sm-2">
                                                <div class="">
                                                    <a href="addFields" class="on-default edit-row btn btn-primary" > <?= $lang['Add_Fields'] ?> <i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </form>
                                </div>

                                <?php
                                mysqli_set_charset($db_con, "utf8");
                                $where = '';
                                if (isset($_GET['metadata']) && !empty($_GET['metadata'])) {
                                    $metaName = preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#. -]+/u", "", $_GET['metadata']);

                                    $metaName = trim($metaName);
                                    $where = "where field_name LIKE '%$metaName%'";
                                }
                                $constructs = "SELECT * FROM tbl_metadata_master $where";
                                mysqli_set_charset($db_con, 'utf8');
                                $run = mysqli_query($db_con, $constructs) or die('Error' . mysqli_error($con));

                                $foundnum = mysqli_num_rows($run);
                                if ($foundnum > 0) {
                                    if (isset($_GET['limit'])) {
                                        if (!empty(preg_replace("/[^0-9]/", "", $_GET['limit']))) {
                                            $per_page = $_GET['limit'];
                                            $per_page = preg_replace("/[^0-9]/", "", $per_page);
                                        } else {
                                            $per_page = 10;
                                        }
                                    } else {
                                        $per_page = 10;
                                        $per_page = preg_replace("/[^0-9]/", "", $per_page);
                                    }
                                    $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                    $max_pages = ceil($foundnum / $per_page);
                                    if (!$start) {
                                        $start = 0;
                                    }
                                    $start = preg_replace("/[^0-9]/", "", $start);

                                    $allot = "select * from tbl_metadata_master $where order by field_name asc LIMIT $start, $per_page ";

                                    $allot_query = mysqli_query($db_con, $allot);
                                    ?>


                                    <div class="box-body">
                                        <div style="overflow-x: auto">
                                            <div class="row">
                                                <div class="col-sm-9 m-b-5">
                                                    <label><?php echo $lang['show_lst']; ?> </label>
                                                    <select id="limit" class="input-sm">
                                                        <option value="10" <?php
                                                        if ($_GET['limit'] == 10) {
                                                            echo 'selected';
                                                        }
                                                        ?>>10</option>
                                                        <option value="25" <?php
                                                        if ($_GET['limit'] == 25) {
                                                            echo 'selected';
                                                        }
                                                        ?>>25</option>
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
                                                    </select> <label><?php echo $lang['Field_Name']; ?></label>
                                                </div>
                                                <div class="col-sm-3 m-b-0">
                                                    <label><?php echo $start + 1 ?> <?php echo $lang['to']; ?> <?php
                                                        if (($start + $per_page) > $foundnum) {
                                                            echo $foundnum;
                                                        } else {
                                                            echo ($start + $per_page);
                                                        }
                                                        ?>  <?php echo $lang['Total_Records']; ?>: <?php echo $foundnum; ?></label>
                                                </div>
                                            </div>

                                            <table class="table table-striped table-bordered js-sort-table">
                                                <thead>
                                                    <tr>
                                                        <th class="sort-js-none" ><?php echo $lang['Sr_No']; ?></th>
                                                        <th><?php echo $lang['Field_Name']; ?></th>
                                                        <th><?php echo $lang['Datatype']; ?></th>
                                                        <th class="sort-js-number" ><?php echo $lang['Data_Length']; ?></th>
                                                        <th><?php echo $lang['Is_Mandatory']; ?></th>
                                                        <th style="width:10px;"><?php echo $lang['label']; ?></th>
                                                        <th style="width:10px;"><?php echo $lang['value']; ?></th>
                                                        <?php if ($rwgetRole['edit_metadata'] == '1' || $rwgetRole['delete_metadata'] == '1'): ?>
                                                            <th style="width: 110px;" class="sort-js-none" ><?php echo $lang['Actions']; ?></th>
                                                        <?php endif; ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $n = $start + 1;
                                                    while ($allot_row = mysqli_fetch_assoc($allot_query)) {
                                                        ?>
                                                        <tr class="gradeX">
                                                            <td><?php echo $n; ?></td>
                                                            <td><?php echo $allot_row['field_name']; ?></td>
                                                            <td><?php
                                                                if ($allot_row['data_type'] == 'boolean') {
                                                                    echo 'Binary';
                                                                } else {
                                                                    echo $allot_row['data_type'];
                                                                }
                                                                ?></td>
                                                            <td><?php echo $allot_row['length_data']; ?></td>

                                                            <td><?php echo $allot_row['mandatory']; ?></td>
                                                            <td> <?php if (!empty($allot_row['label'])) {
                                                                    ?>
                                                                <a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#leaveremark" onclick="getLabelValue('<?php echo trim(preg_replace('/\s+/', ' ', $allot_row['label'])); ?>');" data-toggle="tooltip" title="<?php echo $lang['label']; ?>"><i class="fa fa-eye"></i></a>
                                                                <?php } ?></td>
                                                            <td><?php if (!empty($allot_row['value'])) {
                                                                    ?>
                                                                <a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#listlabelvalue" onclick="getListValue('<?php echo trim(preg_replace('/\s+/', ' ', $allot_row['value'])); ?>');" data-toggle="tooltip" title="<?php echo $lang['value']; ?>"><i class="fa fa-eye"></i></a>
                                                                <?php } ?></td>
                                                            <?php if ($rwgetRole['edit_metadata'] == '1' || $rwgetRole['delete_metadata'] == '1'): ?>
                                                                <td class="actions">
                                                                    <?php if ($rwgetRole['edit_metadata'] == '1'): ?>
                                                                        <a href="#" class="on-default edit-row btn btn-primary" data-toggle="modal" data-target="#con-close-modal" id="editRow" data="<?php echo $allot_row['id']; ?>" title="<?php echo $lang['Edit']; ?>"><i class="fa fa-edit"></i> </a>
                                                                    <?php endif; ?>
                                                                    <?php if ($rwgetRole['delete_metadata'] == '1'): ?>
                                                                        <a href="#" class="on-default remove-row btn btn-danger" data-toggle="modal" data-target="#dialog" id="removeRow" data="<?php echo $allot_row['id']; ?>" title="<?php echo $lang['Delete']; ?>"><i class="fa fa-trash-o"></i></a>
                                                                    <?php endif; ?>
                                                                </td>
                                                            <?php endif; ?>
                                                        </tr>
                                                        <?php
                                                        $n++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>

                                            <?php
                                            $metapage = preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#. -]+/u", "", $_GET['metadata']);
                                            echo "<center>";
                                            $prev = $start - $per_page;
                                            $next = $start + $per_page;

                                            $adjacents = 3;
                                            $last = $max_pages - 1;
                                            if ($max_pages > 1) {
                                                ?>
                                                <ul class='pagination strgePage'>
                                                    <?php
                                                    //previous button
                                                    if (!($start <= 0))
                                                        echo " <li><a href='?start=$prev&limit=$per_page&metadata=" . $metapage . "'>$lang[Prev]</a> </li>";
                                                    else
                                                        echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                    //pages 
                                                    if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                        $i = 0;
                                                        for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                            if ($i == $start) {
                                                                echo "<li class='active'><a href='?start=$i&limit=$per_page&metadata=" . $metapage . "'><b>$counter</b></a> </li>";
                                                            } else {
                                                                echo "<li><a href='?start=$i&limit=$per_page&metadata=" . $metapage . "'>$counter</a></li> ";
                                                            }
                                                            $i = $i + $per_page;
                                                        }
                                                    } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                        //close to beginning; only hide later pages
                                                        if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                            $i = 0;
                                                            for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=$i&limit=$per_page&metadata=" . $metapage . "'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo "<li> <a href='?start=$i&limit=$per_page&metadata=" . $metapage . "'>$counter</a> </li>";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        }
                                                        //in middle; hide some front and some back
                                                        elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                            echo " <li><a href='?start=0&limit=$per_page&metadata=$_GET[metadata]'>1</a></li> ";
                                                            echo "<li><a href='?start=$per_page&limit=$per_page&metadata=$_GET[metadata]'>2</a></li>";
                                                            echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                            $i = $start;
                                                            for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "&metadata=" . $metapage . "'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo " <li><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "&metadata=" . $metapage . "'>$counter</a> </li>";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        }
                                                        //close to end; only hide early pages
                                                        else {
                                                            echo "<li> <a href='?start=0&limit=$per_page&metadata=$_GET[metadata]'>1</a> </li>";
                                                            echo "<li><a href='?start=$per_page&limit=$per_page&metadata=$_GET[metadata]'>2</a></li>";
                                                            echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                            $i = $start;
                                                            for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "&metadata=" . $metapage . "'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo "<li> <a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "&metadata=" . $metapage . "'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        }
                                                    }
                                                    //next button
                                                    if (!($start >= $foundnum - $per_page))
                                                        echo "<li><a href='?start=$next&limit=$per_page&metadata=$_GET[metadata]'>$lang[Next]</a></li>";
                                                    else
                                                        echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
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
                                    <div class="container-fluid">
                                        <table class="table table-striped table-bordered m-t-15">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $lang['Sr_No']; ?></th>
                                                    <th><?php echo $lang['Field_Name']; ?></th>
                                                    <th><?php echo $lang['Datatype']; ?></th>
                                                    <th><?php echo $lang['Data_Length']; ?></th>
                                                    <th><?php echo $lang['label']; ?></th>
                                                    <th><?php echo $lang['value']; ?></th>
                                                    <th><?php echo $lang['Is_Mandatory']; ?></th>
                                                    <?php if ($rwgetRole['edit_metadata'] == '1' || $rwgetRole['delete_metadata'] == '1'): ?>
                                                        <th><?php echo $lang['Actions']; ?></th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tr><td colspan="6">
                                            <center><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></center>
                                            </td></tr>
                                        </table>

                                    <?php }
                                    ?>
                                </div>

                            </div>				
                        </div>
                    </div> <!-- container -->
                </div> <!-- content -->
                <div id="add-metadata" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"><?= $lang['Add_Fields'] ?></h4>
                            </div>

                            <form method="post">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><?php echo $lang['Ent_Fld_Nm']; ?> <span style="color:red;">*</span></label>
                                                <input type="text" id="metaData" class="form-control numspecialcharlock" name="fieldName" placeholder="<?php echo $lang['Ent_Fld_Nm']; ?>" required maxlength="30">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><?php echo $lang['Select_DataType']; ?><span style="color:red;">*</span></label>
                                                <select class="form-control select33" name="dataType" id="selection" onchange="changemetadattype()" required>
                                                    <option selected disabled><?php echo $lang['Select']; ?></option>
                                                    <!--<option valu="bit"><?php echo $lang['Bit']; ?></option>-->
                                                    <option value="char"><?php echo $lang['Char']; ?></option>
                                                    <option value="datetime"><?php echo $lang['datetime']; ?></option>
                                                    <option value="Int"><?php echo $lang['Int']; ?></option>
                                                    <option value="BigInt"><?php echo $lang['BigInt']; ?></option>
                                                    <option value="float"><?php echo $lang['Float']; ?></option>
                                                    <option value="double"><?php echo $lang['Double']; ?></option>
                                                    <option value="varchar"><?php echo $lang['Varchar']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><?php echo $lang['Enter_Data_Length']; ?> <span class="text-alert">*</span></label>
                                                <input type="text" min="0" class="form-control" name="dataLength" id="textbox" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row"> 
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label><?php echo $lang['Is_Mandatory']; ?> ? <span style="color:red;">*</span></label>
                                                <select name="mandatory" class="form-control select33" required>
                                                    <option disabled selected><?php echo $lang['Select']; ?></option>
                                                    <option value="Yes"><?php echo $lang['Yes']; ?></option>
                                                    <option value="No"><?php echo $lang['No']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="addField"><?php echo $lang['Submit']; ?></button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- MODAL -->
                <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="panel panel-color panel-danger"> 
                            <div class="panel-heading"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <label><h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2></label> 
                            </div> 
                            <form method="post">
                                <div class="panel-body">
                                    <p style="color: red;"><?php echo $lang['r_u_sure_wnt_to_del_Mta']; ?></p>
                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-12 text-right">
                                        <input type="hidden" id="uid" name="uid">
                                        <button type="submit" name="delete" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><?php echo $lang['confirm']; ?></button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                    </div>
                                </div>
                            </form>
                        </div> 
                    </div>
                </div>
                <!-- end Modal -->
                <div id="con-close-modal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="modal-content"> 
                            <form method="post" >
                                <div class="modal-header"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                    <h4 class="modal-title"><?php echo $lang['Updt_Mta_Vlu']; ?></h4> 
                                </div>

                                <div class="modal-body" id="modalModify">
                                    <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" width="50px"/>
                                </div> 
                                <div class="modal-footer">

                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                    <button type="submit" name="editMeta" id="addrange" class="btn btn-primary"><?php echo $lang['Save_changes']; ?></button> 
                                </div>
                            </form>

                        </div> 
                    </div>
                </div><!-- /.modal -->
                <div id="leaveremark" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="panel panel-color panel-primary"> 
                            <div class="panel-heading"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button> 
                                <label><h2 class="panel-title"><?php echo $lang['list'].' '.$lang['label']; ?></h2></label> 
                            </div> 
                            <form method="post">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <p id="labelval"></p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                    </div>
                                </div>
                            </form>
                        </div> 
                    </div>
                </div>
                <div id="listlabelvalue" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="panel panel-color panel-primary"> 
                            <div class="panel-heading"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button> 
                                <label><h2 class="panel-title"><?php echo $lang['list'].' '.$lang['label']. ' '. $lang['value']; ?></h2></label> 
                            </div> 
                            <form method="post">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <p id="listval"></p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                                    </div>
                                </div>
                            </form>
                        </div> 
                    </div>
                </div>
                <?php require_once './application/pages/footer.php'; ?>
            </div>
        </div>
        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script>

                                                    function getLabelValue(remark) {
                                                        $('#labelval').text(remark);
                                                    }
                                                    function getListValue(listvalue) {
                                                        $('#listval').text(listvalue);
                                                    }
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('form').parsley();
            });
            $("a#editRow").click(function () {
                var $id = $(this).attr('data');
                var $row = $(this).closest('tr');
                var name = '';
                var values = [];
                var token = $("input[name='token']").val();
                values = $row.find('td:nth-child(2)').map(function () {
                    var $this = $(this);
                    if ($this.hasClass('actions')) {
                    } else {
                        name = $.trim($this.text());
                    }
                    $("#con-close-modal .modal-title").text("<?php echo $lang['Updt_Mta_Dat']; ?>(" + name + ")");
                    $.post("application/ajax/updateMetaData.php", {ID: $id, token:token}, function (result, status) {
                        if (status == 'success') {
                            $("#modalModify").html(result);
                        }
                    });
                });
            });
            $("a#removeRow").click(function () {
                var id = $(this).attr('data');
                $("#uid").val(id);
            });
        </script>
        <script>
            $(".select33").select2();
            //limit filter
            var url = window.location.href + "?";
            function removeParam(key, sourceURL) {
                sourceURL = String(sourceURL).replace("#/", "");
                var rtn = sourceURL.split("?")[0],
                        param,
                        params_arr = [],
                        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
                if (queryString !== "") {
                    params_arr = queryString.split("&");
                    for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                        param = params_arr[i].split("=")[0];
                        if (param === key) {
                            params_arr.splice(i, 1);
                        }
                    }
                    rtn = rtn + "?" + params_arr.join("&");
                } else {
                    rtn = rtn + '?';
                }
                return rtn;
            }
            jQuery(document).ready(function ($) {
                $("#limit").change(function () {
                    lval = $(this).val();
                    url = removeParam("limit", url);
                    url = url + "&limit=" + lval;
                    window.open(url, "_parent");
                });
            });

        </script>


        <script type="text/javascript">
            //for placeholder display
            function changemetadattype() {
                var sel = document.getElementById("selection");
                var indexe = sel.selectedIndex;
                //console.log(indexe);
                if (indexe == 1) {
                    $("#textbox").attr("placeholder", "<?= $lang['Enter_max_characters']; ?>");
                    $("#textbox").attr("required", "required");
                    $("#textbox").val("");
                    $("#errormsg").html("");
                    document.getElementById("textbox").style.borderColor = "grey";
                    $("#textbox").keyup(function () {
                        var valu = $("#textbox").val();
                        console.log(valu);
                        if (valu <= 255)
                        {
                            $(".submit-btn").removeAttr("disabled");
                            $("#errormsg").html("");
                            document.getElementById("textbox").style.borderColor = "grey";

                        } else {

                            $(".submit-btn").attr("disabled", "disabled");
                            $("#errormsg").html("Value should be less or equal to 255");
                            document.getElementById("textbox").style.borderColor = "red";
                        }
                    })
                    $("#textbox").change(function () {
                        var valu = $("#textbox").val();
                        if (valu <= 255)
                        {
                            $(".submit-btn").removeAttr("disabled");
                            $("#errormsg").html("");
                            document.getElementById("textbox").style.borderColor = "grey";

                        } else {
                            $(".submit-btn").attr("disabled", "disabled");
                            $("#errormsg").html("Value should be less or equal to 255");
                            document.getElementById("textbox").style.borderColor = "red";
                        }
                    })
                }
                if (indexe == 3) {
                    $("#textbox").attr("placeholder", "<?= $lang['Enter_max_int_length']; ?>");
                    $("#textbox").attr("required", "required");
                    $(this).keyup(function () {
                        var valu = $("#textbox").val();
                        console.log(valu);
                        if (valu <= 255)
                        {
                            $(".submit-btn").removeAttr("disabled");
                            $("#errormsg").html("");
                            document.getElementById("textbox").style.borderColor = "grey";

                        } else {

                            $(".submit-btn").attr("disabled", "disabled");
                            $("#errormsg").html("Value should be less or equal to 9");
                            document.getElementById("textbox").style.borderColor = "red";
                        }
                    })
                    $(this).change(function () {
                        var valu = $("#textbox").val();
                        if (valu <= 255)
                        {

                            $(".submit-btn").removeAttr("disabled");
                            $("#errormsg").html("");
                            document.getElementById("textbox").style.borderColor = "grey";

                        } else {
                            $(".submit-btn").attr("disabled", "disabled");
                            $("#errormsg").html("Value should be less or equal to 9");
                            document.getElementById("textbox").style.borderColor = "red";
                        }
                    })
                }
                if (indexe == 2) {
                    $("#textbox").removeAttr('required');
                    $("#textbox").val("");
                }
                if (indexe == 4) {
                    $("#textbox").attr("placeholder", "<?= $lang['Enter_max_bigint_length']; ?>");
                    $("#textbox").attr("required", "required");
                    $(this).keyup(function () {
                        var valu = $("#textbox").val();
                        console.log(valu);
                        if (valu <= 255)
                        {
                            $(".submit-btn").removeAttr("disabled");
                            $("#errormsg").html("");
                            document.getElementById("textbox").style.borderColor = "grey";

                        } else {

                            $(".submit-btn").attr("disabled", "disabled");
                            $("#errormsg").html("Value should be less or equal to 255");
                            document.getElementById("textbox").style.borderColor = "red";
                        }
                    })
                    $(this).change(function () {
                        var valu = $("#textbox").val();
                        if (valu <= 255)
                        {
                            $(".submit-btn").removeAttr("disabled");
                            $("#errormsg").html("");
                            document.getElementById("textbox").style.borderColor = "grey";

                        } else {

                            $(".submit-btn").attr("disabled", "disabled");
                            $("#errormsg").html("Value should be less or equal to 255");
                            document.getElementById("textbox").style.borderColor = "red";
                        }
                    })
                }
                if (indexe == 5) {
                    $("#textbox").attr("placeholder", "<?= $lang['Enter_max_float_length']; ?>");
                    $("#textbox").attr("required", "required");
                    $(this).keyup(function () {
                        var valu = $("#textbox").val();
                        console.log(valu);
                        if (valu <= 255)
                        {
                            $(".submit-btn").removeAttr("disabled");
                            $("#errormsg").html("");
                            document.getElementById("textbox").style.borderColor = "grey";

                        } else {

                            $(".submit-btn").attr("disabled", "disabled");
                            $("#errormsg").html("Value should be less or equal to 255");
                            document.getElementById("textbox").style.borderColor = "red";
                        }
                    })
                    $(this).change(function () {
                        var valu = $("#textbox").val();
                        if (valu <= 255)
                        {
                            $(".submit-btn").removeAttr("disabled");
                            $("#errormsg").html("");
                            document.getElementById("textbox").style.borderColor = "grey";

                        } else {

                            $(".submit-btn").attr("disabled", "disabled");
                            $("#errormsg").html("Value should be less or equal to 255");
                            document.getElementById("textbox").style.borderColor = "red";
                        }
                    })
                }

                if (indexe == 6) {
                    $("#textbox").removeAttr('required');
                    $("#textbox").val("");
                }
                if (indexe == 7) {
                    $("#textbox").attr("placeholder", "<?= $lang['Enter_max_characters']; ?>");
                    $("#textbox").attr("required", "required");
                    $(this).keyup(function () {
                        var valu = $("#textbox").val();
                        console.log(valu);
                        if (valu <= 255)
                        {
                            $(".submit-btn").removeAttr("disabled");
                            $("#errormsg").html("");
                            document.getElementById("textbox").style.borderColor = "grey";

                        } else {

                            $(".submit-btn").attr("disabled", "disabled");
                            $("#errormsg").html("Value should be less or equal to 255");
                            document.getElementById("textbox").style.borderColor = "red";
                        }
                    })
                    $(this).change(function () {
                        var valu = $("#textbox").val();
                        if (valu <= 255)
                        {
                            $(".submit-btn").removeAttr("disabled");
                            $("#errormsg").html("");
                            document.getElementById("textbox").style.borderColor = "grey";

                        } else {
                            $(".submit-btn").attr("disabled", "disabled");
                            $("#errormsg").html("Value should be less or equal to 255");
                            document.getElementById("textbox").style.borderColor = "red";
                        }
                    })
                }
            }
            $(document).on('change', '#selection', function () {
                $('#textbox').attr('disabled', $(this).val() == 'datetime' || $(this).val() == 'double');
            });

        </script>
        <script>

            $('.numspecialcharlock').bind("keyup change", function ()
            {
                var GrpNme = $(this).val();
                re = /[`~!@#$%^&*()_|+\-=?;:'",<>\{\}\[\]\\\/]/gi;
                var isSplChar = re.test(GrpNme);
                if (isSplChar)
                {
                    var no_spl_char = GrpNme.replace(/[`~!@#$%^&*()_|+\-=?;:'",<>\{\}\[\]\\\/]/gi, '');
                    $(this).val(no_spl_char);
                }
            });
            $('.numspecialcharlock').bind(function () {
                $(this).val($(this).val().replace(/[<>]/g, ""))
            });
            $(".select33").select2();
        </script>

        <?php
        if (isset($_POST['editMeta'], $_POST['token'])) {
            $metaId = preg_replace("/[^0-9]/", "", $_POST['metaId']);
            $fieldName = trim(strtolower($_POST['fieldName']));
            $fieldName = str_replace(" ", "_", $fieldName);
            $fieldName = preg_replace('/[^\w$\x{0080}-\x{FFFF}]+/u', "", $fieldName); //filter name
            //$fieldName = mysqli_real_escape_string($db_con, $fieldName);
            $fieldName = trim($fieldName);
            $dataType = $_POST['dataTyp'];
            $dataType = preg_replace("/[^a-zA-Z]_/", "", $dataType); //filter name
            $dataType = mysqli_real_escape_string($db_con, $dataType);
            $dataLength = $_POST['dataLength'];
            if (!empty($dataLength) && $dataType != 'range') {
                $dataLength = preg_replace("/[^0-9]/", "", $dataLength); //filter name
                $dataLength = mysqli_real_escape_string($db_con, $dataLength);
            } else if ($dataType == 'range') {
                $dataLength = implode(',', $_POST['dataLengthrange']);
            }
            if ($dataType == 'list' || $dataType == 'checklist') {
                if ($dataType == 'list') {
                    $label = preg_replace('/[^\w$\x{0080}-\x{FFFF} ]+/u', "", $_POST['label']);
                    $label = implode(',', $label);
                    $value = preg_replace('/[^\w$\x{0080}-\x{FFFF} ]+/u', "", $_POST['value']);
                    $value = implode(',', $value);
                } else {
                    $label = preg_replace('/[^\w$\x{0080}-\x{FFFF} ]+/u', "", $_POST['checkboxlabel']);
                    $label = implode(',', $label);
                    $value = preg_replace('/[^\w$\x{0080}-\x{FFFF} ]+/u', "", $_POST['checkboxvalue']);
                    $value = implode(',', $value);
                }
            }
            $mandatory = filter_input(INPUT_POST, "mandatory");
            $mandatory = preg_replace("/[^a-zA-Z]/", "", $mandatory); //filter name
            $mandatory = mysqli_real_escape_string($db_con, $mandatory);
            $flag = 0;
            $metaDataCheck = mysqli_query($db_con, "select * from tbl_metadata_master where field_name='$fieldName' and id<>'$metaId'") or die('error : ' . mysqli_error($db_con));
            if (mysqli_num_rows($metaDataCheck) < 1) {
                $getmetaDataName = mysqli_query($db_con, "select field_name,mandatory,length_data from tbl_metadata_master where id='$metaId'")or die('error gg: ' . mysqli_error($db_con));
                //if (mysqli_num_rows($getmetaDataName) < 1) {
                $rwgetMetaDataName = mysqli_fetch_assoc($getmetaDataName);
                $preMetaName = $rwgetMetaDataName['field_name'];
                $preMandatory = $rwgetMetaDataName['mandatory'];
                $predatalength = $rwgetMetaDataName['length_data'];
                //if ((strcmp("$preMetaName", "$fieldName") == 0) && $preMandatory == $mandatory && $predatalength == $dataLength) { //check same name at same id
                //echo'<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['made_any_change'] . '");</script>';
                //exit();
                // } else {
                if ((($dataType == 'char' ) || ($dataType == 'varchar') AND ( $dataLength <= 255 )) || (( $dataType == 'BigInt' ) AND ( $dataLength <= 20 )) || (( $dataType == 'Int' ) AND ( $dataLength <= 9 )) || (($dataType == 'float') AND ( $dataLength <= 4 )) || ($dataType == 'datetime') || ($dataType == 'double') || ( $dataType == 'range') || ($dataType == 'boolean') || ($dataType == 'list') || ($dataType == 'checklist') || ($dataType == 'date')) {
                    mysqli_set_charset($db_con, "utf8");

                    $updateMeta = mysqli_query($db_con, "update tbl_metadata_master set field_name='$fieldName', length_data='$dataLength', mandatory='$mandatory', label='$label', value='$value' where id = '$metaId'") or die('Error' . mysqli_error($db_con));
                    if ($updateMeta) {
                        $flag = 1;
                        if ($dataType == 'Int' || $dataType == 'datetime' || $dataType == 'BigInt' || $dataType == 'double' || $dataType == 'float' || $dataType == 'range' || $dataType == 'list' || $dataType == 'checklist' || ($dataType == 'date')) {
                            $dataType = 'varchar';
                            $dataLength = 255;
                        }
                        if (!empty($dataLength)) {
                            if ($dataType == 'Int' || $dataType == 'BigInt' || $dataType == 'float' || $dataType == 'double' || $dataType == 'range') {

                                $updateDocColName = mysqli_query($db_con, "ALTER TABLE tbl_document_master CHANGE `$preMetaName` `$fieldName` $dataType($dataLength) DEFAULT 0") or die('Error modify column name' . mysqli_error($db_con));
                            } else {

                                $updateDocColName = mysqli_query($db_con, "ALTER TABLE tbl_document_master CHANGE `$preMetaName` `$fieldName` $dataType($dataLength) DEFAULT NULL") or die('Error modify column name' . mysqli_error($db_con));
                            }
                        } else {

                            if ($dataType == 'Int' || $dataType == 'BigInt' || $dataType == 'float' || $dataType == 'double' || $dataType == 'range') {

                                $updateDocColName = mysqli_query($db_con, "ALTER TABLE tbl_document_master CHANGE `$preMetaName` `$fieldName` $dataType($dataLength) DEFAULT 0"); //or die('Error pg' . mysqli_error($db_con));
                            } else {

                                $updateDocColName = mysqli_query($db_con, "ALTER TABLE tbl_document_master CHANGE `$preMetaName` `$fieldName` $dataType DEFAULT NULL"); //or die('Error pg' . mysqli_error($db_con));
                            }
                        }
                    }
                } else {
                    echo'<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['Fld_Lngth_Excd'] . '");</script>';
                }

                if ($flag) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Keyword Edited','$date','$host','Keyword name $preMetaName to $fieldName Updated')") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . $_SERVER['REQUEST_URI'] . '","' . $lang['Mtadta_Updted_sucsfly'] . '");</script>';
                }
                // }
            } else {
                echo'<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['mtadta_alrady_exst'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>
        <?php
        if (isset($_POST['delete'], $_POST['token'])) {
            mysqli_set_charset($db_con, "utf8");
            $id = preg_replace("/[^0-9]/", "", $_POST['uid']);
            $id = mysqli_real_escape_string($db_con, $id);
            $delName = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$id'") or die('Error:nn' . mysqli_error($db_con));
            $rwdelName = mysqli_fetch_assoc($delName);
            $deletedName = $rwdelName['field_name'];
            $chkMataAsin = mysqli_query($db_con, "select metadata_id from tbl_metadata_to_storagelevel where metadata_id= '$id'") or die('Error:' . mysqli_error($db_con));
            if (mysqli_num_rows($chkMataAsin) == 0) {
                //mysqli_set_charset($db_con,"utf8");	 
                $getMetaDataName = mysqli_query($db_con, "select field_name from tbl_metadata_master where id= '$id'") or die('Error:' . mysqli_error($db_con));
                $rwgetMetaDataName = mysqli_fetch_assoc($getMetaDataName);
                $del = mysqli_query($db_con, "delete from tbl_metadata_master where id='$id'") or die('Error: m' . mysqli_error($db_con));
                $metaCreateDoc = mysqli_query($db_con, "ALTER TABLE tbl_document_master DROP COLUMN $rwgetMetaDataName[field_name]"); // or die('Error:d ' . mysqli_error($db_con));
                if ($del || $metaCreateDoc) {
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Keyword Deleted','$date','$host','Keyword $deletedName Deleted')") or die('error : ' . mysqli_error($db_con));
                    echo'<script>taskSuccess("' . $_SERVER['REQUEST_URI'] . '","' . $lang['Mta_Dta_Dlted_Sucesfuly'] . '!");</script>';
                } else {
                    echo'<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['Mta_Dta_nt_Dltd'] . '");</script>';
                }
            } else {
                echo'<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['Mta_Dta_is_Asgnd_nd_cn_nt_be_Dltd'] . '");</script>';
            }
            mysqli_close($db_con);
        }
        ?>

    </body>
</html>