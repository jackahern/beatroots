<?php
session_start();
$_SESSION['page_title'] = 'Genres';
$_SESSION['page_description'] = 'Browse the genres - Albums are held within each genre';
include_once('header.php');
?>
    <main>
        <!-- ****GENRE**** foreach genre, list it as something that will expand and show the albums.
        foreach genre that is clicked, send a call to the database to get every album tied to that genre
        and subsequently the artist. Probably going to have to setup a function where the genre clicked is passed
        as a parameter to a db call, tables are joined and the ID is found, then retrieve all albums with that genre_id -->
        <a class="btn btn-primary" href="manage-genres.php">Manage genres</a>
        <div id="album-list">
          <?php
          $genres = getGenres();
          foreach ($genres as $genre) {
            ?>
            <button class="dropdown-btn"><i class="fa fa-caret-right"></i>    <?=$genre['genre_name'];?>
            </button>
            <div class="dropdown-container">
                <div class="sub-items">
                  <?php
                  $albums = getAlbumByGenre($genre['genre_id']);
                  foreach ($albums as $album) {
                  ?>
                    <a href="album.php?album=<?=$album['album_id'];?>"><?=$album['album_title'];?> - <?=$album['artist_name'];?></a>
                    <?php
                }
                ?>
                </div>
            </div>
          <?php
          }
          ?>
        </div>
    </main>
<?php
include_once('footer.php');



