/* Banning user */

// Mostly copied from moderation on main.js -> DRY!!
function setupBan(id) {
  const wrapper = createWrapperOverlay();

  const container = document.createElement("div");
  container.className = "mod-container pop-up-container";
  container.id = "mod-container";

  const containerTitle = document.createElement("span");
  containerTitle.className = "pop-up-title";
  containerTitle.textContent = `Reason for deleting ${handle} :`;

  const select = document.createElement("select");
  select.className = "pop-up-input";
  select.id = "mod-select";

  const options = ["Spam", "Inappropriate", "Copyright", "Other"];
  for (let i = 0; i < options.length; i++) {
    const option = document.createElement("option");
    option.value = i;
    option.textContent = options[i];
    select.appendChild(option);
  }

  const message = document.createElement("textarea");
  message.className = "pop-up-input";
  message.placeholder = "Message...";
  message.id = "message";

  const checkBoxWrapper = document.createElement("div");
  checkBoxWrapper.className = "pop-up-input pop-up-checkbox";

  const label = document.createElement("label");
  label.className = "pop-up-text";
  label.setAttribute("for", "checkbox");
  label.textContent = "Delete all thready by this user?";

  const checkbox = document.createElement("input");
  checkbox.type = "checkbox";
  checkbox.id = "checkbox";

  checkBoxWrapper.appendChild(label);
  checkBoxWrapper.appendChild(checkbox);

  const del = document.createElement("button");
  del.className = "pop-up-submit action-button";
  del.textContent = "submit";
  del.addEventListener("mouseup", () => {
    banUser(id, checkbox.checked, select.value, message.value);
    wrapper.remove();
  });

  container.appendChild(containerTitle);
  container.appendChild(select);
  container.appendChild(message);
  container.appendChild(checkBoxWrapper);
  container.appendChild(del);

  wrapper.appendChild(container);

  document.body.prepend(wrapper);
}

async function banUser(id, deleteThreads, reason, message) {
  obj = {};
  obj.i = id;
  obj.t = deleteThreads;
  obj.r = reason;
  obj.m = message;

  const bod = await postJson("/api/delete/deleteAccount.php", obj);

  if (bod[0]) location.reload();
}

/* User demotion and promotion */
function demoteUser(id, reason, message) {
  changeUserAuth(id, reason, message, false);
}

function promoteUser(id, reason, message) {
  changeUserAuth(id, reason, message, true);
}

async function changeUserAuth(id, reason, message, promote) {
  obj = {};
  obj.i = id;
  obj.r = reason;
  obj.m = message;
  obj.p = promote;

  const bod = await postJson("/api/changeUserAuth.php", obj);

  if (bod[0]) location.reload();
}

// To load the right tab
if (window.location.href.includes("#")) {
  const tabs = document.getElementsByClassName("menu-tab");
  const name = window.location.href.split("#")[1].toLowerCase();
  for (let i = 0; i < tabs.length; i++)
    if (tabs[i].textContent.toLowerCase() === name) switchTab(i);
}
