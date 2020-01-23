<?php
require_once('config/config.php');
$current_file = 'create-edit-album.php';
$success_page = 'manage-albums.php';
require_once('resources/pages/create-edit-album/form_handler.php');


// find a way to find if the album is being edited or created
$isEdit = isset($_GET['album_id']) ? true : false;
if ($isEdit) {
  // get artist details for this album
  $album = getAlbum($_GET['album_id']);
  // if album doesn't exist for the supplied album_id, then kick out back to list page and show error
  if ($album == NULL) {
    //First create the redirect to use query string until i see it working and then change it to use the errors the same way it does in the form handler
    siteAddNotification("error", "albums", "The album doesn't exist");
  } else {
    $imgUrl = 'images/albums/' . $album['album_id']. '.png';
  }
}
$_SESSION['page_title'] = $isEdit ? 'Edit album' : 'Create album';
$_SESSION['page_description'] = 'Here you can create a new album. For this, you need to know the title of the album, the artist who authored it and the genre the album suits';

include_once('header.php');

$artists = getArtists();
$genres = getGenres();

?>
  <main>
    <?php
    // Conditions to check whether any error/success messages are present in the session, if there are then print them out on the screen
    outputNotifications("albums");
    ?>
    <section class="w-50">
      <form action="<?=$current_file?>" method="post" enctype="multipart/form-data">
        <label for="albumTitle">Album title:</label>
        <input type="text" name="album_title" class="form-control" aria-describedby="albumTitleHelp" placeholder="Enter album title..." value="<?=$isEdit ? $album['album_title'] : ''?>" required>
        <label>By artist:</label>
        <input list="artists" name="album_artist_id" placeholder="Search for artist..." class="form-control" value="<?=$isEdit ? $album['artist_id'] . ' - ' . $album['artist_name'] : ''?>" required>
        <!-- Use a datalist so suggestions of artists already in the system are given to the user -->
        <datalist id="artists">
          <?php
          foreach ($artists as $artist) {
            ?>
            <option value="<?=$artist['artist_id'] . ' - ' . $artist['artist_name'];?>"><?=$artist['artist_name']?></option>
            <?php
          }
          ?>
        </datalist>
        </input>
        <label>Genre:</label>
        <select id="select-genre" name="album_genre_id" class="custom-select my-1 mr-sm-2" required>
          <?php
          if ($isEdit) {
              ?>
            <option value="<?=$album['genre_id'];?>"><?=$album['genre_name']?></option>
            <?php
          } else {
              ?>
            <option value="0">Please select...</option>
            <?php
          }
          ?>

          <?php
          foreach ($genres as $genre) {
            ?>
            <option value="<?=$genre['genre_id'];?>"><?=$genre['genre_name']?></option>
            <?php
          }
          ?>
        </select>
        <label for="albumCover"><?=$isEdit ? 'Replace album cover photo' : 'Album cover photo'?></label>
        <div class="custom-file">
          <input type="file" class="custom-file-input" id="customFile" name="album_cover">
          <label class="custom-file-label" for="customFile">Choose file</label>
        </div>
        <?php
        // If isEdit is true then show the thumbnail image that is already being used for the card
        if ($isEdit && file_exists($imgUrl)) {
          ?>
            <label for="existingCoverPhoto">Current album cover photo</label>
            <img class="edit-thumbnail-img" src="<?=$imgUrl . '?nc=' . filemtime($imgUrl)?>">
          <?php
        }
        ?>
        <div class="form-group">
          <?php
            if ($isEdit) {
              ?>
              <input type="hidden" value="<?=$album['album_id']?>" name="album_id"/>
              <input type="hidden" value="edit-album" name="action"/>
              <?php
            }
            else {
              ?>
              <input type="hidden" value="create-album" name="action"/>
              <?php
            }
          ?>
        <button type="submit" class="btn btn-primary mt-3" name="create-edit-album-submit">Submit</button>
      </form>
    </section>
  </main>
<?php
include_once('footer.php');
