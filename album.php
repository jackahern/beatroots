<?php
// This file is different, this is going to be the view of each album, this is ultimately where the user can listen to music
// Each album that is clicked to get here is storing a query in the URL, fetch this to know which album to load and then which songs
session_start();

// GET the album_id from the array
$album_id = $_GET['album'];
include_once('config/data.php');
$album = getAlbum($album_id);

$_SESSION['page_title'] = $album['album_title'];
$_SESSION['page_description'] = 'Album by ' . $album['artist_name'];

include_once('header.php');

$songs = getSongsInAlbum($album_id);

?>
    <main>
      <?php
        foreach ($songs as $song) {
          ?>
          <li><?=isset($songs[1]) ? $song['song_title'] : $songs['song_title'];?></li>
          <audio controls>
            <source src="songs/<?=isset($songs[1]) ? $song['song_id'] : $songs['song_id'];?>.mp3" type="audio/mpeg">
          </audio>
        <?php
        }
      ?>
    </main>

<?php
include_once('footer.php');

