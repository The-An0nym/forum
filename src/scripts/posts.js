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

    post.appendChild(userDetails);

    /* POST CONTENT */

    const postData = document.createElement("span");
    postData.className = "post-data";

    /* POST BUTTONS */

    const postOptions = document.createElement("span");
    postOptions.className = "post-options";

    if (data.logged_in) {
      const optionsRevealButton = document.createElement("span");
      optionsRevealButton.className = "options-reveal-button";
      optionsRevealButton.textContent = "...";
      optionsRevealButton.setAttribute("onClick", "toggleSibling(this)");
      postOptions.appendChild(optionsRevealButton);

      const optionButtons = document.createElement("span");
      optionButtons.className = "option-buttons";

      if (jsonData[i].editable) {
        const editable = document.createElement("button");
        editable.className = "edit-button";
        editable.setAttribute("onclick", `editPost('${jsonData[i].id}')`);

        const editIcon = document.createElement("img");
        editIcon.src = "/images/icons/edit.svg";
        editIcon.className = "svg-img";
        editable.appendChild(editIcon);

        optionButtons.appendChild(editable);
      }

      if (jsonData[i].deletable === 1 || jsonData[i].editable) {
        const deletable = document.createElement("button");
        deletable.className = "danger-button";
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

        const binIcon = document.createElement("img");
        binIcon.src = "/images/icons/bin.svg";
        binIcon.className = "svg-img";
        deletable.appendChild(binIcon);

        optionButtons.appendChild(deletable);
      }

      if (jsonData[i].deletable === 0 && !jsonData[i].editable) {
        const reportable = document.createElement("button");
        reportable.className = "report-button";
        reportable.setAttribute(
          "onclick",
          `createReport(0, '${jsonData[i].id}')`
        );

        const reportIcon = document.createElement("img");
        reportIcon.src = "/images/icons/report.svg";
        reportIcon.className = "svg-img";
        reportable.appendChild(reportIcon);

        optionButtons.appendChild(reportable);
      }

      postOptions.appendChild(optionButtons);
    }

    postData.appendChild(postOptions);

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

/* ACCESSING OPTION BUTTONS */
function toggleSibling(ele) {
  // TODO hide not just on re-click, but also on document click
  const eles = document.getElementsByClassName("option-buttons");
  for (e of eles)
    if (ele.parentElement.contains(e))
      if (e.style.display === "flex") e.style.display = "none";
      else e.style.display = "flex";
    else e.style.display = "none";
}

/* EDITING POSTS */

function editPost(id) {
  // Reset Posts
  const allPosts = document.getElementsByClassName("content");
  for (let i of allPosts) i.style.display = "inline";

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
  send.className = "action-button";
  send.setAttribute("onclick", `sendEdit("${id}")`);

  const sendIcon = document.createElementNS(
    "http://www.w3.org/2000/svg",
    "svg"
  );
  sendIcon.setAttribute("viewBox", "0 0 200 200");

  const use = document.createElementNS("http://www.w3.org/2000/svg", "use");
  use.setAttribute("href", "#send-button");

  sendIcon.appendChild(use);
  send.appendChild(sendIcon);
  editWrapper.appendChild(send);

  const cancel = document.createElement("button");
  cancel.setAttribute("onclick", `cancelEdit("${id}")`);
  cancel.className = "danger-button";

  const binIcon = document.createElement("img");
  binIcon.src = "/images/icons/bin.svg";
  binIcon.className = "img-svg";

  cancel.appendChild(binIcon);
  editWrapper.appendChild(cancel);

  post.after(editWrapper);
}

function cancelEdit(id) {
  const post = document.getElementById(id).querySelector(".post-data");
  post.querySelector(".content").style.display = "flex";
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

    createPageMenu("gotoThreadPage", page, totalPosts); // Refresh page menu

    history.pushState({}, null, url);

    if (autoSub) unSubscribe();
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

/**
 * Handles subscription
 * @param {integer} type 0 = unsubscribe, 1 = subscribe (1 is default)
 */
async function unSubscribe(type = 1) {
  document.getElementById("subscribed").style.display =
    type === 1 ? "block" : "none";
  document.getElementById("unsubscribed").style.display =
    type === 0 ? "block" : "none";

  postData("/api/thread/unSubscribe.php", `t=${slug}&s=${type}`);
}

async function gotoThreadPage(p, scrollBottom = false) {
  page = p;
  await getPosts(scrollBottom);

  let url;
  if (page !== 1) url = `https://quir.free.nf/thread/${slug}/${page}`;
  else url = `https://quir.free.nf/thread/${slug}`;

  history.pushState({}, null, url);
}
