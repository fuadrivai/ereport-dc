<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= html_escape(isset($title) ? $title : 'Report Distribution Booking') ?></title>
    <link href="<?= base_url('aset/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('aset/plugins/fa/css/font-awesome.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('aset/css/select2.min.css') ?>" rel="stylesheet">
    <script src="<?= base_url('aset/js/jquery-1.10.2.js') ?>"></script>
    <script src="<?= base_url('aset/js/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('aset/js/select2.min.js') ?>"></script>
    <style>
        :root {
            --booking-maroon: #a90000;
            --booking-maroon-dark: #820000;
            --booking-ink: #2d2525;
            --booking-muted: #756d6d;
            --booking-bg: #f7f5f4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--booking-bg);
            color: var(--booking-ink);
            font-family: "Trebuchet MS", sans-serif;
            margin: 0;
        }

        .booking-shell {
            min-height: 100vh;
            padding: 42px 15px 44px;
        }

        .booking-wrap {
            margin: 0 auto;
            max-width: 900px;
        }

        .booking-header {
            background: var(--booking-maroon);
            border-radius: 18px 18px 0 0;
            color: #fff;
            padding: 40px 46px 22px;
            text-align: center;
        }

        .booking-logo {
            display: block;
            height: 80px;
            margin: 0 auto 18px;
            max-width: 320px;
            object-fit: contain;
            width: auto;
        }

        .booking-header h1 {
            font-size: 31px;
            font-weight: 700;
            margin: 0;
        }

        .booking-header p {
            font-size: 15px;
            margin: 10px 0 27px;
        }

        .wizard-steps {
            display: flex;
            justify-content: space-between;
            list-style: none;
            margin: 0;
            padding: 0;
            position: relative;
        }

        .wizard-steps:before {
            background: rgba(255, 255, 255, .45);
            content: "";
            height: 3px;
            left: 5%;
            position: absolute;
            right: 5%;
            top: 21px;
        }

        .wizard-steps li {
            color: rgba(255, 255, 255, .78);
            flex: 1;
            font-size: 12px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .wizard-step-number {
            align-items: center;
            background: rgba(255, 255, 255, .35);
            border-radius: 50%;
            display: flex;
            font-size: 17px;
            height: 42px;
            justify-content: center;
            margin: 0 auto 8px;
            width: 42px;
        }

        .wizard-steps li.active,
        .wizard-steps li.complete {
            color: #fff;
        }

        .wizard-steps li.active .wizard-step-number,
        .wizard-steps li.complete .wizard-step-number {
            background: #fff;
            color: var(--booking-maroon);
        }

        .wizard-body {
            background: #fff;
            border: 1px solid #e8e1df;
            border-top: 0;
            box-shadow: 0 8px 28px rgba(57, 32, 32, .07);
            padding: 30px;
        }

        .wizard-panel {
            display: none;
        }

        .wizard-panel.active {
            display: block;
        }

        .booking-section-title {
            color: var(--booking-maroon);
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 10px;
        }

        .booking-lead {
            color: var(--booking-muted);
            line-height: 1.6;
            margin: 0 0 24px;
        }

        .booking-meta {
            border-left: 3px solid var(--booking-maroon);
            margin: 18px 0;
            padding: 12px 16px;
        }

        .booking-meta div {
            display: inline-block;
            margin: 0 32px 10px 0;
        }

        .booking-meta strong {
            color: var(--booking-muted);
            display: block;
            font-size: 11px;
            text-transform: uppercase;
        }

        .booking-description {
            line-height: 1.6;
        }

        .booking-label {
            color: var(--booking-ink);
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin: 16px 0 7px;
        }

        .booking-help {
            color: var(--booking-muted);
            font-size: 12px;
            margin-top: 7px;
        }

        .form-control,
        .select2-container .select2-selection--single {
            border: 1px solid #d9d1cf;
            border-radius: 4px;
            height: 42px;
            padding: 7px 8px;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .date-grid,
        .session-grid,
        .booking-type-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .choice-card {
            background: #fff;
            border: 1px solid #ded5d2;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            min-height: 72px;
            padding: 14px;
        }

        .choice-card:hover,
        .choice-card.selected {
            border-color: var(--booking-maroon);
            box-shadow: 0 0 0 2px rgba(169, 0, 0, .08);
        }

        .choice-card.selected {
            background: #fffafa;
        }

        .choice-card input {
            margin-right: 8px;
        }

        .choice-card strong {
            display: block;
            font-size: 14px;
        }

        .choice-card small {
            color: var(--booking-muted);
            display: block;
            font-size: 12px;
            margin-top: 4px;
        }

        .date-panel {
            display: none;
        }

        .date-panel.active {
            display: block;
        }

        .session-card,
        .booking-type-card {
            min-height: 92px;
        }

        .session-time,
        .booking-type-card i {
            color: var(--booking-maroon);
            font-size: 16px;
        }

        .booking-type-card i {
            font-size: 20px;
            margin-bottom: 7px;
        }

        .booking-summary {
            background: #fbf8f7;
            border-left: 3px solid var(--booking-maroon);
            padding: 16px;
        }

        .summary-row {
            border-bottom: 1px solid #eee6e3;
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .summary-row:last-child {
            border: 0;
        }

        .summary-label {
            color: var(--booking-muted);
            font-size: 12px;
        }

        .summary-value {
            font-size: 13px;
            font-weight: 700;
            text-align: right;
        }

        .booking-actions {
            border-top: 1px solid #eee6e3;
            display: flex;
            justify-content: space-between;
            margin-top: 28px;
            padding-top: 20px;
        }

        .btn-booking {
            background: var(--booking-maroon);
            border-color: var(--booking-maroon);
            color: #fff;
            font-weight: 700;
            min-height: 42px;
            padding: 9px 20px;
        }

        .btn-booking:hover,
        .btn-booking:focus {
            background: var(--booking-maroon-dark);
            color: #fff;
        }

        .btn-back {
            background: #fff;
            border: 1px solid #cfc5c3;
            color: var(--booking-ink);
        }

        .booking-error {
            background: #fff4f3;
            border: 1px solid #e8c8c5;
            color: #8b2520;
            display: none;
            margin-top: 16px;
            padding: 12px 14px;
        }

        .booking-card {
            background: #fff;
            border: 1px solid #e8e1df;
            border-radius: 6px;
            padding: 24px;
        }

        .booking-confirmation {
            margin: 0 auto;
            max-width: 620px;
            text-align: center;
        }

        .booking-success-icon {
            align-items: center;
            background: #f9eeee;
            border-radius: 50%;
            color: var(--booking-maroon);
            display: flex;
            font-size: 28px;
            height: 68px;
            justify-content: center;
            margin: 0 auto 18px;
            width: 68px;
        }

        .booking-confirmation h1 {
            color: var(--booking-maroon);
            font-size: 28px;
            margin: 0 0 10px;
        }

        .booking-code {
            color: var(--booking-maroon);
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        @media(max-width: 600px) { .booking-shell { padding: 15px 0 30px; }.booking-header { border-radius: 0; padding: 28px 12px 18px; }.booking-logo { height: 60px; }.booking-header h1 { font-size: 25px; }.wizard-steps li { font-size: 10px; }.wizard-step-number { height: 34px; width: 34px; }.wizard-steps:before { top: 16px; }.wizard-body { padding: 22px 15px; }.date-grid, .session-grid, .booking-type-grid { grid-template-columns: 1fr; }.summary-row { align-items: flex-start; flex-direction: column; gap: 3px; }.summary-value { text-align: left; } }
    </style>
</head>

<body>
    <main class="booking-shell">
        <div class="booking-wrap">
            <header class="booking-header">
                <img class="booking-logo" src="<?= base_url('aset/img/Logo-MH-Transparan-01.png') ?>"
                    alt="Mutiara Harapan Islamic School">
                <h1>Report Distribution Booking</h1>
                <p>Book your report distribution visit in a few easy steps.</p>
                <ol class="wizard-steps">
                    <li class="active"><span class="wizard-step-number">1</span>Intro</li>
                    <li><span class="wizard-step-number">2</span>Student</li>
                    <li><span class="wizard-step-number">3</span>Schedule</li>
                    <li><span class="wizard-step-number">4</span>Confirm</li>
                </ol>
            </header>
            <section class="wizard-body"><?php $this->load->view($content_view); ?></section>
        </div>
    </main>
</body>

</html>