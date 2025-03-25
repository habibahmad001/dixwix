<script>
    function deleteItem(item_id, url_val){
        jQuery.ajax({
            type:'DELETE',
            url: url_val,
            data: {
                "_token":"<?=csrf_token()?>",
                "item_id":item_id
            },
            success: function(result){
                window.location.reload();
            }
        });
    }

    function printQRCodes(url_val,book_id){
        jQuery.ajax({
            type:'GET',
            url: url_val,
            data: {
                "book_id":book_id,
                "_token":"<?=csrf_token()?>",
            },
            success: function(result){
                resultJson = JSON.parse(result);
                if(resultJson.success == true){
                    jQuery("#modal_body").html(resultJson.data);
                    $('#search_user').hide();
                    $('#email_to_invite').hide();
                    $('#invite_button').hide();
                    jQuery("#modal_title").text("QR Codes");
                    jQuery("#modal_print_btn").show();
                    jQuery('#dixwix_modal').modal('show');
                }
            }
        });
    }

</script>