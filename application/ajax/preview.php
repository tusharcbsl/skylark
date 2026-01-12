<?php
require './../../sessionstart.php';
require './../config/database.php';
$workfid = preg_replace("/[^0-9 ]/", "", $_POST['wfid']);
$formbrige = mysqli_query($db_con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$workfid'") or die('Error:' . mysqli_error($db_con));
$formid = mysqli_fetch_assoc($formbrige);
        $userdata = mysqli_query($db_con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'")or die("Error:" . mysqli_errno($db_con));
        $userresult = mysqli_fetch_assoc($userdata);
$formnameqry = mysqli_query($db_con, "select name,label,type from tbl_form_attribute where fid='$formid[form_id]' and dependency_id is null") or die('Error:' . mysqli_error($db_con));
$formnameqry2 = mysqli_query($db_con, "select name from tbl_form_attribute where fid='$formid[form_id]' and dependency_id is null") or die('Error:' . mysqli_error($db_con));

?>
<table border="7" style="border:20px;" class="table table-striped"  align="center">



    <?php
       
//            print_r($_POST);
    $arrdata = array_keys($_POST);

    while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
        ?>
        <tr style="text-align: center;border:20px;">
            <?php
            if ($rowdata["type"] == "header") {
                ?>
                <td colspan="2"><strong><?php echo $rowdata['label']; ?></strong></td>
            <?php
            } else {
                $name = $rowdata['name'];
                if (in_array("wf_ccenter", $arrdata)) {
                    $qry = mysqli_query($db_con, "select * from  tbl_cost_center where cc_id='$_POST[wf_ccenter]'");
                    $rowd = mysqli_fetch_assoc($qry)
                    ?>
                    <td><strong><?php echo $rowdata['label']; ?></strong></td>

                    <td><?php echo $rowd['cc_name'] ?></td>
                    <?php
                    unset($arrdata[0]);
                } else if (in_array("wf_whouse", $arrdata)) {
                    $qry1 = mysqli_query($db_con, "select * from  tbl_whouse_master where wh_id='$_POST[wf_whouse]'");
                    $rowd1 = mysqli_fetch_assoc($qry1);
                    echo "<td><strong>" . $rowdata['label'] . "</strong></td><td>" . $rowd1[wh_name] . "</td>";
                    unset($arrdata[1]);
                } else {
                    if (!empty($_POST[$name])) {
                        echo "<td><strong>" . $rowdata['label'] . "</strong></td><td>" . $_POST[$name] . "</td>";
                    }
                }
            }
            ?>
        </tr>
        <?php
    }
    

    if (isset($_POST['cashvocher']) && isset($_POST['amt'])) {
        echo "<tr><td><strong>Purpose</strong></td><td><strong>Amount</strong></td><td><strong>Paisa</strong></td>";
//              echo "<tr><td>"."Purpose"."</td><td>".implode("*"."<br>", $_POST['cashvocher'])."</td></tr>";
        for ($i = 0; $i < count($_POST['cashvocher']); $i++) {
            echo "<tr><td>" . $_POST['cashvocher'][$i] . "</td><td>" . $_POST['amt'][$i] . "</td><td>" . $_POST['namt'][$i] . "</td>";
        }
    }
//             if(isset($_POST['amt']))
//            {
//                echo "<tr><td>"."Amount"."</td><td>".implode("*"."<br>", $_POST['amt'])."</td></tr>";
//            }
    if (isset($_POST['descp'])) {
        echo "<tr><td><strong>" . "Description" . "</strong></td><td colspan=3>" . $_POST['descp'] . "</td></tr>";
    }
    if (isset($_POST['dname'])) {
        echo "<tr><td><strong>" . "OPS/FM" . "</strong></td><td>" . $_POST['dname'] . "</td></tr>";
    }
    if (isset($_POST['rupee'])) {
        echo "<tr><td><strong>" . "Rupees" . "</strong></td><td>" . $_POST['rupee'] . "</td></tr>";
    }
    if (isset($_POST['amount'])) {
        echo "<tr><td><strong>" . "Amount" . "<strong></td><td>" . $_POST['amount'] . "</td></tr>";
    }
    ?>
</table>
