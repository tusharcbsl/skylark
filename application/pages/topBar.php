<?php
//@sk@29918
require_once './tdn-appoint.php';
$todo = new toDo();
$appoint = new appointment();

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once './application/config/database.php';
$user = mysqli_query($db_con, "select * from tbl_user_master where user_id='".$_SESSION['cdes_user_id']."'") or die('Error : ' . mysqli_error($db_con));
$UserRW = mysqli_fetch_assoc($user);


//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='".$_SESSION['lang']."'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);


?>
<?php
$where = "";
//require_once 'application/pages/where.php';
if (in_array("1", $privileges)) {
    $constructs = "SELECT count(id) as count FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id";
} else {
    $constructs = "SELECT count(id) as count FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id $where";
}
//echo $constructs;
$run = mysqli_query($db_con, $constructs) or die('Error' . mysqli_error($db_con));
$rwRun = mysqli_fetch_assoc($run);
$foundnum = $rwRun['count'];

$getInfopass = mysqli_query($db_con, "SELECT * FROM `tbl_pass_policy`");
$rwInfoPolicy = mysqli_fetch_assoc($getInfopass);

// Check user active or not
$isActive = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` where user_id='".$_SESSION['cdes_user_id']."' and active_inactive_users=0");

if(mysqli_num_rows($isActive)>0){
    
    header("location:logout");
    exit();
}

?>
<div class="topbar">
    <!-- LOGO -->
    <div class="topbar-left">
        <div class="text-center">
           <!-- <a href="index.html" class="logo"><i class="icon-c-logo">BB</i><span>Broker Bazaar</span></a>-->
            <!-- Image Logo here -->
            <a href="index" class="logo">
                <i class="icon-c-logo"> <img src="assets/images/icon.jpg" height="42"/> </i>
                <span><img src="assets/images/<?php echo $projectLogo; ?>" height="57"/></span>
            </a>
        </div>
    </div>

    <!-- Button mobile view to collapse sidebar menu -->
    <div class="navbar navbar-default" role="navigation">
        <div class="container">
            <div class="">
                <div class="pull-left">
                    <button class="button-menu-mobile open-left waves-effect waves-light">
                        <i class="md md-menu" title="<?php echo $lang['exp_colpse']; ?>"></i>
                    </button>
                    <span class="clearfix"></span>

                </div>

                <form role="search" action="search" class="navbar-left app-search hidden-xs" method="get">
					<input type="hidden" name="searchby" value="files">
                    <input type="text" name="searchText" id="searchText" placeholder="<?php echo $lang['Search']; ?>...." class="form-control translatetext">
                    <a href="#" onclick="$(this).closest('form').submit()"><i class="fa fa-search" title="<?php echo $lang['Search']; ?>"></i></a>
                </form>

                <ul class="nav navbar-nav navbar-right pull-right">

                    <li class="dropdown top-menu-item-xs">
                        <?php
                        //for total online user count
                        $onlineUserCount = "SELECT * FROM tbl_user_master where current_login_status ='1' and user_id!='1' and user_id!='$_SESSION[cdes_user_id]'";

                        $rwonlineUserCount = mysqli_query($db_con, $onlineUserCount) or die('Error' . mysqli_error($con));
                        ?>

                         <?php
                        //for total online user count
                        if ($rwgetRole['online_user'] == '1') {
                            ?>
                            <a><i class="glyphicon glyphicon-user" title="<?php echo $lang['Online_Users']; ?>"></i> <span class="badge badge-xs badge-success"><?php
                                    if (mysqli_num_rows($rwonlineUserCount) > 0) {

                                        echo '<span style="class">' . (mysqli_num_rows($rwonlineUserCount)) . '</span>';
                                    }
                                    ?></span></a>
                        <?php } ?>

                    </li>
                    <li>
                        <ul class="nav navbar-nav navbar-right pull-right">
                            <?php
                            $nno = 0;
                            $noty = new notification();
                            if ($rwgetRole['todo_view'] == '1') {
                                $todo_notification = $noty->getNotify($db_con, $_SESSION['cdes_user_id']);
                                $nno = count($todo_notification);
                            }
                            //echo $nno;
                            if ($rwgetRole['appoint_view'] == '1') {
                                //appointment Notification
                                $appoint_notification = $noty->getAppointNotify($db_con, $_SESSION['cdes_user_id']);
                                $nno += count($appoint_notification);
                            }
                            ?>
                            <li class="dropdown top-menu-item-xs">
                                <a href="#" data-target="#" class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-bell" title="<?= $lang['Notification']; ?>"></i><?php if ($nno > 0) { ?><span class="badge badge-xs badge-danger"><?= $nno ?></span><?php } ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-lg">
                                    <li class="notifi-title"><?= $lang['Notification']; ?></li>
                                    <li class="list-group slimscroll-noti notification-list">
                                        <!-- list item-->
                                        <?php
                                        if ($rwgetRole['todo_view'] == '1') {
                                            foreach ($todo_notification as $todo_nt) {
                                                ?>
                                                <a href="manage-todo?tdid=<?= urlencode(base64_encode($todo_nt['id'])) ?>" class="list-group-item">
                                                    <div class="media">
                                                        <div class="pull-left p-r-10">
                                                            <em class="glyphicon glyphicon-th-list noti-primary"></em>
                                                        </div>
                                                        <div class="media-body">
                                                            <h5 class="media-heading"><?= $todo_nt['task_name'] ?></h5>
                                                            <p class="m-0">
                                                                <small><?= $todo_nt['task_description'] ?></small>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </a>
                                                <?php
                                            }
                                        }
                                        ?>

                                        <?php
                                        if ($rwgetRole['appoint_view'] == '1') {
                                            foreach ($appoint_notification as $appoint_nt) {
                                                ?>
                                                <a href="manage-appointment?aid=<?= urlencode(base64_encode($appoint_nt['id'])) ?>" class="list-group-item">
                                                    <div class="media">
                                                        <div class="pull-left p-r-10">
                                                            <em class="fa fa-calendar noti-primary"></em>
                                                        </div>
                                                        <div class="media-body">
                                                            <h5 class="media-heading"><?= $appoint_nt['title'] ?></h5>
                                                            <p class="m-0">
                                                                <small><?= $appoint_nt['agenda'] ?></small>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </a>
                                                <?php
                                            }
                                        }
                                        ?>

                                    </li>
                                    <li>

                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <!--li class="hidden-xs">
                        <a href="#" id="btn-fullscreen" class="waves-effect waves-light"><i class="fa fa-arrows-alt" title="<?= $lang['btn-fullscreen']; ?>"></i></a>
                    </li-->
                    <?php if ($rwgetRole['hindi'] || $rwgetRole['english'] || $rwgetRole['arabic'] || $rwgetRole['punjabi'] || $rwgetRole['russian'] || $rwgetRole['sanskrit'] || $rwgetRole['tamil'] || $rwgetRole['marathi']
                           || $rwgetRole['chinese'] || $rwgetRole['greek'] || $rwgetRole['gujarati'] || $rwgetRole['nepali'] || $rwgetRole['oriya']) { ?>
                        <li class="dropdown short-menu">
                            <a href="#" class="dropdown-toggle waves-effect waves-light" title="<?php echo $lang['change_language']; ?>" data-toggle="dropdown"
                               role="button" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-language" aria-hidden="true"></i></a>
                            <ul class="dropdown-menu add-shortcut">
                                 <?php if ($rwgetRole['hindi']) { ?>
                                    <li class="img-flag"><a href="javascript:void(0)" onclick="changelanguage('Hindi')"><label><img src="assets/images/indian.png"/> <?php echo $lang['Hindi']; ?></label></a></li>
                                <?php } if ($rwgetRole['english']) { ?>
                                    <li class="img-flag"><a href="javascript:void(0)" onclick="changelanguage('English')"><label><img src="assets/images/usa.png" /> <?php echo $lang['English']; ?></label></a></li>
                                <?php } ?>
                            </ul>

                        </li>
                    <?php } ?>
                    <?php if ($rwgetRole['create_user'] == '1' || $rwgetRole['add_metadata'] == '1' || $rwgetRole['view_metadata'] == '1' || $rwgetRole['create_workflow'] == '1') { ?>
                        <li class="dropdown short-menu">
                            <a href="#" class="dropdown-toggle waves-effect waves-light" title="<?php echo $lang['quick_access']; ?>" data-toggle="dropdown"
                               role="button" aria-haspopup="true" aria-expanded="false"> <span class="fa fa-plus"></span></a>
                            <ul class="dropdown-menu add-shortcut">
                                <?php
                                if ($rwgetRole['create_user'] == '1') {
                                    ?>
                                    <li><a href="createUser"><label><i class="fa fa-user-plus"></i> <?php echo $lang['Add_User']; ?></label></a></li>
                                    <?php
                                }
                                if ($rwgetRole['add_metadata'] == '1' || $rwgetRole['view_metadata'] == '1') {
                                    ?>
                                    <li><a href="addFields"><label><i class="fa fa-plus"></i> <?php echo $lang['Add_Fields']; ?></label></a></li>
                                    <?php
                                }
                                if ($rwgetRole['create_workflow'] == '1') {
                                    ?>
                                    <li><a href="createWorkflow"><label><i class="fa fa-plus-circle"></i> <?php echo $lang['Nw_Wrkflow']; ?></label></a></li>
                                <?php } if ($rwgetRole['share_with_me'] == '1') {
                                    ?>
                                    <li><a href="shared-with-me"><label><i class="fa fa-share"></i> <?php echo $lang['Shared_With_Me']; ?></label></a></li>
                                <?php }if ($rwgetRole['subscribe_document'] == '1') {
                                    ?>
                                    <li><a href="subscribe-document-list"><label><i class="fa fa-bell"></i> <?php echo $lang['subscribe_doclist']; ?></label></a></li>
                                <?php } ?>

                            </ul>
                        </li>
                    <?php } ?>
                    <li>
                        <a href="#" class="text-weight"><?php echo $lang['Welcome']; ?> : <?php echo $_SESSION['admin_user_name']; ?></a>
                    </li>

                    
                    <li class="dropdown top-menu-item-xs">
                        <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true">

                            <?php 
                            

                            if (!empty($UserRW['profile_picture'])) { ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($UserRW['profile_picture']); ?>" alt="user-img" class="img-circle"> 
                            <?php } else { ?>

                                <img src="./assets/images/avatar.png" alt="Image" class="img-circle">
                            <?php } ?>

                        </a>
                        <ul class="dropdown-menu">
                            <?php
                            //for user role
                            if ($rwgetRole['dashboard_edit_profile'] == '1') {
                                ?>
                                <li><a href="profile"><i class="fa fa-user m-r-10 text-custom"></i> <?php echo $lang['Profile']; ?></a></li>
                            <?php } ?>
                            <?php
                            $permChk = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                            $rwPermChk = mysqli_fetch_assoc($permChk);
                            $slperm = $rwPermChk['sl_id'];
                            //for user role
                            if ($rwgetRole['dashboard_mydms'] == '1') {
                                ?>
                                <li><a href="storage?id=<?php echo urlencode(base64_encode($slperm)); ?>"><i class="glyphicon glyphicon-hdd m-r-10 text-custom"></i> <?php echo $lang['MY_DMS']; ?></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['mail_lists'] == '1') { ?>
                                <li><a href="mail-lists"><i class="fa fa-envelope m-r-5 text-custom"></i> <?php echo $lang['E_mails_List']; ?></a></li>
                            <?php } ?>
                            <?php if ($rwgetRole['dashboard_mytask'] == '1') { ?>
                                <li><a href="myTask"><i class="fa fa-tasks text-custom m-r-10"></i><?php echo $lang['IN_TRAY']; ?></a></li>
                            <?php } ?>
                            <li class="divider"></li>
                            <li><a href="logout"><i class="fa fa-sign-out m-r-10 text-danger"></i><?php echo $lang['Lgut']; ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>
 
<script src="assets/js/gapi.js" type="text/javascript"></script> 

<script type="text/javascript">
    function changelanguage(lang) {
        $.post("lang.php", {lang: lang}, function (results, status) {
            if (status == 'success') {
                //alert(status);
                window.location.reload();
            }
        });
    }
    
    
    
    // Load the Google Transliterate API
    google.load("elements", "1", {
        packages: "transliteration"
    });

    function onLoad() {

        var langcode = '<?php echo $langDetail['lang_code']; ?>';



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
       // var ids = ["searchText", "submail"];
       var elements = document.getElementsByClassName('translatetext');
        control.makeTransliteratable(elements);


        // Show the transliteration control which can be used to toggle between
        // English and Hindi and also choose other destination language.
        // control.showControl('translControl');

    }
   google.setOnLoadCallback(onLoad);

</script> 

