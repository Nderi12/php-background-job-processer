<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackgroundJobsTable extends Migration
{
    public function up()
    {
        Schema::create('background_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('class');
            $table->integer('priority')->default(1); // higher numbers = higher priority
            $table->string('method');
            $table->text('parameters')->nullable(); // Serialized parameters
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->integer('retry_attempts')->default(0);
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('failed_background_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('class');
            $table->string('method');
            $table->text('parameters')->nullable();
            $table->text('error_message'); // Error message or stack trace
            $table->integer('retry_attempts')->default(0);
            $table->timestamp('failed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('background_jobs');
        Schema::dropIfExists('failed_background_jobs');
    }
}
