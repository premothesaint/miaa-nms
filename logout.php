<?php
session_start();
session_unset();
session_destroy();

// Prevent back button from accessing previous pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header("Location: index.php");
exit();
?>
