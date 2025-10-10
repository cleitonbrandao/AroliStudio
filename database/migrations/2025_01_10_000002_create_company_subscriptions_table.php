<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('plan_type'); // 'basic', 'premium', 'enterprise'
            $table->integer('max_users')->default(5);
            $table->integer('max_companies')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_subscriptions');
    }
};
