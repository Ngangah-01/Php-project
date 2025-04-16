// Get the modal
const modal = document.getElementById("editProfileModal");

// Get the link that opens the modal
const editProfileLink = document.querySelector('a[href="#"]');

// Get the <span> element that closes the modal
const closeBtn = document.querySelector(".close");

// When the user clicks the link, open the modal
editProfileLink.addEventListener("click", function (event) {
    event.preventDefault(); // Prevent default link behavior
    modal.style.display = "block";
});

// When the user clicks on <span> (x), close the modal
closeBtn.addEventListener("click", function () {
    modal.style.display = "none";
});

// When the user clicks anywhere outside the modal, close it
window.addEventListener("click", function (event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

const successMessage = document.getElementById("successMessage");
if (successMessage) {
    setTimeout(() => {
        successMessage.style.display = "none";
    }, 3000); // 3000 milliseconds = 3 seconds
}

const createPostButton = document.getElementById("createPostButton");
const createPostModal = document.getElementById("createPostModal");
const closeCreatePostModal = document.getElementById("closeCreatePostModal");

createPostButton.addEventListener("click", function () {
    createPostModal.style.display = "block";
});

closeCreatePostModal.addEventListener("click", function () {
    createPostModal.style.display = "none";
});

// Close the modal if user clicks outside of the modal content
window.addEventListener("click", function (e) {
    if (e.target === createPostModal) {
        createPostModal.style.display = "none";
    }
});