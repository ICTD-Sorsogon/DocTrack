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
            $table->string('originating_office')->nullable()->contrained('offices');
            $table->foreignId('destination_office_id')->nullable()->constrained('offices');
            $table->enum('status', ['received', 'forwarded', 'processing', 'on hold', 'rejected', 'terminated', 'acknowledged']);
            $table->string('sender_name')->nullable();
            $table->unsignedInteger('page_count');
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
            $table->dropIndex(['destination_office_id']);
            $table->dropForeign(['destination_office_id']);
            $table->dropColumn('destination_office_id');
            $table->dropIndex(['document_type_id']);
            $table->dropForeign(['document_type_id']);
            $table->dropColumn('document_type_id');
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('documents');
    }
}
