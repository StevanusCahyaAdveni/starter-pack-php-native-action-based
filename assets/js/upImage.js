// Fungsi untuk menampilkan gambar di modal
function showImgLink(url) {
  const modalImage = document.getElementById("modalImage");
  modalImage.src = url;

  const imageModal = new bootstrap.Modal(document.getElementById("imageModal"));
  imageModal.show();
}

// Otomatis tambahkan event listener ke semua tag img kecuali yang ada di modal
document.addEventListener("DOMContentLoaded", function () {
  const allImages = document.querySelectorAll("img");

  allImages.forEach(function (img) {
    // Cek apakah img berada di dalam modal
    const isInsideModal = img.closest("#imageModal");

    // Hanya tambahkan event jika TIDAK di dalam modal
    if (!isInsideModal) {
      // Tambahkan cursor pointer untuk indikasi bisa diklik
      img.style.cursor = "pointer";

      // Tambahkan event click
      img.addEventListener("click", function () {
        const imgSrc = this.getAttribute("src");
        if (imgSrc && imgSrc !== "") {
          showImgLink(imgSrc);
        }
      });
    }
  });
});
