<?php

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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'role')) {
                    $table->string('role')->after('email');
                }

                if (! Schema::hasColumn('users', 'phone')) {
                    $table->string('phone')->after('role');
                }

                if (! Schema::hasColumn('users', 'speciality')) {
                    $table->string('speciality')->after('phone');
                }

                if (! Schema::hasColumn('users', 'status')) {
                    $table->string('status')->default('active')->after('speciality');
                }
            });

            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role');
            $table->string('phone');
            $table->string('speciality');
            $table->string('password');
            $table->string('status')->default('active');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['role', 'phone', 'speciality', 'status']);
            });
        }
    }
};
