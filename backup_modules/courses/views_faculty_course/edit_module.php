<!-- View file: application/views/course/edit_module.php -->
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Edit Module</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/course') ?>">Courses</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/course/modules/'.$course['id']) ?>">Modules</a></li>
                <li class="breadcrumb-item active">Edit Module</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>
        
        <div class="card p-2 mb-4">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Course Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Course Code:</strong> <?= $course['course_code'] ?></p>
                        <p><strong>Course Name:</strong> <?= $course['name'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Description:</strong> <?= $course['description'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Edit Module Form -->
        <div class="card p-2">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Edit Module</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url($url.'/course/edit_module/'.$course['id'].'/'.$module['id']) ?>" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="module_name">Module Name</label>
                                <input type="text" class="form-control" id="module_name" name="module_name" value="<?= $module['name'] ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="module_description">Description</label>
                                <textarea class="form-control" id="module_description" name="module_description" rows="3"><?= $module['description'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Update Module</button>
                            <a href="<?= base_url($url.'/course/modules/'.$course['id']) ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>