<div class="card">
    <div class="header">
        <h4 class="title">List Schedule</h4>
    </div>
    <div class="content">
        <div class="panel">
            <div class="panel-body">
                <a href="<?= base_url($url . '/edit/0') ?>" class="btn btn-success">Tambah</a>
            </div>
        </div>

        <table class="table table-hover table-striped" id="tbl-schedule" style="width: 100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Code</th>
                    <th>Tahun</th>
                    <th>Semester</th>
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
                    <td><?= html_escape($d['tahun_label']) ?></td>
                    <td><?= html_escape($d['semester']) ?></td>
                    <td><?= html_escape($d['report_type']) ?></td>
                    <td><?= html_escape($d['title']) ?></td>
                    <td><?= html_escape($d['booking_start_at']) ?><br><?= html_escape($d['booking_end_at']) ?></td>
                    <td><span class="label label-<?= $d['status'] === 'active' ? 'success' : 'default' ?>">
                            <?= html_escape($d['status']) ?></span></td>
                    <td>
                        <a href="<?= base_url($url . '/edit/' . html_escape($d['id'])) ?>"
                            class="btn btn-xs btn-success"><i class="fa fa-edit"></i> Edit</a>
                        <a href="#" class="btn btn-xs btn-danger"
                            onclick="return hapus('<?= html_escape($d['id']) ?>');"><i class="fa fa-remove"></i>
                            Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

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