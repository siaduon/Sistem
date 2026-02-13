<?php
require_once __DIR__ . '/../config/functions.php';
session_destroy();
header('Location: /elearning/auth/login.php');
exit;
