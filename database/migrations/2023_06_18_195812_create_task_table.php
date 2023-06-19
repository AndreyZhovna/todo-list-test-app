<?php

use App\Domain\Auth\Entities\User;
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
        Schema::create('task', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->uuid('parent_id')->nullable();
            $table->foreignIdFor(User::class);
            $table->string('status');
            $table->smallInteger('priority');
            $table->dateTime('created_at');
            $table->dateTime('completed_at')->nullable();

            $table->fullText('title');
        });

        Schema::table('task',function (Blueprint $table){
            $table->foreign('parent_id')
                ->references('id')
                ->on('task');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task');
    }
};
