<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->text('work_description');
            $table->date('work_date');
            $table->string('identifier_code')->nullable()->unique();
            $table->string('email');
            $table->text('gallery_url');
            $table->timestamp('sent_at')->nullable();
            $table->text('last_send_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_deliveries');
    }
};
