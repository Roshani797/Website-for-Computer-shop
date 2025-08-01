// contact.js
function validateForm() {
  const email = document.getElementById("email").value;
  const checkbox = document.getElementById("not_robot");

  if (!checkbox.checked) {
    alert("Please verify you are not a robot.");
    return false;
  }

  const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
  if (!email.match(emailPattern)) {
    alert("Please enter a valid email address.");
    return false;
  }

  return true;
}




