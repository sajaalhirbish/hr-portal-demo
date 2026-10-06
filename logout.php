<?php
session_start();
session_destroy();   // forget the logged-in user
header('Location: login.php');
exit;