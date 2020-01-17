<?php
session_start();
$_SESSION['page_title'] = 'Artists';
$_SESSION['page_description'] = 'Browse the artists - Find your favourite and click on their name to reveal their albums';

// If there is an artist value being passed in the URL, they came from the menu and therefore the artist with that
// id should already be expanded
if (isset($_GET['artist'])) {
    $artist_id_url = $_GET['artist'];
}
include_once('header.php');
?>
  <main>
    <!-- ****ARTISTS**** foreach genre, list it as something that will expand and show the albums.
    foreach genre that is clicked, send a call to the database to get every album tied to that genre
    and subsequently the artist. Probably going to have to setup a function where the genre clicked is passed
    as a parameter to a db call, tables are joined and the ID is found, then retrieve all albums with that genre_id -->
    <a href="manage-artists.php">Manage artists</a>
    <div id="album-list">
      <?php
      $artists = getArtists();
      foreach ($artists as $artist) {
        ?>
        <button class="dropdown-btn <?= isset($artist_id_url) && $artist_id_url == $artist['artist_id'] ? 'active' : '';?>"><i class="fa fa-caret-right"></i>    <?=$artist['artist_name'];?>
        </button>
        <div class="dropdown-container" <?= isset($artist_id_url) && $artist_id_url == $artist['artist_id'] ? 'style="display: block;"' : 'style="display: none;"';?>>
          <div class="sub-items">
              <button class="dropdown-btn <?= isset($artist_id_url) && $artist_id_url == $artist['artist_id'] ? 'active' : '';?>"><i class="fa fa-caret-right"></i> Albums
              </button>
              <div class="dropdown-container" <?= isset($artist_id_url) && $artist_id_url == $artist['artist_id'] ? 'style="display: block;"' : 'style="display: none;"';?>>
                <?php
                $albums = getAlbumByArtist($artist['artist_id']);
                foreach ($albums as $album) {
                  ?>
                    <a href="album.php?album=<?=$album['album_id'];?>"><?=$album['album_title'];?></a>
                  <?php
                }
                ?>
              </div>
              <button class="dropdown-btn <?= isset($artist_id_url) && $artist_id_url == $artist['artist_id'] ? 'active' : '';?>"><i class="fa fa-caret-right"></i>  Singles
              </button>
              <div class="dropdown-container" <?= isset($artist_id_url) && $artist_id_url == $artist['artist_id'] ? 'style="display: block;"' : 'style="display: none;"';?>>
                <?php
                $singles = getSongsAsSingles($artist['artist_id']);
                foreach ($singles as $single) {
                    ?>
                    <div class="card card-in-artists">
                      <img class="card-img-top" src="images/artists/<?=$single['artist_id']?>.png" alt="artist avatar">
                      <div class="card-body">
                        <h4 class="card-title"><?=$single['song_title']?></h4>
                        <p class="card-text"><?=$single['artist_name']?></p>
                        <audio controls class="w-100">
                          <source src="songs/<?=$single['song_id']?>.mp3" type="audio/mpeg">
                        </audio>
                      </div>
                    </div>
                <?php
                }
                ?>
              </div>
          </div>
        </div>
        <?php
      }
      ?>
    </div>
  </main>
<?php
include_once('footer.php');
