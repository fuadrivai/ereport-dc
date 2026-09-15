<div class="card schedule-card">
    <div class="header">
        <h4 class="title">List Schedule</h4>
    </div>
    <div class="content">
        <div class="panel">
            <div class="panel-body">
                <a href="<?= base_url($url . '/edit/0') ?>" class="btn btn-success">Tambah</a>
            </div>
        </div>

        <table class="table table-hover table-striped schedule-table" id="tbl-schedule"
            style="width: 100%; table-layout: fixed;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Code</th>
                    <th>Tahun / Semester</th>
                    <th>Report Type</th>
                    <th>Title</th>
                    <th>Booking</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($p_report as $d) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= html_escape($d['code']) ?></td>
                    <td><?= html_escape($d['tahun_label']) ?><br>
                        <small>Semester <?= html_escape($d['semester']) ?></small></td>
                    <td><?= html_escape($d['report_type']) ?></td>
                    <td><?= html_escape($d['title']) ?></td>
                    <td><?= html_escape(date('d M Y H:i', strtotime($d['booking_start_at']))) ?><br>
                        <?= html_escape(date('d M Y H:i', strtotime($d['booking_end_at']))) ?></td>
                    <td><span
                            class="label label-<?= $d['status'] === 'PUBLISHED' ? 'success' : ($d['status'] === 'DRAFT' ? 'warning' : 'default') ?>">
                            <?= html_escape($d['status']) ?></span></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="<?= base_url($url . '/edit/' . html_escape($d['id'])) ?>"><i
                                            class="fa fa-edit"></i> Edit</a></li>
                                <li><a href="<?= html_escape(base_url('report-distribution/booking/' . $d['code'])) ?>"
                                        target="_blank" rel="noopener"><i class="fa fa-link"></i> Booking Page</a></li>
                                <li><a href="<?= base_url('schedule/registration-list/' . html_escape($d['id'])) ?>"><i
                                            class="fa fa-list"></i> Registered Students</a></li>
                                <li><a href="<?= base_url('schedule/booking-management/' . html_escape($d['id'])) ?>"><i
                                            class="fa fa-calendar-check-o"></i> Manage Bookings</a></li>
                                <li><a href="#modal_slots_<?= html_escape($d['id']) ?>" data-toggle="modal"><i
                                            class="fa fa-clock-o"></i> Slot</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="#" class="text-danger"
                                        onclick="return hapus('<?= html_escape($d['id']) ?>');"><i
                                            class="fa fa-remove"></i> Hapus</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php foreach ($p_report as $d) { ?>
<div class="modal fade" id="modal_slots_<?= html_escape($d['id']) ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title">Slot Schedule - <?= html_escape($d['title']) ?></h4>
            </div>
            <div class="modal-body">
                <?php $report_slots = isset($p_slots[$d['id']]) ? $p_slots[$d['id']] : array(); ?>
                <?php if (empty($report_slots)) { ?>
                <p class="text-muted">No schedule slots available.</p>
                <?php } else { ?>
                <?php foreach ($report_slots as $date_slot) { ?>
                <h5>
                    <strong><?= html_escape(date('d F Y', strtotime($date_slot['date']['distribution_date']))) ?></strong>
                    <span class="text-muted">&middot; <?= html_escape($date_slot['date']['date_label']) ?></span>
                    <span
                        class="label label-<?= (int) $date_slot['date']['date_active'] === 1 ? 'success' : 'default' ?>">
                        <?= (int) $date_slot['date']['date_active'] === 1 ? 'Active' : 'Inactive' ?></span>
                </h5>
                <?php if (empty($date_slot['sessions'])) { ?>
                <p class="text-muted">No slots added.</p>
                <?php } else { ?>
                <div class="table-responsive schedule-table-wrapper">
                    <table class="table table-condensed table-bordered schedule-table"
                        style="width: 100%; table-layout: fixed;">
                        <thead>
                            <tr>
                                <th>Slot</th>
                                <th>Time</th>
                                <th>Therapy Quota</th>
                                <th>Non-therapy Quota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($date_slot['sessions'] as $slot) {
                                $therapy_left = max(0, (int) $slot['therapy_capacity'] - (int) $slot['therapy_booked']);
                                $non_therapy_left = max(0, (int) $slot['non_therapy_capacity'] - (int) $slot['non_therapy_booked']);
                            ?>
                            <tr>
                                <td><?= html_escape($slot['session_number']) ?></td>
                                <td><?= html_escape(date('H:i', strtotime($slot['start_time']))) ?> -
                                    <?= html_escape(date('H:i', strtotime($slot['end_time']))) ?></td>
                                <td><?= html_escape($slot['therapy_booked']) ?> /
                                    <?= html_escape($slot['therapy_capacity']) ?> booked
                                    <strong>(<?= html_escape($therapy_left) ?> left)</strong></td>
                                <td><?= html_escape($slot['non_therapy_booked']) ?> /
                                    <?= html_escape($slot['non_therapy_capacity']) ?> booked
                                    <strong>(<?= html_escape($non_therapy_left) ?> left)</strong></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
                <?php } ?>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="modal" id="modal_delete">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Konfirmasi Hapus</h4>
            </div>
            <div class="modal-body"><input type="hidden" id="delete_id">
                <p>Apakah Anda yakin ingin menghapus schedule ini?</p>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default"
                    data-dismiss="modal">Batal</button><button type="button" class="btn btn-danger"
                    id="btn-delete-schedule">Hapus</button></div>
        </div>
    </div>
</div>

<style>
    .schedule-card .content {
        overflow: visible;
    }

    .schedule-card .dropdown-menu {
        z-index: 1060;
    }

    .schedule-table th,
    .schedule-table td {
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .schedule-table-wrapper {
        overflow-x: hidden;
    }
</style>

<script>
    function hapus(id) {
        $('#delete_id').val(id);
        $('#modal_delete').modal('show');
        return false;
    }

    $(function () {
        $('#tbl-schedule').DataTable();
        $('#btn-delete-schedule').on('click', function () {
            $.post('<?= base_url($url) ?>/delete_schedule', {
                id: $('#delete_id').val()
            }, function (r) {
                if (r.status == 'ok') {
                    window.location.reload();
                } else {
                    noti('danger', r.data);
                }
            });
        });
    });
</script>