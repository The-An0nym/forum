const txt = document.getElementById("post-content");

async function send() {
  const response = await fetch("/api/sendPost.php", {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    body: `c=${encodeURIComponent(txt.value)}&t=${encodeURIComponent(thread)}`,
  });
  const result = await response.text();
  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    txt.value = "";
  }
  getPosts();
}
