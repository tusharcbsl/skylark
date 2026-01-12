<?php
session_start();
$slid=trim($_GET['slid']);
$doc_ids=mysqli_real_escape_string($db_con,$_GET['doc_id']);
require_once './application/config/database.php';
$meta= mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slid'");
            while($rwMeta=mysqli_fetch_assoc($meta)){
                $metan= mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                $rwMetan= mysqli_fetch_assoc($metan);
                if(empty($metaName)){
                    $metaName='`'.$rwMetan['field_name'].'`';
                }else{
                    $metaName.=',`'.$rwMetan['field_name'].'`';
                }
            }
$exportData= mysqli_query($db_con, "select filename,$metaName,uploaded_by,dateposted from tbl_document_master where doc_id in($doc_ids) and doc_name='$slid'");
            //$fields = mysqli_num_fields ( $exportData );
            
            while($fields=mysqli_fetch_field($exportData))
            {
               $header1 .= $fields->name . "\t";
            }
            while( $row = mysqli_fetch_assoc( $exportData ) )
            {

                $line = '';
                foreach( $row as $key => $value )
                {   
            if ( ( !isset( $value ) ) || ( $value == "" ) || ($value==" ") || ($value==NULL))
                    {
                        $value = "--\t";
                    }
                    else
                    {
                        if($key=='uploaded_by'){
                        $dataOwner= mysqli_fetch_assoc(mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$value'"));
                        $name=$dataOwner['first_name'].' '.$dataOwner['last_name'];
                        if(( !isset( $name ) ) || ( $name == "" ) ){
                            $value = "\t";
                        }
                        else{
                            $value = str_replace( '"' , '""' , $name );
                            $value = '"' . $value. '"' . "\t";
                        }
                        }
                        else{
                            $value = str_replace( '"' , '""' , $value );
                            $value = '"' . $value. '"' . "\t";
                        }
                    }

                    $line .= $value;
                }
                $result1 .= trim( $line ) . "\n";
            }
            $result1 = str_replace( "\r" , "" , $result1 );

            if ( $result1 == "" )
            {
                //$result1 = "\nNo Record(s) Found!\n";                        
            }
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=export.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            print "$header1\n$result1";
?>
