document.addEventListener("DOMContentLoaded", function() {
    var fileInput = document.getElementById('fileInput');
    var imagePreview = document.getElementById('imagePreview');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            imagePreview.style.display = 'none';
        }
    });
});
