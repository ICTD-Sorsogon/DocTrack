<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('permission_id')->constrained('permissions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_records', function (Blueprint $table) {
            $table->dropIndex(['role_id']);
            $table->dropForeign(['role_id']);
            $table->dropIndex(['permission_id']);
            $table->dropForeign(['permission_id']);
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('role_permissions');
    }
}
