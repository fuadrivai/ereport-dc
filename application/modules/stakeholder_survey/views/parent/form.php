<?php
$escape = function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$year_start = substr($tahun['tahun'], 0, 4);
$semester = substr($tahun['tahun'], -1);
$academic_year = $year_start . '/' . ((int) $year_start + 1);
$old_answers = is_array($old_answers) ? $old_answers : array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#102b46">
  <title><?= $escape($survey['title']); ?> | MHIS</title>
  <style>
    :root {
      --navy: #102b46;
      --navy-soft: #1b3c5a;
      --green: #8fb83e;
      --green-dark: #668d25;
      --ink: #203246;
      --muted: #6b7b8d;
      --line: #e4eaf0;
      --paper: #fff;
      --page: #f3f6f8;
      --danger: #a43838;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      color: var(--ink);
      background: var(--page);
      font: 15px/1.6 "Segoe UI", Arial, sans-serif;
    }

    .topbar {
      height: 74px;
      display: flex;
      align-items: center;
      background: var(--paper);
      border-bottom: 1px solid var(--line);
    }

    .topbar-inner {
      width: min(100% - 40px, 1040px);
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
    }

    .brand img {
      display: block;
      width: 215px;
      max-height: 52px;
      object-fit: contain;
      object-position: left center;
    }

    .portal-label {
      color: var(--muted);
      font-size: 12px;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
    }

    .page {
      width: min(100% - 32px, 850px);
      margin: 36px auto 56px;
    }

    .hero {
      position: relative;
      overflow: hidden;
      padding: 34px 38px;
      color: #fff;
      background: linear-gradient(120deg, var(--navy), #1b4964);
      border-radius: 18px 18px 0 0;
    }

    .hero:after {
      content: "";
      position: absolute;
      width: 240px;
      height: 240px;
      right: -72px;
      top: -135px;
      border: 34px solid rgba(143, 184, 62, .2);
      border-radius: 50%;
    }

    .eyebrow {
      margin: 0 0 9px;
      color: #c8df9d;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
    }

    h1 {
      position: relative;
      z-index: 1;
      margin: 0;
      font-size: clamp(24px, 4vw, 32px);
      line-height: 1.25;
    }

    .hero-description {
      position: relative;
      z-index: 1;
      max-width: 620px;
      margin: 12px 0 0;
      color: #e1eaf0;
    }

    .period {
      position: relative;
      z-index: 1;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-top: 21px;
      padding: 7px 12px;
      color: #fff;
      background: rgba(255,255,255,.12);
      border: 1px solid rgba(255,255,255,.2);
      border-radius: 999px;
      font-size: 13px;
    }

    .form-shell {
      padding: 28px 38px 34px;
      background: var(--paper);
      border-radius: 0 0 18px 18px;
      box-shadow: 0 12px 34px rgba(16, 43, 70, .08);
    }

    .respondent {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
      padding: 14px 16px;
      background: #f7f9fa;
      border: 1px solid var(--line);
      border-radius: 12px;
    }

    .avatar {
      width: 40px;
      height: 40px;
      display: grid;
      flex: 0 0 40px;
      place-items: center;
      color: var(--navy);
      background: #e8f0d9;
      border-radius: 50%;
      font-weight: 700;
    }

    .respondent small {
      display: block;
      color: var(--muted);
      font-size: 12px;
    }

    .progress-card {
      margin-bottom: 24px;
      padding: 16px 18px;
      border: 1px solid var(--line);
      border-radius: 12px;
    }

    .progress-copy {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 9px;
      color: var(--muted);
      font-size: 13px;
    }

    .progress-copy strong { color: var(--navy); }

    .progress-track {
      height: 7px;
      overflow: hidden;
      background: #edf1f3;
      border-radius: 8px;
    }

    .progress-value {
      width: 0;
      height: 100%;
      background: linear-gradient(90deg, var(--green), #b2d16e);
      border-radius: inherit;
      transition: width .2s ease;
    }

    .alert {
      margin: 0 0 22px;
      padding: 13px 15px;
      color: var(--danger);
      background: #fff3f1;
      border: 1px solid #f0d4cf;
      border-radius: 10px;
    }

    .question-card {
      min-width: 0;
      margin: 0 0 15px;
      padding: 20px;
      border: 1px solid var(--line);
      border-radius: 13px;
      transition: border-color .16s ease, box-shadow .16s ease;
    }

    .question-card:focus-within {
      border-color: #b6cc8b;
      box-shadow: 0 0 0 3px rgba(143,184,62,.1);
    }

    .question-title {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin: 0 0 16px;
      color: var(--navy);
      font-size: 16px;
      font-weight: 650;
    }

    .question-number {
      display: grid;
      flex: 0 0 28px;
      width: 28px;
      height: 28px;
      place-items: center;
      color: var(--green-dark);
      background: #f0f5e7;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
    }

    .required {
      margin-left: 4px;
      color: #bc4e48;
    }

    .optional {
      display: inline-block;
      margin-left: 7px;
      color: var(--muted);
      font-size: 12px;
      font-weight: 400;
    }

    .rating-options {
      display: grid;
      grid-template-columns: repeat(5, minmax(48px, 1fr));
      gap: 9px;
    }

    .rating-option, .choice-option {
      position: relative;
      display: block;
      cursor: pointer;
    }

    .rating-option input, .choice-option input {
      position: absolute;
      width: 1px;
      height: 1px;
      opacity: 0;
    }

    .rating-option span {
      min-height: 58px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 1px;
      color: #44566a;
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 10px;
      font-size: 17px;
      font-weight: 700;
      transition: all .15s ease;
    }

    .rating-option small {
      color: var(--muted);
      font-size: 10px;
      font-weight: 500;
    }

    .rating-option input:checked + span,
    .choice-option input:checked + span {
      color: var(--navy);
      background: #f4f8eb;
      border-color: var(--green);
      box-shadow: inset 0 0 0 1px var(--green);
    }

    .rating-option input:focus-visible + span,
    .choice-option input:focus-visible + span {
      outline: 3px solid rgba(143,184,62,.35);
      outline-offset: 2px;
    }

    .rating-scale {
      display: flex;
      justify-content: space-between;
      margin-top: 8px;
      color: var(--muted);
      font-size: 12px;
    }

    .choice-list {
      display: grid;
      gap: 9px;
    }

    .choice-option span {
      min-height: 48px;
      display: flex;
      align-items: center;
      gap: 11px;
      padding: 10px 13px;
      color: #394d60;
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 10px;
      transition: all .15s ease;
    }

    .choice-mark {
      width: 17px;
      height: 17px;
      flex: 0 0 17px;
      border: 1.5px solid #aeb9c4;
      border-radius: 50%;
    }

    .choice-option input:checked + span .choice-mark {
      border: 5px solid var(--green);
    }

    textarea {
      width: 100%;
      min-height: 116px;
      resize: vertical;
      padding: 12px 14px;
      color: var(--ink);
      background: #fff;
      border: 1px solid #dce4ea;
      border-radius: 10px;
      font: inherit;
    }

    textarea:focus {
      outline: 0;
      border-color: var(--green);
      box-shadow: 0 0 0 3px rgba(143,184,62,.12);
    }

    .character-count {
      margin-top: 5px;
      color: var(--muted);
      text-align: right;
      font-size: 12px;
    }

    .submit-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      margin-top: 24px;
      padding-top: 22px;
      border-top: 1px solid var(--line);
    }

    .privacy-note {
      max-width: 390px;
      color: var(--muted);
      font-size: 12px;
    }

    .submit-button {
      min-height: 48px;
      padding: 0 23px;
      color: #fff;
      background: var(--green-dark);
      border: 0;
      border-radius: 10px;
      box-shadow: 0 5px 12px rgba(102,141,37,.2);
      cursor: pointer;
      font: inherit;
      font-weight: 700;
      transition: background .15s ease, transform .15s ease;
    }

    .submit-button:hover {
      background: #587d1e;
      transform: translateY(-1px);
    }

    .footer {
      padding-top: 20px;
      color: #82909c;
      text-align: center;
      font-size: 12px;
    }

    @media (max-width: 600px) {
      .topbar { height: 64px; }
      .topbar-inner { width: calc(100% - 28px); }
      .brand img { width: 172px; max-height: 43px; }
      .portal-label { font-size: 10px; }
      .page { width: calc(100% - 20px); margin: 18px auto 30px; }
      .hero { padding: 25px 22px; border-radius: 14px 14px 0 0; }
      .hero-description { font-size: 14px; }
      .form-shell { padding: 20px 14px 24px; border-radius: 0 0 14px 14px; }
      .question-card { padding: 16px 13px; }
      .question-title { gap: 9px; font-size: 15px; }
      .rating-options { gap: 5px; }
      .rating-option span { min-height: 52px; border-radius: 8px; }
      .rating-option small { font-size: 9px; }
      .submit-row { align-items: stretch; flex-direction: column; gap: 12px; }
      .submit-button { width: 100%; }
    }
  </style>
</head>
<body>
  <header class="topbar">
    <div class="topbar-inner">
      <a class="brand" href="<?= base_url(); ?>" aria-label="MHIS E-Report">
        <img src="<?= base_url('aset/img/logo_MH.png'); ?>" alt="Mutiara Harapan Islamic School">
      </a>
      <span class="portal-label">Stakeholder Survey</span>
    </div>
  </header>

  <main class="page">
    <section class="hero">
      <p class="eyebrow">Your feedback matters</p>
      <h1><?= $escape($survey['title']); ?></h1>
      <?php if (!empty($survey['description'])): ?>
        <p class="hero-description"><?= nl2br($escape($survey['description'])); ?></p>
      <?php else: ?>
        <p class="hero-description">Your feedback helps us continue improving our learning experience and school community.</p>
      <?php endif; ?>
      <span class="period">
        <?= $escape($report_type); ?> Report
        <span aria-hidden="true">&bull;</span>
        Semester <?= $escape($semester); ?>, <?= $escape($academic_year); ?>
      </span>
    </section>

    <section class="form-shell">
      <div class="respondent">
        <div class="avatar" aria-hidden="true"><?= $escape(strtoupper(substr($student['nama'], 0, 1))); ?></div>
        <div>
          <strong><?= $escape($student['nama']); ?></strong>
          <small>Student feedback form</small>
        </div>
      </div>

      <div class="progress-card" aria-live="polite">
        <div class="progress-copy">
          <strong>Your progress</strong>
          <span><span id="answered-count">0</span> of <?= count($questions); ?> answered</span>
        </div>
        <div class="progress-track"><div class="progress-value" id="progress-value"></div></div>
      </div>

      <?php if (!empty($survey_error)): ?>
        <div class="alert" role="alert"><?= $escape($survey_error); ?></div>
      <?php endif; ?>

      <form id="survey-form" method="post" action="<?= base_url('stakeholder_survey/submit/' . (int) $survey['id']); ?>">
        <input type="hidden" name="report_type" value="<?= $escape($report_type); ?>">

        <?php foreach ($questions as $index => $question): ?>
          <fieldset class="question-card" data-question>
            <legend class="question-title">
              <span class="question-number"><?= $index + 1; ?></span>
              <span>
                <?= $escape($question['question']); ?>
                <?php if ($question['is_required'] === 'Y'): ?>
                  <span class="required" aria-label="required">*</span>
                <?php else: ?>
                  <span class="optional">Optional</span>
                <?php endif; ?>
              </span>
            </legend>

            <?php if ($question['question_type'] === 'RATING'): ?>
              <div class="rating-options">
                <?php
                  $rating_labels = array(1 => 'Very low', 2 => 'Low', 3 => 'Neutral', 4 => 'Good', 5 => 'Great');
                  for ($rating = 1; $rating <= 5; $rating++):
                    $checked = isset($old_answers[$question['id']]) && (int) $old_answers[$question['id']] === $rating;
                    $input_id = 'q' . (int) $question['id'] . '-rating-' . $rating;
                ?>
                  <label class="rating-option" for="<?= $input_id; ?>">
                    <input id="<?= $input_id; ?>" type="radio" name="answers[<?= (int) $question['id']; ?>]" value="<?= $rating; ?>" <?= $checked ? 'checked' : ''; ?> <?= $question['is_required'] === 'Y' ? 'required' : ''; ?>>
                    <span><?= $rating; ?><small><?= $rating_labels[$rating]; ?></small></span>
                  </label>
                <?php endfor; ?>
              </div>
              <div class="rating-scale"><span>Very dissatisfied</span><span>Very satisfied</span></div>

            <?php elseif ($question['question_type'] === 'RADIO'): ?>
              <div class="choice-list">
                <?php foreach ($question['options'] as $option): ?>
                  <?php
                    $input_id = 'q' . (int) $question['id'] . '-option-' . (int) $option['id'];
                    $checked = isset($old_answers[$question['id']]) && (int) $old_answers[$question['id']] === (int) $option['id'];
                  ?>
                  <label class="choice-option" for="<?= $input_id; ?>">
                    <input id="<?= $input_id; ?>" type="radio" name="answers[<?= (int) $question['id']; ?>]" value="<?= (int) $option['id']; ?>" <?= $checked ? 'checked' : ''; ?> <?= $question['is_required'] === 'Y' ? 'required' : ''; ?>>
                    <span><i class="choice-mark" aria-hidden="true"></i><?= $escape($option['option_label']); ?></span>
                  </label>
                <?php endforeach; ?>
              </div>

            <?php else: ?>
              <?php $answer = isset($old_answers[$question['id']]) ? $old_answers[$question['id']] : ''; ?>
              <textarea
                name="answers[<?= (int) $question['id']; ?>]"
                rows="4"
                placeholder="Share your thoughts here..."
                <?= !empty($question['max_length']) ? 'maxlength="' . (int) $question['max_length'] . '"' : ''; ?>
                <?= $question['is_required'] === 'Y' ? 'required' : ''; ?>
              ><?= $escape($answer); ?></textarea>
              <?php if (!empty($question['max_length'])): ?>
                <div class="character-count"><span data-character-count>0</span> / <?= (int) $question['max_length']; ?> characters</div>
              <?php endif; ?>
            <?php endif; ?>
          </fieldset>
        <?php endforeach; ?>

        <div class="submit-row">
          <p class="privacy-note">Your answers are submitted securely and will help the school improve its programs and services.</p>
          <button class="submit-button" type="submit">Submit feedback <span aria-hidden="true">&rarr;</span></button>
        </div>
      </form>
    </section>

    <footer class="footer">Mutiara Harapan Islamic School &nbsp;&bull;&nbsp; MHIS E-Report</footer>
  </main>

  <script>
    (function () {
      var questions = document.querySelectorAll('[data-question]');
      var answeredCount = document.getElementById('answered-count');
      var progressValue = document.getElementById('progress-value');

      function updateProgress() {
        var answered = 0;
        questions.forEach(function (question) {
          var selected = question.querySelector('input[type="radio"]:checked');
          var text = question.querySelector('textarea');
          if (selected || (text && text.value.trim() !== '')) answered++;
        });

        answeredCount.textContent = answered;
        progressValue.style.width = (questions.length ? (answered / questions.length) * 100 : 0) + '%';
      }

      document.querySelectorAll('.question-card input, .question-card textarea').forEach(function (input) {
        input.addEventListener('change', updateProgress);
        input.addEventListener('input', function () {
          var card = input.closest('.question-card');
          var counter = card ? card.querySelector('[data-character-count]') : null;
          if (counter) counter.textContent = input.value.length;
          updateProgress();
        });

        if (input.tagName === 'TEXTAREA') {
          var counter = input.parentElement.querySelector('[data-character-count]');
          if (counter) counter.textContent = input.value.length;
        }
      });

      updateProgress();
    }());
  </script>
</body>
</html>
