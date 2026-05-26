<?php

require_once __DIR__ . '/includes/common.php';

session_unset();
session_destroy();

session_start();
set_flash('Logged out.');
redirect_to('login.php');
