
<?php
require_once './../../sessionstart.php';
require './../config/database.php';
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}

//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
$rid = $_REQUEST['rid'];
$wfid = $_REQUEST['wfid'];
$wftblqry = mysqli_query($db_con, "select * from  tbl_workflow_master where workflow_id='$wfid'");
$rows = mysqli_fetch_assoc($wftblqry);
$ses_val = $_SESSION;
$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);
$wftblqry = mysqli_query($db_con, "select * from  tbl_workflow_master where workflow_id='$wfid'");
$qryform = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
$res = mysqli_fetch_assoc($qryform);
$formid = $res["form_id"];
if (mysqli_num_rows($wftblqry) > 0) {
    $dataContent = mysqli_fetch_assoc($wftblqry);
    $tblname = $dataContent['form_tbl_name'];

    $qry = mysqli_query($db_con, "select * from tbl_wf_reports where rp_id='$rid' and wf_id='$wfid'");

    $rowdata = mysqli_fetch_assoc($qry);
    $recol = $rowdata['coloums'];
    $recol = explode(",", $recol);
    $coloums = $rowdata['coloums'];
    $newcoloums = $coloums . "," . "tbl_id";

    if (!empty($coloums)) {
        if (mysqli_num_rows($qry) > 0) {
            if (!empty($_GET['colname']) && !empty($_GET['search'])) {
                $where = "where " . $_GET['colname'] . " LIKE " . "'%" . $_GET['search'] . "%'";
                if (in_array("emp_id", $recol)) {
                    $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by $where";
//                                             echo $allot;
                } else {
                    $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id";
                }
            } else {
                if (in_array("emp_id", $recol)) {
                    $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by $where";
//                                             echo $allot;
                } else {
                    $allot = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id";
                }
            }
            //echo $allot;
            $coloums = explode(",", $coloums);
            $coloums = implode("','", $coloums);

            $allot_query = mysqli_query($db_con, $allot) or die("Error: " . mysqli_error($db_con));
            $foundnum = mysqli_num_rows($allot_query);
            if ($foundnum > 0) {
                if (is_numeric($_GET['limit'])) {
                    $per_page = $_GET['limit'];
                } else {
                    $per_page = 10;
                }
                $start = isset($_GET['start']) ? $_GET['start'] : '';
                $max_pages = ceil($foundnum / $per_page);
                if (!$start) {
                    $start = 0;
                }
                if (!empty($_GET['colname']) && !empty($_GET['search'])) {
                    $where = "where " . $_GET['colname'] . " LIKE " . "'%" . $_GET['search'] . "%'";

                    if (in_array("emp_id", $recol)) {
                        $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by  $where  LIMIT $start, $per_page";

                        $allote_query = mysqli_query($db_con, $allote) or die("ERROR:" . mysqli_error($db_con));
                    } else {
                        $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id  $where  LIMIT $start, $per_page";
                    }
                } else {
                    if (in_array("emp_id", $recol)) {
                        $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id INNER JOIN  tbl_user_master on tbl_user_master.user_id=tbl_doc_assigned_wf.assign_by   LIMIT $start, $per_page";


                        $allote_query = mysqli_query($db_con, $allote) or die("ERROR:" . mysqli_error($db_con));
                    } else {
                        $allote = "SELECT " . $newcoloums . " FROM " . $tblname . " INNER JOIN tbl_doc_assigned_wf ON " . $tblname . ".ticket_id = tbl_doc_assigned_wf.ticket_id  LIMIT $start, $per_page";
                    }
                    $allote_query = mysqli_query($db_con, $allote) or die("ERROR:" . mysqli_error($db_con));
                }
            }
        }
    }
}
?>
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
<div class="col-md-4 m-t-10 m-l-5">                                            
    <select class="form-control select3" data-live-search="true" name="colname[]">
        <?php
        $labelnameqry = mysqli_query($db_con, "SELECT label,name FROM tbl_form_attribute WHERE name in('$coloums') and dependency_id IS NUll and  fid='$formid'") or die("Label Error:" . mysqli_error($db_con));
        while ($rowdataFetch = mysqli_fetch_assoc($labelnameqry)) {
            if ($rowdataFetch['name'] == "wf_ccenter") {
                echo "<option value=cc_name>" . $rowdataFetch['label'] . "</option>";
            } elseif ($rowdataFetch['name'] == "wf_whouse") {
                echo "<option value=wh_name>" . $rowdataFetch['label'] . "</option>";
            } else {
                ?>
                <option value="<?= $rowdataFetch['name'] ?>"><?= $rowdataFetch['label'] ?></option>
                <?php
            }
        }
        ?>
        <?php if (in_array("task_status", $recol)) { ?>
            <option value="task_status"><?= $lang['Task_Status']; ?></option>
        <?php } if (in_array("action_by", $recol)) { ?>
            <option value="action_by"><?= $lang['approved_by']; ?></option>
        <?php } if (in_array("assign_by", $recol)) { ?>
            <option value="assign_by"><?= $lang['Assigned_By']; ?></option>
        <?php } if (in_array("start_date", $recol)) { ?>
            <option value="start_date"><?= $lang['submitted_date']; ?></option>
        <?php } ?>



    </select>
</div>
<div class="col-md-4 m-t-10">
    <input type="text" id="<?php echo $_GET['id'] ?>" name="search[]" placeholder="<?= $lang['Search']; ?>" class="form-control">
</div>
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script>
    $(".select3").selectpicker();
</script>
<script type="text/javascript">


    google.load("elements", "1", {
        packages: "transliteration"
    });

    function onLoad12() {


        var langcode = '<?php echo $langDetail['lang_code']; ?>';



        var options = {
            sourceLanguage: 'en',
            destinationLanguage: [langcode],
            shortcutKey: 'ctrl+g',
            transliterationEnabled: true
        };


        var control =
                new google.elements.transliteration.TransliterationControl(options);
        var id = "<?php echo $_GET['id'] ?>";
        console.log(id);
        var ids = [id];
        control.makeTransliteratable(ids);


    }
    $.getScript('test.js', function () {
        console.log("hfbfdbfd ");
        // Call custom function defined in script
        onLoad12();
    });

    google.setOnLoadCallback(onLoad12);
    console.log("test 12");

</script> 