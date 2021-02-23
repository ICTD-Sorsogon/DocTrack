<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_summaries', function (Blueprint $table) {
            $table->id();
            $table->enum('action', ['created','received','acknowledged','forwarded']);

            //document id
            $table->foreignId('document_id')->constrained('documents');
            //office id ng nag trigger ng action
            $table->foreignId('office_id')->constrained('offices');
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
        Schema::dropIfExists('tracking_summaries');
    }
}
