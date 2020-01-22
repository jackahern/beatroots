<?php
// This file is different, this is going to be the view of each album, this is ultimately where the user can listen to music
// Each album that is clicked to get here is storing a query in the URL, fetch this to know which album to load and then which songs

// GET the album_id from the array
$album_id = $_GET['album'];
include_once('config/config.php');
$album = getAlbum($album_id);

$_SESSION['page_title'] = $album['album_title'];
$_SESSION['page_description'] = 'Album by ' . $album['artist_name'];

include_once('header.php');
$songs[] = getSongsInAlbum($album_id);
?>
    <main>
      <?php
      // If the value at the first index is not false, carry on
      if ($songs[0]) {
        foreach ($songs as $song) {
          ?>
            <li><?= $song['song_title']; ?></li>
            <audio controls>
                <source src="songs/<?= $song['song_id']; ?>.mp3" type="audio/mpeg">
            </audio>
          <?php
        }
      } else {
          ?>
          <div class="alert alert-warning" role="alert">
              This album currently has no songs attached to it
          </div>
          <div class="alert alert-info" role="alert">
              To add songs to an album, you need to go to go to <a href="manage-songs.php">'Manage songs'</a> and create or edit a song, assigning it to an album
          </div>
        <?php
      }
      ?>
    </main>

<?php
include_once('footer.php');

