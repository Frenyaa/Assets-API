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
       Schema::create('assets', function (Blueprint $table) {
        $table->id();
        $table->string('asset_name');              // tên tài sản
        $table->string('asset_sn')->nullable();    // số serial
        $table->unsignedBigInteger('asset_group_id')->nullable();
        $table->unsignedBigInteger('department_id')->nullable();
        $table->unsignedBigInteger('location_id')->nullable();
        $table->string('accountable_party')->nullable(); 
        $table->text('description')->nullable();
        $table->date('warranty_date')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
