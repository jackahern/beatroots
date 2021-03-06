<?php
session_start();
$_SESSION['page_title'] = 'Songs';
$_SESSION['page_description'] = 'Browse the songs - Find your favourite and click on the song to listen';
include_once('header.php');
?>
  <main>
    <a class="btn btn-primary" href="manage-songs.php">Manage songs</a>
    <!-- To shuffle the songs, we just need to reload the page as the array that outputs the songs is shuffled anyway -->
    <a class="btn btn-primary" href="songs.php">Shuffle songs</a>
    <div id="song-list">
      <?php
      $songs = getSongsWithJoinData();
      shuffle($songs);
      foreach ($songs as $key => $song) {
        $song_url = 'songs/' . $song['song_id']. '.mp3';
        ?>
        <div class="card">
          <img class="card-img-top" src="images/<?=!is_null($song['album_id']) ? 'albums/' . $song['album_id'] : 'artists/' . $song['artist_id']?>.png" alt="Card image">
          <div class="card-body">
            <h4 class="card-title"><?=$song['song_title']?>  <i id="addPlaylistIcon" class="fa fa-plus-circle" title="Add to playlist" data-toggle="modal" data-target="#addPlaylistModal" onclick="setCookie('clicklink', '<?=$song['song_id']?>')"></i></h4>
            <p class="card-text"><?=$song['artist_name']?></p>
            <audio controls class="w-100">
                <!-- Use filemtime($songUrl) to stop the audio file caching, when it is edited it should update without hard refresh -->
              <source src="<?=$song_url . '?nc=' . filemtime($song_url)?>" type="audio/mpeg">
            </audio>
          </div>
        </div>
        <?php
      }
      require_once('modal.php');
      ?>
    </div>
  </main>
<?php
include_once('footer.php');
