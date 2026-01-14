$('#addCounsellorModal').on('shown.bs.modal', function () {
    const selects = $('#location, #language, #communication_method');

    // Destroy previous Select2 instance if exists
    selects.each(function () {
        if ($(this).hasClass("select2-hidden-accessible")) {
            $(this).select2('destroy');
        }
    });

    // Initialize Select2
    selects.select2({
        allowClear: true,
        dropdownParent: $('#addCounsellorModal'),
        width: '100%',
        placeholder: function () {
            return $(this).data('placeholder') || 'Select an option';
        }
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const input = document.querySelector("#tagsInput");
    if (input) {
        const specInput = document.getElementById("specializationsJson");
        const specializations = JSON.parse(specInput.value);


        const tagify = new Tagify(input, {
            whitelist: specializations,
            enforceWhitelist: true,
            dropdown: { enabled: 0, maxItems: 100 }
        });

        // On form submit, join tags into comma-separated string
        input.closest("form").addEventListener("submit", function () {
            input.value = tagify.value.map(item => item.value).join(",");
        });
    }
});
