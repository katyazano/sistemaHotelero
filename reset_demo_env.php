<?php
/**
 * Demo environment reset script.
 * Run with: php reset_demo_env.php
 * Or add to composer.json scripts: "demo:reset": ["@php reset_demo_env.php"]
 */

$root = __DIR__;

echo "=== Resetting demo environment ===\n";

// 1. Fresh migrations
echo "\n[1] Running fresh migrations...\n";
passthru("php \"" . $root . "/artisan\" migrate:fresh --force");

// 2. Seed demo data
echo "\n[2] Seeding demo data...\n";
passthru("php \"" . $root . "/artisan\" db:seed --class=DemoSeeder --force");

echo "\n=== Done! Demo environment ready. ===\n";
echo "  Admin:  admin@test.com  / admin123\n";
echo "  Guest1: guest1@test.com / guest123\n";
echo "  Guest2: guest2@test.com / guest123\n";
