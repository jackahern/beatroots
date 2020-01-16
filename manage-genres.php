<?php
session_start();
$_SESSION['page_title'] = 'Manage Genres';
$_SESSION['page_description'] = 'Here you can view the genres already in the music player and edit them, as well as add new ones and delete them';
include_once('header.php');
?>
    <main>
      <section class="manage-table genre-table-width">
        <a class="btn btn-primary float-right add-new-shadow" href="create-edit-genre.php">Add new genre</a>
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
          $genres = getGenres();
          if (!empty($genres)) {
            foreach ($genres as $genre) {
              ?>
                <tr>
                    <td><?= $genre['genre_id'] ?></td>
                    <td><?= $genre['genre_name'] ?></td>
                    <!-- Insert delete and update functionality and then link it correctly in this last column -->
                    <td class="action-col action-col-width">
                        <!-- update functionality, created by using query string in link and then using $_GET on the create_edit_genre.php page -->
                        <a class="btn btn-link" href="create-edit-genre.php?genre_id=<?= $genre['genre_id'] ?>">Edit</a>
                        <form class="d-inline" action="create-edit-genre.php" method="post">
                            <input class="btn btn-danger unset-width" type="submit" value="Delete"
                                   onclick="return confirm('Are you sure you want to delete this genre (<?= $genre['genre_name'] ?>) ?')"/>
                            <input type="hidden" value="delete-card" name="action"/>
                            <input type="hidden" value="<?= $genre['genre_id'] ?>" name="genre_id"/>
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