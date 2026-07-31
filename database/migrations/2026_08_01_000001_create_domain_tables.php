<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->string('client_code', 20)->primary();
            $table->string('name', 255);
            $table->string('erp_vertical', 20)->default('TERMS');
            $table->string('email', 255)->nullable();
        });

        Schema::create('engineers', function (Blueprint $table) {
            $table->string('engineer_code', 20)->primary();
            $table->string('name', 255);
            $table->string('working_status', 5)->default('WK');
            $table->string('department', 10)->default('SWE');
        });

        Schema::create('sap_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('department_module', 20)->default('TERMS');
        });

        Schema::create('portal_users', function (Blueprint $table) {
            $table->string('user_code', 20)->primary();
            $table->string('username', 100)->unique();
            $table->string('email', 255)->nullable();
            $table->string('password', 255);
            $table->string('status', 1)->default('Y');
        });

        Schema::create('customer_complaints', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('complaint_number', 50)->unique();
            $table->date('complaint_date');
            $table->string('client_code', 20);
            $table->string('module', 100)->nullable();
            $table->string('complaint_type', 50)->nullable();
            $table->string('error_type', 10)->nullable();
            $table->text('problem_description')->nullable();
            $table->string('priority', 5)->nullable();
            $table->date('target_date')->nullable();
            $table->string('status', 5)->default('PN');
            $table->text('internal_remarks')->nullable();
            $table->text('reason')->nullable();
            $table->text('action_taken')->nullable();
            $table->string('file_name', 255)->nullable();
            $table->string('change_type', 50)->nullable();
            $table->string('update_standard', 50)->nullable();
            $table->string('changed_by', 100)->nullable();
            $table->date('closed_date')->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->string('verified_by', 100)->nullable();
            $table->date('verified_at')->nullable();
            $table->string('change_verified_by', 100)->nullable();
            $table->string('time_taken', 50)->nullable();
            $table->date('verification_sent_at')->nullable();
            $table->string('assigned_to', 20)->nullable();
            $table->string('attachment_name', 255)->nullable();
            $table->decimal('rating', 8, 2)->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->string('email_send_status', 5)->nullable();

            $table->foreign('client_code')->references('client_code')->on('clients');
            $table->foreign('assigned_to')->references('engineer_code')->on('engineers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_complaints');
        Schema::dropIfExists('portal_users');
        Schema::dropIfExists('sap_modules');
        Schema::dropIfExists('engineers');
        Schema::dropIfExists('clients');
    }
};
