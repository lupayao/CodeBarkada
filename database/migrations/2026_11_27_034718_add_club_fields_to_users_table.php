<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the CodeBarkada club fields to the users table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // "adviser" or "member". New accounts are members unless we say otherwise.
            $table->string('role', 20)->default('member')->after('email');

            // School details (empty for the adviser).
            $table->string('grade', 10)->nullable()->after('role');
            $table->string('section', 50)->nullable()->after('grade');

            // Picked by the member at first sign-in.
            $table->string('codename', 50)->nullable()->unique()->after('section');
            $table->string('track', 50)->nullable()->after('codename');
            $table->string('avatar')->nullable()->after('track');

            // True when the account still has a default password that must be changed.
            $table->boolean('must_change_password')->default(true)->after('password');
        });
    }

    /**
     * Undo the changes above (used by "php artisan migrate:rollback").
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['codename']);
            $table->dropColumn(['role', 'grade', 'section', 'codename', 'track', 'avatar', 'must_change_password']);
        });
    }
};
