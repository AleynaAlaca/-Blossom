<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->string('type')->default('task')->after('title');
            $table->string('category')->nullable()->after('type');
            $table->string('priority')->nullable()->after('category');
            $table->date('due_date')->nullable()->after('priority');
            $table->decimal('amount', 12, 2)->nullable()->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('todos', fn (Blueprint $table) => $table->dropColumn(['type', 'category', 'priority', 'due_date', 'amount']));
    }
};
