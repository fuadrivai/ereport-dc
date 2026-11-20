<div class="card">
  <div class="header">
    <h4 class="title"><?= !empty($survey) ? 'Manage Survey' : 'Add Survey'; ?></h4>
  </div>
  <div class="content">
    <form id="survey_form" class="form-horizontal">
      <input type="hidden" name="id" value="<?= (int) (!empty($survey) ? $survey['id'] : 0); ?>">
      <div class="form-group">
        <label class="col-sm-3 control-label">Academic Year</label>
        <div class="col-sm-9">
          <select name="tahun_id" class="form-control" required>
            <?php foreach ($tahun as $row): ?>
            <option value="<?= (int) $row['id']; ?>"
              <?= !empty($survey) && (int) $survey['tahun_id'] === (int) $row['id'] ? 'selected' : ''; ?>>
              <?= htmlspecialchars($row['tahun']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label">Report Type</label>
        <div class="col-sm-9">
          <select name="report_type" class="form-control" required>
            <?php foreach (array('MIDTERM' => 'Midterm', 'FINAL' => 'Final') as $value => $label): ?>
            <option value="<?= $value; ?>"
              <?= !empty($survey) && $survey['report_type'] === $value ? 'selected' : ''; ?>><?= $label; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label">Title</label>
        <div class="col-sm-9"><input name="title" class="form-control"
            value="<?= !empty($survey) ? htmlspecialchars($survey['title']) : ''; ?>" required></div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label">Description</label>
        <div class="col-sm-9"><textarea name="description" class="form-control"
            rows="3"><?= !empty($survey) ? htmlspecialchars($survey['description']) : ''; ?></textarea></div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label">Settings</label>
        <div class="col-sm-9">
          <label><input type="checkbox" name="is_mandatory" value="Y"
              <?= empty($survey) || $survey['is_mandatory'] === 'Y' ? 'checked' : ''; ?>> Required</label>
          &nbsp;&nbsp;
          <label><input type="checkbox" name="is_active" value="Y"
              <?= !empty($survey) && $survey['is_active'] === 'Y' ? 'checked' : ''; ?>> Active</label>
        </div>
      </div>
      <div class="col-sm-offset-3 col-sm-9">
        <div class="survey-action-bar">
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Survey</button>
          <?php if (!empty($survey)): ?>
          <a class="btn btn-info" href="<?= base_url('stakeholder_survey/preview/' . $survey['id']); ?>"><i
              class="fa fa-eye"></i> Preview</a>
          <?php endif; ?>
          <a class="btn btn-default" href="<?= base_url('stakeholder_survey'); ?>"><i class="fa fa-arrow-left"></i>
            Back</a>
        </div>
      </div>
    </form>

    <?php if (!empty($survey)): ?>
    <hr>
    <div class="survey-section-heading">
      <h4>Questions</h4>
      <button class="btn btn-success" type="button" onclick="newQuestion()"><i class="fa fa-plus"></i> Add
        Question</button>
    </div>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Order</th>
          <th>Question</th>
          <th>Type</th>
          <th>Target Classes</th>
          <th>Required</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
            $class_names = array();
            foreach ($classes as $class) {
              $class_names[(int) $class['id']] = $class['nama'];
            }
          ?>
        <?php foreach ($questions as $question): ?>
        <tr>
          <td><?= (int) $question['sort_order']; ?></td>
          <td><?= htmlspecialchars($question['question']); ?></td>
          <td><?= htmlspecialchars($question['question_type']); ?></td>
          <td>
            <?php
                  $target_names = array();
                  foreach ($targets[$question['id']] as $class_id) {
                    if (isset($class_names[$class_id])) {
                      $target_names[] = htmlspecialchars($class_names[$class_id]);
                    }
                  }
                  echo !empty($target_names) ? implode(', ', $target_names) : 'All Classes';
                ?>
          </td>
          <td><?= $question['is_required'] === 'Y' ? 'Yes' : 'No'; ?></td>
          <td>
            <button class="btn btn-xs btn-success" type="button"
              onclick="editQuestion(<?= htmlspecialchars(json_encode(array_merge($question, array('options' => $options[$question['id']], 'targets' => $targets[$question['id']])))); ?>)">Edit</button>
            <button class="btn btn-xs btn-danger" type="button"
              onclick="deleteQuestion(<?= (int) $question['id']; ?>)">Delete</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<style>
  .survey-action-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 4px 0 18px;
  }

  .survey-action-bar .btn {
    min-height: 38px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
  }

  .survey-section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    width: 100%;
    clear: both;
    margin: 12px 0 10px;
  }

  .survey-section-heading h4 {
    margin: 0;
    display: block;
  }

  @media (max-width: 767px) {
    .survey-action-bar .btn {
      flex: 1 1 100%;
      justify-content: center;
    }

    .survey-section-heading {
      align-items: stretch;
      flex-direction: column;
    }
  }
</style>

<div class="modal" id="question_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="question_form" class="form-horizontal">
        <input type="hidden" name="survey_id" value="<?= (int) (!empty($survey) ? $survey['id'] : 0); ?>">
        <input type="hidden" name="question_id" id="question_id" value="0">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4>Question</h4>
        </div>
        <div class="modal-body">
          <div class="form-group"><label class="col-sm-3">Question</label>
            <div class="col-sm-9"><textarea name="question" id="question_text" class="form-control" required></textarea>
            </div>
          </div>
          <div class="form-group"><label class="col-sm-3">Type</label>
            <div class="col-sm-9"><select name="question_type" id="question_type" class="form-control">
                <option value="RATING">Rating 1-5</option>
                <option value="RADIO">Radio</option>
                <option value="CHECKBOX">Checkbox</option>
                <option value="ESSAY">Essay</option>
              </select></div>
          </div>
          <div class="form-group" id="rating_labels">
            <label class="col-sm-3">Rating Labels</label>
            <div class="col-sm-9">
              <?php for ($rating = 1; $rating <= 5; $rating++): ?>
                <div class="input-group" style="margin-bottom:5px;">
                  <span class="input-group-addon" style="min-width:95px;">Rating <?= $rating; ?></span>
                  <input name="rating_label[]" class="form-control" placeholder="Description for <?= $rating; ?>">
                </div>
              <?php endfor; ?>
            </div>
          </div>
          <div class="form-group"><label class="col-sm-3">Target Classes</label>
            <div class="col-sm-9"><label><input type="checkbox" name="target_all" id="target_all" value="Y"> All
                Classes</label>
              <div id="class_targets"><?php foreach ($classes as $class): ?><label class="checkbox-inline"><input
                    type="checkbox" name="class_ids[]" value="<?= (int) $class['id']; ?>">
                  <?= htmlspecialchars($class['nama']); ?></label><?php endforeach; ?></div>
            </div>
          </div>
          <div class="form-group"><label class="col-sm-3">Required</label>
            <div class="col-sm-9"><label><input type="checkbox" name="is_required" value="Y" checked></label></div>
          </div>
          <div class="form-group"><label class="col-sm-3">Active</label>
            <div class="col-sm-9"><label><input type="checkbox" name="is_active" value="Y" checked></label></div>
          </div>
          <div class="form-group"><label class="col-sm-3">Order</label>
            <div class="col-sm-9"><input type="number" name="sort_order" id="sort_order" class="form-control" value="0">
            </div>
          </div>
          <div class="form-group" id="essay_limit"><label class="col-sm-3">Max Characters</label>
            <div class="col-sm-9"><input type="number" name="max_length" class="form-control"></div>
          </div>
          <div class="form-group" id="choice_options">
            <label class="col-sm-3" id="choice_options_label">Radio Options</label>
            <div class="col-sm-9">
              <div id="option_rows">
                <div class="input-group" style="margin-bottom:5px;">
                  <input name="option_label[]" class="form-control" placeholder="Enter an option">
                  <span class="input-group-btn"><button type="button" class="btn btn-default"
                      onclick="removeRadioOption(this)" title="Remove option">&times;</button></span>
                </div>
              </div>
              <button type="button" class="btn btn-xs btn-success" onclick="addRadioOption()">+ Add Option</button>
            </div>
          </div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Question</button></div>
      </form>
    </div>
  </div>
</div>

<script>
  function newQuestion() {
    $('#question_form')[0].reset();
    $('#question_id').val('0');
    $('#option_rows').html(
      '<div class="input-group" style="margin-bottom:5px;"><input name="option_label[]" class="form-control" placeholder="Enter an option"><span class="input-group-btn"><button type="button" class="btn btn-default" onclick="removeRadioOption(this)" title="Remove option">&times;</button></span></div>'
      );
    updateQuestionTypeFields();
    $('#question_modal').modal('show');
  }

  function addRadioOption() {
    $('#option_rows').append(
      '<div class="input-group" style="margin-bottom:5px;"><input name="option_label[]" class="form-control" placeholder="Enter an option"><span class="input-group-btn"><button type="button" class="btn btn-default" onclick="removeRadioOption(this)" title="Remove option">&times;</button></span></div>'
      );
  }

  function removeRadioOption(button) {
    if ($('#option_rows .input-group').length > 1) {
      $(button).closest('.input-group').remove();
    } else {
      $(button).closest('.input-group').find('input').val('');
    }
  }

  function updateQuestionTypeFields() {
    var type = $('#question_type').val();
    $('#choice_options').toggle(type === 'RADIO' || type === 'CHECKBOX');
    $('#choice_options_label').text(type === 'CHECKBOX' ? 'Checkbox Options' : 'Radio Options');
    $('#rating_labels').toggle(type === 'RATING');
    $('#essay_limit').toggle(type === 'ESSAY');
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
    $.each(question.targets || [], function (_, classId) {
      $('#class_targets input[name="class_ids[]"][value="' + classId + '"]').prop('checked', true);
    });
    $('#target_all').prop('checked', !(question.targets && question.targets.length));
    var optionHtml = '';
    if (question.question_type === 'RATING') {
      $('#rating_labels input[name="rating_label[]"]').each(function (index) {
        var option = (question.options || [])[index];
        $(this).val(option ? option.option_label : '');
      });
    }
    if (question.question_type === 'RADIO' || question.question_type === 'CHECKBOX') {
      $.each(question.options || [], function (_, option) {
        optionHtml +=
          '<div class="input-group" style="margin-bottom:5px;"><input name="option_label[]" class="form-control" value="' +
          $('<div>').text(option.option_label).html() +
          '"><span class="input-group-btn"><button type="button" class="btn btn-default" onclick="removeRadioOption(this)" title="Remove option">&times;</button></span></div>';
      });
    }
    if (!optionHtml) optionHtml =
      '<div class="input-group" style="margin-bottom:5px;"><input name="option_label[]" class="form-control" placeholder="Enter an option"><span class="input-group-btn"><button type="button" class="btn btn-default" onclick="removeRadioOption(this)" title="Remove option">&times;</button></span></div>';
    $('#option_rows').html(optionHtml);
    updateQuestionTypeFields();
    $('#question_modal').modal('show');
  }

  function deleteQuestion(id) {
    if (!confirm('Delete this question?')) return;
    $.post(base_url + 'stakeholder_survey/delete_question/' + id, function () {
      location.reload();
    }, 'json');
  }
  $(function () {
    $('#question_type').on('change', updateQuestionTypeFields);
    $('#survey_form').on('submit', function () {
      $.post(base_url + 'stakeholder_survey/save_survey', $(this).serialize(), function (response) {
        if (response.status === 'ok') location.href = base_url + 'stakeholder_survey/edit/' + response.id;
        else alert(response.data);
      }, 'json');
      return false;
    });
    $('#question_form').on('submit', function () {
      $.post(base_url + 'stakeholder_survey/save_question', $(this).serialize(), function (response) {
        if (response.status === 'ok') location.reload();
        else alert(response.data);
      }, 'json');
      return false;
    });
  });
</script>