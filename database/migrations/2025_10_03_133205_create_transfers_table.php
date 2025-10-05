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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id'); // liên kết đến tài sản
            $table->unsignedBigInteger('from_department_id')->nullable();
            $table->unsignedBigInteger('to_department_id')->nullable();
            $table->unsignedBigInteger('from_location_id')->nullable();
            $table->unsignedBigInteger('to_location_id')->nullable();
            $table->string('transferred_by')->nullable(); // ai chuyển
            $table->date('transfer_date')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')
                  ->references('id')->on('assets')
                  ->onDelete('cascade');

            $table->foreign('from_department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('to_department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('from_location_id')->references('id')->on('locations')->nullOnDelete();
            $table->foreign('to_location_id')->references('id')->on('locations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
