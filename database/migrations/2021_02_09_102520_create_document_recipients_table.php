<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_recipients', function (Blueprint $table) {
            $table->id('recipient_id');
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade')->nullable();
            $table->integer('destination_office');
            $table->dateTime('acknowledged')->nullable();
            $table->dateTime('received')->nullable();
            $table->dateTime('forwarded')->nullable();
            $table->dateTime('rejected')->nullable();
            $table->dateTime('hold')->nullable();
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
        Schema::dropIfExists('document_recipients');
    }
}
