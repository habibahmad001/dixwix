<script>
   $('#close-modal').click(function(){
        jQuery('#dixwix_modal').modal('hide');
    });
    function getMembersToAdd(url_val, group_type_id) {
        jQuery.ajax({
            type: 'GET',
            url: url_val,
            data: {
                "_token": "<?= csrf_token() ?>",
            },
            success: function(result) {
                resultJson = JSON.parse(result);
                if (resultJson.success == true) {
                    //jQuery("#modal_body").html(resultJson.data);
                  	jQuery("#modal_body").html('');
                    //jQuery("#user_list_container").html(resultJson.data);
                    jQuery("#modal_title").text("Add Members");
                    jQuery('#dixwix_modal').modal('show');
                }
            }
        });
    }


  function addMemberToGroup(member_id, group_id, group_type_id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to add this member to the group?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, add member',
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state and disable escape/outside click
            Swal.fire({
                title: 'Adding member...',
                allowEscapeKey: false,  // Disable closing by pressing Escape
                allowOutsideClick: false,  // Disable closing by clicking outside
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make the AJAX request
            jQuery.ajax({
                type: 'POST',
                url: "<?= route('add-group-member') ?>",
                data: {
                    "_token": "<?= csrf_token() ?>",
                    "group_id": group_id,
                    "member_id": member_id,
                },
                success: function(result) {
                    console.log(result);
                    const resultJson = JSON.parse(result);

                    if (resultJson.success == true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Member request successfully sent!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                             window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to add member',
                            text: resultJson.message || 'Please try again.',
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Mail failed!',
                        text: xhr.responseJSON.message
                    });
                }
            });
        }
    });
}

    /*function addMemberToGroup(member_id, group_id, group_type_id) {
        jQuery.ajax({
            type: 'POST',
            url: "<?= route('add-group-member') ?>",
            data: {
                "_token": "<?= csrf_token() ?>",
                "group_id": group_id,
                "member_id": member_id,
            },
            success: function(result) {
                resultJson = JSON.parse(result);
                if (resultJson.success == true) {
                    getMembersToAdd(resultJson.reload_url, group_type_id);
                }
            }
        });
    }*/

    function deleteGroup(group_id, url_val) {
        jQuery.ajax({
            type: 'DELETE',
            url: url_val,
            data: {
                "_token": "<?= csrf_token() ?>",
                "group_id": group_id
            },
            success: function(result) {
                window.location.reload();
            }
        });
    }

    function invite_by_email(group_id, group_type_id) {
        var grp_id = $('#group_id_modal').val()
        var grp_type_id = $('#group_type_id_modal').val()
        let invite_email = jQuery("#email_to_invite").val();
       if(invite_email){
        $('#invite_button').prop('disabled', true);
        $('#loading').show();
        jQuery.ajax({
            type: 'POST',
            url: "<?= route('invite-user') ?>",
            data: {
                "_token": "<?= csrf_token() ?>",
                "group_id": grp_id,
                "group_type_id": grp_type_id,
                "email_id": invite_email
            },
            success: function(result) {
                resultJson = JSON.parse(result);

                jQuery("#response_message").html(resultJson.message);
            },
            complete: function() {
                // Re-enable button and hide loading after request completes
                $('#invite_button').prop('disabled', false);
                $('#loading').hide();
            },
        });
       }
    }

    function deleteUserFromGroup(member_id,group_id,group_type_id) {
        jQuery.ajax({
            type: 'DELETE',
            url: '<?= route('delete-member-from-group') ?>',
            data: {
                "_token": "<?= csrf_token() ?>",
                "member_id": member_id,
                'group_id': group_id,
                'group_type_id': group_type_id
            },
            success: function(result) {
                resultJson = JSON.parse(result);
                if(resultJson.status==true){
                    jQuery('#res_msg').html('<span>'+resultJson.message+'</span>');
                    jQuery('#res_msg').removeClass('error');
                    jQuery('#res_msg').addClass('success');
                    setTimeout(function() {
                        getMembersToAdd(resultJson.reload_url, group_type_id);
                    }, 2500);
                }else{
                     if(resultJson.success == false)
                    {
                        Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "You can not delete! "+resultJson.message,
                        // footer: '<a href="#">Why do I have this issue?</a>'
                        });
                    }

                    if(resultJson.success == true)
                    {
                        Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: resultJson.message,
                        showConfirmButton: false,
                        timer: 2000
                        });
                        setTimeout(function() {
                            window.location.reload();
                        }, 2500)

                    }
                    jQuery('#res_msg').html('<span>'+resultJson.message+'</span>');
                    jQuery('#res_msg').removeClass('success');
                    jQuery('#res_msg').addClass('error');
                    setTimeout(function() {
                        getMembersToAdd(resultJson.reload_url, group_type_id);
                    }, 2500);
                }

            }
        });
    }

    function acceptUserInGroup(member_id) {
        jQuery.ajax({
            type: 'PATCH',
            url: '<?= route('accept-member-in-group') ?>',
            data: {
                "_token": "<?= csrf_token() ?>",
                "member_id": member_id
            },
            success: function(result) {
                window.location.reload();
            }
        });
    }

    function updateMember(member_id, group_id, group_type_id) {
        const element = document.getElementById('member_' + member_id);
        const originalState = element.checked;
        let role = element.checked ? 'admin' : 'user';

        jQuery.ajax({
            type: 'POST',
            url: '<?= route('update-member-role') ?>',
            data: {
                "_token": "<?= csrf_token() ?>",
                "member_id": member_id,
                "group_id": group_id,
                "group_type_id": group_type_id,
                "role": role
            },
            success: function(result) {
                const resultJson = JSON.parse(result);

                if (resultJson.success === true) {
                    Swal.fire({
                        title: "Success!",
                        text: resultJson.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                    jQuery('#res_msg').html('<span>' + resultJson.message + '</span>');
                    jQuery('#res_msg').removeClass('error').addClass('success');
                } else {
                    element.checked = !originalState;

                    Swal.fire({
                        title: "Error!",
                        text: resultJson.message,
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                    jQuery('#res_msg').html('<span>' + resultJson.message + '</span>');
                    jQuery('#res_msg').removeClass('success').addClass('error');
                }

                setTimeout(function() {
                    jQuery('#res_msg').html('');
                    getMembersToAdd(resultJson.reload_url, group_type_id);
                }, 2500);
            },
            error: function(xhr, status, error) {

                element.checked = !originalState;
                Swal.fire({
                    title: "Error!",
                    text: "An unexpected error occurred. Please try again.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
                jQuery('#res_msg').html('<span>An error occurred. Please try again.</span>');
                jQuery('#res_msg').removeClass('success').addClass('error');

                setTimeout(function() {
                    jQuery('#res_msg').html('');
                }, 2500);
            }
        });
    }


  function updateGroupStatus(group_id) {

    const element = document.getElementById('group_' + group_id);
    let status = element.checked ? 1 : 0;

    if (!element.checked) {
        Swal.fire({
            title: "Are you sure?",
            text: "By disabling the group you will not be to do anything. Do you want to continue?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, disable it",
            cancelButtonText: "No, cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                sendUpdateRequest();
            } else {
                element.checked = true;
            }
        });
    } else {
        sendUpdateRequest();
    }

    function sendUpdateRequest() {
        jQuery.ajax({
            type: 'POST',
            url: "<?= route('update-group-status') ?>",
            data: {
                "_token": "<?= csrf_token() ?>",
                "group_id": group_id,
                "status": status
            },
            success: function(result) {
                try {
                    let resultJson = JSON.parse(result);

                    if (resultJson.success) {
                        Swal.fire({
                            title: "Success!",
                            text: resultJson.message,
                            icon: "success",
                            confirmButtonText: "OK",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });

                        displayMessage(resultJson.message, 'success');
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: resultJson.message,
                            icon: "error",
                            confirmButtonText: "OK",
                        });

                        displayMessage(resultJson.message, 'error');
                    }
                } catch (e) {
                    console.error("Invalid JSON response:", result);
                    Swal.fire({
                        title: "Error!",
                        text: "An unexpected error occurred.",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                Swal.fire({
                    title: "Error!",
                    text: "Unable to update group status. Please try again later.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            }
        });
    }

    // Function to display success or error messages
    function displayMessage(message, type) {
        const resMsg = jQuery('#res_msg');
        resMsg.html('<span>' + message + '</span>');
        resMsg.removeClass('error success').addClass(type);
        setTimeout(() => resMsg.html(''), 2500);
    }
}


    function updateMemberStatus(member_id, group_id, group_type_id) {
        const element = document.getElementById('member_status_' + member_id);
        let status = element.checked ? 1 : 0;

        jQuery.ajax({
            type: 'POST',
            url: '<?= route('update-member-status') ?>',
            data: {
                "_token": "<?= csrf_token() ?>",
                "member_id": member_id,
                "group_id": group_id,
                "group_type_id": group_type_id,
                "status": status
            },
            success: function(result) {
                resultJson = JSON.parse(result);
                if(resultJson.success==true){

                  Swal.fire({
                        title: "Success!",
                        text: resultJson.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });

                }else{
                    jQuery('#res_msg').html('<span>'+resultJson.message+'</span>');
                    jQuery('#res_msg').removeClass('success');
                    jQuery('#res_msg').addClass('error');
                }
                setTimeout(function() {
                    jQuery('#res_msg').html('');
                    getMembersToAdd(resultJson.reload_url, group_type_id);
                }, 2500);
            }
        });
    }

    function acceptJoiningRequest(id, member_id, group_id, is_accepted, notification_id = null) {

        Swal.fire({
            title: 'Are you sure?',
            text: is_accepted
                ? "Do you want to accept this member's request?"
                : "Do you want to reject this member's request?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: is_accepted ? 'Yes, Accept' : 'Yes, Reject',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {

                if(is_accepted)
                {
                    $(".accept-request-btn").text('Processing...');
                }
                else {
                    $(".decline-request-btn").text('Processing...');
                }

                jQuery.ajax({
                    type: 'POST',
                    url: '<?= route('accept-member-request') ?>',
                    data: {
                        "_token": "<?= csrf_token() ?>",
                        "id": id,
                        "member_id": member_id,
                        "group_id": group_id,
                        "is_accepted": is_accepted,
                        "notification_id": notification_id
                    },
                    success: function(result) {
                        if(is_accepted)
                        {
                            $(".accept-request-btn").text('Accept');
                        }
                        else {
                            $(".decline-request-btn").text('Declined');
                        }
                        Swal.fire(
                            is_accepted ? 'Accepted!' : 'Rejected!',
                            is_accepted
                                ? "The member's request has been accepted."
                                : "The member's request has been rejected.",
                            'success'
                        ).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function() {
                        if(is_accepted)
                        {
                            $(".accept-request-btn").text('Accept');
                        }
                        else {
                            $(".decline-request-btn").text('Decline');
                        }
                        Swal.fire(
                            'Error!',
                            'Something went wrong. Please try again later.',
                            'error'
                        );
                    }
                });
            }
        });
    }

</script>
