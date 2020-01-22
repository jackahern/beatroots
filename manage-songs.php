<?php
require('config/config.php');
$current_file = 'manage-songs.php';
require_once('resources/pages/create-edit-song/form_handler.php');
$_SESSION['page_title'] = 'Manage songs';
$_SESSION['page_description'] = 'Here you can view the songs already in the music player and edit them, as well as add new ones and delete them. You can also manage which songs are in albums here';
include_once('header.php');
?>
  <main>
    <?php
    outputNotifications("songs");
    ?>
    <section class="manage-table">
      <a class="btn btn-primary float-right add-new-shadow" href="create-edit-song.php">Add new song</a>
      <table class="table table-dark">
        <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Song title</th>
          <th scope="col">Artist</th>
          <th scope="col">Album</th>
          <th scope="col">Genre</th>
          <th class="action-col-width" scope="col">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $songs = getSongsWithJoinData();
        if (!empty($songs)) {
          foreach ($songs as $song) {
            ?>
            <tr>
              <td><?= $song['song_id'] ?></td>
              <td><?= $song['song_title'] ?></td>
              <td><?= $song['artist_name'] ?></td>
              <td><?= !is_null($song['album_title']) ? $song['album_title'] : 'This song is not in an album'?></td>
              <td><?= $song['genre_name'] ?></td>
              <!-- Insert delete and update functionality and then link it correctly in this last column -->
              <td class="action-col action-col-width">
                <!-- update functionality, created by using query string in link and then using $_GET on the create_edit_genre.php page -->
                <a class="btn btn-link" href="create-edit-song.php?song_id=<?= $song['song_id'] ?>">Edit</a>
                <form class="d-inline" action="<?=$current_file?>" method="post">
                  <input class="btn btn-danger unset-width" type="submit" value="Delete"
                         onclick="return confirm('Are you sure you want to delete this genre (<?= $song['song_title'] ?>) ?')"/>
                  <input type="hidden" value="delete-song" name="action"/>
                  <input type="hidden" value="<?= $song['song_id'] ?>" name="song_id"/>
                </form>
              </td>
            </tr>
            <?php
          }
        } else {
            ?>
            <tr>
                <td> There are no songs in the system yet</td>
            <?php
        }
        ?>
        </tbody>
      </table>
    </section>
  </main>
<?php
include_once('footer.php');