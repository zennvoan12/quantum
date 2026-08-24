<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('association_rules', function (Blueprint $table) {
            $table->float('lift')->default(0)->after('confidence');
        });
    }

    public function down(): void
    {
        Schema::table('association_rules', function (Blueprint $table) {
            $table->dropColumn('lift');
        });
    }
};