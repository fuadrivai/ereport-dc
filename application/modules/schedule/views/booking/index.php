<?php
$first_date_id = !empty($dates) ? $dates[0]['id'] : '';
$format_date = function ($value) { return date('D, d M Y', strtotime($value)); };
$format_time = function ($value) { return date('H:i', strtotime($value)); };
?>
<?php if (empty($dates)) { ?>
<h2 class="booking-section-title">Booking is currently unavailable</h2>
<p class="booking-lead">No booking dates are available for this report distribution yet.</p>
<?php } else { ?>
<form id="booking-form" action="<?= html_escape($submit_url) ?>" method="post"
    data-student-url="<?= html_escape($student_search_url) ?>"
    data-schedule-url="<?= html_escape(site_url('report-distribution/booking/schedule-data')) ?>"
    data-homeroom-conflict-url="<?= html_escape(site_url('report-distribution/booking/homeroom-conflict')) ?>"
    data-booking-status-url="<?= html_escape($booking_status_url) ?>">
    <input type="hidden" name="report_code" value="<?= html_escape($report['code']) ?>">
    <input type="hidden" name="session_id" id="session_id">
    <input type="hidden" name="booking_type" id="booking_type">

    <section class="wizard-panel active" data-step="1">
        <h2 class="booking-section-title">Welcome</h2>
        <p class="booking-lead">Please review the report distribution information before making your booking.</p>
        <h3><?= html_escape($report['title']) ?></h3>
        <div class="booking-meta">
            <div><strong>Academic Year</strong><?= html_escape($report['tahun_label']) ?></div>
            <div><strong>Semester</strong>Semester <?= html_escape($report['semester']) ?></div>
            <div><strong>Report Type</strong><?= html_escape($report['report_type']) ?> Report</div>
            <div><strong>Booking
                    Period</strong><?= html_escape(date('d M Y H:i', strtotime($report['booking_start_at']))) ?> -
                <?= html_escape(date('d M Y H:i', strtotime($report['booking_end_at']))) ?></div>
        </div>
        <?php if (!empty($report['description'])) { ?><p class="booking-description">
            <?= nl2br(html_escape($report['description'])) ?></p><?php } ?>
    </section>

    <section class="wizard-panel" data-step="2">
        <h2 class="booking-section-title">Student and Parent Details</h2>
        <label class="booking-label">Attendance Type</label>
        <div class="booking-type-grid" role="radiogroup">
            <label class="choice-card booking-type-card"><input type="radio" name="booking_type_choice"
                    value="THERAPY"><i class="fa fa-heart-o"></i><strong>Therapy</strong><small>With a
                    Psychologist from Mutiara Edu Sensory.</small></label>
            <label class="choice-card booking-type-card"><input type="radio" name="booking_type_choice"
                    value="NON_THERAPY"><i class="fa fa-users"></i><strong>Non Therapy</strong><small>Without a
                    Psychologist from Mutiara Edu Sensory.</small></label>
        </div>
        <label class="booking-label" for="student_id">Student</label>
        <select id="student_id" name="student_id" class="form-control" required disabled></select>
        <p class="booking-help">Choose an attendance type first, then select a student or search by name, NIS, or NISN.
        </p>
        <label class="booking-label" for="parent_name">Parent / Guardian Name</label>
        <input class="form-control" id="parent_name" name="parent_name" required>
        <label class="booking-label" for="parent_email">Parent / Guardian Email</label>
        <input class="form-control" id="parent_email" name="parent_email" type="email" required>
        <label class="booking-label" for="parent_phone">Parent / Guardian Phone</label>
        <input class="form-control" id="parent_phone" name="parent_phone" type="tel" required>
    </section>

    <section class="wizard-panel" data-step="3">
        <h2 class="booking-section-title">Choose Schedule</h2>
        <label class="booking-label">Preferred Date</label>
        <div class="date-grid" role="radiogroup">
            <?php foreach ($dates as $date) { ?>
            <label
                class="choice-card date-choice <?= (string) $date['id'] === (string) $first_date_id ? 'selected' : '' ?>">
                <input type="radio" name="date_id" value="<?= html_escape($date['id']) ?>"
                    data-target="date-panel-<?= html_escape($date['id']) ?>"
                    <?= (string) $date['id'] === (string) $first_date_id ? 'checked' : '' ?> required>
                <strong><?= html_escape($format_date($date['distribution_date'])) ?></strong><small><?= html_escape($date['label']) ?></small>
            </label>
            <?php } ?>
        </div>
        <label class="booking-label">Preferred Time</label>
        <div id="schedule-date-panels">
            <?php foreach ($dates as $date) { ?>
            <div class="date-panel <?= (string) $date['id'] === (string) $first_date_id ? 'active' : '' ?>"
                id="date-panel-<?= html_escape($date['id']) ?>">
                <div class="session-grid" role="radiogroup">
                    <?php foreach (isset($sessions_by_date[$date['id']]) ? $sessions_by_date[$date['id']] : array() as $session) {
                    $therapy_left = $session['therapy_capacity'] === null || $session['therapy_capacity'] === '' ? null : max(0, (int) $session['therapy_capacity'] - (int) $session['therapy_booked']);
                    $non_therapy_left = $session['non_therapy_capacity'] === null || $session['non_therapy_capacity'] === '' ? null : max(0, (int) $session['non_therapy_capacity'] - (int) $session['non_therapy_booked']);
                    $therapy_full = $therapy_left !== null && (int) $therapy_left <= 0;
                    $non_therapy_full = $non_therapy_left !== null && (int) $non_therapy_left <= 0;
                    $slot_is_full = $therapy_full && $non_therapy_full; ?>
                    <label
                        class="choice-card session-card session-choice <?= $slot_is_full ? 'session-full' : 'session-available' ?>">
                        <input type="radio" name="session_choice" value="<?= html_escape($session['id']) ?>"
                            data-session-id="<?= html_escape($session['id']) ?>"
                            data-therapy-left="<?= $therapy_left === null ? '' : $therapy_left ?>"
                            data-non-therapy-left="<?= $non_therapy_left === null ? '' : $non_therapy_left ?>"
                            <?= $slot_is_full ? 'disabled' : '' ?>>
                        <strong class="session-time"><?= html_escape($format_time($session['start_time'])) ?> -
                            <?= html_escape($format_time($session['end_time'])) ?></strong>
                        <small class="session-slot-meta">
                            <span class="slot-badge <?= $therapy_full ? 'slot-full' : 'slot-available' ?>">Therapy:
                                <?= $therapy_left === null ? 'Available' : $therapy_left . ' left' ?></span>
                            <span class="slot-separator">&middot;</span>
                            <span
                                class="slot-badge <?= $non_therapy_full ? 'slot-full' : 'slot-available' ?>">Non-therapy:
                                <?= $non_therapy_left === null ? 'Available' : $non_therapy_left . ' left' ?></span>
                        </small>
                    </label>
                    <?php } ?>
                </div>
                <?php if (empty($sessions_by_date[$date['id']])) { ?><p class="booking-help">No sessions are available
                    for
                    this date.</p><?php } ?>
            </div>
            <?php } ?>
        </div>
    </section>

    <section class="wizard-panel" data-step="4">
        <h2 class="booking-section-title">Confirm Your Booking</h2>
        <div class="booking-summary">
            <div class="summary-row"><span class="summary-label">Student</span><span class="summary-value"
                    id="summary-student">Not selected</span></div>
            <div class="summary-row"><span class="summary-label">Grade</span><span class="summary-value"
                    id="summary-grade">Not assigned</span></div>
            <div class="summary-row"><span class="summary-label">Homeroom Teacher</span><span class="summary-value"
                    id="summary-homeroom-teacher">Not assigned</span></div>
            <div class="summary-row"><span class="summary-label">Principal</span><span class="summary-value"
                    id="summary-principal">Not assigned</span></div>
            <div class="summary-row"><span class="summary-label">Parent / Guardian</span><span class="summary-value"
                    id="summary-parent">Not selected</span></div>
            <div class="summary-row"><span class="summary-label">Date</span><span class="summary-value"
                    id="summary-date">Not selected</span></div>
            <div class="summary-row"><span class="summary-label">Time</span><span class="summary-value"
                    id="summary-time">Not selected</span></div>
            <div class="summary-row"><span class="summary-label">Attendance Type</span><span class="summary-value"
                    id="summary-type">Not selected</span></div>
        </div>
    </section>

    <div class="booking-error" id="booking-error"></div>
    <div class="booking-actions"><button class="btn btn-back" id="wizard-back" type="button" style="display:none"><i
                class="fa fa-arrow-left"></i> Back</button><button class="btn btn-booking" id="wizard-next"
            type="button">Next <i class="fa fa-arrow-right"></i></button><button class="btn btn-booking"
            id="confirm-booking" type="submit" style="display:none"><i class="fa fa-check"></i> Confirm Booking</button>
    </div>
</form>
<script>
    $(function () {
        var currentStep = 1,
            $form = $('#booking-form'),
            $student = $('#student_id'),
            $scheduleDateGrid = $('.date-grid'),
            $scheduleDatePanels = $('#schedule-date-panels');

        function loadStudentOptions() {
            var bookingType = $('input[name="booking_type_choice"]:checked').val() || '';
            if (!bookingType) {
                $student.empty().prop('disabled', true).trigger('change');
                return;
            }

            $.getJSON($form.data('student-url'), {
                booking_type: bookingType,
                q: ''
            }, function (response) {
                var results = response && response.results ? response.results : [];
                $student.empty();
                $.each(results, function (_, student) {
                    $student.append(new Option(student.text, student.id, false, false));
                });
                $student.prop('disabled', false).trigger('change');
            }).fail(function () {
                $student.empty().prop('disabled', false);
            });
        }

        $student.select2({
            width: '100%',
            placeholder: 'Select or search student name, NIS, or NISN',
            minimumInputLength: 0,
            allowClear: true,
            ajax: {
                url: $form.data('student-url'),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        booking_type: $('input[name="booking_type_choice"]:checked').val() || ''
                    };
                },
                processResults: function (data) {
                    return data;
                }
            }
        });

        $student.prop('disabled', !$('input[name="booking_type_choice"]:checked').val());

        function selectedSession() {
            return $('input[name="session_choice"]:checked');
        }

        function escapeHtml(value) {
            return $('<div>').text(value === null || value === undefined ? '' : value).html();
        }

        function renderSchedule(response) {
            var dates = response.dates || [],
                sessions = response.sessions || [],
                bookingType = response.booking_type || '',
                sessionsByDate = {},
                dateMarkup = '',
                panelMarkup = '';

            $.each(sessions, function (_, session) {
                if (!sessionsByDate[session.date_id]) {
                    sessionsByDate[session.date_id] = [];
                }
                sessionsByDate[session.date_id].push(session);
            });

            $.each(dates, function (index, date) {
                var dateId = escapeHtml(date.id),
                    selected = index === 0 ? ' selected' : '',
                    checked = index === 0 ? ' checked' : '';
                dateMarkup += '<label class="choice-card date-choice' + selected + '">' +
                    '<input type="radio" name="date_id" value="' + dateId +
                    '" data-target="date-panel-' +
                    dateId + '"' + checked + ' required>' +
                    '<strong>' + escapeHtml(date.display_date) + '</strong><small>' + escapeHtml(date
                        .label) +
                    '</small></label>';
                panelMarkup += '<div class="date-panel' + (index === 0 ? ' active' : '') +
                    '" id="date-panel-' + dateId + '"><div class="session-grid" role="radiogroup">';

                if (!sessionsByDate[date.id] || !sessionsByDate[date.id].length) {
                    panelMarkup +=
                        '</div><p class="booking-help">No sessions are available for this date.</p></div>';
                    return;
                }

                $.each(sessionsByDate[date.id], function (_, session) {
                    var therapyFull = !!session.therapy_full,
                        nonTherapyFull = !!session.non_therapy_full,
                        homeroomConflict = !!session.homeroom_conflict,
                        slotIsFull = homeroomConflict || (bookingType === 'THERAPY' ?
                            therapyFull : nonTherapyFull),
                        therapyLeft = session.therapy_left === null ? 'Available' : escapeHtml(
                            session.therapy_left) +
                        ' left',
                        nonTherapyLeft = session.non_therapy_left === null ? 'Available' :
                        escapeHtml(session.non_therapy_left) + ' left',
                        sessionId = escapeHtml(session.id);
                    panelMarkup += '<label class="choice-card session-card session-choice ' +
                        (slotIsFull ? 'session-full' : 'session-available') + '">' +
                        '<input type="radio" name="session_choice" value="' + sessionId +
                        '" data-session-id="' + sessionId + '" data-therapy-left="' +
                        (session.therapy_left === null ? '' : escapeHtml(session
                            .therapy_left)) +
                        '" data-non-therapy-left="' +
                        (session.non_therapy_left === null ? '' : escapeHtml(session
                            .non_therapy_left)) +
                        '"' + (slotIsFull ? ' disabled' : '') + '>' +
                        '<strong class="session-time">' + escapeHtml(session.display_start) +
                        ' - ' +
                        escapeHtml(session.display_end) +
                        '</strong><small class="session-slot-meta">' +
                        '<span class="slot-badge ' + (therapyFull ? 'slot-full' :
                            'slot-available') +
                        '">Therapy: ' + therapyLeft +
                        '</span><span class="slot-separator">&middot;</span>' +
                        '<span class="slot-badge ' + (nonTherapyFull ? 'slot-full' :
                            'slot-available') +
                        '">Non-therapy: ' + nonTherapyLeft + '</span>' +
                        (homeroomConflict ?
                            '<span class="slot-badge slot-full">Homeroom teacher unavailable</span>' :
                            '') +
                        '</small></label>';
                });
                panelMarkup += '</div></div>';
            });

            $scheduleDateGrid.html(dateMarkup);
            $scheduleDatePanels.html(panelMarkup);
            $('input[name="session_choice"]').prop('checked', false);
            updateState();
        }

        function updateState() {
            var session = selectedSession(),
                type = $('input[name="booking_type_choice"]:checked').val() || '';
            $('#session_id').val(session.data('session-id') || '');
            $('#booking_type').val(type);
            $('#summary-student').text($student.find('option:selected').text() || 'Not selected');
            $('#summary-grade').text($student.data('student-grade') || 'Not assigned');
            $('#summary-homeroom-teacher').text($student.data('homeroom-teacher') || 'Not assigned');
            $('#summary-principal').text($student.data('principal-name') || 'Not assigned');
            $('#summary-parent').text($('#parent_name').val() || 'Not selected');
            $('#summary-type').text(type === 'THERAPY' ? 'Therapy' : (type === 'NON_THERAPY' ?
                'Without Therapy' : 'Not selected'));
            $('#summary-collection-method').text('Online');
            $('#summary-time').text(session.length ? session.closest('label').find('.session-time').text() :
                'Not selected');
            $('#summary-date').text($('input[name="date_id"]:checked').closest('label').find('strong').text() ||
                'Not selected');
        }

        function showStep(step) {
            currentStep = step;
            $('.wizard-panel').removeClass('active');
            $('.wizard-panel[data-step="' + step + '"]').addClass('active');
            $('.wizard-steps li').each(function (index) {
                $(this).toggleClass('active', index + 1 === step).toggleClass('complete', index + 1 <
                    step);
            });
            $('#wizard-back').toggle(step > 1);
            $('#wizard-next').toggle(step < 4);
            $('#confirm-booking').toggle(step === 4);
            updateState();
            window.scrollTo(0, 0);
        }

        function validStep() {
            if (currentStep === 2) {
                return !!$('input[name="booking_type_choice"]:checked').val() && !!$student.val() && $(
                        '#parent_name').val().trim() !== '' && $('#parent_email')[0]
                    .checkValidity() && $('#parent_phone').val().trim() !== '';
            }
            if (currentStep === 3) {
                var session = selectedSession(),
                    type = $('input[name="booking_type_choice"]:checked').val(),
                    left = type === 'THERAPY' ? session.data('therapy-left') : session.data('non-therapy-left');
                return session.length > 0 && (left === '' || parseInt(left, 10) > 0);
            }
            return true;
        }

        function validationMessage() {
            if (currentStep !== 3) {
                return 'Please complete the required fields before continuing.';
            }

            var session = selectedSession();
            if (!session.length) {
                return 'Please select a time slot before continuing.';
            }

            var type = $('input[name="booking_type_choice"]:checked').val(),
                left = type === 'THERAPY' ? session.data('therapy-left') : session.data('non-therapy-left');
            if (left !== '' && parseInt(left, 10) <= 0) {
                return type === 'THERAPY' ?
                    'This therapy time slot is full. Please choose another time slot.' :
                    'This time slot is full. Please choose another time slot.';
            }

            return 'Please complete the required fields before continuing.';
        }

        function setScheduleLoading(loading) {
            var $button = $('#wizard-next');
            $button.prop('disabled', loading).html(loading ?
                '<i class="fa fa-spinner fa-spin"></i> Loading...' :
                'Next <i class="fa fa-arrow-right"></i>');
        }

        function proceedToSchedule() {
            var bookingType = $('input[name="booking_type_choice"]:checked').val() || '';
            setScheduleLoading(true);
            $.getJSON($form.data('schedule-url'), {
                report_code: $form.find('[name="report_code"]').val(),
                student_id: $student.val(),
                booking_type: bookingType,
                _: new Date().getTime()
            }, function (scheduleResponse) {
                if (scheduleResponse.status !== 'ok') {
                    $('#booking-error').text(scheduleResponse.message ||
                        'Unable to load the booking schedule.').show();
                    setScheduleLoading(false);
                    return;
                }
                renderSchedule(scheduleResponse);
                $.get($form.data('booking-status-url'), {
                    report_code: $form.find('[name="report_code"]').val(),
                    student_id: $student.val()
                }, function (response) {
                    if (response.status !== 'ok') {
                        $('#booking-error').text(response.message ||
                            'Unable to check the student booking status.').show();
                        setScheduleLoading(false);
                        return;
                    }
                    if (response.has_active_booking) {
                        $('#booking-error').text(response.message).show();
                        setScheduleLoading(false);
                        return;
                    }
                    $('#booking-error').hide();
                    setScheduleLoading(false);
                    showStep(currentStep + 1);
                }, 'json').fail(function () {
                    $('#booking-error').text(
                            'Unable to check the student booking status. Please try again.')
                        .show();
                    setScheduleLoading(false);
                });
            }).fail(function () {
                $('#booking-error').text(
                    'Unable to load the booking schedule. Please try again.').show();
                setScheduleLoading(false);
            });
        }

        function proceedToConfirmation() {
            var session = selectedSession();
            $.getJSON($form.data('homeroom-conflict-url'), {
                report_code: $form.find('[name="report_code"]').val(),
                student_id: $student.val(),
                session_id: session.data('session-id'),
                _: new Date().getTime()
            }, function (response) {
                if (response.status !== 'ok') {
                    $('#booking-error').text(response.message ||
                        'Unable to check the selected session.').show();
                    return;
                }
                if (response.has_conflict) {
                    $('#booking-error').text(response.message).show();
                    return;
                }
                $('#booking-error').hide();
                showStep(currentStep + 1);
            }).fail(function () {
                $('#booking-error').text(
                    'Unable to check the selected session. Please try again.').show();
            });
        }

        $('#wizard-next').on('click', function () {
            if (!validStep()) {
                $('#booking-error').text(validationMessage())
                    .show();
                return;
            }
            if (currentStep === 2) {
                proceedToSchedule();
                return;
            }
            if (currentStep === 3) {
                proceedToConfirmation();
                return;
            }
            $('#booking-error').hide();
            showStep(currentStep + 1);
        });
        $('#wizard-back').on('click', function () {
            $('#booking-error').hide();
            showStep(currentStep - 1);
        });
        $form.on('change', 'input[name="date_id"]', function () {
            $('.date-choice').removeClass('selected');
            $(this).closest('.date-choice').addClass('selected');
            $('.date-panel').removeClass('active');
            $('#' + $(this).data('target')).addClass('active');
            $('input[name="session_choice"]').prop('checked', false).closest('.session-choice')
                .removeClass('selected');
            updateState();
        });
        $form.on('change', 'input[name="session_choice"]', function () {
            $('.session-choice').removeClass('selected');
            $(this).closest('.session-choice').addClass('selected');
            updateState();
        });
        $('input[name="booking_type_choice"]').on('change', function () {
            $('.booking-type-card').removeClass('selected');
            $(this).closest('.booking-type-card').addClass('selected');
            $student.removeData('student-grade');
            $student.removeData('homeroom-teacher');
            $student.removeData('principal-name');
            $student.val(null).trigger('change');
            loadStudentOptions();
            updateState();
        });
        $student.on('select2:select select2-selecting', function (event) {
            var student = event.params ? event.params.data : event.object;
            $student.data('student-grade', student && student.student_grade ? student.student_grade :
                'Not assigned');
            $student.data('homeroom-teacher', student && student.homeroom_teacher ? student
                .homeroom_teacher :
                'Not assigned');
            $student.data('principal-name', student && student.principal_name ? student.principal_name :
                'Not assigned');
        });
        $student.on('change', updateState);
        $('#parent_name').on('input', updateState);
        $form.on('submit', function (event) {
            event.preventDefault();
            if (!validStep()) {
                $('#booking-error').text('Please review your booking details.').show();
                return;
            }
            var $button = $('#confirm-booking');
            $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            $.post($form.attr('action'), $form.serialize(), function (response) {
                if (response.status === 'ok') {
                    window.location.href = response.redirect;
                    return;
                }
                $('#booking-error').text(response.message || 'Booking could not be completed.')
                    .show();
                $button.prop('disabled', false).html(
                    '<i class="fa fa-check"></i> Confirm Booking');
            }, 'json').fail(function () {
                $('#booking-error').text(
                    'Booking could not be completed. Please refresh and try again.').show();
                $button.prop('disabled', false).html(
                    '<i class="fa fa-check"></i> Confirm Booking');
            });
        });
        updateState();
    });
</script>
<?php } ?>