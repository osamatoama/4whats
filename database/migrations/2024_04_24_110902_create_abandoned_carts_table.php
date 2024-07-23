<?php

use App\Models\Contact;
use App\Models\Store;
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
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Store::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Contact::class)->constrained()->restrictOnDelete();
            $table->string('provider_type');
            $table->string('provider_id');
            $table->datetime('provider_created_at')->nullable();
            $table->datetime('provider_updated_at')->nullable();
            $table->unsignedInteger('total_amount');
            $table->string('total_currency');
            $table->string('checkout_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};
