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

      const linkWrapper = document.createElement("a");
      linkWrapper.setAttribute("href", `/thread/${dataJSON[i].slug}`);

      const thread = document.createElement("div");
      thread.className = "thread";
      linkWrapper.appendChild(thread);

      // Main (Title + Created)
      const mainWrapper = document.createElement("span");
      mainWrapper.className = "main-wrapper";

      const name = document.createElement("span");
      name.className = "thread-name";
      name.innerHTML = dataJSON[i].name;
      mainWrapper.appendChild(name);

      const creator = document.createElement("span");
      creator.className = "thread-creator";
      creator.innerHTML = dataJSON[i].creator;
      mainWrapper.appendChild(creator);

      const created = document.createElement("span");
      created.className = "created";
      created.textContent = dataJSON[i].created;
      mainWrapper.appendChild(created);

      thread.appendChild(mainWrapper);

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
      lastUser.textContent = dataJSON[i].lastUser;
      lastWrapper.appendChild(lastUser);

      detailWrapper.appendChild(lastWrapper);

      const postCount = document.createElement("span");
      postCount.className = "count";
      postCount.textContent = dataJSON[i].postCount;
      detailWrapper.appendChild(postCount);

      thread.appendChild(detailWrapper);
      detailWrapper.appendChild(postCount);

      threadWrapper.appendChild(linkWrapper);

      if (dataJSON[i].deletable === 1) {
        const deleteButton = document.createElement("button");
        deleteButton.className = "delete-button";
        deleteButton.textContent = "delete";
        deleteButton.setAttribute(
          "onclick",
          `deleteConf('${dataJSON[i].username}', '${dataJSON[i].id}')`
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

/* DELETING POST */
async function deleteThread(id) {
  // Requests
  const response = await fetch(`/api/deleteThread.php?i=${id}`);
  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    getPosts();
  }
}

function deleteConf(username, id) {
  const wrapper = createWrapperOverlay();

  const container = document.createElement("div");
  container.className = "delete-conf-container pop-up-container";
  container.id = "delete-conf-container";

  const info = document.createElement("span");
  info.className = "delete-conf-info";
  info.textContent = `Delete thread ${id} by ${username}`;

  const input = document.createElement("input");
  input.className = "delete-conf-inp";
  input.id = "delete-conf-inp";
  input.setAttribute("placeholder", "I confirm");

  const del = document.createElement("button");
  del.className = "delete-conf-button";
  del.textContent = "delete";
  del.setAttribute("onclick", `checkConfInput('${id}')`);

  container.appendChild(info);
  container.appendChild(input);
  container.appendChild(del);

  wrapper.appendChild(container);

  document.body.prepend(wrapper);
}

function checkConfInput(id) {
  if (!document.getElementById("delete-conf-inp")) return;
  inp = document.getElementById("delete-conf-inp");
  if (inp.value.toLowerCase() === "i confirm") {
    inp.parentNode.parentNode.remove();
    deleteThread(id);
  } else {
    inp.style.border = "1px solid red";
  }
}
