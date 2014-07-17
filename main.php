<?php
require("config.php");
// If user has already logged in
if ($_SESSION['username'])
{
    //header("Location:user.php");
}
if ($_POST['login'])
{  
    if ($_POST['username'] && $_POST['password'])
    {
        // The entered username
        $username = $_POST['username'];
        
        // The entered password
        $password = sha1($_POST['password']);
        
        // Fetch the user info
        $userData = $dbh->prepare("SELECT * FROM users WHERE email='$username'");
        $userData->execute();
        $data = $userData->fetch(PDO::FETCH_NUM);
        
        // The password stored in the database
        $dbPass = $data[1];
        
        // The salt stored in the database
        $dbSalt = $data[6];
        
        // The latest login attempt
        $dbFirst_login = $data[7];
        
        // The amount of login attempts
        $dbLogin_attempts = $data[8];
        
        // The password to check with the database password
        $checkPass = md5($password . sha1($dbSalt));

        // Number of users with the entered name
        $numberOfUsers = count($data[0]);
        
        // The current time
        $curTime = time(); 
        
        // The number of login attempts before lockout
        $badNumber = 4;
        
        // Lockout time is 10 minutes, 60 seconds * 10 = 600 seconds
        $lockoutTime = 600;

        // If there are no users by the entered name
        if ($numberOfUsers == 0)
        {
            die("Undskyld. dette brugernavn findes ikke, "
                    . "<a href='main.php'>pr&oslashv igen</a>");
        }
        // If the user is locked out
        else if ($dbLogin_attempts >= $badNumber && 
                $curTime - $dbFirst_login < $lockoutTime)
        {
            die("Du har indtastet en forkert kode 5 gange i tr&aeligk,<br>og er "
                    . "desv&aeligrre lukket ude i 10 minutter fra dit f&oslashrste "
                    . "fors&oslashg.<br>"
                    . " <a href='logout.php'>Tilbage til forsiden</a>");
        }
        // If the entered password doesn't match the password in the database
        else if ($dbPass != $checkPass)
        {        
            // If the lockout time is exceeded the login attempts reset
            if ($curTime - $dbFirst_login > $lockoutTime)
            {
                $dbLogin_attempts = 0;
            }
            
            // If it is the first failed attempt
            if ($dbLogin_attempts == 0)
            {
                // Enters the time of the login attempt in the database
                $setTime = $dbh->prepare("UPDATE USERS SET first_login=$curTime "
                    . "WHERE email='$username'");
                $setTime->execute();
            }
            
            // Updates the amount of login-attempts in the database
            $dbLogin_attempts++;
            $setAttempts = $dbh->prepare("UPDATE USERS SET login_attempts=$dbLogin_attempts"
                    . " WHERE email='$username'");
            $setAttempts->execute();
            die ("Det indtastede password var desv&aeligrre forkert, "
                    . "<a href='main.php'>pr&oslashv igen</a>"
                    . "</br></br><a href='recovery.php'>Har du glemt din kode?</a>");
        }
        else
        {
            // Resets login-attempts
            if ($dbLogin_attempts > 0)
            {
                $dbLogin_attempts = 0;
                $setAttempts = $dbh->prepare("UPDATE USERS SET "
                    . "login_attempts='$dbLogin_attempts' WHERE email='$username'");
                $setAttempts->execute();
            }
            
            // Creates a new salt for the user on login
            $salt = md5(rand(). rand(). rand());
            $setSalt = $dbh->prepare("UPDATE users SET salt='$salt' "
                    . "WHERE email='$username'");
            $setSalt->execute();
            
            // Inserts a new password for the user;
            // The password concatinated with the new salt
            $insertPassword = md5($password . sha1($salt));
            $insertNewPassword = $dbh->prepare("UPDATE users "
                    . "SET password='$insertPassword' WHERE email='$username'");
            $insertNewPassword->execute();
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
                    <input name="username" type="text" placeholder="Email@eksempel.dk">
                </td>
            </tr>
            <tr>
                <td>
                    <B>
                    Password: 
                    </B>
                </td>
                <td>
                    <input name="password" type="password" placeholder="Minkode123">
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
