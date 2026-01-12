<?php

$slpermIds = array();
$perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
while ($rwPerm = mysqli_fetch_assoc($perm)) {
    $slpermIds[] = $rwPerm['sl_id'];
}
$slpermIdes = implode(',', $slpermIds);

if(isset($_GET['token'])){
    $url = explode('&', basename($_SERVER['REQUEST_URI']));
    array_pop($url);
    $currentURL =  implode('&', $url); 
}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin which can be used to manage broker and sub broker">
    <meta name="author" content="Brokers Bazaar">
    <link rel="shortcut icon" href="assets/images/favicon/favicon.ico">
    <title><?php echo $projectName; ?> :: <?php echo $lang['Head_Title']; ?></title>
    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="assets/plugins/morris/morris.css">

    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="assets/images/favicons//favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="assets/images/favicons//favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="assets/images/favicons//manifest.json">
    <link rel="mask-icon" href="assets/images/favicons//safari-pinned-tab.svg" color="#5bbad5">
    <!--sweet alert-->
    <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <script src="assets/js/modernizr.min.js"></script>
</head>
