//* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - This allows the user to have multiple dropdowns without any conflict */
var dropdown = document.getElementsByClassName("dropdown-btn");
var i;

for (i = 0; i < dropdown.length; i++) {
    dropdown[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var dropdownContent = this.nextElementSibling;
        console.log(dropdownContent);
        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
        } else {
            dropdownContent.style.display = "block";
        }
    });
}

function setCookie(cname, cvalue) {
    document.cookie = cname + "=" + cvalue;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

function showCookie(){
    var cookie = getCookie('clicklink');
    return cookie;
}


// Use this JQuery to put the name of the file you just uploaded into the file upload field
$('#customFile').on('change',function(){
    // Get the file name
    var fileName = $(this).val();
    // Clean the filename to get rid of the 'fakepath' text that is auto-generated
    var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
    // Replace the "Choose a file" label
    $(this).next('.custom-file-label').html(cleanFileName);
})