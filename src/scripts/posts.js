/* GETTING POSTS */

async function getPosts(scrollBottom = false) {
  // VAR
  const cont = document.getElementById("post-container");
  // Request
  const response = await fetch(`/api/thread/getPosts.php?s=${slug}&p=${page}`);
  const clone = response.clone(); // For error handling
  try {
    const dataJSON = await response.json();
    cont.innerHTML = "";

    if (!dataJSON[1]) return;

    totalPosts = dataJSON[0];
    createPageMenu("gotoThreadPage", page, totalPosts);

    for (let i = 1; i < dataJSON.length; i++) {
      const post = document.createElement("div");
      post.className = "post";
      post.id = dataJSON[i].id;

      /* USER INFO */

      const userDetails = document.createElement("span");
      userDetails.className = "user-details";

      const profilePicture = document.createElement("img");
      profilePicture.className = "profile-picture";
      profilePicture.setAttribute(
        "src",
        `/images/profiles/${dataJSON[i].imageSrc}`
      );
      userDetails.appendChild(profilePicture);

      const username = document.createElement("span");
      username.className = "username";

      const handle = document.createElement("a");
      handle.textContent = dataJSON[i].username;
      handle.href = "/user/" + dataJSON[i].handle;

      username.appendChild(handle);
      userDetails.appendChild(username);

      const userPostCount = document.createElement("span");
      userPostCount.className = "user-post-count";
      userPostCount.textContent = dataJSON[i].userPostCount;
      userDetails.appendChild(userPostCount);

      post.appendChild(userDetails);

      /* REST OF THE POST */

      const postData = document.createElement("span");
      postData.className = "post-data";

      const content = document.createElement("span");
      content.className = "content";
      content.innerHTML = dataJSON[i].content;
      postData.appendChild(content);

      /* META */

      const postMeta = document.createElement("span");
      postMeta.className = "post-metadata";

      const created = document.createElement("span");
      created.className = "created";
      created.textContent = dataJSON[i].created;
      postMeta.appendChild(created);

      if (dataJSON[i].edited === "1") {
        const edited = document.createElement("span");
        edited.className = "edited";
        edited.textContent = "edited";
        postMeta.appendChild(edited);
      }

      if (dataJSON[i].editable) {
        const editable = document.createElement("button");
        editable.className = "edit-button";
        editable.textContent = "edit";
        editable.setAttribute("onclick", `editPost('${dataJSON[i].id}')`);
        postMeta.appendChild(editable);
      }

      if (dataJSON[i].deletable === 1 || dataJSON[i].editable) {
        const deletable = document.createElement("button");
        deletable.className = "delete-button";
        deletable.textContent = "delete";
        if (dataJSON[i].editable) {
          deletable.setAttribute(
            "onclick",
            `createConfirmation('delete ${dataJSON[i].username}\\\'s post', '', deletePost, '${dataJSON[i].id}')`
          );
        } else {
          deletable.setAttribute(
            "onclick",
            `createModeration('deleting ${dataJSON[i].username}\\\'s post', deletePost, '${dataJSON[i].id}')`
          );
        }
        postMeta.appendChild(deletable);
      }

      if (!dataJSON[i].deletable === 1 && !dataJSON[i].deletable) {
        const deletable = document.createElement("button");
        deletable.className = "report-button";
        deletable.textContent = "report";
        deletable.setAttribute(
          "onclick",
          `createReport(0, '${dataJSON[i].id}')`
        );
      }

      postData.appendChild(postMeta);
      post.appendChild(postData);

      cont.appendChild(post);

      // Scroll
      if (scrollBottom) window.scrollTo(0, document.body.scrollHeight);
      else window.scrollTo(0, document.body);
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

  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    editTxt.value = "";
    getPosts();
  }
}

/* SENDING NEW POST */

async function sendPost() {
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

  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
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

  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    if (page) getPosts();
  }
}

async function unSubscribe(type = 1) {
  const response = await fetch(
    `/api/thread/unSubscribe.php?t=${slug}&s=${type}`
  );
  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    const button = document.getElementById("subscribe");
    button.textContent = type === 1 ? "Subscribe" : "Unsubscribe";
    button.setAttribute("onclick", `unSubscribe(${type === 1 ? 0 : 1})`);
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
