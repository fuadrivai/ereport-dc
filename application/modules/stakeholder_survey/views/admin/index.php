<div class="card">
  <div class="header">
    <h4 class="title">Stakeholder Survey</h4>
  </div>
  <div class="content">
    <div class="form-inline" style="margin-bottom:15px;">
      <a class="btn btn-success" href="<?= base_url('stakeholder_survey/edit/0'); ?>">Tambah Survei</a>
    </div>
    <table class="table table-striped" id="survey_table">
      <thead><tr><th>Periode</th><th>Judul</th><th>Wajib</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php if (!empty($surveys)): ?>
          <?php foreach ($surveys as $survey): ?>
            <tr>
              <td><?= htmlspecialchars($survey['tahun'] . ' - ' . $survey['report_type']); ?></td>
              <td><?= htmlspecialchars($survey['title']); ?></td>
              <td><?= $survey['is_mandatory'] === 'Y' ? 'Ya' : 'Tidak'; ?></td>
              <td>
                <?php if ($survey['is_active'] === 'Y'): ?>
                  <span class="label label-success">Aktif</span>
                <?php else: ?>
                  <span class="label label-default">Tidak aktif</span>
                <?php endif; ?>
              </td>
              <td>
                <a class="btn btn-xs btn-info" href="<?= base_url('stakeholder_survey/edit/' . (int) $survey['id']); ?>">Kelola</a>
                <a class="btn btn-xs btn-success" href="<?= base_url('stakeholder_survey/results/' . (int) $survey['id']); ?>">Hasil</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center">Belum ada survei.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
