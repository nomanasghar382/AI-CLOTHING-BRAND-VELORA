<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category', 40)->index();
            $table->string('action', 80)->index();
            $table->nullableMorphs('subject');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_id', 64)->nullable()->index();
            $table->timestamps();
            $table->index(['category', 'created_at']);
        });

        Schema::create('security_login_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->index();
            $table->string('event', 40)->index();
            $table->boolean('successful')->default(false)->index();
            $table->boolean('suspicious')->default(false)->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('device_fingerprint', 64)->nullable()->index();
            $table->string('location', 120)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('device_fingerprint', 64)->index();
            $table->string('device_name')->nullable();
            $table->string('platform', 60)->nullable();
            $table->string('browser', 60)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('trusted')->default(false)->index();
            $table->boolean('remembered')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'device_fingerprint']);
        });

        Schema::create('password_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('password_hash');
            $table->timestamp('rotated_at');
            $table->timestamps();
            $table->index(['user_id', 'rotated_at']);
        });

        Schema::create('application_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric', 80)->index();
            $table->string('group', 40)->index();
            $table->decimal('value', 16, 4);
            $table->json('tags')->nullable();
            $table->timestamp('recorded_at')->index();
            $table->timestamps();
        });

        Schema::create('backup_runs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40)->index();
            $table->string('status', 20)->index();
            $table->string('disk', 40)->default('local');
            $table->string('path')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedSmallInteger('retention_days')->default(14);
            $table->text('message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('enabled')->default(false)->index();
            $table->json('rules')->nullable();
            $table->timestamps();
        });

        Schema::create('incoming_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 40)->index();
            $table->string('event_id')->nullable()->index();
            $table->string('event_type', 80)->nullable()->index();
            $table->string('signature')->nullable();
            $table->boolean('verified')->default(false);
            $table->string('status', 20)->default('received')->index();
            $table->json('payload');
            $table->text('error')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('scheduled_task_runs', function (Blueprint $table) {
            $table->id();
            $table->string('command', 120)->index();
            $table->string('status', 20)->index();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->text('output')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('file_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('disk', 40)->default('cloudinary');
            $table->string('path')->index();
            $table->string('public_id')->nullable()->index();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('checksum', 64)->nullable()->index();
            $table->json('metadata')->nullable();
            $table->boolean('orphaned')->default(false)->index();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'file_assets',
            'scheduled_task_runs',
            'incoming_webhook_events',
            'feature_flags',
            'backup_runs',
            'application_metrics',
            'password_histories',
            'user_devices',
            'security_login_events',
            'audit_logs',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
