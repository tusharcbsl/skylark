<?php
if (isset($_POST['submit'])) {


    if (!empty($_POST['userName1'])) {

        $username1 = $_POST['userName1'];
    }

    if (!empty($_POST['userName2'])) {

        $username2 = $_POST['userName2'];
    }
    $metaimage = $_FILES['myImage']['name'];
    $metaimagetemp = $_FILES['myImage']['tmp_name'];

    $imagesize = ($_FILES['myImage']['size'] / 1024);

    $imagetype = $_FILES['myImage']['type'];

    $imagename = $_FILES['myImage']['name'];

    $imagecount = count($_FILES['myImage']['tmp_name']);
}
?>

<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    ?>

    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/jstree/style.css" rel="stylesheet" type="text/css" />
    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <?php require_once './application/pages/sidebar.php'; ?>
            <!-- Left Sidebar End --> 
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">

                        <!-- Page-Title -->
                        <div class="row">
                            <ol class="breadcrumb">
                                <li><a href="#">Storage Management</a></li>
                                <li class="active">View Storage</li>
                            </ol>
                        </div>
                        <div class="card-box">
                         
                            <div class="stepwizard">
                                <div class="stepwizard-row setup-panel">
                                    <div class="stepwizard-step">
                                        <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                                        <h4>DESCRIBES</h4>
                                    </div>
                                    <div class="stepwizard-step">
                                        <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                        <h4>UPLOAD</h4>
                                    </div>
                                    <div class="stepwizard-step">
                                        <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                        <h4>VERIFY</h4>
                                    </div>
                                    <div class="stepwizard-step">
                                        <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                        <h4>COMPLETE</h4>
                                    </div>
                                </div>
                            </div><hr size="30">
                            <form role="form" action="" method="post" enctype="multipart/form-data">
                                <div class="row setup-content" id="step-1">
                                    <div class="col-xs-12">
                                        <div class="col-md-12">
                                            <?php
                                            
                                            if (empty(base64_decode(urldecode(@$_GET['id'])))) {
                                                    $stor = "select sl_id from tbl_storage_level where sl_name='cbsl'";
                                                    $stor_run = mysqli_query($db_con, $stor);
                                                    $rwstor = mysqli_fetch_assoc($stor_run);
                                                    $id = $rwstor['sl_id'];
                                                } else {
                                                    $id = base64_decode(urldecode(@$_GET['id']));
                                                }

                                            $mata = "SELECT tmm.field_name FROM tbl_metadata_to_storagelevel tms INNER JOIN tbl_metadata_master tmm  ON tms.metadata_id = tmm.id where tms.sl_id=$id";
                                            $meta_run = mysqli_query($db_con, $mata);
                                            $i = 1;
                                            while ($rwmeta = mysqli_fetch_assoc($meta_run)) {
                                                ?>
                                                <div class="form-group clearfix">
                                                    <label class="col-lg-1 control-label " for="metaData<?php echo $i; ?>"><?php echo $rwmeta['field_name']; ?></label>

                                                    <div class="col-lg-11 dev">
                                                        <input class="form-control" id="metaData<?php echo $i; ?>" name="userName<?php echo $i; ?>" type="text" onblur="metaDataChange(<?php echo $i; ?>)" required>
                                                    </div>
                                                </div>
                                                <?php
                                                $i++;
                                            }
                                            ?>
                                            <button class="btn btn-primary nextBtn pull-right" type="button" >Next</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row setup-content" id="step-2">
                                    <div class="col-xs-12">
                                        <div class="col-md-12">
                                         <input type="file" class="form-control" name="myImage" required="required" id="myImage"/> 
                                            
                                              <button class="btn btn-primary nextBtn pull-right" type="button" name="submit" >Next</button>

                                        </div>
                                    </div>
                                </div>

                                <div class="row setup-content" id="step-3">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <table class="table table-bordered  dataTable" cellspacing="0" rules="all" border="1" id="ContentPlaceHolder1_grid" style="border-collapse:collapse;"> 
                                               
                                                <tr>
                                        <?php
                                        
                                           if (empty(base64_decode(urldecode(@$_GET['id'])))) {
                                                    $stor = "select sl_id from tbl_storage_level where sl_name='cbsl'";
                                                    $stor_run = mysqli_query($db_con, $stor);
                                                    $rwstor = mysqli_fetch_assoc($stor_run);
                                                    $id = $rwstor['sl_id'];
                                                } else {
                                                    $id = base64_decode(urldecode(@$_GET['id']));
                                                }

                                        $mata = "SELECT tmm.field_name FROM tbl_metadata_to_storagelevel tms INNER JOIN tbl_metadata_master tmm  ON tms.metadata_id = tmm.id where tms.sl_id=$id";
                                        $meta_run = mysqli_query($db_con, $mata);
                                        $i=1;
                                        while ($rwmeta = mysqli_fetch_assoc($meta_run)) {
                                            ?>      
                                                    
                                                        <th scope="col"><div id="bold"><?php echo $rwmeta['field_name'];?></div> </th>
                                                        <td> <div id="metaVal<?php echo $i;?>"></div></td>
                                                   
                                                   
                                                <?php
                                             $i++;   
                                            }
                                            ?>
                                             </tr>
                                               
                                            </table>
                                        </div>
                                        <div class="form-group">
                                        <table class="table table-bordered  dataTable" cellspacing="0" rules="all" border="1" id="ContentPlaceHolder1_grid" style="border-collapse:collapse;">
                                            <tbody>
                                                <tr>
                                                    <th scope="col">Size</th><th scope="col">File Format</th><th scope="col">File</th><th scope="col">Page Count</th>
                                                </tr>
                                                <tr>
                                                    <td> <div id="fileSize"></div></td><td><div id="fileType"></div></td><td><div id="fileName"></div></td><td><div id="pageCount"></div></td>
                                                    
                                                </tr>
                                            </tbody>
                                        </table>
                                        </div>
                                        
                                    </div>
                                    <button class="btn btn-primary nextBtn pull-right" type="button" >Next</button>
                                </div>
                                <div class="row setup-content" id="step-4">
                                    <div class="col-xs-12">
                                        <div class="col-md-12">
                                            <div class="col-lg-12">
                                                <input id="acceptTerms-2" name="acceptTerms2" type="checkbox" class="required">
                                                <label for="acceptTerms-2">I agree with the Terms and Conditions.</label>
                                            </div>
                                            <button class="btn btn-success pull-right" type="button">UPLOAD FILE !</button>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>

                    </div> <!-- container -->
                    
                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>
            </div>
            <!-- Right Sidebar -->
            <?php require_once './application/pages/rightSidebar.php'; ?>
            <!-- /Right-bar -->
            <?php require_once './application/pages/footerForjs.php'; ?>
            <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
            <script src="assets/plugins/jstree/jstree.min.js"></script>
            <script src="assets/pages/jquery.tree.js"></script>
            <script src="assets/jscustom/wizard.js"></script>
            <script>
                function metaDataChange(val)
                {
                   var valInput= $("#metaData"+val).val();
                   //alert(valInput);
                   $("#metaVal"+val).html(valInput);
                }
   //image detail              
   $('#myImage').bind('change', function() {
  //this.files[0].size gets the size of your file.
  $("#fileSize").html(this.files[0].size);
$("#fileName").html(this.files[0].name);
$("#fileType").html(this.files[0].type);
//var input = document.getElementById("#myImage");
var reader = new FileReader();
reader.readAsBinaryString(this.files[0]);
reader.onloadend = function(){
    var count = reader.result.match(/\/Type[\s]*\/Page[^s]/g).length;
    $("#pageCount").html(count);
       // console.log('Number of Pages:',count );
}
});




                </script>
    </body>
</html>


