<?php
// This config file is going to be required at the top of almost every page to allow every page to use the global functions
// So, include the connection to the database
require_once('connection.php');
// The include the global functions
require_once('resources/lib/functions.php');
// Ignore notices and warnings so end users cannot see them on front end
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
// Start a session, page titles, descriptions and notifications/alerts will all be using session
session_start();
