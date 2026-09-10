<page backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm"
	backimg="https://report.mhis.link/images/hanya_logo_op.png" backimgw="50%">
	<style type="text/css">
		body {
			font-family: arial;
			font-size: 11pt;
			width: 8.5in
		}

		.table {
			border-collapse: collapse;
			border: solid 1px #0162b1;
			width: 100%
		}

		.table tr td,
		.table tr th {
			border: solid 1px #0162b1;
			padding: 3px;
		}

		.table tr th {
			font-weight: bold;
			text-align: center
		}

		.rgt {
			text-align: right;
		}

		.ctr {
			text-align: center;
		}

		.tbl {
			font-weight: bold
		}

		table tr td {
			vertical-align: top
		}

		.font_kecil {
			font-size: 12px
		}
	</style>
	<table>
		<tr>
			<td>
				<img src="https://report.mhis.link/images/Logo-MH-Transparan-01.png" width="150" style="margin-left:20px">
			</td>
			<td>
				<p style="text-align: center; margin-left:180px;font-size:24px;"><b>Student’s Progress Report<br>
						Development Class<br>
						Academic Year
						<?php echo $ta; ?>
					</b></p>
			</td>
		</tr>
	</table>
	<br><br>
	<table style="font-weight: bold;" class="table">
		<tr>
			<td style="width:400px">Student’s Name</td>
			<td style="width:515px">:
				<?php echo $det_siswa['nama']; ?>
			</td>
		</tr>
		<tr>
			<td style="width:400px">Class’s Name</td>
			<td style="width:540px">:
				<?php echo strtoupper($wali_kelas['nmkelas']); ?>
			</td>
		</tr>

		<tr>
			<td style="width:400px">Compiled By</td>
			<td style="width:515px">:
				<?php echo $wali_kelas['nmguru']; ?>
			</td>
		</tr>
		<tr>
			<td style="width:400px">Acknowledge By</td>
			<td style="width:515px">:
				<?php echo $det_raport['nama_kepsek']; ?>
			</td>
		</tr>
		<tr>
			<td style="width:400px">Reporting Period</td>
			<td style="width:515px">: <?php $year = date('Y', strtotime($det_raport['tgl_raport']));
			if($semester ==1){ ; ?>July - Sept<?php }else{?>Jan - Mar<?php } ?> <?php echo $year; ?>

			</td>
		</tr>
		<tr>
			<td style="width:400px">Date Submitted</td>
			<td style="width:515px">:
					<?php echo $this->config->item('kota'); ?>,
					<?php echo tjs($det_raport['tgl_raport'], "l"); ?><br>
			</td>
		</tr>
	</table>
	<br><br>
	<table>
		<tr>
			<td colspan="3">
				<h2><b>Summary of Student’s Performance</b></h2>
			</td>
		</tr>
	</table>
	<table class="table">
		<tr style="font-weight: bold;text-align: center;">
			<td style="width:80px;padding: 2px 2px;">Areas of Coverage</td>
			<td style="width:435px;padding: 2px 2px;">Areas of Achievement</td>
			<td style="width:435px;padding: 2px 2px;">Follow Up and Recommendation</td>
		</tr>
		<?php echo $nilai_utama; ?>
	</table>
	<br><br>


	<page backtop="30mm" backbottom="7mm" backleft="10mm" backright="10mm"
		backimg="https://report.mhis.link/images/hanya_logo_op.png" backimgw="50%">
		<table class="table">
			<tr style="font-weight: bold;text-align: center;">
				<td rowspan="2" style="width:425px;padding: 10px 5px;">Attendance</td>
				<td style="width:212px;padding: 10px 5px;">Sick</td>
				<td style="width:212px;padding: 10px 5px;"><?php echo $nilai_absensi['s']??""; ?> Days</td>
			</tr>
			<tr style="font-weight: bold;text-align: center;">
				<td style="width:212px;padding: 10px 5px;">Leave of absence</td>
				<td style="width:212px;padding: 10px 5px;"><?php echo $nilai_absensi['a']??""; ?> Days</td>
			</tr>


		</table>
		<br><br>
			<table>
				<tr>
					<td style="width:300px;text-align: center;">
						Acknowledged by,
						<br><br><br><br><br><br>
						<u><b>
								<?php echo $det_raport['nama_kepsek']; ?>
							</b></u><br>
						Head of Development Class
						<br>
					</td>
					<td style="width:380px;text-align: center;">

					</td>
					<td></td>
					<td style="text-align: center;">
						<?php
						if ($wali_kelas['tingkat'] != 9) {
							?>
							<?php echo $this->config->item('kota'); ?>,
							<?php echo tjs($det_raport['tgl_raport'], "l"); ?><br>
						<?php } else { ?>
							<?php echo $this->config->item('kota'); ?>,
							<?php echo tjs($det_raport['tgl_raport_kelas3'], "l"); ?><br>
						<?php } ?>
						<br><br><br><br><br>
						<u><b>
								<?php echo $wali_kelas['nmguru']; ?><br>
							</b></u>Homeroom Teacher<br>
					</td>
				</tr>
				<tr>
					<td style="text-align: center;">
					</td>
					<td style="text-align: center;">

					</td>
					<td></td>
					<td style="text-align: center;">
					</td>
				</tr>
			</table>
	</page>