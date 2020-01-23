<?php
require('config/config.php');
// GET the playlist ID from the URL
$playlist_id = $_GET['playlist'];
require_once 'resources/lib/functions.php';
// Use the ID from the URL to load the specifc playlist that has been clicked on
$playlist = getPlaylistById($playlist_id);

$is_shuffled = isset($_GET['shuffle']) ? true : false;
$_SESSION['page_title'] = $playlist['playlist_name'];
$_SESSION['page_description'] = 'Playlist';

$songs = getSongsInPlaylist($playlist_id);
if ($is_shuffled) {
    // Shuffle the songs so they are in a different order each time the button is pressed
    shuffle($songs);
}
include_once('header.php');
?>
      <main>
          <!-- Add query parameter for shuffle to pick up in the php and use the 'shuffle' function to change the order of songs in the playlist -->
          <a class="btn btn-primary" href="playlist.php?playlist=<?=$playlist_id?>&shuffle=yes">Shuffle songs</a>
          <?php
            // Output any error/success/warning/info messages on this page that are related to playlists
            outputNotifications("playlists");
          // If songs is not empty, loop through and display audio tags foreach of them
          if (!empty($songs)) {
            foreach ($songs as $song) {
                // The song title is not retrieved when getting songs in a playlist, use a separate function so we can print the title with the song
                $title = getSongTitle($song['song_id']);
              ?>
                <li><?= $title['song_title']; ?></li>
                <audio controls>
                    <source src="songs/<?= $song['song_id']; ?>.mp3" type="audio/mpeg">
                </audio>
              <?php
            }
          } else {
              // else this playlist has no songs, display useful messages explaining why the user is not seeing anything and how to change that
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