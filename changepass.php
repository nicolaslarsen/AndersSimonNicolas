<?php
require("config.php");
// Check if the user was logged in or redirect to the login page
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
$dbPass     = $data[1];
$dbSalt     = $data[6];

// Check if the salt stored and the database matches the salt of the session.
// If it doesn't, redirect the user to logout.php
if ($salt != $dbSalt)
{
    die("Du er desv&aeligrre blevet logget ud, "
            . "<a href='logout.php'>log ind</a> igen");
}

if ($_POST['oldPassword'] && $_POST['newPassword'] && $_POST['newPassword_re'])
{
    $oldPass    = sha1($_POST['oldPassword']);
    // The password to be compared to the one in the database
    $checkPass  = md5($oldPass . sha1($dbSalt));
    $message    = '';

    if ($checkPass == $dbPass)
    {
        $newPass    = sha1($_POST['newPassword']);
        $newPassRe  = sha1($_POST['newPassword_re']);
        if ($newPass == $newPassRe)
        {
            // The password to be inserted in the database
            $insertPassword = md5($newPass . sha1($dbSalt));
            $changePassword = $dbh->prepare("UPDATE users "
                    . "SET password='$insertPassword' WHERE email='$username'");
            $changePassword->execute();
            die("Passwordet blev skiftet, <a href='user.php'>"
                    . "g&aring tilbage</a>");
        }
        else
        {
            $message = "*De to indtastede password var desværre ikke ens, prøv igen";
        }
    }
    else
    {
        $message = "*Dit password var desværre ikke korrekt, prøv igen";
    }
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Skift kode</title>
    </head>
    <body>
        <h1>
            Skift kode
        </h1>
        <form method="post">
            <table width="380">
                <tr>
                    <td>
                        <b>
                            Indtast dit gamle password:
                        </b>
                    </td>
                    <td>
                        <input type="password" name="oldPassword">
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Indtast dit nye password:
                        </b>
                    </td>
                    <td>
                        <input type="password" name="newPassword">
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Gentag dit nye password:
                        </b>
                    </td>
                    <td>
                        <input type="password" name="newPassword_re">
                    </td>
                </tr>
            </table>
            </br>
            <button type="button" name="cancel" onclick=
                "window.location='user.php'">Annullér</button>
            <input type="submit" name="changePass" value="Skift kode">
        </form>
        <?php
        print $message;
        ?>
    </body>
</html>
