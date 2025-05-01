function sendEdit(id) {
  const editTxt = document.getElementById("editTxt");
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onload = function () {
    console.log(xmlhttp.responseText); // Check if there is response (for testing purposes)
    editTxt.value = "";
    getPosts();
  };
  xmlhttp.open("POST", "/ajax/sendEdit.php");
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.send(
    `c=${encodeURIComponent(editTxt.value)}&i=${encodeURIComponent(id)}`
  );
}
