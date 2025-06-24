const getCache = {};

async function getModerationHistory(page = 0, reports = false) {
  const target = reports
    ? document.getElementById("report-history")
    : document.getElementById("moderation-history");

  let url = `/api/moderation/getModerationHistory.php?p=${page}`;
  if (reports) url += "&r=1";
  const response = await fetch(url);
  const text = await response.text(); // For error handling

  // Naive test
  if (text.trim()[0] === "<") {
    target.innerHTML = text;
  } else if (/\S/.test(text)) {
    errorMessage(text);
  }
}

async function markReport(as, id) {
  if (as === undefined || isNaN(as) || id === undefined) return;

  const response = await fetch(
    `/api/moderation/markReport.php?r=${as}&i=${id}`
  );
  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    getModerationHistory(0, true);
  }
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

  // Request
  const response = await fetch("/api/moderation/undo.php", {
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
  const response = await fetch(`/api/moderation/getPostCont.php?i=${id}`);
  const txt = response.text();
  getCache[id] = txt;
  return txt;
}

async function getThreadSlug(id) {
  if (getCache[id]) {
    return getCache[id];
  }
  const response = await fetch(`/api/moderation/getThreadSlug.php?i=${id}`);
  const txt = response.text();
  getCache[id] = txt;
  return txt;
}
