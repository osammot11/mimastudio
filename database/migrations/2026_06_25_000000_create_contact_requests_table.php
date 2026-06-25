<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 50);
            $table->string('project_type');
            $table->text('message');
            $table->date('wedding_date')->nullable();
            $table->time('wedding_time')->nullable();
            $table->string('ceremony_type')->nullable();
            $table->string('reception_location')->nullable();
            $table->unsignedSmallInteger('guest_count')->nullable();
            $table->string('request_type')->nullable();
            $table->json('additional_services')->nullable();
            $table->json('premium_services')->nullable();
            $table->text('wedding_story')->nullable();
            $table->string('referral_source')->nullable();
            $table->timestamp('privacy_accepted_at');
            $table->timestamp('viewed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_requests');
    }
};
