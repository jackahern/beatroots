<?php
session_start();
$_SESSION['page_title'] = 'Saved Playlists';
$_SESSION['page_description'] = 'Showing all the playlists already saved in the system';
include_once('header.php');
?>
<main>
    <a class="btn btn-primary" href="manage-playlists.php">Manage playlists</a>

</main>








