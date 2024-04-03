const toggleEdit = document.getElementById("toggleEdit");
const editForm = document.getElementById("editForm");
const editSubmitBtn = editForm.querySelectorAll("button[type=submit]")[0];
const toggleCreate = document.getElementById("toggleCreate");
const createUsers = document.getElementById("createUsers");
const editSelect = document.getElementsByClassName("editSelect");
const editEtat = document.getElementById("editEtat");

const editSelectArray = Array.from(editSelect);
editSelectArray.map((select) => {
  select.addEventListener("change", function () {
    // Submit the form when the select value changes
    console.log("change");
    var closestForm = this.closest("form");
    closestForm.requestSubmit();
  });
});

// making everyThing readOnly
editForm.querySelectorAll("input").forEach((input) => (input.readOnly = true));
editForm.querySelectorAll("select")[0].disabled = true;
editSubmitBtn.classList.add("disabled");

// Prevent the default form submission behavior
editForm.addEventListener("submit", function (event) {
  if (!toggleEdit.classList.contains("active")) {
    event.preventDefault();
  }
});

toggleEdit.addEventListener("click", function () {
  editForm.querySelectorAll("select")[0].disabled =
    !editForm.querySelectorAll("select")[0].disabled;
  toggleEdit.classList.toggle("active");
  editForm.classList.toggle("readOnly");
  editSubmitBtn.classList.toggle("disabled");
  editForm.querySelectorAll("input").forEach(function (input) {
    input.readOnly = !input.readOnly;
  });
});

toggleCreate.addEventListener("click", function () {
  toggleCreate.classList.toggle("active");
  createUsers.classList.toggle("expanded");
});
