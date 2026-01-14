$(document).ready(function () {

    /* --------------------------------------------------------
     Initial Setup
    ---------------------------------------------------------*/
    let timeZones = [];
    const counselorIdInput = document.getElementById('counselor_id');
    const counselorId = counselorIdInput.value;

    const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    let availability = initializeAvailability();
    let selectedTimezone = "Australia/Sydney";

    /* --------------------------------------------------------
     Load Timezones JSON (public file)
    ---------------------------------------------------------*/
    fetch('/timezones.json')
        .then(response => response.json())
        .then(data => {
            timeZones = data.timezones;
            initializeTimezoneList();
        })
        .catch(err => console.error("Error fetching timezones:", err));

    /* --------------------------------------------------------
     Fetch Availability from Server
    ---------------------------------------------------------*/
    fetchAvailability();

    function fetchAvailability() {
        $.ajax({
            url: '/counsellor/get-vailability?counselorId=' + counselorId,
            method: 'GET',
            success: function (response) {

                // Load available time slots
                if (response.availability) {
                    response.availability.forEach(item => {
                        const day = days[item.day_of_week];

                        availability[day].available = true;
                        availability[day].start = item.start_time;
                        availability[day].end = item.end_time;
                    });
                }

                // Load timezone properly
                if (response.timezone) {
                    selectedTimezone = response.timezone;
                    $('#selected-timezone').text(
                        getTimezoneDisplay(response.timezone)
                    );
                }

                renderSchedule();
            },
            error: function (xhr) {
                console.error('Availability fetch failed:', xhr.responseText);
                alert("Error fetching availability data.");
            }
        });
    }

    /* --------------------------------------------------------
     Initialize empty availability structure
    ---------------------------------------------------------*/
    function initializeAvailability() {
        let obj = {};
        days.forEach(day => {
            obj[day] = {
                available: false,
                start: '',
                end: ''
            };
        });
        return obj;
    }

    /* --------------------------------------------------------
     Convert timezone ID → Display name
    ---------------------------------------------------------*/
    function getTimezoneDisplay(tzId) {
        const tz = timeZones.find(t => t.name === tzId || t.id === tzId);
        return tz ? tz.name : tzId;
    }

    /* --------------------------------------------------------
     Render full weekly schedule
    ---------------------------------------------------------*/
    function renderSchedule() {
        const container = $('#availability-container');
        container.empty();

        days.forEach(day => {
            container.append(renderDayRow(day));
        });
    }

    /* --------------------------------------------------------
     Build Timezone List
    ---------------------------------------------------------*/
    function initializeTimezoneList() {
        const list = $('#timezone-list');
        list.empty();

        timeZones.forEach(tz => {
            list.append(
                $('<div>')
                    .addClass('timezone-item')
                    .text(tz.name)
                    .data('tz-id', tz.name)
                    .on('click', function () {
                        selectTimezone($(this).data('tz-id'), $(this).text());
                    })
            );
        });
    }

    function selectTimezone(tzId, display) {

        selectedTimezone = tzId;
        const counselorId = document.getElementById('counselor_id').value;
        $('#selected-timezone').text(display);
        $.ajax({
            url: '/counsellor/save-data',
            type: 'POST',
            data: {
                counselorId: counselorId,
                value: selectedTimezone,
                key: 'timezone',
                _token: $('meta[name="csrf-token"]').attr(
                    'content'),
            },
            success: function (response) {
                toastr.success("TimeZone updated successfully");
                $('#timezoneModal').modal('hide');


            },
            error: function (xhr, status, error) {
                toastr.error("Failed to update time zone. Please try again");
            }
        });
    }

    /* --------------------------------------------------------
     Render Day Row (Bootstrap + jQuery only)
    ---------------------------------------------------------*/
    function renderDayRow(day) {
        const dayData = availability[day];

        const row = $('<div>')
            .addClass('day-row d-flex align-items-center')
            .data('day', day);

        const indicator = $('<div>')
            .addClass('availability-indicator')
            .css(dayData.available ? activeIndicatorCss() : inactiveIndicatorCss())
            .on('click', function () {
                toggleAvailability(day, $(this), row);
            });

        const dayLabel = $('<span>').addClass('day-label me-3').text(day);

        const timeContainer = createTimeContainer(day);

        const unavailableText = $('<span>')
            .addClass('unavailable-text ' + (dayData.available ? 'd-none' : ''))
            .text('Unavailable');

        if (dayData.available) {
            timeContainer.removeClass('d-none');
        }

        row.append(indicator, dayLabel, timeContainer, unavailableText);
        return row;
    }

    function activeIndicatorCss() {
        return {
            backgroundColor: '#688EDC',
            boxShadow: '0 2px 4px #688EDC',
            transform: 'scale(1)'
        };
    }

    function inactiveIndicatorCss() {
        return {
            backgroundColor: '#e5e7eb',
            boxShadow: 'none',
            transform: 'scale(0.95)'
        };
    }

    /* --------------------------------------------------------
     Toggle Availability for a Day
    ---------------------------------------------------------*/
    function toggleAvailability(day, indicator, row) {
        const dayData = availability[day];

        dayData.available = !dayData.available;

        indicator.css(dayData.available ? activeIndicatorCss() : inactiveIndicatorCss());

        const timeContainer = row.find('.time-container');
        const unavailableText = row.find('.unavailable-text');

        if (dayData.available) {
            dayData.start = "09:00";
            dayData.end = "17:00";

            unavailableText.addClass('d-none');
            timeContainer.removeClass('d-none');
        } else {
            unavailableText.removeClass('d-none');
            timeContainer.addClass('d-none');
        }
    }

    /* --------------------------------------------------------
     Create Time Inputs for a Day
    ---------------------------------------------------------*/
    function createTimeContainer(day) {
        const dayData = availability[day];

        const container = $('<div>')
            .addClass('time-container d-flex d-none');

        const startInput = $('<input>')
            .attr('type', 'time')
            .addClass('form-control time-input mx-1')
            .val(dayData.start)
            .on('change', function () {
                const newVal = $(this).val();
                if (newVal >= dayData.end) {
                    alert("Start time must be before end time");
                    $(this).val(dayData.start);
                    return;
                }
                dayData.start = newVal;
            });

        const endInput = $('<input>')
            .attr('type', 'time')
            .addClass('form-control time-input mx-1')
            .val(dayData.end)
            .on('change', function () {
                const newVal = $(this).val();
                if (newVal <= dayData.start) {
                    alert("End time must be after start time");
                    $(this).val(dayData.end);
                    return;
                }
                dayData.end = newVal;
            });

        container.append(startInput, $('<span>').addClass('mx-3 mt-2').text('–'), endInput);
        return container;
    }

    /* --------------------------------------------------------
     Prepare Data for API
    ---------------------------------------------------------*/
    function formatAvailabilityData() {
        const out = {
            counselorId: counselorId,
            timezone: selectedTimezone,
            availability: []
        };

        days.forEach((day, idx) => {
            if (availability[day].available) {
                out.availability.push({
                    day_of_week: idx,
                    start_time: availability[day].start,
                    end_time: availability[day].end
                });
            }
        });

        return out;
    }

    /* --------------------------------------------------------
     Save Button
    ---------------------------------------------------------*/
    $('#saveButton').on('click', function () {
        const btn = $(this);

        btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: '/counsellor/availability-save',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType: 'application/json',
            data: JSON.stringify(formatAvailabilityData()),

            success: function () {
                btn.html('✓ Saved Successfully')
                    .addClass('btn-success')
                    .removeClass('btn-primary');

                setTimeout(() => {
                    btn.html('Save Changes')
                        .removeClass('btn-success')
                        .addClass('btn-primary')
                        .prop('disabled', false);
                }, 1800);
            },

            error: function (xhr) {
                console.error("Save error:", xhr.responseText);
                btn.html('Save Changes').prop('disabled', false);
            }
        });
    });

});
