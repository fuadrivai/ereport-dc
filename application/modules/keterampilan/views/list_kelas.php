<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Set Kelompok Rapor Skill</h4>
            </div>
            <div class="content">
                <div class="panel">
                    <div class="panel-body ">
                        <a href="<?php echo base_url()."".$url?>/set_kelas" class="btn btn-success pull-left">Tambah</a>
                    </div>
                </div>
                
                <?php echo $this->session->userdata('ue'); ?>
                <table class="table table-hover table-striped" id="datatabel" style="width: 100%">
                    <thead>
                        <td width="5%">No</td>
                        <td width="25%">Guru</td>
                        <td width="25%">Mapel</td>
                        <td width="20%">Kelas</td>
                        <td width="15%">Aksi</td>
                    </thead>

                </table>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        pagination("datatabel", base_url + "<?php echo $url; ?>/serverside", []);
    });
</script>

