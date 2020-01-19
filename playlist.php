<?php
// This file is different, this is going to be the view of each album, this is ultimately where the user can listen to music
// Each album that is clicked to get here is storing a query in the URL, fetch this to know which album to load and then which songs
session_start();

// GET the album_id from the array
$playlist_id = $_GET['playlist'];
include_once('config/data.php');
$playlist = getPlaylistById($playlist_id);

$_SESSION['page_title'] = $playlist['playlist_name'];
$_SESSION['page_description'] = 'Playlist';

include_once('header.php');
?>
      <main>
        <h1>Im here</h1>
      </main>
<?php
include_once('footer.php');