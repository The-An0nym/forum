/* UPLOADING THE PROFILE PICTURE */

async function uploadImage() {
  if (document.getElementById("imageButton").className.includes("disabled"))
    return;

  const file = document.getElementById("pfp-input");
  const img = file.files[0];

  if (!verifyImage(img)) return;

  const form_data = new FormData();
  form_data.append("i", img);

  const response = await fetch("/api/profile/settings/changePFP.php", {
    method: "POST",
    body: form_data,
  });

  const bod = await parseResponse(response);

  if (bod[0]) {
    file.value = "";
    location.reload();
  }
}

function verifyImage(img) {
  if (!["image/jpeg", "image/jpg", "image/png"].includes(img.type)) {
    createPopUpMessage("Image must be jpg or png");
    return false;
  }

  if (img.size > 1024 * 1024) {
    createPopUpMessage("Image must be less than 1MB");
    return false;
  }

  return true;
}

/* CHANGING THE USERNAME */

async function changeUsername() {
  if (document.getElementById("usernameButton").className.includes("disabled"))
    return;

  const val = document.getElementById("username").value.trim();

  if (username === val) return;
  if (!checkUsername(val)) return;

  const body = `u=${val}`;

  const bod = await postData("/api/profile/settings/changeUsername.php", body);

  if (bod[0]) location.reload();
}

/* CHANGING THE HANDLE */

async function changeHandle() {
  if (document.getElementById("handleButton").className.includes("disabled"))
    return;

  const val = document.getElementById("handle").value.trim();

  if (handle === val) return;
  if (!checkHandle(val)) return;

  const obj = {};
  obj.h = val;

  const bod = await postJson("/api/profile/settings/changeHandle.php", obj);

  if (bod[0]) location.reload();
}

/* CHANGING THE PASSWORD */
async function changePassword() {
  if (document.getElementById("passwordButton").className.includes("disabled"))
    return;

  const currPswdEle = document.getElementById("currPassword");
  const newPswdEle = document.getElementById("newPassword");
  const confPswdEle = document.getElementById("confPassword");
  const currPswd = currPswdEle.value;
  const newPswd = newPswdEle.value;
  const confPswd = confPswdEle.value;

  if (!checkPassword(currPswd)) return;
  if (!checkPassword(newPswd)) return;
  if (confPswd !== newPswd) {
    createPopUpMessage("Passwords do not match");
    return;
  }

  const body = `p=${currPswd}&np=${newPswd}`;

  const bod = await postData("/api/profile/settings/changePassword.php", body);

  if (bod[0]) location.reload();
}

async function changeAboutMe() {
  if (document.getElementById("aboutMeButton").className.includes("disabled"))
    return;

  const aboutMeVal = document.getElementById("aboutMeInput").value;

  if (aboutMeVal > 200) {
    createPopUpMessage("About me has to be less than 200 characters long");
    return;
  }

  const obj = {};
  obj.a = aboutMeVal;
  const bod = await postJson("/api/profile/settings/changeAboutMe.php", obj);
  if (bod[0]) location.reload();
}

// Deleting account
function deleteAccountAlert(user_id) {
  const wrapper = document.createElement("div");
  wrapper.className = "pop-up-body";

  const pwInput = document.createElement("input");
  pwInput.className = "pop-up-input";
  pwInput.placeholder = "Password...";

  const submitButton = document.createElement("button");
  submitButton.className = "action-button";
  submitButton.textContent = "Delete";
  submitButton.addEventListener("click", () => {
    deleteAccount(pwInput.value, user_id);
  });

  wrapper.appendChild(pwInput);
  wrapper.appendChild(submitButton);

  createAlert("Are you sure you want to delete your account?", wrapper);
}

async function deleteAccount(pw, id) {
  obj = {};
  obj.i = id;
  obj.r = 0; // Reason needs to be set (is never read though)
  obj.m = pw;

  const bod = await postJson("/api/delete/deleteAccount.php", obj);

  if (bod[0]) location.reload();
}

/* SAVE BUTTONS */

// Image
function loadPreview() {
  const file = document.getElementById("pfp-input");
  if (file.value == "") return; // Guard clause

  const img = file.files[0];
  if (verifyImage(img)) {
    document.getElementById("imageButton").className = "action-button";
    document.getElementById("preview").src = window.URL.createObjectURL(img);
  }
}

// Username
function usernameChange() {
  const usernameInput = document.getElementById("username");

  document.getElementById("usernameButton").className =
    username === usernameInput.value
      ? "action-button disabled"
      : "action-button";
}

// Handle
function handleChange() {
  const handleInput = document.getElementById("handle");
  document.getElementById("handleButton").className =
    handle === handleInput.value ? "action-button disabled" : "action-button";
}

// Password
function passwordChange() {
  const currPassword = document.getElementById("currPassword").value;
  const newPassword = document.getElementById("newPassword").value;
  const confPassword = document.getElementById("confPassword").value;
  if (
    currPassword !== "" &&
    newPassword !== "" &&
    confPassword !== "" &&
    currPassword !== newPassword
  ) {
    document.getElementById("passwordButton").className = "action-button";
  } else {
    document.getElementById("passwordButton").className =
      "action-button disabled";
  }
}

// About me
function aboutMeChange() {
  const aboutMeVal = document.getElementById("aboutMeInput").value;

  document.getElementById("aboutMeButton").className =
    aboutMe == aboutMeVal ? "action-button disabled" : "action-button";
}

// Appearance

async function appearanceChange() {
  const val = document.getElementById("appearanceSelect").value;
  if (isNaN(val)) return;
  const value = parseInt(val);

  setAppearance(value);
  const bod = await postData("/api/menu/setMode.php", `m=${value}`);
  if (bod[0]) createPopUpMessage("Saved appearance", 0); // TODO language?
}

/* OTHER BUTTONS */

async function deleteSession(session_id) {
  const bod = await postData(
    "/api/profile/moderation/deleteSession.php",
    `i=${encodeURIComponent(session_id)}`
  );

  if (bod[0]) document.getElementById(session_id).remove();
}

// Restoring post
async function restorePost(id) {
  const body = `i=${id}`;

  const bod = await postData("/api/profile/moderation/restorePost.php", body);

  if (bod[0]) location.reload();
}

// Loading session info
async function loadSessionLocation() {
  const sessionItems = document.getElementsByClassName("session-item");
  const promises = [];

  for (let i = 0; i < sessionItems.length; i++) {
    const ipElement = sessionItems[i].getElementsByClassName("ip")[0];
    const ip = ipElement.innerText.trim();

    const r = fetch(`https://get.geojs.io/v1/ip/country/${ip}.json`).then((x) =>
      x.json()
    );
    promises.push(r);
  }

  const resps = await Promise.all(promises);

  for (let i = 0; i < sessionItems.length; i++) {
    const locationElement =
      sessionItems[i].getElementsByClassName("location")[0];

    const json = resps[i];
    locationElement.textContent = json.name;
  }
}

loadSessionLocation();
