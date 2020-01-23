// Very simple small bit of JQuery to open the modal for adding songs to a playlist
$(document).ready(function(){
    $("#addPlaylistIcon").click(function(){
        $("#addPlaylistModal").modal();
    });
});