<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents');
            $table->unsignedTinyInteger('actions');
            $table->unsignedTinyInteger('status');
            $table->foreignId('approved_by')->nullable()->constrained('personnels');
            $table->foreignId('touched_by')->nullable()->constrained('users');
            $table->dateTime('last_touched');
            $table->foreignId('forwarded_by')->nullable()->constrained('offices');
            $table->foreignId('forwarded_to')->nullable()->constrained('offices');
            $table->string('remarks')->nullable();
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
        Schema::table('tracking_records', function (Blueprint $table) {
            $table->dropIndex(['document_id']);
            $table->dropForeign(['document_id']);
            $table->dropColumn('document_id');
            $table->dropIndex(['approved_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');
            $table->dropIndex(['touched_by']);
            $table->dropForeign(['touched_by']);
            $table->dropColumn('touched_by');
            $table->dropIndex(['forwarded_by']);
            $table->dropForeign(['forwarded_by']);
            $table->dropColumn('forwarded_by');
            $table->dropIndex(['forwarded_to']);
            $table->dropForeign(['forwarded_to']);
            $table->dropColumn('forwarded_to');
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('tracking_records');
    }
}
