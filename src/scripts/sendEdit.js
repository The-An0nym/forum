function sendEdit(id) {
  const editTxt = document.getElementById("editTxt");
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onload = function () {
    if (xmlhttp.responseText !== "") {
      errorMessage(xmlhttp.responseText);
    } else {
      editTxt.value = "";
    }
    getPosts();
  };
  xmlhttp.open("POST", "/ajax/sendEdit.php");
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.send(
    `c=${encodeURIComponent(editTxt.value)}&i=${encodeURIComponent(id)}`
  );
}
