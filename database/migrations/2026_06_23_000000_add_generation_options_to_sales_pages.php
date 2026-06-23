<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_pages', function (Blueprint $table) {
            $table->string('tone')->nullable()->after('unique_selling_points');
            $table->string('color_scheme')->nullable()->after('tone');
            $table->text('image_url')->nullable()->after('color_scheme');
            $table->text('logo_url')->nullable()->after('image_url');
        });
    }

    public function down(): void
    {
        Schema::table('sales_pages', function (Blueprint $table) {
            $table->dropColumn(['tone', 'color_scheme', 'image_url', 'logo_url']);
        });
    }
};
