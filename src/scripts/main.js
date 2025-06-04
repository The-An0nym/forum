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
  if (username.length > 16) {
    errorMessage("Max 16. chars allowed for username");
    return false;
  } else if (username.length < 4) {
    errorMessage("Min 4. chars needed for username");
    return false;
  } else if (!/^[A-z0-9.\-+]*$/i.test(username)) {
    errorMessage("Only characters <b>a-Z 0-9 + - _ .</b> are allowed");
    return false;
  }
  return true;
}

function checkPassword(pswd) {
  if (pswd.length > 64) {
    errorMessage("Max 50. chars allowed for your password");
    return false;
  } else if (pswd.length < 8) {
    errorMessage("Min. 8 chars needed for password");
    return false;
  }
  return true;
}
