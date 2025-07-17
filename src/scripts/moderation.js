const getCache = {};

async function getModerationHistory(page = 0, reports = false) {
  const target = reports
    ? document.getElementById("report-history")
    : document.getElementById("moderation-history");
  const targetNumb = reports
    ? document.getElementById("report-result")
    : document.getElementById("mod-result");

  let url = `/api/profile/moderation/getModerationHistory.php?p=${page}`;
  if (reports) url += "&r=1";
  if (reports) {
    url += getReportParams();
  } else {
    url += getModParams();
  }

  const bod = await getData(url);

  if (!bod[0]) return;

  target.innerHTML = bod[1].html;
  targetNumb.textContent = bod[1].amount;

  if (reports) {
    document.getElementById("report-unread").textContent = bod[1].unread;
    reportTotalPage = parseInt(bod[1].amount);
    reportPage = page;
    paginateReport();
  } else {
    modTotalPage = parseInt(bod[1].amount);
    modPage = page;
    paginateMod();
  }
}

async function markReport(as, id) {
  if (as === undefined || isNaN(as) || id === undefined) return;

  const body = `r=${as}&i=${id}`;

  const bod = await postData("/api/profile/moderation/markReport.php", body);

  if (bod[0]) getModerationHistory(0, true);
}

function getReportParams() {
  params = "";

  const sender = document.getElementById("report-sender").value;
  if (sender !== "") {
    if (checkHandle(sender)) params += `&s=${sender}`;
  }

  const culp = document.getElementById("report-culp").value;
  if (culp !== "") {
    if (checkHandle(culp)) params += `&c=${culp}`;
  }

  const id = document.getElementById("report-id").value;
  if (id !== "") {
    if (id.length === 33) params += `&i=${id}`;
    else errorMessage("Invalid ID");
  }

  const type = document.getElementById("report-type").value;
  if (type !== "") {
    if (!isNaN(type)) {
      if (type >= 0 && type <= 3) params += `&t=${parseInt(type)}`;
      else errorMessage("Invalid type");
    } else {
      errorMessage("Type not a number");
    }
  }

  if (document.getElementById("mod-sort").checked) {
    params += "&rev=true";
  }

  return params;
}

function getModParams() {
  params = "";

  const sender = document.getElementById("mod-sender").value;
  if (sender !== "") {
    if (checkHandle(sender)) params += `&s=${sender}`;
  }

  const culp = document.getElementById("mod-culp").value;
  if (culp !== "") {
    if (checkHandle(culp)) params += `&c=${culp}`;
  }

  const id = document.getElementById("mod-id").value;
  if (id !== "") {
    if (id.length === 33) params += `&i=${id}`;
    else errorMessage("Invalid ID");
  }

  const type = document.getElementById("mod-type").value;
  if (type !== "") {
    if (!isNaN(type)) {
      if (type >= 0 && type <= 3) params += `&t=${parseInt(type)}`;
      else errorMessage("Invalid type");
    } else {
      errorMessage("Type not a number");
    }
  }

  if (document.getElementById("mod-sort").checked) {
    params += "&rev=true";
  }

  return params;
}

function undo(id, selection = true) {
  const wrapper = createWrapperOverlay();

  const container = document.createElement("div");
  container.className = "mod-container pop-up-container";
  container.id = "mod-container";

  const info = document.createElement("span");
  info.className = "mod-info";
  info.textContent = `Reason for restoring this moderation action:`;

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

  if (!selection) {
    select.style.display = "none";
  }

  const message = document.createElement("textarea");
  message.placeholder = "Message...";
  message.id = "message";

  const del = document.createElement("button");
  del.className = "mod-submit-button";
  del.textContent = "submit";
  del.addEventListener("mouseup", () => {
    undoRequest(id, select.value, message.value);
    wrapper.remove();
  });

  container.appendChild(info);
  container.appendChild(select);
  container.appendChild(message);
  container.appendChild(del);

  wrapper.appendChild(container);

  document.body.prepend(wrapper);
}

async function undoRequest(id, reason = 0, message) {
  obj = {};
  obj.i = id;
  obj.r = reason;
  obj.m = message;

  const bod = await postJson("/api/profile/moderation/undo.php", obj);
  if (bod[0]) {
    getModerationHistory(modPage);
  }
}

async function showContent(type, id) {
  if (type === 0) {
    const resp = await getPostContent(id);
    errorMessage(resp); // Temporary
  } else if (type === 1) {
    const slug = await getThreadSlug(id);
    window.open(`/thread/${slug}`, "_blank").focus();
  }
}

async function getPostContent(id) {
  if (getCache[id]) {
    return getCache[id];
  }

  const body = `i=${id}`;

  const bod = await postData("/api/profile/moderation/getPostCont.php", body);

  if (bod[0]) return bod[1].cont + bod[1].dt + bod[1].edited;
  return "";
}

async function getThreadSlug(id) {
  if (getCache[id]) {
    return getCache[id];
  }

  const body = `i=${id}`;

  const bod = await postData("/api/profile/moderation/getThreadSlug.php", body);

  if (bod[0]) return bod[1].slug;
  return "";
}

function generateButton(report, earlier) {
  let text = "";
  let page = 0;
  if (earlier) {
    text = "Load previous";
    page = report ? reportPage - 1 : modPage - 1;
  } else {
    text = "Load next";
    page = report ? reportPage + 1 : modPage + 1;
  }

  const button = document.createElement("button");
  button.textContent = earlier ? "Load previous" : "Load next";
  button.setAttribute("onclick", `getModerationHistory(${page}, ${report})`);

  const id = report ? "report-history" : "moderation-history";

  if (earlier) {
    document.getElementById(id).prepend(button);
  } else {
    document.getElementById(id).appendChild(button);
  }
}

function paginateMod() {
  if (modPage !== 0) {
    generateButton(false, true);
  }
  if (modPage * 50 + 50 < modTotalPage) {
    generateButton(false, false);
  }
}

function paginateReport() {
  if (reportPage !== 0) {
    generateButton(true, true);
  }
  if (reportPage * 50 + 50 < reportTotalPage) {
    generateButton(true, false);
  }
}
