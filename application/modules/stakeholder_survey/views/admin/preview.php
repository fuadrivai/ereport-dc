<style>
  .survey-preview {
    max-width: 860px;
    margin: 0 auto 30px;
  }
  .survey-preview .preview-hero {
    position: relative;
    overflow: hidden;
    min-height: 170px;
    padding: 28px 34px;
    color: #fff;
    background: linear-gradient(135deg, #102b46 0%, #1b4964 100%);
    border-radius: 8px 8px 0 0;
  }
  .survey-preview .preview-hero:after {
    content: "";
    position: absolute;
    width: 220px;
    height: 220px;
    right: -95px;
    top: -125px;
    border: 28px solid rgba(143, 184, 62, .22);
    border-radius: 50%;
  }
  .survey-preview .preview-badge {
    display: inline-block;
    margin-bottom: 12px;
    padding: 5px 10px;
    color: #dbeab7;
    background: rgba(255, 255, 255, .1);
    border: 1px solid rgba(255, 255, 255, .18);
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
  }
  .survey-preview .preview-hero h3 {
    position: relative;
    z-index: 1;
    margin: 0 0 8px;
    color: #fff;
    font-size: 28px;
    line-height: 1.25;
  }
  .survey-preview .preview-description {
    position: relative;
    z-index: 1;
    max-width: 650px;
    margin: 0;
    color: #dce8ef;
  }
  .survey-preview .preview-body {
    padding: 28px 34px 34px;
    background: #fff;
    border: 1px solid #e4eaf0;
    border-top: 0;
    border-radius: 0 0 8px 8px;
  }
  .survey-preview .preview-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 18px;
    padding-bottom: 16px;
    border-bottom: 1px solid #edf1f4;
  }
  .survey-preview .preview-count {
    color: #6b7b8d;
    font-size: 13px;
  }
  .survey-preview .preview-question {
    margin: 0 0 14px;
    padding: 20px;
    background: #f9fbfc;
    border: 1px solid #edf1f4;
    border-radius: 8px;
  }
  .survey-preview .preview-question-title {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin: 0 0 16px;
  }
  .survey-preview .question-number {
    flex: 0 0 32px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    background: #102b46;
    border-radius: 50%;
    font-size: 13px;
    font-weight: 700;
  }
  .survey-preview .question-label {
    padding-top: 3px;
    color: #203246;
    font-size: 15px;
  }
  .survey-preview .required-mark {
    color: #b33a3a;
  }
  .survey-preview .rating-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }
  .survey-preview .rating-preview span {
    width: 48px;
    height: 48px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #405366;
    background: #fff;
    border: 1px solid #d9e1e7;
    border-radius: 8px;
    font-weight: 700;
  }
  .survey-preview .radio-preview {
    display: grid;
    gap: 8px;
  }
  .survey-preview .radio-preview label {
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 0;
    padding: 10px 12px;
    background: #fff;
    border: 1px solid #dfe6eb;
    border-radius: 7px;
  }
  .survey-preview .preview-essay {
    min-height: 92px;
  }
  .survey-preview .preview-target {
    margin-top: 14px;
    color: #738396;
    font-size: 12px;
  }
  .survey-preview .preview-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 24px;
  }
  .survey-preview .preview-actions .btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
  }
  @media (max-width: 767px) {
    .survey-preview {
      margin: 0 -10px 20px;
    }
    .survey-preview .preview-hero,
    .survey-preview .preview-body {
      padding-left: 20px;
      padding-right: 20px;
    }
    .survey-preview .preview-hero h3 {
      font-size: 22px;
    }
    .survey-preview .preview-actions .btn {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<div class="survey-preview">
  <div class="preview-hero">
    <span class="preview-badge">Survey Preview</span>
    <h3><?= htmlspecialchars($survey['title']); ?></h3>
    <?php if (!empty($survey['description'])): ?>
      <p class="preview-description"><?= nl2br(htmlspecialchars($survey['description'])); ?></p>
    <?php endif; ?>
  </div>

  <div class="preview-body">
    <div class="preview-summary">
      <strong><?= htmlspecialchars($survey['report_type']); ?> Report</strong>
      <span class="preview-count"><?= count($questions); ?> question<?= count($questions) === 1 ? '' : 's'; ?></span>
    </div>

    <?php foreach ($questions as $index => $question): ?>
      <div class="preview-question">
        <div class="preview-question-title">
          <span class="question-number"><?= $index + 1; ?></span>
          <span class="question-label">
            <?= htmlspecialchars($question['question']); ?>
            <?php if ($question['is_required'] === 'Y'): ?>
              <span class="required-mark">*</span>
            <?php endif; ?>
          </span>
        </div>

        <?php if ($question['question_type'] === 'RATING'): ?>
          <div class="rating-preview" aria-label="Rating from 1 to 5">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <span><?= $i; ?></span>
            <?php endfor; ?>
          </div>
        <?php elseif ($question['question_type'] === 'RADIO'): ?>
          <div class="radio-preview">
            <?php foreach ($question['options'] as $option): ?>
              <label><input type="radio" disabled> <?= htmlspecialchars($option['option_label']); ?></label>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <textarea class="form-control preview-essay" disabled maxlength="<?= (int) $question['max_length']; ?>" placeholder="Essay response"></textarea>
        <?php endif; ?>

        <?php if (!empty($question['targets'])): ?>
          <div class="preview-target">
            <i class="fa fa-users"></i>
            Target: <?= htmlspecialchars(implode(', ', array_column($question['targets'], 'nama'))); ?>
          </div>
        <?php else: ?>
          <div class="preview-target"><i class="fa fa-users"></i> Target: All Classes</div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

    <?php if (empty($questions)): ?>
      <div class="alert alert-info">This survey does not have any questions yet.</div>
    <?php endif; ?>

    <div class="preview-actions">
      <a class="btn btn-default" href="<?= base_url('stakeholder_survey/edit/' . $survey['id']); ?>">
        <i class="fa fa-arrow-left"></i> Back to Survey
      </a>
    </div>
  </div>
</div>
