<?php
// This file is different, this is going to be the view of each album, this is ultimately where the user can listen to music
// Each album that is clicked to get here is storing a query in the URL, fetch this to know which album to load and then which songs
require('config/config.php');
// GET the album_id from the array
$playlist_id = $_GET['playlist'];
require_once 'resources/lib/functions.php';
$playlist = getPlaylistById($playlist_id);
$_SESSION['page_title'] = $playlist['playlist_name'];
$_SESSION['page_description'] = 'Playlist';

$songs = getSongsInPlaylist($playlist_id);
include_once('header.php');
?>
      <main>
        <?php
            outputNotifications("playlists");
          // If the value at the first index is not false, carry on
          if (!empty($songs)) {
            foreach ($songs as $song) {
                $title = getSongTitle($song['song_id']);
              ?>
                <li><?= $title['song_title']; ?></li>
                <audio controls>
                    <source src="songs/<?= $song['song_id']; ?>.mp3" type="audio/mpeg">
                </audio>
              <?php
            }
          } else {
              ?>
              <div class="alert alert-warning" role="alert">
                  This playlist currently has no songs attached to it
              </div>
              <div class="alert alert-info" role="alert">
                  To add songs to a playlist, you need to go inside an album or find the song on the <a href="songs.php">Songs</a> page and click the '+' symbol
              </div>
            <?php
          }
          ?>
      </main>
<?php
include_once('footer.php');