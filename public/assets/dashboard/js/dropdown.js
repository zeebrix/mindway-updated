let locations = [];
let languages = [];

function loadLocation() {
    fetch('/country.json')
        .then(response => response.json())
        .then(data => {
            locations = data;
            initLocationList();
        })
        .catch(error => {
            console.error('Error fetching locations:', error);
        });
}

function initLocationList() {
    const locationSelect = $('#location');
    locationSelect.empty().append('<option value="">Select a location</option>'); // Reset options
    locations.forEach(tz => {
        locationSelect.append(new Option(tz.name, tz.name));
    });
    const defaultLocation = document.getElementById('default-location').value;
    if (defaultLocation) {
        locationSelect.val(defaultLocation).trigger('change');
    }
}

function loadLanguage() {
    fetch('/language.json')
        .then(response => response.json())
        .then(data => {
            languages = data;
            initLanguageList();
        })
        .catch(error => {
            console.error('Error fetching languages:', error);
        });
}

function initLanguageList() {
    const languageSelect = $('#language');
    languageSelect.empty().append('<option value="">Select a language</option>'); // Reset options
    languages.forEach(tz => {
        languageSelect.append(new Option(tz.name, tz.name));
    });
    let defaultLanguages = document.getElementById('default-languages').value;

    try {
        defaultLanguages = JSON.parse(defaultLanguages);
    } catch {
        defaultLanguages = [];
    }

    if (defaultLanguages.length) {
        languageSelect.val(defaultLanguages).trigger('change');
    }
}

// Call functions on page load
document.addEventListener("DOMContentLoaded", function () {
    loadLocation();
    loadLanguage();
});
