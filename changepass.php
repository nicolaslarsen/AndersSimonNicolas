<?php
require("config.php");
// Check if the user was logged in or redirect to the login page
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
$dbPass     = $data[1];
$salt       = $data[6];

if ($_SESSION['salt'])
{
    if ($salt = $_SESSION['salt'])
    {
        // Do nothing
    }
    else
    {
        die("Du er desværre blevet logget ud og "
                . "har derfor ikke adgang til denne side, "
                . "<a href='main.php'>log ind</a>");
    }
}


if ($_POST['oldPassword'] && $_POST['newPassword'] && $_POST['newPassword_re'])
{
    $oldPass    = md5($_POST['oldPassword']);
    $message    = '';

    if ($oldPass == $dbPass)
    {
        $newPass    = md5($_POST['newPassword']);
        $newPassRe  = md5($_POST['newPassword_re']);
        if ($newPass == $newPassRe)
        {
            $changePassword = $dbh->prepare("UPDATE users "
                    . "SET password='$newPass' WHERE email='$username'");
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
