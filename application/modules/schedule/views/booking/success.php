<?php
$format_date = date('d F Y', strtotime($booking['distribution_date']));
$format_start = date('H:i', strtotime($booking['start_time']));
$format_end = date('H:i', strtotime($booking['end_time']));
?>
<div class="booking-card booking-card-body booking-confirmation">
    <div class="booking-success-icon"><i class="fa fa-check"></i></div>
    <h1>Booking Confirmed</h1>
    <p class="booking-help">Your report distribution booking has been successfully registered.</p>

    <div class="booking-summary" style="margin-top:24px; text-align:left">
        <div class="summary-row"><span class="summary-label">Student</span><span
                class="summary-value"><?= html_escape($booking['student_name']) ?></span></div>
        <?php if (!empty($booking['student_nis'])) { ?>
        <div class="summary-row"><span class="summary-label">Student ID</span><span
                class="summary-value"><?= html_escape($booking['student_nis']) ?></span></div>
        <?php } ?>
        <div class="summary-row"><span class="summary-label">Report</span><span
                class="summary-value"><?= html_escape($booking['title']) ?></span></div>
        <div class="summary-row"><span class="summary-label">Date</span><span
                class="summary-value"><?= html_escape($format_date) ?></span></div>
        <div class="summary-row"><span class="summary-label">Time</span><span
                class="summary-value"><?= html_escape($format_start . ' - ' . $format_end) ?></span></div>
        <div class="summary-row"><span class="summary-label">Attendance</span><span
                class="summary-value"><?= $booking['booking_type'] === 'THERAPY' ? 'Therapy' : 'Without Therapy' ?></span>
        </div>
        <div class="summary-row"><span class="summary-label">Therapist</span><span class="summary-value">Not assigned
                yet</span></div>
    </div>

    <p class="booking-help" style="margin-top:24px">Please keep this booking code for your records.</p>
    <div class="booking-code"><?= html_escape($booking['booking_code']) ?></div>
</div>