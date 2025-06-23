async function getModerationHistory(page) {
  const response = await fetch("/api/moderation/getHistory.php");
  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    // To do... (parse data)
    console.log(result);
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
  }
  console.log(display);
}

async function getPostContent(id) {
  const response = await fetch(`/api/moderation/getPostCont.php?i=${id}`);
  return response.text();
}

async function getThreadSlug(id) {
  const response = await fetch(`/api/moderation/getThreadSlug.php?i=${id}`);
  return response.text();
}
