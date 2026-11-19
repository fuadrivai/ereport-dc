<div class="row"><div class="col-md-8 col-md-offset-2"><div class="card">
  <div class="header"><h4 class="title">Preview: <?= htmlspecialchars($survey['title']); ?></h4></div>
  <div class="content">
    <?php if (!empty($survey['description'])): ?><p><?= nl2br(htmlspecialchars($survey['description'])); ?></p><?php endif; ?>
    <?php foreach ($questions as $question): ?>
      <div class="form-group">
        <label><?= htmlspecialchars($question['question']); ?> <?= $question['is_required'] === 'Y' ? '<span class="text-danger">*</span>' : ''; ?></label>
        <?php if ($question['question_type'] === 'RATING'): ?>
          <?php for ($i = 1; $i <= 5; $i++): ?><label class="radio-inline"><input type="radio" disabled> <?= $i; ?></label><?php endfor; ?>
        <?php elseif ($question['question_type'] === 'RADIO'): ?>
          <?php foreach ($question['options'] as $option): ?><div class="radio"><label><input type="radio" disabled> <?= htmlspecialchars($option['option_label']); ?></label></div><?php endforeach; ?>
        <?php else: ?><textarea class="form-control" disabled maxlength="<?= (int) $question['max_length']; ?>"></textarea><?php endif; ?>
        <?php if (!empty($question['targets'])): ?><div class="text-muted">Target: <?php echo htmlspecialchars(implode(', ', array_column($question['targets'], 'nama'))); ?></div><?php endif; ?>
      </div>
    <?php endforeach; ?>
    <a class="btn btn-default" href="<?= base_url('stakeholder_survey/edit/' . $survey['id']); ?>">Kembali</a>
  </div>
</div></div></div>
