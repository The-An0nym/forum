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
document.getElementById("pfp").addEventListener("change", (e) => {
  const file = e.target;
  if ((file.value = "")) return; // Guard clause

  const img = file.files[0];
  if (verifyImage(img)) {
    document.getElementById("imageSave").setAttribute("disabled", "false");
    document.getElementById("imageClear").setAttribute("disabled", "false");
    document.getElementById("imagePreview").src =
      window.URL.createObjectURL(img);
  } else {
    clearImage();
  }
});

function clearImage() {
  document.getElementById("pfp").value = "";
  document.getElementById("imageSave").setAttribute("disabled", "true");
  document.getElementById("imageClear").setAttribute("disabled", "true");
}

// Username
document.getElementById("username").addEventListener("change", (e) => {
  if (username !== e.target.value) {
    document.getElementById("usernameSave").setAttribute("disabled", "true");
  } else {
    document.getElementById("usernameSave").setAttribute("disabled", "false");
  }
});
