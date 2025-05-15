<!-- Sidebar Start -->
<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a href="<?= base_url('dashboard') ?>" class="navbar-brand mx-4 mb-3">
            <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>PT.CNF</h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle" src="<?= base_url('assets/dashmin-1.0.0/') ?>img/user.jpg" alt=""
                    style="width: 40px; height: 40px;">
                <div
                    class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                </div>
            </div>

            <div class="ms-3">
                <h6 class="mb-0"><?= session()->get('auth')['name'] ?></h6>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <a href="<?= base_url('dashboard') ?>" class="nav-item nav-link"><i
                    class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
            <?php
            if (session()->has('auth')) {
                if (session()->get('auth')['level'] == 1) {


                    ?>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i
                                class="fa fa-cube me-2"></i>Master Data</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="<?= base_url('supplier') ?>" class="dropdown-item"> Supplier</a>
                            <a href="<?= base_url('customer') ?>" class="dropdown-item"> Customer</a>
                            <a href="<?= base_url('material') ?>" class="dropdown-item"> Material</a>
                            <!-- <a href="<?= base_url('finishing') ?>" class="dropdown-item"> Finishing</a> -->

                            <a href="<?= base_url('product') ?>" class="dropdown-item">BoM Finished goods</a>
                            <!-- <a href="<?= base_url('track_material') ?>" class="dropdown-item">Track Material Inventory</a> -->
                            <a href="<?= base_url('department') ?>" class="dropdown-item">Department</a>
                            <a href="<?= base_url('warehouse') ?>" class="dropdown-item">Warehouse</a>
                            <a href="<?= base_url('productionArea') ?>" class="dropdown-item">Production Area</a>
                            <!-- <a href="<?= base_url('scrap_management') ?>" class="dropdown-item">Scrap Management</a> -->
                        </div>
                    </div>
                <?php
                }
            }
            ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i
                        class="fa fa-industry  me-2"></i>Production</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="<?= base_url('production') ?>" class="dropdown-item">All Productions</a>
                    <a href="<?= base_url('design') ?>" class="dropdown-item">Production Design</a>
                    <a href="<?= base_url('scrap') ?>" class="dropdown-item">Scrap</a>
                    <a href="<?= base_url('materialrequisition') ?>" class="dropdown-item">Material Requisition</a>

                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i
                        class="fa fa-cubes me-2"></i>Logistic</a>
                <div class="dropdown-menu bg-transparent border-0">

                    <a href="<?= base_url('stock') ?>" class="dropdown-item">Stock</a>
                    <a href="<?= base_url('material_requisition_progress') ?>" class="dropdown-item">Progress Material Requisition</a>
                    <a href="<?= base_url('pemusnahan') ?>" class="dropdown-item"> Inventory reduction</a>
                    <a href="<?= base_url('materialreturn') ?>" class="dropdown-item">Material Return</a>
                    <a href="<?= base_url('stock') ?>" class="dropdown-item">Stock Opname</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i
                        class="fa fa-archive me-2"></i>Document</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="<?= base_url('proformainvoice') ?>" class="dropdown-item">Proforma Invoice</a>
                    <a href="<?= base_url('work_order') ?>" class="dropdown-item">Work Orders</a>

                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i
                        class="fa fa-file me-2"></i>Purchase</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="<?= base_url('material_request') ?>" class="dropdown-item">PR</a>
                    <a href="<?= base_url('purchase_order') ?>" class="dropdown-item">PO</a>
                    <a href="<?= base_url('pembelian') ?>" class="dropdown-item">Goods Received Note</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i
                        class="fa fa-recycle me-2"></i>Scrap</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="<?= base_url('record_scrap') ?>" class="dropdown-item">Record Scrap</a>
                </div>
            </div>
            <?php
                    if (session()->has('auth')) {
                        if (session()->get('auth')['email'] != 'beacukai@mail.com') {
                            ?>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i
                        class="fa fa-user me-2"></i>User</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <?php
                    if (session()->has('auth')) {
                        if (session()->get('auth')['level'] == 1) {


                            ?>
                            <a href="<?= base_url('user') ?>" class="dropdown-item">All user</a>
                            <a href="<?= base_url('role') ?>" class="dropdown-item">Roles & Permissions</a>
                            <a href="<?= base_url('activitylog') ?>" class="dropdown-item">User Activity logs</a>
                        <?php }
                    } ?>
                    <a href="<?= base_url('employee') ?>" class="dropdown-item">Employee</a>
                    <a href="<?= base_url('salary') ?>" class="dropdown-item">Salary Setting</a>
                    <a href="<?= base_url('master_salary') ?>" class="dropdown-item">Master Salary</a>
                </div>
            </div>
            <?php }
        } ?> 
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle " data-bs-toggle="dropdown"><i
                        class="far fa-file-alt me-2"></i>Report</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="<?= base_url('report/material') ?>" class="dropdown-item">Material</a>
                    <a href="<?= base_url('report/finished_good') ?>" class="dropdown-item">Finished Good</a>
                    
                </div>
            </div>
        </div>
    </nav>
</div>
<!-- Sidebar End -->


<!-- Content Start -->
<div class="content">
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
        <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
            <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
        </a>
        <a href="#" class="sidebar-toggler flex-shrink-0">
            <i class="fa fa-bars"></i>
        </a>
        <div class="navbar-nav align-items-center ms-auto h1"><?php if (isset($title)) {
            echo $title;
        } ?></div>

        <div class="navbar-nav align-items-center ms-auto">


            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img class="rounded-circle me-lg-2" src="<?= base_url('assets/dashmin-1.0.0/') ?>img/user.jpg" alt=""
                        style="width: 40px; height: 40px;">
                    <span class="d-none d-lg-inline-flex"><?= session()->get('auth')['name'] ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                    <a href="#" class="dropdown-item">My Profile</a>
                    <a href="#" class="dropdown-item">Settings</a>
                    <a href="<?= base_url('logout') ?>" class="dropdown-item">Log Out</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->