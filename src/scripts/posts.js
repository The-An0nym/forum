/* GETTING POSTS */

async function getPosts() {
  // VAR
  const cont = document.getElementById("post-container");
  // Request
  const response = await fetch(`/api/getPosts.php?s=${slug}&p=${page}`);
  const clone = response.clone(); // For error handling
  try {
    const dataJSON = await response.json();
    cont.innerHTML = "";

    createPageMenu("thread", slug, page, dataJSON[0]);

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
      username.textContent = dataJSON[i].username;
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

      if (dataJSON[i].edited !== "0") {
        const edited = document.createElement("span");
        edited.className = "edited";
        edited.textContent = "edited";
        postMeta.appendChild(edited);
      }

      if (dataJSON[i].editable) {
        const editable = document.createElement("button");
        editable.className = "edit-button";
        editable.textContent = "edit";
        editable.setAttribute("onclick", `editPost("${dataJSON[i].id}")`);
        postMeta.appendChild(editable);
      }

      postData.appendChild(postMeta);
      post.appendChild(postData);

      cont.appendChild(post);
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

getPosts();

/* EDITING POSTS */

function editPost(id) {
  const div = document.getElementById(id);
  const cont = div.querySelector(".content").textContent;
  div.innerHTML = "";

  const textarea = document.createElement("textarea");
  textarea.id = "editTxt";
  textarea.value = cont;
  const button = document.createElement("button");
  button.textContent = "submit";
  button.setAttribute("onclick", `sendEdit("${id}")`);
  div.appendChild(textarea);
  div.appendChild(button);
}

/* SENDING EDITED POST */

async function sendEdit(id) {
  // VAR
  const editTxt = document.getElementById("editTxt");
  // Requests
  const response = await fetch("/api/sendEdit.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify({
      c: editTxt.value,
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
  const response = await fetch("/api/sendPost.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify({
      c: txt.value,
      s: slug,
    }),
  });

  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    txt.value = "";
    getPosts();
  }
}
