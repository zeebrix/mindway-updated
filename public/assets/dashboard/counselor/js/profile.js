// profile.js

document.addEventListener("DOMContentLoaded", () => {

    /* ---------------- TAGIFY SPECIALIZATIONS ---------------- */
    const initTagify = () => {
        const tagInput = document.querySelector("#tagsInput");
        if (!tagInput) return;

        const specializations = [
            "Stress", "Burnout", "Anxiety", "Depression", "Grief & Loss", "Sleep Difficulties",
            "Conflict Resolution", "Family & Relationship Issues", "Leader/Manager Support",
            "Addiction", "Trauma & PTSD", "Work-Life Balance", "Personal Development",
            "Career Counselling", "Mindfulness", "Coping Strategies", "Life Transitions",
            "Anger Management", "Confidence Building", "Parenting Support", "Sexuality & Identity Issues",
            "Workplace Bullying & Harassment", "Communication Skills", "Motivation & Goal Setting",
            "Eating Disorders", "Body Image Issues", "Cognitive Behavioural Therapy (CBT)",
            "Emotional Regulation", "Finding Purpose", "Personal Boundaries", "Phobias & Fears",
            "Spirituality & Faith Issues", "Domestic Violence Support", "Health & Wellness"
        ];
        let selectedSpecializations = $('#default-specializations').val();
        if (!selectedSpecializations) {
            selectedSpecializations = [];
        } else {
            try {
                selectedSpecializations = JSON.parse(selectedSpecializations);
            } catch (e) {
                selectedSpecializations = selectedSpecializations
                    .split(',')
                    .map(v => v.trim());
            }
        }
        const tagify = new Tagify(tagInput, {
            whitelist: specializations,
            enforceWhitelist: true,
            dropdown: { enabled: 0, maxItems: 100 }
        });

        tagify.addTags(selectedSpecializations);
        document.querySelector("form")?.addEventListener("submit", () => {
            tagInput.value = tagify.value.map(t => t.value).join(",");
        });
    };

    /* ---------------- SELECT2 LOCATION & LANGUAGE ---------------- */
    const initSelect2 = () => {
        $('#location, #language').select2({
            width: '100%',
            allowClear: true,
            placeholder: function () { return $(this).data('placeholder'); }
        });
    };

    /* ---------------- EDIT DESCRIPTION ---------------- */
    const initDescriptionEdit = () => {
        document.getElementById('edit-description')?.addEventListener('click', e => {
            e.preventDefault();
            const box = document.getElementById('description');
            box.removeAttribute('readonly');
            box.focus();
        });
    };

    /* ---------------- FILE UPLOAD ---------------- */
    const initFileUploads = () => {

        const setupUpload = (triggerId, inputId, callback) => {
            document.getElementById(triggerId)?.addEventListener('click', () => {
                document.getElementById(inputId)?.click();
            });

            document.getElementById(inputId)?.addEventListener('change', e => {
                const file = e.target.files[0];
                if (!file) return;
                callback(file);
            });
        };

        // Logo upload
        setupUpload('uploadLogoTrigger', 'uploadLogoInput', file => {
            const counselorId = document.getElementById('counselor_id').value;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('counselorId', counselorId);
            formData.append('key', 'avatar');


            fetch("/counsellor/save-data", {
                method: "POST",
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        toastr.success("Logo Updated Successfully");
                        setTimeout(() => location.reload(), 2000);
                    } else toastr.error("Error uploading logo");
                })
                .catch(() => toastr.error("Unexpected error"));
        });

        // Intro video upload
        setupUpload('uploadIntroTrigger', 'uploadIntroInput', file => {
            const counselorId = document.getElementById('counselor_id').value;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('key', 'introduction_video');
            formData.append('counselorId', counselorId);

            $.ajax({
                url: "/counsellor/save-data",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (data) {
                    if (data.status === 'success') {
                        toastr.success("Intro Video Added Successfully");
                        setTimeout(() => location.reload(), 2000);
                    } else toastr.error("Error uploading File");
                },
                error: function () { toastr.error("Unexpected error"); }
            });
        });
    };

    /* ---------------- TIMEZONE MODAL ---------------- */

    /* ---------------- INIT ALL ---------------- */
    initTagify();
    initSelect2();
    initDescriptionEdit();
    initFileUploads();

});
