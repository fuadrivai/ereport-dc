<div class="card schedule-card" id="booking-management" data-slots='<?= html_escape(json_encode($slots), true) ?>'
    data-update-url="<?= html_escape(base_url('schedule/update-booking')) ?>"
    data-delete-url="<?= html_escape(base_url('schedule/delete-booking')) ?>">
    <div class="header">
        <h4 class="title"><?= html_escape($report_title) ?></h4>
        <p class="category">All booking records</p>
    </div>
    <div class="content">
        <div class="row booking-filters">
            <div class="col-sm-2 form-group"><label for="filter-type">Type</label>
                <select class="form-control input-sm booking-filter" id="filter-type">
                    <option value="">All</option>
                    <option value="THERAPY">Therapy</option>
                    <option value="NON_THERAPY">Non Therapy</option>
                </select></div>
            <div class="col-sm-2 form-group"><label for="filter-collection">Collection</label>
                <select class="form-control input-sm booking-filter" id="filter-collection">
                    <option value="">All</option>
                    <option value="ONLINE">Online</option>
                    <option value="ONSITE">Onsite</option>
                </select></div>
            <div class="col-sm-2 form-group"><label for="filter-status">Status</label>
                <select class="form-control input-sm booking-filter" id="filter-status">
                    <option value="">All</option>
                    <option value="BOOKED">Booked</option>
                    <option value="COMPLETED">Completed</option>
                    <option value="CANCELLED">Cancelled</option>
                </select></div>
            <div class="col-sm-3 form-group"><label for="filter-date">Date</label>
                <select class="form-control input-sm booking-filter" id="filter-date">
                    <option value="">All</option>
                    <?php foreach ($dates as $date) { ?>
                    <option value="<?= html_escape($date['distribution_date']) ?>">
                        <?= html_escape(date('d M Y', strtotime($date['distribution_date']))) ?><?= !empty($date['label']) ? ' - ' . html_escape($date['label']) : '' ?>
                    </option>
                    <?php } ?>
                </select></div>
            <div class="col-sm-2 form-group"><label for="filter-grade">Grade</label>
                <select class="form-control input-sm booking-filter" id="filter-grade">
                    <option value="">All</option>
                    <?php foreach ($grades as $grade) { ?>
                    <option value="<?= html_escape($grade['nama']) ?>"><?= html_escape($grade['nama']) ?></option>
                    <?php } ?>
                </select></div>
            <div class="col-sm-1 form-group"><label>&nbsp;</label><button type="button"
                    class="btn btn-default btn-sm form-control" id="clear-booking-filters" title="Clear filters"><i
                        class="fa fa-refresh"></i></button></div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="booking-management-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Student</th>
                        <th>Parent</th>
                        <th>Type / Collection</th>
                        <th>Date and Time</th>
                        <th>Google Meet</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $number = 1; foreach ($bookings as $booking) { ?>
                    <tr data-booking-type="<?= html_escape($booking['booking_type']) ?>"
                        data-collection="<?= html_escape($booking['report_collection_method']) ?>"
                        data-status="<?= html_escape($booking['status']) ?>"
                        data-date="<?= html_escape($booking['distribution_date']) ?>"
                        data-grade="<?= html_escape(!empty($booking['student_grade']) ? $booking['student_grade'] : '-') ?>">
                        <td><?= $number++ ?></td>
                        <td><?= html_escape($booking['student_name']) ?><br>
                            <span class="label label-default">Grade:
                                <?= html_escape(!empty($booking['student_grade']) ? $booking['student_grade'] : '-') ?></span>
                        </td>
                        <td><?= html_escape($booking['parent_name']) ?><br>
                            <small><?= html_escape($booking['parent_email']) ?></small><br>
                            <small><?= html_escape($booking['parent_phone']) ?></small></td>
                        <td><?= html_escape($booking['booking_type']) ?><br>
                            <span
                                class="label label-<?= $booking['report_collection_method'] === 'ONLINE' ? 'info' : 'success' ?>">
                                <?= html_escape($booking['report_collection_method']) ?></span></td>
                        <td><?= html_escape(date('d M Y', strtotime($booking['distribution_date']))) ?><br>
                            <?= html_escape(date('H:i', strtotime($booking['start_time']))) ?> -
                            <?= html_escape(date('H:i', strtotime($booking['end_time']))) ?></td>
                        <td><?php if (!empty($booking['gmeet_link'])) { ?><a
                                href="<?= html_escape($booking['gmeet_link']) ?>" target="_blank" rel="noopener"><i
                                    class="fa fa-video-camera"></i> Open</a><?php } else { ?><span
                                class="text-muted">-</span><?php } ?></td>
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

<style>
    .booking-filters {
        margin-bottom: 10px;
    }

    .booking-filters label {
        display: block;
        font-size: 12px;
        margin-bottom: 3px;
    }
</style>

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
        var table = $('#booking-management-table').DataTable();
        var filters = {
            type: $('#filter-type'),
            collection: $('#filter-collection'),
            status: $('#filter-status'),
            date: $('#filter-date'),
            grade: $('#filter-grade')
        };

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'booking-management-table') {
                return true;
            }
            var row = $(settings.aoData[dataIndex].nTr);
            return (!filters.type.val() || row.data('booking-type') === filters.type.val()) &&
                (!filters.collection.val() || row.data('collection') === filters.collection.val()) &&
                (!filters.status.val() || row.data('status') === filters.status.val()) &&
                (!filters.date.val() || row.data('date') === filters.date.val()) &&
                (!filters.grade.val() || row.data('grade') === filters.grade.val());
        });

        $('.booking-filter').on('change', function () {
            table.draw();
        });

        $('#clear-booking-filters').on('click', function () {
            $('.booking-filter').val('');
            table.draw();
        });

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