<?php
require_once __DIR__.'/inc/bootstrap.php';
header('Location: '.(logged_in() ? 'dashboard.php' : 'welcome.php')); exit;
