<?php
define('BASE_URL', '../');
require_once '../includes/config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php?msg=Accès réservé aux administrateurs');
}
