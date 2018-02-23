<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class Billing extends RequestAbstract
{
    /**
     * Get All Invoices
     *
     * Gets all invoices, optionally filtered by additional parameters.
     *
     * @param $query array An array of GET parameters to filter the request.
     * @param $query['type'] string The invoice type (all, completed, active). Defaults to active.
     * @param $query['projectStatus'] string The projects to query based on status (all, archived, active), defaults to active.
     * @param $query['page'] int The page number to get results for. See headers for page information.
     *
     * @see https://developer.teamwork.com/billing#get_all_invoices_
     */
    public function allInvoices($query = null)
    {
        return $this->apiClient->get('invoices.json', $query)->invoices;
    }

    /**
     * Get All Invoices for Project
     *
     * Gets all invoices for a project when given a valid project ID.
     *
     * @param $projectId int The project ID.
     * @param $query array An array of GET parameters to filter the request.
     * @param $query['type'] string The invoice type (all, completed, active). Defaults to active.
     * @param $query['page'] int The page number to get results for. See headers for page information.
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     *
     * @see https://developer.teamwork.com/billing#get_all_invoices_
     */
    public function allInvoicesForProject($projectId, $query = null)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when getting an invoice.');

        return $this->apiClient->get(sprintf('projects/%s/invoices.json', $projectId), $query)->invoices;
    }

    /**
     * Get Invoice
     *
     * Get a single invoice when given invoice ID.
     *
     * @param $query array An array of GET parameters to filter the request.
     * @param $query['type'] string The invoice type (all, completed, active). Defaults to active.
     * @param $query['projectStatus'] string The projects to query based on status (all, archived, active), defaults to active.
     * @param $query['page'] int The page number to get results for. See headers for page information.
     *
     * @see https://developer.teamwork.com/invoices#get_a_single_invo
     */
    public function getInvoice($invoiceId)
    {
        $this->assertValidResourceId($invoiceId, 'You must specify a valid invoice ID when getting an invoice by ID.');

        return $this->apiClient->get(sprintf('invoices/%s.json', $invoiceId))->invoice;
    }

    /**
     * Create New Invoice
     *
     * Creates a new invoice. On success, returns an object with the invoice ID.
     *
     * @param $params array An array of parameters for the new invoice.
     *
     * @see https://developer.teamwork.com/invoices#create_a_new_invo
     */
    public function createInvoice($params)
    {
        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($params, [
            'project-id'   => 'required',
            'display-date' => 'required',
            'number'       => 'required'
        ], [
            'project-id.required'   => '`project-id` is a required field when creating new invoice.',
            'display-date.required' => '`display-date` is a required field when creating new invoice.',
            'number.required'       => '`number` is a required field when creating new invoice.'
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post('invoices.json', [
            'invoice' => $params
        ]);
    }

    /**
     * Update Invoice
     *
     * Updates an invoice when given a valid invoice ID and invoice attributes to update.
     *
     * @param $params array An array of parameters for the invoice
     *
     * @see https://developer.teamwork.com/invoices#update_a_specific
     */
    public function updateInvoice($invoiceId, $params)
    {
        $this->assertValidResourceId($invoiceId, 'You must specify a valid invoice ID when updating an invoice.');

        return $this->apiClient->put(sprintf('invoices/%s.json', $invoiceId), [
            'invoice' => $params
        ]);
    }

    /**
     * Delete Invoice
     *
     * Deletes an invoice when given a valid invoice ID.
     *
     * @param $invoiceId int The invoice ID.
     *
     * @see https://developer.teamwork.com/invoices#delete_a_specific
     */
    public function deleteInvoice($invoiceId)
    {
        $this->assertValidResourceId($invoiceId, 'You must specify a valid invoice ID when deleting an invoice.');

        return $this->apiClient->delete(sprintf('invoices/%s.json', $invoiceId));
    }

    /**
     * Mark Invoice Complete
     *
     * Marks an invoice as completed when given invoice ID.
     *
     * @param $invoiceId int The invoice ID.
     *
     * @see https://developer.teamwork.com/invoices#mark_a_specific_i
     *
     * @return object
     * @throws InvalidRequest
     */
    public function markInvoiceComplete($invoiceId)
    {
        $this->assertValidResourceId($invoiceId, 'You must specify a valid invoice ID when marking invoice complete.');

        return $this->apiClient->put(sprintf('invoices/%s/complete.json', $invoiceId));
    }

    /**
     * Mark Invoice Uncomplete
     *
     * Marks an invoice as uncompleted when given invoice ID.
     *
     * @param $invoiceId int The invoice ID.
     *
     * @see https://developer.teamwork.com/invoices#mark_a_specific_i
     *
     * @return object
     * @throws InvalidRequest
     */
    public function markInvoiceUncomplete($invoiceId)
    {
        $this->assertValidResourceId($invoiceId, 'You must specify a valid invoice ID when marking invoice uncomplete.');

        return $this->apiClient->put(sprintf('invoices/%s/uncomplete.json', $invoiceId));
    }

    /**
     * Get Currency Codes
     *
     * Gets a list of all currency codes and country names from Teamwork's dictionary.
     *
     * @see https://developer.teamwork.com/invoices#get_a_list_of_val
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function getCurrencyCodes()
    {
        return $this->apiClient->get('/currencycodes.json')->{'currency-codes'};
    }

    /**
     * Get All Expenses
     *
     * Gets all expenses across all projects.
     *
     * @see https://developer.teamwork.com/account#get_all_expenses_
     */
    public function allExpenses($query = null)
    {
        return $this->apiClient->get('expenses.json', $query)->expenses;
    }

    /**
     * Get All Expenses for Project
     *
     * Gets all expenses for a project when given a valid expense ID.
     *
     * @param $projectId int The project ID.
     *
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     *
     * @see https://developer.teamwork.com/expenses#get_all_expenses_
     */
    public function allExpensesForProject($projectId, $query = null)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when getting getting expenses by project.');

        return $this->apiClient->get(sprintf('projects/%s/expenses.json', $projectId), $query)->expenses;
    }

    /**
     * Get Expense
     *
     * Get a single expense when given expense ID.
     *
     * @see https://developer.teamwork.com/account#get_a_single_expe
     */
    public function getExpense($expenseId)
    {
        $this->assertValidResourceId($expenseId, 'You must specify a valid expense ID when getting an expense.');

        return $this->apiClient->get(sprintf('expenses/%s.json', $expenseId))->expense;
    }

    /**
     * Create New Expense
     *
     * Creates a new expense. On success, returns an object with the expense ID.
     *
     * @var $params array An array of parameters for the new expense.
     *
     * @see https://developer.teamwork.com/projectsapi#create_a_new_expe
     */
    public function createExpense($params)
    {
        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($params, [
            'project-id' => 'required',
            'date'       => 'required',
            'name'       => 'required',
            'cost'       => 'required'
        ], [
            'project-id.required' => '`project-id` is a required field when creating new expense.',
            'date.required'       => '`date` is a required field when creating new expense.',
            'name.required'       => '`name` is a required field when creating new expense.',
            'cost.required'       => '`cost` is a required field when creating new expense.'
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post('expenses.json', [
            'expense' => $params
        ]);
    }

    /**
     * Update Expense
     *
     * Updates an expense when given a valid expense ID and expense attributes to update.
     *
     * @var $params array An array of parameters for the expense.
     *
     * @see https://developer.teamwork.com/expenses#update_a_single_e
     */
    public function updateExpense($expenseId, $params)
    {
        $this->assertValidResourceId($expenseId, 'You must specify a valid expense ID when updating an expense.');

        return $this->apiClient->put(sprintf('expenses/%s.json', $expenseId), [
            'expense' => $params
        ]);
    }

    /**
     * Delete Expense
     *
     * Deletes an expense when given a valid expense ID.
     *
     * @param $expenseId int The expense ID.
     *
     * @see https://developer.teamwork.com/expenses#delete_a_single_e
     */
    public function deleteExpense($expenseId)
    {
        $this->assertValidResourceId($expenseId, 'You must specify a valid expense ID when deleting an expense.');

        return $this->apiClient->delete(sprintf('expenses/%s.json', $expenseId));
    }
}