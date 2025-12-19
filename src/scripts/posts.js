/* GETTING POSTS */
async function getPosts(scrollBottom = false) {
  const bod = await getData(`/api/thread/getPosts.php?s=${slug}&p=${page}`);

  if (bod[0]) parsePosts(bod[1], scrollBottom);
}

function parsePosts(data, scrollBottom = false) {
  if (!data) return;

  const cont = document.getElementById("post-container");

  cont.innerHTML = "";

  totalPosts = data.amount;
  createPageMenu("gotoThreadPage", page, totalPosts);

  const jsonData = data.posts;

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

    const username = document.createElement("a");
    username.textContent = jsonData[i].username;
    username.href = "/user/" + jsonData[i].handle;
    username.className = "username";

    userDetails.appendChild(username);

    const userPostCount = document.createElement("span");
    userPostCount.className = "user-post-count";
    userPostCount.textContent = jsonData[i].userPostCount;
    userDetails.appendChild(userPostCount);

    const userRating = document.createElement("span");
    userRating.className = "user-rating";
    userRating.textContent = jsonData[i].auth;
    userDetails.appendChild(userRating);

    post.appendChild(userDetails);

    /* POST CONTENT */

    const postData = document.createElement("span");
    postData.className = "post-data";

    /* POST BUTTONS */

    const postButtons = document.createElement("span");
    postButtons.className = "post-buttons";

    if (jsonData[i].editable) {
      const editable = document.createElement("button");
      editable.className = "edit-button";
      editable.textContent = "edit";
      editable.setAttribute("onclick", `editPost('${jsonData[i].id}')`);
      postButtons.appendChild(editable);
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
      postButtons.appendChild(deletable);
    }

    if (!jsonData[i].deletable === 1 && !jsonData[i].deletable) {
      const reportable = document.createElement("button");
      reportable.className = "report-button";
      reportable.textContent = "report";
      reportable.setAttribute(
        "onclick",
        `createReport(0, '${jsonData[i].id}')`
      );
      postButtons.appendChild(reportable);
    }

    postData.appendChild(postButtons);

    /* CONTENT */

    const content = document.createElement("span");
    content.className = "content";
    content.innerHTML = jsonData[i].content;
    postData.appendChild(content);

    /* METADATA (date + edited) */

    const created = document.createElement("span");
    created.className = "created";
    created.textContent = jsonData[i].created;
    postData.appendChild(created);

    if (jsonData[i].edited === "1") {
      const edited = document.createElement("span");
      edited.className = "edited";
      edited.textContent = "edited";
      postData.appendChild(edited);
    }

    post.appendChild(postData);

    cont.appendChild(post);
  }
  // Scroll
  const sglob = document.getElementById("super-global");
  if (scrollBottom) sglob.scrollTo(0, sglob.scrollHeight);
  else sglob.scrollTo(0, 0);
}

/* EDITING POSTS */

function editPost(id) {
  // Reset Posts
  const allPosts = document.getElementsByClassName("content");

  for (let i of allPosts) {
    i.style.display = "inline";
  }

  const post = document.getElementById(id).querySelector(".content");
  const textCont = post.textContent;
  post.style.display = "none"; // Hide

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

  post.nextSibling.insertBefore("TODO SOME TEXT", editWrapper); // TODO fix this
}

function cancelEdit(id) {
  const post = document.getElementById(id).querySelector(".content");
  post.querySelector(".post-data").style.display = "flex";
  if (post.parentElement.querySelector(".edit-wrapper"))
    post.parentElement.querySelector(".edit-wrapper").remove();
}

/* SENDING EDITED POST */

async function sendEdit(id) {
  const editTxt = document.getElementById("editTxt");

  const obj = {};
  obj.c = editTxt.value.trim();
  obj.i = id;
  obj.s = slug;
  obj.p = page;

  const bod = await postJson("/api/thread/sendEdit.php", obj);

  if (bod[0]) {
    editTxt.value = "";
    parsePosts(bod[1]);
  }
}

/* SENDING NEW POST */

async function sendPost() {
  const txt = document.getElementById("post-content");

  const obj = {};
  obj.c = txt.value.trim();
  obj.s = slug;

  const bod = await postJson("/api/thread/sendPost.php", obj);

  if (bod[0]) {
    txt.value = "";
    parsePosts(bod[1], true);

    let url;
    if (page !== 1) url = `https://quir.free.nf/thread/${slug}/${page}`;
    else url = `https://quir.free.nf/thread/${slug}`;

    history.pushState({}, null, url);

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

  const bod = await postJson("/api/delete/deletePost.php", obj);

  if (bod[0]) {
    // if (page) // What was the idea behind this?? -> test
    getPosts();
  }
}

async function unSubscribe(type = 1) {
  postData("/api/thread/unSubscribe.php", `t=${slug}&s=${type}`);

  ele = document.getElementById("subscribe");
  if (type === 1) {
    ele.textContent = "Unsubscribe";
    ele.setAttribute("onclick", "unSubscribe(0)");
  } else {
    ele.textContent = "Subscribe";
    ele.setAttribute("onclick", "unSubscribe(1)");
  }
}

async function gotoThreadPage(p, scrollBottom = false) {
  page = p;
  await getPosts(scrollBottom);

  let url;
  if (page !== 1) url = `https://quir.free.nf/thread/${slug}/${page}`;
  else url = `https://quir.free.nf/thread/${slug}`;

  history.pushState({}, null, url);
}
