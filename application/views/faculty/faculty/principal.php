<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Principal</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <!-- <li class="breadcrumb-item">Purchase</li> -->
                <li class="breadcrumb-item">Principal</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>
        <!-- <div class="card mb-4">
            <form method="get" action="<?= base_url('Purchase/Invoice') ?>">
                <div class="card-body">
                    <div class="form-row align-items-center">
                        <div class="col-md my-2">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" value="<?= $this->input->get("search") ?>" placeholder="Search Invoice No" class="form-control">
                        </div>
                        <div class="col-md my-2">
                            <label class="form-label">Customer</label>
                            <input type="text" name="customer_search" value="<?= $this->input->get("customer_search") ?>" placeholder="Search Customer" class="form-control">

                        </div>
                        <div class="col-md my-2">
                            <label class="form-label">Start date</label>
                            <input type="date" name="form_date" value="<?php echo $this->input->get('form_date') ? $this->input->get('form_date') : date('Y-m-d', strtotime('-7 days')); ?>" class="form-control">
                        </div>
                        <div class="col-md my-2">
                            <label class="form-label">End date</label>
                            <input type="date" name="to_date" value="<?php echo $this->input->get('to_date') ? $this->input->get('to_date') : date('Y-m-d'); ?>" class="form-control">
                        </div>
                        <div class="col-md col-xl-2 my-2">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <button class="btn btn-primary btn-block">Show</button>
                        </div>
                    </div>
                </div>
            </form>
        </div> -->
        <div class="card p-2">
            <!-- <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Invoice Details</h6>
                <div>
                    <a href="<?= base_url('Purchase/AddInvoice') ?>" class="btn btn-primary mr-3">Add Invoice</a>
                </div>
            </div> -->
            <div class="card-datatable container table-responsive">
                <table id="mytable" class="datatables-demo table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Created At</th>
                            <th>Reset Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($principal)) {
                            $no = 0;
                            foreach ($principal as $row) {
                                $no++ ?>
                                <tr>
                                    <td> <?= $no; ?></td>
                                    <td><?php if (isset($row['name'])) {
                                            echo $row['name'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td><?php if (isset($row['email'])) {
                                            echo $row['email'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td><?php if (isset($row['phone_number'])) {
                                            echo $row['phone_number'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td><?php if (isset($row['created_at'])) {
                                            echo $this->common->display_date($row['created_at']);
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td>
                                        <button type="button" onclick="model_open(<?=$row['id']?>)"  class="btn btn-warning btn-sm" ><i class="feather icon-edit"></i>&nbsp;Reset Password </button>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="resetpassword" tabindex="-1" aria-labelledby="resetpassword" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetpassword">Reset Password</h5> 
            </div>
            <div class=" ml-4 m-2">
                <form  action="<?= $post_url ?>" method="POST"> 
                    <input type="hidden" name="id" id="reset_id">
                    <div class="mb-3 p-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="text" class="form-control" id="password" name="password" required>
                    </div> 
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveVoucherBtn">Reset</button>
                </form>
            </div>
        </div>
        <div class="modal-footer">
        </div>
    </div>
</div>

<script>
    function model_open(id){
        var resetModel = new bootstrap.Modal(document.getElementById('resetpassword'));
        document.getElementById("reset_id").value = id;
        resetModel.show();

        $('#resetpassword').on('shown.bs.modal', function() {
            $('#password').focus();
        });
    }
</script>

<script src="<?= base_url('') ?>assets/faculty/libs/datatables/datatables.js"></script>
<script src="<?= base_url('') ?>assets/faculty/js/pages/tables_datatables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="<?= base_url('') ?>assets/faculty/js/pages/forms_selects.js"></script>
<script src="<?= base_url('') ?>assets/faculty/libs/bootstrap-select/bootstrap-select.js"></script>
<script src="<?= base_url('') ?>assets/faculty/libs/select2/select2.js"></script>