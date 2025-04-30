function editPost(id) {
  const div = document.getElementById(id);
  div.innerHTML = "";
  let textarea = document.createElement("textarea");
  textarea.id = "editTxt";
  let button = document.createElement("button");
  button.textContent = "submit";
  button.setAttribute("onclick", `/sendEdit("${id}")`);
  div.appendChild(textarea);
  div.appendChild(button);
}
