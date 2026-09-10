<style type="text/css">
    .ctr {text-align: center}
    .nso {}
</style>
<div class="row">
    <div class="col-md-12">
        <p>
            <!--<a href="<?php echo base_url()."/".$url; ?>/cetak/<?php echo $this->uri->segment(3); ?>" class="btn btn-warning" target="_blank">Cetak</a>-->
        </p>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Cetak Raport PAS</h4>
            </div>
            <div class="content">  

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="40%">Nama</th>
                            <th width="50%">Cetak</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php 

                        $no = 1;
                        if (!empty($siswa_kelas)) {
                            foreach ($siswa_kelas as $sk) {  
                        ?>
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo $sk['nama']; ?></td>
                                <td>
                                    <?php if ($sk['tingkat'] == '1' || $sk['tingkat'] == '2' || $sk['tingkat'] == '3' || $sk['tingkat'] == '4' || $sk['tingkat'] == '5' || $sk['tingkat'] == '6') { ?>
                                        <a href="<?php echo base_url().$url."/cetak_sd/".$sk['id_siswa']."/".$tasm; ?>" class="btn btn-success btn-sm" target="_blank"><i  class="fa fa-print"></i> Raport Diknas</a>
                                        <a href="<?php echo base_url() . $url . "/cetak_projek/" . $sk['id_siswa'] . "/" . $tasm; ?>"
                                                            class="btn btn-success btn-sm" target="_blank"><i class="fa fa-print"></i>
                                                            Raport P5</a>
                                        <?php
                            			    $data_siswa = $this->db->query("SELECT 
                                                            a.id_siswa, b.nama, c.tingkat
                                                            FROM t_kelas_siswa_reguler a
                                                            INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                            INNER JOIN m_kelas c ON a.id_kelas = c.id
                                                            WHERE a.id_siswa = '" . $sk['id_siswa'] . "'")->result_array();
                                            foreach ($data_siswa as $ds) {
                                                if ($ds['tingkat'] == '1' || $ds['tingkat'] == '2' || $ds['tingkat'] == '3' ||$ds['tingkat'] == '4' || $ds['tingkat'] == '5' || $ds['tingkat'] == '6') {
                                                        ?>
                                                        
                                            
                                                
                                                    <?php
                                                    }
                                                
                                            }
                                        }else if ($sk['tingkat'] == '7' || $sk['tingkat'] == '8' || $sk['tingkat'] == '9' || $sk['tingkat'] == '10'){?>
                                        <a href="<?php echo base_url().$url."/cetak/".$sk['id_siswa']."/".$tasm; ?>" class="btn btn-success btn-sm" target="_blank"><i  class="fa fa-print"></i> Raport Diknas</a>
                                        <a href="<?php echo base_url().$url."/cetak_projek/".$sk['id_siswa']."/".$tasm; ?>" class="btn btn-success btn-sm" target="_blank"><i  class="fa fa-print"></i> Raport Projek</a>
                                    <?php
                                    $data_siswa = $this->db->query("SELECT 
                                                a.id_siswa, b.nama, c.tingkat
                                                FROM t_kelas_siswa_reguler a
                                                INNER JOIN m_siswa b ON a.id_siswa = b.id
                                                INNER JOIN m_kelas c ON a.id_kelas = c.id
                                                WHERE a.id_siswa = '" . $sk['id_siswa'] . "'")->result_array();
                                    foreach ($data_siswa as $ds) {
                                    if ($ds['tingkat'] == '7' || $ds['tingkat'] == '8' || $ds['tingkat'] == '9' || $ds['tingkat'] == '10') {
                                    ?> 
                                    <?php }
                                    }
                                    }?>
                                    
                                </td>
                            </tr>
                        <?php 
                                $no++;
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