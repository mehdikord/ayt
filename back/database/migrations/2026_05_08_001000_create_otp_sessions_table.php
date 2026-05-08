<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 11)->index();
            $table->string('otp_code_hash');
            $table->timestamp('expires_at')->index();
            $table->timestamp('resend_available_at');
            $table->unsignedInteger('attempt_count')->default(0);
            $table->unsignedInteger('max_attempts')->default(5);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('consumed_at')->nullable();
            $table->string('request_ip', 45)->nullable();
            $table->string('request_user_agent')->nullable();
            $table->timestamps();
            $table->index(['is_verified', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_sessions');
    }
};
