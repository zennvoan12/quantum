<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Skema mengikuti Tabel 3.6-3.15 dokumen TA (Laporan_TA_Farhan_REVISI_TERSTRUKTUR.docx).
// ponytail: apriori_logs/frequent_itemsets/association_rules masih kosong — diisi saat
// fitur admin "Proses Apriori" dibuat; saat ini aturan dihitung on-the-fly di PageController.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnRestrict();
            $table->string('name');
            $table->unsignedInteger('price'); // Rupiah, tanpa sen
            $table->unsignedInteger('stock')->default(0);
            $table->string('image')->nullable();
            $table->string('status', 50)->default('aktif');
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_no', 100)->unique();
            $table->unsignedInteger('total')->default(0);
            $table->string('status', 50)->default('pending');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnRestrict();
            $table->unsignedInteger('qty')->default(1);
            $table->unsignedInteger('price'); // rekam harga satuan saat transaksi
            $table->timestamps();

            $table->unique(['order_id', 'product_id']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method', 100);
            $table->string('payment_status', 50)->default('pending');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->unique('order_id'); // 1:1 dengan orders, sesuai ERD
        });

        Schema::create('apriori_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('run_at');
            $table->float('min_support');
            $table->float('min_confidence');
            $table->unsignedInteger('total_rules')->default(0);
            $table->timestamps();
        });

        Schema::create('frequent_itemsets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apriori_log_id')->constrained()->cascadeOnDelete();
            $table->text('items');
            $table->float('support');
            $table->timestamps();
        });

        Schema::create('association_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apriori_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id_a')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_id_b')->constrained('products')->cascadeOnDelete();
            $table->float('support');
            $table->float('confidence');
            $table->timestamps();

            $table->unique(['apriori_log_id', 'product_id_a', 'product_id_b'], 'assoc_rules_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('association_rules');
        Schema::dropIfExists('frequent_itemsets');
        Schema::dropIfExists('apriori_logs');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
