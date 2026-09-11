<div class="card">
    <div class="header">
        <h4 class="title">
            <?= !empty($data['id']) ? 'Edit Mapping Student Therapist' : 'Tambah Mapping Student Therapist' ?></h4>
    </div>
    <div class="content">
        <?php if ($this->session->flashdata('student_therapist_error')) { ?>
        <div class="alert alert-danger">
            <?= html_escape($this->session->flashdata('student_therapist_error')) ?>
        </div>
        <?php } ?>
        <?php if ($this->session->flashdata('student_therapist_success')) { ?>
        <div class="alert alert-success">
            <?= html_escape($this->session->flashdata('student_therapist_success')) ?>
        </div>
        <?php } ?>

        <form method="post" action="<?= base_url($url . '/simpan_massal') ?>" id="f_student_therapist_bulk">
            <input type="hidden" name="_mode" value="<?= html_escape($data['mode']) ?>">
            <?php if (!empty($data['id'])) { ?>
            <input type="hidden" name="_id" value="<?= html_escape($data['id']) ?>">
            <?php } ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tahun_id">Tahun Ajaran</label>
                        <select name="tahun_id" id="tahun_id" class="form-control" required>
                            <option value="">Pilih Tahun</option>
                            <?php foreach ($p_tahun as $tahun) { ?>
                            <option value="<?= html_escape($tahun['id']) ?>"
                                <?= !empty($data['tahun_id']) && (string) $data['tahun_id'] === (string) $tahun['id'] ? 'selected' : '' ?>>
                                <?= html_escape($tahun['tahun']) ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="therapist_id">Therapist</label>
                        <select name="therapist_id" id="therapist_id" class="form-control" required>
                            <option value="">Pilih Therapist</option>
                            <?php foreach ($p_therapists as $therapist) { ?>
                            <option value="<?= html_escape($therapist['id']) ?>"
                                <?= !empty($data['therapist_id']) && (string) $data['therapist_id'] === (string) $therapist['id'] ? 'selected' : '' ?>>
                                <?= html_escape($therapist['name']) ?><?= !empty($therapist['specialization']) ? ' - ' . html_escape($therapist['specialization']) : '' ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <label class="checkbox-inline" style="padding-top: 7px;">
                                <input type="checkbox" name="is_active" value="1"
                                    <?= !empty($data['is_active']) ? 'checked' : '' ?>> Active
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control"
                    rows="3"><?= html_escape($data['notes']) ?></textarea>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <strong>Daftar Student</strong>
                    <button type="button" class="btn btn-primary btn-xs pull-right" id="btn-select-student">
                        <i class="fa fa-list"></i> Pilih Student
                    </button>
                </div>
                <div class="panel-body" id="student-list-panel" style="display: none;">
                    <div class="checkbox" style="margin-top: 0;">
                        <label>
                            <input type="checkbox" id="check_all_students"> Pilih Semua
                        </label>
                    </div>
                    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                        <table class="table table-hover table-striped table-bordered" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" id="check_all_students_header"></th>
                                    <th>Student</th>
                                    <th>NIS</th>
                                    <th>NISN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($p_students as $student) { 
                                    $identifier = $student['nis'] !== '' ? $student['nis'] : $student['nisn'];
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="student_ids[]"
                                            value="<?= html_escape($student['id']) ?>" class="student-checkbox"
                                            <?= !empty($data['student_id']) && (string) $data['student_id'] === (string) $student['id'] ? 'checked' : '' ?>>
                                    </td>
                                    <td><?= html_escape($student['nama']) ?></td>
                                    <td><?= html_escape($student['nis']) ?></td>
                                    <td><?= html_escape($student['nisn']) ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="clearfix">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Mapping</button>
                <a href="<?= base_url($url) ?>" class="btn btn-default">Kembali</a>
            </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        $('#tahun_id, #therapist_id').select2({
            width: '100%'
        });

        $('#btn-select-student').on('click', function () {
            $('#student-list-panel').slideToggle();
        });

        $('#check_all_students, #check_all_students_header').on('change', function () {
            var checked = $(this).is(':checked');
            $('.student-checkbox').prop('checked', checked);
            $('#check_all_students').prop('checked', checked);
            $('#check_all_students_header').prop('checked', checked);
        });

        $('.student-checkbox').on('change', function () {
            var total = $('.student-checkbox').length;
            var selected = $('.student-checkbox:checked').length;
            var allSelected = total > 0 && selected === total;
            $('#check_all_students').prop('checked', allSelected);
            $('#check_all_students_header').prop('checked', allSelected);
        });
    });
</script>