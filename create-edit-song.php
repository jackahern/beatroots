<?php
session_start();
$action = $_POST['action'] ?? NULL;
$currentFile = 'create-edit-song.php';
$maxUpload = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$maxUpload = str_replace('M', '', $maxUpload);
$maxUploadMsg = $maxUpload . 'MB';
$maxUpload = $maxUpload * 2048;
$uploadOk = 1;
$target_dir = "songs";
include_once('header.php');

// find a way to find if the genre is being edited or created, leave isedit here as false for now
$isEdit = isset($_GET['song_id']) ? true : false;
if ($isEdit) {
  // get artist details for this artist
  $song = getSong($_GET['song_id']);
  // if card doesn't exist for the supplied card_id, then kick out back to list page and show error
  if ($song == NULL) {
    //First create the redirect to use query string until i see it working and then change it to use the errors the same way it does in the form handler
    siteAddNotification("error", "songs", "That song doesn't exist");
  }
}
$_SESSION['page_title'] = $isEdit ? 'Edit song' : 'Create song';
$_SESSION['page_description'] = 'Here you can add a new song. For this, you need to know the title of the song, the artist who authored it, the genre and if it is part of an album, then the album';

// handle form input here
if ($action == 'create-song' || $action == 'edit-song') {
  $exploded_artist_val = explode(" ", $_POST['song_artist_id']);
  $artist_id = $exploded_artist_val[0];

  if (!empty($_POST['song_album_id'])) {
    $exploded_album_val = explode(" ", $_POST['song_album_id']);
    $album_id = $exploded_album_val[0];
  } else {
    $album_id = NULL;
  }
  // Validate the image upload
  if (isset($_FILES['song']) && strlen(trim($_FILES['song']['tmp_name']))) {
    $target_file = $target_dir . basename($_FILES["song"]["name"]);
    $songFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if ($_FILES["song"]["size"] == 0) {
      $uploadOk = 0;
      siteAddNotification("error", "songs", "The file is not an MP3");
    }
    // Check file size - cannot exceed 4MB
//    if ($_FILES["artist_avatar"]["size"] > $maxUpload) {
//      $uploadOk = 0;
//      siteAddNotification("error", "artists", "The file is too large");
//    }
    // Allow certain file formats
    if($songFileType != "mp3") {
      $uploadOk = 0;
      siteAddNotification("error", "songs", "The file is not an accepted file type, MP3 is needed");
    }
  }
  // If the upload has not worked and there are errors present, refresh the page and show the user the errors
  if ($uploadOk == 0) {
    siteAddNotification("error", "songs", "The file upload has not worked and therefore your song has not been saved");
    exit;
  }
  else if ($action == 'create-song') {
    // Involve some validation to stop the same card being created twice
    $sql = "SELECT song_title FROM songs WHERE song_title = :song_title";
    $stmt = $conn->prepare($sql);
    // Create execute variable to be assigned the statement execute function
    $execute = $stmt->execute([
      ':song_title' => $_POST['song_title']
    ]);
    // Condition to check if the prepared statement will provide something to the database that already exists
    if ($stmt->rowCount() > 0) {
      siteAddNotification("error", "songs", "A song titled " . $_POST['song_title'] . " already exists");
    } else {
      $conn->beginTransaction();
      $sql = "INSERT INTO songs (song_title, song_artist_id, song_album_id, song_genre_id) VALUES (:song_title, :song_artist_id, :song_album_id, :song_genre_id)";
      $stmt = $conn->prepare($sql);
      $execute = $stmt->execute([
        ':song_title' => $_POST['song_title'],
        ':song_artist_id' => $artist_id,
        ':song_album_id' => $album_id,
        ':song_genre_id' => $_POST['song_genre_id']
      ]);
      $id = $conn->lastInsertId();
      if ($execute) {
        $destination = $target_dir . "/" . $id . "." . $songFileType;
        if (move_uploaded_file($_FILES["song"]["tmp_name"], $destination)) {
          $conn->commit();
          chmod($destination, 0755);
          siteAddNotification("success", "songs", "Song titled " . $_POST['song_title'] . " added");
          unset($_POST['song_title']);
          unset($_POST['song_artist_id']);
          unset($_POST['song_album_id']);
          unset($_POST['song_genre_id']);
        } else {
          $conn->rollback();
          siteAddNotification("error", "songs", "Upload of song MP3 unsuccessful");
        }
      }
    }
  }
  else if ($action == 'edit-song') {
    // Write an update query to change the card details that has been edited
    $sql = "UPDATE songs 
    SET song_title = :song_title,
    song_artist_id = :song_artist_id,
    song_album_id = :song_album_id,
    song_genre_id = :song_genre_id
    WHERE song_id = :song_id";
    $stmt = $conn->prepare($sql);
    $execute = $stmt->execute([
      ':song_title' => $_POST['song_title'],
      ':song_artist_id' => $artist_id,
      ':song_album_id' => $album_id,
      ':song_genre_id' => $_POST['song_genre_id'],
      ':song_id' => $_POST['song_id']
    ]);
    // If the upload has worked, then check the existence of the image and move it to the destination if it has been changed
    if ($uploadOk) {
      $destination = $target_dir . "/" . $_POST['song_id'] . ".mp3";
      if (!empty($_FILES['song']['tmp_name'])) {
        move_uploaded_file($_FILES["song"]["tmp_name"], $destination);
        chmod($destination, 0755);
      }
      siteAddNotification("success", "songs", "The song has been updated");
    }
  }
}

$artists = getArtists();
$albums = getAlbums();
$genres = getGenres();
$songUrl = 'songs/' . $song['song_id']. '.mp3';

?>
  <main>
    <?php
    // Conditions to check whether any error/success messages are present in the session, if there are then print them out on the screen
    outputNotifications("songs");
    ?>
    <section class="create-edit-song">
      <form action="create-edit-song.php" method="post" enctype="multipart/form-data">
        <label for="songTitle">Song title:</label>
        <input type="text" name="song_title" class="form-control" aria-describedby="songTitleHelp" placeholder="Enter song title..." value="<?=$isEdit ? $song['song_title'] : ''?>">
        <label>By artist:</label>
        <input list="artists" name="song_artist_id" placeholder="Search for artist..." class="form-control" value="<?=$isEdit ? $song['artist_id'] . ' - ' . $song['artist_name'] : ''?>">
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
        <label>In album (can be left blank if the song is not in an album):</label>
        <input list="albums" name="song_album_id" placeholder="Search for album..." class="form-control" value="<?=$isEdit ? $song['album_id'] . ' - ' . $song['album_title'] : ''?>">
        <datalist id="albums">
        <?php
          foreach ($albums as $album) {
            ?>
            <option value="<?=$album['album_id'] . ' - ' . $album['album_title'];?>"><?=$album['album_title']?></option>
            <?php
            }
          ?>
        </datalist>
        </input>
        <label>Genre:</label>
        <select id="select-genre" name="song_genre_id" class="custom-select my-1 mr-sm-2">
          <?php
          if ($isEdit) {
            ?>
            <option value="<?=$song['genre_id'];?>"><?=$song['genre_name']?></option>
            <?php
          } else {
            ?>
            <option value="0">Please select...</option>
            <?php
          }
          foreach ($genres as $genre) {
            ?>
            <option value="<?=$genre['genre_id'];?>"><?=$genre['genre_name']?></option>
            <?php
          }
          ?>
        </select>
        <label for="uploadedSong"><?=$isEdit ? 'Change uploaded song' : 'Upload song'?></label>
        <div class="custom-file">
          <input type="file" class="custom-file-input" id="customFile" name="song">
          <label class="custom-file-label" for="customFile">Choose file</label>
        </div>
        <?php
        // If isEdit is true then show the thumbnail image that is already being used for the card
        if ($isEdit && file_exists($songUrl)) {
          ?>
          <label for="existingCoverPhoto">Current uploaded song - <?=$song['song_title'];?></label>
          <audio controls>
              <source src="<?=$songUrl;?>" type="audio/mpeg">
          </audio>
          <?php
        }
        ?>
        <div class="form-group">
          <?php
          if ($isEdit) {
            ?>
            <input type="hidden" value="<?=$song['song_id']?>" name="song_id"/>
            <input type="hidden" value="edit-song" name="action"/>
            <?php
          }
          else {
            ?>
            <input type="hidden" value="create-song" name="action"/>
            <?php
          }
          ?>
          <button type="submit" class="btn btn-primary mt-3" name="create-edit-song-submit"><?=$isEdit ? 'Update' : 'Submit';?></button>
      </form>
    </section>
  </main>
<?php
include_once('footer.php');