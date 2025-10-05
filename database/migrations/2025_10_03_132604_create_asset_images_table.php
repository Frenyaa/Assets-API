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
        Schema::create('asset_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id'); // liên kết đến bảng assets
            $table->string('path');                // đường dẫn file
            $table->string('filename');            // tên file gốc
            $table->timestamps();

            $table->foreign('asset_id')
                  ->references('id')
                  ->on('assets')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_images');
    }
};
