/* Banning user */

// Mostly copied from moderation on main.js -> DRY!!
function setupBan(id) {
  const wrapper = createWrapperOverlay();

  const container = document.createElement("div");
  container.className = "mod-container pop-up-container";
  container.id = "mod-container";

  const info = document.createElement("span");
  info.className = "mod-info";
  info.textContent = `Reason for deleting ${handle} :`;

  const select = document.createElement("select");
  select.className = "mod-select";
  select.id = "mod-select";

  const options = ["Spam", "Inappropriate", "Copyright", "Other"];
  for (let i = 0; i < options.length; i++) {
    const option = document.createElement("option");
    option.value = i;
    option.textContent = options[i];
    select.appendChild(option);
  }

  const message = document.createElement("textarea");
  message.placeholder = "Message...";
  message.id = "message";

  const label = document.createElement("label");
  label.for = "checkbox";
  label.textContent = "Delete all thready by this user?";

  const checkbox = document.createElement("input");
  checkbox.type = "checkbox";
  checkbox.id = "checkbox";

  const del = document.createElement("button");
  del.className = "mod-submit-button";
  del.textContent = "submit";
  del.addEventListener("mouseup", () => {
    banUser(id, checkbox.checked, select.value, message.value);
    wrapper.remove();
  });

  container.appendChild(info);
  container.appendChild(select);
  container.appendChild(message);
  container.appendChild(label);
  container.appendChild(checkbox);
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
