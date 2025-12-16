<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Cropper.js JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Profile</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li> 
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>
        <div class="card mb-4">
            <h6 class="card-header">Profile</h6>
            <div class="card-body">
                <form method="post" action="<?= base_url("$url/principal/profile") ?>" enctype="multipart/form-data" id="profileForm">
                    <input type="hidden" name="url" value="<?=$url?>">
                    
                    <!-- Logo Upload -->
                    <div class="form-group">
                        <label class="form-label" for="logo">Logo:</label>
                        
                        <!-- Show existing logo if available -->
                        <?php if (isset($logo) && !empty($logo)) : ?>
                        <div class="existing-image mb-3">
                            <p class="mb-2">Current Logo:</p>
                            <img src="<?= $logo ?>" style="max-height: 100px; border: 1px solid #ddd; padding: 5px;" alt="Current Logo">
                        </div>
                        <?php endif; ?>
                        
                        <input type="file" class="form-control" id="imageInput1" accept="image/jpeg,image/png,image/gif,image/webp">
                        <small class="form-text text-muted">Upload a new logo to replace the existing one (optional). Max size: 1MB. Allowed formats: JPG, PNG, GIF, WEBP</small>
                        
                        <div id="fileError1" class="text-danger mt-1" style="display: none;"></div>
                        
                        <div class="cropper-container mt-3" style="display: none;">
                            <div class="img-container" style="max-height: 400px; margin-bottom: 10px;">
                                <img id="imagePreview1" style="max-width: 100%; display: block;">
                            </div>
                            <div class="cropper-controls mb-3">
                                <button type="button" class="btn btn-secondary btn-sm zoom-in" data-target="1"><i class="fa fa-search-plus"></i> Zoom In</button>
                                <button type="button" class="btn btn-secondary btn-sm zoom-out" data-target="1"><i class="fa fa-search-minus"></i> Zoom Out</button>
                                <button type="button" class="btn btn-secondary btn-sm reset" data-target="1"><i class="fa fa-refresh"></i> Reset</button>
                                <button type="button" id="cropButton1" class="btn btn-primary btn-sm"><i class="fa fa-crop"></i> Apply Crop</button>
                            </div>
                        </div>
                        <div class="cropped-preview mt-3" style="display: none;">
                            <h6>New Logo Preview:</h6>
                            <img id="croppedPreview1" style="max-width: 100%; max-height: 200px; border: 1px solid #ddd;">
                        </div>
                        <input type="hidden" name="croppedImageData1" id="croppedImageData1">
                    </div>

                    <!-- Banner Upload -->
                    <div class="form-group">
                        <label class="form-label" for="banner">Banner:</label>
                        
                        <!-- Show existing banner if available -->
                        <?php if (isset($banner) && !empty($banner)) : ?>
                        <div class="existing-image mb-3">
                            <p class="mb-2">Current Banner:</p>
                            <img src="<?= $banner ?>" style="max-height: 200px; border: 1px solid #ddd; padding: 5px;" alt="Current Banner">
                        </div>
                        <?php endif; ?>
                        
                        <input type="file" class="form-control" id="imageInput2" accept="image/jpeg,image/png,image/gif,image/webp">
                        <small class="form-text text-muted">Upload a new banner to replace the existing one (optional). Max size: 1MB. Allowed formats: JPG, PNG, GIF, WEBP</small>
                        
                        <div id="fileError2" class="text-danger mt-1" style="display: none;"></div>
                        
                        <div class="cropper-container mt-3" style="display: none;">
                            <div class="img-container" style="max-height: 400px; margin-bottom: 10px;">
                                <img id="imagePreview2" style="max-width: 100%; display: block;">
                            </div>
                            <div class="cropper-controls mb-3">
                                <button type="button" class="btn btn-secondary btn-sm zoom-in" data-target="2"><i class="fa fa-search-plus"></i> Zoom In</button>
                                <button type="button" class="btn btn-secondary btn-sm zoom-out" data-target="2"><i class="fa fa-search-minus"></i> Zoom Out</button>
                                <button type="button" class="btn btn-secondary btn-sm reset" data-target="2"><i class="fa fa-refresh"></i> Reset</button>
                                <button type="button" id="cropButton2" class="btn btn-primary btn-sm"><i class="fa fa-crop"></i> Apply Crop</button>
                            </div>
                        </div>
                        <div class="cropped-preview mt-3" style="display: none;">
                            <h6>New Banner Preview:</h6>
                            <img id="croppedPreview2" style="max-width: 100%; max-height: 200px; border: 1px solid #ddd;">
                        </div>
                        <input type="hidden" name="croppedImageData2" id="croppedImageData2">
                    </div> 

                    <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Maximum file size in bytes (1MB = 1048576 bytes)
    const MAX_FILE_SIZE = 1048576;
    
    // Allowed file types
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    // Store cropper instances
    var croppers = {
        1: null,  // Logo cropper
        2: null   // Banner cropper
    };
    
    // Store whether files were chosen
    var fileUploaded = {
        1: false,
        2: false
    };
    
    // Validate file before processing
    function validateFile(file, errorElementId) {
        const errorElement = document.getElementById(errorElementId);
        errorElement.style.display = 'none';
        errorElement.textContent = '';
        
        // Check if file is an image
        if (!file || !file.type.match('image.*')) {
            errorElement.textContent = 'Please select a valid image file.';
            errorElement.style.display = 'block';
            return false;
        }
        
        // Check file type
        if (!ALLOWED_TYPES.includes(file.type)) {
            errorElement.textContent = 'Invalid file format. Allowed formats: JPG, PNG, GIF, WEBP.';
            errorElement.style.display = 'block';
            return false;
        }
        
        // Check file size
        if (file.size > MAX_FILE_SIZE) {
            errorElement.textContent = 'File is too large. Maximum size allowed is 1MB.';
            errorElement.style.display = 'block';
            return false;
        }
        
        return true;
    }
    
    // Initialize cropper for an image
    function initCropper(imageId) {
        var imageElement = document.getElementById('imagePreview' + imageId);
        var cropperContainer = imageElement.closest('.cropper-container');
        
        cropperContainer.style.display = 'block';
        
        // Destroy previous cropper if exists
        if (croppers[imageId]) {
            croppers[imageId].destroy();
        }
        
        // Initialize new cropper with free-form cropping (no fixed aspect ratio)
        croppers[imageId] = new Cropper(imageElement, {
            viewMode: 1,
            dragMode: 'move',
            aspectRatio: NaN,  // Free aspect ratio for flexible cropping
            autoCropArea: 0.8,
            restore: false,
            guides: true,
            center: true,
            highlight: true,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: true
        });
    }
    
    // Apply crop to image
    function applyCrop(imageId) {
        if (!croppers[imageId]) return;
        
        var cropper = croppers[imageId];
        var croppedCanvas = cropper.getCroppedCanvas({
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });
        
        if (!croppedCanvas) {
            alert('Cropping failed. Please try again.');
            return;
        }
        
        // Get the cropped image data
        var croppedImageData = croppedCanvas.toDataURL('image/png');
        
        // Set the data to the hidden input
        document.getElementById('croppedImageData' + imageId).value = croppedImageData;
        
        // Display preview of cropped image
        var croppedPreview = document.getElementById('croppedPreview' + imageId);
        croppedPreview.src = croppedImageData;
        croppedPreview.closest('.cropped-preview').style.display = 'block';
        
        // Indicate successful crop
        console.log('Image ' + imageId + ' cropped successfully');
    }
    
    // Handle file input change
    document.getElementById('imageInput1').addEventListener('change', function(event) {
        if (event.target.files.length > 0) {
            const file = event.target.files[0];
            if (validateFile(file, 'fileError1')) {
                fileUploaded[1] = true;
                handleImageInput(event, 1);
            } else {
                // Reset file input if validation fails
                this.value = '';
                fileUploaded[1] = false;
                
                // Hide cropper container if it was previously shown
                document.querySelector('.cropper-container').style.display = 'none';
                document.querySelector('.cropped-preview').style.display = 'none';
            }
        }
    });
    
    document.getElementById('imageInput2').addEventListener('change', function(event) {
        if (event.target.files.length > 0) {
            const file = event.target.files[0];
            if (validateFile(file, 'fileError2')) {
                fileUploaded[2] = true;
                handleImageInput(event, 2);
            } else {
                // Reset file input if validation fails
                this.value = '';
                fileUploaded[2] = false;
                
                // Hide cropper containers
                const cropperContainers = document.querySelectorAll('.cropper-container');
                if (cropperContainers.length > 1) {
                    cropperContainers[1].style.display = 'none';
                }
                const croppedPreviews = document.querySelectorAll('.cropped-preview');
                if (croppedPreviews.length > 1) {
                    croppedPreviews[1].style.display = 'none';
                }
            }
        }
    });
    
    function handleImageInput(event, imageId) {
        var file = event.target.files[0];
        
        var reader = new FileReader();
        reader.onload = function(e) {
            var imagePreview = document.getElementById('imagePreview' + imageId);
            imagePreview.src = e.target.result;
            
            // Initialize cropper when image loads
            imagePreview.onload = function() {
                initCropper(imageId);
            };
        };
        reader.readAsDataURL(file);
    }
    
    // Handle crop buttons
    document.getElementById('cropButton1').addEventListener('click', function() {
        applyCrop(1);
    });
    
    document.getElementById('cropButton2').addEventListener('click', function() {
        applyCrop(2);
    });
    
    // Zoom in button handlers
    document.querySelectorAll('.zoom-in').forEach(function(button) {
        button.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            if (croppers[targetId]) {
                croppers[targetId].zoom(0.1);
            }
        });
    });
    
    // Zoom out button handlers
    document.querySelectorAll('.zoom-out').forEach(function(button) {
        button.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            if (croppers[targetId]) {
                croppers[targetId].zoom(-0.1);
            }
        });
    });
    
    // Reset button handlers
    document.querySelectorAll('.reset').forEach(function(button) {
        button.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            if (croppers[targetId]) {
                croppers[targetId].reset();
            }
        });
    });
    
    // Form submission handler
    document.getElementById('profileForm').addEventListener('submit', function(event) {
        // Check if new files were uploaded but not cropped
        if ((fileUploaded[1] && !document.getElementById('croppedImageData1').value) || 
            (fileUploaded[2] && !document.getElementById('croppedImageData2').value)) {
            event.preventDefault();
            alert('Please crop your uploaded image(s) before submitting.');
            return false;
        }
        
        // Form will submit normally with existing images if no new uploads
        return true;
    });
</script>