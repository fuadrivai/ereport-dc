<div class="card schedule-card">
    <div class="header">
        <h4 class="title">Registered Students - <?= html_escape($report['title']) ?></h4>
        <p class="category"><?= html_escape($report['tahun_label']) ?> | Semester
            <?= html_escape($report['semester']) ?> | <?= html_escape($report['report_type']) ?></p>
    </div>
    <div class="content">
        <div class="panel">
            <div class="panel-body">
                <a href="<?= base_url('schedule/report') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i>
                    Back to Schedule</a>
                <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fa fa-print"></i>
                    Print</button>
            </div>
        </div>
        <?php if (empty($registration_dates)) { ?>
        <p class="text-muted">No active schedule sessions are available.</p>
        <?php } else { foreach ($registration_dates as $date) { ?>
        <div class="table-responsive registration-list-wrapper">
            <table class="table table-bordered registration-list-table">
                <thead>
                    <tr>
                        <th colspan="8" class="registration-date">
                            <?= html_escape(date('d F Y', strtotime($date['distribution_date']))) ?><?= !empty($date['date_label']) ? ' - ' . html_escape($date['date_label']) : '' ?>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4" class="registration-therapy">With Psychologist From Mutiara Edu Sensory
                            (<?= array_sum(array_map(function ($session) { return count($session['THERAPY']); }, $date['sessions'])) ?>)
                        </th>
                        <th colspan="4" class="registration-non-therapy">Without Therapist
                            (<?= array_sum(array_map(function ($session) { return count($session['NON_THERAPY']); }, $date['sessions'])) ?>)
                        </th>
                    </tr>
                    <tr>
                        <th>Students Name</th>
                        <th>Class Name</th>
                        <th>Meet</th>
                        <th>Session Schedule</th>
                        <th>Students Name</th>
                        <th>Class Name</th>
                        <th>Meet</th>
                        <th>Session Schedule</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($date['sessions'] as $session) {
                        $therapy = $session['THERAPY'];
                        $non_therapy = $session['NON_THERAPY'];
                        $total_rows = max(1, count($therapy), count($non_therapy));
                        for ($index = 0; $index < $total_rows; $index++) {
                            $therapy_booking = isset($therapy[$index]) ? $therapy[$index] : null;
                            $non_therapy_booking = isset($non_therapy[$index]) ? $non_therapy[$index] : null;
                    ?>
                    <tr>
                        <?php if (count($therapy) <= 1) { ?>
                        <?php if ($index === 0) { ?><td rowspan="<?= $total_rows ?>">
                            <?= $therapy_booking ? html_escape($therapy_booking['student_name']) : '<span class="text-muted">Available</span>' ?>
                        </td><?php } ?>
                        <?php } else { ?><td>
                            <?= $therapy_booking ? html_escape($therapy_booking['student_name']) : '' ?></td><?php } ?>
                        <td><?= $therapy_booking ? html_escape($therapy_booking['class_name']) : '' ?></td>
                        <td><?php if (!empty($therapy_booking['gmeet_link'])) { ?><a
                                class="btn btn-xs btn-primary open-gmeet-link"
                                href="<?= html_escape($therapy_booking['gmeet_link']) ?>" target="_blank" rel="noopener"
                                title="Open Google Meet"><i class="fa fa-video-camera"></i></a><button type="button"
                                class="btn btn-xs btn-default copy-gmeet-link"
                                data-link="<?= html_escape($therapy_booking['gmeet_link']) ?>"
                                title="Copy Google Meet link"><i class="fa fa-copy"></i></button><?php } ?></td>
                        <?php if ($index === 0) { ?><td rowspan="<?= $total_rows ?>" class="registration-session">Sesi
                            <?= html_escape($session['session_number']) ?><br><?= html_escape(date('H.i', strtotime($session['start_time']))) ?>-<?= html_escape(date('H.i', strtotime($session['end_time']))) ?>
                        </td><?php } ?>
                        <td><?= $non_therapy_booking ? html_escape($non_therapy_booking['student_name']) : ($index === 0 ? '<span class="text-muted">Available</span>' : '') ?>
                        </td>
                        <td><?= $non_therapy_booking ? html_escape($non_therapy_booking['class_name']) : '' ?></td>
                        <td><?php if (!empty($non_therapy_booking['gmeet_link'])) { ?><a
                                class="btn btn-xs btn-primary open-gmeet-link"
                                href="<?= html_escape($non_therapy_booking['gmeet_link']) ?>" target="_blank"
                                rel="noopener" title="Open Google Meet"><i class="fa fa-video-camera"></i></a><button
                                type="button" class="btn btn-xs btn-default copy-gmeet-link"
                                data-link="<?= html_escape($non_therapy_booking['gmeet_link']) ?>"
                                title="Copy Google Meet link"><i class="fa fa-copy"></i></button><?php } ?></td>
                        <?php if ($index === 0) { ?><td rowspan="<?= $total_rows ?>" class="registration-session">Sesi
                            <?= html_escape($session['session_number']) ?><br><?= html_escape(date('H.i', strtotime($session['start_time']))) ?>-<?= html_escape(date('H.i', strtotime($session['end_time']))) ?>
                        </td><?php } ?>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
        <?php } } ?>
    </div>
</div>

<style>
    .registration-list-wrapper {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: auto;
    }

    .registration-list-table {
        min-width: 900px;
        color: #000;
    }

    .registration-list-table th,
    .registration-list-table td {
        border-color: #222 !important;
        text-align: center;
        vertical-align: middle !important;
    }

    .registration-date {
        font-weight: 700;
        font-size: 15px;
    }

    .registration-therapy {
        font-weight: 700;
        background: #f6e6e6;
        color: #800000;
    }

    .registration-non-therapy {
        font-weight: 700;
        background: #e8f0f4;
        color: #28536b;
    }

    .registration-session {
        width: 12%;
        line-height: 1.3;
    }

    .open-gmeet-link,
    .copy-gmeet-link {
        min-width: 28px;
    }
</style>

<script>
    $(function () {
        $('.copy-gmeet-link').on('click', function () {
            var button = $(this),
                link = button.data('link');

            function copied() {
                button.find('i').removeClass('fa-copy').addClass('fa-check');
                setTimeout(function () {
                    button.find('i').removeClass('fa-check').addClass('fa-copy');
                }, 1500);
            }
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(link).then(copied);
                return;
            }
            var input = $('<textarea>').val(link).appendTo('body').select();
            document.execCommand('copy');
            input.remove();
            copied();
        });
    });
</script>