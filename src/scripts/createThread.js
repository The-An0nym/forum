const threadName = document.getElementById("thread-name");
const content = document.getElementById("post-content");

async function createThread() {
  const response = await fetch("/api/createThread.php", {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    body: `n=${encodeURIComponent(threadName.value)}&p=${encodeURIComponent(
      content.value
    )}&c=${encodeURIComponent(category)}`,
  });

  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    txt.value = "";
    getPosts();
  }
}
