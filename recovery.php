<?php
require("config.php");

if ($_SESSION['username'])
{
    header("Location:user.php");
}

if ($_POST['sendMail'])
{
    if ($_POST['username'])
    {
        $username = $_POST['username'];
        $userData = $dbh->prepare("SELECT email FROM users WHERE email='$username'");
        $userData->execute();
        $data = $userData->fetch(PDO::FETCH_NUM);
        $numUsers = count($data[0]);
        $message = '';
        if ($numUsers != 1)
        {
            $message = "Denne e-mail findes desværre ikke i systemet";
        }
        else if ($numUsers == 1)
        {
            die("Du er blevet tilsendt en e-mail</br></br>"
                . "<a href='main.php'>Tilbage til forsiden</a>");
            // Send mail
        }
    }
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Genoprettelse af password</title>
    </head>
    <body>
        <h1>
            Genoprettelse af password
        </h1>
        <form method='post'>
            <table width='300'>
                <tr>
                    <td>
                        <b>
                            Indtast e-mail:
                        </b>
                    </td>
                    <td>
                        <input type='text' name='username'>
                    </td>
                </tr>
            </table>
            <button type='button' onclick=
                "window.location='main.php'">Tilbage</button>
            <input type='submit' name='sendMail' value='Send e-mail'>
        </form>
        <?php
        print $message;
        ?>
        
    </body>
</html>