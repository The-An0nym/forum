function errorMessage(msg) {
  const errorMsg = document.createElement("div");
  errorMsg.className = "error-message";
  errorMsg.textContent = msg;

  document.body.prepend(errorMsg);

  setTimeout(() => errorMsg.remove(), 5000);
}
