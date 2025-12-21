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
$apify_api_key = getSetting('apify_api_key', '');
$google_place_id = getSetting('google_place_id', 'CTFqEnDtDuxAEAE');
$update_method = getSetting('review_update_method', 'outscraper'); // outscraper, apify, or sheets

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

} elseif ($update_method === 'apify' && !empty($apify_api_key)) {
    writeLog("Using Apify API method");

    // Apify API - Run Google Maps Reviews Scraper
    $actor_id = 'compass/google-maps-reviews-scraper';
    $run_url = "https://api.apify.com/v2/acts/{$actor_id}/runs?token={$apify_api_key}";

    // Input for the actor
    $input = [
        'startUrls' => [
            ['url' => "https://www.google.com/maps/place/?q=place_id:{$google_place_id}"]
        ],
        'maxReviews' => 150,
        'language' => 'ar',
        'sortBy' => 'newest'
    ];

    // Start the actor run
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $run_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 201) {
        $run_data = json_decode($response, true);
        $run_id = $run_data['data']['id'];
        $dataset_id = $run_data['data']['defaultDatasetId'];

        writeLog("Apify actor started with run ID: {$run_id}");

        // Wait for the run to complete (poll every 10 seconds, max 2 minutes)
        $max_wait = 120; // 2 minutes
        $wait_interval = 10;
        $waited = 0;
        $status = 'RUNNING';

        while ($waited < $max_wait && $status === 'RUNNING') {
            sleep($wait_interval);
            $waited += $wait_interval;

            $status_url = "https://api.apify.com/v2/acts/{$actor_id}/runs/{$run_id}?token={$apify_api_key}";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $status_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $status_response = curl_exec($ch);
            curl_close($ch);

            $status_data = json_decode($status_response, true);
            $status = $status_data['data']['status'] ?? 'RUNNING';

            writeLog("Actor status: {$status} (waited {$waited}s)");
        }

        if ($status === 'SUCCEEDED') {
            // Fetch the dataset results
            $dataset_url = "https://api.apify.com/v2/datasets/{$dataset_id}/items?token={$apify_api_key}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $dataset_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $results = curl_exec($ch);
            curl_close($ch);

            $reviews = json_decode($results, true);

            if (is_array($reviews)) {
                $imported = 0;
                $skipped = 0;

                foreach ($reviews as $item) {
                    if (isset($item['reviews'])) {
                        foreach ($item['reviews'] as $review) {
                            $rating = $review['stars'] ?? 0;

                            if ($rating < 3) {
                                $skipped++;
                                continue;
                            }

                            $review_id = $review['reviewId'] ?? md5($review['name'] . $review['text']);

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

                            $text = !empty($review['text']) ? $review['text'] : 'تقييم ممتاز';
                            $photo = $review['reviewerPhotoUrl'] ?? '';
                            $created = isset($review['publishedAtDate']) ? date('Y-m-d H:i:s', strtotime($review['publishedAtDate'])) : date('Y-m-d H:i:s');

                            if ($stmt->execute([
                                $review['name'],
                                $rating,
                                $text,
                                $review_id,
                                $photo,
                                $created
                            ])) {
                                $imported++;
                            }
                        }
                    }
                }

                writeLog("Successfully imported {$imported} new reviews from Apify (skipped {$skipped})");
            } else {
                writeLog("ERROR: Invalid dataset response from Apify");
            }
        } else {
            writeLog("ERROR: Apify actor failed or timed out. Final status: {$status}");
        }
    } else {
        writeLog("ERROR: Apify API returned HTTP {$http_code}");
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
