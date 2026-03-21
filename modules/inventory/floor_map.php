<?php
$page_title = 'Warehouse Floor Map';
require_once '../../includes/db.php';
$base_path = '../../';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_path . 'index.php');
    exit();
}

// Fetch all products with their locations
$products = [];
$res = $conn->query("SELECT name, stock_quantity, reorder_level, rack_location, bin_location FROM products");
while($row = $res->fetch_assoc()) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | Warehouse Pro</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .map-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-top: 30px;
        }
        .rack {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
        }
        .bin {
            width: 100%;
            height: 40px;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 8px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            transition: 0.3s;
        }
        .bin:hover {
            background: rgba(99, 102, 241, 0.2);
            transform: scale(1.05);
        }
        .bin.occupied {
            background: #10b981;
            color: white;
        }
        .bin.low {
            background: #f59e0b;
            color: white;
            animation: pulse-warn 2s infinite;
        }
        .bin.empty {
            background: #f43f5e;
            color: white;
        }
        @keyframes pulse-warn {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include $base_path . 'includes/sidebar.php'; ?>
        <?php include $base_path . 'includes/top_nav.php'; ?>

        <main class="main-content">
            <header class="mb-4">
                <h1 style="font-size: 24px; font-weight: 700;">Live Warehouse Floor Map</h1>
                <p style="color: var(--text-muted);">Visual layout of racks and digital bin tracking.</p>
            </header>

            <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px;">
                    <div style="width: 12px; height: 12px; background: #10b981; border-radius: 3px;"></div> Optimal Stock
                </div>
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px;">
                    <div style="width: 12px; height: 12px; background: #f59e0b; border-radius: 3px;"></div> Reorder Warning
                </div>
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px;">
                    <div style="width: 12px; height: 12px; background: #f43f5e; border-radius: 3px;"></div> Critical/Out
                </div>
            </div>

            <div class="map-grid">
                <?php 
                // Fetch distinct racks from products table
                $rackQuery = $conn->query("SELECT DISTINCT rack_location FROM products WHERE rack_location IS NOT NULL AND rack_location != '' ORDER BY rack_location ASC");
                $racksFound = [];
                while($rq = $rackQuery->fetch_assoc()) {
                    $racksFound[] = $rq['rack_location'];
                }
                
                // Fallback if no racks defined yet
                if (empty($racksFound)) {
                    $racksFound = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
                }

                foreach($racksFound as $r): 
                ?>
                <div class="rack">
                    <h4 style="margin: 0 0 15px; color: var(--primary);">Rack <?php echo htmlspecialchars($r); ?></h4>
                    <?php 
                    for($i=1; $i<=4; $i++): 
                        $bin = str_pad($i, 3, '0', STR_PAD_LEFT);
                        $targetRack = $r;
                        $prod = array_values(array_filter($products, function($p) use ($targetRack, $bin) {
                            return strtoupper($p['rack_location']) == strtoupper($targetRack) && $p['bin_location'] == $bin;
                        }));
                        
                        $class = '';
                        $title = "Empty Bin: $bin";
                        if (!empty($prod)) {
                            $p = $prod[0];
                            if ($p['stock_quantity'] <= 0) $class = 'empty';
                            elseif ($p['stock_quantity'] <= $p['reorder_level']) $class = 'low';
                            else $class = 'occupied';
                            $title = $p['name'] . " (" . $p['stock_quantity'] . ")";
                        }
                    ?>
                    <div class="bin <?php echo $class; ?>" title="<?php echo $title; ?>">
                        <?php echo $bin; ?>
                    </div>
                    <?php endfor; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</body>
</html>
