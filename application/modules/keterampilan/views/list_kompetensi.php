<div class="row">
    
    <div class="col-md-12">
        <div class="alert alert-warning" style="color: #000">
            <b>Petunjuk : </b><br>
            <ul>
                <li>Menu ini digunakan untuk menginput nilai pengetahuan pada mata pelajaran <b><i><?php echo $detil_mp['nmmapel'].", kelas ".$detil_mp['nmkelas']; ?>.</i></b> </li>
                <li>Jika kompetensi dasar belum ada, silakan klik tombol <b><i>Tambah Topik</i></b>. Untuk mengubah atau menghapus nama KD, silakan klik tombol "<i class="fa fa-pencil"></i>" atau "<i class="fa fa-remove"></i>". </li>
                <li>Untuk mengisikan nilai pengetahuan pada masing-masing KD, silakan klik nama KD, dan akan muncul daftar siswa serta isian nilai. Nilai dalam <b><i>skala 1-100</i></b>. Jangan lupa klik tombol <b><i>Simpan</i></b> di sebelah bawah.</li>
            </ul>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <a href="<?php echo base_url(); ?>keterampilan/homeroom" class="btn btn-info"><i class="fa fa-arrow-left"></i> Kembali</a>
                <!--<a href="<?php echo base_url(); ?>n_pengetahuan/cetak/<?php echo $detil_mp['id_keterampilan']."-".$detil_mp['id_kelas']; ?>" class="btn btn-warning" target="_blank"><i class="fa fa-print"></i> Cetak</a>-->

                <!--<a href="<?php echo base_url(); ?>n_pengetahuan/import/<?php echo $detil_mp['id_keterampilan']."-".$detil_mp['id_kelas']; ?>" class="btn btn-danger"><i class="fa fa-download"></i> Download File Excel</a>-->
                <!--<a href="<?php echo base_url(); ?>n_pengetahuan/upload/<?php echo $detil_mp['id_keterampilan']."-".$detil_mp['id_kelas']; ?>" class="btn btn-success"><i class="fa fa-upload"></i> Upload File Excel</a>-->
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h5 class="title">Nilai Keterampilan &raquo; <?php echo $detil_mp['nmmapel']." - ".$detil_mp['nmkelas']; ?></h5>
            </div>
            <div class="content">
                <p>
                    <a href="#" data-toggle="modal" data-target="#modal_data" class="btn btn-info"><i class="fa fa-plus-circle"></i> Tambah Kompetensi</a>
                </p>
                 <ul class="list-group" id="list_kd">
                    <?php foreach($list_kd as $kd){?>
                        <li class="list-group-item">
                            <a href="#"><?= $kd['no_kd'].". ". $kd['nama_kd']?></a>
                            <div class="pull-right">
                                <a href="#" data-id="<?=$kd['id']?>" class="btn btn-xs btn-info btn-sub" data-toggle="modal" data-target="#modal_detail" ><i class="fa fa-plus-circle"></i> sub kategori</a>
                                <a href="#" class="btn btn-xs btn-success btn-edit-kd"
                                            data-id="<?=$kd['id']?>"
                                            data-kode="<?=$kd['no_kd']?>"
                                            data-nama="<?=$kd['nama_kd']?>"
                                            data-mid_final="<?=$kd['mid_final']?>"
                                            data-toggle="modal" data-target="#modal_data">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a href="#" data-id="<?=$kd['id']?>" class="btn btn-xs btn-danger"><i class="fa fa-remove"></i> </a>
                            </div>
                            <?php $details = $this->db->query("SELECT * FROM `t_kompetensi_detail` WHERE id_kompetensi = {$kd['id']} ORDER BY urutan ASC")?>
                            <?php if($details->num_rows()>0){?>
                                <ul class="list-group" style="padding-top:5px;margin-bottom : 0px">
                                    <?php foreach(($details->result_array()) as $dt){?>
                                        <li class="list-group-item">
                                            <a href="#"><?= $kd['no_kd'].".".$dt['urutan']." ". $dt['nama']?></a>
                                        </li>
                                    <?php }?>
                                </ul>
                            <?php }?>
                        </li>
                    <?php }?>
                </ul>
                <ul class="list-group">
                    <li class="list-group-item"><a href="#"><i class="fa fa-chevron-right"></i>  ULANGAN TENGAH SEMESTER</a></li>
                    <li class="list-group-item"><a href="#"><i class="fa fa-chevron-right"></i>  ULANGAN AKHIR SEMESTER</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Input Nilai </h4>
            </div>
            <div class="content">
                <table class="table table-sm" id="tbl-nilai">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Nilai Mid</th>
                            <th>Nilai Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1?>
                        <?php foreach($list_siswa as $siswa){?>
                            <tr>
                                <td><?= $no=$no+1; ?></td>
                                <td><?= $siswa['nama_siswa']; ?></td>
                                <td>
                                    <div class="form-group nilai-mid">
                                        <select class="form-control">
                                            <option>--</option>
                                            <option value="AS">Above standard at this time</option>
                                            <option value="MS">Meeting standard at this time</option>
                                            <option value="PS">Progressing standard at this time</option>
                                            <option value="WS">Beginning work towards standard at this time</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group nilai-final">
                                        <select class="form-control">
                                            <option>--</option>
                                            <option value="AS">Above standard at this time</option>
                                            <option value="MS">Meeting standard at this time</option>
                                            <option value="PS">Progressing standard at this time</option>
                                            <option value="WS">Beginning work towards standard at this time</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modal_data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Set KD</h4>
            </div>
            <form class="form-horizontal" method="post" id="<?php echo $nama_form; ?>" name="<?php echo $nama_form; ?>" onsubmit="return simpan_kd();">
                <input type="hidden" name="id_kd" id="id_kd" value="">
                <input type="hidden" name="id_guru" id="id_guru" value="<?php echo $detil_mp['id_guru']; ?>">
                <input type="hidden" name="id_keterampilan" id="id_keterampilan" value="<?php echo $detil_mp['id_keterampilan']; ?>">
                <input type="hidden" name="tingkat" id="tingkat" value="<?php echo $detil_mp['tingkat']; ?>">
                <input type="hidden" name="nama_guru" id="nama_guru" value="<?php echo $detil_mp['nama_guru']; ?>">
                <input type="hidden" name="nama_keterampilan" id="nama_keterampilan" value="<?php echo $detil_mp['nmmapel']; ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama" class="col-sm-2 control-label">Kode</label>
                        <div class="col-sm-10">
                            <input type="text" name="kode" class="form-control" autofocus="true" id="kode" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nama" class="col-sm-2 control-label">Nama</label>
                        <div class="col-sm-10">
                            <input type="text" name="nama_kd" class="form-control" id="nama_kd" required>
                        </div>
                    </div>
                    <div class="form-group">
                            <label for="nama" class="col-sm-2 control-label">Mid/Final</label>
                            <div class="col-sm-10">
                                <select name="semester" class="form-control" id="semester" required>
                                    <option value="1">Mid</option>
                                    <option value="2">Final</option>
                                </select>
                                <!--<p style="color: red; font-size: 10pt">DILARANG memakai spasi, koma, strip. Contoh : K01</p>-->
                            </div>
                        </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="tbSimpanKd">Simpan</button>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal" id="modal_detail">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Set KD</h4>
            </div>
            <form class="form-horizontal" method="post" id="<?php echo $nama_form."_detail"; ?>" name="<?php echo $nama_form."_detail"; ?>" onsubmit="return simpan_detail();">
                <input type="hidden" name="id_kompetensi" id="id_kompetensi" value="">
                <input type="hidden" name="id_detail" id="id_detail" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="urutan" class="col-sm-2 control-label">Urutan</label>
                        <div class="col-sm-10">
                            <input type="text" name="urutan" class="form-control" autofocus="true" id="urutan" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nama" class="col-sm-2 control-label">Nama</label>
                        <div class="col-sm-10">
                            <input type="text" name="nama_detail" class="form-control" id="nama_detail" required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="tbSimpanDetail">Simpan</button>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
    let id_guru_mapel = "<?= isset($id)?$id:''; ?>";
    let dataNilai = [];
    $(document).on("ready", function() {
        $('#tbl-nilai').dataTable({
            paging:false,
            searching:false,
            bInfo:false,
            ordering: false,
            // data:dataNilai,
        });
        $('#list_kd li').on('click', function(){
            $('li.active').removeClass('active');
            $(this).addClass('active');
        });
        
        $('.btn-edit-kd').on('click',function(){
            let id=$(this).attr('data-id');
            let kode=$(this).attr('data-kode');
            let nama=$(this).attr('data-nama');
            let mid_final=$(this).attr('data-mid_final');
            $('#id_kd').val(id);
            $('#nama_kd').val(nama);
            $('#kode').val(kode);
            $('#semester').val(mid_final).trigger('change');
        })
        $('.btn-sub').on('click',function(){
            let kdId=$(this).attr('data-id');
            $('#id_kompetensi').val(kdId);
        })
        
        $('#modal_data').on('hidden.bs.modal',function(){
             $('#id_kd').val("");
            $('#nama_kd').val("");
            $('#kode').val("");
            $('#semester').prop('selectedIndex', 0).trigger('change');
        })
        $('#modal_detail').on('hidden.bs.modal',function(){
             $('#id_kompetensi').val("");
             $('#id_detail').val("");
            $('#urutan').val("");
            $('#nama_detail').val("");
        })
    });
    // function hapus(id) {
    //     if (id == 0) {
    //         noti("danger", "Silakan pilih datanya..!");
    //     } else {
    //         if (confirm('Anda yakin...?')) {
    //             $.ajax({
    //                 type: "GET",
    //                 url: base_url+"set_kd/hapus/"+id,
    //                 success: function(data) {
    //                     noti("success", "Berhasil dihapus...!");
    //                     list_kd();
    //                 }
    //             });                
    //         }
    //     }
    //     return false;
    // }
    function simpan_kd() {
        let data = {
            id_kd:$('#id_kd').val(),
            id_keterampilan:$('#id_keterampilan').val(),
            tingkat:$('#tingkat').val(),
            nama_guru:$('#nama_guru').val(),
            nama_keterampilan:$('#nama_keterampilan').val(),
            kode:$('#kode').val(),
            nama_kd:$('#nama_kd').val(),
            semester:$('#semester').val(),
        }
        
        $.ajax({
            type: "POST",
            contentType: 'application/json',
            data: JSON.stringify(data),
            url: base_url+"keterampilan/simpan_kd",
            beforeSend: function(){
                $("#tbSimpanKd").attr("disabled", true);
            },
            success: function(r) {
                $("#tbSimpanKd").attr("disabled", false);
                $("#modal_data").modal('hide');
                noti("success", r.data);
                setTimeout(() => {
                    location.reload()
                }, 1000);
            },
            error: function (err) {
              noti("error", err?.responseJSON?.message??"Tidak Dapat Mengakses Server");
            }
        });
        return false;
    }
    function simpan_detail() {
        let data = {
            id_kompetensi:$('#id_kompetensi').val(),
            id:$('#id_detail').val(),
            urutan:$('#urutan').val(),
            nama_detail:$('#nama_detail').val(),
        }
    
        $.ajax({
            type: "POST",
            contentType: 'application/json',
            data: JSON.stringify(data),
            url: base_url+"keterampilan/simpan_detail_kd",
            beforeSend: function(){
                $("#tbSimpanDetail").attr("disabled", true);
            },
            success: function(r) {
                $("#tbSimpanDetail").attr("disabled", false);
                $("#modal_detail").modal('hide');
                noti("success", r.data);
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function (err) {
              noti("error", err?.responseJSON?.message??"Tidak Dapat Mengakses Server");// Log error response
            }
        });
        return false;
    }
    
</script>
