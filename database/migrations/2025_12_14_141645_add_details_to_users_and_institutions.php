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
        Schema::table('users', function (Blueprint $table) {
                $table->string('whatsapp')->nullable(); // Removed ->after('status') because status doesn't exist yet
        });

        Schema::table('institutions', function (Blueprint $table) {
            if (!Schema::hasColumn('institutions', 'address')) {
                $table->text('address')->nullable()->after('name');
            }
            if (!Schema::hasColumn('institutions', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('institutions', 'type')) {
                $table->string('type')->nullable()->after('city');
            }
            if (!Schema::hasColumn('institutions', 'subdomain')) {
                $table->string('subdomain')->unique()->nullable()->after('type'); // url
            }
            if (!Schema::hasColumn('institutions', 'affiliate_code')) {
                $table->string('affiliate_code')->nullable()->after('subdomain');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('whatsapp');
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['address', 'city', 'type', 'subdomain', 'affiliate_code']);
        });
    }
};
