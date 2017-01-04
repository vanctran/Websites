<?php
    require_once '../login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if($con->connect_error) die($conn->connect_error);

    $query = "SELECT * FROM ip ORDER BY time DESC";
    $result = $conn->query($query);
    if(!$result)    die("Database access failed: " . $conn->error);

    $rows = $result->num_rows;

    echo "<table><tr><th>Remote IP</th><th>Forwarded IP</th><th>Time</th><th>DESCRIPTION</th></tr>";
    for($j = 0; $j < $rows; ++$j) {
        $result->data_seek($j);
        $row = $result->fetch_array(MYSQLI_NUM);
        echo "<tr>";
        
        for($k = 1; $k < 5; ++$k) {
            echo "<td>$row[$k]</td>";
        }
        echo "</tr>";
    }

    echo"</table>";
    $result->close();
    $conn->close();
?>
