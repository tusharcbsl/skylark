<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    if ($rwgetRole['mail_lists'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/custombox/css/custombox.css" rel="stylesheet">
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
                                        <a href="#"><?php echo $lang['E_mls_Mgmt'] ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['E_mails_List'] ?>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>

                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <form method="get">
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="subject" id="submail" value="<?php echo xss_clean($_GET['subject']); ?>" parsley-trigger="change"  placeholder="<?php echo $lang['Enter_mail_subject']; ?>" required />
                                                </div>
                                                <div class="col-sm-5">
                                                    <button type="submit" class="btn btn-primary"><?php echo $lang['Search']; ?> <i class="fa fa-search"></i> </button>
                                                    <a href="mail-lists" class="btn btn-warning"> <?php echo $lang['Reset']; ?> </a>
                                                </div>
                                            </form>
                                            <div class="col-sm-3">
                                                <div class="pull-right"><button class="btn btn-primary" id="syncMails"><i class="fa fa-refresh"></i> <?php echo $lang['sync_email']; ?></button></div>
                                            </div>


                                        </div>
                                    </div>
                                    <?php
                                    $where = "WHERE user_id='$_SESSION[cdes_user_id]'";
                                    if (isset($_GET['subject']) && !empty($_GET['subject'])) {
                                        $subject = xss_clean($_GET['subject']);
                                        $where .= "and subject like '%$subject%'";
                                    }

                                    $mailList = mysqli_query($db_con, "SELECT id FROM `tbl_my_mails` $where"); //or die("Error : in mails" . mysqli_error($db_conn));
                                    $foundnum = mysqli_num_rows($mailList);
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
                                        ?>
                                        <div class="box-body">
                                            <div class="m-b-10">
                                                <label><?php echo $lang['Show']; ?></label>
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
                                                </select> <label> <?php echo $lang['your_email']; ?></label>
                                                <div class="pull-right record">
                                                    <label> <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                        if (($start + $per_page) > $foundnum) {
                                                            echo $foundnum;
                                                        } else {
                                                            echo ($start + $per_page);
                                                        };
                                                        ?> <span><?php echo $lang['ttl_recrds']; ?> : <?php echo $foundnum; ?></span></label>
                                                </div>
                                            </div>
                                            <div style="overflow-x: auto;">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $lang['Sr_No']; ?></th>
                                                        <th style="width: 250px;"><?php echo $lang['Subject'] ?></th>
                                                        <th><?php echo $lang['Details'] ?></th>
                                                        <th><?php echo $lang['Email_Body'] ?></th>
                                                        <th><?php echo $lang['Attachments'] ?></th>
                                                        <th><?php echo $lang['Email_Date'] ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $j = 1;
                                                    $j += $start;
                                                    $start = xss_clean(trim($start));
                                                    $per_page = xss_clean(trim($per_page));
                                                    mysqli_set_charset($db_con, "utf8");
                                                    $mailList = mysqli_query($db_con, "SELECT * FROM `tbl_my_mails` $where order by email_date Desc LIMIT $start, $per_page"); //or die("Error : in mails" . mysqli_error($db_conn));
                                                   
                                                    //print_r("SELECT * FROM `tbl_my_mails` $where order by email_date Desc LIMIT $start, $per_page");die;
                                                    while ($rwMailList = mysqli_fetch_assoc($mailList)) {
                                                        ?>
                                                        <tr class="gradeX"> 
                                                            <td><?php echo $j; ?></td>
                                                            <td><?php echo $rwMailList['subject']; ?></td>
                                                            <td><label>From : </label> <?php
                                                                $from = str_replace("<", '"', $rwMailList['from']);
                                                                $from = str_replace(">", '"', $from);
                                                                echo $from;
                                                                ?> <br>
                                                                <label>To : </label> <?php
                                                                $to = str_replace("<", '"', $rwMailList['to']);
                                                                $to = str_replace(">", '"', $to);
                                                                echo $to;
                                                                ?><br>
                                                                <label>CC : </label> 
                                                                <?php
                                                                $cc = str_replace("<", '"', $rwMailList['cc']);
                                                                $cc = str_replace(">", '"', $cc);
                                                                echo $cc;
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#mailLists" id="ViewMsg" data="<?php echo $rwMailList['id']; ?>" title="<?= $lang['Email_Body']; ?>"><i class="fa fa-eye"></i></a> 
                                                            </td>
                                                            <td>
                                                                <a href="#custom-modal" class="btn btn-primary btn-sm waves-effect waves-light" data-animation="fadein" data-plugin="custommodal" 
                                                                   id="viewattchmnt" data-overlaySpeed="200" data-overlayColor="#36404a" data="<?php echo $rwMailList['user_id']; ?>,<?php echo $rwMailList['message_id']; ?>,<?php echo $rwMailList['attachment']; ?>" title="<?php echo $lang['all_email_attachment']; ?>"><i class="fa fa-paperclip"></i></a>

                                                            </td>
                                                            <td><span class="label label-primary"><?php echo date('d-m-Y g:i A', strtotime($rwMailList['email_date'])); ?></span></td>
                                                        </tr>
                                                        <?php
                                                        $j++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                            </div>
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
                                                        echo " <li><a href='?start=$prev&limit=$per_page&subject=$_GET[subject]'>$lang[Prev]</a> </li>";
                                                    else
                                                        echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                    //pages 
                                                    if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                        $i = 0;
                                                        for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                            if ($i == $start) {
                                                                echo " <li class='active'><a href='?start=$i&limit=$per_page&subject=$_GET[subject]'><b>$counter</b></a> </li>";
                                                            } else {
                                                                echo "<li><a href='?start=$i&limit=$per_page&subject=$_GET[subject]'>$counter</a></li> ";
                                                            }
                                                            $i = $i + $per_page;
                                                        }
                                                    } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                        //close to beginning; only hide later pages
                                                        if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                            $i = 0;
                                                            for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=$i&limit=$per_page&subject=$_GET[subject]'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo "<li> <a href='?start=$i&limit=$per_page&subject=$_GET[subject]'>$counter</a> </li>";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        }
                                                        //in middle; hide some front and some back
                                                        elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                            echo " <li><a href='?start=0&limit=$per_page&subject=$_GET[subject]'>1</a></li> ";
                                                            echo "<li><a href='?start=$per_page&limit=$per_page&subject=$_GET[subject]'>2</a></li>";
                                                            echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                            $i = $start;
                                                            for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=$i&limit=$per_page&subject=$_GET[subject]'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo " <li><a href='?start=$i&limit=$per_page&subject=$_GET[subject]'>$counter</a> </li>";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        }
                                                        //close to end; only hide early pages
                                                        else {
                                                            echo "<li> <a href='?start=0&limit=$per_page&subject=$_GET[subject]'>1</a> </li>";
                                                            echo "<li><a href='?start=$per_page&limit=$per_page&subject=$_GET[subject]'>2</a></li>";
                                                            echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                            $i = $start;
                                                            for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=$i&limit=$per_page&subject=$_GET[subject]'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo "<li> <a href='?start=$i&limit=$per_page&subject=$_GET[subject]'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        }
                                                    }
                                                    //next button
                                                    if (!($start >= $foundnum - $per_page))
                                                        echo "<li><a href='?start=$next&limit=$per_page&subject=$_GET[subject]'>$lang[Next]</a></li>";
                                                    else
                                                        echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                    ?>
                                                </ul>
                                                <?php
                                            }
                                            echo "</center>";
                                        } else {
                                            ?>
                                            <div class="row m-t-40">
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['Sr_No']; ?></th>
                                                            <th><?php echo $lang['Subject'] ?></th>
                                                            <th><?php echo $lang['Details'] ?></th>
                                                            <th><?php echo $lang['Email_Body'] ?></th>
                                                            <th><?php echo $lang['Attachments'] ?></th>
                                                            <th><?php echo $lang['Email_Date'] ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="6" class="text-center "><label class="text-alert"><?php echo $lang['Who0ps!_No_Records_Found']; ?></label></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php }
                                        ?>
                                    </div>
                                </div>
                                <!-- end: page -->

                            </div> <!-- end Panel -->
                        </div>
                    </div> <!-- container -->
                </div> <!-- content -->
                <div id="full-width-modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" id="myLargeModalLabel"><?php echo $lang['Email_Body']; ?></h4>
                            </div>
                            <div class="modal-body">
                                <div id="Display"></div>
                            </div>
                            <div class="modal-footer">

                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="mailLists" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-full"> 
                        <div class="modal-content"> 
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 

                                <h4 class="modal-title"><?php echo $lang['Email_Body']; ?></h4> 
                            </div>
                            <div class="modal-body" id="emailBody">
                                <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />

                            </div> 
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                            </div>
                        </div> 
                    </div>
                </div><!-- /.modal -->
                <div id="custom-modal" class="modal-demo">
                    <button type="button" class="close" onclick="Custombox.close();">
                        <span>&times;</span><span class="sr-only">Close</span>
                    </button>
                    <h4 class="custom-modal-title"><?php echo $lang['all_email_attachment']; ?></h4>
                    <div class="custom-modal-text text-left">
                        <div class="form-group" id="viewattachment"></div>
                    </div>

                </div>
                <div id="attachment" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-lg"> 
                        <div class="modal-content"> 
                            <div class="modal-header"> 
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 

                                <h4 class="modal-title"><?php echo $lang['Email_Attachments'] ?></h4> 
                            </div>
                            <div class="modal-body" id="attached">
                                <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />

                            </div> 
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
                            </div>
                        </div> 
                    </div>
                </div><!-- /.modal -->
                <script src="assets/plugins/custombox/js/custombox.min.js"></script>
                <?php require_once './application/pages/footer.php'; ?>
            </div>

            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>
            <div  style="display:none; text-align: center; color: #fff;  background: rgba(0,0,0,0.5); width: 100%; height: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
                <img src="assets/images/proceed.gif" alt="load"  style="margin-top: 250px;height:100px; position: fixed; "/>
            </div>
        </div>
        
    </body>
     <div style=" display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
            <img src="assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed;" />
        </div>
    <script type="text/javascript">

                        //for message body
                        $("a#ViewMsg").click(function () {
                            var id = $(this).attr('data');

                            $.post("application/ajax/emailbody.php", {ID: id}, function (result, status) {
                                if (status == 'success') {
                                    $("#emailBody").html(result);
                                    //alert(result);

                                }
                            });
                        });

                        $("a#attach").click(function () {
                            var id = $(this).attr('data');
                            var result = $("#" + id).html();
                            $("#attached").html(result);

                        });
                        $("#syncMails").click(function () {
                            var id = "<?php echo $_SESSION['cdes_user_id'] ?>";
                            $("#wait").show();
                            //alert(id);
                            var token = $("input[name='token']").val();
                            $.post("insertMailDMS.php", {UID: id, token:token}, function (result, status) {
                                if (status == 'success') {
                                   
                                    $("#wait").hide();
                                    alert('<?php echo $lang['email_sync_msg']; ?>');
                                    //location.href = "mail-lists";
                                }
                            });
                            $("#syncMails").attr("disabled", true);
                            return true;
                        });
                        

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

                        $("a#viewattchmnt").click(function () {
                            var usermailIds = $(this).attr('data');
                            var usermailId = usermailIds.split(',');
                            var userId = usermailId[0];
                            var mailId = usermailId[1];
                            var mailatt = usermailId[2];
                            $.post("application/ajax/view-email-attachment.php", {UID: userId, MID: mailId, MAID: mailatt}, function (result, status) {
                                if (status == 'success') {
                                    $("#viewattachment").html(result);
                                }
                            });
                        });
    </script>
</body>
</html>
