<?php

require_once('loginvalidate.php');
require_once('./application/config/database.php');

$sql = mysqli_query($db_con, "select * from tbl_client_master");

$i = 1;

$data = "";
$data .= '<table border=1><thead><tr>';
$data .= '<th>S.No</th>
                                                            <th>Customer CRM ID</th>
                                                            <th>Company Name</th>
                                                            <th>Client Name</th>
                                                            <th>Client EmailID</th>
                                                            <th>Plan Type</th>
                                                            <th>Product Type</th>
                                                            <th>Start Date</th>
                                                            <th>End Date</th>
                                                            <th>Domain Name</th>                                                            
                                                        </tr>                                                        
                                                    </thead><tbody>';
while ($rwExport = mysqli_fetch_assoc($sql)) {

    $data .= '<tr>';
    $data .= '<td>' . $i . '</td>';
    $data .= '<td>' . $rwExport['crm_cid'] . '</td>';
    $data .= '<td>' . $rwExport['company'] . '</td>';
    $data .= '<td>' . $rwExport['fname'] . " " . $rwExport['lname'] . '</td>';
    $data .= '<td>' . $rwExport['email'] . '</td>';
    $data .= '<td>' . $rwExport['total_user'] . " Users " . $rwExport['total_memory'] . " GB" . '</td>';
    $qry_product = mysqli_query($db_con, "select * from tbl_user_roles where role_id='$rwExport[product_type]'");
    $qry_product_fetch = mysqli_fetch_assoc($qry_product);
    $data .= '<td>' . $qry_product_fetch['user_role'] . '</td>';
    $data .= '<td>' . date("d-M-Y", strtotime($rwExport['reg_date'])) . '</td>';
    $data .= '<td>' . date("d-M-Y", $rwExport['valid_upto']) . '</td>';
    $data .= '<td>' . $rwExport['subdomain'] . '</td></tr>';
    $i++;
}
$data .= " </tbody></table>";

$fileName = "Client List.xls";
echo $data;
header("Content-Disposition: attachment; filename=\"$fileName\"");
?>