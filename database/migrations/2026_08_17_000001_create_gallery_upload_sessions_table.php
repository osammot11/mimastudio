<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_upload_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('manifest_hash', 64);
            $table->unsignedInteger('expected_files');
            $table->unsignedInteger('uploaded_files')->default(0);
            $table->unsignedBigInteger('expected_bytes');
            $table->unsignedBigInteger('uploaded_bytes')->default(0);
            $table->string('status')->default('active');
            $table->boolean('notification_requested')->default(false);
            $table->timestamp('notification_sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['client_id', 'manifest_hash', 'status'], 'gallery_session_manifest_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_upload_sessions');
    }
};
