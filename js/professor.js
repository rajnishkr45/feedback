$(document).ready(function () {
    // Handle Edit Button Click
    $('.edit-btn').click(function () {
        // Find the parent row of the clicked edit button
        const row = $(this).closest('tr');

        // Extract current professor details from the row
        const id = row.find('td:first').text();
        const name = row.find('.prof-name').text();
        const email = row.find('.prof-email').text();
        const phone = row.find('.prof-phone').text();
        const dept = row.find('.prof-dept').text();
        const role = row.find('.prof-role').text();

        // Populate the edit form with the current details
        $('#edit-name').val(name);
        $('#edit-email').val(email);
        $('#edit-phone').val(phone);
        $('#edit-dept').val(dept);
        $('#edit-role').val(role);

        // Open the modal to edit professor details
        $('#edit-modal').fadeIn();

        // Submit the form with updated values
        $('#edit-form').submit(function (e) {
            e.preventDefault();

            const updatedName = $('#edit-name').val();
            const updatedEmail = $('#edit-email').val();
            const updatedPhone = $('#edit-phone').val();
            const updatedDept = $('#edit-dept').val();
            const updatedRole = $('#edit-role').val();

            // AJAX call to update the professor details in the database
            $.ajax({
                url: '../admin/update_prof.php', // Create this PHP script to handle the update
                method: 'POST',
                data: {
                    id: id,
                    name: updatedName,
                    email: updatedEmail,
                    phone: updatedPhone,
                    department: updatedDept,
                    role: updatedRole
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Professor details updated successfully!'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect the changes
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to update professor details.'
                    });
                }
            });
        });
    });

    // Handle Delete Button Click
    $(document).on('click', '.delete-btn', function () {
        var profId = $(this).data('prof-id'); // Get professor ID from the button

        // Confirm deletion with SweetAlert2
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform AJAX request to delete the professor
                $.ajax({
                    url: '../admin/delete_prof.php',
                    type: 'POST',
                    data: { prof_id: profId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Remove the row from the table
                            $('#professor-' + profId).remove();

                            // Display success message
                            Swal.fire('Deleted!', 'Professor has been deleted.', 'success');
                        } else {
                            // Display error message
                            Swal.fire('Error!', response.error || 'Failed to delete professor.', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error!', 'An error occurred while processing the request.', 'error');
                    }
                });
            }
        });
    });

    // Close the modal when clicking outside it
    $(document).click(function (event) {
        if (!$(event.target).closest('.modal-content').length && !$(event.target).is('.edit-btn')) {
            $('#edit-modal').fadeOut();
        }
    });
});