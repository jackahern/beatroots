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

$directory = "songs/album-id-" . $album_id . "/";
$file_count = 0;
$files = glob($directory . "*");
if ($files){
  $file_count = count($files);
}

$songs = [];
$no_of_songs = isset($file_count) ? $file_count : mt_rand(4,14);
for ($x = 0; $x < $no_of_songs; $x++) {
  // Generate random number of songs in the album
  $songs[] = [
    [
      'title' => generateRandomString(),
      'id' => $x != 0 ? $x : 1,
      'artist_id' => $album['artist_id'],
      'album_id' => $album['album_id']
    ],
  ];

}

?>
    <main>
      <?php
        $count = 1;
        foreach ($songs as $song) {
          ?>
          <li><?=$song[0]['title'];?></li>
          <audio controls>
            <source src="songs/album-id-<?=$album_id;?>/<?=$count;?>.mp3" type="audio/mpeg">
          </audio>
        <?php
          $count++;
        }
      ?>
    </main>

<?php
include_once('footer.php');

