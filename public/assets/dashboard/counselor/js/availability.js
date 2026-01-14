document.addEventListener("DOMContentLoaded", () => {

    /* ------------------------------------------------------------------
       ELEMENTS
    ------------------------------------------------------------------ */
    const availabilityContainer = document.getElementById("availability-container");
    const saveButton = document.getElementById("saveButton");
    const selectedTimezoneLabel = document.getElementById("selected-timezone");
    const timezoneList = document.getElementById("timezone-list");
    const timezoneSearch = document.getElementById("timezone-search");
    const timezoneModal = document.getElementById("timezoneModal");

    /* ------------------------------------------------------------------
       GLOBAL STATE
    ------------------------------------------------------------------ */
    const days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
    let timezoneData = [];
    const counselorIdInput = document.getElementById('counselor_id');
    const counselorId = counselorIdInput.value;
    let selectedTimezone = selectedTimezoneLabel?.textContent || "Australia/Sydney";

    let availability = initializeAvailability();

    /* ------------------------------------------------------------------
       LOAD TIMEZONES WHEN MODAL OPENS
    ------------------------------------------------------------------ */
    if (timezoneModal) {
        timezoneModal.addEventListener("show.bs.modal", () => {
            if (timezoneData.length === 0) {
                fetch("/mw-1/timezones.json")
                    .then(res => res.json())
                    .then(data => {
                        timezoneData = data.timezones || [];
                        renderTimezoneList(timezoneData.slice(0, 10));
                    })
                    .catch(err => console.error("Error loading timezones:", err));
            }
        });
    }

    function renderTimezoneList(list) {
        timezoneList.innerHTML = "";
        list.forEach(tz => {
            let item = document.createElement("div");
            item.className = "timezone-item";
            item.textContent = tz.name;

            item.onclick = () => selectTimezone(tz.name);

            timezoneList.appendChild(item);
        });
    }

    timezoneSearch?.addEventListener("input", () => {
        const term = timezoneSearch.value.toLowerCase();
        renderTimezoneList(
            timezoneData.filter(t => t.name.toLowerCase().includes(term))
        );
    });

    function selectTimezone(tzName) {
        selectedTimezone = tzName;
        selectedTimezoneLabel.textContent = tzName;

        bootstrap.Modal.getInstance(timezoneModal).hide();

        toastr.success("Timezone updated");
    }

    /* ------------------------------------------------------------------
       INITIAL AVAILABILITY STRUCTURE
    ------------------------------------------------------------------ */
    function initializeAvailability() {
        const base = {};
        days.forEach(day => {
            base[day] = {
                available: false,
                start: "",
                end: ""
            };
        });
        return base;
    }

    /* ------------------------------------------------------------------
       FETCH COUNSELLOR AVAILABILITY
    ------------------------------------------------------------------ */
    fetch(`/counsellor/get-vailability?counselorId=${counselorId}`)
        .then(res => res.json())
        .then(data => {
            if (data.availability) {
                data.availability.forEach(item => {
                    let day = days[item.day_of_week];
                    availability[day].available = true;
                    availability[day].start = item.start_time;
                    availability[day].end = item.end_time;
                });
            }

            if (data.timezone) {
                selectedTimezone = data.timezone;
                selectedTimezoneLabel.textContent = data.timezone;
            }

            renderSchedule();
        })
        .catch(err => console.error("Failed to fetch availability:", err));

    /* ------------------------------------------------------------------
       RENDER DAILY AVAILABILITY ROWS
    ------------------------------------------------------------------ */
    function renderSchedule() {
        availabilityContainer.innerHTML = "";
        days.forEach(day => {
            availabilityContainer.appendChild(renderDayRow(day));
        });
    }

    function renderDayRow(day) {
        const dayData = availability[day];

        const row = document.createElement("div");
        row.className = "day-row d-flex align-items-center";

        // clickable toggle button
        const indicator = document.createElement("div");
        indicator.className = "availability-indicator";
        applyIndicatorStyle(indicator, dayData.available);

        indicator.addEventListener("click", () => {
            dayData.available = !dayData.available;
            applyIndicatorStyle(indicator, dayData.available);

            if (dayData.available) {
                dayData.start = dayData.start || "09:00";
                dayData.end = dayData.end || "17:00";

                unavailableText.classList.add("d-none");
                timeContainer.classList.remove("d-none");
            } else {
                unavailableText.classList.remove("d-none");
                timeContainer.classList.add("d-none");
            }
        });

        // Day label
        const dayLabel = document.createElement("span");
        dayLabel.className = "day-label me-3";
        dayLabel.textContent = day;

        // Time container
        const timeContainer = createTimeInputs(day);
        if (!dayData.available) timeContainer.classList.add("d-none");

        // Unavailable text
        const unavailableText = document.createElement("span");
        unavailableText.className = "unavailable-text" + (dayData.available ? " d-none" : "");
        unavailableText.textContent = "Unavailable";

        row.append(indicator, dayLabel, timeContainer, unavailableText);
        return row;
    }

    /* ------------------------------------------------------------------
       TIME INPUTS
    ------------------------------------------------------------------ */
    function createTimeInputs(day) {
        const wrap = document.createElement("div");
        wrap.className = "time-container d-flex";

        const start = document.createElement("input");
        start.type = "time";
        start.className = "form-control time-input mx-1";
        start.value = availability[day].start;

        start.onchange = () => {
            if (start.value >= availability[day].end) {
                alert("Start time must be before end time.");
                start.value = availability[day].start;
                return;
            }
            availability[day].start = start.value;
        };

        const dash = document.createElement("span");
        dash.className = "mx-3 mt-2";
        dash.textContent = "–";

        const end = document.createElement("input");
        end.type = "time";
        end.className = "form-control time-input mx-1";
        end.value = availability[day].end;

        end.onchange = () => {
            if (end.value <= availability[day].start) {
                alert("End time must be after start time.");
                end.value = availability[day].end;
                return;
            }
            availability[day].end = end.value;
        };

        wrap.append(start, dash, end);
        return wrap;
    }

    /* ------------------------------------------------------------------
       SAVE AVAILABILITY
    ------------------------------------------------------------------ */
    saveButton.addEventListener("click", () => {
        saveButton.disabled = true;
        saveButton.innerHTML =
            `<span class="spinner-border spinner-border-sm me-2"></span>Saving...`;

        const payload = formatPayload();

        fetch("/counsellor/availability-save", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(() => {
                saveButton.classList.remove("btn-primary");
                saveButton.classList.add("btn-success");
                saveButton.innerHTML = "✓ Saved Successfully";

                setTimeout(() => {
                    saveButton.disabled = false;
                    saveButton.innerHTML = "Save Changes";
                    saveButton.classList.add("btn-primary");
                    saveButton.classList.remove("btn-success");
                }, 1500);
            })
            .catch(err => {
                console.error("Save failed:", err);
                toastr.error("Failed to save availability");
                saveButton.disabled = false;
                saveButton.innerHTML = "Save Changes";
            });
    });

    function formatPayload() {
        const payload = {
            timezone: selectedTimezone,
            counselorId: counselorId,
            availability: []
        };

        days.forEach((day, index) => {
            if (availability[day].available) {
                payload.availability.push({
                    day_of_week: index,
                    start_time: availability[day].start,
                    end_time: availability[day].end
                });
            }
        });

        return payload;
    }

    /* ------------------------------------------------------------------
       HELPER STYLE FUNCTION
    ------------------------------------------------------------------ */
    function applyIndicatorStyle(indicator, active) {
        if (active) {
            indicator.style.background = "#688EDC";
            indicator.style.boxShadow = "0 2px 4px #688EDC";
            indicator.style.transform = "scale(1)";
        } else {
            indicator.style.background = "#e5e7eb";
            indicator.style.boxShadow = "none";
            indicator.style.transform = "scale(0.95)";
        }
    }
});
