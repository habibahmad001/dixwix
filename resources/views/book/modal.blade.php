<style>
    #user_list_container {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ccc;
        margin-top: 25px;
    }
</style>
<div class="modal" id="dixwix_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <input type="hidden" id="group_id_modal" name="group_id">
            <input type="hidden" id="group_type_id_modal" name="group_type_id">
            <div class="modal-body" id="modal_body" style="overflow-y: scroll; max-height: 450px;">
                <button type="button" class="close book-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <form action="" id="book-status-form" enctype="multipart/form-data">
                    @csrf
                    <div class="col">
                        <label for="book_duration">Select Duration</label>
                        <select class="form-control" name="duration" id="book_duration">
                            <option value="1">1 Week</option>
                            <option value="2">2 Weeks</option>
                            <option value="3">3 Weeks</option>
                            <option value="4">1 Month</option>
                        </select>
                    </div>
                    <div class="col mt-3">
                        <button class="btn btn-secondary">Reserve</button>
                    </div>
                </form>
            </div>
            <div class="container mt-5">
                <div class="form-group">
                    <div class="input-group">
                        <input type="email" id="email_to_invite" class="form-control" placeholder="Enter Email ID to invite" />
                        <div class="input-group-append">
                            <button class="btn btn-primary" id="invite_button" onclick="invite_by_email(1,2)">Invite</button>
                        </div>
                    </div>
                </div>
                <div id="loading" class="mt-2 text-center" style="display: none;">Inviting...</div>
                <p id="response_message" class="text-center text-danger small mt-2"></p>
            </div>
            <button id="modal_print_btn" class="dark-btn btn link_with_img mx-5" style="display:none;" onclick="printBarcodes()">Print QR Codes</button>
            <div id="user_list_container" style="padding:20px">
            </div>
            <button id="close-modal" class="btn btn-danger">Close</button>
        </div>
    </div>
</div>
<script>
    function searchUsers() {
        let searchQuery = document.getElementById('search_user').value;
        let group_id = $('#group_id_modal').val();
        let group_type_id = $('#group_type_id_modal').val();

        $.ajax({
            url: "{{ url('search-users') }}",
            method: 'GET',
            data: {
                search_user: searchQuery,
                group_type_id: group_type_id,
                group_id: group_id
            },
            success: function(result) {
                let dataJson = JSON.parse(result);

                if (dataJson.success == true) {
                    jQuery("#user_list_container").html(dataJson.data);
                    jQuery("#modal_title").text("Add Members");
                }
            },
            error: function() {
                console.error("An error occurred while fetching users.");
            }
        });
    }

</script>

<script>
    function printBarcodes() {
        var content = document.getElementById('modal_body').innerHTML;

        var printWindow = window.open('', '', 'height=900,width=600');

        printWindow.document.write('<html><head><title>Print QR Codes</title>');

        printWindow.document.write(`
            <style>
                @media print {
                    body {
                        margin: 0;
                        padding: 0;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    img {
                        max-width: 100px;
                        max-height: 100px;
                        margin: 5px auto;
                        display: block;
                    }
                }
                @page {
                    size: A4;
                    margin: 1cm;
                }
            </style>
        `);

        printWindow.document.write('</head><body>');
        printWindow.document.write('<div>' + content + '</div>');
        printWindow.document.write('</body></html>');

        printWindow.document.close();
        printWindow.print();
    }
</script>
