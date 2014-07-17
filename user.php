<?php
require("config.php");
// Check if the user was logged in or redirect to login page
if ($_SESSION['username'] && $_SESSION['salt'])
{
    $username = $_SESSION['username'];
    $salt     = $_SESSION['salt'];
}
else
{
    die("Du har ikke adgang til denne side "
            . "<a href='logout.php'>log ind</a> f&oslashrst");
}
// get the userdata
$userData = $dbh->prepare("SELECT * FROM users WHERE email='$username'");
$userData->execute();
$data = $userData->fetch(PDO::FETCH_NUM);
$firstName  = $data[2];
$lastName   = $data[3];
$isAdmin    = $data[5];
$dbSalt     = $data[6];

// Check if the salt stored and the database matches the salt of the session.
// If it doesn't, redirect the user to logout.php
if ($salt != $dbSalt)
{
    die("Du er desv&aeligrre blevet logget ud, "
            . "<a href='logout.php'>log ind</a> igen");
}
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
if ($_POST['queue'])
{
    header("Location:lists.php");
    die();
}
if ($_POST['createMessage'])
{
    header("Location:messages.php");
    die();
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Bruger</title>
    </head>
    <body>
        <?php
        // Execute the following html if user is not an admin
        if ($isAdmin == 'n')
        {
        ?>
        Du er nu logget ind som : 
        <b>
            <?php print $firstName . " " . $lastName; ?>
            </br>
            </br>
        </b>
        <form name="menu" method="post">
            <table style='float: left;' width="300">
                <tr>
                    <td>
                        <input type="submit" name="check" 
                               value="Se plads på ventelisten">
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
            </table>
            <table width="300" style='float: center;' border='3'>
                <th>
                    Beskeder
                </th>
                <?php
                // Create table for messages
                foreach($dbh->query("SELECT * FROM Messages "
                        . "ORDER BY id DESC") as $row)
                {
                    echo
                    "<tr>"
                .       "<td>"
                .           "<br>" . $row[0] . "<br><br>"
                .       "</td>"
                .   "</tr>";
                }
                ?>
            </table>
        </form>
        <?php 
        }
        // Execute the following html if user is admin
        else if ($isAdmin == 'y')
        {
        ?>
        <h1>
            Administrator
        </h1>
        <form name="menu" method="post">
            <table width="300" style="float: left">
                <tr>
                    <td>
                        <input type="submit" name="queue" value="Se ventelister">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="createMessage" value="Redigér nyheder"
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
            </table>
        </form>
            <table width='300' style='float: center;' border='3'>
                <th>
                    Beskeder
                </th>
                <?php
                // Create table for messages
                foreach($dbh->query("SELECT * FROM Messages "
                        . "ORDER BY id DESC") as $row)
                {
                    echo
                    "<tr>"
                .       "<td>"
                .           "<br>" . $row[0] . "<br><br>"
                .       "</td>"
                .   "</tr>";
                }
                ?>
            </table>
        <?php
        }
        ?>
    </body>
</html>
