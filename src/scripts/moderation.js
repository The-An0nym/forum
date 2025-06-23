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
  if (text[0] === "<") {
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
