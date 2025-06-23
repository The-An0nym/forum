const getCache = {};

async function getModerationHistory(page = 0, reports = false) {
  let url = `/api/moderation/getHistory.php?p=${page}`;
  if (reports) url += "&r=1";
  const response = await fetch(
    `/api/moderation/getModerationHistory.php?p=${page}`
  );
  const clone = response.clone(); // For error handling

  try {
    const dataJSON = await response.json();
    console.log(dataJSON);
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

async function markReport(as, id) {
  if (as === undefined || isNaN(as) || id === undefined) return;

  const response = await fetch(
    `/api/moderation/markReport.php?r=${as}&i=${id}`
  );
  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    // To do... (refresh reports)
    console.log(result);
  }
}

async function showContent(type, id) {
  let display = "";
  if (type === 0) {
    display = await getPostContent(id);
  } else if (type === 1) {
    display = await getThreadSlug(id);
    // Send user to that page?
  }
  console.log(display);
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
