<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_images', function (Blueprint $table) {
            $table->string('thumbnail_path')->nullable()->after('image_path');
            $table->string('original_name')->nullable()->after('alt_text');
            $table->unsignedBigInteger('byte_size')->nullable()->after('original_name');
            $table->unsignedInteger('width')->nullable()->after('byte_size');
            $table->unsignedInteger('height')->nullable()->after('width');
        });
    }

    public function down(): void
    {
        Schema::table('client_images', function (Blueprint $table) {
            $table->dropColumn([
                'thumbnail_path',
                'original_name',
                'byte_size',
                'width',
                'height',
            ]);
        });
    }
};
