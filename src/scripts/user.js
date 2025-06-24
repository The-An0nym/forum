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

  // Request
  const response = await fetch("/api/delete/deleteUser.php", {
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
    location.reload();
  }
}

/* Demoting user */
async function demoteUser(id, reason, message) {
  obj = {};
  obj.i = id;
  obj.r = reason;
  obj.m = message;

  // Request
  const response = await fetch("/api/demoteUser.php", {
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
    location.reload();
  }
}

/* Promoting user */
async function promoteUser(id, reason, message) {
  obj = {};
  obj.i = id;
  obj.r = reason;
  obj.m = message;

  // Request
  const response = await fetch("/api/promoteUser.php", {
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
    location.reload();
  }
}
