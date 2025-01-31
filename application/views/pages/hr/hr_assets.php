<!-- Main Wrapper -->
<div class="main-wrapper">
	<!-- Header -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
	<?php $this->load->view('templates/nav_bar'); ?>
	<!-- /Header -->
	<!-- Sidebar -->
	<?php $this->load->view('templates/sidebar') ?>
	<!-- /Sidebar -->
	<!-- Two Col Sidebar -->

	<!-- /Two Col Sidebar -->
	<!-- Page Wrapper -->
	<div class="page-wrapper">

		<!-- Page Content -->
		<div class="content container-fluid" data-select2-id="select2-data-23-5b7q">

			<!-- Page Header -->
			<div class="page-header">
				<div class="row align-items-center">
					<div class="col">
						<h3 class="page-title">Assets</h3>
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
							<li class="breadcrumb-item active">Assets</li>
						</ul>
					</div>
					<div class="col-auto float-end ms-auto">
						<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_asset"><i class="fa-solid fa-plus"></i> Add Asset</a>

					</div>
				</div>
			</div>

			<!-- /Page Header -->
			<!-- Add Asset Modal -->
			<div id="add_asset" class="modal custom-modal fade" role="dialog" data-bs-backdrop="static">
				<div class="modal-dialog modal-dialog-centered modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Add Asset</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<form method="post" id="addasset" enctype="multipart/form-data">
								<div class="row">
									<div class="col-sm-6">
										<div class="input-block mb-3">
											<label class="col-form-label">Asset Name <span class="text-danger">*</span></label>
											<input class="form-control" type="text" name="assetname">
										</div>
									</div>
									<div class="col-sm-6">
										<div class="input-block mb-3">
											<label class="col-form-label">Purchase Date</label>


											<div class="cal-icon"><input name="purchdate" class="form-control" type="date"></div>

										</div>
									</div>
									<div class="col-sm-6">
										<div class="input-block mb-3">
											<label class="col-form-label">Model <span class="text-danger">*</span></label>
											<input class="form-control" type="text" name="model">

										</div>
									</div>
									<div class="col-sm-6">
										<div class="input-block mb-3">
											<label class="col-form-label">Serial Number </label>
											<input class="form-control" type="text" name="serial">

										</div>
									</div>
									<div class="col-sm-6">
										<div class="input-block mb-3">
											<label class="col-form-label">Condition</label>
											<select class="form-select" name="condition">
												<option value="">Select Status</option>
												<option value="Available">Available</option>
												<option value="In Use">In Use</option>
												<option value="Under Maintenance">Under Maintenance</option>
												<option value="Out of Service">Out of Service</option>
											</select>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="input-block mb-3">
											<label class="col-form-label">Warranty</label>
											<input class="form-control" type="text" name="warranty">

										</div>
									</div>
									<div class="col-sm-6">
										<div class="input-block mb-3">
											<label class="col-form-label">Value <span class="text-danger">*</span></label>
											<input class="form-control" type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');">


										</div>
									</div>
									<div class="col-md-6">
										<div class="input-block mb-3">
											<label class="col-form-label">User <span class="text-danger">*</span></label>
											<select class="form-select form-control" name="">

												<?php
												// Get select-options
												$this->db->order_by('id', 'ASC');
												$query =  $this->db->get('employee');

												// Start by displaying the default option
												echo '<option value="">Select Employee</option>';

												// Check if there are any results
												if ($query->num_rows() > 0) {
													// Load the options if there are employees available
													$data['query'] = $query;
													$this->load->view('components/select-options-user', $data);
												}
												?>

											</select>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="input-block mb-3">
											<label class="col-form-label">Status </label>
											<select class="form-select" name="">
												<option value="">Select Status</option>
												<option value="Pending">Pending</option>
												<option value="Approved">Approved</option>
												<option value="Returned">Returned</option>

											</select>
										</div>
									</div>


								</div>

								<div class="submit-section">
									<button type="submit" class="btn btn-primary submit-btn" id="assetAdd">Add Asset</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>


			<!-- Search Filter -->
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">

						<table id="" class="datatable table-striped custom-table mb-0 datatable">
							<thead>
								<tr>
									<th>Asset Name</th>

									<th>Purchase Date</th>

									<th>Model</th>
									<th>Serial Number</th>
									<th class="text-center">Condition</th>
									<th class="text-end">Warranty</th>
									<th>Value</th>
									<th>User</th>
									
									<th class="text-end">Status</th><th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($this->session->userdata('pfp')) {
									$pfp = $this->session->userdata('pfp');
								}

								$row_arr = array();

								$emp_id = $this->session->userdata('id');
								$this->db->where('emp_id', $emp_id);
								$query = $this->db->get('f_leaves');
								foreach ($query->result() as $row) {
									$leave_id =  $row->id;
									$name = ucwords(strtolower($row->name));
									$date_filled = $row->date_filled;
									$leave_type = $row->type_of_leave;
									$department = $row->department;
									$status = $row->status;
									$request_type = "LEAVE REQUEST";
									$row_arr[] = array(
										'id' => $leave_id,
										'name' => $name,
										'leave_type' => $leave_type,
										'date_filled' => $date_filled,
										'department' => $department,
										'status' => $status,
										'request_type' => $request_type
									);
								}
								$emp_id = $this->session->userdata('id');

								$this->db->where('emp_id', $emp_id);
								$query2 = $this->db->get('f_undertime');
								foreach ($query2->result() as $row2) {
									$undertime_leave_id =  $row2->id;
									$undertime_name = ucwords(strtolower($row2->name));
									$undertime_date_filled = $row2->date_filled;
									$undertime_department = $row2->department;
									$undertime_status = $row2->status;
									$request_type = "UNDERTIME REQUEST";
									$row_arr[] = array(
										'id' => $undertime_leave_id,

										'name' => $undertime_name,
										'date_filled' => $undertime_date_filled,
										'department' => $undertime_department,
										'status' => $undertime_status,
										'request_type' => $request_type
									);
								}
								$emp_id = $this->session->userdata('id');

								$this->db->where('emp_id', $emp_id);
								$query3 = $this->db->get('f_outgoing');
								foreach ($query3->result() as $row3) {
									$outgoing_id =  $row3->id;
									$outgoing_name = ucwords(strtolower($row3->name));
									$outgoing_date_filled = $row3->date_filled;
									$outgoing_department = $row3->department;
									$outgoing_status = $row3->status;
									$request_type = "OUTGOING PASS REQUEST";
									$row_arr[] = array(
										'id' => $outgoing_id,

										'name' => $outgoing_name,
										'date_filled' => $outgoing_date_filled,
										'department' => $outgoing_department,
										'status' => $outgoing_status,
										'request_type' => $request_type
									);
								}
								$emp_id = $this->session->userdata('id');

								$this->db->where('emp_id', $emp_id);
								$query4 = $this->db->get('f_off_bussiness');
								foreach ($query4->result() as $row4) {
									$ob_id =  $row4->id;
									$ob_name = ucwords(strtolower($row4->name));
									$ob_date_filled = $row4->outgoing_pass_date;
									$ob_department = $row4->department;
									$ob_status = $row4->status;
									$request_type = "OFFICIAL BUSINESS REQUEST";
									$row_arr[] = array(
										'id' => $ob_id,

										'name' => $ob_name,
										'date_filled' => $ob_date_filled,
										'department' => $ob_department,
										'status' => $ob_status,
										'request_type' => $request_type
									);
								}
								$emp_id = $this->session->userdata('id');

								$this->db->where('emp_id', $emp_id);
								$query5 = $this->db->get('f_overtime');
								foreach ($query5->result() as $row5) {
									$ot_id =  $row5->id;
									$ot_name = ucwords(strtolower($row5->name));
									$date_ot = $row5->date_ot;
									$ot_department = $row5->department;
									$ot_status = $row5->status;
									$request_type = "OVERTIME REQUEST";
									$row_arr[] = array(
										'id' => $ot_id,

										'name' => $ob_name,
										'date_filled' => $date_ot,
										'department' => $ob_department,
										'status' => $ob_status,
										'request_type' => $request_type
									);
								}
								// $emp_id = $this->session->userdata('id');

								// $this->db->where('emp_id', $emp_id);
								// $query6 = $this->db->get('work_schedule_adjustment_table');
								// foreach ($query6->result() as $row6) {
								//     $ws_id =  $row6->id;
								//     $ws_name = ucwords(strtolower($row6->name));
								//     $ws_date = $row6->date_filled;
								//     $ws_department = $row6->department;
								//     $ws_status = $row6->status;
								//     $request_type = "WORK SCHEDULE ADJUSTMENT REQUEST";
								//     $row_arr[] = array(
								//         'id' => $ws_id,

								//         'name' => $ws_name,
								//         'date_filled' => $ws_date,
								//         'department' => $ws_department,
								//         'status' => $ws_status,
								//         'request_type' => $request_type
								//     );
								// }



								foreach ($row_arr as $row) {
								?>

									<tr>
										<td>
											<h2 class="table-avatar">

												<a href="profile.html" class="avatar">
													<img src="<?php
																// Retrieve base64-encoded image data from session
																if ($this->session->userdata('pfp')) {
																	$pfp = $this->session->userdata('pfp');
																}

																// Check if $pfp is set and not empty
																if (!empty($pfp)) {
																	// Embed the base64-encoded image data in the 'src' attribute of the <img> tag
																	echo "data:image/jpeg;base64," . base64_encode($pfp);
																} else {
																	// If $pfp is empty or not set, display a placeholder image or handle it as per your requirement
																	echo "path/to/placeholder_image.jpg";
																}
																?>" alt="User Image">
												</a>
												<a href="<?= base_url('hr_profile') ?>" style="font-size: 0.6em;"><?php echo $row['name']; ?></a>


											</h2>
										</td>
										<td><?php echo $row['request_type']; ?></td>
										<td><?php echo $row['date_filled']; ?></td>

										<td class="text-center">
											<div class="dropdown action-label">
												<a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
													<i class="fa-regular fa-circle-dot text-purple"></i> <?php echo $row['status']; ?>
												</a>
												<div class="dropdown-menu dropdown-menu-right">
													<a class="dropdown-item" href="#"><i class="fa-regular fa-circle-dot text-purple"></i> New</a>
													<a class="dropdown-item" href="#"><i class="fa-regular fa-circle-dot text-info"></i> Pending</a>
													<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#approve_leave"><i class="fa-regular fa-circle-dot text-success"></i> Approved</a>
													<a class="dropdown-item" href="#"><i class="fa-regular fa-circle-dot text-danger"></i> Declined</a>
												</div>
											</div>
										</td>
										<td class="text-end">
											<div class="dropdown dropdown-action">
												<a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
												<div class="dropdown-menu dropdown-menu-right">
													<a class="dropdown-item update-req" href="#" data-bs-toggle="modal" id="" data-bs-target="#edit_leave" data-target-id="<?php echo $row['id']; ?>"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
													<a class="dropdown-item delete-req" href="#" data-bs-toggle="modal" data-bs-target="#delete_approve" data-target-id="<?php echo $row['id']; ?>"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
												</div>
											</div>
										</td>
									</tr>

								<?php
								}
								?>


							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>




	</div>
</div>
<!-- /Page Content -->






</div>
<!-- /Page Wrapper -->
</div>
<!-- /Main Wrapper -->


<script>
	document.addEventListener('DOMContentLoaded', function() {
		$("li > a[href='<?= base_url('forms/history') ?>']").parent().parent().css("display", "block") //get sidebar item with link
		$("li > a[href='<?= base_url('forms/history') ?>']").addClass("active"); // for items inside the sidebar




	})
</script>