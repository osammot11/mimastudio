<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_upload_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_upload_session_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('fingerprint', 64);
            $table->string('original_name');
            $table->unsignedBigInteger('byte_size');
            $table->unsignedInteger('position');
            $table->string('status')->default('pending');
            $table->string('staged_path')->nullable();
            $table->string('staged_thumbnail_path')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->timestamps();

            $table->unique(['gallery_upload_session_id', 'fingerprint'], 'gallery_item_fingerprint_unique');
            $table->unique(['gallery_upload_session_id', 'position'], 'gallery_item_position_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_upload_items');
    }
};
