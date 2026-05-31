<div>
    <script src="https://cdn.tiny.cloud/1/7z8np3ft9l8rtj4bcrbg73k20hcdds5eziv9n9f4psyk1oyx/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: 'textarea#content', // Ensure your textarea has this ID
                plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
                toolbar_mode: 'floating',
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                height: 500,
                setup: function (editor) {
                    editor.on('change', function () {
                        tinymce.triggerSave();
                    });
                }
            });
        });
    </script>
</div>