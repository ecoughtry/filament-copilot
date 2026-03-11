<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('copilot_plans', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('conversation_id')
                ->constrained('copilot_conversations')
                ->cascadeOnDelete();
            $table->foreignUlid('message_id')
                ->nullable()
                ->constrained('copilot_messages')
                ->nullOnDelete();
            $table->longText('plan_content');
            $table->json('steps')->nullable();
            $table->string('status')->default('proposed');
            $table->unsignedInteger('current_step')->default(0);
            $table->timestamps();

            $table->index(['conversation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('copilot_plans');
    }
};
