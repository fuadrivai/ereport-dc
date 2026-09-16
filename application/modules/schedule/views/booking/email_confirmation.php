<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="x-apple-disable-message-reformatting" />
    <title>Development Class || MHIS Bintaro</title>
</head>

<body style="
      margin: 0;
      padding: 0;
      background-color: #f9f7f4;
      font-family:
        &quot;Arial&quot;, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    ">
    <center style="width: 100%; background-color: #f9f7f4">
        <!-- Decorative Top Border -->
        <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
            style="max-width: 600px">
            <tr>
                <td height="4" style="
              background: linear-gradient(
                90deg,
                #800000 0%,
                #d4af37 50%,
                #800000 100%
              );
            ">
                </td>
            </tr>
        </table>

        <!-- Main Container -->
        <table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
            style="max-width: 600px; margin: 0 auto">
            <!-- Header with Logo & Title -->
            <tr>
                <td align="center" style="
              padding: 40px 30px 25px;
              background-color: #ffffff;
              border-left: 1px solid #f0f0f0;
              border-right: 1px solid #f0f0f0;
            ">
                    <!-- School Logo -->
                    <img src="https://bangka.mutiaraharapan.sch.id/wp-content/uploads/2020/03/LOGO-5-1536x864-1-1024x576.png"
                        alt="Mutiara Harapan Islamic School" width="200" height="61" border="0" style="
                display: block;
                height: auto;
                max-width: 200px;
                width: 100%;
                margin: 0 auto 20px;
              " />

                    <!-- Page Title -->
                    <h3 style="
                margin: 0 0 15px;
                font-size: 22px;
                color: #800000;
                font-weight: bold;
                letter-spacing: 0.5px;
              ">
                        Report Distribution Booking Confirmation
                    </h3>

                    <!-- Arabic Greeting -->
                    <p style="
                margin: 0 0 10px;
                font-size: 20px;
                color: #800000;
                font-weight: bold;
                line-height: 1.4;
              ">
                        ٱلسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ ٱللَّٰهِ وَبَرَكَاتُهُ
                    </p>
                </td>
            </tr>

            <!-- Main Content Card -->
            <tr>
                <td align="center" style="
              padding: 0 30px 35px;
              background-color: #ffffff;
              border-left: 1px solid #f0f0f0;
              border-right: 1px solid #f0f0f0;
            ">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                        style="background-color: #ffffff">
                        <tr>
                            <td style="
                    padding: 0 0 25px;
                    text-align: left;
                    color: #444444;
                    font-size: 15px;
                    line-height: 1.7;
                  ">
                                <p>Dear Mr./Mrs. <?= html_escape($parent_name) ?>,</p>
                                <p>Your report distribution appointment has been successfully registered.</p>
                                <p><strong>Booking Details</strong></p>
                                <ul>
                                    <li>Booking Code: <?= html_escape($booking_code) ?></li>
                                    <li>Student: <?= html_escape($student_name) ?></li>
                                    <li>Report: <?= html_escape($report_title) ?></li>
                                    <li>Semester: <?= html_escape($semester) ?></li>
                                    <li>Report Type: <?= html_escape($report_type) ?></li>
                                    <li>Type: <?= html_escape($type_label) ?></li>
                                    <?php if ($type_label === 'Therapy') { ?><li>Phycologist:
                                        <?= html_escape($therapist_name) ?></li><?php } ?>
                                    <li>Date: <?= html_escape(date('l, d F Y', strtotime($distribution_date))) ?></li>
                                    <li>Time: <?= html_escape(date('H:i', strtotime($start_time))) ?> -
                                        <?= html_escape(date('H:i', strtotime($end_time))) ?></li>
                                </ul>
                                <?php if (!empty($gmeet_link)) { ?>
                                <p>Please join the meeting at the scheduled time.</p>
                                <p><a href="<?= html_escape($gmeet_link) ?>"
                                        style="display:inline-block;padding:12px 18px;background-color:#800000;color:#ffffff;text-decoration:none">Join
                                        Google Meet</a></p>
                                <?php } else { ?>
                                <p>Your Google Meet link is being prepared and will be available shortly.</p>
                                <?php } ?>
                            </td>
                        </tr>

                        <!-- Important Instructions -->

                        <!-- Welcome Message -->
                        <tr>
                            <td style="padding: 25px 0 10px; text-align: left">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                                    style="
                      background: linear-gradient(to right, #fdf8f3, #faf9f7);
                      border-radius: 8px;
                      padding: 25px;
                    ">
                                    <tr>
                                        <td style="
                          color: #444444;
                          font-size: 16px;
                          line-height: 1.7;
                          text-align: center;
                        ">
                                            <p style="
                            margin: 0 0 15px;
                            font-weight: bold;
                            color: #800000;
                          ">
                                                Please keep this email as your booking confirmation.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Closing Section -->
            <tr>
                <td align="center" style="
              padding: 30px 30px 40px;
              background-color: #ffffff;
              border-left: 1px solid #f0f0f0;
              border-right: 1px solid #f0f0f0;
              border-bottom: 1px solid #f0f0f0;
            ">
                    <!-- Arabic Closing -->
                    <p style="
                margin: 0 0 20px;
                font-size: 20px;
                color: #800000;
                font-weight: bold;
                line-height: 1.4;
              ">
                        وَالسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللَّهِ وَبَرَكَاتُهُ
                    </p>

                    <!-- School Signature -->
                    <p style="
                margin: 0;
                color: #333333;
                font-size: 15px;
                line-height: 1.6;
              ">
                        Warm regards,<br />
                        <strong style="
                  font-size: 18px;
                  color: #800000;
                  display: block;
                  margin: 15px 0 5px;
                  letter-spacing: 0.5px;
                ">Mutiara
                            Harapan Islamic School</strong>
                        <em style="color: #d4af37; font-size: 14px">Home of The Champions</em>
                    </p>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td align="center" style="
              padding: 25px 30px;
              background-color: #f5f1ec;
              color: #777777;
              font-size: 12px;
              line-height: 1.5;
              border-top: 1px solid #e8e4e0;
            ">
                    <p style="margin: 0 0 10px">
                        &copy; 2025 Mutiara Harapan Islamic School. All Rights Reserved.
                    </p>
                    <p style="margin: 0; font-size: 11px; color: #999999">
                        Jl. Pondok Kacang Raya No.2 Pondok Kacang Timur, Pondok Aren
                        Tangerang Selatan – 15426
                    </p>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>