<?php

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
        Schema::create('langs', function (Blueprint $table) {
            $table->collation = 'utf8mb4_bin';
            $table->id()->startingValue(1000);
            $table->string('namespace')->index()->default('*');
            $table->string('group')->index();
            $table->string('key')->index();
            $table->jsonb('text')->fulltext();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(['namespace', 'group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('langs');
    }
};
