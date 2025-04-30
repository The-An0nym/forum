const cont = document.getElementById("post-container");

function getPosts() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onload = function () {
    const dataJSON = JSON.parse(xmlhttp.responseText);
    cont.innerHTML = "";
    for (let i = 0; i < dataJSON.length; i++) {
      const post = document.createElement("div");
      post.className = "post";
      post.id = dataJSON[i].id;

      const username = document.createElement("span");
      username.className = "username";
      username.textContent = dataJSON[i].username;
      post.appendChild(username);

      const userPostCount = document.createElement("span");
      userPostCount.className = "user-post-count";
      userPostCount.textContent = dataJSON[i].userPostCount;
      post.appendChild(userPostCount);

      const content = document.createElement("span");
      content.className = "content";
      content.innerHTML = dataJSON[i].content;
      post.appendChild(content);

      const created = document.createElement("span");
      created.className = "created";
      created.textContent = dataJSON[i].created;
      post.appendChild(created);

      if (dataJSON[i].edited !== "0") {
        const edited = document.createElement("span");
        edited.className = "edited";
        edited.textContent = "edited";
        post.appendChild(edited);
      }

      if (dataJSON[i].editable) {
        const editable = document.createElement("button");
        editable.className = "edit-button";
        editable.textContent = "edit";
        editable.setAttribute("onclick", `editPost("${dataJSON[i].id}")`);
        post.appendChild(editable);
      }

      cont.appendChild(post);
    }
  };
  xmlhttp.open("GET", `/ajax/getPosts.php?t=${thread}`);
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.send();
}

getPosts();
