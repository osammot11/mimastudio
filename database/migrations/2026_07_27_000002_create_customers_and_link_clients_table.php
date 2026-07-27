<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->timestamps();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->restrictOnDelete();
        });

        $customersByEmail = [];

        foreach (DB::table('clients')->orderBy('id')->get() as $client) {
            $email = $client->email ? strtolower(trim($client->email)) : null;
            $customerId = $email ? ($customersByEmail[$email] ?? null) : null;

            if (! $customerId) {
                $customerId = DB::table('customers')->insertGetId([
                    'name' => $client->name,
                    'email' => $email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($email) {
                    $customersByEmail[$email] = $customerId;
                }
            }

            DB::table('clients')
                ->where('id', $client->id)
                ->update(['customer_id' => $customerId]);
        }

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['email']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
        });

        foreach (DB::table('clients')->get() as $client) {
            DB::table('clients')
                ->where('id', $client->id)
                ->update([
                    'email' => DB::table('customers')
                        ->where('id', $client->customer_id)
                        ->value('email'),
                ]);
        }

        Schema::table('clients', function (Blueprint $table) {
            $table->index('email');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });

        Schema::dropIfExists('customers');
    }
};
