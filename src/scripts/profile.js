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

/* UPLOADING THE USERNAME */

async function changeUsername() {
  const inp = document.getElementById("username");
  const val = inp.value;

  if (username === val) return;

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
document.getElementById("username").addEventListener("keyup", (e) => {
  if (username === e.target.value) {
    document.getElementById("usernameButtons").style.display = "none";
  } else {
    document.getElementById("usernameButtons").style.display = "block";
  }
});

function revertUsername() {
  document.getElementById("username").value = username;
  document.getElementById("usernameButtons").style.display = "none";
}

// Password
