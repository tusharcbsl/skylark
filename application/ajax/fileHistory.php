<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require './../config/database.php';
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//require_once '../../application/pages/sendSms.php';
require_once '../../application/pages/function.php';


//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
$sameGroupIDs = array();
$group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['user_ids'];
}
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$rwgetRole = mysqli_fetch_assoc($chekUsr);

if(!isset($_POST['token'], $_POST['doc_id'])){
   echo "Unauthrized Access";  
}

//task process
// for showing group wise  user
$sameGroupIDs = array();
$group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['user_ids'];
}
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);

$user_id = $_SESSION['cdes_user_id'];
$doc_id = preg_replace("/[^0-9 ]/", "", $_POST['doc_id']);
$slid = preg_replace("/[^0-9 ]/", "", $_POST['slid']);
?>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th style="width:30%;"><?php echo $lang['file_ver'] ?></th>
            <th class="text-center"><?php echo $lang['MetaData'] ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        //$versionView = mysqli_query($db_con, "SELECT * FROM tbl_document_master where substring_index(doc_name,'_',-1)='$doc_id' and substring_index(doc_name,'_',1)='$slid' ") or die("Error: test" . mysqli_error($db_con));
		
		$docname = $slid.'_'.$doc_id;
		
		$versionView = mysqli_query($db_con, "SELECT * FROM tbl_document_master where doc_name='$docname' ") or die("Error: test" . mysqli_error($db_con));
		
		
        if (mysqli_num_rows($versionView) > 0) {
            $i = 1.0;
            while ($rwView = mysqli_fetch_assoc($versionView)) {
                if ($rwgetRole['file_version'] == '1') {
                    ?>
                    <tr>
                        <td><?php

                            if(file_exists('../../thumbnail/'.base64_encode($rwView['doc_id']).'.jpg')){ ?>
                                <div> 
                                    <img class="thumb-image" src="thumbnail/<?=base64_encode($rwView['doc_id'])?>.jpg"> 
                                </div>
                            <?php } 
                            if ($i > 0) {
                                echo 'Version ' . $i . '-';
                            }
                            ?><?php echo $rwView['old_doc_name']; ?>
                            <?php
                            //@sk(221118): include view handler to handle different file formats
                            //echo $rwView['old_doc_name'];
                            $file_row = $rwView;
                            require '../../view-handler.php';

                            if ($rwgetRole['delete_version'] == '1') {
                                ?>
                                <a data="<?php echo $rwView['doc_id']; ?>" data-toggle="modal" data-target="#deleteVersion" id="deleteVersionDocument"><i class="fa fa-trash"></i></a>
                                    <?php
                                }
                                ?></td><td><?php
                                $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$file_row[doc_name]'") or die('Error:gg' . mysqli_error($db_con));

                                while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                                    $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                                    while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                                        $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$file_row[doc_id]'");
                                        $rwMeta = mysqli_fetch_assoc($meta);

                                        if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                            if ($rwgetMetaName['field_name'] == 'noofpages' || $rwgetMetaName['field_name'] == 'filename') {
                                                
                                            } else {
                                                echo "<label>" . $rwgetMetaName['field_name'] . "</label> : ";
                                                if ($rwMeta[$rwgetMetaName['field_name']] != '0000-00-00 00:00:00') {

                                                    echo "<span>" . $rwMeta[$rwgetMetaName['field_name']] . "</span>";
                                                }
                                                echo " | ";
                                            }
                                        }
                                    }
                                }
                                ?></td></tr><?php
                        $i = $i + 0.1;
                    }
                }
            }
            ?>


    <script>
        $("a#video").click(function () {
            //alert(id);
            var id = $(this).attr('data');
            //alert(id);
            $.post("application/ajax/videoformat.php", {vid: id}, function (result, status) {
                if (status == 'success') {
                    $("#videofor").html(result);
                    //alert(result);

                }
            });
        });

        $("a#audio").click(function () {
            var id = $(this).attr('data');
            $.post("application/ajax/audioformat.php", {aid: id}, function (result, status) {
                if (status == 'success') {
                    $("#foraudio").html(result);
                    //alert(result);

                }
            });
        });
    </script>