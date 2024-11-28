document.addEventListener("DOMContentLoaded", function() {
    var fileInput = document.getElementById('fileInput');
    var imagePreview = document.getElementById('imagePreview');
    var submitButton = document.getElementById('submitButton');

    submitButton.style.display = 'none';

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                submitButton.style.display = 'block';
                submitButton.disabled = false;
            };

            reader.readAsDataURL(this.files[0]);
        } else {
            imagePreview.style.display = 'none';
            submitButton.style.display = 'none';
            submitButton.disabled = true;
        }
    });
});
