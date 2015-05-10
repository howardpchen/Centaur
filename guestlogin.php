<?php
session_start();
if (isset($_GET['moduleid'])) {
    $_SESSION['username'] = 'guest';
    header("location:module.php?moduleid=" . $_GET['moduleid']);
}
else  {
    header("location:/centaur/");    
}

?>
