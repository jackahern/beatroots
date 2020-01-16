<?php
session_start();
$_SESSION['page_title'] = 'Manage artists';
$_SESSION['page_description'] = 'Here you can view the artists already in the music player and edit them, as well as add new ones and delete them';
include_once('header.php');
?>
  <main>
    <section class="manage-table artists-table-width">
      <a class="btn btn-primary float-right add-new-shadow" href="create-edit-artists.php">Add new artist</a>
      <table class="table table-dark">
        <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Avatar</th>
          <th scope="col">Name</th>
          <th class="action-col-width" scope="col">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $artists = getArtists();
        if (!empty($artists)) {
          foreach ($artists as $artist) {
            $imgUrl = 'images/artists/' . $artist['artist_id'].'.png';
            ?>
            <tr>
              <td><?= $artist['artist_id'] ?></td>
              <td>
                <?php
                  if (file_exists($imgUrl)) {
                    ?>
                      <img class="thumbnail" src="<?=$imgUrl . '?nc=' . filemtime($imgUrl)?>" alt="<?=$artist['artist_name']?>">
                    <?php
                  }
                  else {
                    ?>
                      <p>There is no avatar for this artist</p>
                    <?php
                  }
                ?>
                </td>
              <td><?= $artist['artist_name'] ?></td>
              <!-- Insert delete and update functionality and then link it correctly in this last column -->
              <td class="action-col action-col-width">
                <!-- update functionality, created by using query string in link and then using $_GET on the create_edit_genre.php page -->
                <a class="btn btn-link" href="create-edit-artists.php?artist_id=<?= $artist['artist_id'] ?>">Edit</a>
                <form class="d-inline" action="create-edit-artists.php" method="post">
                  <input class="btn btn-danger unset-width" type="submit" value="Delete"
                         onclick="return confirm('Are you sure you want to delete this artist (<?= $artist['artist_name'] ?>) ?')"/>
                  <input type="hidden" value="delete-card" name="action"/>
                  <input type="hidden" value="<?= $artist['artist_id'] ?>" name="artist_id"/>
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
