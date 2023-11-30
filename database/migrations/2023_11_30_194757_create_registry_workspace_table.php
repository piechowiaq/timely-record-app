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
        Schema::create('registry_workspace', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registry_id');
            $table->unsignedBigInteger('workspace_id');
            $table->timestamps();

            $table->foreign('registry_id')->references('id')->on('registries')->onDelete('cascade');
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registry_workspace');
    }
};
