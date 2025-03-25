<script>
    function getMembersToAddNew(url_val, group_type_id) {
        jQuery.ajax({
            type: 'GET',
            url: url_val,
            data: {
                "_token": "<?= csrf_token() ?>",
            },
            success: function(result) {
                resultJson = JSON.parse(result);
                if (resultJson.success == true) {
                    //jQuery("#modal_body_invite").html(resultJson.data);
                    jQuery("#modal_title_invite").text("Add Members");
                    jQuery('#dixwix_modal_invite').modal('show');
                }
            }
        });
    }

    function addMemberToGroup(member_id, group_id, group_type_id) {
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
                    getMembersToAddNew(resultJson.reload_url, group_type_id);
                }
            }
        });
    }

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
        let invite_email = jQuery("#email_to_invite").val();
        jQuery.ajax({
            type: 'POST',
            url: "<?= route('invite-user') ?>",
            data: {
                "_token": "<?= csrf_token() ?>",
                "group_id": group_id,
                "group_type_id": group_type_id,
                "email_id": invite_email
            },
            success: function(result) {
                resultJson = JSON.parse(result);
                jQuery("#response_message").html(resultJson.message);
            }
        });
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
                        getMembersToAddNew(resultJson.reload_url, group_type_id);
                    }, 2500);
                }else{
                    jQuery('#res_msg').html('<span>'+resultJson.message+'</span>');
                    jQuery('#res_msg').removeClass('success');
                    jQuery('#res_msg').addClass('error');
                    setTimeout(function() {
                        getMembersToAddNew(resultJson.reload_url, group_type_id);
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
                resultJson = JSON.parse(result);
                if(resultJson.status==true){
                    jQuery('#res_msg').html('<span>'+resultJson.message+'</span>');
                    jQuery('#res_msg').removeClass('error');
                    jQuery('#res_msg').addClass('success');
                }else{
                    jQuery('#res_msg').html('<span>'+resultJson.message+'</span>');
                    jQuery('#res_msg').removeClass('success');
                    jQuery('#res_msg').addClass('error');
                }
                setTimeout(function() {
                    jQuery('#res_msg').html('');
                    getMembersToAddNew(resultJson.reload_url, group_type_id);
                }, 2500);
            }
        });
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
                if(resultJson.status==true){
                    jQuery('#res_msg').html('<span>'+resultJson.message+'</span>');
                    jQuery('#res_msg').removeClass('error');
                    jQuery('#res_msg').addClass('success');
                }else{
                    jQuery('#res_msg').html('<span>'+resultJson.message+'</span>');
                    jQuery('#res_msg').removeClass('success');
                    jQuery('#res_msg').addClass('error');
                }
                setTimeout(function() {
                    jQuery('#res_msg').html('');
                    getMembersToAddNew(resultJson.reload_url, group_type_id);
                }, 2500);
            }
        });
    }

    function acceptJoiningRequest(id, member_id, group_id, is_accepted){

        jQuery.ajax({
            type: 'POST',
            url: '<?= route('accept-member-request') ?>',
            data: {
                "_token": "<?= csrf_token() ?>",
                "id": id,
                "member_id": member_id,
                "group_id": group_id,
                "is_accepted": is_accepted
            },
            success: function(result) {
                window.location.reload();
            }
        });
    }

    function acceptInvitation(group_id, member_id, created_by) {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to accept this group invitation?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, accept it!",
            cancelButtonText: "No, cancel!",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#accept-request-btn").attr("disabled",true).text("Processing...");
                jQuery.ajax({
                    type: 'GET',
                    url: '/confirm-group-add/' + group_id + '/' + member_id + '/' + created_by,
                    data: {
                        "_token": "<?= csrf_token() ?>",
                        "member_id": member_id,
                        "group_id": group_id
                    },
                    success: function(result) {
                    $("#accept-request-btn").attr("disabled",false).text("Accept");
                        Swal.fire(
                            "Success!",
                            "You have successfully accepted the invitation.",
                            "success"
                        ).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        $("#accept-request-btn").attr("disabled",false).text("Accept");
                        Swal.fire(
                            "Error!",
                            "Something went wrong. Please try again.",
                            "error"
                        );
                    }
                });
            }
        });
    }


    function rejectInvitation(group_id, member_id){
        jQuery.ajax({
            type: 'GET',
            url: '/reject-group-add/'+ group_id + '/'+member_id ,
            data: {
                "_token": "<?= csrf_token() ?>",
                "member_id": member_id,
                "group_id": group_id
            },
            success: function(result) {
                window.location.reload();
            }
        });
    }
</script>
