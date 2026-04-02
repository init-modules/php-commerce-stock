<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commerce_warehouses', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('status', 32)->default('active');
            $table->boolean('is_default')->default(false);
            $table->foreignUuid('responsible_party_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('responsible_party_id');
        });

        Schema::create('commerce_stock_levels', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('warehouse_id');
            $table->uuid('catalog_item_id');
            $table->integer('on_hand_quantity')->default(0);
            $table->integer('allocated_quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['warehouse_id', 'catalog_item_id']);
            $table->index('catalog_item_id');
            $table->foreign('warehouse_id')->references('id')->on('commerce_warehouses')->cascadeOnDelete();
            $table->foreign('catalog_item_id')->references('id')->on('commerce_catalog_items')->cascadeOnDelete();
        });

        Schema::create('commerce_stock_movements', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('warehouse_id');
            $table->uuid('catalog_item_id');
            $table->string('type', 32);
            $table->integer('on_hand_delta')->default(0);
            $table->integer('allocated_delta')->default(0);
            $table->integer('on_hand_after')->default(0);
            $table->integer('allocated_after')->default(0);
            $table->integer('available_after')->default(0);
            $table->string('reference_type')->nullable();
            $table->string('reference_id')->nullable();
            $table->text('note')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['warehouse_id', 'catalog_item_id']);
            $table->index(['reference_type', 'reference_id']);
            $table->foreign('warehouse_id')->references('id')->on('commerce_warehouses')->cascadeOnDelete();
            $table->foreign('catalog_item_id')->references('id')->on('commerce_catalog_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commerce_stock_movements');
        Schema::dropIfExists('commerce_stock_levels');
        Schema::dropIfExists('commerce_warehouses');
    }
};
