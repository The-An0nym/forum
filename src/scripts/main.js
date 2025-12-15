/* STYLE */

const saveModeFunc = saveMode(); // Instance save mode

/**
 * Sets appearance to the specified value
 * @param {Number} to 0 = system, 1 = light, 2 = dark
 */
function setAppearance(to = 0) {
  const classList = document.body.classList;
  const result = window.matchMedia("(prefers-color-scheme: dark)");
  const isDarkMode = classList.value === "dark";

  if (((to === 0 && result.matches) || to === 2) && !isDarkMode)
    classList.toggle("dark");
  else if (isDarkMode) classList.toggle("dark");
}

/* OVERLAY */
function createWrapperOverlay() {
  const wrapper = document.createElement("div");
  wrapper.className = "overlay";
  wrapper.addEventListener("mousedown", (e) => {
    if (e.target.className === "overlay") wrapper.remove();
  });
  return wrapper;
}

/* MENU */
function toggleMenuOptions() {
  const menuOption = document.getElementById("profile-options-wrapper");
  document.removeEventListener("mouseup", menuEventListener);

  if (menuOption.style.display !== "flex") {
    menuOption.style.display = "flex";
    document.addEventListener("mouseup", menuEventListener);
  } else menuOption.style.display = "none";
}

function menuEventListener(e) {
  const menuOption = document.getElementById("profile-options-wrapper");
  if (!menuOption.parentElement.contains(e.target)) toggleMenuOptions();
}

/* OTHER */

/**
 * Creates a new pop-up on screen
 * @param {String} message
 * @param {int} type 0 = pass, 1 = fail (default 1)
 * @returns {boolean} returns true or false depending on whether pop-up could be created
 */
function createPopUp(message = "", type = 1) {
  const typeList = ["pass", "warn", "fail"];
  const popUpContainer = document.getElementById("pop-up-message-container");

  // Guard clauses
  if (!popUpContainer) return false; // No pop up container found
  if (!message) return false; // In case message is unset
  if (type < 0 || type >= typeList.length) return false; // Invalid type

  // New pop-up
  const popUp = document.createElement("div");
  popUp.className = "pop-up-message " + typeList[type];
  popUp.textContent = message;
  popUpContainer.appendChild(popUp);

  // Removal
  setTimeout(() => popUp.remove(), 7900); // time in ms - 100ms

  return true; // Successfully appended element
}

function createPageMenu(funcName, p, items) {
  const pages = Math.ceil(items / 20);

  document.getElementById("page-menu").innerHTML = "";

  if (p > 4) {
    addPageButton(funcName, 1);
    addPageButton("selectPage", "...");
  } else {
    if (p > 3) addPageButton(funcName, p - 3);
    if (p > 2) addPageButton(funcName, p - 2);
  }

  if (p > 1) addPageButton(funcName, p - 1); // Previous

  addPageButton(funcName, p, true); // Current page

  if (pages - p > 0) addPageButton(funcName, p + 1); // Next

  if (pages - p > 3) {
    addPageButton("selectPage", "...");
    addPageButton(funcName, pages);
  } else {
    if (pages - p > 1) addPageButton(funcName, p + 2);
    if (pages - p > 2) addPageButton(funcName, p + 3);
  }
}

function addPageButton(funcName, p, selected = false) {
  const button = document.createElement("button");
  button.textContent = p;

  if (isNaN(p)) button.setAttribute("onclick", `${funcName}`);
  else button.setAttribute("onclick", `${funcName}(${p})`);

  if (selected) button.className = "page-menu-button selected-page";
  else button.className = "page-menu-button";

  document.getElementById("page-menu").appendChild(button);
}

function selectPage(e) {
  const ele = e.target;
  const loc = window.location.pathname.split("/")[1];

  ele.style.display = "none";
  const inp = document.getElementById("input");
  inp.className = "page-menu-input";

  inp.addEventListener("keyup", (e) => {
    if (e.key !== "Enter") return;
    if (isNaN(inp.value)) return;
    const page = parseInt(inp.value);

    if (loc === "thread") {
      if (page > 0 && page <= Math.ceil(totalPosts / 20)) gotoThreadPage(page);
    } else if (loc === "topic") {
      if (page > 0 && page <= Math.ceil(threadCount / 20)) gotoTopicPage(page);
    }
  });

  e.parentNode.appendChild(inp);
}

/* USER INPUT VALIDATION */
function checkUsername(username) {
  if (username.length > 24) {
    createPopUp("Max 24. chars allowed for username");
    return false;
  } else if (username.length < 4) {
    createPopUp("Min 4. chars needed for username");
    return false;
  }
  return true;
}

function checkHandle(handle) {
  if (handle.length > 16) {
    createPopUp("Max 16. chars allowed for handle");
    return false;
  } else if (handle.length < 4) {
    createPopUp("Min 4. chars needed for handle");
    return false;
  } else if (!/^[A-z0-9.\-_]*$/i.test(handle)) {
    createPopUp(
      "Only characters <b>a-Z 0-9 - _ .</b> are allowed for the handle"
    );
    return false;
  }
  return true;
}

function checkPassword(pswd) {
  if (pswd.length > 64) {
    createPopUp("Max 64. chars allowed for your password");
    return false;
  } else if (pswd.length < 8) {
    createPopUp("Min. 8 chars needed for password");
    return false;
  }
  return true;
}

/* CONFIRMATION POP-UP */
function createConfirmation(text, confInp, callback, param) {
  if (confInp === "") confInp = "I confirm";

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

function createModeration(text, callback, param) {
  const wrapper = createWrapperOverlay();

  const container = document.createElement("div");
  container.className = "mod-container pop-up-container";
  container.id = "mod-container";

  const containerTitle = document.createElement("span");
  containerTitle.className = "pop-up-title";
  containerTitle.textContent = `Reason for ${text}:`;

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

  const del = document.createElement("button");
  del.className = "pop-up-submit action-button";
  del.textContent = "submit";
  del.addEventListener("mouseup", () => {
    callback(param, select.value, message.value);
    wrapper.remove();
  });

  container.appendChild(containerTitle);
  container.appendChild(select);
  container.appendChild(message);
  container.appendChild(del);

  wrapper.appendChild(container);

  document.body.prepend(wrapper);
}

/* REPORTING */
function createReport(type, id) {
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
  submitButton.addEventListener("mouseup", () => {
    if (
      message.value.trim().length >= 20 &&
      message.value.trim().length <= 200
    ) {
      sendReport(type, id, select.value, message.value);
      wrapper.remove();
    } else {
      message.style.border = "2px solid red";
      info.textContent = "Message length should be between 20 to 500 chars";
    }
  });
  submitButton.setAttribute("onclick", "report()");

  container.appendChild(select);
  container.appendChild(message);
  container.appendChild(submitButton);

  wrapper.appendChild(container);

  document.body.prepend(wrapper);
}

async function sendReport(type, id, reason, message) {
  obj = {};
  obj.t = type;
  obj.i = id;
  obj.r = reason;
  obj.m = message;

  const bod = await postJson("/api/report.php", obj);

  if (bod[0]) location.reload();
}

/* Reading API responses */
async function parseResponse(resp, autoLog = true) {
  try {
    const result = await resp.json();

    const e = new Error("e");

    if (!result.status) throw e;

    if (result.status === "pass") {
      if (!result.data) return [true, {}];
      return [true, result.data];
    } else if (result.status === "fail") {
      if (!result.msg) throw e;
      if (!/\S/.test(result.msg)) throw e;

      if (autoLog) createPopUp(result.msg, 0);
      return [false, result.msg];
    } else {
      throw e;
    }
  } catch (e) {
    if (autoLog) createPopUp("An error has occured"); // Todo: language support
    return [false, ""];
  }
}

/* Progress bad */
function progress(per) {
  const ele = document.getElementById("progress-bar");
  ele.style.width = per + "%";
}

/* Fetch */
async function postJson(URL, jsonData = {}) {
  if (!URL) return [false];

  progress(20);

  const response = await fetch(URL, {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify(jsonData),
  });

  progress(100);

  const bod = await parseResponse(response);

  progress(0);

  return bod;
}

async function postData(URL, data = "") {
  if (!URL) return [false];

  progress(20);

  const reponse = await fetch(URL, {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    body: data,
  });

  progress(100);

  const bod = await parseResponse(reponse);

  progress(0);

  return bod;
}

async function getData(URL) {
  if (!URL) return [false];

  progress(20);

  const reponse = await fetch(URL);

  progress(100);

  const bod = await parseResponse(reponse);

  progress(0);

  return bod;
}

/* FOR PAGES WITH TABS (/user/ and /account/moderation/) */
function switchTab(index) {
  const tabs = document.getElementsByClassName("menu-tab");
  const tabContent = document.getElementsByClassName("tab-content");

  for (let i = 0; i < tabs.length; i++) {
    tabs[i].className = "menu-tab" + (i == index ? " selected" : "");
    tabContent[i].style.display = i == index ? "block" : "none";
  }
}
