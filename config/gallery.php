<?php

return [
    'max_files' => 1000,
    'max_file_kilobytes' => 4096,
    'batch_files' => 8,
    'batch_bytes' => 32 * 1024 * 1024,
    'expires_days' => 7,
    'min_free_bytes' => (int) env('GALLERY_MIN_FREE_SPACE_MB', 5120) * 1024 * 1024,
    'page_size' => 24,
    'admin_page_size' => 50,
    'thumbnail_width' => 900,
    'thumbnail_quality' => 78,
    'max_pixels' => 30_000_000,
];
