<?php
require("config.php");
session_destroy();
header("Location:main.php");
?>