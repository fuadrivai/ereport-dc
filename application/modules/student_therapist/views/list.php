<div class="card">
    <div class="header">
        <h4 class="title">Mapping Student Therapist</h4>
    </div>
    <div class="content">
        <div class="panel">
            <div class="panel-body">
                <button type="button" class="btn btn-success"
                    onclick="window.location.href = base_url + '<?= $url ?>/form'; return false;">
                    <i class="fa fa-plus"></i> Tambah Mapping
                </button>
            </div>
        </div>

        <table class="table table-hover table-striped" id="datatabel" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Student</th>
                    <th>Tahun</th>
                    <th>Therapist</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal" id="modal_data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title">Mapping Student Therapist</h4>
            </div>
            <form method="post" id="<?= html_escape($nama_form) ?>">
                <input type="hidden" name="_id" id="_id">
                <input type="hidden" name="_mode" id="_mode">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="student_id">Student</label>
                        <select name="student_id" id="student_id" class="form-control" required>
                            <option value="">Pilih Student</option>
                            <?php foreach ($p_students as $student) {
                                $identifier = $student['nis'] !== '' ? $student['nis'] : $student['nisn'];
                            ?>
                            <option value="<?= html_escape($student['id']) ?>">
                                <?= html_escape($student['nama'] . ' (' . $identifier . ')') ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tahun_id">Tahun</label>
                        <select name="tahun_id" id="tahun_id" class="form-control" required>
                            <option value="">Pilih Tahun</option>
                            <?php foreach ($p_tahun as $tahun) { ?>
                            <option value="<?= html_escape($tahun['id']) ?>">
                                <?= html_escape($tahun['tahun']) ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="therapist_id">Therapist</label>
                        <select name="therapist_id" id="therapist_id" class="form-control" required>
                            <option value="">Pilih Therapist</option>
                            <?php foreach ($p_therapists as $therapist) { ?>
                            <option value="<?= html_escape($therapist['id']) ?>">
                                <?= html_escape($therapist['name']) ?><?= $therapist['specialization'] !== '' ? ' - ' . html_escape($therapist['specialization']) : '' ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" name="is_active" id="is_active" value="1"> Active</label>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
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

<script>
    $(function () {
        pagination('datatabel', base_url + '<?= $url ?>/datatable', []);
        $('#student_id, #tahun_id, #therapist_id').select2({
            dropdownParent: $('#modal_data'),
            width: '100%'
        });

        $('#<?= html_escape($nama_form) ?>').on('submit', function () {
            $.post(base_url + '<?= $url ?>/simpan', $(this).serialize(), function (response) {
                if (response.status === 'ok') {
                    $('#modal_data').modal('hide');
                    noti('success', response.data);
                    pagination('datatabel', base_url + '<?= $url ?>/datatable', []);
                } else {
                    noti('danger', response.data);
                }
            }, 'json');
            return false;
        });
    });

    function hapus(id) {
        if (!id || !confirm('Anda yakin akan menghapus mapping ini?')) {
            return false;
        }

        $.getJSON(base_url + '<?= $url ?>/hapus/' + id, function (response) {
            noti(response.status === 'ok' ? 'success' : 'danger', response.data);
            if (response.status === 'ok') {
                pagination('datatabel', base_url + '<?= $url ?>/datatable', []);
            }
        });
        return false;
    }
</script>