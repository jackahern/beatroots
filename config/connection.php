<?php
// Use this file to make a connection with the database, use PDO
$host = '127.0.0.1';
$dbname = 'jack_music_player';
$user = 'root';
$pass = 'root';

try {
  $conn = new PDO('mysql:host='. $host . ';dbname=' . $dbname, $user, $pass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
