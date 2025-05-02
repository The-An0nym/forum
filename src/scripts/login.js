function createLogin() {
  const container = document.createElement("div");
  container.className = "login-container";
  container.id = "login-container";

  const username = document.createElement("input");
  username.placeholder = "Username...";
  username.id = "username";

  const password = document.createElement("input");
  password.placeholder = "Password...";
  password.id = "password";

  const submitButton = document.createElement("button");
  submitButton.textContent = "Login";
  submitButton.setAttribute("onclick", "login()");

  container.appendChild(username);
  container.appendChild(password);
  container.appendChild(submitButton);

  document.body.prepend(container);
}

async function login() {
  const user = document.getElementById("username");
  const pswd = document.getElementById("password");
  const loginCont = document.getElementById("login-container");

  // Login
  const response = await fetch("/api/login.php", {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    body: `u=${encodeURIComponent(user.value)}&p=${encodeURIComponent(
      pswd.value
    )}`,
  });
  const result = await response.text();
  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    loginCont.remove();
    location.reload();
  }
}
