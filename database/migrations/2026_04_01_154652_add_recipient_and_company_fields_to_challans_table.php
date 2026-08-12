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
        Schema::table('challans', function (Blueprint $table) {
            // Add column if not exists
            if (!Schema::hasColumn('challans', 'recipient_organization')) {
                $table->string('recipient_organization')->nullable()->after('client_id');
            }
            if (!Schema::hasColumn('challans', 'recipient_designation')) {
                $table->string('recipient_designation')->nullable()->after('recipient_organization');
            }
            if (!Schema::hasColumn('challans', 'recipient_address')) {
                $table->text('recipient_address')->nullable()->after('recipient_designation');
            }
            if (!Schema::hasColumn('challans', 'attention_to')) {
                $table->string('attention_to')->nullable()->after('recipient_address');
            }
            if (!Schema::hasColumn('challans', 'subject')) {
                $table->string('subject')->nullable()->after('attention_to');
            }
            if (!Schema::hasColumn('challans', 'notes')) {
                $table->text('notes')->nullable()->after('subject');
            }
            if (!Schema::hasColumn('challans', 'company_name')) {
                $table->string('company_name')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('challans', 'signatory_name')) {
                $table->string('signatory_name')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('challans', 'signatory_designation')) {
                $table->string('signatory_designation')->nullable()->after('signatory_name');
            }
            if (!Schema::hasColumn('challans', 'company_phone')) {
                $table->string('company_phone')->nullable()->after('signatory_designation');
            }
            if (!Schema::hasColumn('challans', 'company_email')) {
                $table->string('company_email')->nullable()->after('company_phone');
            }
            if (!Schema::hasColumn('challans', 'company_website')) {
                $table->string('company_website')->nullable()->after('company_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challans', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_organization',
                'recipient_designation',
                'recipient_address',
                'attention_to',
                'subject',
                'notes',
                'company_name',
                'signatory_name',
                'signatory_designation',
                'company_phone',
                'company_email',
                'company_website',
            ]);
        });
    }
};
