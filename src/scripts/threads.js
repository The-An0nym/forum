/* GET THREADS */

async function getThreads() {
  const cont = document.getElementById("thread-container");

  const bod = await getData(`/api/topic/getThreads.php?s=${slug}&p=${page}`);

  if (!bod[0]) return;

  parseThreads(bod[1]);
}

function parseThreads(jsonData) {
  const threadData = jsonData.threads;

  threadCount = jsonData.amount;
  createPageMenu("gotoTopicPage", page, threadCount);

  cont.innerHTML = "";

  for (let i = 0; i < threadData.length; i++) {
    const threadWrapper = document.createElement("div");
    threadWrapper.className = "thread-wrapper";

    // Main (Title + Created)
    const mainWrapper = document.createElement("span");
    mainWrapper.className = "main-wrapper";

    const name = document.createElement("span");
    name.className = "thread-name";

    const threadHref = document.createElement("a");
    threadHref.setAttribute("href", `/thread/${threadData[i].slug}`);
    threadHref.innerHTML = threadData[i].name;

    name.appendChild(threadHref);
    mainWrapper.appendChild(name);

    const creator = document.createElement("span");
    creator.className = "thread-creator";

    const creatorHandle = document.createElement("a");
    creatorHandle.innerHTML = threadData[i].creator;
    creatorHandle.href = "/user/" + threadData[i].creatorHandle;

    creator.appendChild(creatorHandle);
    mainWrapper.appendChild(creator);

    const created = document.createElement("span");
    created.className = "created";
    created.textContent = threadData[i].created;
    mainWrapper.appendChild(created);

    threadWrapper.appendChild(mainWrapper);

    // Last user & last post)
    const lastWrapper = document.createElement("span");
    lastWrapper.className = "last-wrapper";

    const lastPost = document.createElement("span");
    lastPost.className = "last-post";
    lastPost.textContent = threadData[i].lastPost;
    lastWrapper.appendChild(lastPost);

    const lastUser = document.createElement("span");
    lastUser.className = "last-user";

    const lastHandle = document.createElement("a");
    lastHandle.textContent = threadData[i].lastUser;
    lastHandle.href = "/user/" + threadData[i].lastHandle;

    lastUser.appendChild(lastHandle);
    lastWrapper.appendChild(lastUser);

    threadWrapper.appendChild(lastWrapper);

    // Post count
    const postCount = document.createElement("span");
    postCount.className = "count";
    postCount.textContent = threadData[i].postCount;

    threadWrapper.appendChild(postCount);

    if (threadData[i].deletable === 1) {
      const deleteButton = document.createElement("button");
      deleteButton.className = "delete-button";
      deleteButton.textContent = "delete";
      deleteButton.setAttribute(
        "onclick",
        `createConfirmation('delete ${threadData[i].username}\\\'s post', '', deleteThread, '${threadData[i].id}')`
      );

      threadWrapper.appendChild(deleteButton);
    } else {
      const deletable = document.createElement("button");
      deletable.className = "report-button";
      deletable.textContent = "report";
      deletable.setAttribute(
        "onclick",
        `createReport(1, '${threadData[i].id}')`
      );
    }

    cont.appendChild(threadWrapper);
  }
  // Scroll
  window.scrollTo(0, document.body);
}

/* CREATE THREADS */

async function createThread() {
  const threadName = document.getElementById("thread-name");
  const content = document.getElementById("post-content");

  const obj = {};
  obj.t = threadName.value.trim();
  obj.c = content.value.trim();
  obj.s = slug;

  const bod = await postJson("/api/topic/createThread.php", obj);

  if (bod[0]) {
    threadName.value = "";
    content.value = "";
    history.pushState({}, null, `https://quir.free.nf/topic/${slug}`);
    parseThreads(bod[1]);
  }
}

/* DELETING POST */
async function deleteThread(id, reason, message) {
  obj = {};
  obj.i = id;
  obj.r = reason;
  obj.m = message;

  const bod = await postJson("/api/delete/deleteThread.php", obj);

  if (bod[0]) getThreads();
}

async function gotoTopicPage(p) {
  page = p;
  await getThreads();

  let url;
  if (page !== 1) url = `https://quir.free.nf/topic/${slug}/${page}`;
  else url = `https://quir.free.nf/topic/${slug}`;

  history.pushState({}, null, url);
}
