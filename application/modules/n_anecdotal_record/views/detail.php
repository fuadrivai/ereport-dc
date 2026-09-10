<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Anecdotal Record / Daily Reporting System</h4>
                <p class="category">Student: <?php echo $siswa['nama']; ?></p>
            </div>
            <div class="content">
                <div class="row">
                    <!-- Form for new record -->
                    <div class="col-md-5">
                        <form id="f_simpan" onsubmit="return false">
                            <input type="hidden" name="id_siswa" value="<?php echo $siswa['id']; ?>">
                            
                            <div class="form-group">
                                <label>Date of Incident</label>
                                <input type="date" class="form-control" name="tanggal" required value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Description of Incident <small>(Write in detail the A-B-C)</small></label>
                                <textarea class="form-control" name="catatan" rows="8" required placeholder="Write the description here..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block" onclick="simpan()">Submit Record</button>
                            <a href="<?php echo base_url(); ?>n_anecdotal_record" class="btn btn-default btn-block">Back to List</a>
                        </form>
                    </div>
                    
                    <!-- Timeline of records -->
                    <div class="col-md-7">
                        <h4 style="margin-top:0;">Student's History</h4>
                        <?php if(empty($records)): ?>
                            <div class="alert alert-info">No previous records found for this student.</div>
                        <?php else: ?>
                            <ul class="list-group">
                            <?php foreach($records as $rec): ?>
                                <li class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between" style="border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px;">
                                        <h5 class="mb-1" style="margin-top: 0; color: #1F77D0;">
                                            <i class="pe-7s-date"></i> 
                                            <?php echo date('d M Y', strtotime($rec['tanggal'])); ?> 
                                            <small style="color: #999;">at <?php echo date('H:i', strtotime($rec['waktu'])); ?></small>
                                        </h5>
                                        <small><strong>Reporter:</strong> <?php echo htmlspecialchars($rec['reporter_name']); ?></small>
                                    </div>
                                    <p class="mb-1" style="white-space: pre-line;"><?php echo htmlspecialchars($rec['catatan']); ?></p>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function simpan() {
    var data = getFormData($('#f_simpan'));
    
    if (data.tanggal == "" || data.catatan == "") {
        swal('Error', 'Please fill all required fields.', 'error');
        return;
    }
    
    $.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>n_anecdotal_record/save",
        data: data,
        success: function(response){
            if(response.status == "ok") {
                swal('Success', response.data, 'success').then(function() {
                    window.location.reload();
                });
            } else {
                swal('Error', response.data, 'error');
            }
        },
        error: function(e){
            swal('Error', 'Terjadi kesalahan sistem', 'error');
        }
    });
}
</script>
