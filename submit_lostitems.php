<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();

$defaultType = isset($_GET['type']) && in_array($_GET['type'], ['lost', 'found']) ? $_GET['type'] : 'lost';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/logo.png">
    <title>Report Lost or Found Belongings — Lost & Found BD</title>
    <meta name="description" content="Report a lost item or submit details about a found belonging in Bangladesh. Fast, secure, and coordinated.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
      .report-page-header {
        padding-top: 130px;
        padding-bottom: 30px;
      }
      .type-selector-tab {
        flex: 1;
        padding: 1rem;
        text-align: center;
        background: var(--bg-surface);
        border: 2px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        cursor: pointer;
        transition: all var(--transition-smooth);
        font-weight: 700;
        color: var(--text-secondary);
      }
      .type-selector-tab.active-lost {
        background: rgba(244, 63, 94, 0.12);
        border-color: var(--color-rose-500);
        color: var(--color-rose-400);
        box-shadow: 0 4px 18px var(--color-rose-glow);
      }
      .type-selector-tab.active-found {
        background: rgba(16, 185, 129, 0.12);
        border-color: var(--color-emerald-500);
        color: var(--color-emerald-400);
        box-shadow: 0 4px 18px var(--color-emerald-glow);
      }
      .upload-dropzone {
        border: 2px dashed var(--border-medium);
        border-radius: var(--radius-lg);
        padding: 2rem 1.5rem;
        text-align: center;
        background: rgba(255, 255, 255, 0.02);
        cursor: pointer;
        transition: all var(--transition-fast);
      }
      .upload-dropzone:hover {
        border-color: var(--color-emerald-500);
        background: rgba(16, 185, 129, 0.04);
      }
      .preview-image-box {
        width: 100%;
        max-height: 220px;
        border-radius: var(--radius-md);
        object-fit: cover;
        display: none;
      }
    </style>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="container mx-auto px-3" style="max-width: 860px;">
        <div class="report-page-header text-center">
            <span class="brand-badge mb-2">Publish An Official Alert</span>
            <h1 class="text-light fw-bold mb-2" style="font-size: 2.2rem;">Lost & Found Report Wizard</h1>
            <p class="text-secondary" style="font-size: 1rem;">Complete the form below with precise details to trigger instant community search matching.</p>
        </div>

        <div class="glass-panel p-4 p-md-5 mb-5 animate-slide-up">
            <!-- Mode Switch Tabs -->
            <div class="d-flex gap-3 mb-4">
                <div class="type-selector-tab <?= $defaultType === 'lost' ? 'active-lost' : '' ?>" id="tabLost" onclick="switchType('lost')">
                    <i class="bi bi-search fs-4 d-block mb-1"></i>
                    <span>I Lost Something</span>
                    <small class="d-block fw-normal text-muted" style="font-size: 0.75rem;">Seek help finding your property</small>
                </div>
                <div class="type-selector-tab <?= $defaultType === 'found' ? 'active-found' : '' ?>" id="tabFound" onclick="switchType('found')">
                    <i class="bi bi-box-seam fs-4 d-block mb-1"></i>
                    <span>I Found Something</span>
                    <small class="d-block fw-normal text-muted" style="font-size: 0.75rem;">Help return items to owner</small>
                </div>
            </div>

            <!-- Report Submission Form -->
            <form action="lost_item.php" method="POST" enctype="multipart/form-data" id="itemReportForm">
                <input type="hidden" name="item_type" id="formItemType" value="<?= htmlspecialchars($defaultType, ENT_QUOTES, 'UTF-8') ?>">

                <!-- Item Title & Category -->
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label for="itemTitleInput" class="form-label">Item Name / Title <span class="text-rose">*</span></label>
                        <input type="text" class="form-control" id="itemTitleInput" name="item_name" placeholder="e.g. Dell XPS 15, Leather Wallet, Passport" required>
                    </div>
                    <div class="col-md-4">
                        <label for="itemCategorySelect" class="form-label">Category <span class="text-rose">*</span></label>
                        <select class="form-select" id="itemCategorySelect" name="category" required>
                            <option value="Electronics" selected>Electronics & Gadgets</option>
                            <option value="Wallet/Cards">Wallet, Cards & Money</option>
                            <option value="Documents">ID, Passport & Documents</option>
                            <option value="Bags">Bags, Backpacks & Luggage</option>
                            <option value="Jewelry">Jewelry & Watches</option>
                            <option value="Pets">Pets & Animals</option>
                            <option value="Keys">Keys & Access Cards</option>
                            <option value="Vehicles">Bicycle, Motorbike or Car</option>
                            <option value="Other">Other Miscellaneous</option>
                        </select>
                    </div>
                </div>

                <!-- Division, District & Date -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="divisionSelect" class="form-label">Division (Bangladesh) <span class="text-rose">*</span></label>
                        <select class="form-select" id="divisionSelect" name="state" required onchange="updateDistrictOptions(this.value)">
                            <option value="Dhaka" selected>Dhaka</option>
                            <option value="Chattogram">Chattogram</option>
                            <option value="Sylhet">Sylhet</option>
                            <option value="Rajshahi">Rajshahi</option>
                            <option value="Khulna">Khulna</option>
                            <option value="Barishal">Barishal</option>
                            <option value="Rangpur">Rangpur</option>
                            <option value="Mymensingh">Mymensingh</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="districtSelect" class="form-label">District / City <span class="text-rose">*</span></label>
                        <input type="text" class="form-control" id="districtSelect" name="city" placeholder="e.g. Mirpur, Gulshan, Agrabad" required>
                    </div>
                    <div class="col-md-4">
                        <label for="incidentDateInput" class="form-label">Date Lost or Found <span class="text-rose">*</span></label>
                        <input type="date" class="form-control" id="incidentDateInput" name="date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <!-- Specific Location & Surroundings -->
                <div class="mb-3">
                    <label for="specificAddressInput" class="form-label">Specific Location / Landmark <span class="text-rose">*</span></label>
                    <input type="text" class="form-control" id="specificAddressInput" name="address" placeholder="e.g. Hazrat Shahjalal International Airport, Terminal 2 Waiting Lounge" required>
                </div>

                <div class="mb-3">
                    <label for="surroundingsInput" class="form-label">Detailed Description & Distinctive Markers</label>
                    <textarea class="form-control" id="surroundingsInput" name="address2" rows="3" placeholder="Provide scratches, stickers, serial prefixes, case color, or surrounding circumstances..."></textarea>
                </div>

                <!-- Optional Reward (for lost items) -->
                <div class="mb-3" id="rewardSection">
                    <label for="rewardInput" class="form-label">Community Reward Offer (Optional BDT)</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-gift icon text-amber"></i>
                        <input type="number" class="form-control" id="rewardInput" name="reward" placeholder="e.g. 2000" min="0" step="100">
                    </div>
                    <small class="form-text text-muted">Offering a reward motivates citizen responders and courier returns.</small>
                </div>

                <!-- Picture Upload with Drag & Drop and Instant Live Preview -->
                <div class="mb-4">
                    <label class="form-label">Attach Item Photo (Recommended)</label>
                    <div class="upload-dropzone" onclick="document.getElementById('itemPhotoInput').click()">
                        <i class="bi bi-cloud-arrow-up text-emerald fs-1 mb-2 d-block"></i>
                        <div class="fw-semibold text-light mb-1">Click to select or drag photo here</div>
                        <small class="text-muted d-block mb-3">Supports JPG, PNG, WEBP up to 8MB</small>
                        <img id="imagePreviewElement" class="preview-image-box mx-auto" alt="Item Preview">
                    </div>
                    <input type="file" id="itemPhotoInput" name="image" accept="image/*" style="display: none;" onchange="previewSelectedImage(this)">
                </div>

                <!-- Contact Details -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="contactPhoneInput" class="form-label">Contact Phone Number <span class="text-rose">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-telephone icon"></i>
                            <input type="tel" class="form-control" id="contactPhoneInput" name="phone" value="<?= htmlspecialchars($currentUser['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="017XXXXXXXX" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="contactEmailInput" class="form-label">Contact Email Address <span class="text-rose">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-envelope icon"></i>
                            <input type="email" class="form-control" id="contactEmailInput" name="email" value="<?= htmlspecialchars($currentUser['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="user@domain.com" required>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-block w-100 py-3" id="submitReportBtn">
                    <i class="bi bi-send-fill me-2"></i>
                    <span id="submitBtnText">Publish Report Alert</span>
                </button>
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script>
        function switchType(type) {
            const tabLost = document.getElementById('tabLost');
            const tabFound = document.getElementById('tabFound');
            const hiddenType = document.getElementById('formItemType');
            const form = document.getElementById('itemReportForm');
            const rewardSection = document.getElementById('rewardSection');
            const submitBtn = document.getElementById('submitReportBtn');
            const submitBtnText = document.getElementById('submitBtnText');

            hiddenType.value = type;

            if (type === 'lost') {
                tabLost.className = 'type-selector-tab active-lost';
                tabFound.className = 'type-selector-tab';
                form.action = 'lost_item.php';
                rewardSection.style.display = 'block';
                submitBtn.className = 'btn btn-danger btn-block w-100 py-3';
                submitBtnText.textContent = 'Publish Lost Item Alert';
            } else {
                tabFound.className = 'type-selector-tab active-found';
                tabLost.className = 'type-selector-tab';
                form.action = 'found_item.php';
                rewardSection.style.display = 'none';
                submitBtn.className = 'btn btn-primary btn-block w-100 py-3';
                submitBtnText.textContent = 'Submit Found Item Notice';
            }
        }

        // Initialize mode based on defaultType
        <?php if ($defaultType === 'found'): ?>
            switchType('found');
        <?php endif; ?>

        function previewSelectedImage(input) {
            const preview = document.getElementById('imagePreviewElement');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>