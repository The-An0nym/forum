/* Banning user */
async function deletePost(id) {
  // Requests
  const response = await fetch(`/api/banUser.php?i=${id}`);
  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    getPosts();
  }
}

/* Demoting user */
async function deletePost(id) {
  // Requests
  const response = await fetch(`/api/demoteUser.php?i=${id}`);
  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    getPosts();
  }
}

/* Promoting user */
async function deletePost(id) {
  // Requests
  const response = await fetch(`/api/promoteUser.php?i=${id}`);
  const result = await response.text();

  if (/\S/.test(result)) {
    errorMessage(result);
  } else {
    getPosts();
  }
}
