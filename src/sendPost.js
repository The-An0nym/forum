const txt = document.getElementById("post-content");

function send() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onload = function () {
    txt.value = "";
    getPosts();
  };
  xmlhttp.open("POST", "/sendPost.php");
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.send(
    `c=${encodeURIComponent(txt.value)}&t=${encodeURIComponent(thread)}`
  );
}
