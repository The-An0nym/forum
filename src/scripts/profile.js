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
