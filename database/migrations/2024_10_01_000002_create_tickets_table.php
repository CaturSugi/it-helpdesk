<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#6366f1');
            $table->string('icon')->default('fa-tag');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('subject');
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'pending', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();        // requester
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // agent
            $table->text('resolution_notes')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->boolean('is_internal')->default(false); // internal notes (agent only)
            $table->timestamps();
        });

        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->timestamps();
        });

        Schema::create('ticket_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action'); // created, status_changed, assigned, replied, etc.
            $table->text('description');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_activities');
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('categories');
    }
};
