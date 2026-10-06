<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingLedgerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ChartOfAccount $cashAccount;
    private ChartOfAccount $salesAccount;
    private ChartOfAccount $receivableAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Seed core CoA
        $this->cashAccount = ChartOfAccount::create([
            'account_code' => '1110',
            'account_name' => 'Cash on Hand',
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'is_system' => true,
            'is_active' => true,
        ]);

        $this->receivableAccount = ChartOfAccount::create([
            'account_code' => '1130',
            'account_name' => 'Accounts Receivable',
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'is_system' => true,
            'is_active' => true,
        ]);

        $this->salesAccount = ChartOfAccount::create([
            'account_code' => '4110',
            'account_name' => 'Sales Revenue',
            'account_type' => 'revenue',
            'normal_balance' => 'credit',
            'is_system' => true,
            'is_active' => true,
        ]);
    }

    public function test_post_balanced_journal_entry_creates_record_and_items(): void
    {
        $voucher = postJournalEntry([
            'entry_date' => date('Y-m-d'),
            'reference_type' => 'sale',
            'reference_id' => 101,
            'description' => 'Test Sales Journal',
            'status' => 'approved',
            'created_by' => $this->user->id,
            'items' => [
                [
                    'account_id' => $this->cashAccount->id,
                    'debit' => 5000.00,
                    'credit' => 0.00,
                    'description' => 'Cash received',
                ],
                [
                    'account_id' => $this->salesAccount->id,
                    'debit' => 0.00,
                    'credit' => 5000.00,
                    'description' => 'Sales revenue recorded',
                ]
            ]
        ]);

        $this->assertInstanceOf(JournalEntry::class, $voucher);
        $this->assertEquals(5000.00, (float) $voucher->total_debit);
        $this->assertEquals(5000.00, (float) $voucher->total_credit);
        $this->assertEquals('approved', $voucher->status);
        $this->assertCount(2, $voucher->items);

        // Assert balances
        $this->assertEquals(5000.00, getAccountBalance('1110'));
        $this->assertEquals(5000.00, getAccountBalance('4110'));
    }

    public function test_unbalanced_journal_entry_throws_domain_exception(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Unbalanced Journal Voucher');

        postJournalEntry([
            'entry_date' => date('Y-m-d'),
            'reference_type' => 'manual',
            'description' => 'Broken Voucher',
            'status' => 'approved',
            'items' => [
                [
                    'account_id' => $this->cashAccount->id,
                    'debit' => 5000.00,
                    'credit' => 0.00,
                ],
                [
                    'account_id' => $this->salesAccount->id,
                    'debit' => 0.00,
                    'credit' => 4500.00, // Unbalanced
                ]
            ]
        ]);
    }

    public function test_reverse_journal_entry_creates_storno_and_restores_balances(): void
    {
        $original = postJournalEntry([
            'entry_date' => date('Y-m-d'),
            'reference_type' => 'sale',
            'description' => 'Initial Sales Entry',
            'status' => 'approved',
            'created_by' => $this->user->id,
            'items' => [
                [
                    'account_id' => $this->cashAccount->id,
                    'debit' => 2500.00,
                    'credit' => 0.00,
                ],
                [
                    'account_id' => $this->salesAccount->id,
                    'debit' => 0.00,
                    'credit' => 2500.00,
                ]
            ]
        ]);

        $this->assertEquals(2500.00, getAccountBalance('1110'));

        $reversal = reverseJournalEntry($original->id, 'Customer cancellation');

        $this->assertInstanceOf(JournalEntry::class, $reversal);
        $original->refresh();
        $this->assertEquals('reversed', $original->status);
        $this->assertEquals($reversal->id, $original->reversed_entry_id);

        // Account balances should now net back to zero
        $this->assertEquals(0.00, getAccountBalance('1110'));
        $this->assertEquals(0.00, getAccountBalance('4110'));
    }

    public function test_cannot_reverse_already_reversed_entry(): void
    {
        $original = postJournalEntry([
            'entry_date' => date('Y-m-d'),
            'reference_type' => 'manual',
            'description' => 'Entry for double reversal test',
            'status' => 'approved',
            'created_by' => $this->user->id,
            'items' => [
                [
                    'account_id' => $this->cashAccount->id,
                    'debit' => 1000.00,
                    'credit' => 0.00,
                ],
                [
                    'account_id' => $this->salesAccount->id,
                    'debit' => 0.00,
                    'credit' => 1000.00,
                ]
            ]
        ]);

        reverseJournalEntry($original->id, 'First reversal');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('already reversed');

        reverseJournalEntry($original->id, 'Second reversal attempt');
    }
}
