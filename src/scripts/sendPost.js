const txt = document.getElementById("post-content");

async function sendPost() {
  const response = await fetch("/api/sendPost.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json",
    },
    body: JSON.stringify({
      c: txt.value,
      s: slug,
    }),
  });

  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    txt.value = "";
    getPosts();
  }
}
