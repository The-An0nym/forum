async function sendEdit(id) {
  const editTxt = document.getElementById("editTxt");
  const response = await fetch("/api/sendEdit.php", {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    body: `c=${encodeURIComponent(editTxt.value)}&i=${encodeURIComponent(id)}`,
  });
  const result = await response.text();
  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    editTxt.value = "";
  }
  getPosts();
}
