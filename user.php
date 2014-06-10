<?php
require("config.php");
// Check if the user was logged in or redirect to login page
if ($_SESSION['username'])
{
    $username = $_SESSION['username'];
}
else
{
    die("Du har ikke adgang til denne side "
            . "<a href='main.php'>log ind</a> først");
}
// get the userdata
$userData = $dbh->prepare("SELECT * FROM users WHERE email='$username'");
$userData->execute();
$data = $userData->fetch(PDO::FETCH_NUM);
$firstName  = $data[2];
$lastName   = $data[3];
$isAdmin    = $data[5];

if ($_POST['logout'])
{
    header("Location:logout.php");
    die();
}
if ($_POST['changePass'])
{
    header("Location:changepass.php");
    die();
}
if ($_POST['check'])
{
    header("Location:queue.php");
    die();
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Bruger</title>
    </head>
    <body>
        Du er nu logget ind som: 
        <b>
            <?php print $firstName . " " . $lastName; ?>
            </br>
            </br>
        </b>
        <form name="menu" method="post">
        <table width="300">
            <tr>
                <td>
                    <input type="submit" name="check" value="Se plads på ventelisten">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="changePass" value="Skift kode">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="logout" value="Log out">
                </td>
            </tr>
    </form>
    </body>
</html>
