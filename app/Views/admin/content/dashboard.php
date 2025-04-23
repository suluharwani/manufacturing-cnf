

<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">FACTORY PRODUCTION</h6>
        </div>
        <div class="container mt-5">
            <div id="statusMessage" class="mt-3"></div>
        </div>
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-dark">
                        <th scope="col">#</th>
                        <th scope="col">PI</th>
                        <th scope="col">CUSTOMER</th>
                        <th scope="col">PO CUSTOMER</th>
                        <th scope="col">ORDER</th>
                        <th scope="col">WAITING PROGRESS</th>
                        <th scope="col">PRODUCTION</th>
                        <th scope="col">WAREHOUSE</th>
                        <th scope="col">VIEW</th>
                    </tr>
                </thead>
                <tbody id="tableProduction">
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Currency rate</h6>
            <button class= "btn btn-primary updateCurrency">Update</button>
        </div>
        <div class="container mt-5">
            <div id="statusMessage" class="mt-3"></div>
        </div>
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-dark">
                        <th scope="col">#</th>
                        <th scope="col">Code</th>
                        <th scope="col">Currency</th>
                        <th scope="col">Last Date</th>
                        <th scope="col">Rate 1 IDR</th>
                        <th scope="col">New Date</th>
                        <th scope="col">Rate 1 IDR</th>
                        <th scope="col">Graph IDR</th>
                    </tr>
                </thead>
                <tbody id="tableCurrency">
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Recent Sales End -->


<!-- Widgets Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-md-6 col-xl-6">
            <div class="h-100 bg-light rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="mb-0">Country Data</h6>

                </div>
                <div class="table-responsive" style="max-height: 300px;">
                  <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Negara</th>
                            <th scope="col">Kode 1</th>
                            <th scope="col">Kode 2</th>
                            <th scope="col">Flag</th> 
                        </tr>
                    </thead>
                    <tbody id= "country"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- <div class="col-sm-12 col-md-6 col-xl-6">
        <div class="h-100 bg-light rounded p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h6 class="mb-0">To Do List</h6>
                <a href="">Show All</a>
            </div>
            <div class="d-flex mb-2">
                <input class="form-control bg-transparent" type="text" placeholder="Enter task">
                <button type="button" class="btn btn-primary ms-2">Add</button>
            </div>
            <div class="d-flex align-items-center border-bottom py-2">
                <input class="form-check-input m-0" type="checkbox">
                <div class="w-100 ms-3">
                    <div class="d-flex w-100 align-items-center justify-content-between">
                        <span>Short task goes here...</span>
                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center border-bottom py-2">
                <input class="form-check-input m-0" type="checkbox">
                <div class="w-100 ms-3">
                    <div class="d-flex w-100 align-items-center justify-content-between">
                        <span>Short task goes here...</span>
                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center border-bottom py-2">
                <input class="form-check-input m-0" type="checkbox" checked>
                <div class="w-100 ms-3">
                    <div class="d-flex w-100 align-items-center justify-content-between">
                        <span><del>Short task goes here...</del></span>
                        <button class="btn btn-sm text-primary"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center border-bottom py-2">
                <input class="form-check-input m-0" type="checkbox">
                <div class="w-100 ms-3">
                    <div class="d-flex w-100 align-items-center justify-content-between">
                        <span>Short task goes here...</span>
                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center pt-2">
                <input class="form-check-input m-0" type="checkbox">
                <div class="w-100 ms-3">
                    <div class="d-flex w-100 align-items-center justify-content-between">
                        <span>Short task goes here...</span>
                        <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</div>
</div>
<!-- Widgets End -->


<script type="text/javascript" src="<?= base_url('assets') ?>/js/dashboard.js"></script>
