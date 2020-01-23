<?php
require('config/config.php');
$current_file = 'search.php';

$has_searched = isset($_GET['searched']) ? true : false;
$_SESSION['page_title'] = "Search";
$_SESSION['page_description'] = "Search through the music player to find the album or song you desire!";

require_once('header.php');

?>
      <main>
          <form class="search-form"  action="<?=$current_file?>?searched=true" method="post">
            <div class="row">
              <div class="col">
                <legend class="col-form-label col-sm-8 pt-0">I am searching for...</legend>
                <div class="col-sm-10">
                  <div class="form-check">
                    <input type="radio" name="search_criteria" value="album">
                    <label class="form-check-label" for="gridRadios1">
                      An album
                    </label>
                  </div>
                  <div class="form-check">
                    <input type="radio" name="search_criteria" value="song">
                    <label class="form-check-label" for="gridRadios2">
                      A song
                    </label>
                  </div>
                </div>
              </div>
              <div class="col">
                <label>Keyword(s)</label>
                <input type="text" class="form-control" name="search_keywords" placeholder="Someone like you"  value="<?=$has_searched ? $has_searched['search_keywords'] : ''?>" required>
              </div>
            </div>
            <button type="submit" class="btn btn-primary float-right">Search</button>
          </form>

        <section class="search-results">
          <?php
            // Only print out this section when there are search results for the page to show
            if ($has_searched) {
              ?>
              <h3>Search Results:</h3>
              <?php
              $matches = searchDatabase($_POST['search_criteria'], $_POST['search_keywords']);
              foreach ($matches as $match) {
                if ($_POST['search_criteria'] == 'song') {
                  $song = getSong($match['song_id']);
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
                  require_once('modal.php');
                } else {
                  $album = getAlbum($match['album_id']);
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
              }
            }
          ?>
        </section>
      </main>

<?php
require_once('footer.php');
