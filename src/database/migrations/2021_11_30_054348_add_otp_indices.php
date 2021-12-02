<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddOtpIndices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('otp', function (Blueprint $table) {
            $table->index(['entity_id', 'module'], 'otp_entity_id_module_index');
            $table->index('expires_on');
        });

        Schema::table('otp_blacklist', function (Blueprint $table) {
            $table->index(['entity_id', 'module'], 'otp_blacklist_entity_id_module_index');
        });

        Schema::table('otp_attempts', function (Blueprint $table) {
            $table->index(['entity_id', 'module'], 'otp_attempts_entity_id_module_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('otp', function (Blueprint $table) {
            $table->dropIndex(['expires_on']);
            $table->dropIndex('otp_entity_id_module_index');
        });

        Schema::table('otp_blacklist', function (Blueprint $table) {
            $table->dropIndex('otp_blacklist_entity_id_module_index');
        });

        Schema::table('otp_attempts', function (Blueprint $table) {
            $table->dropIndex('otp_attempts_entity_id_module_index');
        });
    }
}
