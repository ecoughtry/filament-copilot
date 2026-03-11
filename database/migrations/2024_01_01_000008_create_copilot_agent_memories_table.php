<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('copilot_agent_memories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->morphs('participant');
            $table->string('panel_id')->index();
            $table->nullableMorphs('tenant');
            $table->string('key')->index();
            $table->text('value');
            $table->timestamps();

            $table->unique(
                ['participant_type', 'participant_id', 'panel_id', 'tenant_type', 'tenant_id', 'key'],
                'copilot_memory_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('copilot_agent_memories');
    }
};
