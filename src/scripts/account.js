/* SIGN UP */

function createSignUp() {
  const container = document.createElement("div");
  container.className = "login-container";
  container.id = "login-container";

  const username = document.createElement("input");
  username.placeholder = "Username...";
  username.id = "username";

  const password = document.createElement("input");
  password.placeholder = "Password...";
  password.id = "password";

  const passwordConfirmation = document.createElement("input");
  passwordConfirmation.placeholder = "Confirm password";
  passwordConfirmation.id = "password-confirmation";

  const submitButton = document.createElement("button");
  submitButton.textContent = "Sign-up";
  submitButton.setAttribute("onclick", "signUp()");

  container.appendChild(username);
  container.appendChild(password);
  container.appendChild(passwordConfirmation);
  container.appendChild(submitButton);

  document.body.prepend(container);
}

async function signUp() {
  const user = document.getElementById("username");
  const pswd = document.getElementById("password");
  const pswdConf = document.getElementById("password-confirmation");
  const loginCont = document.getElementById("login-container");

  if (!checkUsername(user)) return;
  if (!checkPassword(pswd)) return;
  if (pswd !== pswdConf) return;

  // Sign up
  const response = await fetch("/api/signUp.php", {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    body: `u=${encodeURIComponent(user.value)}&p1=${encodeURIComponent(
      pswd.value
    )}&p2=${encodeURIComponent(pswdConf.value)}`,
  });
  const result = await response.text();
  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    loginCont.remove();
    location.reload();
  }
}

/* LOGIN */

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

  if (!checkUsername(user)) return;
  if (!checkPassword(pswd)) return;

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
