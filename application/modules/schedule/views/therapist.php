<div class="card ">
    <div class="header">
        <h4 class="title">List Therapist</h4>

    </div>
    <div class="content">

        <div class="panel">
            <div class="panel-body ">
                <a href="#" onclick="return tambah();" class="btn btn-success pull-left" data-toggle="modal"
                    data-target="#modal_data" data-backdrop="static" data-keyboard="false">Tambah</a>
            </div>
        </div>

        <table class="table table-hover table-striped" id="tbl-therapist" style="width: 100%">
            <thead>
                <td>No</td>
                <td>Nama</td>
                <td>Kontak</td>
                <td>Tipe</td>
                <td>Status</td>
                <td>Action</td>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($p_therapist as $d) {
                    ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $d['name'] ?></td>
                    <td><?= $d['email'] ?> <br> <?= $d['phone'] ?></td>
                    <td><?= $d['therapist_type'] ?></td>
                    <td>
                        <span class="label label-<?= $d['is_active'] == 1 ? 'success' : 'default' ?>">
                            <?= $d['is_active'] == 1 ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <a href="#" onclick="return edit(<?= htmlspecialchars(json_encode([
                            'id' => $d['id'],
                            'name' => $d['name'],
                            'therapist_type' => $d['therapist_type'],
                            'phone' => $d['phone'],
                            'email' => $d['email'],
                            'specialization' => $d['specialization']
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8') ?>);"><i
                                class="fa fa-edit"></i> Edit</a>
                        <a href="#" onclick="return hapus('<?= $d['id'] ?>');" class="btn btn-xs btn-danger"><i
                                class="fa fa-remove"></i> Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal" id="modal_data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Therapist</h4>
            </div>
            <form class="form-horizontal" method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>">
                <input type="hidden" name="_id" id="_id" value="">
                <input type="hidden" name="_mode" id="_mode" value="">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label">Nama</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control" id="name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="therapist_type" class="col-sm-3 control-label">Tipe Therapist</label>
                        <div class="col-sm-9">
                            <select name="therapist_type" class="form-control" id="therapist_type" required>
                                <option value="">Pilih Tipe Therapist</option>
                                <option value="Internal">Internal</option>
                                <option value="External">External</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="col-sm-3 control-label">Phone</label>
                        <div class="col-sm-9">
                            <input type="text" name="phone" class="form-control" id="phone" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" class="form-control" id="email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="specialization" class="col-sm-3 control-label">Spesialisasi</label>
                        <div class="col-sm-9">
                            <input type="text" name="specialization" class="form-control" id="specialization" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal" id="modal_delete">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Konfirmasi Hapus</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="delete_id" value="">
                <p>Apakah Anda yakin ingin menghapus therapist ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-delete-therapist">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    function tambah() {
        $('#<?php echo $nama_form; ?>')[0].reset();
        $('#_id').val('');
        $('#_mode').val('add');
    }

    function edit(therapist) {
        $('#_id').val(therapist.id);
        $('#name').val(therapist.name);
        $('#therapist_type').val(therapist.therapist_type);
        $('#phone').val(therapist.phone);
        $('#email').val(therapist.email);
        $('#specialization').val(therapist.specialization);
        $('#_mode').val('edit');
        $('#modal_data').modal('show');

        return false;
    }

    function hapus(id) {
        $('#delete_id').val(id);
        $('#modal_delete').modal('show');

        return false;
    }

    function deleteTherapist() {
        $.ajax({
            type: 'POST',
            data: {
                id: $('#delete_id').val()
            },
            url: base_url + "<?php echo $url; ?>/delete_therapist",
            success: function (r) {
                if (r.status == 'gagal') {
                    noti('danger', r.data);
                } else {
                    noti('success', r.data);
                    window.location.reload();
                }
            }
        });

        return false;
    }

    $(document).ready(function () {

        $('#tbl-therapist').DataTable();

        $('#btn-delete-therapist').on('click', deleteTherapist);

        $("#<?php echo $nama_form; ?>").on("submit", function () {
            var data = $(this).serialize();

            $.ajax({
                type: "POST",
                data: data,
                url: base_url + "<?php echo $url; ?>/simpan_therapist",
                success: function (r) {
                    if (r.status == "gagal") {
                        noti("danger", r.data);
                    } else {
                        $("#modal_data").modal('hide');
                        noti("success", r.data);
                        window.location.reload();
                    }
                }
            });

            return false;
        });
    });
</script>