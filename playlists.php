<?php
require('config/config.php');
$_SESSION['page_title'] = 'Saved Playlists';
$_SESSION['page_description'] = 'Showing all the playlists already saved in the system';
include_once('header.php');
$playlists = getPlaylists();
?>
<main>
    <div>
        <a class="btn btn-primary" href="manage-playlists.php">Manage playlists</a>
    </div>
  <?php
    foreach ($playlists as $playlist) {
        ?>
        <div class="card text-center" style="width: 18rem;">
            <div class="card-body">
                <h5 class="card-title"><?=$playlist['playlist_name']?></h5>
                <a href="playlist.php?playlist=<?=$playlist['playlist_id']?>" class="btn btn-secondary">Go to playlist</a>
            </div>
        </div>
    <?php
    }
  ?>
</main>
<?php
include_once('footer.php');








