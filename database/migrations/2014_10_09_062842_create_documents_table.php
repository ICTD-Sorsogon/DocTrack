<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 120);
            $table->string('subject');
            $table->boolean('is_external')->default(false);
            $table->foreignId('document_type_id')->constrained('document_types');
            $table->string('originating_office')->nullable();
            $table->foreignId('current_office_id')->nullable()->constrained('offices');
            $table->string('sender_name')->nullable();
            $table->unsignedInteger('page_count');
            $table->dateTime('date_filed');
            $table->unsignedTinyInteger('is_terminal')->default(0);
            $table->string('remarks')->nullable();
            $table->unsignedInteger('attachment_page_count')->default(0);
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
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['current_office_id']);
            $table->dropForeign(['current_office_id']);
            $table->dropColumn('current_office_id');
            $table->dropIndex(['document_type_id']);
            $table->dropForeign(['document_type_id']);
            $table->dropColumn('document_type_id');
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('documents');
    }
}
