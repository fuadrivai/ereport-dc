<div class="card schedule-card" id="booking-management" data-slots='<?= html_escape(json_encode($slots), true) ?>'
    data-update-url="<?= html_escape(base_url('schedule/update-booking')) ?>"
    data-delete-url="<?= html_escape(base_url('schedule/delete-booking')) ?>">
    <div class="header">
        <h4 class="title">Manage Report Distribution Bookings</h4>
        <p class="category">All booking records</p>
    </div>
    <div class="content">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="booking-management-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Student</th>
                        <th>Report</th>
                        <th>Type</th>
                        <th>Date and Time</th>
                        <th>Collection</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $number = 1; foreach ($bookings as $booking) { ?>
                    <tr>
                        <td><?= $number++ ?></td>
                        <td><?= html_escape($booking['student_name']) ?></td>
                        <td><?= html_escape($booking['report_title']) ?></td>
                        <td><?= html_escape($booking['booking_type']) ?></td>
                        <td><?= html_escape(date('d M Y', strtotime($booking['distribution_date']))) ?><br>
                            <?= html_escape(date('H:i', strtotime($booking['start_time']))) ?> -
                            <?= html_escape(date('H:i', strtotime($booking['end_time']))) ?></td>
                        <td><?= html_escape($booking['report_collection_method']) ?></td>
                        <td><span
                                class="label label-<?= $booking['status'] === 'BOOKED' ? 'info' : ($booking['status'] === 'COMPLETED' ? 'success' : 'default') ?>">
                                <?= html_escape($booking['status']) ?></span></td>
                        <td>
                            <?php if ($booking['status'] === 'BOOKED') { ?>
                            <button type="button" class="btn btn-xs btn-info edit-booking"
                                data-booking='<?= html_escape(json_encode($booking), true) ?>'>
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-xs btn-success complete-booking"
                                data-id="<?= (int) $booking['id'] ?>">
                                <i class="fa fa-check"></i> Confirm
                            </button>
                            <button type="button" class="btn btn-xs btn-danger cancel-booking"
                                data-id="<?= (int) $booking['id'] ?>">
                                <i class="fa fa-remove"></i> Cancel
                            </button>
                            <?php } ?>
                            <button type="button" class="btn btn-xs btn-danger delete-booking"
                                data-id="<?= (int) $booking['id'] ?>">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="booking-edit-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form class="modal-content" id="booking-edit-form">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change Booking Schedule</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="booking_id" id="booking-id">
                <input type="hidden" name="action" value="reschedule">
                <div class="form-group">
                    <label>Student</label>
                    <p class="form-control-static" id="booking-student"></p>
                </div>
                <div class="form-group">
                    <label for="booking-session">New Date and Time</label>
                    <select class="form-control" name="session_id" id="booking-session" required></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Schedule</button>
            </div>
        </form>
    </div>
</div>

<script>
    var bookingManagement = $('#booking-management');
    var bookingSlots = JSON.parse(bookingManagement.attr('data-slots') || '{}');
    var bookingUpdateUrl = bookingManagement.attr('data-update-url');
    var bookingDeleteUrl = bookingManagement.attr('data-delete-url');

    function submitBookingAction(bookingId, action) {
        $.post(bookingUpdateUrl, {
            booking_id: bookingId,
            action: action
        }, function (response) {
            if (response.status === 'ok') {
                window.location.reload();
            } else {
                noti('danger', response.data);
            }
        });
    }

    $(function () {
        $('#booking-management-table').DataTable();

        $('.edit-booking').on('click', function () {
            var booking = $(this).data('booking'),
                slots = bookingSlots[booking.report_distribution_id] || [],
                options = '';
            $('#booking-id').val(booking.id);
            $('#booking-student').text(booking.student_name);
            $.each(slots, function (_, slot) {
                var selected = Number(slot.id) === Number(booking.session_id) ? ' selected' :
                    '';
                options += '<option value="' + slot.id + '"' + selected + '>' + slot
                    .distribution_date +
                    ' | Session ' + slot.session_number + ' | ' + slot.start_time.substring(0,
                        5) +
                    ' - ' + slot.end_time.substring(0, 5) + '</option>';
            });
            $('#booking-session').html(options);
            $('#booking-edit-modal').modal('show');
        });

        $('#booking-edit-form').on('submit', function (event) {
            event.preventDefault();
            $.post(bookingUpdateUrl, $(this).serialize(), function (response) {
                if (response.status === 'ok') {
                    window.location.reload();
                } else {
                    noti('danger', response.data);
                }
            });
        });

        $('.cancel-booking').on('click', function () {
            if (window.confirm('Cancel this booking?')) {
                submitBookingAction($(this).data('id'), 'cancel');
            }
        });

        $('.complete-booking').on('click', function () {
            if (window.confirm('Confirm this student has attended?')) {
                submitBookingAction($(this).data('id'), 'complete');
            }
        });

        $('.delete-booking').on('click', function () {
            if (window.confirm('Delete this booking record permanently?')) {
                $.post(bookingDeleteUrl, {
                    booking_id: $(this).data('id')
                }, function (response) {
                    if (response.status === 'ok') {
                        window.location.reload();
                    } else {
                        noti('danger', response.data);
                    }
                });
            }
        });
    });
</script>