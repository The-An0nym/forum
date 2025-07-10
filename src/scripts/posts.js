/* GETTING POSTS */

async function getPosts(scrollBottom = false) {
  progress(20);

  // VAR
  const cont = document.getElementById("post-container");
  // Request
  const response = await fetch(`/api/thread/getPosts.php?s=${slug}&p=${page}`);

  progress(40);

  const bod = await parseResponse(response);

  progress(0);

  if (!bod[0]) return; // Status: fail

  const jsonData = bod[1].posts;
  cont.innerHTML = "";

  totalPosts = bod[1].amount;
  createPageMenu("gotoThreadPage", page, totalPosts);

  for (let i = 0; i < jsonData.length; i++) {
    const post = document.createElement("div");
    post.className = "post";
    post.id = jsonData[i].id;

    /* USER INFO */

    const userDetails = document.createElement("span");
    userDetails.className = "user-details";

    const profilePicture = document.createElement("img");
    profilePicture.className = "profile-picture";
    profilePicture.setAttribute(
      "src",
      `/images/profiles/${jsonData[i].imageSrc}`
    );
    userDetails.appendChild(profilePicture);

    const username = document.createElement("span");
    username.className = "username";

    const handle = document.createElement("a");
    handle.textContent = jsonData[i].username;
    handle.href = "/user/" + jsonData[i].handle;

    username.appendChild(handle);
    userDetails.appendChild(username);

    const userPostCount = document.createElement("span");
    userPostCount.className = "user-post-count";
    userPostCount.textContent = jsonData[i].userPostCount;
    userDetails.appendChild(userPostCount);

    post.appendChild(userDetails);

    /* REST OF THE POST */

    const postData = document.createElement("span");
    postData.className = "post-data";

    const content = document.createElement("span");
    content.className = "content";
    content.innerHTML = jsonData[i].content;
    postData.appendChild(content);

    /* META */

    const postMeta = document.createElement("span");
    postMeta.className = "post-metadata";

    const created = document.createElement("span");
    created.className = "created";
    created.textContent = jsonData[i].created;
    postMeta.appendChild(created);

    if (jsonData[i].edited === "1") {
      const edited = document.createElement("span");
      edited.className = "edited";
      edited.textContent = "edited";
      postMeta.appendChild(edited);
    }

    if (jsonData[i].editable) {
      const editable = document.createElement("button");
      editable.className = "edit-button";
      editable.textContent = "edit";
      editable.setAttribute("onclick", `editPost('${jsonData[i].id}')`);
      postMeta.appendChild(editable);
    }

    if (jsonData[i].deletable === 1 || jsonData[i].editable) {
      const deletable = document.createElement("button");
      deletable.className = "delete-button";
      deletable.textContent = "delete";
      if (jsonData[i].editable) {
        deletable.setAttribute(
          "onclick",
          `createConfirmation('delete ${jsonData[i].username}\\\'s post', '', deletePost, '${jsonData[i].id}')`
        );
      } else {
        deletable.setAttribute(
          "onclick",
          `createModeration('deleting ${jsonData[i].username}\\\'s post', deletePost, '${jsonData[i].id}')`
        );
      }
      postMeta.appendChild(deletable);
    }

    if (!jsonData[i].deletable === 1 && !jsonData[i].deletable) {
      const deletable = document.createElement("button");
      deletable.className = "report-button";
      deletable.textContent = "report";
      deletable.setAttribute("onclick", `createReport(0, '${jsonData[i].id}')`);
    }

    postData.appendChild(postMeta);
    post.appendChild(postData);

    cont.appendChild(post);
  }
  // Scroll
  if (scrollBottom) window.scrollTo(0, document.body.scrollHeight);
  else window.scrollTo(0, document.body);
}

/* EDITING POSTS */

function editPost(id) {
  // Reset Posts
  const posts = document.getElementsByClassName("post");
  for (let i of posts) {
    if (i.querySelector(".edit-wrapper")) {
      i.querySelector(".post-data").style.display = "flex";
      i.querySelector(".edit-wrapper").remove();
    }
  }

  const post = document.getElementById(id);
  const textCont = post.querySelector(".content").textContent;
  post.querySelector(".post-data").style.display = "none"; // Hide

  // Create textarea and buttons
  const editWrapper = document.createElement("span");
  editWrapper.className = "edit-wrapper";

  const textarea = document.createElement("textarea");
  textarea.className = "editTxt";
  textarea.id = "editTxt";
  textarea.value = textCont;
  editWrapper.appendChild(textarea);

  const send = document.createElement("button");
  send.textContent = "send";
  send.setAttribute("onclick", `sendEdit("${id}")`);
  editWrapper.appendChild(send);

  const cancel = document.createElement("button");
  cancel.textContent = "cancel";
  cancel.setAttribute("onclick", `cancelEdit("${id}")`);
  editWrapper.appendChild(cancel);

  post.appendChild(editWrapper);
}

function cancelEdit(id) {
  const post = document.getElementById(id);
  post.querySelector(".post-data").style.display = "flex";
  post.querySelector(".edit-wrapper").remove();
}

/* SENDING EDITED POST */

async function sendEdit(id) {
  progress(10);
  // VAR
  const editTxt = document.getElementById("editTxt");
  // Requests
  const response = await fetch("/api/thread/sendEdit.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify({
      c: editTxt.value.trim(),
      i: id,
    }),
  });

  const bod = await parseResponse(response);

  if (bod[0]) {
    editTxt.value = "";
    getPosts();
  }
}

/* SENDING NEW POST */

async function sendPost() {
  progress(10);

  // VAR
  const txt = document.getElementById("post-content");
  // Request
  const response = await fetch("/api/thread/sendPost.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify({
      c: txt.value.trim(),
      s: slug,
    }),
  });

  const bod = await parseResponse(response);

  if (bod[0]) {
    txt.value = "";
    totalPosts++;
    gotoThreadPage(Math.ceil(totalPosts / 20), true);

    if (autoSub) {
      unSubscribe();
    }
  }
}

/* DELETING POST */
async function deletePost(id, reason, message) {
  obj = {};
  obj.i = id;
  if (reason) {
    obj.r = reason;
    obj.m = message;
  }

  // Request
  const response = await fetch("/api/delete/deletePost.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify(obj),
  });

  const bod = await parseResponse(response);

  if (bod[0]) {
    // if (page) // What was the idea behind this?? -> test
    getPosts();
  }
}

async function unSubscribe(type = 1) {
  const response = await fetch(
    `/api/thread/unSubscribe.php?t=${slug}&s=${type}`
  );
  parseResponse(response);
}

async function gotoThreadPage(p, scrollBottom = false) {
  page = p;
  await getPosts(scrollBottom);

  let url;
  if (page !== 1) url = `https://quir.free.nf/thread/${slug}/${page}`;
  else url = `https://quir.free.nf/thread/${slug}`;

  history.pushState({}, null, url);
}
