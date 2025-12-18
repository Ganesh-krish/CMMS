<style>
    .choices {
        min-height: 38px !important;
        /* Same as Bootstrap form-control */
    }

    .choices__inner {
        min-height: 38px !important;
        padding: 6px 12px !important;
        background-color: #fff !important;
        /* Matches form input */
    }
</style>
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">
            <?php echo isset($group) ? 'Edit Music Group' : 'Add New Music Group'; ?>
        </h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/groups'); ?>">Music Groups</a></li>
                <li class="breadcrumb-item active"><?php echo isset($group) ? 'Edit' : 'Add'; ?> Group</li>
            </ol>
        </div>

        <!-- Flash Message -->
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Music Group Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label"><i class="feather icon-tag mr-2"></i>Group Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?php echo isset($group) ? htmlspecialchars($group['name']) : ''; ?>"
                                       placeholder="Enter music group name" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label"><i class="feather icon-file-text mr-2"></i>Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                          placeholder="Enter group description (optional)"><?php echo isset($group) ? htmlspecialchars($group['description']) : ''; ?></textarea>
                                <small class="form-text text-muted">Optional description for the music group</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-save"></i>
                                    <?php echo isset($group) ? 'Update Music Group' : 'Create Music Group'; ?>
                                </button>
                                <a href="<?php echo base_url($url.'/groups'); ?>" class="btn btn-secondary">
                                    <i class="feather icon-arrow-left"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
   document.addEventListener("DOMContentLoaded", function() {
    new Choices("#group_members", {
        removeItemButton: true,
        placeholder: true,
        placeholderValue: "Select Group Members",
        classNames: {
            containerOuter: 'choices', // Matches other inputs
        }
    });

    // Fix alignment issue
    document.querySelector(".choices__inner").style.padding = "8px";
});
</script>