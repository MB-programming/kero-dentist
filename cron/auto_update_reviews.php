#!/usr/bin/env php
<?php
/**
 * Auto Update Google Reviews - Cron Job
 * Run every 3 days to fetch latest reviews
 *
 * Add to crontab:
 * 0 2 * * */3 /usr/bin/php /home/user/kero-dentist/cron/auto_update_reviews.php
 */

require_once __DIR__ . '/../includes/config.php';

// Log file
$log_file = __DIR__ . '/reviews_update.log';

function writeLog($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[{$timestamp}] {$message}\n", FILE_APPEND);
}

writeLog("=== Starting auto-update reviews ===");

// Get settings
$outscraper_api_key = getSetting('outscraper_api_key', '');
$google_place_id = getSetting('google_place_id', 'CTFqEnDtDuxAEAE');
$update_method = getSetting('review_update_method', 'outscraper'); // outscraper or sheets

if ($update_method === 'outscraper' && !empty($outscraper_api_key)) {
    writeLog("Using Outscraper API method");

    // Outscraper API endpoint
    $url = "https://api.app.outscraper.com/maps/reviews-v3";

    $params = http_build_query([
        'query' => "https://www.google.com/maps/place/?q=place_id:{$google_place_id}",
        'reviewsLimit' => 150,
        'language' => 'ar',
        'sort' => 'newest',
        'async' => false
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-API-KEY: ' . $outscraper_api_key
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $data = json_decode($response, true);

        if (isset($data['data'][0]['reviews_data'])) {
            $reviews = $data['data'][0]['reviews_data'];
            $imported = 0;
            $skipped = 0;

            foreach ($reviews as $review) {
                // Only 3-5 stars
                if ($review['review_rating'] < 3) {
                    $skipped++;
                    continue;
                }

                // Check if exists
                $review_id = $review['review_id'] ?? $review['review_timestamp'];
                $stmt = $pdo->prepare("SELECT id FROM reviews WHERE google_review_id = ?");
                $stmt->execute([$review_id]);

                if ($stmt->fetch()) {
                    $skipped++;
                    continue;
                }

                // Insert
                $stmt = $pdo->prepare("
                    INSERT INTO reviews (
                        client_name, rating, review_text, source,
                        is_approved, is_displayed, google_review_id, google_author_photo,
                        created_at
                    ) VALUES (?, ?, ?, 'google', 1, 1, ?, ?, ?)
                ");

                $text = !empty($review['review_text']) ? $review['review_text'] : 'تقييم ممتاز';
                $photo = $review['author_image'] ?? '';
                $created = date('Y-m-d H:i:s', $review['review_timestamp']);

                if ($stmt->execute([
                    $review['author_title'],
                    $review['review_rating'],
                    $text,
                    $review_id,
                    $photo,
                    $created
                ])) {
                    $imported++;
                }
            }

            writeLog("Successfully imported {$imported} new reviews (skipped {$skipped})");
        } else {
            writeLog("ERROR: No reviews data in response");
        }
    } else {
        writeLog("ERROR: Outscraper API returned HTTP {$http_code}");
    }

} elseif ($update_method === 'sheets') {
    writeLog("Using Google Sheets method");

    // Google Sheets CSV URL (public sheet)
    $sheet_url = getSetting('reviews_sheet_csv_url', '');

    if (!empty($sheet_url)) {
        $csv_content = file_get_contents($sheet_url);

        if ($csv_content !== false) {
            $lines = explode("\n", $csv_content);
            $imported = 0;
            $skipped = 0;

            // Skip header
            array_shift($lines);

            foreach ($lines as $line) {
                $row = str_getcsv($line);
                if (count($row) < 2) continue;

                $name = $row[0] ?? '';
                $rating = intval($row[1] ?? 0);
                $text = $row[2] ?? 'تقييم ممتاز';
                $photo = $row[3] ?? '';
                $date = $row[4] ?? date('Y-m-d H:i:s');

                if (empty($name) || $rating < 3) {
                    $skipped++;
                    continue;
                }

                $review_id = md5($name . $text . $date);

                $stmt = $pdo->prepare("SELECT id FROM reviews WHERE google_review_id = ?");
                $stmt->execute([$review_id]);

                if ($stmt->fetch()) {
                    $skipped++;
                    continue;
                }

                $stmt = $pdo->prepare("
                    INSERT INTO reviews (
                        client_name, rating, review_text, source,
                        is_approved, is_displayed, google_review_id, google_author_photo,
                        created_at
                    ) VALUES (?, ?, ?, 'google', 1, 1, ?, ?, ?)
                ");

                if ($stmt->execute([$name, $rating, $text, $review_id, $photo, $date])) {
                    $imported++;
                }
            }

            writeLog("Successfully imported {$imported} new reviews from Sheets (skipped {$skipped})");
        } else {
            writeLog("ERROR: Failed to fetch Google Sheets CSV");
        }
    } else {
        writeLog("ERROR: No Google Sheets URL configured");
    }

} else {
    writeLog("ERROR: No update method configured or API key missing");
}

writeLog("=== Finished auto-update reviews ===\n");

echo "Review update completed. Check log: {$log_file}\n";
?>
