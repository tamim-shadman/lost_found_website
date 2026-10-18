<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\ItemService;

$itemService = new ItemService();

if (isset($_POST['query']) || isset($_GET['query'])) {
    $search = trim($_POST['query'] ?? $_GET['query'] ?? '');
    
    if ($search === '') {
        echo "<p class='list-group-item text-muted py-2 px-3 border-0'>Type to search items...</p>";
        exit();
    }

    $res = $itemService->getItems(['query' => $search, 'limit' => 6]);
    $items = $res['items'];

    if (!empty($items)) {
        foreach ($items as $row) {
            $typeClass = $row['item_type'] === 'lost' ? 'badge-lost' : 'badge-found';
            $typeLabel = ucfirst($row['item_type']);
            $title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
            $loc = htmlspecialchars($row['district'] . ', ' . $row['division'], ENT_QUOTES, 'UTF-8');
            $id = (int)$row['id'];
            
            echo "<a href='#' class='list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 search-result-item' data-id='{$id}' onclick='selectSearchResult({$id}, \"{$title}\"); return false;'>";
            echo "  <div>";
            echo "    <div class='fw-semibold text-light search-item-title'>{$title}</div>";
            echo "    <small class='text-muted'><i class='bi bi-geo-alt me-1'></i>{$loc}</small>";
            echo "  </div>";
            echo "  <span class='badge {$typeClass}'>{$typeLabel}</span>";
            echo "</a>";
        }
    } else {
        echo "<p class='list-group-item text-muted py-2 px-3 border-0'>No matching reports found</p>";
    }
}