<?php
session_start();
$_SESSION['page_title'] = 'Manage Playlists';
$_SESSION['page_description'] = 'Here you can view the playlists already in the music player and edit them, as well as add new ones and delete them';
include_once('header.php');
?>
  <main>
    <section class="manage-table playlists-table-width">
      <a class="btn btn-primary float-right add-new-shadow" href="create-edit-playlist.php">Add new playlist</a>
      <table class="table table-dark">
        <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Name</th>
          <th class="action-col-width" scope="col">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $playlists = getPlaylists();
        if (!empty($playlists)) {
          foreach ($playlists as $playlist) {
            ?>
            <tr>
              <td><?= $playlist['playlist_id'] ?></td>
              <td><?= $playlist['playlist_name'] ?></td>
              <!-- Insert delete and update functionality and then link it correctly in this last column -->
              <td class="action-col action-col-width">
                <!-- update functionality, created by using query string in link and then using $_GET on the create_edit_genre.php page -->
                <a class="btn btn-link" href="create-edit-playlist.php?playlist_id=<?= $playlist['playlist_id'] ?>">Edit</a>
                <form class="d-inline" action="create-edit-playlist.php" method="post">
                  <input class="btn btn-danger unset-width" type="submit" value="Delete"
                         onclick="return confirm('Are you sure you want to delete this playlist (<?= $playlist['playlist_name'] ?>) ?')"/>
                  <input type="hidden" value="delete-playlist" name="action"/>
                  <input type="hidden" value="<?= $playlist['playlist_id'] ?>" name="playlist_id"/>
                </form>
              </td>
            </tr>
            <?php
          }
        } else {
          ?>
          <tr>
            <td>There are no playlists currently in the system</td>
          </tr>
          <?php
        }
        ?>
        </tbody>
      </table>
    </section>
  </main>
<?php
include_once('footer.php');
