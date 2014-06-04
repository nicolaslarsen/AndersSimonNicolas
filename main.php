<?php
require("config.php");
// If user has already logged in
if ($_SESSION['username'])
{
    header("Location:user.php");
}

if ($_POST['login'])
{            
    if ($_POST['username'] && $_POST['password'])
    {
        // The entered username
        $username = $_POST['username'];
        
        // The entered password
        $password = md5($_POST['password']);
        
        // Fetch the user info
        $userData = $dbh->prepare("SELECT * FROM users WHERE email='$username'");
        $userData->execute();
        $data = $userData->fetch(PDO::FETCH_NUM);
        
        // The password stored in the database
        $dbPass = $data[1];
        
        // Number of users with the entered name
        $numberOfUsers = count($data[0]);
        
        // If there is no users by the entered name
        if ($numberOfUsers == 0)
        {
            die("Undskyld. dette brugernavn findes ikke, "
                    . "<a href='main.php'>pr&oslashv igen</a>");
        }
        // If the entered password doesn't match the password in the database
        else if ($dbPass != $password)
        {
            die ("Det indtastede password var desv&aeligrre forkert, "
                    . "<a href='main.php'>pr&oslashv igen</a>"
                    . "</br></br><a href='recovery.php'>Har du glemt din kode?</a>");
        }
        else
        {
            $salt = md5(rand(). rand(). rand());
            $setSalt = $dbh->prepare("UPDATE users SET salt='$salt' "
                    . "WHERE email='$username'");
            $setSalt->execute();
            $_SESSION['username']=$username;
            $_SESSION['salt']=$salt;
            header("Location:user.php");
        }    
    }   
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Login</title>
    </head>
    <body>
        <h1>
            Login
        </h1>
        <form name="login" method="post">
        <table width="300">
            <tr>
                <td>
                    <B>
                        E-mail: 
                    </B>
                </td>
                <td>
                    <input name="username" type="text">
                </td>
            </tr>
            <tr>
                <td>
                    <B>
                    Password: 
                    </B>
                </td>
                <td>
                    <input name="password" type="password">
                </td>
            </tr>
        </table>
            </br>
            <button type="button" onclick=
                "window.location='recovery.php'">Glemt kode</button>
            <input type="submit" name="login" value="Login">
            </br></br>
        <a href="application.php">Ansøg om plads på ventelisten</a>    
        </form>
    </body>
</html>
