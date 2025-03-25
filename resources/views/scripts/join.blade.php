<script>
    function RequestJoinGroup(url_val,group_id,member_id){
        jQuery.ajax({
            type:'POST',
            url: url_val,
            data: {
                "_token":"<?=csrf_token()?>",
                "member_id": member_id,
                "group_id": group_id
            },
            success: function(result){
                resultJson = JSON.parse(result);
                if(resultJson.success == true){
                    window.location.reload();
                }
              
               if (resultJson.success == true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Your joining request has been sent to group admin!',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to join the group',
                        text: resultJson.message || 'Please try again.',
                    });
                }
              
            },
           error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Request failed!',
                    text: 'Something went wrong, please try again.'
                });
            }
        });
    }
</script>