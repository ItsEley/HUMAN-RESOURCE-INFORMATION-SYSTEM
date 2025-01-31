<style>
    input[readonly],
    textarea[readonly] {
        background-color: white !important;
        box-shadow: none;
        cursor: default;
    }
</style>

<!-- Main Wrapper -->
<div class="main-wrapper">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css">
    <?php
    if (isset($_POST['request_type']) && !empty($_POST['request_type']) && isset($_POST['id']) && !empty($_POST['id'])) {

        $request_type = $_POST['request_type'];
        $id = $_POST['id'];

        $data = array();
        if ($request_type == 'LEAVE REQUEST') {

            $sql = "SELECT * FROM f_leaves WHERE id = $id";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $data['emp_id'] = $row['emp_id'];
            }
        } elseif ($request_type == 'OUTGOING REQUEST') {
            $sql = "SELECT * FROM f_outgoing WHERE id = $id";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $data['emp_id'] = $row['emp_id'];
            }
        }
        echo json_encode($data);
    } else {

        echo json_encode(array('error' => 'Invalid request'));
    }

    ?>
    <?php $this->load->view('templates/nav_bar'); ?>
    <?php $this->load->view('templates/sidebar') ?>
    <div class="page-wrapper">
        <!-- Page Content -->
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Pending Requests</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pendings</li>
                        </ul>
                    </div>
                    <div class="col-auto float-end ms-auto">
                        <!-- <a href="#" class="bt
                        
                        
                        n add-btn" data-bs-toggle="modal" data-bs-target="#add_employee"><i class="fa-solid fa-plus"></i> Add Employee</a> -->
                        <div class="view-icons">

                        </div>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            <div class="row">
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                    <?php
                    // Retrieve department name from the session
                    $current_department_name = $this->session->userdata('department');

                    // Query to get the department ID based on the department name
                    $department_query = $this->db->query("
    SELECT id 
    FROM department 
    WHERE department = ?
", array($current_department_name));

                    // Check if the department exists
                    if ($department_query->num_rows() > 0) {
                        $department_row = $department_query->row();
                        $department_id = $department_row->id;

                        // Initialize total count
                        $total_count = 0;

                        // Loop through each table and count pending requests
                        $tables = array('f_leaves', 'f_outgoing', 'f_overtime', 'f_undertime', 'f_off_bussiness');
                        foreach ($tables as $table) {
                            $this->db->from($table);
                            $this->db->where('head_status', 'pending');

                            // Add department condition using the department ID
                            $this->db->where('department', $department_id);

                            $total_count += $this->db->count_all_results();
                        }

                        // Prepare data for the view
                        $data['icon_type'] = "1";

                        $data['icon'] = "fa fa-address-book";
                        $data['count'] = $total_count;
                        $data['label'] = "Request Pending";
                        $this->load->view('components/card-dash-widget', $data);
                    } else {
                        // Handle case where department does not exist
                        // You can set a default count or show an error message
                    }

                    ?>

                </div>
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">

                    <?php
                    $data['icon'] = "fa fa-address-book";

                    $this->db->select('COUNT(*) as count');
                    $this->db->from('f_leaves');
                    $this->db->where('status', 'Approved');
                    $this->db->where('CURDATE() BETWEEN date_from AND date_to');

                    // Execute the query

                    $query = $this->db->get();
                    $data['icon_type'] = "1";
                    $data['count'] = $query->row_array()['count'];
                    $data['label'] = "Active Leaves";

                    $this->load->view('components/card-dash-widget', $data)

                    ?>

                </div>
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">

                    <?php
                    $data['icon'] = "fa fa-address-book";

                    $this->db->from('f_overtime');
                    $this->db->where('status', 'approved');
                    $this->db->where('date_ot', date('Y-m-d'));
                    $data['icon_type'] = "1";
                    $data['count'] = $count = $this->db->count_all_results();;
                    $data['label'] = "Active Overtime";
                    $this->load->view('components/card-dash-widget', $data)
                    ?>
                </div>
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">

                    <?php
                    $data['icon'] = "fa fa-address-book";

                    $this->db->from('f_undertime');
                    $this->db->where('status', 'approved');
                    $this->db->where('date_of_undertime', date('Y-m-d'));
                    $data['icon_type'] = "1";
                    $data['count'] = $count = $this->db->count_all_results();;
                    $data['label'] = "Active Undertime";
                    $this->load->view('components/card-dash-widget', $data)
                    ?>
                </div>
            </div>
            <ul class="nav nav-tabs nav-tabs-solid">
                <li class="nav-item"><a class="nav-link active" href="#solid-tab1" data-bs-toggle="tab">

                        Pending Leaves
                        <?php
                        // Get the department name from the session
                        $current_department_name = $this->session->userdata('department');

                        // Query to retrieve the department ID from the department table based on the department name
                        $dept_query = $this->db->query("
            SELECT id 
            FROM department 
            WHERE department = ?
        ", array($current_department_name));

                        // Check if the query returned a row
                        if ($dept_query->num_rows() > 0) {
                            // Fetch the row
                            $row = $dept_query->row();
                            $department_id = $row->id;

                            // Count the number of pending requests in the f_outgoing table for the department ID
                            $this->db->from('f_leaves');
                            $this->db->where('head_status', 'pending');
                            $this->db->where('department', $department_id);
                            $count = $this->db->count_all_results();

                            // Check if there are any pending requests
                            if ($count > 0) {
                                echo '<span class="badge bg-primary rounded-pill ms-1" style="font-size: 1.0rem;">' . $count . '</span>';
                            }
                        }
                        ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#solid-tab2" data-bs-toggle="tab">
                        Pending Outgoing
                        <?php
                        // Get the department name from the session
                        $current_department_name = $this->session->userdata('department');

                        // Query to retrieve the department ID from the department table based on the department name
                        $dept_query = $this->db->query("
            SELECT id 
            FROM department 
            WHERE department = ?
        ", array($current_department_name));

                        if ($dept_query->num_rows() > 0) {

                            $row = $dept_query->row();
                            $department_id = $row->id;
                            $this->db->from('f_outgoing');
                            $this->db->where('head_status', 'pending');
                            $this->db->where('department', $department_id);
                            $count = $this->db->count_all_results();

                            // Check if there are any pending requests
                            if ($count > 0) {
                                echo '<span class="badge bg-primary rounded-pill ms-1" style="font-size: 1.0rem;">' . $count . '</span>';
                            }
                        }
                        ?>
                    </a>
                </li>

                <li class="nav-item"><a class="nav-link" href="#solid-tab3" data-bs-toggle="tab">Pending Overtime

                        <?php

                        $current_department_name = $this->session->userdata('department');
                        $dept_query = $this->db->query("
            SELECT id 
            FROM department 
            WHERE department = ?
        ", array($current_department_name));

                        // Check if the query returned a row
                        if ($dept_query->num_rows() > 0) {
                            // Fetch the row
                            $row = $dept_query->row();
                            $department_id = $row->id;

                            // Count the number of pending requests in the f_outgoing table for the department ID
                            $this->db->from('f_overtime');
                            $this->db->where('head_status', 'pending');
                            $this->db->where('department', $department_id);
                            $count = $this->db->count_all_results();

                            // Check if there are any pending requests
                            if ($count > 0) {
                                echo '<span class="badge bg-primary rounded-pill ms-1" style="font-size: 1.0rem;">' . $count . '</span>';
                            }
                        }
                        ?>
                    </a></li>
                <li class="nav-item"><a class="nav-link" href="#solid-tab4" data-bs-toggle="tab">Pending Undertime

                        <?php
                        // Get the department name from the session
                        $current_department_name = $this->session->userdata('department');

                        // Query to retrieve the department ID from the department table based on the department name
                        $dept_query = $this->db->query("
            SELECT id 
            FROM department 
            WHERE department = ?
        ", array($current_department_name));

                        // Check if the query returned a row
                        if ($dept_query->num_rows() > 0) {
                            // Fetch the row
                            $row = $dept_query->row();
                            $department_id = $row->id;

                            // Count the number of pending requests in the f_outgoing table for the department ID
                            $this->db->from('f_undertime');
                            $this->db->where('head_status', 'pending');
                            $this->db->where('department', $department_id);
                            $count = $this->db->count_all_results();

                            // Check if there are any pending requests
                            if ($count > 0) {
                                echo '<span class="badge bg-primary rounded-pill ms-1" style="font-size: 1.0rem;">' . $count . '</span>';
                            }
                        }
                        ?>


                    </a></li>
                <li class="nav-item"><a class="nav-link" href="#solid-tab5" data-bs-toggle="tab">Pending Official Business

                        <?php
                        // Get the department name from the session
                        $current_department_name = $this->session->userdata('department');

                        // Query to retrieve the department ID from the department table based on the department name
                        $dept_query = $this->db->query("
            SELECT id 
            FROM department 
            WHERE department = ?
        ", array($current_department_name));

                        // Check if the query returned a row
                        if ($dept_query->num_rows() > 0) {
                            // Fetch the row
                            $row = $dept_query->row();
                            $department_id = $row->id;

                            // Count the number of pending requests in the f_outgoing table for the department ID
                            $this->db->from('f_off_bussiness');
                            $this->db->where('head_status', 'pending');
                            $this->db->where('department', $department_id);
                            $count = $this->db->count_all_results();

                            // Check if there are any pending requests
                            if ($count > 0) {
                                echo '<span class="badge bg-primary rounded-pill ms-1" style="font-size: 1.0rem;">' . $count . '</span>';
                            }
                        }
                        ?>
                    </a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane show active" id="solid-tab1">
                    <div class="card mb-0">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title mb-0">Pending Leave Request</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="leavereq_dt" class="datatable table-striped custom-table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Date Filled</th>
                                            <th class="text-center">Leave Type</th>
                                            <th class="text-center">Date From</th>
                                            <th class="text-center">Date To</th>
                                            <th class="text-center">Reason</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $current_department_name = $this->session->userdata('department');
                                        $dept_query = $this->db->query("
                                         SELECT id 
                                         FROM department 
                                         WHERE department = ?
                                     ", array($current_department_name));
                                        if ($dept_query->num_rows() > 0) {
                                            $row = $dept_query->row();
                                            $current_department_id = $row->id;
                                        } else {
                                        }
                                        $query = $this->db->query("
                                         SELECT f.*, e.fname, e.lname 
                                         FROM f_leaves f 
                                         LEFT JOIN employee e ON f.emp_id = e.id 
                                         WHERE f.head_status = 'pending'
                                         AND f.department = ?
                                     ", array($current_department_id));

                                        foreach ($query->result() as $row) {
                                            $fname = $row->fname;
                                            $lname = $row->lname;
                                            $fullname = $fname . ' ' . $lname;
                                        ?>
                                            <tr class="hoverable-row" id="<?php echo $row->id ?>">
                                                <td style="max-width: 200px; overflow: hidden; 
                                        text-overflow: ellipsis; white-space: nowrap;" name="emp_name">
                                                    <?php echo $fullname; ?>
                                                    <?php echo $row->id ?>

                                                </td>
                                                <td name="date_filled"><?php echo formatDateOnly($row->date_filled); ?></td>
                                                <td name="leave_type"><?php echo $row->type_of_leave; ?></td>
                                                <td name="date_from"><?php echo formatDateOnly($row->date_from); ?></td>
                                                <td name="date_to"><?php echo formatDateOnly($row->date_to); ?></td>
                                                <td name="leave_reason" style="max-width: 200px; overflow: hidden; 
                                        text-overflow: ellipsis; white-space: nowrap;cursor: pointer;user-select:none" title="Double click to expand"><?php echo $row->reason; ?></td>
                                                <td name="status"><?php echo ucwords($row->head_status); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="material-icons">more_vert</i>
                                                        </a>
                                                        <div class="dropdown-menu update-leave" aria-labelledby="dropdownMenuButton_<?php echo $row->emp_id; ?>">

                                                            <a class="dropdown-item update-pending" href="#" data-bs-toggle="modal" data-bs-target="#view_request" data-target-id="<?php echo $row->id; ?>">
                                                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                            </a>
                                                            <a class="dropdown-item delete-employee" href="#" data-bs-toggle="modal" data-bs-target="#delete_approve_<?php echo $row->emp_id; ?>">
                                                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="edit_leave" tabindex="-1" aria-labelledby="edit_leave_label" aria-hidden="true">
                                                <div class="modal-dialog modal-lg ">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <!-- <h5 class="modal-title">Outgoing Pass</h5> -->
                                                            <h3 class="m-0 text-center">Leave Request Details </h3>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mb-4 justify-content-center">
                                                                <div class="col-md-9 d-flex justify-content-center align-items-center">
                                                                    <div class="account-logo">
                                                                        <img src="<?= base_url('assets/img/famco_logo_clear.png') ?>" alt="Famco Retail Inc." style="max-width: 100px; height: auto;" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <form id="update_leave" method="posts">
                                                                <input type="hidden" class="form-control text-left" id="leave_id" readonly>
                                                                <div class="mb-3 row">
                                                                    <div class="col-md-6">
                                                                        <label for="emp_name" class="form-label">Employee Name</label>
                                                                        <input type="text" class="form-control text-left" id="emp_name" readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="date_filled" class="form-label">Date Filled</label>
                                                                        <input type="text" class="form-control" id="date_filled" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 row">
                                                                    <div class="col-md-4">
                                                                        <label for="leave_type">Leave Type</label>
                                                                        <div>
                                                                            <input type="text" class="form-control" id="leave_type" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="date_from">Date From</label>
                                                                        <div>
                                                                            <input type="text" class="form-control" id="date_from" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="date_to">Date To</label>
                                                                        <div>
                                                                            <input type="text" class="form-control" id="date_to" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 row">
                                                                    <label for="leave_reason">Leave Reason</label>
                                                                    <div>
                                                                        <textarea class="form-control" id="leave_reason" rows="3" readonly></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 row">
                                                                    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 col-12 mx-auto">
                                                                        <div class="input-block mb-3 form-focus select-focus text-center">
                                                                            <div class="row">
                                                                                <div class="mb-3 row">
                                                                                    <div class="col-sm-6">
                                                                                        <button id="leave_denyButton" class="btn text-danger btn-block bg-white border border-danger" data-row-id="<?php echo $row->id; ?>">Deny</button>
                                                                                    </div>
                                                                                    <div class="col-sm-6">
                                                                                        <button id="leave_approveButton" class="btn btn-primary btn-block" data-row-idd="<?php echo $row->id; ?>">Approve</button>

                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="solid-tab2">
                    <div class="card mb-0">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title mb-0">Pending Outgoing Request</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="row ">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="outgoingreq_dt" class="datatable table-striped custom-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Name</th>
                                                        <th class="text-center">Date Filled</th>
                                                        <th class="text-center">Destination</th>
                                                        <th class="text-center">Time From</th>
                                                        <th class="text-center">Time To</th>
                                                        <th class="text-center">Reason</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $current_department_name = $this->session->userdata('department');
                                                    $dept_query = $this->db->query("
                                                        SELECT id 
                                                        FROM department 
                                                        WHERE department = ?
                                                    ", array($current_department_name));

                                                    if ($dept_query->num_rows() > 0) {
                                                        $row = $dept_query->row();
                                                        $current_department_id = $row->id;
                                                    } else {
                                                    }
                                                    $query = $this->db->query("
                                                        SELECT f.*, e.fname, e.lname 
                                                        FROM f_outgoing f 
                                                        LEFT JOIN employee e ON f.emp_id = e.id 
                                                        WHERE f.head_status = 'pending'
                                                        AND f.department = ?
                                                    ", array($current_department_id));

                                                    foreach ($query->result() as $row) {
                                                        $fname = $row->fname;
                                                        $lname = $row->lname;
                                                        $fullname = $fname . ' ' . $lname;
                                                    ?>
                                                        <tr class="hoverable-row" id="double-click-row_<?php echo $row->id ?>">
                                                            <td style="max-width: 200px; overflow: hidden; 
                                        text-overflow: ellipsis; white-space: nowrap;" name="og_name">

                                                                <?php echo $fullname ?>
                                                                <?php echo $row->id; ?>

                                                            </td>
                                                            <td><?php echo formatDateOnly($row->date_filled); ?></td>
                                                            <td name="going_to"><?php echo $row->going_to; ?></td>
                                                            <td name="og_date_from"><?php echo  date("h:i A", strtotime($row->time_from)); ?></td>
                                                            <td name="og_date_to"><?php echo date("h:i A", strtotime($row->time_to)); ?></td>
                                                            <td name="og_reason" style="max-width: 200px; overflow: hidden; 
                                        text-overflow: ellipsis; white-space: nowrap;cursor: pointer;user-select:none" title="Double click to expand"><?php echo $row->reason; ?></td>
                                                            <td name="status"><?php echo ucwords($row->head_status); ?></td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="material-icons">more_vert</i>
                                                                    </a>
                                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_<?php echo $row->emp_id; ?>">
                                                                        <!-- <a class="dropdown-item update-outgoing" href="#" data-bs-toggle="modal" data-bs-target="#edit_outgoing" data-emp-id="<?php echo $row->emp_id; ?>">
                                                                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                                            </a> -->
                                                                        <a class="dropdown-item update-outgoing" href="#" data-bs-toggle="modal" data-bs-target="#edit_outgoing" data-og-id="<?php echo $row->id; ?>">
                                                                            <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                                        </a>

                                                                        <a class="dropdown-item delete-employee" href="#" data-bs-toggle="modal" data-bs-target="#delete_approve_<?php echo $row->emp_id; ?>">
                                                                            <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <div class="modal fade" id="edit_outgoing" tabindex="-1" aria-labelledby="edit_outgoing_label" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg ">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <!-- <h5 class="modal-title">Outgoing Pass</h5> -->
                                                                        <h3 class="m-0 text-center">Outgoing Request Details</h3>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row mb-4 justify-content-center"> <!-- Added justify-content-center to center horizontally -->
                                                                            <div class="col-md-9 d-flex justify-content-center align-items-center"> <!-- Added justify-content-center to center horizontally -->
                                                                                <div class="account-logo">
                                                                                    <img src="<?= base_url('assets/img/famco_logo_clear.png') ?>" alt="Famco Retail Inc." style="max-width: 200px; height: auto;" /> <!-- Adjusted logo size -->
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <form id="update_outgoing" method="post">
                                                                            <input type="hidden" class="form-control text-left" id="og_id" readonly>
                                                                            <div class="mb-3 row">
                                                                                <div class="col-md-6">
                                                                                    <label for="emp_name" class="form-label">Employee Name</label>
                                                                                    <input type="text" class="form-control text-left" name="og_emp_name" id="og_emp_name" readonly>

                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <label for="date_filled" class="form-label">Date Filled</label>
                                                                                    <input type="text" class="form-control" id="og_date_filled" readonly>
                                                                                </div>
                                                                            </div>

                                                                            <div class="mb-3 row">
                                                                                <div class="col-md-4">
                                                                                    <label for="leave_type">Destination</label>
                                                                                    <div>
                                                                                        <input type="text" class="form-control" id="destin" readonly>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <label for="date_from">Time From</label>
                                                                                    <div>
                                                                                        <input type="text" class="form-control" id="og_time_from" readonly>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <label for="date_to">Time To</label>
                                                                                    <div>
                                                                                        <input type="text" class="form-control" id="og_time_to" readonly>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mb-3 row">
                                                                                <label for="leave_reason">Reason</label>
                                                                                <div>
                                                                                    <textarea class="form-control" id="outgoing_reason" rows="3" readonly></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mb-3 row">
                                                                                <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12 mx-auto">
                                                                                    <div class="input-block mb-3 form-focus select-focus text-center">
                                                                                        <button id="og_approveButton" class="btn btn-primary">Approve</button>
                                                                                        <button id="og_denyButton" class="btn btn-danger">Deny</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
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
                </div>
                <div class="tab-pane" id="solid-tab3">
                    <div class="card mb-0">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title mb-0">Pending Overtime Request</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="overtime_dt" class="datatable table-striped custom-table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Date Filled</th>
                                            <th class="text-center">OT Date</th>
                                            <th class="text-center">Time From</th>
                                            <th class="text-center">Time To</th>
                                            <th class="text-center">Reason</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $current_department_name = $this->session->userdata('department');
                                        $dept_query = $this->db->query("
                                         SELECT id 
                                         FROM department 
                                         WHERE department = ?
                                     ", array($current_department_name));

                                        if ($dept_query->num_rows() > 0) {
                                            $row = $dept_query->row();
                                            $current_department_id = $row->id;
                                        } else {
                                        }
                                        $query = $this->db->query("
                                         SELECT f.*, e.fname, e.lname 
                                         FROM f_overtime f 
                                         LEFT JOIN employee e ON f.emp_id = e.id 
                                         WHERE f.head_status = 'pending'
                                         AND f.department = ?
                                     ", array($current_department_id));

                                        foreach ($query->result() as $row) {
                                            $fname = $row->fname;
                                            $lname = $row->lname;
                                            $fullname = $fname . ' ' . $lname;
                                        ?>
                                            <tr class="hoverable-row" id="double-click-row_<?php echo $row->id ?>">
                                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" name="emp_name">
                                                    <?php echo $fullname; ?>
                                                </td>
                                                <td name="date_filled"><?php echo formatDateOnly($row->date_filled); ?></td>
                                                <td name="date_ot"><?php echo $row->date_ot; ?></td>
                                                <td name="time_in"><?php echo date("h:i A", strtotime($row->time_in)); ?></td>
                                                <td name="time_out"><?php echo date("h:i A", strtotime($row->time_out)); ?></td>
                                                <td name="ot_reason" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; cursor: pointer; user-select: none;" title="Double click to expand"><?php echo $row->reason; ?></td>
                                                <td name="status"><?php echo ucwords($row->head_status); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-link action-icon dropdown-toggle" type="button" id="dropdownMenuButton_<?php echo $row->emp_id; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="material-icons">more_vert</i>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton_<?php echo $row->emp_id; ?>">
                                                            <li>
                                                                <a class="dropdown-item update-ot" href="#" data-ot-id="<?php echo $row->id; ?>" data-emp-id="<?php echo $row->emp_id; ?>">
                                                                    <i class="fas fa-pencil-alt"></i> Edit
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item delete-employee" href="#" data-bs-toggle="modal" data-bs-target="#delete_approve_<?php echo $row->emp_id; ?>">
                                                                    <i class="fas fa-trash-alt"></i> Delete
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="edit_employee" tabindex="-1" aria-labelledby="edit_employee_label" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h3 class="m-0 text-center">Overtime Details</h3>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form id="update_employee" method="post">
                                                                <input type="text" class="form-control text-left" id="ot_id" readonly>
                                                                <div class="mb-3 row">
                                                                    <div class="col-md-6">
                                                                        <label for="emp_name" class="form-label">Employee Name</label>
                                                                        <input type="text" class="form-control text-left" name="ot_emp_name" id="ot_emp_name" readonly>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="date_filled" class="form-label">Date Filled</label>
                                                                        <input type="text" class="form-control" id="ot_date_filled" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 row">
                                                                    <!-- <div class="col-md-4">
                                                                        <label for="leave_type">Leave Type</label>
                                                                        <input type="text" class="form-control" id="leave_type" readonly>
                                                                    </div> -->
                                                                    <div class="col-md-4">
                                                                        <label for="date_from">Time From</label>
                                                                        <input type="text" class="form-control" id="ot_time_in" readonly>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="date_to">Time To</label>
                                                                        <input type="text" class="form-control" id="ot_time_out" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 row">
                                                                    <label for="leave_reason">Overtime Reason</label>
                                                                    <div>
                                                                        <textarea class="form-control" id="ot_reason" rows="3" readonly></textarea>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3 row">
                                                                    <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12 mx-auto">
                                                                        <div class="input-block mb-3 form-focus select-focus text-center">
                                                                            <button id="ot_approveButton" class="btn btn-primary">Approve</button>
                                                                            <button id="ot_denyButton" class="btn btn-danger">Deny</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="solid-tab4">
                    <div class="card mb-0">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title mb-0">Pending Undertime Request</h4>
                                </div>

                            </div>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">

                                <table id="undertime_dt" class="datatable table-striped custom-table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Date Filled</th>
                                            <th class="text-center">Undertime Date</th>
                                            <th class="text-center">Time From</th>
                                            <th class="text-center">Time To</th>
                                            <th class="text-center">Reason</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $current_department_name = $this->session->userdata('department');
                                        $dept_query = $this->db->query("
                                         SELECT id 
                                         FROM department 
                                         WHERE department = ?
                                     ", array($current_department_name));

                                        if ($dept_query->num_rows() > 0) {
                                            $row = $dept_query->row();
                                            $current_department_id = $row->id;
                                        } else {
                                        }
                                        $query = $this->db->query("
                                         SELECT f.*, e.fname, e.lname 
                                         FROM f_undertime f 
                                         LEFT JOIN employee e ON f.emp_id = e.id 
                                         WHERE f.head_status = 'pending'
                                         AND f.department = ?
                                     ", array($current_department_id));

                                        foreach ($query->result() as $row) {
                                            $fname = $row->fname;
                                            $lname = $row->lname;
                                            $fullname = $fname . ' ' . $lname;
                                        ?>
                                            <tr class="hoverable-row" id="double-click-row_<?php echo $row->id ?>">
                                                <td style="max-width: 200px; overflow: hidden; 
                                            text-overflow: ellipsis; white-space: nowrap;" name="ut_emp_name">
                                                    <?php echo $fullname; ?>
                                                </td>
                                                <td><?php echo formatDateOnly($row->date_filled); ?></td>
                                                <td name="ut_date"><?php echo formatDateOnly($row->date_of_undertime); ?></td>
                                                <td name="ut_time_in"><?php echo date("h:i A", strtotime($row->time_in)); ?></td>
                                                <td name="ut_time_out"><?php echo date("h:i A", strtotime($row->time_out)); ?></td>
                                                <td name="ut_reason" style="max-width: 200px; overflow: hidden; 
                    text-overflow: ellipsis; white-space: nowrap;cursor: pointer;user-select:none" title="Double click to expand"><?php echo $row->reason; ?></td>
                                                <td name="status"><?php echo ucwords($row->head_status); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="material-icons">more_vert</i>
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_<?php echo $row->emp_id; ?>">
                                                            <a class="dropdown-item update_undertime" href="#" data-bs-toggle="modal" data-bs-target="#edit_undertime" data-ut-id="<?php echo $row->id; ?>">
                                                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                            </a>
                                                            <a class="dropdown-item delete-employee" href="#" data-bs-toggle="modal" data-bs-target="#delete_approve_<?php echo $row->emp_id; ?>">
                                                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="edit_undertime" tabindex="-1" aria-labelledby="edit_undertime_label" aria-hidden="true">
                                                <div class="modal-dialog modal-lg ">
                                                    <div class="modal-content">
                                                        <div class="modal-header">

                                                            <h3 class="m-0 text-center">Undertime Request Details</h3>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mb-4 justify-content-center"> <!-- Added justify-content-center to center horizontally -->
                                                                <div class="col-md-9 d-flex justify-content-center align-items-center"> <!-- Added justify-content-center to center horizontally -->
                                                                    <div class="account-logo">
                                                                        <img src="<?= base_url('assets/img/famco_logo_clear.png') ?>" alt="Famco Retail Inc." style="max-width: 100px; height: auto;" /> <!-- Adjusted logo size -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <form id="update_undertime" method="post">
                                                                <input type="hidden" class="form-control text-left" id="ut_id" readonly>
                                                                <div class="mb-3 row">
                                                                    <div class="col-md-6">
                                                                        <label for="ut_emp_name" class="form-label">Employee Name</label>

                                                                        <input type="text" class="form-control text-left" name="ut_emp_name" id="ut_emp_name" readonly>

                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="date_filled" class="form-label">Date Filled</label>
                                                                        <input type="text" class="form-control" id="ut_date_filled" readonly>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3 row">
                                                                    <div class="col-md-4">
                                                                        <label for="leave_type">Undertime Date</label>
                                                                        <div>
                                                                            <input type="text" class="form-control" id="ut_date" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="date_from">Time From</label>
                                                                        <div>
                                                                            <input type="text" class="form-control" id="ut_time_from" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="date_to">Time To</label>
                                                                        <div>
                                                                            <input type="text" class="form-control" id="ut_time_to" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 row">
                                                                    <label for="leave_reason">Reason</label>
                                                                    <div>
                                                                        <textarea class="form-control" id="ut_reason" rows="3" readonly></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 row">
                                                                    <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12 mx-auto">
                                                                        <div class="input-block mb-3 form-focus select-focus text-center">
                                                                            <button id="ut_approveButton" class="btn btn-primary">Approve</button>
                                                                            <button id="ut_denyButton" class="btn btn-danger">Deny</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="solid-tab5">
                    <div class="card mb-0">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title mb-0">Pending Official Business Request</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="ob_dt" class="datatable table-striped custom-table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Date Applied</th>
                                            <th class="text-center">Destin From</th>
                                            <th class="text-center">Destin To</th>
                                            <th class="text-center">Time From</th>
                                            <th class="text-center">Time To</th>
                                            <th class="text-center">Reason</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $current_department_name = $this->session->userdata('department');
                                        $dept_query = $this->db->query("
                                         SELECT id 
                                         FROM department 
                                         WHERE department = ?
                                     ", array($current_department_name));

                                        if ($dept_query->num_rows() > 0) {
                                            $row = $dept_query->row();
                                            $current_department_id = $row->id;
                                        } else {
                                        }
                                        $query = $this->db->query("
                                         SELECT f.*, e.fname, e.lname 
                                         FROM f_off_bussiness f 
                                         LEFT JOIN employee e ON f.emp_id = e.id 
                                         WHERE f.head_status = 'pending'
                                         AND f.department = ?
                                     ", array($current_department_id));

                                        foreach ($query->result() as $row) {
                                            $fname = $row->fname;
                                            $lname = $row->lname;
                                            $fullname = $fname . ' ' . $lname;
                                        ?>
                                            <tr class="hoverable-row" id="<?php echo $row->id ?>">
                                                <td style="max-width: 200px; overflow: hidden; 
                    text-overflow: ellipsis; white-space: nowrap;" name="emp_name">
                                                    <?php echo $fullname; ?>
                                                </td>
                                                <td><?php echo formatDateOnly($row->date_filled); ?></td>
                                                <td name="leave_type"><?php echo $row->destin_from; ?></td>
                                                <td name="leave_type"><?php echo $row->destin_to; ?></td>
                                                <td name="time_from"><?php echo date("h:i A", strtotime($row->time_from)); ?></td>
                                                <td name="time_to"><?php echo date("h:i A", strtotime($row->time_to)); ?></td>


                                                <td name="leave_reason" style="max-width: 200px; overflow: hidden; 
                    text-overflow: ellipsis; white-space: nowrap;cursor: pointer;user-select:none" title="Double click to expand"><?php echo $row->reason; ?></td>

                                                <td name="status"><?php echo ucwords($row->head_status); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="material-icons">more_vert</i>
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_<?php echo $row->emp_id; ?>">
                                                            <a class="dropdown-item update_ob" href="#" data-bs-toggle="modal" data-bs-target="#edit_ob" data-ob-id="<?php echo $row->id; ?>">
                                                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                            </a>
                                                            <a class="dropdown-item delete-employee" href="#" data-bs-toggle="modal" data-bs-target="#delete_approve_<?php echo $row->emp_id; ?>">
                                                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="edit_ob" tabindex="-1" aria-labelledby="edit_ob_label" aria-hidden="true">
                                                <div class="modal-dialog modal-lg ">
                                                    <div class="modal-content">
                                                        <div class="modal-header">

                                                            <h3 class="m-0 text-center">Official Business Request Details</h3>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mb-4 justify-content-center"> <!-- Added justify-content-center to center horizontally -->
                                                                <div class="col-md-9 d-flex justify-content-center align-items-center"> <!-- Added justify-content-center to center horizontally -->
                                                                    <div class="account-logo">
                                                                        <img src="<?= base_url('assets/img/famco_logo_clear.png') ?>" alt="Famco Retail Inc." style="max-width: 100px; height: auto;" /> <!-- Adjusted logo size -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <form id="update_ob" method="post">
                                                                <input type="hidden" class="form-control text-left" id="ob_id" readonly>
                                                                <div class="mb-3 row">
                                                                    <div class="col-md-6">
                                                                        <label for="emp_name" class="form-label">Employee Name</label>
                                                                        <input type="text" class="form-control text-left" id="ob_emp_name" readonly>

                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="date_filled" class="form-label">Date Filled</label>
                                                                        <input type="text" class="form-control" id="ob_date_filled" readonly>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3 row">
                                                                    <div class="col-md-6">
                                                                        <label for="leave_type">Destination From</label>
                                                                        <div>
                                                                            <input type="text" class="form-control" id="destin_from" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="leave_type">Destination To</label>
                                                                        <div>
                                                                            <input type="text" class="form-control" id="destin_to" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 row">
                                                                    <div class="col-md-6">
                                                                        <label for="date_from">Time From</label>
                                                                        <div>
                                                                            <input type="text" class="form-control" id="time_from" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="date_to">Time To</label>
                                                                        <div>
                                                                            <input type="text" class="form-control" id="time_to" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <label for="leave_reason">Reason</label>
                                                                    <div>
                                                                        <textarea class="form-control" id="ob_reason" rows="3" readonly></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 row">
                                                                    <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12 mx-auto">
                                                                        <div class="input-block mb-3 form-focus select-focus text-center">
                                                                            <button id="ob_approveButton" class="btn btn-primary">Approve</button>
                                                                            <button id="ob_denyButton" class="btn btn-danger">Deny</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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


<div class="modal fade" id="modal_leave_request" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal_leave_request_label" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Increased modal size to large -->
        <form id="leave_request1" method="post">
            <div class="modal-content">
                <div class="modal-header">

                    <h3 class="m-0 text-center">Leave Request Form</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-4 justify-content-center"> <!-- Added justify-content-center to center horizontally -->
                            <div class="col-md-9 d-flex justify-content-center align-items-center"> <!-- Added justify-content-center to center horizontally -->
                                <div class="account-logo">
                                    <img src="<?= base_url('assets/img/famco_logo_clear.png') ?>" alt="Famco Retail Inc." style="max-width: 100px; height: auto;" /> <!-- Adjusted logo size -->
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="emp_id" class="form-label">Select Employee</label>
                                <select class="form-select show-tick" data-live-search="true" name="emp_id" id="emp_id">
                                    <option value="">-- Select an Employee --</option>
                                    <?php
                                    // Get employees from the database
                                    $employees = $this->db->order_by('id', 'ASC')->get('employee');

                                    // Check if there are any employees
                                    if ($employees->num_rows() > 0) {
                                        // Loop through each employee
                                        foreach ($employees->result() as $employee) {
                                            // Output an option for each employee
                                            echo '<option value="' . $employee->id . '">' . $employee->fname . ' ' . $employee->lname . '</option>';
                                        }
                                    } else {
                                        // If no employees found, display a message
                                        echo '<option disabled>No employees found</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="leaveType" class="form-label">Type of Leave</label>
                                <select class="form-select show-tick" data-live-search="true" name="leaveType">
                                    <option value="">-- Select an Option --</option>
                                    <?php
                                    // Get select-options
                                    $query = $this->db->order_by('id', 'ASC')->get('f_leave_type');

                                    // Check if query executed successfully
                                    if ($query->num_rows() > 0) {
                                        foreach ($query->result() as $row) {
                                            // Output each option with its value and text
                                            echo '<option value="' . $row->id . '">' . $row->leave_type . '</option>';
                                        }
                                    } else {
                                        // Handle no results from the database
                                        // echo '<option value="">No options found</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="from_date" class="form-label">From</label>
                                <input type="date" class="form-control" name="from_date" id="from_date" min="<?php echo date('Y-m-d'); ?>" />
                            </div>
                            <div class="col-md-3">
                                <label for="to_date" class="form-label">To</label>
                                <input type="date" class="form-control" name="to_date" id="to_date" min="<?php echo date('Y-m-d'); ?>" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="reason" class="form-label">Reason</label>
                                <textarea class="form-control" rows="3" name="reason" placeholder="State your reason here"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_outgoing_pass" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal_outgoing_pass_label" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Adjust modal size if needed -->
        <form id="outgoing_request1" method="post">
            <div class="modal-content">
                <div class="modal-header">

                    <h3 class="m-0 text-center">Outgoing Pass Form</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-4 justify-content-center"> <!-- Added justify-content-center to center horizontally -->
                            <div class="col-md-9 d-flex justify-content-center align-items-center"> <!-- Added justify-content-center to center horizontally -->
                                <div class="account-logo">
                                    <img src="<?= base_url('assets/img/famco_logo_clear.png') ?>" alt="Famco Retail Inc." style="max-width: 100px; height: auto;" /> <!-- Adjusted logo size -->
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="emp_id" class="form-label">Select Employee</label>
                                <select class="form-select" data-live-search="true" name="emp_id" id="emp_id">
                                    <option value="">-- Select an Employee --</option>
                                    <?php
                                    // Loop through each employee
                                    foreach ($employees->result() as $employee) {
                                        // Output an option for each employee
                                        echo '<option value="' . $employee->id . '">' . $employee->fname . ' ' . $employee->lname . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="outgoing_date" class="form-label">Date</label>
                                <input type="date" class="form-control" name="outgoing_date" id="outgoing_date" min="<?php echo date('Y-m-d'); ?>" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="time_from" class="form-label">From</label>
                                <input type="time" class="form-control" name="time_from" id="time_from" />
                            </div>
                            <div class="col-md-6">
                                <label for="time_to" class="form-label">To</label>
                                <input type="time" class="form-control" name="time_to" id="time_to" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="reason" class="form-label">Reason</label>
                                <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="State your reason here"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="destination" class="form-label">Destination</label>
                                <input type="text" class="form-control" name="destination" id="destination" placeholder="Destination">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_overtime_request" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal_overtime_request_label" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Adjust modal size if needed -->
        <form method="post" id="ot_request1">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title">Outgoing Pass</h5> -->
                    <h3 class="m-0 text-center">Overtime Request Form</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-4 justify-content-center"> <!-- Added justify-content-center to center horizontally -->
                            <div class="col-md-9 d-flex justify-content-center align-items-center"> <!-- Added justify-content-center to center horizontally -->
                                <div class="account-logo">
                                    <img src="<?= base_url('assets/img/famco_logo_clear.png') ?>" alt="Famco Retail Inc." style="max-width: 100px; height: auto;" /> <!-- Adjusted logo size -->
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="emp_id" class="form-label">Select Employee</label>
                                <select class="form-select show-tick" data-live-search="true" name="emp_id" id="emp_id">
                                    <option value="">-- Select an Employee --</option>
                                    <?php
                                    // Get employees from the database
                                    $employees = $this->db->order_by('id', 'ASC')->get('employee');

                                    // Check if there are any employees
                                    if ($employees->num_rows() > 0) {
                                        // Loop through each employee
                                        foreach ($employees->result() as $employee) {
                                            // Output an option for each employee
                                            echo '<option value="' . $employee->id . '">' . $employee->fname . ' ' . $employee->lname . '</option>';
                                        }
                                    } else {
                                        // If no employees found, display a message
                                        echo '<option disabled>No employees found</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="ot_date" class="form-label">Date of Overtime</label>
                                <input type="date" class="form-control" name="ot_date" id="ot_date" min="<?php echo date('Y-m-d'); ?>" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="from_time" class="form-label">Time from</label>
                                <input type="time" class="form-control" name="from_time" id="from_time" />
                            </div>
                            <div class="col-md-6">
                                <label for="to_time" class="form-label">Time to</label>
                                <input type="time" class="form-control" name="to_time" id="to_time" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="reason" class="form-label">Reason</label>
                                <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="State your reason here"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_undertime_request" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal_undertime_request_label" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Adjust modal size if needed -->
        <form method="post" id="undertime_request">

            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title">Outgoing Pass</h5> -->
                    <h3 class="m-0 text-center">Undertime Request Form</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-4 justify-content-center"> <!-- Added justify-content-center to center horizontally -->
                            <div class="col-md-9 d-flex justify-content-center align-items-center"> <!-- Added justify-content-center to center horizontally -->
                                <div class="account-logo">
                                    <img src="<?= base_url('assets/img/famco_logo_clear.png') ?>" alt="Famco Retail Inc." style="max-width: 100px; height: auto;" /> <!-- Adjusted logo size -->
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="emp_id" class="form-label">Select Employee</label>
                                <select class="form-select show-tick" data-live-search="true" name="emp_id" id="emp_id">
                                    <option value="">-- Select an Employee --</option>
                                    <?php
                                    // Get employees from the database
                                    $employees = $this->db->order_by('id', 'ASC')->get('employee');

                                    // Check if there are any employees
                                    if ($employees->num_rows() > 0) {
                                        // Loop through each employee
                                        foreach ($employees->result() as $employee) {
                                            // Output an option for each employee
                                            echo '<option value="' . $employee->id . '">' . $employee->fname . ' ' . $employee->lname . '</option>';
                                        }
                                    } else {
                                        // If no employees found, display a message
                                        echo '<option disabled>No employees found</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="undertime_date" class="form-label">Date of Undertime</label>
                                <input type="date" class="form-control" name="undertime_date" id="undertime_date" min="<?php echo date('Y-m-d'); ?>" />
                            </div>

                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="time_out" class="form-label">Time out</label>
                                <input type="time" class="form-control" name="time_out" id="time_out" />
                            </div>
                            <div class="col-md-6">
                                <label for="time_in" class="form-label">Time in</label>
                                <input type="time" class="form-control" name="time_in" id="time_in" />
                            </div>

                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="reason" class="form-label">Reason</label>
                                <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="State your reason here"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="add_ob" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal_ob_request_label" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Adjust modal size if needed -->
        <form method="post" id="ob_request1">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title m-0">Official Business Form</h5> <!-- Adjusted title -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="account-logo">
                                    <img src="<?= base_url('assets/img/famco_logo_clear.png') ?>" alt="Famco Retail Incorporated" style="max-width: 100px; height: auto;" /> <!-- Adjusted logo size -->
                                </div>
                            </div>
                            <div class="col-md-10">
                                <!-- Empty column for alignment -->
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="emp_id" class="form-label">Select Employee</label>
                                <select class="form-select show-tick" data-live-search="true" name="emp_id" id="emp_id">
                                    <option value="">-- Select an Employee --</option>
                                    <?php
                                    // Get employees from the database
                                    $employees = $this->db->order_by('id', 'ASC')->get('employee');

                                    // Check if there are any employees
                                    if ($employees->num_rows() > 0) {
                                        // Loop through each employee
                                        foreach ($employees->result() as $employee) {
                                            // Output an option for each employee
                                            echo '<option value="' . $employee->id . '">' . $employee->fname . ' ' . $employee->lname . '</option>';
                                        }
                                    } else {
                                        // If no employees found, display a message
                                        echo '<option disabled>No employees found</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="outgoing_pass_date" class="form-label">Date</label>
                                <input type="date" class="form-control" name="ob_date" id="ob_date" min="<?php echo date('Y-m-d'); ?>" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="destin_from" class="form-label">Destination From</label>
                                <input type="text" class="form-control" name="destin_from" id="destin_from" />
                            </div>
                            <div class="col-md-6">
                                <label for="destin_to" class="form-label">Destination To</label>
                                <input type="text" class="form-control" name="destin_to" id="destin_to" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="time_from" class="form-label">Time From</label>
                                <input type="time" class="form-control" name="time_from" id="time_from" />
                            </div>
                            <div class="col-md-6">
                                <label for="time_to" class="form-label">Time To</label>
                                <input type="time" class="form-control" name="time_to" id="time_to" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="reason" class="form-label">Reason</label>
                                <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="State your reason here"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal custom-modal fade" id="approveModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Approve Request Confirmation</h3>
                    <p style="font-size: 14px;">Are you really sure to Approve?</p>
                </div>
                <input type="hidden" id="approveModalRowId" value="">
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-6 text-end">
                            <button type="button" class="btn btn-secondary cancel-btn" data-bs-dismiss="modal">Cancel</button>
                        </div>
                        <div class="col-6 text-start"> 
                            <button type="button" class="btn btn-outline-success continue-btn" id="confirmApprove">Confirm Approve</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Confirm Approve Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure to Approve the request?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="confirmApprove">Confirm Approve</button>

            </div>
        </div>
    </div>
</div> -->
<div class="modal fade" id="denyModal" tabindex="-1" aria-labelledby="denyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Deny Request Confirmation</h3>
                    <p style="font-size: 14px;">Are you sure to Deny the request?</p>
                </div>
                <!-- Display the rowId here -->
                <input type="hidden" id="denyModalRowId" value="">
                <div class="modal-btn delete-action">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6 bg-primary">
                                <button type="button" class="btn btn-secondary cancel-btn" data-bs-dismiss="modal">Cancel</button>
                            </div>
                            <div class="col-6 text-start">
                                <button type="button" class="btn btn-danger continue-btn" id="confirmDeny">Confirm Deny</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     var approveButton = document.getElementById('approveButton');
    //     var denyButton = document.getElementById('denyButton');
    //     var approveModal = new bootstrap.Modal(document.getElementById('approveModal'));

    //     approveButton.addEventListener('click', function() {
    //         approveModal.show();
    //     });

    //     denyButton.addEventListener('click', function() {
    //         approveModal.show();
    //     });

    //     var confirmApproveButton = document.getElementById('confirmApprove');
    //     var confirmDenyButton = document.getElementById('confirmDeny');

    //     confirmApproveButton.addEventListener('click', function() {
    //         // Perform logic for approving OT status here
    //         approveModal.hide();
    //     });

    //     confirmDenyButton.addEventListener('click', function() {
    //         // Perform logic for denying OT status here
    //         approveModal.hide();
    //     });
    // });
</script>

<script>
    $(document).ready(function() {
        $("li > a[href='<?= base_url('hr/pendingrequests') ?>']").addClass("active");
        $("li > a[href='<?= base_url('hr/pendingrequests') ?>']").parent().parent().css("display", "block")

        $(".update-pending").click(function(event) {
            event.preventDefault();

            let leave_id = $(this).attr("data-target-id");
            let emp_id = $(this).attr("data-emp-id");
            let emp_name = $(this).closest('tr').find('td[name="emp_name"]').text().trim();
            let leave_type = $(this).closest('tr').find('td[name="leave_type"]').text();
            let date_filled = $(this).closest('tr').find('td[name="date_filled"]').text();
            let date_from = $(this).closest('tr').find('td[name="date_from"]').text();
            let date_to = $(this).closest('tr').find('td[name="date_to"]').text();
            let leave_reason = $(this).closest('tr').find('td[name="leave_reason"]').text();
            let status = $(this).closest('tr').find('td[name="status"]').text();

            $("#leave_id").val(leave_id);
            $("#leave_type").val(leave_type);
            $("#emp_id").val(emp_id);
            $("#emp_name").val(emp_name);
            $("#date_filled").val(date_filled);
            $("#date_from").val(date_from);
            $("#date_to").val(date_to);
            $("#leave_reason").val(leave_reason);
            $("#status").val(status);

            $('#edit_leave').modal('show');
        });

        $(".update-outgoing").click(function(event) {
            event.preventDefault();

            let og_id = $(this).attr("data-og-id");
            let emp_name = $(this).closest('tr').find('td[name="og_name"]').text().trim();
            let date_filled = $(this).closest('tr').find('td:eq(1)').text();
            let destin = $(this).closest('tr').find('td[name="going_to"]').text();
            let time_from = $(this).closest('tr').find('td[name="og_date_from"]').text();
            let time_to = $(this).closest('tr').find('td[name="og_date_to"]').text();
            let outgoing_reason = $(this).closest('tr').find('td[name="og_reason"]').text();

            $("#og_id").val(og_id);
            $("#og_emp_name").val(emp_name);
            $("#og_date_filled").val(date_filled);
            $("#destin").val(destin);
            $("#og_time_from").val(time_from);
            $("#og_time_to").val(time_to);
            $("#outgoing_reason").val(outgoing_reason);

            $('#edit_outgoing').modal('show');
        });

        $(".update-ot").click(function(event) {
            event.preventDefault();
            let ot_id = $(this).attr("data-ot-id");
            let ot_emp_id = $(this).attr("data-emp-id");
            let ot_emp_name = $(this).closest('tr').find('td[name="emp_name"]').text().trim();
            let ot_date_filled = $(this).closest('tr').find('td[name="date_ot"]').text();
            let ot_time_in = $(this).closest('tr').find('td[name="time_in"]').text();
            let ot_time_out = $(this).closest('tr').find('td[name="time_out"]').text();
            let ot_reason = $(this).closest('tr').find('td[name="ot_reason"]').text();
            let ot_status = $(this).closest('tr').find('td[name="status"]').text();

            $("#ot_id").val(ot_id);
            $("#ot_emp_id").val(ot_emp_id);
            $("#ot_emp_name").val(ot_emp_name);
            $("#ot_date_filled").val(ot_date_filled);
            $("#ot_time_in").val(ot_time_in);
            $("#ot_time_out").val(ot_time_out);
            $("#ot_reason").val(ot_reason);
            $("#ot_status").val(ot_status);

            $('#edit_employee').modal('show');

        });

        $(".update_undertime").click(function(event) {
            event.preventDefault();

            let ut_id = $(this).attr("data-ut-id");
            let ut_emp_name = $(this).closest('tr').find('td[name="ut_emp_name"]').text().trim();
            let ut_date_filled = $(this).closest('tr').find('td:eq(1)').text().trim();
            let ut_date = $(this).closest('tr').find('td[name="ut_date"]').text().trim();
            let ut_time_from = $(this).closest('tr').find('td[name="ut_time_in"]').text().trim();
            let ut_time_to = $(this).closest('tr').find('td[name="ut_time_out"]').text().trim();
            let ut_reason = $(this).closest('tr').find('td[name="ut_reason"]').text().trim();

            $("#ut_id").val(ut_id);
            $("#ut_emp_name").val(ut_emp_name);
            $("#ut_date_filled").val(ut_date_filled);
            $("#ut_date").val(ut_date);
            $("#ut_time_from").val(ut_time_from);
            $("#ut_time_to").val(ut_time_to);
            $("#ut_reason").val(ut_reason);

            $('#edit_undertime').modal('show');
        });

        $(".update_ob").click(function(event) {
            event.preventDefault();

            let ob_id = $(this).attr("data-ob-id");
            let ob_emp_name = $(this).closest('tr').find('td[name="emp_name"]').text().trim();
            let ob_date_filled = $(this).closest('tr').find('td:eq(1)').text().trim();
            let destin_from = $(this).closest('tr').find('td[name="leave_type"]').eq(0).text().trim();
            let destin_to = $(this).closest('tr').find('td[name="leave_type"]').eq(1).text().trim();
            let time_from = $(this).closest('tr').find('td[name="time_from"]').text().trim();
            let time_to = $(this).closest('tr').find('td[name="time_to"]').text().trim();
            let ob_reason = $(this).closest('tr').find('td[name="leave_reason"]').text().trim();

            $("#ob_id").val(ob_id);
            $("#ob_emp_name").val(ob_emp_name);
            $("#ob_date_filled").val(ob_date_filled);
            $("#destin_from").val(destin_from);
            $("#destin_to").val(destin_to);
            $("#time_from").val(time_from);
            $("#time_to").val(time_to);
            $("#ob_reason").val(ob_reason);

            $('#edit_ob').modal('show');
        });

        $(document).on("submit", "#expanded_vq", function(e) {
            e.preventDefault();
            var expanded_vq = $(this).serialize();
            console.log("Serialized Form Data:", expanded_vq);
            $.ajax({
                url: base_url + 'humanr/update_leave_status',
                type: 'post',
                data: expanded_vq,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 1) {
                        alert(res.msg);
                    } else {
                        alert(res.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                }
            });
        });


    });
</script>




<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"></script>