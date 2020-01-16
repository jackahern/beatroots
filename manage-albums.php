<?php
session_start();
$_SESSION['page_title'] = 'Manage albums';
$_SESSION['page_description'] = 'Here you can view the albums already in the music player and edit them, as well as add new ones and delete them';
include_once('header.php');
?>
  <main>
    <section class="manage-table albums-table-width">
      <a class="btn btn-primary float-right add-new-shadow" href="create-edit-album.php">Add new album</a>
      <table class="table table-dark">
        <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Album cover</th>
          <th scope="col">Title</th>
          <th scope="col">Artist</th>
          <th scope="col">Genre</th>
          <th class="action-col-width" scope="col">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $albums = getAlbums();
        if (!empty($albums)) {
          foreach ($albums as $album) {
            $imgUrl = 'images/albums/' . $album['album_id'].'.png';
            ?>
            <tr>
              <td><?= $album['album_id'] ?></td>
              <td>
                <?php
                    if (file_exists($imgUrl)) {
                      ?>
                        <img class="thumbnail" src="<?=$imgUrl . '?nc=' . filemtime($imgUrl)?>" alt="<?=$album['album_title']?>">
                      <?php
                    }
                    else {
                      ?>
                        <p>There is no cover for this album</p>
                      <?php
                    }
                    ?>
              </td>
              <td><?= $album['album_title'] ?></td>
              <td><?= $album['artist_name'] ?></td>
              <td><?= $album['genre_name'] ?></td>
              <!-- Insert delete and update functionality and then link it correctly in this last column -->
              <td class="action-col action-col-width">
                <!-- update functionality, created by using query string in link and then using $_GET on the create_edit_genre.php page -->
                <a class="btn btn-link" href="create-edit-album.php?album_id=<?= $album['album_id'] ?>">Edit</a>
                <form class="d-inline" action="create-edit-album.php" method="post">
                  <input class="btn btn-danger unset-width" type="submit" value="Delete"
                         onclick="return confirm('Are you sure you want to delete this genre (<?= $album['album_title'] ?>) ?')"/>
                  <input type="hidden" value="delete-card" name="action"/>
                  <input type="hidden" value="<?= $album['album_id'] ?>" name="album_id"/>
                </form>
              </td>
            </tr>
            <?php
          }
        }
        ?>
        </tbody>
      </table>
    </section>
  </main>
<?php
include_once('footer.php');
