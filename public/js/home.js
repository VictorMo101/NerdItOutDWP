const imageModal = document.getElementById("image-modal");
const imageModalContent = document.getElementById("image-modal-content");
const imageModalClose = document.getElementById("image-modal-close");

function closeImageModal() {
    imageModal.classList.remove("is-visible");
    imageModalContent.src = "";
}

document.querySelectorAll("[data-image]").forEach((imageButton) => {
    imageButton.addEventListener("click", () => {
        imageModalContent.src = imageButton.dataset.image;
        imageModal.classList.add("is-visible");
    });
});

imageModalClose.addEventListener("click", closeImageModal);
imageModal.addEventListener("click", (event) => {
    if (event.target === imageModal) {
        closeImageModal();
    }
});
document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
        closeImageModal();
    }
});
