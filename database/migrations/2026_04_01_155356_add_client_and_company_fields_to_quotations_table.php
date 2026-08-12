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
        Schema::table('quotations', function (Blueprint $table) {
            // Add client details fields if not exist
            if (!Schema::hasColumn('quotations', 'client_name')) {
                $table->string('client_name')->nullable()->after('client_id');
            }
            if (!Schema::hasColumn('quotations', 'client_designation')) {
                $table->string('client_designation')->nullable()->after('client_name');
            }
            if (!Schema::hasColumn('quotations', 'client_address')) {
                $table->text('client_address')->nullable()->after('client_designation');
            }
            if (!Schema::hasColumn('quotations', 'client_phone')) {
                $table->string('client_phone')->nullable()->after('client_address');
            }
            if (!Schema::hasColumn('quotations', 'client_email')) {
                $table->string('client_email')->nullable()->after('client_phone');
            }
            if (!Schema::hasColumn('quotations', 'attention_to')) {
                $table->string('attention_to')->nullable()->after('client_email');
            }
            // Add body/content fields
            if (!Schema::hasColumn('quotations', 'body_content')) {
                $table->text('body_content')->nullable()->after('attention_to');
            }
            if (!Schema::hasColumn('quotations', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->after('body_content');
            }
            if (!Schema::hasColumn('quotations', 'subject')) {
                $table->string('subject')->nullable()->after('terms_conditions');
            }
            // Add company details fields
            if (!Schema::hasColumn('quotations', 'company_name')) {
                $table->string('company_name')->nullable()->after('subject');
            }
            if (!Schema::hasColumn('quotations', 'signatory_name')) {
                $table->string('signatory_name')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('quotations', 'signatory_designation')) {
                $table->string('signatory_designation')->nullable()->after('signatory_name');
            }
            if (!Schema::hasColumn('quotations', 'company_phone')) {
                $table->string('company_phone')->nullable()->after('signatory_designation');
            }
            if (!Schema::hasColumn('quotations', 'company_email')) {
                $table->string('company_email')->nullable()->after('company_phone');
            }
            if (!Schema::hasColumn('quotations', 'company_website')) {
                $table->string('company_website')->nullable()->after('company_email');
            }
            if (!Schema::hasColumn('quotations', 'additional_enclosed')) {
                $table->text('additional_enclosed')->nullable()->after('company_website');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'client_name',
                'client_designation',
                'client_address',
                'client_phone',
                'client_email',
                'attention_to',
                'body_content',
                'terms_conditions',
                'subject',
                'company_name',
                'signatory_name',
                'signatory_designation',
                'company_phone',
                'company_email',
                'company_website',
                'additional_enclosed',
            ]);
        });
    }
};
