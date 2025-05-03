const threadName = document.getElementById("thread-name");
const content = document.getElementById("post-content");

async function createThread() {
  const response = await fetch("/api/createThread.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json",
    },
    body: JSON.stringify({
      t: threadName.value,
      c: content.value,
      s: slug,
    }),
  });

  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    threadName.value = "";
    content.value = "";
    getThreads();
  }
}
