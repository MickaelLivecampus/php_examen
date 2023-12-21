<?php
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
}

function verifyCSRFToken($csrfToken) {
    return isset($_SESSION['csrf_token']) && $csrfToken === $_SESSION['csrf_token'];
}

?>