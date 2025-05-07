async function uploadImage() {
  const file = document.getElementById("pfp");
  const img = file.files[0];

  if (!verifyImage(img)) return;

  const form_data = new FormData();
  form_data.append("i", img);

  fetch("/api/changePFP.php", {
    method: "POST",
    body: form_data,
  })
    .then(function (response) {
      return response.json();
    })
    .then(function (responseData) {
      errorMessage(responseData.image_source);
      file.value = "";
    });
}

function verifyImage(img) {
  if (!["image/jpeg", "image/jpg", "image/png"].includes(img.type)) {
    errorMessage("Image must be jpg or png");
    file.value = "";
    return false;
  }

  if (img.size > 2 * 1024 * 1024) {
    errorMessage("Image must be less than 2MB");
    file.value = "";
    return false;
  }

  return true;
}
