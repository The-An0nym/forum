const txt = document.getElementById("post-content");

function send() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onload = function () {
    if (xmlhttp.responseText !== "") {
      errorMessage(xmlhttp.responseText);
    } else {
      txt.value = "";
    }
    getPosts();
  };
  xmlhttp.open("POST", "/ajax/sendPost.php");
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.send(
    `c=${encodeURIComponent(txt.value)}&t=${encodeURIComponent(thread)}`
  );
}
