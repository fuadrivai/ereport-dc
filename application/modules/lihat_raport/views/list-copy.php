<style type="text/css">
    .ctr {text-align: center}
    .nso {}
</style>
<div class="">
    <div class="col-md-12">
        <p>
            <!--<a href="<?php echo base_url()."/".$url; ?>/cetak/<?php echo $this->uri->segment(3); ?>" class="btn btn-warning" target="_blank">Cetak</a>-->
        </p>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Report Card</h4>
            </div>
            <div class="content">  

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="40%">Name</th>
                            <th width="50%">View</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php
                        for ($i = 0; $i < count($tahun); $i++) {
                            $th = $tahun[$i];
                            $_th =  substr($th['tahun'], 0, 4);
                            $smt = substr($th['tahun'], -1);
                            $tglmid = new DateTime($th['tgl_raport']);
                            $tglfinal = new DateTime($th['tgl_raport_kelas3']);
                            $today = new DateTime();
                        ?>
                            <tr>
                                <td><?= ($i + 1); ?></td>
                                <td><?= "Semester " . $smt . " / " . $_th . "-" . ($_th + 1); ?></td>
                                <td>

                                    <?php if (!empty($siswa_kelas)) { ?>
                                        <?php for ($j = 0; $j < count($siswa_kelas); $j++) { ?>
                                            <?php
                                            $sk = $siswa_kelas[$j];
                                            ?>
                                            <?php if ($_th == $sk['ta']) { ?>
                                                <?php if ($tglmid <= $today) { ?>
                                                    <a href="<?= base_url() . "cetak_raport_pts" . "/cetak_dc/" . $sk['id_siswa'] . "/" . $th['tahun']; ?>" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-print"></i> Midterm Report</a>
                                                <?php } ?>
                                                <?php if ($tglfinal <= $today) { ?>
                                                    <?php if($sk['tingkat'] == '1' || $sk['tingkat'] == '2' || $sk['tingkat'] == '3' || $sk['tingkat'] == '4' || $sk['tingkat'] == '5' || $sk['tingkat'] == '6'){ ?>
                                                        <a href="https://report.mhis.link/bintaro/dc/cetak_raport/cetak_sd/<?php echo $sk['id_siswa']."/".$th['tahun']; ?>" class="btn btn-success btn-sm" target="_blank"><i  class="fa fa-print"></i> Final Raport</a>
                                                    <?php } else { ?>
                                                        <a href="https://report.mhis.link/bintaro/dc/cetak_raport/cetak/<?php echo $sk['id_siswa']."/".$th['tahun']; ?>" class="btn btn-success btn-sm" target="_blank"><i  class="fa fa-print"></i> Final Raport</a>
                                                    <?php }?>
                                                    
                                                    <a href="https://report.mhis.link/bintaro/dc/cetak_raport/cetak_projek/<?php echo $sk['id_siswa'] . "/" . $th['tahun']; ?>" target="_blank" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Report Project P5</a>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                            </tr>
                         <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>