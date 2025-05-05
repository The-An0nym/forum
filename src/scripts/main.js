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

  if (pages - page <= 0) {
    const next = document.createElement("a");
    next.className = "next-button";
    next.textContent = "NEXT";
    next.setAttribute("href", `/${dir}/${slug}/${page + 1}`);
    pageMenu.appendChild(next);
  }
  if (page <= 0) {
    const prev = document.createElement("a");
    prev.className = "prev-button";
    prev.textContent = "PREV";
    prev.setAttribute("href", `/${dir}/${slug}/${page - 1}`);
    pageMenu.appendChild(prev);
  }
}
