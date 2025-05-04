async function sendEdit(id) {
  const editTxt = document.getElementById("editTxt");
  const response = await fetch("/api/sendEdit.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify({
      c: editTxt.value,
      i: id,
    }),
  });

  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    editTxt.value = "";
    getPosts();
  }
}
