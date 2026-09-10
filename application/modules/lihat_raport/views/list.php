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

                        $no = 1;
                        if (!empty($siswa_kelas)) {
                            foreach ($siswa_kelas as $sk) {
                                foreach ($tahun as $th){
                                    $thn = $th['tahun'];
                                    $smt = $thn % 10;
                                    $tglmid = new DateTime($th['tgl_raport']);
                                    $tglfinal = new DateTime($th['tgl_raport_kelas3']);
                                    $today = new DateTime();
                                    
                                ?>
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo "Semester ".$smt." / ".$sk['ta']."-".($sk['ta']+1); ?></td>
                                <td>
                                    <?php if ($tglmid <= $today) { ?>
                                        <a href="https://report.mhis.link/bintaro/dc/cetak_raport_pts/cetak_dc/<?php echo $sk['id_siswa']."/".$thn; ?>" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-print"></i> Midterm Report</a>
                                        <?php
                                    } ?>
                                    <?php if ($tglfinal <= $today) { ?>
                                    <?php if ($sk['tingkat'] == '1' || $sk['tingkat'] == '2' || $sk['tingkat'] == '3' || $sk['tingkat'] == '4' || $sk['tingkat'] == '5' || $sk['tingkat'] == '6') { ?>
                                    <a href="https://report.mhis.link/bintaro/dc/cetak_raport/cetak_sd/<?php echo $sk['id_siswa']."/".$thn; ?>" class="btn btn-success btn-sm" target="_blank"><i  class="fa fa-print"></i> Final Raport</a>
                                    <?php
                			    $data_siswa = $this->db->query("SELECT 
                                                a.id_siswa, b.nama, c.tingkat
                                                FROM t_kelas_siswa_reguler a
                                                INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                INNER JOIN m_kelas c ON a.id_kelas = c.id
                                                WHERE a.id_siswa = '" . $sk['id_siswa'] . "'")->result_array();
                                foreach ($data_siswa as $ds) {
                                    if ($ds['tingkat'] == '1' || $ds['tingkat'] == '4') {
                                            ?>
                                            <a href="https://report.mhis.link/bintaro/dc/cetak_raport/cetak_projek/<?php echo $sk['id_siswa']."/".$thn; ?>"
                                                class="btn btn-success btn-sm" target="_blank"><i class="fa fa-print"></i>
                                                Report Project P5</a>
                                
                                    
                                        <?php
                                        }
                                }
                                        }else { ?>
                                    <a href="https://report.mhis.link/bintaro/dc/cetak_raport/cetak/<?php echo $sk['id_siswa']."/".$thn; ?>" class="btn btn-success btn-sm" target="_blank"><i  class="fa fa-print"></i> Final Raport</a> <?php
                			    $data_siswa = $this->db->query("SELECT 
                                                a.id_siswa, b.nama, c.tingkat
                                                FROM t_kelas_siswa_reguler a
                                                INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                INNER JOIN m_kelas c ON a.id_kelas = c.id
                                                WHERE a.id_siswa = '" . $sk['id_siswa'] . "'")->result_array();
                                foreach ($data_siswa as $ds) {
                                    if ($ds['tingkat'] == '7' || $ds['tingkat'] == '10') {
                                            ?>
                                            <a style="<?=$th['tahun'] == 20251?"display:none":""?>" href="https://report.mhis.link/bintaro/dc/cetak_raport/cetak_projek/<?php echo $sk['id_siswa']."/".$thn; ?>"
                                                class="btn btn-success btn-sm" target="_blank"><i class="fa fa-print"></i>
                                                Report Project P5</a>
                                
                                    
                                        <?php
                                        }
                                }
                                    }
                                    }?>
                                </td>
                            </tr>
                        <?php 
                                    $no++;
                                }
                            }
                        } else {
                            echo '<tr><td colspan="3">Belum ada data siswa</td></tr>';
                        }
                        ?>

                        
                        
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>

</div>