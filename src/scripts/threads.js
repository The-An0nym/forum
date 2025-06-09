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
      const threadWrapper = document.createElement("div");
      threadWrapper.className = "thread-wrapper";

      // Main (Title + Created)
      const mainWrapper = document.createElement("span");
      mainWrapper.className = "main-wrapper";

      const name = document.createElement("span");
      name.className = "thread-name";

      const threadHref = document.createElement("a");
      threadHref.setAttribute("href", `/thread/${dataJSON[i].slug}`);
      threadHref.innerHTML = dataJSON[i].name;

      name.appendChild(threadHref);
      mainWrapper.appendChild(name);

      const creator = document.createElement("span");
      creator.className = "thread-creator";

      const creatorHandle = document.createElement("a");
      creatorHandle.innerHTML = dataJSON[i].creator;
      creatorHandle.href = "/user/" + dataJSON[i].creatorHandle;

      creator.appendChild(creatorHandle);
      mainWrapper.appendChild(creator);

      const created = document.createElement("span");
      created.className = "created";
      created.textContent = dataJSON[i].created;
      mainWrapper.appendChild(created);

      threadWrapper.appendChild(mainWrapper);

      // Details (Last user & last post + Posts)

      const detailWrapper = document.createElement("span");
      detailWrapper.className = "details-wrapper";

      const lastWrapper = document.createElement("span");
      lastWrapper.className = "last-wrapper";

      const lastPost = document.createElement("span");
      lastPost.className = "last-post";
      lastPost.textContent = dataJSON[i].lastPost;
      lastWrapper.appendChild(lastPost);

      const lastUser = document.createElement("span");
      lastUser.className = "last-user";

      const lastHandle = document.createElement("a");
      lastHandle.textContent = dataJSON[i].lastUser;
      lastHandle.href = "/user/" + dataJSON[i].lastHandle;

      lastUser.appendChild(lastHandle);
      lastWrapper.appendChild(lastUser);

      detailWrapper.appendChild(lastWrapper);

      const postCount = document.createElement("span");
      postCount.className = "count";
      postCount.textContent = dataJSON[i].postCount;
      detailWrapper.appendChild(postCount);

      threadWrapper.appendChild(detailWrapper);
      detailWrapper.appendChild(postCount);

      if (dataJSON[i].deletable === 1) {
        const deleteButton = document.createElement("button");
        deleteButton.className = "delete-button";
        deleteButton.textContent = "delete";
        deleteButton.setAttribute(
          "onclick",
          `createConfirmation('delete ${dataJSON[i].username}\\\'s post', '', deleteThread, '${dataJSON[i].id}')`
        );

        threadWrapper.appendChild(deleteButton);
      }

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
      t: threadName.value.trim(),
      c: content.value.trim(),
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

/* DELETING POST */
async function deleteThread(id) {
  // Requests
  const response = await fetch(`/api/deleteThread.php?i=${id}`);
  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    getThreads();
  }
}
