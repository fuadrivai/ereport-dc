<?php if(empty($records)): ?>
    <div class="alert alert-info">No previous records found for this student.</div>
<?php else: ?>
    <ul class="list-group">
    <?php foreach($records as $rec): ?>
        <li class="list-group-item">
            <div class="d-flex w-100 justify-content-between" style="border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px;">
                <h5 class="mb-1" style="margin-top: 0; color: #1F77D0;">
                    <i class="pe-7s-date"></i> 
                    <?php echo date('d M Y', strtotime($rec['tanggal'])); ?> 
                    <small style="color: #999;">at <?php echo date('H:i', strtotime($rec['waktu'])); ?></small>
                </h5>
                <small><strong>Reporter:</strong> <?php echo htmlspecialchars($rec['reporter_name']); ?></small>
            </div>
            <p class="mb-1" style="white-space: pre-line;"><?php echo htmlspecialchars($rec['catatan']); ?></p>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>
