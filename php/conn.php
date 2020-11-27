<?php

function getConn() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    
    // Create connection
    $conn = mysqli_connect($servername, $username, $password);
    
    // Check connection
    if (! $conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

function runQuery($query, $cols) {
    $conn = getConn();
    $result = mysqli_query($conn, $query);
    if (is_bool($result) && (mysqli_num_rows($result) == 0)) {
        // print($error_msg, "Query ERROR: Failed to get summary data<br>" . __FILE__ ." line:". __LINE__ );
    }
    
    $values = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $entry = array();
        foreach($cols as $k => $v) {
            array_push($entry, strval($row[$v]));
        }
        array_push($values, $entry);
    }
    mysqli_close($conn);
    return $values;
}

?>