/* OVERLAY */
function createWrapperOverlay() {
  const wrapper = document.createElement("div");
  wrapper.className = "overlay";
  wrapper.addEventListener("mouseup", (e) => {
    if (e.target.className === "overlay") wrapper.remove();
  });
  return wrapper;
}

/* OTHER */

function errorMessage(msg) {
  const errorMsg = document.createElement("div");
  errorMsg.className = "error-message";
  errorMsg.textContent = msg;

  document.body.prepend(errorMsg);

  setTimeout(() => errorMsg.remove(), 5000);
}

function createPageMenu(dir, slug, page, items) {
  const pageMenu = document.getElementById("pageMenu");
  const pages = Math.ceil(items / 20) - 1;

  pageMenu.innerHTML = "";

  if (!(page <= 0)) {
    const prev = document.createElement("a");
    prev.className = "prev-button";
    prev.textContent = "PREV";
    prev.setAttribute("href", `/${dir}/${slug}/${page - 1}`);
    pageMenu.appendChild(prev);
  }
  if (!(pages - page <= 0)) {
    const next = document.createElement("a");
    next.className = "next-button";
    next.textContent = "NEXT";
    next.setAttribute("href", `/${dir}/${slug}/${page + 1}`);
    pageMenu.appendChild(next);
  }
}

/* USER INPUT VALIDATION */
function checkUsername(username) {
  if (username.length > 24) {
    errorMessage("Max 24. chars allowed for username");
    return false;
  } else if (username.length < 4) {
    errorMessage("Min 4. chars needed for username");
    return false;
  }
  return true;
}

function checkHandle(handle) {
  if (handle.length > 16) {
    errorMessage("Max 16. chars allowed for handle");
    return false;
  } else if (handle.length < 4) {
    errorMessage("Min 4. chars needed for handle");
    return false;
  } else if (!/^[A-z0-9.\-_]*$/i.test(handle)) {
    errorMessage(
      "Only characters <b>a-Z 0-9 - _ .</b> are allowed for the handle"
    );
    return false;
  }
  return true;
}

function checkPassword(pswd) {
  if (pswd.length > 64) {
    errorMessage("Max 64. chars allowed for your password");
    return false;
  } else if (pswd.length < 8) {
    errorMessage("Min. 8 chars needed for password");
    return false;
  }
  return true;
}

/* CONFIRMATION POP-UP */
function createConfirmation(text, confInp, callback, param) {
  const wrapper = createWrapperOverlay();

  const container = document.createElement("div");
  container.className = "conf-container pop-up-container";
  container.id = "conf-container";

  const info = document.createElement("span");
  info.className = "conf-info";
  info.textContent = `Are you sure you want to ${text}?`;

  const input = document.createElement("input");
  input.className = "conf-inp";
  input.id = "conf-inp";
  input.setAttribute("placeholder", confInp);

  const del = document.createElement("button");
  del.className = "delete-conf-button";
  del.textContent = "delete";
  del.addEventListener("mouseup", () => {
    if (input.value === confInp) {
      callback(param);
      wrapper.remove();
    } else {
      input.style.border = "2px solid red";
    }
  });

  container.appendChild(info);
  container.appendChild(input);
  container.appendChild(del);

  wrapper.appendChild(container);

  document.body.prepend(wrapper);
}

/* REPORTING */
function createReport(type) {
  const wrapper = createWrapperOverlay();

  const container = document.createElement("div");
  container.className = "report-container pop-up-container";
  container.id = "report-container";

  const select = document.createElement("select");
  select.className = "report-select";
  select.id = "report-select";

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

  const submitButton = document.createElement("button");
  submitButton.textContent = "Report";
  submitButton.setAttribute("onclick", "report()");

  container.appendChild(select);
  container.appendChild(message);
  container.appendChild(submitButton);

  wrapper.appendChild(container);

  document.body.prepend(wrapper);
}

function report() {
  // Todo...
}
