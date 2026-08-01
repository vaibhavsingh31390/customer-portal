<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_messages', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number', 50);
            $table->string('author_user_code', 20);
            $table->string('author_name', 255)->nullable();
            $table->string('author_role', 20);
            $table->text('body');
            $table->boolean('is_internal')->default(false);
            $table->string('message_type', 20)->default('comment');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('attachment_name', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('complaint_number')
                ->references('complaint_number')
                ->on('customer_complaints')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_messages');
    }
};
