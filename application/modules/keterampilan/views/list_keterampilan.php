<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Master Keterampilan</h4>
            </div>
            <div class="content">

                <div class="panel">
                    <div class="panel-body ">
                        <a href="#" class="btn btn-success pull-left" data-toggle="modal" data-target="#modal_data"
                            data-backdrop="static" data-keyboard="false">Tambah</a>
                    </div>
                </div>
                
                <?php echo $this->session->userdata('ue'); ?>
                <table class="table table-hover table-striped" id="datatabel" style="width: 100%">
                    <thead>
                        <td width="5%">No</td>
                        <td width="15%">Kode</td>
                        <td width="25%">Nama</td>
                        <td width="25%">Deskripsi</td>
                        <td width="15%">Aksi</td>
                    </thead>

                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="modal_data">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Kelas</h4>
            </div>
            <form action="<?php echo base_url()."". $url; ?>/simpan" class="form-horizontal" method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>">
                <input type="hidden" name="id" id="id" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="kode" class="col-sm-2 control-label">Kode</label>
                        <div class="col-sm-10">
                            <input type="text" name="kode" class="form-control" id="kode">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nama" class="col-sm-2 control-label">Nama</label>
                        <div class="col-sm-10">
                            <input type="text" name="nama" class="form-control" id="nama" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi" class="col-sm-2 control-label">Descripsi</label>
                        <div class="col-sm-10">
                            <textarea type="text" rows="4" name="deskripsi" class="form-control" id="deskripsi"></textarea>
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


<script>
    $(document).ready(function () {
        pagination("datatabel", base_url + "<?php echo $url; ?>/datatable", []);
    });
    
    function edit(id) {
        $("#modal_data").modal('show');

        $.ajax({
            type: "GET",
            url: base_url+"<?php echo $url; ?>/edit/"+id,
            success: function(data) {
                $("#id").val(data.data.id);
                $("#nama").val(data.data.nama);
                $("#kode").val(data.data.kode);
                $("#deskripsi").val(data.data.deskripsi);
            }
        });
        return false;
    }

    function hapus(id) {
        if (id == 0) {
            noti("danger", "Silakan pilih datanya..!");
        } 


        if (confirm('Anda yakin..? ')) {

            $.ajax({
                type: "GET",
                url: base_url+"<?php echo $url; ?>/hapus/"+id,
                success: function(data) {
                    noti("success", "Berhasil dihapus...!");
                    pagination("datatabel", base_url+"<?php echo $url; ?>/datatable", []);
                }
            });

        }
        
        return false;
    }
</script>
