<?php
session_start();
unset($_SESSION['show_qr_modal']);
unset($_SESSION['qr_base64']);
unset($_SESSION['new_username']);
echo 'Session cleared';
?>
