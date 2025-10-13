<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('mail_logs')) {
            return;
        }

        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('direction')->default('outgoing');
            $table->string('status')->default('sent');
            $table->string('subject');
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->nullableMorphs('related', 'mail_related_index');
            $table->json('context')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
