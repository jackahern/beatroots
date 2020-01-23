<?php
require('config/config.php');
$_SESSION['page_title'] = 'Albums';
$_SESSION['page_description'] = 'Browse the albums - Find your favourite and click on the album to listen to the songs in it';
include_once('header.php');
?>
  <main>
    <a class="btn btn-primary" href="manage-albums.php">Manage albums</a>
    <div id="album-list">
      <?php
      $albums = getAlbums();
      // Shuffle the albums so they are never in the same order
      shuffle($albums);
      foreach ($albums as $album) {
        ?>
        <a href="album.php?album=<?=$album['album_id'];?>" title="Go to <?=$album['artist_name'];?>'s album">
          <div class="card">
              <!-- If the album has a photo, use the photo. Otherwise, as a fallback, use the artist avatar for the album -->
              <img class="card-img-top" src="images/<?=!is_null($album['album_id']) ? 'albums/' . $album['album_id'] : 'artists/' . $album['artist_id']?>.png" alt="album cover photo">
              <div class="card-body">
                  <h4 class="card-title"><?=$album['album_title']?></h4>
                  <p class="card-text"><?=$album['artist_name']?></p>
              </div>
          </div>
        </a>
        <?php
      }
      ?>
    </div>
  </main>
<?php
include_once('footer.php');
