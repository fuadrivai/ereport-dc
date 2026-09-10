<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Anecdotal Record / Daily Reporting System</h4>
                <p class="category">Class: <?php echo $nama_kelas; ?></p>
            </div>
            <div class="content">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($siswa_kelas)) {
                            $no = 1;
                            foreach ($siswa_kelas as $s) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $s['nis']; ?></td>
                            <td><?php echo $s['nama']; ?></td>
                            <td>
                                <a href="<?php echo base_url(); ?>n_anecdotal_record/detail/<?php echo $s['id_siswa']; ?>" class="btn btn-info btn-sm"><i class="pe-7s-notebook"></i> View Records</a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data siswa di kelas ini.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
