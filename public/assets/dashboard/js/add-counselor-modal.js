$('#addCounsellorModal').on('shown.bs.modal', function () {
    $('#location, #language','#communication_method').select2({
        allowClear: true,
        dropdownParent: $('#addCounsellorModal'),
        width: '100%',
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
