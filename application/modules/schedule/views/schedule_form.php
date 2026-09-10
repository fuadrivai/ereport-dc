<div class="card">
    <div class="header">
        <h4 class="title"><?= $data['mode'] === 'edit' ? 'Edit Report Distribution' : 'New Report Distribution' ?></h4>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <form class="form-horizontal" method="post" action="<?= base_url($url . '/simpan_schedule') ?>"
                    id="<?= $nama_form ?>">
                    <input type="hidden" name="_id" value="<?= html_escape($data['id']) ?>">
                    <input type="hidden" name="_mode" value="<?= html_escape($data['mode']) ?>">

                    <div class="form-group">
                        <label for="title" class="col-sm-3 control-label">Title</label>
                        <div class="col-sm-9"><input type="text" name="title" id="title" class="form-control"
                                value="<?= html_escape($data['title']) ?>" required></div>
                    </div>
                    <div class="form-group">
                        <label for="tahun_id" class="col-sm-3 control-label">Tahun</label>
                        <div class="col-sm-9">
                            <select name="tahun_id" id="tahun_id" class="form-control" required>
                                <option value="">Pilih Tahun</option>
                                <?php foreach ($p_tahun as $tahun) { ?>
                                <option value="<?= html_escape($tahun['id']) ?>"
                                    <?= (string) $data['tahun_id'] === (string) $tahun['id'] ? 'selected' : '' ?>>
                                    <?= html_escape($tahun['tahun']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="semester" class="col-sm-3 control-label">Semester</label>
                        <div class="col-sm-9">
                            <select name="semester" id="semester" class="form-control" required>
                                <option value="">Pilih Semester</option>
                                <option value="1" <?= $data['semester'] === '1' ? 'selected' : '' ?>>1</option>
                                <option value="2" <?= $data['semester'] === '2' ? 'selected' : '' ?>>2</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="report_type" class="col-sm-3 control-label">Report Type</label>
                        <div class="col-sm-9">
                            <select name="report_type" id="report_type" class="form-control" required>
                                <option value="">Pilih Report Type</option>
                                <option value="Mid" <?= $data['report_type'] === 'Mid' ? 'selected' : '' ?>>Mid</option>
                                <option value="Final" <?= $data['report_type'] === 'Final' ? 'selected' : '' ?>>Final
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-sm-3 control-label">Status</label>
                        <div class="col-sm-9">
                            <select name="status" id="status" class="form-control" required>
                                <option value="">Pilih Status</option>
                                <option value="active" <?= $data['status'] === 'active' ? 'selected' : '' ?>>Active
                                </option>
                                <option value="inactive" <?= $data['status'] === 'inactive' ? 'selected' : '' ?>>
                                    Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="booking_start_at" class="col-sm-3 control-label">Start Date</label>
                        <div class="col-sm-9"><input type="datetime-local" name="booking_start_at" id="booking_start_at"
                                class="form-control"
                                value="<?= html_escape(substr(str_replace(' ', 'T', $data['booking_start_at']), 0, 16)) ?>"
                                required></div>
                    </div>
                    <div class="form-group">
                        <label for="booking_end_at" class="col-sm-3 control-label">End Date</label>
                        <div class="col-sm-9"><input type="datetime-local" name="booking_end_at" id="booking_end_at"
                                class="form-control"
                                value="<?= html_escape(substr(str_replace(' ', 'T', $data['booking_end_at']), 0, 16)) ?>"
                                required></div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-sm-3 control-label">Description</label>
                        <div class="col-sm-9"><textarea name="description" id="description" class="form-control"
                                rows="3" required><?= html_escape($data['description']) ?></textarea></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url($url . '/report') ?>" class="btn btn-default">Kembali</a>
                </form>
            </div>

            <div class="col-md-5">
                <div class="panel panel-default">
                    <div class="panel-heading clearfix">
                        <strong>Report Distribution Dates</strong>
                        <button type="button" class="btn btn-success btn-xs pull-right" data-toggle="modal"
                            data-target="#modal_schedule_date" title="Add Schedule Date"
                            <?= empty($data['id']) ? 'disabled' : '' ?>>Add Schedule Date</button>
                    </div>
                    <div class="panel-body">
                        <?php if (empty($data['id'])) { ?>
                        <p class="text-muted">Save the report distribution before adding schedule dates.</p>
                        <?php } else { ?>
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Label</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($p_dates as $date) { ?>
                                <tr>
                                    <td colspan="4">
                                        <strong><?= html_escape($date['distribution_date']) ?></strong>
                                        <span class="text-muted">&middot; <?= html_escape($date['label']) ?></span>
                                        <span
                                            class="label label-<?= (int) $date['is_active'] === 1 ? 'success' : 'default' ?>">
                                            <?= (int) $date['is_active'] === 1 ? 'Active' : 'Inactive' ?></span>
                                        <button type="button" class="btn btn-xs btn-success pull-right btn-add-session"
                                            data-toggle="modal" data-target="#modal_schedule_session"
                                            data-date-id="<?= html_escape($date['id']) ?>"
                                            data-date-label="<?= html_escape($date['label']) ?>">Add Session</button>
                                        <button type="button" class="btn btn-xs btn-danger pull-right btn-delete-date"
                                            data-id="<?= html_escape($date['id']) ?>">Hapus</button>
                                        <?php if (!empty($p_sessions[$date['id']])) { ?>
                                        <table class="table table-condensed table-bordered" style="margin: 10px 0 0">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Start</th>
                                                    <th>End</th>
                                                    <th>Therapy</th>
                                                    <th>Non-therapy</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($p_sessions[$date['id']] as $session) { ?>
                                                <tr>
                                                    <td><?= html_escape($session['session_number']) ?></td>
                                                    <td><?= html_escape($session['start_time']) ?></td>
                                                    <td><?= html_escape($session['end_time']) ?></td>
                                                    <td><?= html_escape($session['therapy_capacity']) ?></td>
                                                    <td><?= html_escape($session['non_therapy_capacity']) ?></td>
                                                    <td><?= (int) $session['is_active'] === 1 ? 'Active' : 'Inactive' ?>
                                                    </td>
                                                    <td><button type="button"
                                                            class="btn btn-xs btn-danger btn-delete-session"
                                                            data-id="<?= html_escape($session['id']) ?>">Hapus</button>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                        <?php } else { ?>
                                        <p class="text-muted" style="margin: 10px 0 0">No sessions added.</p>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($data['id'])) { ?>
<div class="modal" id="modal_schedule_date">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Schedule Date</h4>
            </div>
            <form method="post" action="<?= base_url($url . '/simpan_schedule_date') ?>" id="f_schedule_date">
                <input type="hidden" name="report_distribution_id" value="<?= html_escape($data['id']) ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="distribution_date">Distribution Date</label>
                        <input type="date" name="distribution_date" id="distribution_date" class="form-control"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="label">Label</label>
                        <input type="text" name="label" id="label" class="form-control" required>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" name="is_active" value="1" checked> Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<?php if (!empty($data['id'])) { ?>
<div class="modal" id="modal_schedule_session">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Session</h4>
            </div>
            <form method="post" action="<?= base_url($url . '/simpan_schedule_session') ?>" id="f_schedule_session">
                <input type="hidden" name="report_distribution_date_id" id="session_date_id">
                <div class="modal-body">
                    <p class="text-muted" id="session_date_label"></p>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="session_number">Session Number</label>
                                <input type="number" name="session_number" id="session_number" class="form-control"
                                    min="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_time">Start Time</label>
                                <input type="time" name="start_time" id="start_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="end_time">End Time</label>
                                <input type="time" name="end_time" id="end_time" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="therapy_capacity">Therapy Capacity</label>
                                <input type="number" name="therapy_capacity" id="therapy_capacity" class="form-control"
                                    min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="non_therapy_capacity">Non-therapy Capacity</label>
                                <input type="number" name="non_therapy_capacity" id="non_therapy_capacity"
                                    class="form-control" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox" style="margin-top: 30px">
                                <label><input type="checkbox" name="is_active" value="1" checked> Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<script>
    $(function () {
        $('#<?= $nama_form ?>').on('submit', function () {
            $.post($(this).attr('action'), $(this).serialize(), function (response) {
                if (response.status === 'ok') {
                    window.location.href = '<?= base_url($url) ?>/report';
                } else {
                    noti('danger', response.data);
                }
            });
            return false;
        });

        $('#f_schedule_date').on('submit', function () {
            $.post($(this).attr('action'), $(this).serialize(), function (response) {
                if (response.status === 'ok') {
                    window.location.reload();
                } else {
                    noti('danger', response.data);
                }
            });
            return false;
        });

        $('.btn-delete-date').on('click', function () {
            if (!confirm('Apakah Anda yakin ingin menghapus schedule date ini?')) {
                return;
            }

            $.post('<?= base_url($url) ?>/delete_schedule_date', {
                id: $(this).data('id')
            }, function (response) {
                if (response.status === 'ok') {
                    window.location.reload();
                } else {
                    noti('danger', response.data);
                }
            });
        });

        $('.btn-add-session').on('click', function () {
            $('#session_date_id').val($(this).data('date-id'));
            $('#session_date_label').text('Date: ' + $(this).data('date-label'));
            $('#f_schedule_session')[0].reset();
            $('#session_date_id').val($(this).data('date-id'));
        });

        $('#f_schedule_session').on('submit', function () {
            $.post($(this).attr('action'), $(this).serialize(), function (response) {
                if (response.status === 'ok') {
                    window.location.reload();
                } else {
                    noti('danger', response.data);
                }
            });
            return false;
        });

        $('.btn-delete-session').on('click', function () {
            if (!confirm('Apakah Anda yakin ingin menghapus session ini?')) {
                return;
            }

            $.post('<?= base_url($url) ?>/delete_schedule_session', {
                id: $(this).data('id')
            }, function (response) {
                if (response.status === 'ok') {
                    window.location.reload();
                } else {
                    noti('danger', response.data);
                }
            });
        });
    });
</script>