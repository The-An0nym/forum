const txt = document.getElementById("post-content");

async function send() {
  const response = await fetch("/ajax/sendPost.php", {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    body: `c=${encodeURIComponent(txt.value)}&t=${encodeURIComponent(thread)}`,
  });
  const result = await response.text();
  if (result !== "") {
    errorMessage(result);
  } else {
    txt.value = "";
  }
  getPosts();
}
