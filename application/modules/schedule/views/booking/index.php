<?php
$first_date_id = !empty($dates) ? $dates[0]['id'] : '';
$format_date = function ($value) {
    return date('D, d M Y', strtotime($value));
};
$format_time = function ($value) {
    return date('H:i', strtotime($value));
};
?>
<div class="booking-card booking-hero">
    <div class="booking-eyebrow">Report Distribution</div>
    <h1><?= html_escape($report['title']) ?></h1>
    <div class="booking-meta">
        <div><strong>Academic Year</strong><?= html_escape($report['tahun_label']) ?></div>
        <div><strong>Semester</strong>Semester <?= html_escape($report['semester']) ?></div>
        <div><strong>Report Type</strong><?= html_escape($report['report_type']) ?> Report</div>
    </div>
    <?php if (!empty($report['description'])) { ?>
    <p class="booking-description"><?= nl2br(html_escape($report['description'])) ?></p>
    <?php } ?>
</div>

<?php if (empty($dates)) { ?>
<div class="booking-card booking-card-body">
    <h2 class="booking-section-title">Booking is currently unavailable</h2>
    <p class="booking-help">No booking dates are available for this report distribution yet.</p>
</div>
<?php } else { ?>
<form id="booking-form" action="<?= html_escape($submit_url) ?>" method="post">
    <input type="hidden" name="report_code" value="<?= html_escape($report['code']) ?>">
    <input type="hidden" name="session_id" id="session_id">
    <input type="hidden" name="booking_type" id="booking_type">

    <div class="booking-card booking-card-body">
        <h2 class="booking-section-title"><i class="fa fa-user-o"></i> Choose Student</h2>
        <label class="booking-label" for="student_id">Student</label>
        <select id="student_id" name="student_id" class="form-control" required></select>
        <div class="booking-help">Search by student name, NIS, or NISN.</div>
    </div>

    <div class="booking-card booking-card-body">
        <h2 class="booking-section-title"><i class="fa fa-calendar-o"></i> Choose Your Preferred Date</h2>
        <div class="date-grid" role="radiogroup" aria-label="Choose date">
            <?php foreach ($dates as $date) { ?>
            <label
                class="choice-card date-choice <?= (string) $date['id'] === (string) $first_date_id ? 'selected' : '' ?>">
                <input type="radio" name="date_id" value="<?= html_escape($date['id']) ?>"
                    data-target="date-panel-<?= html_escape($date['id']) ?>"
                    <?= (string) $date['id'] === (string) $first_date_id ? 'checked' : '' ?> required>
                <strong><?= html_escape($format_date($date['distribution_date'])) ?></strong>
                <small><?= html_escape($date['label']) ?></small>
            </label>
            <?php } ?>
        </div>
    </div>

    <div class="booking-card booking-card-body">
        <h2 class="booking-section-title"><i class="fa fa-clock-o"></i> Choose Your Time</h2>
        <?php foreach ($dates as $date) { ?>
        <div class="date-panel <?= (string) $date['id'] === (string) $first_date_id ? 'active' : '' ?>"
            id="date-panel-<?= html_escape($date['id']) ?>">
            <div class="session-grid" role="radiogroup" aria-label="Choose session">
                <?php foreach (isset($sessions_by_date[$date['id']]) ? $sessions_by_date[$date['id']] : array() as $session) {
                    $therapy_capacity = $session['therapy_capacity'];
                    $non_therapy_capacity = $session['non_therapy_capacity'];
                    $therapy_left = $therapy_capacity === null || $therapy_capacity === ''
                        ? null : max(0, (int) $therapy_capacity - (int) $session['therapy_booked']);
                    $non_therapy_left = $non_therapy_capacity === null || $non_therapy_capacity === ''
                        ? null : max(0, (int) $non_therapy_capacity - (int) $session['non_therapy_booked']);
                ?>
                <label class="choice-card session-card session-choice">
                    <input type="radio" name="session_choice" value="<?= html_escape($session['id']) ?>"
                        data-session-id="<?= html_escape($session['id']) ?>"
                        data-therapy-left="<?= $therapy_left === null ? '' : $therapy_left ?>"
                        data-non-therapy-left="<?= $non_therapy_left === null ? '' : $non_therapy_left ?>">
                    <strong class="session-time"><?= html_escape($format_time($session['start_time'])) ?> -
                        <?= html_escape($format_time($session['end_time'])) ?></strong>
                    <small class="session-capacity">
                        Therapy: <?= $therapy_left === null ? 'Available' : $therapy_left . ' slot(s) left' ?>
                        &middot; Non-therapy:
                        <?= $non_therapy_left === null ? 'Available' : $non_therapy_left . ' slot(s) left' ?>
                    </small>
                </label>
                <?php } ?>
            </div>
            <?php if (empty($sessions_by_date[$date['id']])) { ?>
            <p class="booking-help">No sessions are available for this date.</p>
            <?php } ?>
        </div>
        <?php } ?>
    </div>

    <div class="booking-card booking-card-body">
        <h2 class="booking-section-title"><i class="fa fa-hand-pointer-o"></i> How Will Your Child Attend?</h2>
        <div class="booking-type-grid" role="radiogroup" aria-label="Booking type">
            <label class="choice-card booking-type-card">
                <input type="radio" name="booking_type_choice" value="THERAPY">
                <i class="fa fa-heart-o"></i>
                <strong>Therapy</strong>
                <small>This session is limited by therapy capacity.</small>
            </label>
            <label class="choice-card booking-type-card">
                <input type="radio" name="booking_type_choice" value="NON_THERAPY">
                <i class="fa fa-users"></i>
                <strong>Without Therapy</strong>
                <small>Attend the report distribution without therapy.</small>
            </label>
        </div>
        <p class="booking-help" id="therapy-help" style="display:none">This session is limited to one student for
            therapy.</p>
    </div>

    <div class="booking-card booking-card-body">
        <h2 class="booking-section-title"><i class="fa fa-list-alt"></i> Booking Summary</h2>
        <div class="booking-summary">
            <div class="summary-row"><span class="summary-label">Student</span><span class="summary-value"
                    id="summary-student">Not selected</span></div>
            <div class="summary-row"><span class="summary-label">Date</span><span class="summary-value"
                    id="summary-date">Not selected</span></div>
            <div class="summary-row"><span class="summary-label">Time</span><span class="summary-value"
                    id="summary-time">Not selected</span></div>
            <div class="summary-row"><span class="summary-label">Attendance</span><span class="summary-value"
                    id="summary-type">Not selected</span></div>
            <div class="summary-row"><span class="summary-label">Therapist</span><span class="summary-value">Not
                    assigned yet</span></div>
        </div>
        <div class="booking-error" id="booking-error"></div>
        <button type="submit" class="btn btn-booking btn-block" id="confirm-booking" disabled>
            <i class="fa fa-check"></i> Confirm Booking
        </button>
    </div>
</form>

<script>
    $(function () {
        var $form = $('#booking-form');
        var $student = $('#student_id');
        var $session = $('#session_id');
        var $type = $('#booking_type');
        var $button = $('#confirm-booking');

        $student.select2({
            width: '100%',
            placeholder: 'Search student name, NIS, or NISN',
            minimumInputLength: 2,
            ajax: {
                url: < ? = json_encode($student_search_url) ? > ,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return data;
                }
            }
        });

        function selectedSession() {
            return $('input[name="session_choice"]:checked');
        }

        function updateState() {
            var session = selectedSession();
            var type = $('input[name="booking_type_choice"]:checked').val() || '';
            var hasStudent = !!$student.val();
            var therapyLeft = session.data('therapy-left');
            var nonTherapyLeft = session.data('non-therapy-left');
            var capacityAvailable = type === 'THERAPY' ?
                (therapyLeft === '' || parseInt(therapyLeft, 10) > 0) :
                (nonTherapyLeft === '' || parseInt(nonTherapyLeft, 10) > 0);

            $session.val(session.data('session-id') || '');
            $type.val(type);
            $('#therapy-help').toggle(type === 'THERAPY');
            $button.prop('disabled', !(hasStudent && session.length && type && capacityAvailable));
            $('#summary-student').text($student.find('option:selected').text() || 'Not selected');
            $('#summary-type').text(type === 'THERAPY' ? 'Therapy' : (type === 'NON_THERAPY' ?
                'Without Therapy' : 'Not selected'));
            $('#summary-time').text(session.length ? session.closest('label').find('.session-time').text() :
                'Not selected');
            $('#summary-date').text($('input[name="date_id"]:checked').closest('label').find('strong').text() ||
                'Not selected');
        }

        $('input[name="date_id"]').on('change', function () {
            $('.date-choice').removeClass('selected');
            $(this).closest('.date-choice').addClass('selected');
            $('.date-panel').removeClass('active');
            $('#' + $(this).data('target')).addClass('active');
            $('input[name="session_choice"]').prop('checked', false).closest('.session-choice')
                .removeClass('selected');
            updateState();
        });

        $('input[name="session_choice"]').on('change', function () {
            $('.session-choice').removeClass('selected');
            $(this).closest('.session-choice').addClass('selected');
            updateState();
        });

        $('input[name="booking_type_choice"]').on('change', function () {
            $('.booking-type-card').removeClass('selected');
            $(this).closest('.booking-type-card').addClass('selected');
            updateState();
        });

        $student.on('change', updateState);
        updateState();

        $form.on('submit', function (event) {
            event.preventDefault();
            $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            $('#booking-error').hide().text('');
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
    });
</script>
<?php } ?>