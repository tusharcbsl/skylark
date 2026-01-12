
<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    // require_once './application/config/database.php';
    require_once './application/pages/function.php';
    require_once './application/pages/head.php';
    
    if ($rwgetRole['save_query'] != '1') {
        header('Location: ./index');
    }
    ?>

    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="https://www.google.com/jsapi" type="text/javascript"></script>  
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

            var control =
                    new google.elements.transliteration.TransliterationControl(options);

// Enable transliteration in the text fields with the given ids.
            var ids = ["querynameId"];
            control.makeTransliteratable(ids);

        }
        google.setOnLoadCallback(onLoad);

    </script> 
    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <!-- ========== Left Sidebar Start ========== -->
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
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    <li>
                                        <a href="metasearch"><?php echo $lang['Search']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['Frequently_Queries']; ?>
                                    </li>
                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="18" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title"> <?php echo $lang['stored_queries_here']; ?></h4>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                    $slperms = array();
                                    while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                        $slperms[] = $rwPerm['sl_id'];
                                    }

                                    $sl_perm = implode(',', $slperms);
                                    $slids = findsubfolder($sl_perm, $db_con);

                                    $slids = implode(',', $slids);
                                    ?>
                                    <?php
                                    if (!empty($slids)) {
                                        $queryUserId = $_SESSION['cdes_user_id'];
                                        if ($queryUserId == '1') {
                                            $where = '';
                                        } else {
                                            $where = "where user_id='$queryUserId' and sl_id in($slids)";
                                        }
                                        $constructs = "SELECT * FROM `query` $where";
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
                                            $limit = $_GET['limit'];
                                            ?>
                                        
                                                <div class="box-body">
                                                    <label><?php echo $lang['show_lst']; ?></label>   
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
                                                    </select> <label> <?php echo $lang['entries_lst']; ?></label>
                                                    <div class="pull-right record">
                                                        <label><?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                            if (($start + $per_page) > $foundnum) {
                                                                echo $foundnum;
                                                            } else {
                                                                echo ($start + $per_page);
                                                            }
                                                            ?> <span><?php echo $lang['Ttal_Rcrds']; ?> : <?php echo $foundnum; ?></span></label>
                                                    </div>
                                                </div>
                                                <table class="table table-striped table-bordered" id="query">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['Sr_No']; ?></th> 
                                                            <th><?php echo $lang['Query']; ?></th>
                                                            <th><?php echo $lang['storage']; ?></th>
                                                            <th><?php echo $lang['save_by']; ?></th>
                                                            <th><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        mysqli_set_charset($db_con, 'utf8');
                                                        $query_ft = mysqli_query($db_con, "SELECT * FROM `query` $where LIMIT $start, $per_page") or die("Error: test" . mysqli_error($db_con));
                                                        $i = 1;
                                                        while ($query_row = mysqli_fetch_assoc($query_ft)) {
                                                            $storagename = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='" . $query_row['sl_id'] . "'");
                                                            $rwstoragename = mysqli_fetch_assoc($storagename);
                                                            $savedby = mysqli_query($db_con, "select first_name,middle_name,last_name from tbl_user_master where user_id='" . $query_row['user_id'] . "'");
                                                            $rwsavedby = mysqli_fetch_assoc($savedby);
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $i . '.'; ?></td>
                                                                <td><a href="<?php echo $query_row['url']; ?>"><?php echo $query_row['query_name']; ?></a></td>
                                                                <td><?php echo $rwstoragename['sl_name']; ?></td>
                                                                <td><?php echo $rwsavedby['first_name'] . ' ' . $rwsavedby['middle_name'] . ' ' . $rwsavedby['last_name']; ?></td>
                                                                <td><a href="#" class="btn btn-primary" id="modiquery" data-toggle="modal" data-target="#modify-query-model" onclick="setModifyQuery('<?php echo $query_row['id']; ?>', '<?php echo $query_row['query_name']; ?>')" data="<?php echo $query_row['id']; ?>"><i class="fa fa-edit"></i> <?php echo $lang['Modify_column']; ?></a>
                                                                    <a class="btn btn-danger" id="delquery" data-toggle="modal" data-target="#del-query-model" data="<?php echo $query_row['id']; ?>"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></a></td>
                                                            </tr>
                                                            <?php
                                                            $i++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <?php
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
                                                            echo " <li><a href='?start=$prev&limit=$per_page&limit=" . $_GET['limit'] . "'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //in middle; hide some front and some back
                                                            elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                echo " <li><a href='?start=0'>1</a></li> ";
                                                                echo "<li><a href='?start=$per_page'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                echo "<li> <a href='?start=0'>1</a> </li>";
                                                                echo "<li><a href='?start=$per_page&limit=" . $_GET['limit'] . "'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&limit=" . $_GET['limit'] . "'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next&limit=$per_page&limit=" . $_GET['limit'] . "''>$lang[Next]</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                            } else {
                                                ?>
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['Sr_No']; ?></th> 
                                                            <th><?php echo $lang['Query']; ?></th>
                                                            <th><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tr>
                                                        <td colspan="3" class="text-center"> <strong class="text-danger"><?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></td>
                                                    </tr>

                                                </table>  
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $lang['Sr_No']; ?></th> 
                                                        <th><?php echo $lang['Query']; ?></th>
                                                        <th><?php echo $lang['Actions']; ?></th>
                                                    </tr>
                                                </thead>
                                                <tr>
                                                    <td colspan="3" class="text-center"> <strong class="text-danger"><?php echo $lang['storage_permission']; ?></strong></td>
                                                </tr>

                                            </table>
                                        <?php }
                                        ?>
                                    </div>
                                    <!-- end: page -->
                                </div>
                            </div> <!-- end Panel -->
                        </div> <!-- container -->

                    </div> <!-- content -->

                    <?php require_once './application/pages/footer.php'; ?>                
                </div>
                <div id="modify-query-model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="panel panel-color panel-primary"> 
                            <div class="panel-heading"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <h2 class="panel-title"><?php echo $lang['r_u_wnt_to_sv_qry']; ?>?</h2> 
                            </div>
                            <form method="post">
                                <div class="panel-body">
                                    <label><?php echo $lang['Query']; ?><span class="text-alert">*</span></label>
                                    <input type="text" id="querynameId" name="modiname" class="form-control specialchaecterlock" placeholder="<?php echo $lang['eyqh']; ?>" required="">
                                    <input type="hidden" id="queryId" name="modifyId">
                                </div> 
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                    <input type="submit" class="btn btn-primary " name="updatequery" value="<?php echo $lang['Submit']; ?>">

                                </div>
                            </form>

                        </div> 
                    </div>
                </div>
                <!--start delete model-->
                <div id="del-query-model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog"> 
                        <div class="panel panel-color panel-danger"> 
                            <div class="panel-heading"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                <h2 class="panel-title"><?php echo $lang['Frequently_Queries']; ?></h2> 
                            </div> 
                            <form method="post">
                                <div class="panel-body">
                                    <p class="text-danger"><?php echo $lang['are_u_sure_delete_query']; ?>?</p>
                                </div>
                                <div class="modal-footer"> 
                                    <input type="hidden" id="querydel" name="deletequery">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                    <button type="submit" name="deleteSaveQuery" class="btn btn-danger"><i class="fa fa-trash-o"></i> <?php echo $lang['Delete']; ?></button>
                                </div>
                            </form>
                        </div> 
                    </div>
                </div><!--ends delete modal -->
                <!-- Right Sidebar -->
                <?php //require_once './application/pages/rightSidebar.php';    ?>
            </div>
            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>
            <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

            <script>
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
                                                                        $("a#delquery").click(function () {
                                                                            var query = $(this).attr('data');

                                                                            $("#querydel").val(query);
                                                                        })
                                                                    });


                                                                    function setModifyQuery(id, name)
                                                                    {
                                                                        $("#queryId").val(id);
                                                                        $("#querynameId").val(name);
                                                                    }
            </script>
    </body>
</html>
<?php
if (isset($_POST['updatequery'], $_POST['token'])) {
    $QueryId = $_POST['modifyId'];
    $QueryName = $_POST['modiname'];
    $savequery = mysqli_query($db_con, "SELECT `query_name` FROM `query` WHERE id='$QueryId'");
    $rwsavequery = mysqli_fetch_assoc($savequery);
    $checkDulQ = mysqli_query($db_con, "select * from query where query_name='$QueryName' and id!='$QueryId'");
    if (mysqli_num_rows($checkDulQ) < 1) {
        $updatequery = mysqli_query($db_con, "update query set query_name='$QueryName' WHERE id='$QueryId'") or die("Error : " . mysqli_error($db_con));
        if ($updatequery) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Saved query edited','$date','$host','Search metadata saved query changed from $rwsavequery[query_name] to $QueryName')") or die('error on failed: ' . $sl_id . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Query_Successfully_Saved'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['Query_Saved_failed'] . '");</script>';
        }
    } else {
        echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['query_exist'] . '");</script>';
    }
}
?>
<?php
if (isset($_POST['deleteSaveQuery'], $_POST['token'])) {
    $delId = $_POST['deletequery'];
    $savequeryd = mysqli_query($db_con, "SELECT `query_name` FROM `query` WHERE id='$delId'");
    $rwsavequeryd = mysqli_fetch_assoc($savequeryd);
    $delquery = mysqli_query($db_con, "DELETE FROM query WHERE id='$delId'") or die("Error : " . mysqli_error($db_con));
    if ($delquery) {
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Saved query deleted','$date','$host','Search metadata saved query $rwsavequeryd[query_name] deleted.')") or die('error on failed: ' . $sl_id . mysqli_error($db_con));
        echo'<script>taskSuccess("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['query_deleted'] . '");</script>';
    } else {
        echo'<script>taskFailed("' . basename($_SERVER[REQUEST_URI]) . '","' . $lang['failed_deleted'] . '");</script>';
    }
}
?>