<?php

require_once './application/config/database.php';

// Set autocommit to off
//mysqli_autocommit($db_con,FALSE);  
$firstname = filter_input(INPUT_POST, "firstname");
$firstname = preg_replace("/[^a-zA-Z ]/", "", $firstname); //filter name
$firstname = mysqli_real_escape_string($db_con, $firstname);

$lastname = filter_input(INPUT_POST, "lastname");
$lastname = preg_replace("/[^a-zA-Z ]/", "", $lastname); //filter name
$lastname = mysqli_real_escape_string($db_con, $lastname);
$email = filter_input(INPUT_POST, "email");
$email = preg_replace("/[^a-zA-Z0-9_@.-]/", "", $email); //filter email
$email = mysqli_real_escape_string($db_con, $email);

$phone = filter_input(INPUT_POST, "phone");
$phone = preg_replace("/[^0-9]/", "", $phone); //filter phone
$phone = mysqli_real_escape_string($db_con, $phone);

$company = filter_input(INPUT_POST, "cname");
// $company = preg_replace("/[^0-9A-Za-z]/", "", $company); //filter phone
$company = mysqli_real_escape_string($db_con, $company);
$password = filter_input(INPUT_POST, "password");
$password = mysqli_real_escape_string($db_con, $password);

$validupto = filter_input(INPUT_POST, "validupto_year");
$validupto= strtotime("+7 day",$validupto);
// $validupto = strtotime(date("Y-m-d", strtotime("+" . $validupto . " " . $validupto_year))); //end of validity in time stamp
$plantype = filter_input(INPUT_POST, "plantype");
$product_type = filter_input(INPUT_POST, "product_type");
$product_type = mysqli_real_escape_string($db_con, $product_type);

$total_user = preg_replace("/[^0-9]/", "", $_POST['nouser']);
$total_memory = preg_replace("/[^0-9]/", "", $_POST['tomemory']);

$featurelist = $_POST['featurelist'];
$coustomer_id = $_POST['aggregate_id'];
$validateAlready = mysqli_query($db_con, "select * from  `tbl_client_master` where crm_cid='$coustomer_id' and product_type='$product_type' and plan_type='$plantype'");
if (mysqli_num_rows($validateAlready) > 0) {
    $dataAlredy= mysqli_fetch_assoc($validateAlready);
    $subdomain=$dataAlredy['subdomain'];
    $runupdate = mysqli_query($db_con, "UPDATE `tbl_client_master` SET valid_upto='$validupto' , total_memory='$total_memory' , total_user='$total_user' WHERE crm_cid='$coustomer_id'");
    if ($runupdate) {
        
        $data = array("status" => True, "domain_name" => $subdomain, "data" => "Client Update Successfully");
        echo json_encode($data);
        exit();
    } else {
        $data = array("status" => False, "data" => "Client Update Failed! ");
        echo json_encode($data);
    }
} else {
    if ($product_type == 1) {
    define('domain', "ezeepea.com");
    define('proname', "ezeefile");
} elseif ($product_type == 2) {
    define('domain', "ezeepea.com");
    define('proname', "ezeeprocess");
} elseif ($product_type == 3) {
    define('domain', "ezeepea.com");
    define('proname', "ezeeoffice");
} else {
    $data = array("status" => False, "data" => "Client Update Failed! ");
    echo json_encode($data);
    exit();
}
$subDomain = filter_input(INPUT_POST, "subd");
$subDomain = preg_replace("/[^a-zA-Z_ ]/", "", $subDomain); //filter name
$FullSubDomain = $subDomain . "." . domain; //new subdomain
$subDomain = mysqli_real_escape_string($db_con, $subDomain);
    $chkDuplicateCompany = mysqli_query($db_con, "select * from  `tbl_client_master` where subdomain='$FullSubDomain'");
    if (mysqli_num_rows($chkDuplicateCompany) > 0) {
        $fetchValidation = mysqli_fetch_assoc($chkDuplicateCompany);
        if ($fetchValidation['subdomain'] == $FullSubDomain) {
            $data = array("status" => FALSE, "data" => "Sub Domain Already Exist!");
            echo json_encode($data);
        }
    } else {

        $create_client = mysqli_query($db_con, "insert into `tbl_client_master`(`fname`,`lname`,`email`,`company`,`password`,`profile`,`plan_type`,`valid_upto`,`product_type`,`crm_cid`,`total_memory`,`total_user`,`subdomain`)values('$firstname','$lastname','$email','$company','$password','$image','$plantype','$validupto','$product_type','$coustomer_id','$total_memory','$total_user','$FullSubDomain')")or die(mysqli_error($db_con));
        $lastinsertid = mysqli_insert_id($db_con);
        if ($create_client) {
//            echo "R4";
            $client_status = createNewDB($company, $lastinsertid, $email, $password, $db_con, $date, $firstname, $lastname, $phone, $image, $dbHost, $dbUser, $dbPwd, $product_type, $projectName, $coustomer_id, $mainDirectorySrc, $subDomain, $FullSubDomain, $featurelist);
            if ($client_status['status']) {
                if (array_key_exists("connect", $client_status)) {
                    $connection = $client_status['connect'];
                    if (!empty($client_status[db_name])) {
                        $qry_remove_db = mysqli_query($connection, "DROP DATABASE $client_status[db_name]");
                        $qry_remove_client_id = mysqli_query($db_con, "Delete From `tbl_client_master` where client_id='$lastinsertid'");
                    }
                } else {

                    $qry_remove_client_id = mysqli_query($db_con, "Delete From `tbl_client_master` where client_id='$lastinsertid'");
                }

                $data = array("status" => FALSE, "data" => "Company Creation Failed!", "wantedmsg" => print_r($client_status));
                echo json_encode($data);
            } else {
                $qry_update_crm = mysqli_query($db_con, "update tbl_client_master set crm_cid='$coustomer_id' where client_id='$lastinsertid'");
                //mysqli_commit($db_con);
                $data = array("status" => TRUE, "domain_name" => $FullSubDomain, "data" => "Company created successfully");
                echo json_encode($data);
            }
//             
        } else {
            $data = array("status" => FALSE, "data" => "Company created successfully");
            echo json_encode($data);
        }
//            }
//        } else {
//            echo '<script>taskFailed("client_create", "' . $response['message'] . '!")</script>';
//        }
    }
}

function generateLicenseKey($clientdb, $clientId) {

    $key = '987654123';
    $plaintext = $clientdb . '%' . $clientId;
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    $ciphertext = base64_encode($iv . /* $hmac. */$ciphertext_raw);

    return $ciphertext;
}

function decryptLicenseKey($licenseKey) {
    $key = '987654123';
    $c = base64_decode($licenseKey);
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
//$hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen/* +$sha2len */);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    return $original_plaintext;
}

function createNewDB($company, $lastinsertid, $email, $password, $db_con, $date, $firstname, $lastname, $phone, $image, $dbHost, $dbUser, $dbPwd, $product_type, $projectName, $coustomer_id, $mainDirectorySrc, $subDomain, $FullSubDomain, $featurelist) {
    $fetchMainColQry = mysqli_query($db_con, "show tables");
    if (mysqli_num_rows($fetchMainColQry) > 0) {
        $mainDbTables = array();
        while ($fetchAllColsMain = mysqli_fetch_array($fetchMainColQry)) {
            array_push($mainDbTables, $fetchAllColsMain['0']);
        }
        /*
         * Unset unneccessary tables values
         */
        array_splice($mainDbTables, array_search("tbl_aggregate_user_master", $mainDbTables), 1);
        array_splice($mainDbTables, array_search("tbl_client_master", $mainDbTables), 1);
        array_splice($mainDbTables, array_search("tbl_plantype", $mainDbTables), 1);

        /*
         * End
         */

        $conn = mysqli_connect($dbHost, $dbUser, $dbPwd);
        if ($conn) {
            $company = trim($company);
            // Create database
            $db_company = preg_replace("/[^A-Za-z]/", "_", $company); //filter phone
            $ddb_name = "DMS_" . $db_company . "_" . strtotime($date); //name of database
            //$licenseKey=NULL;
            /*
             * this line generate error in linux server
             */
            $licenseKey = generateLicenseKey($ddb_name, $lastinsertid);
            $sql = "CREATE DATABASE $ddb_name";
            $result = mysqli_query($conn, $sql); //create new database for particular client
            if ($result) {
                $conn = new mysqli($dbHost, $dbUser, $dbPwd, $ddb_name); // connection with dynamic database
                for ($index = 0; $index < count($mainDbTables); $index++) {
                    $newTableName = $mainDbTables[$index];
                    $fetchTbaleExist = "SHOW CREATE TABLE $newTableName";
                    $fetchTableQry = mysqli_query($db_con, $fetchTbaleExist);
                    if ($fetchTableQry) {
                        $fetchA = mysqli_fetch_assoc($fetchTableQry);
                        $query = $fetchA['Create Table'];
                        $tbl_qry = mysqli_query($conn, $query);
                        if ($tbl_qry) {
                            
                        } else {
                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "qry_error:" . $query);
                        }
                    } else {
                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "qry_error:" . $fetchTbaleExist);
                    }
                }
                /*
                 * admin info insert new database
                 */
                $insertAdminUserMaster = mysqli_query($db_con, "select * from `tbl_user_master` where user_id='1'");
                $res = mysqli_fetch_all($insertAdminUserMaster, MYSQLI_ASSOC);
                $userdataAdmin = "'" . implode("','", $res[0]) . "'";
                $insertSuperUser = mysqli_query($conn, "insert into `tbl_user_master` values($userdataAdmin)");
                if ($insertSuperUser) {
                    $insertAdminRoleMaster = mysqli_query($db_con, "select * from `tbl_user_roles` where role_id='1'");
                    $roleAdmin = mysqli_fetch_all($insertAdminRoleMaster, MYSQLI_ASSOC);
                    $adminRoleInsert = "'" . implode("','", $roleAdmin[0]) . "'";
                    $insertSuperUserRole = mysqli_query($conn, "insert into `tbl_user_roles` values($adminRoleInsert)");
                    if ($insertSuperUserRole) {
                        $grp_to_su = mysqli_query($conn, "INSERT INTO `tbl_bridge_role_to_um` (`role_id`,`user_ids`) VALUES ('1','1')");
                        if ($grp_to_su) {
                            $storage_permissionSU = mysqli_query($conn, "insert into `tbl_storagelevel_to_permission`(`user_id`,`sl_id`) values('1','113')");
                            if ($storage_permissionSU) {
                                $SUgroup = mysqli_query($conn, "insert into `tbl_group_master`(`group_id`,`group_name`) values('1','SUPER ADMIN')");
                                if ($SUgroup) {
                                    $SUgrouprole = mysqli_query($conn, "insert into `tbl_bridge_grp_to_um`(`id`,`group_id`,`user_ids`,`roleids`) values('1','1','1','1')");
                                    if ($SUgrouprole) {
                                        
                                    } else {
                                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Bridge group to user Problem" . mysqli_error($conn));
                                    }
                                } else {
                                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Group Add Problem" . mysqli_error($conn));
                                }
                            } else {
                                return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Permission Problem" . mysqli_error($conn));
                            }
                        } else {
                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database User Master Problem" . mysqli_error($conn));
                        }
                    } else {
                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database admin role Problem" . mysqli_error($conn));
                    }
                } else {
                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Admin add Problem" . mysqli_error($conn));
                }

                /*
                 * Admin end
                 * 
                 * FIrst Client info add start
                 */
                //  echo "update `tbl_client_master` SET db_name='$ddb_name', license_key='$licenseKey' where client_id='$lastinsertid'"; 
                $update_dbname_clientqry = mysqli_query($db_con, "update `tbl_client_master` SET db_name='$ddb_name', license_key='$licenseKey' where client_id='$lastinsertid'")or die(mysqli_error($db_con));
                if ($update_dbname_clientqry) {
                    for ($kj = 0; $kj < count(explode(",", $featurelist)); $kj++) {
                        $newdata[] = 1;
                    }
                    $new_imploded_data = "'" . implode("'" . "," . "'", $newdata) . "'";
                    $Insert_New_User = mysqli_query($conn, "insert into `tbl_user_roles`(role_id,user_role,$featurelist)values('2','Admin',$new_imploded_data)");
                    $new_user_role = mysqli_insert_id($conn);
                    if ($Insert_New_User) {
                        $create = mysqli_query($conn, "insert into tbl_user_master (`user_email_id`, `first_name`, `last_name`, `password`, `designation`, `phone_no`, `profile_picture`, `superior_name`, `superior_email`, `user_created_date`, `emp_id`) values('$email','$firstname','$lastname','$password','null','$phone','$image','null','null','$date', 'null')");
                        $user_id = mysqli_insert_id($conn);
                        $user_idBridge = "1," . $user_id;
                        if ($create) {

                            $grp_to_um = mysqli_query($conn, "INSERT INTO `tbl_bridge_role_to_um` (`role_id`,`user_ids`) VALUES ('$new_user_role','$user_id')");
                            if ($grp_to_um) {
                                $create_Root_Strg = mysqli_query($conn, "insert into `tbl_storage_level`(`sl_id`,`sl_name`,`sl_parent_id`,`sl_depth_level`) values('113','$company','0',0)");
                                if ($create_Root_Strg) {
                                    $storage_permission = mysqli_query($conn, "insert into `tbl_storagelevel_to_permission`(`user_id`,`sl_id`) values('$user_id','113')");
                                    if ($storage_permission) {
                                        $Firstgroupqry = mysqli_query($conn, "insert into `tbl_group_master`(`group_id`,`group_name`) values('2','ADMIN')");
                                        if ($Firstgroupqry) {
                                            $firstgrouproleqry = mysqli_query($conn, "insert into `tbl_bridge_grp_to_um`(`id`,`group_id`,`user_ids`,`roleids`) values('2','2','$user_idBridge','$new_user_role')");
                                            if ($firstgrouproleqry) {

                                                $rootdir = $_SERVER['DOCUMENT_ROOT'] . "/ezeefile_saas_client";
                                                $newClientDirectory = $_SERVER['DOCUMENT_ROOT'] . "/ezeefile_saas_client/" . $ddb_name . "/";
                                                if (!is_dir($rootdir)) {
                                                    mkdir($rootdir, 0777, TRUE);
                                                }
                                                if (!is_dir($newClientDirectory)) {
                                                    mkdir($newClientDirectory, 0777, TRUE);
                                                }
                                                exec("cp -r $mainDirectorySrc" . "*" . " ./ $newClientDirectory", $shell); //copy whole directory

                                                chmod($newClientDirectory, 0777);

                                                if ($shell[0] != 0) {

                                                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Directory Creation Failed");
                                                }
                                                exec("cp $mainDirectorySrc.htaccess $dest"); //copy htaccess file
                                                $path_to_file = $newClientDirectory . '/application/config/conf.php';
                                                $file_contents = file_get_contents($path_to_file);
                                                $file_contents = preg_replace('/\bezeefile_saas\b/u', $ddb_name, $file_contents);
                                                $file_contents = preg_replace('/\bdummymaindb\b/u', "ezeefile_saas", $file_contents);
                                                $file_contents = str_replace("iXejqbRUFYEYvBW6Qa9s4hyIgPeOAQK31pPm3vmC8Ss=", $licenseKey, $file_contents);
                                                // $confUpdates='$mainDbName="ezeefile_saas";'.'$clientKey='.'"'.$licenseKey.'";';
                                                file_put_contents($path_to_file, $file_contents);
                                                $subdomainName = $subDomain;
                                                $confname = "$subdomainName" . "." . proname . ".conf";
                                                $file = fopen($confname, "w");
                                                $content = "<VirtualHost *:80>

                                                            ServerName $FullSubDomain
                                                            ServerAlias www.$FullSubDomain
                                                            #Redirect permanent /  http://$FullSubDomain
                                                            ServerAdmin ezeefileadmin@cbsl-india.com
                                                            DocumentRoot $newClientDirectory
                                                            ErrorLog ${APACHE_LOG_DIR}/error.log
                                                            CustomLog ${APACHE_LOG_DIR}/access.log combined
                                                    </VirtualHost>
                                                    <IfModule mod_ssl.c>
                                                            <VirtualHost *:443>
                                                            ServerName $FullSubDomain
                                                            ServerAlias www.$FullSubDomain
                                                            ServerAdmin ezeefileadmin@cbsl-india.com
                                                            DocumentRoot $newClientDirectory

                                                            #   SSL Engine Switch:
                                                            #   Enable/Disable SSL for this virtual host.
                                                            SSLEngine on

                                                            #   A self-signed (snakeoil) certificate can be created by installing
                                                            #   the ssl-cert package. See
                                                            #   /usr/share/doc/apache2.2-common/README.Debian.gz for more info.
                                                            #   If both key and certificate are stored in the same file, only the
                                                            #   SSLCertificateFile directive is needed.
                                                            SSLCertificateFile /etc/apache2/ssl/ezeepea/45b90ab1c8461a7e.crt
                                                            SSLCertificateKeyFile /etc/apache2/ssl/ezeepeain.key
                                                    </VirtualHost>
                                                    </IfModule>
                                                    # vim: syntax=apache ts=4 sw=4 sts=4 sr noet";

                                                fwrite($file, $content);
                                                fclose($file);
                                                chmod("$subdomainName" . "." . proname . ".conf", 0755);
                                                exec("mv $subdomainName" . "." . proname . ".conf /etc/apache2/sites-available", $output);
                                                if ($output[0] == 0) {
                                                    exec("a2ensite $confname", $output1);
                                                   
                                                    if ($output1[0] == 0) {
                                                        //exec("/etc/init.d/apache2 restart 2>&1",$output2);
                                                        //var_dump($output2);
                                                        exec("sudo /etc/init.d/apache2 reload 2>&1", $output2);
                                                        
                                                        $qry_update_crm = mysqli_query($db_con, "update tbl_client_master set conf_file_name='$confname' where client_id='$lastinsertid'");
//                                                        require 'mail.php';
//                                                        $mail = mailClientCreate($email, $password, $projectName, $coustomer_id);
//                                                        if ($mail) {
//                                                            
//                                                        } else {
//
//                                                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Mail Not Sent");
//                                                        }
                                                    } else {

                                                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "failed client site url enabled" . $output1);
                                                    }
                                                } else {

                                                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "failed client site conf enable move" . $output);
                                                }
                                                //var_dump($output);*/
                                                //exec("/usr/sbin/apache2 reload 2>&1",$output2);
                                            } else {
                                                return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Bridge group to user first client  Problem" . mysqli_error($conn));
                                            }
                                        } else {
                                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Group First client Add Problem" . mysqli_error($conn));
                                        }


//                                   
                                    } else {
                                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Level Permission Problem-" . mysqli_error($conn));
                                    }
                                } else {
                                    return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Storage Level Problem" . mysqli_error($conn));
                                }
                            } else {
                                return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database Group To User  Master Problem" . mysqli_error($conn));
                            }
                        } else {
                            return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client database User Master Problem" . mysqli_error($conn));
                        }
                    } else {
                        return array("status" => True, "msg" => "Error creating Company", "db_name" => $ddb_name, "connect" => $conn, "dev_msg" => "Client User Role Creation Table" . mysqli_error($conn));
                    }
                } else {
                    return array("status" => True, "msg" => "Error creating Company", "connect" => $conn, "db_name" => $ddb_name, "dev_msg" => "Client master table failed");
                }
                mysqli_close($conn);
                //return TRUE;
            } else {
                return array("status" => True, "msg" => "Error creating Company", "dev_msg" => "Database Creation Failed");
            }
        } else {
            return array("status" => True, "msg" => "Error creating Connection", "dev_msg" => "Connection Failed");
        }
    } else {
        return array("status" => True, "msg" => "Error Fetch Table", "dev_msg" => "Master Database Table fetch Error");
    }
}
