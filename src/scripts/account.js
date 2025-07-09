/* SIGN UP */

function createSignUp() {
  const wrapper = createWrapperOverlay();

  const container = document.createElement("div");
  container.className = "signup-container pop-up-container";
  container.id = "signup-container";

  const username = document.createElement("input");
  username.placeholder = "Username...";
  username.id = "username";

  const handle = document.createElement("input");
  handle.placeholder = "User handle...";
  handle.id = "handle";

  const password = document.createElement("input");
  password.placeholder = "Password...";
  password.type = "password";
  password.id = "password";

  const passwordConfirmation = document.createElement("input");
  passwordConfirmation.placeholder = "Confirm password";
  passwordConfirmation.type = "password";
  passwordConfirmation.id = "password-confirmation";

  const submitButton = document.createElement("button");
  submitButton.textContent = "Sign-up";
  submitButton.setAttribute("onclick", "signUp()");

  container.appendChild(username);
  container.appendChild(handle);
  container.appendChild(password);
  container.appendChild(passwordConfirmation);
  container.appendChild(submitButton);

  wrapper.appendChild(container);

  document.body.prepend(wrapper);
}

async function signUp() {
  const user = document.getElementById("username").value.trim();
  const handle = document.getElementById("handle").value.trim();
  const pswd = document.getElementById("password").value.trim();
  const pswdCnf = document.getElementById("password-confirmation").value.trim();
  const loginCont = document.getElementById("signup-container");

  if (!checkUsername(user)) return;
  if (!checkHandle(handle)) return;
  if (!checkPassword(pswd)) return;
  if (pswd !== pswdCnf) {
    errorMessage("Passwords do not match");
    return;
  }

  // Sign up
  const response = await fetch("/api/menu/signUp.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify({
      u: user,
      h: handle,
      p: pswd,
    }),
  });

  const bod = await parseResponse(response);

  if (bod[0]) {
    loginCont.remove();
    location.reload();
  }
}

/* LOGIN */

function createLogin() {
  const wrapper = createWrapperOverlay();

  const container = document.createElement("div");
  container.className = "login-container pop-up-container";
  container.id = "login-container";

  const handle = document.createElement("input");
  handle.placeholder = "Handle...";
  handle.id = "handle";

  const password = document.createElement("input");
  password.placeholder = "Password...";
  password.type = "password";
  password.id = "password";

  const submitButton = document.createElement("button");
  submitButton.textContent = "Login";
  submitButton.setAttribute("onclick", "login()");

  container.appendChild(handle);
  container.appendChild(password);
  container.appendChild(submitButton);

  wrapper.appendChild(container);

  document.body.prepend(wrapper);
}

async function login() {
  const handle = document.getElementById("handle").value.trim();
  const pswd = document.getElementById("password").value.trim();
  const loginCont = document.getElementById("login-container");

  if (!checkHandle(handle)) return;
  if (!checkPassword(pswd)) return;

  // Login
  const response = await fetch("/api/menu/login.php", {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded",
    },
    body: `h=${encodeURIComponent(handle)}&p=${encodeURIComponent(pswd)}`,
  });

  const bod = await parseResponse(response);

  if (bod[0]) {
    loginCont.remove();
    location.reload();
  }
}

async function logout() {
  const response = await fetch("/api/menu/logout.php");

  const result = await response.text();
  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    location.reload();
  }
}
