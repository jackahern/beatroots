<?php

function getGenres() {
  global $conn;
  $query = "SELECT * FROM genres WHERE 1=1";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $genres;

}

function getGenreById($genre_id) {
  global $conn;
  $query = "SELECT * FROM genres WHERE genre_id = :genre_id";
  $stmt = $conn->prepare($query);
  $stmt->execute([
      ':genre_id' => $genre_id
  ]);
  $genre = $stmt->fetch(PDO::FETCH_ASSOC);

  return $genre;

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

/**
 * Get all song data with no joins to other tables
 */
function getSongsQuickly() {
  global $conn;
  $query = "SELECT * FROM songs WHERE 1=1";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $songs;
}

function getSongsWithJoinData() {
  global $conn;
  $query = "SELECT * FROM songs AS s
              JOIN artists AS ar ON s.song_artist_id = ar.artist_id
              JOIN genres AS g ON s.song_genre_id = g.genre_id
              JOIN albums AS al ON s.song_album_id = al.album_id
              WHERE s.song_album_id IS NOT NULL";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $query = "SELECT * FROM songs AS s
            JOIN artists AS ar ON s.song_artist_id = ar.artist_id
            JOIN genres AS g ON s.song_genre_id = g.genre_id
            WHERE s.song_album_id IS NULL";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $singles = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $songs = array_merge($singles,$songs);

  return $songs;
}

function getSongsAsSingles($artist_id = FALSE) {
  global $conn;
  $query = "SELECT * FROM songs AS s
            JOIN artists AS ar ON s.song_artist_id = ar.artist_id
            JOIN genres AS g ON s.song_genre_id = g.genre_id
            WHERE s.song_album_id IS NULL";
  if ($artist_id) {
    $query .= " AND s.song_artist_id = :artist_id";
  }
  $stmt = $conn->prepare($query);
  if ($artist_id) {
    $stmt->execute([
      ':artist_id' => $artist_id
    ]);
  } else {
    $stmt->execute();
  }
  $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $songs;
}

function getSong($song_id) {
  global $conn;
  $query = "SELECT * FROM songs AS s
              JOIN artists AS ar ON s.song_artist_id = ar.artist_id
              JOIN genres AS g ON s.song_genre_id = g.genre_id
              LEFT JOIN albums AS al ON s.song_album_id = al.album_id
              WHERE s.song_id = :song_id";
  $stmt = $conn->prepare($query);
  $stmt->execute([
    ':song_id' => $song_id
  ]);
  $song = $stmt->fetch(PDO::FETCH_ASSOC);

  return $song;
}

function getSongsInAlbum($album_id) {
  global $conn;
  $query = "SELECT * FROM songs AS s
              JOIN albums AS al ON s.song_album_id = al.album_id
              WHERE al.album_id = :album_id";
  $stmt = $conn->prepare($query);
  $stmt->execute([
    ':album_id' => $album_id
  ]);
  $songs = $stmt->fetch(PDO::FETCH_ASSOC);

  return $songs;
}

function getPlaylists() {
  global $conn;
  $query = "SELECT * FROM playlists WHERE 1=1";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $playlists;
}

function getPlaylistById($playlist_id) {
  global $conn;
  $query = "SELECT * FROM playlists
            WHERE playlist_id = :playlist_id";
  $stmt = $conn->prepare($query);
  $stmt->execute([
    ':playlist_id' => $playlist_id
  ]);
  $playlist = $stmt->fetch(PDO::FETCH_ASSOC);

  return $playlist;
}

function getSongsInPlaylist($playlist_id) {
  global $conn;
  $query = "SELECT * FROM playlist_assignment as pa
            JOIN playlists AS p ON p.playlist_id = pa.playlist_id
            WHERE pa.playlist_id = :playlist_id";
  $stmt = $conn->prepare($query);
  $stmt->execute([
    ':playlist_id' => $playlist_id
  ]);
  $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $songs;
}

function getSongTitle($song_id) {
    global $conn;
    $sql = "SELECT song_title FROM songs WHERE song_id = :song_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':song_id' => $song_id
    ]);
    $song = $stmt->fetch(PDO::FETCH_ASSOC);
    return $song;
}

function searchDatabase($criteria, $keywords) {
    global $conn;
    if ($criteria == 'song') {
        $sql = "SELECT * FROM songs WHERE song_title LIKE :search_keywords";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':search_keywords' => '%' . $keywords . '%'
        ]);
        // matching songs
        $output = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $output;
    }
    else {
    // search criteria must equal 'album'
      $sql = "SELECT * FROM albums WHERE album_title LIKE :search_keywords";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':search_keywords' => '%' . $keywords . '%'
      ]);
      // matching albums
      $output = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $output;
    }
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
        <div class="w-50 alert <?=$class?>">
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