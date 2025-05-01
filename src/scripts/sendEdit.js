async function sendEdit(id) {
  const editTxt = document.getElementById("editTxt");
  const response = await fetch("/ajax/sendEdit.php", {
    Method: "POST",
    Headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    Body: `c=${encodeURIComponent(editTxt.value)}&i=${encodeURIComponent(id)}`,
  });
  const result = await response.text();
  if (result !== "") {
    errorMessage(result);
  } else {
    editTxt.value = "";
  }
  getPosts();
}
