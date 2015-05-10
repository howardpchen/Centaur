<?php

if (isset($_SESSION['username']) && $_SESSION['username'] == "notrack")  {
    include "footer_nologin.php";
} else  {
    include "footer_login.php";
}
?>

