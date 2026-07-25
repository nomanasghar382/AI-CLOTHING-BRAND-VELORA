<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('velora:scheduler-run exchange-rates')->dailyAt('01:00');
Schedule::command('velora:scheduler-run trend-forecasts')->dailyAt('02:00');
Schedule::command('velora:scheduler-run analytics-snapshot')->hourly();
Schedule::command('velora:scheduler-run inventory-scan')->everySixHours();
Schedule::command('velora:scheduler-run low-stock-alerts')->hourly();
Schedule::command('velora:scheduler-run expired-coupons')->dailyAt('00:30');
Schedule::command('velora:scheduler-run price-alerts')->everyThirtyMinutes();
Schedule::command('velora:scheduler-run restock-alerts')->everyThirtyMinutes();
Schedule::command('velora:scheduler-run temp-cleanup')->dailyAt('03:30');
Schedule::command('velora:scheduler-run database-cleanup')->dailyAt('04:00');
Schedule::command('velora:scheduler-run cache-warmup')->everyTenMinutes();
Schedule::command('velora:scheduler-run recommendation-refresh')->dailyAt('05:00');
Schedule::command('velora:scheduler-run database-backup')->dailyAt('02:30');
Schedule::command('velora:scheduler-run config-backup')->weeklyOn(1, '03:00');
Schedule::command('velora:scheduler-run orphan-files')->weeklyOn(7, '04:30');
