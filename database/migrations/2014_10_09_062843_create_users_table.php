<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles');
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('first_name', 80);
            $table->string('middle_name', 80);
            $table->string('last_name', 80);
            $table->string('suffix', 20)->nullable();
            $table->unsignedTinyInteger('gender');
            $table->date('birthday');
            $table->string('id_number', 50);
            $table->foreignId('office_id')->constrained('offices');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role_id']);
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropIndex(['office_id']);
            $table->dropForeign(['office_id']);
            $table->dropColumn('office_id');
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('users');
    }
}
