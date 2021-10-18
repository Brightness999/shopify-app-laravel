@extends('layouts.app')
@section('content')

<div class="indexContent" data-page_name="ADMIN USERS">
	<div class="maincontent">
		<div class="wrapinsidecontent">
			<div class="pagesize">
				<span class="h5 my-0">Show</span>
				<select name="PageSize" id="page_size">
					<option value="10">10</option>
					<option value="20">20</option>
					<option value="50">50</option>
					<option value="100">100</option>
				</select>
			</div>
			<div class="users">
				<h3 class="font-weight-bold">Search </h3>
                <table class="searchtable tableorders" cellspacing="0">
					<thead>
						<tr>
							<th>USER NAME</th>
							<th>EMAIL</th>
							<th>STATUS</th>
						</tr>
					</thead>
                    <tbody>
                        <tr>
                            <td data-label="USER NAME">
                                <input type="text" id="user_name" class="merchant-search" list="names">
                                <datalist id="names">
                                    <div id="name_data"></div>
                                </datalist>
                            </td>
                            <td data-label="EMAIL">
                                <input type="text" id="user_email" class="merchant-search" list="emails">
                                <datalist id="emails">
                                    <div id="email_data"></div>
                                </datalist>
                            </td>
                            <td data-label="STATUS">
                                <select name="Active" id="user_active" class="merchant-search">
                                    <option value="" style="color: gray"></option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-center mb-5">
                    <span class="h5 btn-link user-reset" style="text-decoration: underline; cursor:pointer;">Reset</span>
                </p>
				<table class="greentable tableorders userdatatable" cellspacing="0">
					<thead>
						<tr>
							<th>USER NAME</th>
							<th>EMAIL</th>
							<th>ACTIVE</th>
							<th>ACTIONS</th>
						</tr>
					</thead>
					<tbody id="user_data"></tbody>
				</table>
			</div>
            <div id="pagination"></div>
		</div>
	</div>
</div>
<div class="back-to-top" style="display:none">
    <img src=" {{ asset('/img/back_to_top.png') }}" alt="Back to Top">
    <span style="text-align: center;" class="h5">Back<br>to Top</span>
</div>

<input type="text" id="total_count" value="{{$total_count}}" hidden>
<input type="text" id="user_id" hidden>

<script type="text/javascript">
    $(document).ready(function() {
        $('#total_count').text("{{$total_count}}");
		$('#confirm').click(function() {
            var user_id = $('#user_id').val();
            var status = $(`#switch-label${user_id}`).is(':checked') ? false : true;
            var parameters = {
                action: 'change-user-status',
                user_id: user_id,
                active: status ? 1 : 0,
            }
            $.getJSON(ajax_link, parameters, function(res) {
                $(`#switch-label${user_id}`).prop('checked', status);
            })
        });
	});
	$('#user_data').on('click', 'input.change-status', function(e) {
        let status = $(this).is(':checked')
        if ($(this).is(':checked')) {
			$(this).prop('checked', false);
		} else {
			$(this).prop('checked', true);
		}
        $('#user_id').val(e.target.dataset.userid);
        $('#confirm-modal-body').html(`<h5>Are you sure to ${$(this).is(':checked') ? 'disable' : 'enable'} this user?</h5>`);
    });

    $('#user_data').on('click', '.admin-user-view', function(e) {
        window.open(`/admin/merchants/show/${e.target.dataset.userid}`, '_blank');
    });
</script>

@endsection