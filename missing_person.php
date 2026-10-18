<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\MissingPersonService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();

$missingService = new MissingPersonService();
$division = $_GET['division'] ?? 'All';
$status = $_GET['status'] ?? 'All';
$query = $_GET['query'] ?? '';

$filters = [
    'division' => $division,
    'status'   => $status,
    'query'    => $query,
    'limit'    => 50
];

$res = $missingService->getMissingPersons($filters);
$persons = $res['persons'];
$total = $res['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/logo.png">
    <title>Missing Persons Directory — Bangladesh Police Coordinated</title>
    <meta name="description" content="National Missing Persons Directory in Bangladesh. Report missing loved ones, check police GD alerts, and aid community sightings.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
      .missing-header {
        padding-top: 130px;
        padding-bottom: 30px;
      }
      .emergency-call-card {
        background: linear-gradient(135deg, rgba(244, 63, 94, 0.15), rgba(14, 18, 26, 0.9));
        border: 1px solid rgba(244, 63, 94, 0.3);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.5rem;
      }
      .person-card {
        border-radius: var(--radius-lg);
        background: var(--bg-surface);
        border: 1px solid var(--border-subtle);
        overflow: hidden;
        transition: transform var(--transition-smooth), border-color var(--transition-smooth);
      }
      .person-card:hover {
        transform: translateY(-4px);
        border-color: rgba(244, 63, 94, 0.35);
      }
    </style>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="container mx-auto px-3" style="max-width: 1320px;">
        <div class="missing-header">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <span class="badge badge-missing mb-2"><i class="bi bi-shield-fill-check me-1"></i> Coordinated with Bangladesh Police</span>
                    <h1 class="text-light fw-bold mb-1" style="font-size: 2.2rem;">National Missing Persons Directory</h1>
                    <p class="text-secondary mb-0">Help reunite missing children, elders, and vulnerable citizens across Bangladesh.</p>
                </div>
                <button type="button" class="btn btn-danger btn-pill px-4 py-3" onclick="openReportModal()">
                    <i class="bi bi-person-fill-plus me-1"></i> File Missing Person Report
                </button>
            </div>
        </div>

        <!-- Hotlines Banner -->
        <div class="emergency-call-card mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-telephone-outbound-fill text-rose fs-1"></i>
                <div>
                    <div class="fw-bold text-light">Immediate Emergency Response</div>
                    <small class="text-secondary">If someone has just gone missing or you have sighted a vulnerable child, dial immediately:</small>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="tel:999" class="btn btn-danger btn-sm px-3 py-2"><i class="bi bi-shield-fill-exclamation me-1"></i> Police: 999</a>
                <a href="tel:1098" class="btn btn-secondary btn-sm px-3 py-2"><i class="bi bi-people-fill me-1 text-emerald"></i> Child Helpline: 1098</a>
                <a href="tel:109" class="btn btn-secondary btn-sm px-3 py-2"><i class="bi bi-gender-female me-1 text-rose"></i> Helpline: 109</a>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="glass-panel p-3 mb-4 d-flex flex-wrap align-items-center gap-3">
            <div class="flex-grow-1" style="min-width: 240px;">
                <form action="missing_person.php" method="GET" class="d-flex gap-2">
                    <input type="text" class="form-control" name="query" value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search name, last seen location, clothes...">
                    <button type="submit" class="btn btn-primary px-3"><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="d-flex gap-2">
                <select class="form-select" onchange="location.href='missing_person.php?division=' + this.value;">
                    <option value="All" <?= $division === 'All' ? 'selected' : '' ?>>All Divisions</option>
                    <option value="Dhaka" <?= $division === 'Dhaka' ? 'selected' : '' ?>>Dhaka</option>
                    <option value="Chattogram" <?= $division === 'Chattogram' ? 'selected' : '' ?>>Chattogram</option>
                    <option value="Sylhet" <?= $division === 'Sylhet' ? 'selected' : '' ?>>Sylhet</option>
                    <option value="Rajshahi" <?= $division === 'Rajshahi' ? 'selected' : '' ?>>Rajshahi</option>
                    <option value="Khulna" <?= $division === 'Khulna' ? 'selected' : '' ?>>Khulna</option>
                    <option value="Barishal" <?= $division === 'Barishal' ? 'selected' : '' ?>>Barishal</option>
                    <option value="Rangpur" <?= $division === 'Rangpur' ? 'selected' : '' ?>>Rangpur</option>
                    <option value="Mymensingh" <?= $division === 'Mymensingh' ? 'selected' : '' ?>>Mymensingh</option>
                </select>
            </div>
        </div>

        <!-- Missing Person Cards Grid -->
        <?php if (!empty($persons)): ?>
            <div class="row g-4 mb-5">
                <?php foreach ($persons as $p): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="person-card h-100 d-flex flex-column">
                        <div style="height: 240px; overflow: hidden; position: relative; background: #000;">
                            <img src="<?= htmlspecialchars($p['photo'] ?: 'images/default_person.jpg', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($p['full_name'], ENT_QUOTES, 'UTF-8') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <span class="badge badge-missing position-absolute top-0 start-0 m-3">
                                <?= ucfirst($p['status']) ?>
                            </span>
                            <?php if (!empty($p['gd_number'])): ?>
                                <span class="badge bg-dark text-emerald position-absolute bottom-0 start-0 m-3" style="border: 1px solid var(--border-subtle);">
                                    <i class="bi bi-shield-check me-1"></i><?= htmlspecialchars($p['gd_number'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h3 class="fw-bold text-light mb-0" style="font-size: 1.3rem;"><?= htmlspecialchars($p['full_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <span class="badge bg-secondary-subtle text-light" style="font-size: 0.75rem;"><?= (int)$p['age'] ?> Years</span>
                            </div>

                            <div class="text-rose mb-3" style="font-size: 0.85rem;">
                                <i class="bi bi-geo-alt-fill me-1"></i>Last seen: <?= htmlspecialchars($p['last_seen_district'] . ', ' . $p['last_seen_division'], ENT_QUOTES, 'UTF-8') ?>
                            </div>

                            <div class="p-3 rounded glass-subtle small mb-3 flex-grow-1">
                                <div class="mb-1"><strong>Date:</strong> <?= htmlspecialchars($p['missing_date'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="mb-1"><strong>Clothing:</strong> <?= htmlspecialchars($p['clothing_description'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php if (!empty($p['distinguishing_features'])): ?>
                                    <div class="mb-1"><strong>Features:</strong> <?= htmlspecialchars($p['distinguishing_features'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                                <div><strong>Details:</strong> <?= htmlspecialchars($p['last_seen_details'], ENT_QUOTES, 'UTF-8') ?></div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="tel:<?= htmlspecialchars($p['contact_phone'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-danger btn-sm flex-grow-1 py-2">
                                    <i class="bi bi-telephone-fill me-1"></i> Call Family
                                </a>
                                <button type="button" class="btn btn-secondary btn-sm flex-grow-1 py-2" onclick="alert('Thank you. Sighting reports are shared with the investigating police officer.')">
                                    <i class="bi bi-eye-fill me-1"></i> Sighting Info
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="glass-panel text-center p-5 mb-5">
                <i class="bi bi-check-circle-fill text-emerald mb-3 d-block" style="font-size: 3rem;"></i>
                <h3 class="text-light fw-bold">No Missing Reports Found</h3>
                <p class="text-muted">There are no active missing person reports matching your filter criteria.</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- File Missing Person Modal -->
    <div class="cmd-palette-backdrop" id="reportMissingModal" onclick="closeReportModal(event)">
        <div class="cmd-palette-modal glass-panel p-4" onclick="event.stopPropagation()" style="max-width: 640px; max-height: 85vh; overflow-y: auto;">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-subtle">
                <h4 class="fw-bold text-light mb-0"><i class="bi bi-person-fill-plus text-rose me-2"></i> File Missing Person Report</h4>
                <button type="button" class="btn btn-ghost btn-sm text-muted" onclick="closeReportModal(null)">✕</button>
            </div>

            <form action="missing.php" method="POST" enctype="multipart/form-data">
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Full Name <span class="text-rose">*</span></label>
                        <input type="text" class="form-control" name="full_name" placeholder="Full name of person" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Age <span class="text-rose">*</span></label>
                        <input type="number" class="form-control" name="age" min="1" max="120" placeholder="e.g. 24" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Division <span class="text-rose">*</span></label>
                        <select class="form-select" name="last_seen_division" required>
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
                    <div class="col-md-6">
                        <label class="form-label">District / Thana <span class="text-rose">*</span></label>
                        <input type="text" class="form-control" name="last_seen_district" placeholder="e.g. Mirpur, Sadarghat, Agrabad" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Missing Date <span class="text-rose">*</span></label>
                        <input type="date" class="form-control" name="missing_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Police GD Number (If filed)</label>
                        <input type="text" class="form-control" name="gd_number" placeholder="e.g. GD-1024/Mirpur">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Seen Landmark / Location Details <span class="text-rose">*</span></label>
                    <input type="text" class="form-control" name="last_seen_details" placeholder="e.g. Leaving home for tuition, near railway station" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Clothing Description <span class="text-rose">*</span></label>
                    <textarea class="form-control" name="clothing_description" rows="2" placeholder="e.g. Blue denim shirt, black trousers, white sneakers, carrying red bag" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Distinguishing Features & Medical Conditions</label>
                    <textarea class="form-control" name="distinguishing_features" rows="2" placeholder="e.g. Glasses, birthmark on neck, speech impairment, dementia..."></textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Primary Contact Phone <span class="text-rose">*</span></label>
                        <input type="tel" class="form-control" name="contact_phone" placeholder="017XXXXXXXX" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Emergency Phone (Optional)</label>
                        <input type="tel" class="form-control" name="emergency_phone" placeholder="018XXXXXXXX">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Recent Photo of Missing Person (Recommended)</label>
                    <input type="file" class="form-control" name="photo" accept="image/*">
                </div>

                <button type="submit" class="btn btn-danger w-100 py-3">
                    <i class="bi bi-broadcast me-2"></i> Broadcast Missing Person Alert
                </button>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script>
        function openReportModal() {
            document.getElementById('reportMissingModal').classList.add('active');
        }
        function closeReportModal(e) {
            if (e && e.target !== document.getElementById('reportMissingModal')) return;
            document.getElementById('reportMissingModal').classList.remove('active');
        }
    </script>
</body>
</html>