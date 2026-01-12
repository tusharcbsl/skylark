 <?php

 require_once 'connection.php';

  if(isset($_POST['userid'])&&!empty($_POST['userid'])){
		
    $res = array();
		$userid = $_POST['userid'];
		
	    $perm = mysqli_query($con, "select sl_id from tbl_storagelevel_to_permission where user_id='$userid' group by sl_id");
      $rwPerm = mysqli_fetch_assoc($perm);
      $slperm = $rwPerm['sl_id'];
      $sllevel = mysqli_query($con, "select * from tbl_storage_level where sl_id='$slperm'");
      $rwSllevel = mysqli_fetch_assoc($sllevel);
      $level = $rwSllevel['sl_depth_level'];
		
    findChild($slperm, $level, $slperm, $slperm);
    echo json_encode($res);
    
                                                     }


		
	


                                               
                                                  /*  <select class="form-control select2" id="parent" name="parentName" required>
                                                        <option disabled selected>Select</option>
                                                        <?php
                                                        if (isset($_GET['parentName']) && !empty($_GET['parentName'])) {
                                                            $parentId = $_GET['parentName'];
                                                        }
                                                        findChild($slperm, $level, $slperm, $parentId);
                                                        ?>
                                                    </select> */



                                         

                                                    function findChild($sl_id, $level, $slperm, $parentId) {

                                                        global $con;
                                                        global $res;

                                                        if ($sl_id == $parentId) {
                                                          //  echo '<option value="' . $sl_id . '">';
                                                            
                                                            //parentLevel($sl_id, $con, $slperm, $level, '')."&&".$sl_id;
                                                            //echo '</option>';
                                                          
                                                         array_push($res, parentLevel($sl_id, $con, $slperm, $level, '')."&&".$sl_id) ;
                                                        } else {
                                                            //echo '<option value="' . $sl_id . '" >';
                                                          //parentLevel($sl_id, $con, $slperm, $level, '')."&&".$sl_id;
                                                          
                                                         array_push($res, parentLevel($sl_id, $con, $slperm, $level, '')."&&".$sl_id); 
                                                            //echo '</option>';
                                                        }

                                                        $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id' ";

                                                        $sql_child_run = mysqli_query($con, $sql_child) or die('Error:' . mysqli_error($con));

                                                        if (mysqli_num_rows($sql_child_run) > 0) {

                                                            while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                                                                $child = $rwchild['sl_id'];
                                                                findChild($child, $level, $slperm, $parentId);
                                                             //die();
                                                            }
                                                        }
                                                    }

 function parentLevel($slid, $con, $slperm, $level, $value) {
   global $res;

                                                        if ($slperm == $slid) {
                                                            $parent = mysqli_query($con, "select * from tbl_storage_level where sl_id='$slid' ") or die('Error' . mysqli_error($con));
                                                            $rwParent = mysqli_fetch_assoc($parent);

                                                            if ($level < $rwParent['sl_depth_level']) {
                                                                parentLevel($rwParent['sl_parent_id'], $con, $slperm, $level, $rwParent['sl_name']);
                                                            }
                                                        } else {
                                                            $parent = mysqli_query($con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'") or die('Error' . mysqli_error($con));
                                                            if (mysqli_num_rows($parent) > 0) {

                                                                $rwParent = mysqli_fetch_assoc($parent);
                                                                if ($level < $rwParent['sl_depth_level']) {
                                                                    parentLevel($rwParent['sl_parent_id'], $con, $slperm, $level, $rwParent['sl_name']);
                                                                }
                                                            } else {
                                                                $parent = mysqli_query($con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($con));
                                                                $rwParent = mysqli_fetch_assoc($parent);
                                                                $getparnt = $rwParent['sl_parent_id'];
                                                                if ($level <= $rwParent['sl_depth_level']) {
                                                                    parentLevel($getparnt, $con, $slperm, $level, $rwParent['sl_name']);
                                                                } else {
                                                                    //header('Location: ./index.php');
                                                                    // header("Location: ./storage?id=".urlencode(base64_encode($slperm)));
                                                                }
                                                            }
                                                        }

                                                        //echo $value;
                                                        if (!empty($value)) {
                                                            $value = $rwParent['sl_name'] . ' > ';
                                                        } else {
                                                            $value = $rwParent['sl_name'];
                                                        }
                                                         array_push($res,$value);}
 
                                                    ?>
