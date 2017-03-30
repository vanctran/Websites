<?php //  createuser.php

error_reporting(0);
require_once('secured/vt_login.php');
require_once('secured/encrypt.php');

$conn = new mysqli($hn, $un, $pw, $db);
if($conn->connect_error)  die($conn->connect_erorr);

if(isset($_POST['user']) && isset($_POST['pw']) && isset($_POST['rkey']))
{
    $username = mysql_entities_fix_string($conn, $_POST['user']);
    $password = mysql_entities_fix_string($conn, $_POST['pw']);
    $reg_key  = mysql_entities_fix_string($conn, $_POST['rkey']);

    $query = "SELECT * FROM regkeys where regkey='$reg_key'";
    $result = $conn->query($query);
    if(!$result)
        echo "Invalid registration key entered."; 
    elseif($result->num_rows)
    {
        $row = $result->fetch_array(MYSQLI_NUM);
        $result->close();

        if(!is_null($row[1]))
            echo "Registration key has already been used.";
        else
        {
            $token = hash('ripemd128', "$salt1$password$salt2");
            add_user($conn, $username, $token);
            echo "Registration is successful. Welcome $username!<br><br>";
            $query = 
                "UPDATE regkeys SET username='$username' WHERE regkey='$reg_key'";
            $conn->query($query);
        }
    }
}

echo <<<_END
<script defer src="https://code.getmdl.io/1.2.1/material.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://code.getmdl.io/1.2.1/material.indigo-red.min.css"/>
<link rel="stylesheet" href="css/signup.css">
<div class="mdl-layout mdl-js-layout mdl-color--grey-100">
  <main class="mdl-layout__content"> 
    <div class="mdl-card mdl-shadow--6dp">
      <div class="mdl-card__title mdl-color--primary mdl-color-text--white">
        <h2 class="mdl-card__title-text">Sign Up</h2>
      </div>

      <div class="mdl-card__supporting-text">
        <form action="signup.php" method="post">

          <div class="mdl-textfield mdl-js-textfield">
            <input class="mdl-textfield__input" type="text" id="username" name="user" maxlength="12">
            <label class="mdl-textfield__label" for="username">Username</label>
          </div>

          <div class="mdl-textfield mdl-js-textfield">
            <input class="mdl-textfield__input" type="password" id="userpass" name="pw" maxlength="12">
            <label class="mdl-textfield__label" for="userpass">Password</label>
          </div>

          <div class="mdl-textfield mdl-js-textfield">
            <input class="mdl-textfield__input" type="text" id="regkey" name="rkey">
            <label class="mdl-textfield__label" for="regkey">Registration Key</label>
          </div>

          <div class="mdl-card__actions"> 
            <input class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit" value="Sign Up">
          </div> 

        </form>
      </div>
    </div>
  </main>
</div>
_END;

$conn->close();

function add_user($conn, $un, $pw)
{
    $role = 'standard';
    $stmt = $conn->prepare('INSERT INTO users VALUES(?,?,?)');
    $stmt->bind_param('sss', $un, $pw, $role);
    $result = $stmt->execute();
    if(!$result) {
        echo "Username has already been taken.";
        die($conn->error());
    }
    $stmt->close();
}

function mysql_entities_fix_string($connection, $string)
{
    return htmlentities(mysql_fix_string($connection, $string));
}


function mysql_fix_string($connection, $string)
{
    if (get_magic_quotes_gpc())
        $string = stripslashes($string);
    return $connection->real_escape_string($string);
}
?>
