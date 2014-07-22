<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryAlterRolesFix extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('roles')){
            Schema::table('roles', function($table)
            {
                $table->dropColumn('created_at');
                $table->dropColumn('updated_at');
            });

            Schema::table('roles', function($table)
            {
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }

}


