const cont = document.getElementById("thread-container");

async function getThreads() {
  const response = await fetch(`/api/getThreads.php?n=${category}`);
  try {
    const dataJSON = await response.json();
    cont.innerHTML = "";
    for (let i = 0; i < dataJSON.length; i++) {
      const URLEscapedName = dataJSON[i].name.replaceAll(" ", "-");

      const threadWrapper = document.createElement("a");
      threadWrapper.className = "thread-wrapper";
      threadWrapper.setAttribute("href", `/thread/${URLEscapedName}`);

      const thread = document.createElement("div");
      thread.className = "thread";
      threadWrapper.appendChild(thread);

      const name = document.createElement("name");
      name.className = "thread-name";
      name.textContent = dataJSON[i].name;
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
    errorMessage(xmlhttp.responseText);
  }
}

getThreads();
