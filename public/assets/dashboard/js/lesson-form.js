document.addEventListener('DOMContentLoaded', function () {
    // Show/hide fields based on lesson type
    const lessonType = document.getElementById('lesson_type');
    const audioField = document.getElementById('audio-field');
    const videoField = document.getElementById('video-field');
    const articleField = document.getElementById('article-field');

    function toggleFields() {
        const type = lessonType.value;
        audioField.style.display = type === 'audio' ? 'block' : 'none';
        videoField.style.display = type === 'video' ? 'block' : 'none';
        articleField.style.display = type === 'article' ? 'block' : 'none';
    }

    lessonType.addEventListener('change', toggleFields);
    toggleFields(); // Initial call for edit forms

    // Initialize CKEditor ONLY when the field is visible
    if (articleField.style.display === 'block') {
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor.create(document.querySelector('#article_text'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote']
            }).catch(error => console.error(error));
        } else {
            console.error('ClassicEditor is not loaded!');
        }
    }
});
