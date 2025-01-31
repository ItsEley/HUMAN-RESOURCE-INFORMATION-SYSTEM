
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

<!-- Main Wrapper -->
<div class="main-wrapper">
    <!-- Header -->
    <?php $this->load->view('templates/nav_bar'); ?>
    <!-- /Header -->
    <!-- Sidebar -->
    <?php $this->load->view('templates/sidebar') ?>
    <!-- /Sidebar -->

    <!-- Page Wrapper -->
    <div class="page-wrapper">

        <!-- Page Content -->
        <div class="content container-fluid">


            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Salary Rates</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="admin-dashboard.html">Payroll</a></li>
                            <li class="breadcrumb-item active">Salary Rates</li>
                        </ul>
                    </div>
                
                </div>
            </div>
            <!-- /Page Header -->


            <!-- data table -->
            <div class="row timeline-panel">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="dt_emp_salary" class="datatable table-striped custom-table mb-0">
                            <thead>
                                <tr>
                                    <th hidden>ID</th>
                                    <th>Name</th>
                                    <th>Deparment</th>
                                    <th>Role</th>
                                    <th>Salary Rate</th>
                                    <th>Salary Type</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $query = $this->db->query("SELECT 
                                e.employee_id,
                                e.id,
                              CONCAT(e.fname, ' ', e.mname, ' ', e.lname) AS full_name,
                                e.department AS department_id,
                                d.department,
                                e.role AS role_id,
                                dr.roles AS role,
                                dr.salary,
                                dr.salary_type,
                                e.shift
                            FROM 
                                employee e
                            JOIN 
                                department d ON e.department = d.id
                            JOIN 
                                department_roles dr ON e.role = dr.id;
                            
                            ");

                                foreach ($query->result() as $row) {



                                ?>
                                    <tr class="hoverable-row">
                                        <td hidden></td>
                                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?php echo $row->full_name; ?>
                                        </td>
                                        <td style="max-width: 200px; max-height: 100px; overflow: hidden;">
                                            <div class="ellipsis" style="max-height: 1.2em; overflow: hidden;">
                                                <?php echo $row->department;; ?>
                                            </div>
                                        </td>

                                        <td><?php echo $row->role;?></td>
                                        <td><?php echo $row->salary;?> </td>
                                        <td><?php echo $row->salary_type;?></td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="material-icons">more_vert</i>
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_">
                                                    <a class="dropdown-item edit-employee" href="#" data-bs-toggle="modal" data-bs-target="#edit_employee" data-emp-id="">
                                                        <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                    </a>
                                                    <a class="dropdown-item delete-employee" href="#" data-bs-toggle="modal" data-bs-target="#delete_approve_">
                                                        <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                    </a>
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
            <!-- /data table -->

            
        </div>
        
        <!-- /Page Content -->
    </div>
    <!-- /Page Wrapper -->
</div>
<!-- /Main Wrapper -->

<!-- jQuery -->




<script>
    document.addEventListener('DOMContentLoaded', function() {

        $("li > a[href='<?= base_url('hr/payroll/salary_rate') ?>']").addClass("active");
        $("li > a[href='<?= base_url('hr/payroll/salary_rate') ?>']").parent().parent().css("display", "block")

        $('#dt_emp_salary').DataTable();


    })
</script>