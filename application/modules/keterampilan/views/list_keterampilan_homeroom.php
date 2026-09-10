<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning" style="color: #000">
            <b>Petunjuk : </b><br>
            Menu ini digunakan untuk menginput nilai pada setiap masing-masing mata pelajaran diampu. Silakan klik menu <b><i>Nilai Pengetahuan</i></b> untuk menginput nilai pengetahuan, dan <b><i>Nilai Keterampilan</i></b> untuk menginput nilai keterampilan.
        </div>
    </div>
    
    <?php foreach($list_mapelkelas as $mk){?>
        <div class="col-md-4">
            <div class="card">
                <div class="header">
                    <h5 class="title"><?php echo $mk['mapel']." - ".$mk['kelas']; ?> </h5>
                </div>
                 <div class="content">
                     <li class="list-group-item"><a href="<?php echo base_url()."".$url."/kompetensi/".$mk['id']; ?>"><i class="fa fa-chevron-right"></i> Isi Rapor</a></li>
                 </div>
            </div>
        </div>
    <?php }?>
</div>