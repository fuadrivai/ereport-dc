<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrated Daily Reporting System</title>
    <!-- Bootstrap CSS -->
    <link href="<?php echo base_url(); ?>aset/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>aset/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>aset/plugins/swal/sweetalert2.min.css">
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Arial', sans-serif; }
        .container { max-width: 800px; margin-top: 50px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group label { font-weight: bold; }
        .select2-container .select2-selection--single { height: 38px; border: 1px solid #ccc; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
        .history-container { margin-top: 30px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Integrated Daily Reporting System</h2>

    <div class="card">
        <form id="anecdotalForm">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Date of Incident</label>
                    <input type="date" class="form-control" name="tanggal" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label>Teachers' Name/therapist name</label>
                    <input type="text" class="form-control" name="reporter_name" required placeholder="Enter your name">
                </div>
            </div>
            
            <div class="form-group">
                <label>Students' Name</label>
                <select class="form-control" id="id_siswa" name="id_siswa" required></select>
            </div>

            <div class="form-group">
                <label>Description of Incident <small>(Write in detail the A-B-C)</small></label>
                <textarea class="form-control" name="catatan" rows="5" required placeholder="Write the description here..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit Record</button>
        </form>
    </div>

    <div class="history-container" id="history-container" style="display: none;">
        <h4>Student's History</h4>
        <div id="history-content">
            <!-- Timeline will be loaded here -->
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>aset/js/jquery-1.10.2.js"></script>
<script src="<?php echo base_url(); ?>aset/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>aset/js/select2.min.js"></script>
<script src="<?php echo base_url(); ?>aset/plugins/swal/sweetalert2.min.js"></script>

<script>
$(document).ready(function() {
    var base_url = '<?php echo base_url(); ?>';

    // Initialize Select2 for Student Search
    $('#id_siswa').select2({
        placeholder: 'Search for a student...',
        ajax: {
            url: base_url + 'anecdotal/search_student',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { term: params.term };
            },
            processResults: function (data) {
                return { results: data };
            },
            cache: true
        }
    });

    // When student is selected, load history
    $('#id_siswa').on('change', function() {
        var id_siswa = $(this).val();
        if(id_siswa) {
            loadHistory(id_siswa);
        } else {
            $('#history-container').hide();
        }
    });

    function loadHistory(id_siswa) {
        $.ajax({
            url: base_url + 'anecdotal/get_history/' + id_siswa,
            type: 'GET',
            success: function(response) {
                $('#history-content').html(response);
                $('#history-container').fadeIn();
            }
        });
    }

    // Handle Form Submit
    $('#anecdotalForm').submit(function(e) {
        e.preventDefault();
        
        var id_siswa = $('#id_siswa').val();
        if(!id_siswa) {
            swal('Error', 'Please select a student.', 'error');
            return;
        }

        $.ajax({
            url: base_url + 'anecdotal/save',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.status == 'success') {
                    swal('Success', 'Record saved successfully!', 'success');
                    $('#anecdotalForm')[0].reset();
                    $('#id_siswa').val(id_siswa).trigger('change'); // keep student selected and refresh history
                    // Set date back to today
                    $('input[name="tanggal"]').val(new Date().toISOString().split('T')[0]);
                } else {
                    swal('Error', response.message, 'error');
                }
            }
        });
    });
});
</script>

</body>
</html>
