<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= html_escape(isset($title) ? $title : 'Report Distribution Booking') ?></title>
    <link href="<?= base_url('aset/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('aset/css/light-bootstrap-dashboard-unminify.css') ?>" rel="stylesheet">
    <link href="<?= base_url('aset/plugins/fa/css/font-awesome.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('aset/css/select2.min.css') ?>" rel="stylesheet">
    <script src="<?= base_url('aset/js/jquery-1.10.2.js') ?>"></script>
    <script src="<?= base_url('aset/js/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('aset/js/select2.min.js') ?>"></script>
    <style>
        :root {
            --booking-maroon: #800000;
            --booking-maroon-dark: #5f0000;
            --booking-ink: #2d2525;
            --booking-muted: #756d6d;
            --booking-bg: #f7f5f4;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            background: var(--booking-bg);
            color: var(--booking-ink);
            font-family: "Helvetica Neue", Arial, sans-serif;
        }

        .booking-shell {
            min-height: 100vh;
            padding: 28px 15px 44px;
        }

        .booking-wrap {
            max-width: 1040px;
            margin: 0 auto;
        }

        .booking-brand {
            color: var(--booking-maroon);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin-bottom: 18px;
            text-transform: uppercase;
        }

        .booking-card {
            background: #fff;
            border: 1px solid #e8e1df;
            border-radius: 6px;
            box-shadow: 0 8px 28px rgba(57, 32, 32, .07);
            margin-bottom: 18px;
        }

        .booking-card-body {
            padding: 24px;
        }

        .booking-hero {
            background: var(--booking-maroon);
            border-radius: 6px;
            color: #fff;
            padding: 30px 28px;
            position: relative;
            overflow: hidden;
        }

        .booking-hero:after {
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 50%;
            content: "";
            height: 180px;
            position: absolute;
            right: -65px;
            top: -70px;
            width: 180px;
        }

        .booking-eyebrow {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            opacity: .78;
            text-transform: uppercase;
        }

        .booking-hero h1 {
            font-size: 30px;
            line-height: 1.2;
            margin: 9px 0 18px;
            position: relative;
            z-index: 1;
        }

        .booking-meta {
            display: flex;
            flex-wrap: wrap;
            font-size: 14px;
            gap: 8px 22px;
            opacity: .94;
        }

        .booking-meta strong {
            display: block;
            font-size: 11px;
            letter-spacing: .7px;
            margin-bottom: 3px;
            opacity: .7;
            text-transform: uppercase;
        }

        .booking-description {
            color: rgba(255, 255, 255, .86);
            font-size: 14px;
            line-height: 1.6;
            margin: 20px 0 0;
            max-width: 720px;
        }

        .booking-section-title {
            color: var(--booking-maroon);
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 16px;
        }

        .booking-label {
            color: var(--booking-ink);
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .booking-help {
            color: var(--booking-muted);
            font-size: 12px;
            margin-top: 7px;
        }

        .select2-container .select2-selection--single {
            border: 1px solid #d9d1cf;
            border-radius: 4px;
            height: 42px;
            padding: 7px 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .date-grid,
        .session-grid {
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
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .choice-card:hover,
        .choice-card.selected {
            border-color: var(--booking-maroon);
            box-shadow: 0 0 0 2px rgba(128, 0, 0, .08);
        }

        .choice-card.selected {
            background: #fffafa;
        }

        .choice-card input {
            margin-right: 8px;
            accent-color: var(--booking-maroon);
        }

        .choice-card strong {
            color: var(--booking-ink);
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

        .session-card {
            min-height: 92px;
        }

        .session-time {
            color: var(--booking-maroon);
            font-size: 16px;
            font-weight: 700;
        }

        .session-capacity {
            color: var(--booking-muted);
            font-size: 12px;
            margin-top: 7px;
        }

        .booking-type-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .booking-type-card {
            min-height: 92px;
        }

        .booking-type-card i {
            color: var(--booking-maroon);
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
            border-bottom: 0;
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

        .btn-booking {
            background: var(--booking-maroon);
            border-color: var(--booking-maroon);
            color: #fff;
            font-weight: 700;
            min-height: 44px;
            padding: 10px 18px;
        }

        .btn-booking:hover,
        .btn-booking:focus {
            background: var(--booking-maroon-dark);
            border-color: var(--booking-maroon-dark);
            color: #fff;
        }

        .booking-error {
            background: #fff4f3;
            border: 1px solid #e8c8c5;
            color: #8b2520;
            display: none;
            margin-bottom: 16px;
            padding: 12px 14px;
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

        .booking-confirmation {
            margin: 0 auto;
            max-width: 620px;
            text-align: center;
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

        <blade media|%20(max-width%3A%20600px)%20%7B%0D>.booking-shell {
            padding: 16px 10px 30px;
        }

        .booking-card-body {
            padding: 18px 15px;
        }

        .booking-hero {
            padding: 23px 18px;
        }

        .booking-hero h1 {
            font-size: 25px;
        }

        .date-grid,
        .session-grid,
        .booking-type-grid {
            grid-template-columns: 1fr;
        }

        .summary-row {
            align-items: flex-start;
            flex-direction: column;
            gap: 3px;
        }

        .summary-value {
            text-align: left;
        }

        .btn-booking {
            width: 100%;
        }
        }
    </style>
</head>

<body>
    <main class="booking-shell">
        <div class="booking-wrap">
            <div class="booking-brand"><i class="fa fa-calendar-check-o"></i> Report Distribution</div>
            <?php $this->load->view($content_view); ?>
        </div>
    </main>
</body>

</html>