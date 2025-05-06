/* GET THREADS */

async function getThreads() {
  // VAR
  const cont = document.getElementById("thread-container");
  // Request
  const response = await fetch(`/api/getThreads.php?s=${slug}&p=${page}`);
  const clone = response.clone(); // For error handling
  try {
    const dataJSON = await response.json();

    createPageMenu("topic", slug, page, dataJSON[0]);

    cont.innerHTML = "";

    for (let i = 1; i < dataJSON.length; i++) {
      const threadWrapper = document.createElement("a");
      threadWrapper.className = "thread-wrapper";
      threadWrapper.setAttribute("href", `/thread/${dataJSON[i].slug}`);

      const thread = document.createElement("div");
      thread.className = "thread";
      threadWrapper.appendChild(thread);

      const name = document.createElement("name");
      name.className = "thread-name";
      name.innerHTML = dataJSON[i].name;
      thread.appendChild(name);

      const created = document.createElement("span");
      created.className = "created";
      created.textContent = dataJSON[i].created;
      thread.appendChild(created);

      const lastPost = document.createElement("span");
      lastPost.className = "last-post";
      lastPost.textContent = dataJSON[i].lastPost;
      thread.appendChild(lastPost);

      const lastUser = document.createElement("span");
      lastUser.className = "last-user";
      lastUser.textContent = dataJSON[i].lastUser;
      thread.appendChild(lastUser);

      const postCount = document.createElement("span");
      postCount.className = "thread-post-count";
      postCount.textContent = dataJSON[i].postCount;
      thread.appendChild(postCount);

      cont.appendChild(threadWrapper);
    }
  } catch {
    const msg = await clone.text();
    if (/\S/.test(msg)) {
      errorMessage(msg);
    } else {
      const noResults = document.createElement("div");
      noResults.textContent("There are no threads here yet...");

      cont.appendChild(noResults);
    }
  }
}

getThreads();

/* CREATE THREADS */

async function createThread() {
  // VAR
  const threadName = document.getElementById("thread-name");
  const content = document.getElementById("post-content");
  // Request
  const response = await fetch("/api/createThread.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
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
