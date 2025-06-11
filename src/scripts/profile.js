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

  const response = await fetch("/api/changePFP.php", {
    method: "POST",
    body: form_data,
  });
  const result = await response.text();

  file.value = "";

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    // Refresh?
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

  // Check username restrictions
  const response = await fetch("/api/changeUsername.php", {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    body: `u=${val}`,
  });

  const result = await response.text();
  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    location.reload();
  }
}

/* CHANGING THE HANDLE */

async function changeHandle() {
  const val = document.getElementById("handle").value.trim();

  if (handle === val) return;
  if (!checkHandle(val)) return;

  // Check username restrictions
  const response = await fetch("/api/changeHandle.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify({
      h: val,
    }),
  });

  const result = await response.text();
  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    location.reload();
  }
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

  // Check password restrictions
  const response = await fetch("/api/changePassword.php", {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    body: `p=${currPswd}&np=${newPswd}`,
  });

  const result = await response.text();
  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    location.reload();
  }
}

// Deleting account
async function deleteAccount(id) {
  const response = await fetch(`/api/delete/deleteAccount.php?i=${id}`);

  const result = await response.text();
  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    location.reload();
  }
}

/* SAVE BUTTONS */

// Image
function loadPreview() {
  const file = document.getElementById("pfp");
  if (file.value == "") return; // Guard clause

  const img = file.files[0];
  if (verifyImage(img)) {
    document.getElementById("imageButtons").style.display = "block";
    document.getElementById("preview").src = window.URL.createObjectURL(img);
  } else {
    revertImage();
  }
}

function revertImage() {
  document.getElementById("pfp").value = "";
  document.getElementById("imageButtons").style.display = "none";
  document.getElementById("preview").src = `/images/profiles/${image_dir}`;
}

// Username
function usernameChange() {
  const usernameInput = document.getElementById("username");
  if (username === usernameInput.value) {
    document.getElementById("usernameButtons").style.display = "none";
  } else {
    document.getElementById("usernameButtons").style.display = "block";
  }
}

function revertUsername() {
  document.getElementById("username").value = username;
  document.getElementById("usernameButtons").style.display = "none";
}

// Handle
function handleChange() {
  const handleInput = document.getElementById("handle");
  if (handle === handleInput.value) {
    document.getElementById("handleButtons").style.display = "none";
  } else {
    document.getElementById("handleButtons").style.display = "block";
  }
}

function revertHandle() {
  document.getElementById("handle").value = handle;
  document.getElementById("handleButtons").style.display = "none";
}

// Password
function passwordChange() {
  const currPassword = document.getElementById("currPassword").value;
  const newPassword = document.getElementById("newPassword").value;
  const confPassword = document.getElementById("confPassword").value;
  // Maybe just hide/show if the user wants to edit instead of detecting changes?
  if (
    currPassword !== "" &&
    newPassword !== "" &&
    confPassword !== "" &&
    currPassword !== newPassword
  ) {
    document.getElementById("passwordButtons").style.display = "block";
  } else {
    document.getElementById("passwordButtons").style.display = "none";
  }
}

function revertPassword() {
  currPassword = document.getElementById("currPassword").value = "";
  newPassword = document.getElementById("newPassword").value = "";
  confPassword = document.getElementById("confPassword").value = "";
}

/* OTHER BUTTONS */

// Restoring post
async function restorePost(id) {
  const response = await fetch("/api/restorePost.php", {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    body: `i=${id}`,
  });

  const result = await response.text();
  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    location.reload();
  }
}
