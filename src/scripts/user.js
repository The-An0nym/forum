/* Banning user */
async function badUser(id, message) {
  obj = {};
  obj.i = id;
  obj.m = message;

  // Request
  const response = await fetch("/api/delete/deleteUser.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify(obj),
  });

  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    location.reload();
  }
}

/* Demoting user */
async function demoteUser(id, message) {
  obj = {};
  obj.i = id;
  obj.m = message;

  // Request
  const response = await fetch("/api/demoteUser.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify(obj),
  });

  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    location.reload();
  }
}

/* Promoting user */
async function promoteUser(id, reason, message) {
  obj = {};
  obj.i = id;
  obj.r = reason;
  obj.m = message;

  // Request
  const response = await fetch("/api/promoteUser.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json; charset=utf-8",
    },
    body: JSON.stringify(obj),
  });

  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    location.reload();
  }
}
