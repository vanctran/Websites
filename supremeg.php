<?php   //supremeg.php
require_once('secured/login.php');
//Establish connection with MySQL database using credentials from external file.
    echo <<<_END
<script defer src="https://code.getmdl.io/1.2.1/material.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script defer src="js/supremeg.js"></script>
<script src="js/JsBarcode.all.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://code.getmdl.io/1.2.1/material.indigo-red.min.css" />
<link rel="stylesheet" href="css/supremeg.css">
_END;

session_start();
ini_set('session.gc_maxlifetime', 30 * 3);

if(!isset($_SESSION['username'])
    && !isset($_SESSION['password'])) {
    header('HTTP/1.0 401 Unauthorized');
    //header("Location: login.php");
    exit();
}

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error)   die($conn->connect_error);

if (isset($_POST['delete']) 
    && isset($_POST['id']) 
    && isset($_POST['code'])
    && isset($_POST['store']))
{
    $id     = get_post($conn, 'id');
    $code   = get_post($conn, 'code');
    $store  = get_post($conn, 'store');
    /*
    $query  = "DELETE FROM " . $store
    . " WHERE id='" . $id . "'";
     */
    $query = "DELETE FROM " . $store . " WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    //$result = $conn->query($query);
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $forwardip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    $time = date("Y-m-d H:i:s");
    $message = "Removed code: " . $code . " from $store";
    $query = "INSERT INTO ip VALUES" .
        "(NULL, '$remoteip', '$forwardip', '$time','$message')";
    $conn->query($query);
    $stmt->close(); 
    //$result->close();
}

//Logout form and option menu
echo <<<_END

<div align="center">

    <h1>SupremeG Beta 1.0</h1>

    <form action="login.php" method="post">
    <input type="hidden" name="logout" value="true">
    <input class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent mdl-color--red" type="submit" value="Logout"></form>


    <form action="supremeg.php" method="post">
    <h5><b>Select Store</b></h5>
    <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="store-1">
        <input type="radio" id="store-1" class="mdl-radio__button" name="store" value="officeG" required checked>
        <span class="mdl-radio__label">Office Depot --------Balance Checker &#10060;</span>
    </label>
    <br>

    <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="store-2">
        <input type="radio" id="store-2" class="mdl-radio__button" name="store" value="finishG">
        <span class="mdl-radio__label">Finish Line --------Balance Checker &#10060;</span>
    </label>
    <br>

    <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="store-3"> 
        <input type="radio" id="store-3" class="mdl-radio__button" name="store" value="paneraG">
        <span class="mdl-radio__label">Panera Bread --------Balance Checker &#9989;</span>
    </label>
    <br>

    <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="store-4">
        <input type="radio" id="store-4" class="mdl-radio__button" name="store" value="noodlesG">
        <span class="mdl-radio__label">Noodles &amp; Company --------Balance Checker &#10060;</span>
    </label>
    <br>
    <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="store-5">
        <input type="radio" id="store-5" class="mdl-radio__button" name="store" value="tgiFridayG">
        <span class="mdl-radio__label">TGI Friday's --------Balance Checker &#9989;</span>
    </label>
    <br>

    <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="store-6">
        <input type="radio" id="store-6" class="mdl-radio__button" name="store" value="crackingG">
        <span class="mdl-radio__label">Cracker Barrel --------Balance Checker &#10060;</span>
    </label> 
    <br>

    <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="store-7">
        <input type="radio" id="store-7" class="mdl-radio__button" name="store" value="robertG">
        <span class="mdl-radio__label">ROBERTWAYNE --------Balance Checker &#10060;</span>
    </label> 
    <br>

    <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="store-8">
        <input type="radio" id="store-8" class="mdl-radio__button" name="store" value="dennyG">
        <span class="mdl-radio__label">Denny's --------Balance Checker &#9989;</span>
    </label>
    <br>

    <h5><b>Sort By</b></h5>

    <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="sort-1">
        <input type="radio" class="mdl-radio__button" id="sort-1" name="sort_by" value="amount" required checked>
        <span class="mdl-radio__label">Amount</span>
    </label>
    <br>
    <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="sort-2">
        <input type="radio" class="mdl-radio__button" id="sort-2" name="sort_by" value="created">
        <span class="mdl-radio__label">Date</span>
    </label>
    <br>
    <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="sort-3">
        <input type="radio" class="mdl-radio__button" id="sort-3" name="sort_by" value="code">
        <span class="mdl-radio__label">Code</span>
    </label>
    <br>
    <h5><b>Set Minimum Amount</b><h5>
    <div class="mdl-textfield mdl-js-textfield" align="center">
        <input id="amount" class="mdl-textfield__input" style="text-align:right;" type="text" pattern="-?[0-9]*(\.[0-9]+)?" name="minimum_value" value="20" min="0" required>
        <label class="mdl-textfield__label" style="text-align:right;" for="amount">Minimum Amount</label>
        <span class="mdl-textfield__error">Input is not a number!</span>
    </div>
    <input class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" mdl-color--green type="submit" value="Sort"></form>
</div>

<dialog id="dialog-box" class="mdl-dialog">
    <h4 class="mdl-dialog__title"></h4>
    <div class="mdl-dialog__content" align="center">
        <svg id="barcode"></svg>
    </div>
    <div class="mdl-dialog__actions">
        <button type="button" class="mdl-button close mdl-color--grey-300">Close</button>
    </div>
</dialog>

<div id="snackbar" class="mdl-js-snackbar mdl-snackbar">
    <div class="mdl-snackbar__text"></div>
        <button class="mdl-snackbar__action" type="button"></button?>
    </div>
</div>
_END;

if(isset($_POST['sort_by'])
    && isset($_POST['minimum_value'])
    && isset($_POST['store'])) 
{
    $min_value = 
        isset($_POST['minimum_value'])
        ? get_post($conn, 'minimum_value')
        : 1;
    $sort_by = get_post($conn, 'sort_by');
    $store = get_post($conn, 'store');
    $query = "SELECT * FROM " . $store . " WHERE amount >= ? ORDER BY amount DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $min_value);
    $stmt->execute();
    $result = $stmt->get_result(); 
    if (!$result)
        die ("Database access failed: " . $conn->error);
    
    $rows = $result->num_rows;

    for ($j = 0; $j < $rows; ++$j) 
    {
        $result->data_seek($j);
        $row = $result->fetch_array(MYSQLI_NUM);

        echo <<<_END
        <div align="center">
            <p>Code: $row[1] | Amount: $$row[2] | Date: $row[3]</p>
            <form action="supremeg.php" method="post">
            <input type="hidden" name="delete" value="yes">
            <input type="hidden" name="code" value="$row[1]">
            <input type="hidden" name="id" value="$row[0]">
            <input type="hidden" name="store" value="$store">
            <input class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent mdl-color--red" type="submit" value="Remove Code">
            <button type="button" name="balance-button" class="balance-button mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent mdl-color--indigo" value="$row[1]">Check Balance</button>
            <button type="button" name="barcode-button" class="barcode-button mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent mdl-color--purple" value="$row[1]">Display Barcode</button>
            </form>
        </div>
_END;
    }

    $result->close();
    $stmt->close();
}

$conn->close();

function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}

?>
