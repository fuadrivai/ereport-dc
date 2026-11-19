<div class="card">
  <div class="header">
    <h4 class="title"><?= !empty($survey) ? 'Kelola Survei' : 'Tambah Survei'; ?></h4>
  </div>
  <div class="content">
    <form id="survey_form" class="form-horizontal">
      <input type="hidden" name="id" value="<?= (int) (!empty($survey) ? $survey['id'] : 0); ?>">
      <div class="form-group">
        <label class="col-sm-3 control-label">Tahun Ajaran</label>
        <div class="col-sm-9">
          <select name="tahun_id" class="form-control" required>
            <?php foreach ($tahun as $row): ?>
              <option value="<?= (int) $row['id']; ?>" <?= !empty($survey) && (int) $survey['tahun_id'] === (int) $row['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($row['tahun']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label">Tipe Raport</label>
        <div class="col-sm-9">
          <select name="report_type" class="form-control" required>
            <?php foreach (array('MIDTERM' => 'Midterm', 'FINAL' => 'Final') as $value => $label): ?>
              <option value="<?= $value; ?>" <?= !empty($survey) && $survey['report_type'] === $value ? 'selected' : ''; ?>><?= $label; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label">Judul</label>
        <div class="col-sm-9"><input name="title" class="form-control" value="<?= !empty($survey) ? htmlspecialchars($survey['title']) : ''; ?>" required></div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label">Deskripsi</label>
        <div class="col-sm-9"><textarea name="description" class="form-control" rows="3"><?= !empty($survey) ? htmlspecialchars($survey['description']) : ''; ?></textarea></div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label">Pengaturan</label>
        <div class="col-sm-9">
          <label><input type="checkbox" name="is_mandatory" value="Y" <?= empty($survey) || $survey['is_mandatory'] === 'Y' ? 'checked' : ''; ?>> Wajib</label>
          &nbsp;&nbsp;
          <label><input type="checkbox" name="is_active" value="Y" <?= !empty($survey) && $survey['is_active'] === 'Y' ? 'checked' : ''; ?>> Aktif</label>
        </div>
      </div>
      <div class="col-sm-offset-3 col-sm-9">
        <button type="submit" class="btn btn-primary">Simpan Survei</button>
      </div>
    </form>
    <div class="col-sm-offset-3 col-sm-9" style="margin-top:10px;">
      <?php if (!empty($survey)): ?>
        <a class="btn btn-info" href="<?= base_url('stakeholder_survey/preview/' . $survey['id']); ?>">Preview</a>
      <?php endif; ?>
      <a class="btn btn-default" href="<?= base_url('stakeholder_survey'); ?>">Kembali</a>
    </div>

    <?php if (!empty($survey)): ?>
      <hr>
      <h4>Pertanyaan</h4>
      <table class="table table-striped">
        <thead><tr><th>Urutan</th><th>Pertanyaan</th><th>Jenis</th><th>Wajib</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php foreach ($questions as $question): ?>
            <tr>
              <td><?= (int) $question['sort_order']; ?></td>
              <td><?= htmlspecialchars($question['question']); ?></td>
              <td><?= htmlspecialchars($question['question_type']); ?></td>
              <td><?= $question['is_required'] === 'Y' ? 'Ya' : 'Tidak'; ?></td>
              <td>
                <button class="btn btn-xs btn-success" type="button" onclick="editQuestion(<?= htmlspecialchars(json_encode(array_merge($question, array('options' => $options[$question['id']], 'targets' => $targets[$question['id']])))); ?>)">Edit</button>
                <button class="btn btn-xs btn-danger" type="button" onclick="deleteQuestion(<?= (int) $question['id']; ?>)">Hapus</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <button class="btn btn-success" type="button" onclick="newQuestion()">Tambah Pertanyaan</button>
    <?php endif; ?>
  </div>
</div>

<div class="modal" id="question_modal">
  <div class="modal-dialog"><div class="modal-content">
    <form id="question_form" class="form-horizontal">
      <input type="hidden" name="survey_id" value="<?= (int) (!empty($survey) ? $survey['id'] : 0); ?>">
      <input type="hidden" name="question_id" id="question_id" value="0">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4>Pertanyaan</h4></div>
      <div class="modal-body">
        <div class="form-group"><label class="col-sm-3">Pertanyaan</label><div class="col-sm-9"><textarea name="question" id="question_text" class="form-control" required></textarea></div></div>
        <div class="form-group"><label class="col-sm-3">Jenis</label><div class="col-sm-9"><select name="question_type" id="question_type" class="form-control"><option value="RATING">Rating 1-5</option><option value="RADIO">Radio</option><option value="ESSAY">Essay</option></select></div></div>
        <div class="form-group"><label class="col-sm-3">Target Kelas</label><div class="col-sm-9"><label><input type="checkbox" name="target_all" id="target_all" value="Y"> Semua Kelas</label><div id="class_targets"><?php foreach ($classes as $class): ?><label class="checkbox-inline"><input type="checkbox" name="class_ids[]" value="<?= (int) $class['id']; ?>"> <?= htmlspecialchars($class['nama']); ?></label><?php endforeach; ?></div></div></div>
        <div class="form-group"><label class="col-sm-3">Wajib</label><div class="col-sm-9"><label><input type="checkbox" name="is_required" value="Y" checked></label></div></div>
        <div class="form-group"><label class="col-sm-3">Aktif</label><div class="col-sm-9"><label><input type="checkbox" name="is_active" value="Y" checked></label></div></div>
        <div class="form-group"><label class="col-sm-3">Urutan</label><div class="col-sm-9"><input type="number" name="sort_order" id="sort_order" class="form-control" value="0"></div></div>
        <div class="form-group" id="essay_limit"><label class="col-sm-3">Max Karakter</label><div class="col-sm-9"><input type="number" name="max_length" class="form-control"></div></div>
        <div class="form-group" id="radio_options"><label class="col-sm-3">Opsi Radio</label><div class="col-sm-9" id="option_rows"><input name="option_label[]" class="form-control" style="margin-bottom:5px;"></div></div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan Pertanyaan</button></div>
    </form>
  </div></div>
</div>

<script>
function newQuestion() {
  $('#question_form')[0].reset();
  $('#question_id').val('0');
  $('#option_rows').html('<input name="option_label[]" class="form-control" style="margin-bottom:5px;">');
  $('#question_modal').modal('show');
}
function editQuestion(question) {
  $('#question_id').val(question.id);
  $('#question_text').val(question.question);
  $('#question_type').val(question.question_type);
  $('#sort_order').val(question.sort_order);
  $('input[name="is_required"]').prop('checked', question.is_required === 'Y');
  $('input[name="is_active"]').prop('checked', question.is_active === 'Y');
  $('#essay_limit input[name="max_length"]').val(question.max_length || '');
  $('#class_targets input[name="class_ids[]"]').prop('checked', false);
  $.each(question.targets || [], function(_, classId) {
    $('#class_targets input[name="class_ids[]"][value="' + classId + '"]').prop('checked', true);
  });
  $('#target_all').prop('checked', !(question.targets && question.targets.length));
  var optionHtml = '';
  if (question.question_type === 'RADIO') {
    $.each(question.options || [], function(_, option) {
      optionHtml += '<input name="option_label[]" class="form-control" style="margin-bottom:5px;" value="' + $('<div>').text(option.option_label).html() + '">';
    });
  }
  if (!optionHtml) optionHtml = '<input name="option_label[]" class="form-control" style="margin-bottom:5px;">';
  $('#option_rows').html(optionHtml);
  $('#question_modal').modal('show');
}
function deleteQuestion(id) {
  if (!confirm('Hapus pertanyaan ini?')) return;
  $.post(base_url + 'stakeholder_survey/delete_question/' + id, function() { location.reload(); }, 'json');
}
$(function() {
  $('#survey_form').on('submit', function() {
    $.post(base_url + 'stakeholder_survey/save_survey', $(this).serialize(), function(response) {
      if (response.status === 'ok') location.href = base_url + 'stakeholder_survey/edit/' + response.id;
      else alert(response.data);
    }, 'json');
    return false;
  });
  $('#question_form').on('submit', function() {
    $.post(base_url + 'stakeholder_survey/save_question', $(this).serialize(), function(response) {
      if (response.status === 'ok') location.reload(); else alert(response.data);
    }, 'json');
    return false;
  });
});
</script>
