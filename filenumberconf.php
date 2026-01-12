<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['group_id'];
    }
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);



    // echo $rwgetRole['dashboard_mydms']; die;
    if ($rwgetRole['view_group_list'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />

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
                                        <a href="groupList"><?php echo $lang['Group_Manager']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['Grp_Lst']; ?>
                                    </li>
                                </ol>
                            </div>
                        </div>

                        <div class="panel">

                            <div class="panel-body">

                                <?php if ($rwgetRole['add_group'] == '1') { ?>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="m-b-30">
                                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-left" data-toggle="modal" data-target="#generate-add">Generate File Number<i class="fa fa-plus"></i></a>
                                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-alt-left"></i> <?php echo $lang['go_back']; ?> </a>
                                            </div>
                                        </div>
                                    </div>
                                
                                <?php } ?>
                                <div class="">
                                    <?php
                                    $sql = "SELECT * FROM  tbl_group_master where group_id in($sameGroupIDs)";
                                    $retval = mysqli_query($db_con, $sql); // or die('Could not get data: ' . mysqli_error($db_con));
                                    $foundnum = mysqli_num_rows($retval);
                                    if ($foundnum > 0) {
                                        $StartPoint= preg_replace("/[^0-9]/","",$_GET['limit']);//filter limit from all special chars
                                        if (is_numeric($StartPoint)) {
                                            $per_page = $StartPoint;
                                        } else {
                                            $per_page = 10;
                                        }
                                        $start= preg_replace("/[^0-9]/", "",$_GET['start']);//filter start variable
                                        $start = isset($start) ? $start : '';
                                        $max_pages = ceil($foundnum / $per_page);
                                        if (!$start) {
                                            $start = 0;
                                        }
                                        ?>
                                        <div class="container">
                                               <div class="pull-right record m-t-10">
                                                    <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                    if ($start + $per_page > $foundnum) {
                                                        echo $foundnum;
                                                    } else {
                                                        echo ($start + $per_page);
                                                    };
                                                    ?> <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span>
                                                </div>
                                            <div class="box-body">
                                                <?php
                                                $limit= preg_replace("/[^0-9]/", "",$_GET['limit']);//filter limit variable
                                                $limit = trim($limit);

                                                if (isset($limit) and ! empty($limit) and $limit == '') {

                                                    $rec_limit = $limit;
                                                } else {

                                                    $rec_limit = 10;
                                                }
                                                $sql = "SELECT count(group_id) FROM  tbl_group_master where group_id in($sameGroupIDs)";

                                                $retval = mysqli_query($db_con, $sql) or die('Could not get data: ' . mysqli_error($db_con));
                                                $row = mysqli_fetch_array($retval, MYSQLI_NUM);
                                                $rec_count = $row[0];
                                                $maxpage = $rec_count / $rec_limit;
                                                $maxpage = ceil($maxpage);
                                                $page= preg_replace("/[^0-9]/", "", $_GET['page']);//filter page
                                                if (isset($page)) {
                                                    $page = $page + 1;
                                                    $offset = $rec_limit * $page;
                                                    $i = $_GET['index'];
                                                } else {
                                                    $page = 0;
                                                    $offset = 0;
                                                }
                                                $left_rec = $rec_count - ($page * $rec_limit);
                                                $bg = '#E3EDF0'; //variable used to store alternate row colors
                                                
                                                ?>
                                                 <?php echo $lang['show_lst']; ?>  
                                                <select id="limit">
                                                    <option value="10" <?php
                                                    if ($limit== 10) {
                                                        echo 'selected';
                                                    }
                                                    ?>>10</option>
                                                    <option value="25" <?php
                                                    if ($limit== 25) {
                                                        echo 'selected';
                                                    }
                                                    ?>>25</option>
                                                    <option value="50" <?php
                                                    if ($limit== 50) {
                                                        echo 'selected';
                                                    }
                                                    ?>>50</option>
                                                    <option value="250" <?php
                                                    if ($limit == 250) {
                                                        echo 'selected';
                                                    }
                                                    ?>>250</option>
                                                    <option value="500" <?php
                                                    if ($limit == 500) {
                                                        echo 'selected';
                                                    }
                                                    ?>>500</option>
                                                </select>   
                                                <?php echo $lang['entries_lst']; ?>
                                                <?php
                                                $where = '';
                                                $grpname= preg_replace("/[^A-Za-z0-9 ]/", "", $_GET['grpName']);
                        
                                                if (isset($grpname) && !empty($grpname)) {

                                                    $where = "where group_name LIKE '%$grpname%'";
                                                }
                                                $order = order_type();
                                                $users = mysqli_query($db_con, "select * from tbl_group_master where group_id in($sameGroupIDs) order by $order asc LIMIT $start, $per_page"); // or die('Error:' . mysqli_error($db_con));
//                                             
                                                showData($users, $rwgetRole, $db_con, $privilegeSession);
                                                ?>
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
                                                            echo " <li><a href='?start=$prev'>Prev</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>Prev</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i'>$counter</a> </li>";
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
                                                                        echo " <li class='active'><a href='?start=$i'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                echo "<li> <a href='?start=0'>1</a> </li>";
                                                                echo "<li><a href='?start=$per_page'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next'>Next</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>Next</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                            }else {
                                                ?>
                                                <div class="form-group no-records-found"><label><i>Users Groups Appears Here !!</i></label></div>
                                            <?php }
                                            ?>	
                                        </div>
                                    </div>
                                    <!-- end: page -->
                                </div> <!-- end Panel -->
                            </div> <!-- container -->

                        </div> <!-- content -->


                        <!-- /Right-bar -->
                        <!-- MODAL -->
                        <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="modal-content"> 
                                    <div class="modal-header"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <label><h2 class="panel-title"><?php echo $lang['Are_u_confirm']; ?></h2></label> 
                                    </div> 
                                    <form method="post">
                                        <div class="modal-body">
                                            <p style="color: red;"><?php echo $lang['Are_u_sure_dlt_this_grp']; ?></p>
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

                        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="modal-content"> 
                                    <form method="post" >
                                        <div class="modal-header"> 
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                            <h4 class="modal-title"><?php echo $lang['Update_Group_Name']; ?></h4> 
                                        </div>

                                        <div class="modal-body" id="modalModify">

                                            <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />
                                        </div> 
                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                            <button type="submit" name="editGroup" class="btn btn-primary"><?php echo $lang['Save']; ?></button> 
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div><!-- /.modal -->
                        <div id="generate-add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog "> 
                                <div class="modal-content"> 
                                    <form method="post" >
                                        <div class="modal-header"> 
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                            <h4 class="modal-title"><?php echo $lang['Add_New_Group']; ?></h4> 
                                        </div>

                                        <div class="modal-body" >

                                            <div class="row">
                                                <div class="form-group">
                                                    <label>Enter Total Number Of Character <span style="color:red">*</span></label>
                                                    <input type="text" name="groupName" required class="form-control" id="groupName" placeholder="Enter Total Number Of Character">
                                                </div>
                                            </div>
                                            <div class="row"> 

                                                <div class="form-group">
                                                    <label>Enter Separator<span style="color:red">*</span></label>
                                                    <input type="text" name="groupName" required class="form-control" id="groupName" placeholder="Enter Separator">
                                                </div>


                                            </div>
                                             <div class="row"> 

                                                <div class="form-group">
                                                    <label>Enter Number Of Character Between Separator<span style="color:red">*</span></label>
                                                    <input type="text" name="groupName" required class="form-control" id="groupName" placeholder="Enter Separator">
                                                </div>


                                            </div>
                                        </div> 
                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                            <button type="submit" name="addGroup" class="btn btn-primary"><?php echo $lang['Add']; ?></button> 
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div><!-- /.modal -->
                    </div>
                    <!-- END wrapper -->
                    <?php require_once './application/pages/footer.php'; ?>

                </div>
                <!-- ============================================================== -->
                <!-- End Right content here -->
                <!-- ============================================================== -->
                <!-- Right Sidebar -->
                <?php require_once './application/pages/rightSidebar.php'; ?>
                <?php require_once './application/pages/footerForjs.php'; ?>

                <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

                <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('form').parsley();

                    });
                    $(".select2").select2();
                    //firstname last name 
                    $("input#groupName").keypress(function (e) {
                        //if the letter is not digit then display error and don't type anything
                        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                            //display error message
                            return true;
                        } else {
                            return false;
                        }
                        str = $(this).val();
                        str = str.split(".").length - 1;
                        if (str > 0 && e.which == 46) {
                            return false;
                        }
                    });

                </script>
                <script>

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

                    $("a#editRow").click(function () {
                        var $id = $(this).attr('data');
                        var $row = $(this).closest('tr');
                        var name = '';
                        var values = [];
                        values = $row.find('td:nth-child(2)').map(function () {
                            var $this = $(this);
                            if ($this.hasClass('actions')) {

                            } else {
                                name = $.trim($this.text());
                                //$.trim( $this.text());
                            }

                            $("#con-close-modal .modal-title").text("" + name + " <?php echo $lang['Updt_Grp'] ?>");
                            $.post("application/ajax/updateGroup.php", {ID: $id}, function (result, status) {
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
                <script type="text/javascript">

                    $(document).ready(function () {
                        $('form').parsley();
                    });
                </script>
                <?php
                if (isset($_POST['addGroup'])) {
                    $userid= $_POST['userid'];
                    $userid[] = '1';
                    $userid[] = $_SESSION['cdes_user_id'];
                    $userid = array_unique($userid);
                    $userid = implode(",", $userid);
                    $groupName = filter_input(INPUT_POST, "groupName");
                    $groupName = mysqli_real_escape_string($db_con, $groupName);
                    $userid= mysqli_escape_string($db_con, $userid);
                    $groupName= preg_replace("/[^a-zA-Z0-9 ]/", "", $groupName);//filter groupname
                    $userid=preg_replace("/[^0-9, ]/", "", $userid);//filter userids
                    $check = mysqli_query($db_con, "select group_id from tbl_group_master where group_name='$groupName'");
                    if (mysqli_num_rows($check) <= 0) {
                        $insert = mysqli_query($db_con, "insert into tbl_group_master(group_id,group_name) values(null,'$groupName')") or die('Error : ' . mysqli_error($db_con));
                        $gid = mysqli_insert_id($db_con);
                        if ($insert) {
                            if (!empty($userid)) {

                                $grp = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where group_id='$gid'");
                                if (mysqli_num_rows($grp) > 0) {
                                    $grpToUm = mysqli_query($db_con, "update tbl_bridge_grp_to_um set user_ids='$userid' where group_id='$gid'");
                                } else {
                                    $grpToUm = mysqli_query($db_con, "insert into tbl_bridge_grp_to_um(id,group_id, user_ids,roleids) values(null,'$gid','$userid','')");
                                }
                            }
                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Group $groupName Added','$date', null,'$host','')") or die('error :' . mysqli_error($db_con));
                            echo'<script>taskSuccess("groupList","'.$lang['Group_Added_Successfully'].' !");</script>';
                        }
                    } else {
                        echo'<script>taskFailed("groupList","'.$lang['Group_Already_Exist'].'!");</script>';
                    }
                    mysqli_close($db_con);
                }

                if (isset($_POST['editGroup'])) {
                    $gid = filter_input(INPUT_POST, "gid");
                    $groupName = filter_input(INPUT_POST, "groupName");
                    $userIds = $_POST['users'];
                    $userIds[] = '1';
                    $userIds[] = $_SESSION['cdes_user_id'];
                    $userIds = array_unique($userIds);
                    $userIds = implode(",", $userIds);
                    $groupName = mysqli_real_escape_string($db_con, $groupName);
                    $groupName= preg_replace("/[^a-zA-Z0-9 ]/", "", $groupName);//filter groupname
                    $userid=preg_replace("/[^0-9, ]/", "", $userid);//filter userids
                    $fetchValidate= mysqli_query($db_con, "select * from tbl_group_master where `group_name`='$groupName' and group_id!='$gid'");
                    if(mysqli_num_rows($fetchValidate)>0){
                      echo'<script>taskFailed("groupList","Group Already Exist !");</script>';  
                    }else{
                    $edit = mysqli_query($db_con, "update tbl_group_master set `group_name`='$groupName' where group_id='$gid'") or die('Error : ' . mysqli_error($db_con));
                    if ($edit) {
//                $GroupName = mysqli_query($db_con, "select group_name from tbl_group_master where group_id='$gid'");
//                $rwGrpName = mysqli_fetch_assoc($GroupName);
//                $OldgrpName = $rwupdateName['group_name'];
                        if (!empty($userIds)) {

                            $grp = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where group_id='$gid'");
                            if (mysqli_num_rows($grp) > 0) {
                                $grpToUm = mysqli_query($db_con, "update tbl_bridge_grp_to_um set user_ids='$userIds' where group_id='$gid'");
                            } else {
                                $grpToUm = mysqli_query($db_con, "insert into tbl_bridge_grp_to_um(id,group_id, user_ids) values(null,'$gid','$userIds')");
                            }
                        }

                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Group Name $groupName Updated','$date', null,'$host',null)") or die('error :' . mysqli_error($db_con));
                        echo'<script>taskSuccess("groupList?start='.$_GET[start].'","'.$lang['Group_Updated_Successfully'].' !");</script>';
                    }
             
                    mysqli_close($db_con);
                }
                }
                if (isset($_POST['delete'])) {
                    $id = mysqli_real_escape_string($db_con, $_POST['uid']);
                    $delNme = mysqli_query($db_con, "select group_name from tbl_group_master where group_id='$id'");
                    $rwdel = mysqli_fetch_assoc($delNme);
                    $delName = $rwdel['group_name'];
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Group $delName Deleted','$date', null,'$host','')") or die('error :' . mysqli_error($db_con));
                    $del = mysqli_query($db_con, "delete from tbl_group_master where group_id='$id'");
                    if ($del) {
                        $delbridge = mysqli_query($db_con, "delete from tbl_bridge_grp_to_um where group_id='$id'");

                        echo'<script>taskSuccess("groupList?start='.$_GET[start].'","'.$lang['Group_Deleted_Successfully'].' !");</script>';
                    }
                    mysqli_close($db_con);
                }
                ?>

                <?php

                function showData($user, $rwgetRole, $privilegeSession, $db_con) {
                    if (isset($_SESSION['lang'])) {
                        $file = $_SESSION['lang'] . ".json";
                    } else {
                        $file = "English.json";
                    }
                    $data = file_get_contents($file);
                    $lang = json_decode($data, true);
                    ?>

                    <table class="table table-striped table-bordered dataTable no-footer">
                        <thead>
                            <tr>
                                <th><?php echo $lang['Sr_No']; ?></th>
                                <th><?php echo $lang['group_Name']; ?></th>
                                <th><?php echo $lang['Actions']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $i += $_GET['start'];
                            while ($rwUser = mysqli_fetch_assoc($user)) {
                                ?>
                                <tr class="gradeX">
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $rwUser['group_name']; ?></td>
                                    <td class="actions">
                                        <?php if ($rwgetRole['modify_group'] == '1') { ?>
                                            <a href="#" class="on-default edit-row btn btn-primary" data-toggle="modal" data-target="#con-close-modal" id="editRow" data="<?php echo $rwUser['group_id']; ?>" style="<?php
                                            if ($rwUser['group_name'] == 'Administrator') {
                                                echo 'display:none';
                                            }
                                            ?>" title="<?php echo $lang['Modify_column']; ?>"><i class="fa fa-edit"></i> </a>

                                        <?php } ?>

                                        <?php if ($rwgetRole['delete_group'] == '1') { ?>
                                            <a href="#" class="on-default remove-row btn btn-danger" data-toggle="modal" data-target="#dialog" id="removeRow" data="<?php echo $rwUser['group_id']; ?>" style="<?php
                                            if ($rwUser['group_name'] == 'Administrator') {
                                                echo 'display:none';
                                            }
                                            ?>" title="<?php echo $lang['Delete']; ?>" ><i class="fa fa-trash-o"></i></a>
                                            <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                }

                function order_type() {
                    //selecting the order for displaying the search result data
                    if (!isset($_GET['order']))
                        $_GET['order'] = 'none';
                    if (!isset($_GET['type'])) {
                        $_GET['type'] = 'asc';
                        $_GET['tchange'] = 'desc';
                    } else if ($_GET['type'] == 'asc') {
                        $_GET['tchange'] = 'desc';
                    } else if ($_GET['type'] == 'desc') {
                        $_GET['tchange'] = 'asc';
                    }
                    switch ($_GET['order']) {
                        case 'groupname': {
                                $order = 'group_name';
                                break;
                            }


                        default: {
                                $order = 'group_id';
                                break;
                            }
                    }
                    return ($order);
                }
                ?>
                </body>
                </html>