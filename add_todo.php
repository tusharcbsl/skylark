<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Add To Do</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link href="../../dist/img/favicon.ico" rel="icon" type="image/x-icon" />
    <link rel="stylesheet" href="../../bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->

    <link rel="stylesheet" href="../../bower_components/Ionicons/css/ionicons.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="../../bower_components/bootstrap-daterangepicker/daterangepicker.css">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="../../bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="../../plugins/iCheck/all.css">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="../../bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
    <!-- Bootstrap time Picker -->
    <link rel="stylesheet" href="../../plugins/timepicker/bootstrap-timepicker.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../../bower_components/select2/dist/css/select2.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
     <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="../../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
        .cke_chrome {
            width: 95% !important;
        }
        #desc_box{
                 margin-left: 38px;
            }

        .bootstrap-timepicker-widget.dropdown-menu.open {
                margin-left: 44%;
        }

        @media only screen and (max-width: 768px) {
            .cke_chrome {
                margin-left: 0px;
                width: 121% !important;
            }
            #desc_box{
                margin-left: 0px;
                width: 83%;
            }
            .time-apps {
                width: 100%;
            }
            .bootstrap-timepicker-widget.dropdown-menu.open {
                margin-left: 0px;
            }
        }
    </style>


</head>

<body class="hold-transition skin-blue sidebar-mini">
     <!-- Preloader -->
          <div id="preloader">
            <div id="status">&nbsp;</div>
        </div>
    <!-- ./Preloader-->
    <!--Add loading-->
<div id="checking" style="display:none;position: fixed;top: 0;left: 0;width: 100%;height: 100%;background: #f4f4f4;z-index: 99;">
<div class="text" style="position: absolute;top: 40%;left: 8%;height: 100%;width: 100%;font-size: 5px;text-align: center;">
<span class="text-center"><img src="../../dist/img/35.gif" alt="Loading" style="width:100px;"></span>
</div>
</div>
<!--./Add loading-->
    <div class="wrapper">

        <?php
            if (isset($_SESSION['isLogin'])) {
                $_SESSION['active'] = 'addtodo';
            }
            include '../common_pages/header.php';

            if(!($addTodo || $updateTodo)){
                echo '<script>window.history.back();</script>';
                exit;
            }

            echo '<span id="hash" style="display:none;">'.$service->setHash("todoHash").'</span>';
            
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <!-- Main content -->
            <section class="content">
                <!-- Horizontal Form -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title" id="page_title">Add To Do</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal" id="todo_form" method="post">
                            <input type="hidden" name="todo_id" id="todo_id" class="form-control">

                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5 col-sm-5 ">
                                            <div class="form-group">
                                                <label for="taskname" class="col-sm-5 control-label">Task Name<span class="text-red"><sup>*</sup></span></label>

                                                <div class="col-sm-7">
                                                    <input type="text" name="taskname_id" id="taskname" class="form-control" tabindex="1" placeholder="Enter Task Name" onkeypress="return onlyAlphabets(event,this);" autofocus>
                                                    <p class="text-red"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-md-5 col-sm-5">
                                            <div class="form-group">
                                                <label for="courts" class="col-sm-5 control-label">Employee ID<span class="text-red"><sup>*</sup></span></label>

                                                <div class="col-sm-7" id="EmpId">
                                                    <select class="form-control select2" style="width: 100%;" multiple="multiple" name="emp_id" tabindex="2" id="emp_id" data-placeholder="Employee ID">
                                                        <?php
                                                            // if ($user_type == 'admin') {
                                                                echo '<option value="' . $refid . '" selected>Self</option>';
                                                                try {
                                                                    // $dynamicDb
                                                                    $stmt = $dynamicDb->query('SELECT * FROM employee_registration');
                                                                    $res = $stmt->fetchAll(PDO::FETCH_OBJ);
                                                                    for ($i = 0; $i < sizeof($res); $i++) {
                                                                        echo '<option value="' . $res[$i]->emp_ref_id . '" >' . $res[$i]->emp_fname . ' ' . $res[$i]->emp_lname . ' (' . $res[$i]->emp_cust_id . ' )</option>';
                                                                    }

                                                                    $stmt = $dynamicDb->query('SELECT * FROM all_admin_details  WHERE admin_ref_id <> "'.$refid.'"');
                                                                    $res = $stmt->fetchAll(PDO::FETCH_OBJ);
                                                                    for ($i = 0; $i < sizeof($res); $i++) {
                                                                        echo '<option value="' . $res[$i]->admin_ref_id . '" >' . $res[$i]->admin_fname . ' ' . $res[$i]->admin_lname . ' (' . $adminDesc . ' )</option>';
                                                                    }

                                                                } catch (Exception $e) {
                                                                    echo $e->getMessage();
                                                                }
                                                            // } else {
                                                            //     echo '<option value="' . $_SESSION['refid'] . '" selected>' . $_SESSION['userFname'] . ' ' . $_SESSION['userLname'] . '</option>';
                                                            // }
                                                        ?>
                                                    </select>
                                                    <p class="text-red"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-lg-5 col-md-5 col-sm-5">
                                            <div class="form-group">
                                                <label for="task_date" class="col-sm-5 control-label">Task Date<span class="text-red"><sup>*</sup></span></label>
                                                <div class="col-sm-7">
                                                    <div class="input-group date " id="calen">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" placeholder="Select Task Date" class="form-control pull-right datepicker holidayCheck" mandatory="no" tabindex="3" id="dateFilling" readonly name="task_date_id">
                                                    </div>
                                                    <p class="text-red"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-5 col-md-5 col-sm-5">
                                            <div class="bootstrap-timepicker">
                                                <div class="form-group">
                                                    <label for="task_date" class="col-sm-5 control-label">Task Time<span class="text-red"><sup>*</sup></span></label>

                                                    <div class="col-sm-7">
                                                        <div class="input-group time-apps" id="task_time">
                                                            <input type="text" class="form-control timepicker" id="task_time_id" tabindex="4" name="task_time" readonly>
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-clock-o"></i>
                                                            </div>
                                                        </div>
                                                        <p class="text-red"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5 col-sm-5 " id="notification">
                                            <div class="form-group">
                                                <label for="task_notify" class="col-sm-5 control-label">Task Notification Frequency<span class="text-red"><sup>*</sup></span></label>

                                                <div class="col-sm-7">
                                                    <select class="form-control select2" name="task_notify" id="task_frequency" style="width: 100%;" tabindex="5" data-placeholder="Task Notification Frequency">
                                                        <option></option>
                                                        <option value="0">Same Day</option>
                                                        <option value="1">1 Day Before</option>
                                                        <option value="2">2 Day Before</option>
                                                        <option value="3">3 Day Before</option>
                                                        <option value="4">4 Day Before</option>
                                                        <option value="5">5 Day Before</option>
                                                        <option value="6">6 Day Before</option>
                                                        <option value="7">7 Day Before</option>
                                                    </select>
                                                    <p class="text-red"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-md-5 col-sm-5">
                                            <div class="bootstrap-timepicker">
                                                <div class="form-group">
                                                    <label for="task_date" class="col-sm-5 control-label">Notification Time<span class="text-red"><sup>*</sup></span></label>

                                                    <div class="col-sm-7">
                                                        <div class="input-group time-apps" id="note_time">
                                                            <input type="text" class="form-control timepicker" id="noti_time_id" tabindex="6" name="task_noti_time" value="12:00" readonly>
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-clock-o"></i>
                                                            </div>
                                                        </div>
                                                        <p class="text-red"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-10 col-md-10 col-sm-10" id="desc_box">
                                            <div class="form-group">
                                                <label for="description" class="col-sm-2 control-label text-right">Description</label>

                                                <div class="col-sm-10">
                                                    <textarea class="form-control" tabindex="7" rows="6" id="description" name="task_description" placeholder="Description"></textarea>
                                                    <p class="text-red"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 ">
                                            <div class="form-group">
                                                <div class="col-md-2 col-md-offset-5 text-center">
                                                    <button type="submit" class="btn btn-block btn-info" tabindex="8">Proceed</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </form>
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-lg-1"></div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include '../common_pages/footer.php';?>
        <!-- /.control-sidebar -->
        <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 3 -->
    <script src="../../bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="../../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="../../bower_components/select2/dist/js/select2.full.min.js"></script>
    <!-- InputMask -->
    <script src="../../plugins/input-mask/jquery.inputmask.js"></script>
    <script src="../../plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
    <script src="../../plugins/input-mask/jquery.inputmask.extensions.js"></script>
    <!-- date-range-picker -->
    <script src="../../bower_components/moment/min/moment.min.js"></script>
    <script src="../../bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap datepicker -->
    <script src="../../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!-- bootstrap color picker -->
    <script src="../../bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
    <!-- bootstrap time picker -->
    <script src="../../plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <!-- SlimScroll -->
    <script src="../../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- iCheck 1.0.1 -->
    <!-- <script src="../../plugins/iCheck/icheck.min.js"></script> -->
    <!-- FastClick -->
    <script src="../../bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="../../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../../dist/js/demo.js"></script>
    <!-- CK Editor -->
    <script src="../../bower_components/ckeditor/ckeditor.js"></script> 
    <!-- Bootstrap WYSIHTML5 -->
    <script src="../../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <!-- CKEditor Script -->
    <script>
    $(function () {
        // Replace the <textarea id="editor1"> with a CKEditor
        // instance, using default configuration.
        CKEDITOR.replace( 'description', {
        uiColor: '#9e3237',
        colorButton_colors : '#ffff',
        removeButtons: 'Link,Table,Image,Anchor,NumberedList,BulletedList,Blockquote,Subscript,Superscript,HorizontalRule,Indent,SpecialChar,Unlink,Outdent,Styles,About'
     });
        
        //bootstrap WYSIHTML5 - text editor
        $('.textarea').wysihtml5()
    });
    </script> 
     <!-- ./CKEditor Script -->
    <!-- Page script -->
    <!--  AUTH SCRIPT -->

    <!--  SUBMITTODO SCRIPT -->
    <script src="submit_todo.js"></script>
    <script src="../common_pages/common.js"></script>


<script>
    $(document).ready(function(){
        var getUpdateData = window.localStorage.getItem('updateId');
       // alert(getUpdateData);
        if(getUpdateData){
            $('#page_title').html('Update To Do');
            console.log(JSON.parse(getUpdateData));
            var toUpdate = JSON.parse(getUpdateData);
            window.localStorage.removeItem('updateId');
            $('#todo_id').val(toUpdate[0].todo_id);
            $('#taskname').val(toUpdate[0].task_name);

            // $('#emp_id').val(toUpdate[0].emp_id);
            $('#emp_id').select2().val(toUpdate[0].emp_id.split(",")).trigger('change');

            $('#dateFilling').val(toUpdate[0].task_date);


            $('#task_time_id').val(toUpdate[0].task_time);

            $('#task_frequency').select2().val(toUpdate[0].task_notification_frequency).trigger('change');

            $('#noti_time_id').val(toUpdate[0].task_notify_time);

        $('#description').val(toUpdate[0].task_description);

        }else{
            $('#todo_id').val('');
        }

        $(".select2-selection").focus(function(){
            $('.dropdown-menu').removeClass('open');
        });

        $("#description").focus(function(){
            $('.dropdown-menu').removeClass('open');
        });

    });
   
</script>

</body>

</html>