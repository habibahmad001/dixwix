<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    })

    function showCopies(bookId) {
        $.ajax({
            url: '/show-copies/' + bookId,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                let tbody = $('#modal_table_body_copies');
                tbody.empty();

                let i = 1;
                data.forEach(function (row) {

                    let isReservedBadge;
                    switch (row.is_reserved) {
                        case 0:
                            isReservedBadge = '<span class="badge badge-primary">Available</span>';
                            break;
                        case 1:
                            isReservedBadge = '<span class="badge badge-secondary">Reserved</span>';
                            break;
                        case 2:
                            isReservedBadge = '<span class="badge badge-warning">Approval Pending</span>';
                            break;
                        case 3:
                            isReservedBadge = '<span class="badge badge-success">Sold</span>';
                            break;
                        default:
                            isReservedBadge = '<span class="badge badge-secondary">Sold</span>';
                    }
                    let tr = $('<tr></tr>');
                    tr.append('<td> <span class="badge badge-success">' + row.id + '</span></td>');
                    tr.append('<td>' + (row.name || "N/A") + '</td>');
                    tr.append('<td style="text-align: center">' + isReservedBadge + '</td>');
                    tr.append('<td style="text-align: center">' + (row.reserved_by && row.is_reserved && row.reserved_by.name ? row.reserved_by.name : "N/A") + '</td>');
                    tr.append('<td style="text-align: center">' + (row.average_rating && row.average_rating ? row.average_rating : 'N/A') + '</td>');
                    tr.append('<td style="text-align: center">' + (row.due_date && row.is_reserved ? row.due_date : 'N/A') + '</td>');
                        if (row.is_reserved == 2) {
                        let actionButtons = `
                            <li class="list-inline-item">
                                <a class="btn btn-sm btn-success" href="javascript:void(0)" id="approve-reserve"
                                onclick="approveDisapprove('approve', ${row.id}, ${bookId}, this)">Approve</a>
                            </li>
                            <li class="list-inline-item">
                                <a class="btn btn-sm btn-danger" href="javascript:void(0)" id="disapprove-reserve"
                                onclick="approveDisapprove('disapprove', ${row.id}, ${bookId}, this)">Reject</a>
                            </li>`;

                            tr.append('<td style="text-align: center" class="d-flex justify-content-center">' + actionButtons + '</td>');
                        } else {
                            tr.append('<td style="text-align: center">No actions available</td>');
                        }
                    tbody.append(tr);
                    i++;
                });
                $('#dixwix_book_copies_modal').modal('show');
            },
            error: function (xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }

    function setBookStatus(book_id, group_id, status) {
        jQuery('#dixwix_book_modal').modal('show');

        $('#book-status-form').on('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to proceed with this action?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#reserve-book-btn").attr('disabled', true).text('Processing...');
                    let formData = $(this).serialize();
                    formData += "&book_id=" + book_id + "&group_id=" + group_id + "&status=" + status;

                    $.ajax({
                        type: 'POST',
                        url: '/set-book-status',
                        data: formData,
                        success: function (result) {
                            $("#reserve-book-btn").attr('disabled', false).text('Reserve');
                            const resultJson = JSON.parse(result);
                            if (resultJson.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: resultJson.message,
                                    icon: 'success',
                                    showConfirmButton: true
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: resultJson.message,
                                    icon: 'error',
                                    showConfirmButton: true
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            $("#reserve-book-btn").attr('disabled', false).text('Reserve');
                            console.error(error);
                        }
                    });
                }
            });
        });
    }

    function returnBook(entry_id, book_id) {
        Swal.fire({
            title: 'Are you sure?',
            html: `
                <p>Do you really want to return this book?</p>
                <input type="file" id="image_at_returning" accept="image/*" class="form-control">
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, return it!',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const fileInput = document.getElementById('image_at_returning');
                if (!fileInput.files[0]) {
                    Swal.showValidationMessage('You must upload an image of the returned book.');
                    return false;
                }
                return fileInput.files[0];
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const returnImage = result.value;
                const formData = new FormData();
                formData.append('entry_id', entry_id);
                formData.append('book_id', book_id);
                formData.append('image_at_returning', returnImage);

                $("#return-book-request-btn").attr('disabled', true).text('Processing...');

                $.ajax({
                    type: 'POST',
                    url: '/return-book',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (result) {
                        $("#return-book-request-btn").attr('disabled', false).text('Return');
                        if (result.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: result.message,
                                icon: 'success',
                                showConfirmButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: result.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        $("#return-book-request-btn").attr('disabled', false).text('Return');
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while processing your request.',
                            icon: 'error'
                        });
                        console.log(error);
                    }
                });
            }
        });
    }

    function adminReturnBook(entry_id, book_id, event) {
        event.preventDefault();

        const buttonElement = event.target; // Get the clicked button element
        const originalText = buttonElement.textContent; // Save the original button text

        Swal.fire({
            title: 'Are you sure?',
            html: `
                <label for="rating">Rate the borrower (1 to 5):</label>
                <select id="rating" class="form-control mb-2">
                    <option value="1">1 - Poor</option>
                    <option value="2">2 - Fair</option>
                    <option value="3">3 - Good</option>
                    <option value="4">4 - Very Good</option>
                    <option value="5">5 - Excellent</option>
                </select>
                <label for="feedback">Provide feedback (optional):</label>
                <textarea id="feedback" class="form-control" style="height:100px !important;" rows="3" placeholder="Enter feedback..."></textarea>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, return it!',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const rating = document.getElementById('rating').value;
                const feedback = document.getElementById('feedback').value;

                if (!rating) {
                    Swal.showValidationMessage('Please provide a rating.');
                    return false;
                }

                return {
                    rating: rating,
                    feedback: feedback,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const { rating, feedback } = result.value;

                buttonElement.textContent = 'Processing...';
                buttonElement.disabled = true;

                $.ajax({
                    type: 'POST',
                    url: '/admin-return-book',
                    data: {
                        "entry_id": entry_id,
                        "book_id": book_id,
                        "rating": rating,
                        "feedback": feedback
                    },
                    success: function (result) {
                        const resultJson = JSON.parse(result);
                        if (resultJson.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: resultJson.message,
                            icon: 'success',
                            showConfirmButton: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: resultJson.message,
                            icon: 'error'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while processing your request.',
                        icon: 'error'
                    });
                },
                complete: function () {
                        buttonElement.textContent = originalText;
                        buttonElement.disabled = false;
                    }
                });
            }
        });
    }


    function approveDisapprove(status, entryID, bookID, element) {
        const action = status === 'approve' ? 'Approve' : 'Reject';
        const originalText = $(element).text();

        let htmlContent = '';
        if (status === 'approve') {
            htmlContent = `
                <p>Please upload the reservation image:</p>
                <input type="file" id="reservation_image" accept="image/*" class="form-control">
            `;
        }

        Swal.fire({
            title: `Are you sure you want to ${action} this reservation?`,
            html: htmlContent,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, ${action} it!`,
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                if (status === 'approve') {
                    const fileInput = document.getElementById('reservation_image');
                    if (!fileInput.files[0]) {
                        Swal.showValidationMessage('You need to upload an image.');
                        return false;
                    }
                    return fileInput.files[0];
                }
                return true;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('status', status);
                formData.append('entry_id', entryID);
                formData.append('book_id', bookID);

                if (status === 'approve' && result.value instanceof File) {
                    formData.append('image_at_reservation', result.value);
                }

                $(element).text('Processing...').addClass('disabled');

                $.ajax({
                    type: 'POST',
                    url: '/reserve-approval',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (result) {
                        const resultJson = JSON.parse(result);
                        if (resultJson.success) {
                            Swal.fire(
                                'Success!',
                                resultJson.message || `The reservation has been ${action}d.`,
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                resultJson.message || `Failed to ${action} the reservation.`,
                                'error'
                            );
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'An error occurred while processing your request.',
                            'error'
                        );
                    },
                    complete: function () {
                        $(element).text(originalText).removeClass('disabled');
                    }
                });
            }
        });
}

</script>
