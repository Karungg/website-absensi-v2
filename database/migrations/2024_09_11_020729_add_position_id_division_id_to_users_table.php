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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('position_id')->after('remember_token')->nullable()->constrained();
            $table->foreignUuid('division_id')->after('position_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropForeign(['division_id']);
            $table->dropColumn('position_id');
            $table->dropColumn('division_id');
        });
    }
};