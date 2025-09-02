<?php
global $wpdb;

$scheduled_posts = $wpdb->get_results("
    SELECT p.ID, p.post_date
    FROM {$wpdb->posts} p
    WHERE p.post_status = 'future'
    AND p.post_type = 'post'
    ORDER BY p.post_date ASC
");

$category_data = [];
foreach ($scheduled_posts as $post) {
    $categories = wp_get_post_categories($post->ID);
    foreach ($categories as $cat_id) {
        if (!isset($category_data[$cat_id])) {
            $category_data[$cat_id] = ['count' => 0, 'last_date' => null];
        }
        $category_data[$cat_id]['count']++;
        $post_time = strtotime($post->post_date);
        if (!$category_data[$cat_id]['last_date'] || $post_time > strtotime($category_data[$cat_id]['last_date'])) {
            $category_data[$cat_id]['last_date'] = $post->post_date;
        }
    }
}

$all_categories = get_categories(['hide_empty' => false]);
$results = [];
foreach ($all_categories as $cat) {
    $count     = $category_data[$cat->term_id]['count'] ?? 0;
    $last_date = $category_data[$cat->term_id]['last_date'] ?? null;
    $results[] = [
        'name'      => $cat->name,
        'count'     => $count,
        'last_date' => $last_date,
    ];
}

usort($results, function($a, $b) {
    return $a['count'] <=> $b['count'];
});

$total = array_sum(array_column($results, 'count'));
?>

<div class="wrap bootstrap-wrapper">
    <h1 class="mb-4">Schedule Report</h1>
    <p>Total artikel schedule: <strong><?php echo $total; ?></strong></p>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Kategori</th>
                <th>Jumlah Artikel</th>
                <th>Status</th>
                <th>Habis pada</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
                <?php
                    if ($row['count'] == 0) {
                        $status = '<span class="badge bg-danger">Kosong</span>';
                    } elseif ($row['count'] < 10) {
                        $status = '<span class="badge bg-warning text-dark">Sisa Sedikit</span>';
                    } else {
                        $status = '<span class="badge bg-success">Aman</span>';
                    }
                    $last_pub = $row['last_date'] ? date('Y-m-d H:i', strtotime($row['last_date'])) : '-';
                ?>
                <tr>
                    <td><?php echo esc_html($row['name']); ?></td>
                    <td><?php echo $row['count']; ?></td>
                    <td><?php echo $status; ?></td>
                    <td><?php echo $last_pub; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
