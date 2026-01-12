<?php

error_reporting(E_ALL);

class MatchTable {

    var $_table_one_name;
    var $_table_two_name;
    var $_table_one_db_user;
    var $_table_one_db_pass;
    var $_table_one_db_host;
    var $_table_one_db_name;
    var $_table_two_db_user;
    var $_table_two_db_pass;
    var $_table_two_db_host;
    var $_table_two_db_name;
    var $_table_one_columns = array();
    var $_table_two_columns = array();
    var $_table_one_types = array();
    var $_table_two_types = array();
    var $_table_null_values = array();
    var $_table_key_values = array();
    var $_table_default_values = array();
    var $_table_one_link;
    var $_table_two_link;
    var $_log = "";

    function matchTables($table1, $table2) {
        $this->_table_one_name = $table1;
        $this->_table_two_name = $table2;

        if (isset($this->_table_one_db_pass)) {
            $this->db_connect('ONE');
        }
        list($this->_table_one_columns, $this->_table_one_types, $this->_table_null_values, $this->_table_key_values, $this->_table_default_values) = $this->getColumns($this->_table_one_link, $this->_table_one_name);

        if (isset($this->_table_two_db_pass)) {
            $this->db_connect('TWO');
        }
        list($this->_table_two_columns, $this->_table_two_types) = $this->getColumns($this->_table_two_link, $this->_table_two_name);

        $this->addAdditionalColumns();
    }

    function setTableOneConnection($host, $user, $pass, $name) {
        $this->_table_one_db_host = $host;
        $this->_table_one_db_user = $user;
        $this->_table_one_db_pass = $pass;
        $this->_table_one_db_name = $name;
    }

    function setTableTwoConnection($host, $user, $pass, $name) {
        $this->_table_two_db_host = $host;
        $this->_table_two_db_user = $user;
        echo $this->_table_two_db_pass = $pass;
        $this->_table_two_db_name = $name;
    }

    function db_connect($table) {
        switch (strtoupper($table)) {
            case 'ONE':
                $host = $this->_table_one_db_host;
                $user = $this->_table_one_db_user;
                $pass = $this->_table_one_db_pass;
                $name = $this->_table_one_db_name;
                $link = $this->_table_one_link = mysqli_connect($host, $user, $pass, $name);
                // mysqli_select_db($name) or die(mysqli_error());
                break;
            case 'TWO';
                echo $host = $this->_table_two_db_host;
                echo $user = $this->_table_two_db_user;
                echo $pass = $this->_table_two_db_pass;
                echo $name = $this->_table_two_db_name;
                $link = $this->_table_two_link = mysqli_connect($host, $user, $pass, $name);
                //mysqli_select_db($name) or die(mysqli_error());
                break;
            default:
                die('Improper parameter in MatchTable->db_connect() expecting "one" or "two".');
                break;
        }
        if (!$link) {
            die('Could not connect: ' . mysqli_error());
        }
    }

    function getColumns($link, $table_name) {
        $columns = array();
        $types = array();
        $nulls = array();
        $keys = array();
        $defaults = array();
        $qry = 'SHOW COLUMNS FROM ' . $table_name;
        $result = mysqli_query($link, $qry) or die(mysqli_error());
        while ($row = mysqli_fetch_assoc($result)) {
            $field = $row['Field'];
            $type = $row['Type'];
            $null = $row['Null'];
            $key = $row['Key'];
            $default = $row['Default'];
            /*
              $column = array('Field' => $field, 'Type' => $type);
              array_push($columns, $column);
             */
            $types[$field] = $type;
            $nulls[$field] = $null;
            $keys[$field] = $key;
            $defaults[$field] = $default;
            array_push($columns, $field);
        }
        $arr = array($columns, $types, $nulls, $keys, $defaults);
        return $arr;
    }

    function addAdditionalColumns() {
        $resultCommon = array_intersect($this->_table_one_columns, $this->_table_two_columns);
        $additionalField = array_diff($this->_table_one_columns, $this->_table_two_columns);
        $qry = '';

        foreach ($this->_table_one_columns as $field) {
            $addVal = '';
            if (!empty($this->_table_default_values[$field])) {
                $addVal .= " DEFAULT " . $this->_table_default_values[$field];
            }
            if (!empty($this->_table_null_values[$field])) {
                if ($this->_table_null_values[$field] == "NO") {
                    $addVal .= " NOT NULL";
                }
                if ($this->_table_null_values[$field] == "YES") {

                    $addVal .= " NULL";
                }
            }
            if (in_array($field, $resultCommon)) {
                $qry = 'ALTER TABLE ' . $this->_table_two_name . ' MODIFY  ' . $field . ' ' . $this->_table_one_types[$field] . $addVal . '; ';
            } elseif (in_array($field, $additionalField)) {
                $qry = 'ALTER TABLE ' . $this->_table_two_name . ' ADD ' . $field . ' ' . $this->_table_one_types[$field] . $addVal . '; ';
            } else {
                
            }
            $qryGen = mysqli_query($this->_table_two_link, $qry);
            if ($qryGen) {
                $this->_log .= $qry . "-run" . "\n";
            } else {
                $this->_log .= $qry . "-fail-" . mysqli_error($this->_table_two_link) . "\n";
            }
        }
        $this->_tname['col_update'] = $this->_field;
        $this->_log .= "\n";
    }

    /*
     * Fetch all table From master db and support db
     */

    function fetchTablesCols() {

        $this->db_connect('ONE');
        $this->db_connect('TWO');
        $mainDbTables = array();
        $supportDbTables = array();
        $fetchMainColQry = mysqli_query($this->_table_one_link, "show tables");
        $fetchSupportColQry = mysqli_query($this->_table_two_link, "show tables");
        if ((mysqli_num_rows($fetchMainColQry) > 0) && (mysqli_num_rows($fetchSupportColQry) > 0)) {
            while ($fetchAllColsMain = mysqli_fetch_array($fetchMainColQry)) {
                array_push($mainDbTables, $fetchAllColsMain['0']);
            }

            while ($fetchAllColsSupport = mysqli_fetch_array($fetchSupportColQry)) {
                array_push($supportDbTables, $fetchAllColsSupport['0']);
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

            $newTablesAdd = array_diff($mainDbTables, $supportDbTables);
            for ($index = 0; $index < count($newTablesAdd); $index++) {
                $newTableName = $newTablesAdd[$index];
                $fetchTableQry = mysqli_query($this->_table_one_link, "SHOW CREATE TABLE $newTableName");
                if ($fetchTableQry) {
                    $fetchA = mysqli_fetch_assoc($fetchTableQry);
                    $createTable = $fetchA['Create Table'];
                    $createNewTable = mysqli_query($this->_table_two_link, "$createTable");
                    if ($createNewTable) {
                        $this->_log .= $createTable . "-run \n";
                    } else {
                        $this->_log .= $createTable . "-fail--" . mysqli_error($this->_table_two_link) . "\n";
                    }
                }
            }
            $oldTablesExists = array_intersect($mainDbTables, $supportDbTables);
            $oldTablesExists = implode(",", $oldTablesExists);
            $oldTablesExists = explode(",", $oldTablesExists);
            for ($i = 0; $i < count($oldTablesExists); $i++) {
                $this->_tname['table_name'] = $this->_table_two_name;
                $this->matchTables($oldTablesExists[$i], $oldTablesExists[$i]);
            }
        } else {
            $this->_log .= "Fail";
            exit();
        }
        $myfile = fopen("dblog.txt", "a");
        fwrite($myfile, $date . "\n====================================================================================================\n");
        fwrite($myfile, $this->_log);
        fclose($myfile);
        return TRUE;
    }

    /**
     * End of Class
     */
}
