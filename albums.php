<?php
session_start();
$_SESSION['page_title'] = 'Albums';
$_SESSION['page_description'] = 'Browse the albums - Find your favourite and click on the album to listen to the songs in it';
include_once('header.php');
?>
  <main>
    <!-- ****ALBUMS**** foreach genre, list it as something that will expand and show the albums.
    foreach genre that is clicked, send a call to the database to get every album tied to that genre
    and subsequently the artist. Probably going to have to setup a function where the genre clicked is passed
    as a parameter to a db call, tables are joined and the ID is found, then retrieve all albums with that genre_id -->
    <a href="manage-albums.php">Manage albums</a>
    <div id="album-list">
      <?php
      $albums = getAlbums();
      shuffle($albums);
      foreach ($albums as $album) {
        ?>
        <a href="album.php?album=<?=$album['album_id'];?>" title="Go to <?=$album['artist_name'];?>'s album">
          <div class="card">
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
