<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrated Daily Reporting System</title>
    <!-- Bootstrap CSS -->
    <link href="<?php echo base_url(); ?>aset/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>aset/plugins/fa/css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>aset/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>aset/plugins/swal/sweetalert2.min.css">

    <style>
        :root {
            --anecdotal-maroon: #a90000;
            --anecdotal-maroon-dark: #820000;
            --anecdotal-ink: #2d2525;
            --anecdotal-muted: #756d6d;
            --anecdotal-bg: #f7f5f4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--anecdotal-bg);
            color: var(--anecdotal-ink);
            font-family: "Trebuchet MS", sans-serif;
            margin: 0;
        }

        .anecdotal-shell {
            min-height: 100vh;
            padding: 42px 15px 44px;
        }

        .anecdotal-wrap {
            margin: 0 auto;
            max-width: 900px;
        }

        .anecdotal-header {
            background: var(--anecdotal-maroon);
            border-radius: 18px 18px 0 0;
            color: #fff;
            padding: 38px 46px 30px;
            text-align: center;
        }

        .anecdotal-logo {
            display: block;
            height: 72px;
            margin: 0 auto 16px;
            max-width: 300px;
            object-fit: contain;
            width: auto;
        }

        .anecdotal-header h1 {
            font-size: 30px;
            font-weight: 700;
            margin: 0;
        }

        .anecdotal-header p {
            color: rgba(255, 255, 255, .82);
            font-size: 15px;
            margin: 10px 0 0;
        }

        .anecdotal-body {
            background: #fff;
            border: 1px solid #e8e1df;
            border-top: 0;
            box-shadow: 0 8px 28px rgba(57, 32, 32, .07);
            padding: 30px;
        }

        .anecdotal-section-title {
            color: var(--anecdotal-maroon);
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 8px;
        }

        .anecdotal-lead {
            color: var(--anecdotal-muted);
            line-height: 1.6;
            margin: 0 0 24px;
        }

        .anecdotal-label {
            color: var(--anecdotal-ink);
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin: 16px 0 7px;
        }

        .anecdotal-label small {
            color: var(--anecdotal-muted);
            font-weight: 400;
        }

        .form-control,
        .select2-container .select2-selection--single {
            border: 1px solid #d9d1cf;
            border-radius: 4px;
            height: 42px;
            padding: 7px 8px;
        }

        textarea.form-control {
            height: auto;
            min-height: 132px;
            resize: vertical;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .anecdotal-actions {
            border-top: 1px solid #eee6e3;
            margin-top: 24px;
            padding-top: 20px;
            text-align: right;
        }

        .btn-anecdotal {
            background: var(--anecdotal-maroon);
            border-color: var(--anecdotal-maroon);
            color: #fff;
            font-weight: 700;
            min-height: 42px;
            padding: 9px 20px;
        }

        .btn-anecdotal:hover,
        .btn-anecdotal:focus {
            background: var(--anecdotal-maroon-dark);
            border-color: var(--anecdotal-maroon-dark);
            color: #fff;
        }

        .history-container {
            border-top: 1px solid #e8e1df;
            margin-top: 30px;
            padding-top: 28px;
        }

        .history-container h2 {
            margin-bottom: 20px;
        }

        .history-container .list-group-item {
            border-color: #e8e1df;
            padding: 18px;
        }

        .history-container .list-group-item h5 {
            color: var(--anecdotal-maroon) !important;
        }

        .history-container .alert {
            background: #fff8f7;
            border-color: #e8c8c5;
            color: #8b2520;
        }
    </style>
    <style media="(max-width: 600px)">
        .anecdotal-shell {
            padding: 15px 0 30px;
        }

        .anecdotal-header {
            border-radius: 0;
            padding: 28px 15px 24px;
        }

        .anecdotal-logo {
            height: 60px;
        }

        .anecdotal-header h1 {
            font-size: 25px;
        }

        .anecdotal-body {
            padding: 22px 15px;
        }

        .anecdotal-actions {
            text-align: left;
        }

        .btn-anecdotal {
            width: 100%;
        }
    </style>
</head>

<body>

    <main class="anecdotal-shell">
        <div class="anecdotal-wrap">
            <header class="anecdotal-header">
                <img class="anecdotal-logo" src="<?php echo base_url('aset/img/Logo-MH-Transparan-01.png'); ?>"
                    alt="Mutiara Harapan Islamic School">
                <h1>Daily Anecdotal Report</h1>
                <p>Record and review student observations in one place.</p>
            </header>

            <section class="anecdotal-body">
                <h2 class="anecdotal-section-title">New Student Observation</h2>
                <p class="anecdotal-lead">Complete the details below to add a report for an active student.</p>

                <form id="anecdotalForm">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="anecdotal-label">Date of Incident</label>
                            <input type="date" class="form-control" name="tanggal" required
                                value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="anecdotal-label">Teacher / Therapist Name</label>
                            <input type="text" class="form-control" name="reporter_name" required
                                placeholder="Enter your name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="anecdotal-label">Student</label>
                        <select class="form-control" id="id_siswa" name="id_siswa" required></select>
                    </div>

                    <div class="form-group">
                        <label class="anecdotal-label">Description of Incident <small>(Write in detail the
                                A-B-C)</small></label>
                        <textarea class="form-control" name="catatan" rows="5" required
                            placeholder="Write the description here..."></textarea>
                    </div>

                    <div class="anecdotal-actions">
                        <button type="submit" class="btn btn-anecdotal"><i class="fa fa-check"></i> Submit
                            Record</button>
                    </div>
                </form>

                <div class="history-container" id="history-container" style="display: none;">
                    <h2 class="anecdotal-section-title">Student History</h2>
                    <div id="history-content">
                    </div>
                </div>
        </div>
        </div>
    </main>

    <script src="<?php echo base_url(); ?>aset/js/jquery-1.10.2.js"></script>
    <script src="<?php echo base_url(); ?>aset/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>aset/js/select2.min.js"></script>
    <script src="<?php echo base_url(); ?>aset/plugins/swal/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function () {
            var base_url = '<?php echo base_url(); ?>';

            // Initialize Select2 for Student Search
            $('#id_siswa').select2({
                placeholder: 'Search for a student...',
                ajax: {
                    url: base_url + 'anecdotal/search_student',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            // When student is selected, load history
            $('#id_siswa').on('change', function () {
                var id_siswa = $(this).val();
                if (id_siswa) {
                    loadHistory(id_siswa);
                } else {
                    $('#history-container').hide();
                }
            });

            function loadHistory(id_siswa) {
                $.ajax({
                    url: base_url + 'anecdotal/get_history/' + id_siswa,
                    type: 'GET',
                    success: function (response) {
                        $('#history-content').html(response);
                        $('#history-container').fadeIn();
                    }
                });
            }

            // Handle Form Submit
            $('#anecdotalForm').submit(function (e) {
                e.preventDefault();

                var id_siswa = $('#id_siswa').val();
                if (!id_siswa) {
                    swal('Error', 'Please select a student.', 'error');
                    return;
                }

                $.ajax({
                    url: base_url + 'anecdotal/save',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status == 'success') {
                            swal('Success', 'Record saved successfully!', 'success');
                            $('#anecdotalForm')[0].reset();
                            $('#id_siswa').val(id_siswa).trigger(
                                'change'); // keep student selected and refresh history
                            // Set date back to today
                            $('input[name="tanggal"]').val(new Date().toISOString().split(
                                'T')[0]);
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