/* UPLOADING THE PROFILE PICTURE */

async function uploadImage() {
  const file = document.getElementById("pfp");
  const img = file.files[0];

  if (!verifyImage(img)) {
    const file = document.getElementById("pfp");
    return;
  }

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
    errorMessage("Image must be jpg or png");
    return false;
  }

  if (img.size > 1024 * 1024) {
    errorMessage("Image must be less than 1MB");
    return false;
  }

  return true;
}

/* CHANGING THE USERNAME */

async function changeUsername() {
  const val = document.getElementById("username").value.trim();

  if (username === val) return;
  if (!checkUsername(val)) return;

  const body = `u=${val}`;

  const bod = await postData("/api/profile/settings/changeUsername.php", body);

  if (bod[0]) location.reload();
}

/* CHANGING THE HANDLE */

async function changeHandle() {
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
  const currPswdEle = document.getElementById("currPassword");
  const newPswdEle = document.getElementById("newPassword");
  const confPswdEle = document.getElementById("confPassword");
  const currPswd = currPswdEle.value;
  const newPswd = newPswdEle.value;
  const confPswd = confPswdEle.value;

  if (!checkPassword(currPswd)) return;
  if (!checkPassword(newPswd)) return;
  if (confPswd !== newPswd) {
    errorMessage("Passwords do not match");
    return;
  }

  const body = `p=${currPswd}&np=${newPswd}`;

  const bod = await postData("/api/profile/settings/changePassword.php", body);

  if (bod[0]) location.reload();
}

// Deleting account
async function deleteAccount(id, reason, message) {
  obj = {};
  obj.i = id;
  if (reason) {
    obj.r = reason;
    obj.m = message;
  }

  const bod = await postJson("/api/delete/deleteAccount.php", obj);

  if (bod[0]) location.reload();
}

/* SAVE BUTTONS */

// Image
function loadPreview() {
  const file = document.getElementById("pfp");
  if (file.value == "") return; // Guard clause

  const img = file.files[0];
  if (verifyImage(img)) {
    document.getElementById("imageButton").className = "action-button";
    document.getElementById("preview").src = window.URL.createObjectURL(img);
  } else {
    revertImage();
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

/* OTHER BUTTONS */

// Restoring post
async function restorePost(id) {
  const body = `i=${id}`;

  const bod = await postData("/api/profile/moderation/restorePost.php", body);

  if (bod[0]) location.reload();
}
