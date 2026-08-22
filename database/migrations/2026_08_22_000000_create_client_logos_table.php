<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_logos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image_path')->unique();
            $table->string('original_name')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();

        DB::table('customers')
            ->whereNotNull('logo_path')
            ->where('logo_path', '!=', '')
            ->orderBy('name')
            ->get(['name', 'logo_path'])
            ->each(function (object $customer, int $index) use ($now): void {
                DB::table('client_logos')->insertOrIgnore([
                    'name' => $customer->name,
                    'image_path' => $customer->logo_path,
                    'original_name' => basename($customer->logo_path),
                    'sort_order' => $index,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_logos');
    }
};
