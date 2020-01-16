<?php
include_once('connection.php');
session_start();

function getGenres() {
  global $conn;
  $query = "SELECT * FROM genres WHERE 1=1";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $genres;

}

function getAlbumByGenre($genre_id) {
  global $conn;
  $query = "SELECT * FROM albums AS al JOIN artists AS ar ON al.album_artist_id = ar.artist_id WHERE album_genre_id = :genre_id";
  $stmt = $conn->prepare($query);
  $stmt->execute([
    ':genre_id' => $genre_id
  ]);
  $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $albums;
}

function getArtists() {
  global $conn;
  $query = "SELECT * FROM artists WHERE 1=1";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $artists;
}

function getArtistById($artist_id) {
  global $conn;
  $query = "SELECT * FROM artists WHERE artist_id = :artist_id";
  $stmt = $conn->prepare($query);
  $stmt->execute([
      ':artist_id' => $artist_id
  ]);
  $artist = $stmt->fetch(PDO::FETCH_ASSOC);

  return $artist;
}

function getAlbumByArtist($artist_id) {
  global $conn;
  $query = "SELECT * FROM albums AS al JOIN artists AS ar ON al.album_artist_id = ar.artist_id WHERE album_artist_id = :artist_id";
  $stmt = $conn->prepare($query);
  $stmt->execute([
    ':artist_id' => $artist_id
  ]);
  $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $albums;
}

function getAlbums() {
  global $conn;
  $query = "SELECT * FROM albums AS a 
            JOIN artists AS ar ON a.album_artist_id = ar.artist_id 
            JOIN genres AS g ON a.album_genre_id = g.genre_id
            WHERE 1=1";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $albums;
}

function getAlbum($album_id) {
  global $conn;
  $query = "SELECT * FROM albums AS a 
            JOIN artists AS ar ON a.album_artist_id = ar.artist_id 
            JOIN genres AS g ON a.album_genre_id = g.genre_id
            WHERE a.album_id = :album_id";
  $stmt = $conn->prepare($query);
  $stmt->execute([
    ':album_id' => $album_id
  ]);
  $album = $stmt->fetch(PDO::FETCH_ASSOC);

  return $album;
}
// Build up an array for all the songs, with key value pairs for 'title', 'genre', 'album' and 'artist'

function generateRandomString() {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  $length = mt_rand(5,20);
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

/**
 * This function will be used to add notifications to the session where they will be stored for the user
 *
 * First the type is passed into the function, this can consist of error, success, warning etc
 * Then the msgKey is passed, this will be either card or users
 * Finally the message is passed in, this is the message that will display to the user
 * As $_SESSION is global, no return is needed
 */
function siteAddNotification($type, $msgKey, $msg) {
  $_SESSION[$msgKey][$type][] = $msg;
}
/**
 * This function will be used to integrate a notification into the html of a page
 *
 * The $pageType is passed in and will reflect either card or user, this will specify what key is needed in the session
 * Using the $pageType as the key will provide notifications that differ between the pages, so that you never have notifcations
 * relating to a user when you are creating cards and vice versa
 *
 * Inside the functions an array is declared that holds values for keys
 * These values are what is used to style the notification
 */
function outputNotifications($pageType) {
  $availableNotifications = [
    'success' => 'alert-success',
    'error' =>  'alert-danger',
    'warning' =>  'alert-warning'
  ];
  if (!empty($_SESSION[$pageType])) {
    foreach ($availableNotifications as $notification => $class) {
      if (!empty($_SESSION[$pageType][$notification])) {
        ?>
        <div class="alert <?=$class?>">
          <?= implode("<br>", $_SESSION[$pageType][$notification]) ?>
          <?php
          unset($_SESSION[$pageType][$notification]);
          ?>
        </div>
        <?php
      }
    }
  }
}

$songs = [];
for ($x = 0; $x <= 100; $x++) {
  // Generate 100 songs
  $songs += [
    [
      'title' => generateRandomString(),
      'id' => $x != 0 ? $x : 1,
      'genre_id' => mt_rand(3,8),
      'artist_id' => mt_rand(1,11),
      'album_id' => mt_rand(1,31)
    ],
  ];

}

