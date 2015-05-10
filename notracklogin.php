<?php
session_start();
if (isset($_GET['moduleid'])) {
    $_SESSION['username'] = 'notrack';
    header("location:module.php?moduleid=" . $_GET['moduleid']);
}
else  {
    header("location:/centaur/");    
}

?>
