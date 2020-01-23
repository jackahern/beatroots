<?php

/**
 * This function is used to get everything from the genres table
 *
 * @return array
 */
function getGenres() {
  global $conn;
  $query = "SELECT * FROM genres WHERE 1=1";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $genres;

}

/**
 * When a specific genre is needed, potentially for editing, deleting or assigning
 * this function can be used, with the genre_id parameter to get the specific genre
 * from the genres table
 *
 * @param $genre_id
 * @return mixed
 */
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

/**
 * All albums sit inside a genre, when looking on the genres page for an album in that genre
 * use this function to lists the albums that relate to the genre
 *
 * @param $genre_id
 * @return array
 */
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

/**
 * Lists everything from the artists table in the database in an array
 *
 * @return array
 */
function getArtists() {
  global $conn;
  $query = "SELECT * FROM artists WHERE 1=1";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $artists;
}

/**
 * Finds a specific artist in the artists table in the database using the artist_id param
 * that is passed in when it is called. This would be used when editing, deleting or assigning
 * the artist
 *
 * @param $artist_id
 * @return mixed
 */
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

/**
 * On the artists page, under their names, are there albums and singles, listed.
 * Use this function to join the albums table with the artists table where the artist
 * of the album is equal to the artist_id that is passed in as a param
 *
 * @param $artist_id
 * @return array
 */
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

/**
 * Use this function to get all album data, including the data from the artists and genres tables.
 * Doing this allows us to use fields like 'artist_name' and 'genre_name'
 *
 * @return array
 */
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


/**
 * Gets a specific album from the albums table using an ID that is passed as a param.
 * Also joins the artists and genres table to get extra information such as the artist's
 * name and the genre name
 *
 * @param $album_id
 * @return mixed
 */
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
 * This function is future-proofing as all other means of getting songs do a few joins,
 * there may be a scenario where the developer does not need the data from the joins
 * and therefore can use this quicker way of polling the database
 *
 * @return array
 */
function getSongsQuickly() {
  global $conn;
  $query = "SELECT * FROM songs WHERE 1=1";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $songs;
}

/**
 * Get all songs, joining onto the artists, genres and albums table in one result
 * and then collecting all the data for songs that are not in an album. Returned is a
 * merge of 2 arrays, that contain all songs, some with album data and some without
 *
 * @return array
 */
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


/**
 * Retrieve all the songs from the database that are not in albums
 * i.e. They are singles..
 *
 * @param bool $artist_id
 * @return array
 */
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

/**
 * Get specific song data, using song_id that is passed as a parameter
 * Note: We left join on the albums table because the song may not be in an album
 * and therefore we may have to join ON NULL, which is not possible unless using a LEFT JOIN
 *
 * @param $song_id
 * @return mixed
 */
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


/**
 * Retrieve all the songs that are in a specific album, this will
 * be used to list all the songs for a specific album
 *
 * @param $album_id
 * @return mixed
 */
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


/**
 * Get all the playlists so they can be listed in multiple places,
 * one place being the modal that allows you to put songs into a playlist
 *
 * @return array
 */
function getPlaylists() {
  global $conn;
  $query = "SELECT * FROM playlists WHERE 1=1";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $playlists;
}


/**
 * Get playlist data using playlist_id
 *
 * @param $playlist_id
 * @return mixed
 */
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

/**
 * Use functions to get songs that are in a playlist using the playlist_id
 * For this we must access the playlist_assignment table to find song_ids
 * linked to that specific playlist_id
 *
 * @param $playlist_id
 * @return array
 */
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

/**
 * Use this function to specifically return the song_title from the
 * database for scenarios where we only want this tiny piece of data to be
 * retrieved in a loop
 *
 * @param $song_id
 * @return mixed
 */
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


/**
 * Take post data from a form and use it to make LIKE queries to the database
 * in order to find entities that are similar to the keywords
 *
 * @param $criteria
 * @param $keywords
 * @return array
 */
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
 * A function that allows you to keep adding notifications for the front-end
 * Particularly useful for when validation is involved, with the use of 3 params
 * you specify, type of notification (success,warning,info or error), the area
 * of the system where the notification is relevant and will therefore be printed
 * and finally the message that will be inside the notification
 *
 * @param $type
 * @param $msgKey
 * @param $msg
 */
function siteAddNotification($type, $msgKey, $msg) {
  $_SESSION[$msgKey][$type][] = $msg;
}

/**
 * Using siteAddNotificiation(), we can now output the notification that has been
 * stored against the session. Using the type, we change the class of the notification
 * to be relevant in colour to the message that will be displayed. Once this is put
 * out on the screen the session is then unset to stop it appearing again and duplicating
 *
 * @param $pageType
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