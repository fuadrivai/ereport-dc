<div class="card"><div class="header"><h4 class="title">Hasil: <?= htmlspecialchars($survey['title']); ?></h4></div><div class="content">
  <p><strong>Periode:</strong> <?= htmlspecialchars($tahun['tahun']); ?> &nbsp; <strong>Tipe:</strong> <?= htmlspecialchars($survey['report_type']); ?></p>
  <p><strong>Respons selesai:</strong> <?= count($completed); ?></p>
  <a class="btn btn-success" href="<?= base_url('stakeholder_survey/export/' . $survey['id']); ?>">Export Excel</a>
  <?php foreach ($results as $result): ?>
    <hr><h4><?= htmlspecialchars($result['question']['question']); ?> (<?= (int) $result['total']; ?> jawaban)</h4>
    <?php if ($result['question']['question_type'] === 'RATING'): ?>
      <p>Rata-rata: <?= htmlspecialchars($result['summary']['average']); ?></p>
      <?php foreach ($result['summary']['distribution'] as $value => $count): ?><span>Rating <?= $value; ?>: <?= (int) $count; ?></span><br><?php endforeach; ?>
    <?php elseif ($result['question']['question_type'] === 'RADIO'): ?>
      <?php foreach ($result['summary'] as $option): ?><span><?= htmlspecialchars($option['label']); ?>: <?= (int) $option['count']; ?></span><br><?php endforeach; ?>
    <?php else: ?>
      <ul><?php foreach ($result['summary'] as $answer): ?><li><?= nl2br(htmlspecialchars($answer)); ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
  <?php endforeach; ?>
</div></div>
