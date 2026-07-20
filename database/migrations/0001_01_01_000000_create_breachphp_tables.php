<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('breachphp_prefixes', function (Blueprint $table) {
            $table->id();
            $table->char('prefix', 5)->unique();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index('prefix');
        });

        Schema::create('breachphp_suffixes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefix_id')->constrained('breachphp_prefixes')->cascadeOnDelete();
            $table->char('suffix', 35);
            $table->unsignedBigInteger('count')->default(0);
            $table->timestamps();

            $table->index('prefix_id');
            $table->unique(['prefix_id', 'suffix']);
        });

        Schema::create('breachphp_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->char('prefix', 5);
            $table->string('status');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('duration')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index('prefix');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breachphp_sync_logs');
        Schema::dropIfExists('breachphp_suffixes');
        Schema::dropIfExists('breachphp_prefixes');
    }
};
