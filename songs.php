<?php
session_start();
$_SESSION['page_title'] = 'Songs';
$_SESSION['page_description'] = 'Browse the songs - Find your favourite and click on the song to listen';
include_once('header.php');
?>
  <main>
    <!-- ****songs**** foreach genre, list it as something that will expand and show the albums.
    foreach genre that is clicked, send a call to the database to get every album tied to that genre
    and subsequently the artist. Probably going to have to setup a function where the genre clicked is passed
    as a parameter to a db call, tables are joined and the ID is found, then retrieve all albums with that genre_id -->
    <a href="manage-songs.php">Manage songs</a>
    <div id="album-list">
      <?php
      $songs = getSongsWithJoinData();
      shuffle($songs);
      foreach ($songs as $song) {
        ?>
        <div class="card">
          <img class="card-img-top" src="images/<?=!is_null($song['album_id']) ? 'albums/' . $song['album_id'] : 'artists/' . $song['artist_id']?>.png" alt="Card image">
          <div class="card-body">
            <h4 class="card-title"><?=$song['song_title']?></h4>
            <p class="card-text"><?=$song['artist_name']?></p>
            <audio controls>
              <source src="songs/<?=$song['song_id']?>.mp3" type="audio/mpeg">
            </audio>
          </div>
        </div>
        <?php
      }
      ?>
    </div>
  </main>
<?php
include_once('footer.php');
