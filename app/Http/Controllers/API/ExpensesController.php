<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpensesController extends Controller
{
    /**
     * Add a new expense category for a specified institution.
     *
     * Expected request parameters:
     * - institution_id: required, must exist in the institutions table.
     * - category_name: required, string.
     * - description: optional, string.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addExpenseCategory(Request $request)
    {
        // Validate the incoming request.
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,id',
            'category_name'  => 'required|string|max:255',
            'description'    => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Log::error('Expense category validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the expense category already exists for this institution.
            $exists = DB::table('expense_categories')
                ->where('institution_id', $request->institution_id)
                ->where('category_name', $request->category_name)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Expense category already exists for this institution.',
                ], 409); // Conflict
            }

            // Insert the expense category record into the database.
            DB::table('expense_categories')->insert([
                'institution_id' => $request->institution_id,
                'category_name'  => $request->category_name,
                'description'    => $request->description,
                'status'         => 'Active',
                'created_at'     => now()->format('Y-m-d H:i:s'),
                'updated_at'     => now()->format('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Expense category added successfully.',
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error adding expense category.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while adding the expense category.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View expense categories for a given institution.
     *
     * Expected request parameter:
     * - institution_id: required, must exist in the institutions table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewExpenseCategories(Request $request)
    {
        // Validate that the institution id is provided and exists.
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,id',
        ]);

        if ($validator->fails()) {
            Log::error('Expense categories view validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Retrieve expense categories for the provided institution.
            $expenseCategories = DB::table('expense_categories')
                ->where('institution_id', $request->institution_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Expense categories retrieved successfully.',
                'data'    => $expenseCategories,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving expense categories.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching expense categories.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Edit an existing expense category.
     *
     * Expected request parameters:
     * - category_name: required, string.
     * - description: optional, string.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id Expense category record ID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function editExpenseCategory(Request $request, $id)
    {
        // Validate the incoming request.
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            Log::error('Expense category edit validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Check if the expense category exists.
            $expenseCategory = DB::table('expense_categories')->where('id', $id)->first();
            if (!$expenseCategory) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Expense category not found.',
                ], 404);
            }

            // Update the expense category.
            DB::table('expense_categories')
                ->where('id', $id)
                ->update([
                    'category_name' => $request->category_name,
                    'description'   => $request->description,
                    'updated_at'    => now()->format('Y-m-d H:i:s'),
                ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Expense category updated successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error updating expense category.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while updating the expense category.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle the status of an expense category.
     * This function automatically toggles the status:
     * If the current status is "Active", it becomes "Inactive", and vice versa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id Expense category record ID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleExpenseCategory(Request $request, $id)
    {
        try {
            // Retrieve the expense category.
            $expenseCategory = DB::table('expense_categories')->where('id', $id)->first();
            if (!$expenseCategory) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Expense category not found.',
                ], 404);
            }

            // Determine the new status.
            $newStatus = $expenseCategory->status === 'Active' ? 'Inactive' : 'Active';

            // Update the status.
            DB::table('expense_categories')
                ->where('id', $id)
                ->update([
                    'status'     => $newStatus,
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ]);

            return response()->json([
                'status'     => 'success',
                'message'    => "Expense category status updated to {$newStatus} successfully.",
                'new_status' => $newStatus,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error toggling expense category status.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while toggling expense category status.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
     /**
     * Add a new expense record.
     *
     * Expected request parameters:
     * - institution_id: required, must exist in the institutions table.
     * - title: required, string.
     * - category: required, string.
     * - amount: required, numeric.
     * - invoice_number: optional, string.
     * - expense_date: required, date.
     * - attachment: optional, file (stored in assets/expense_attachment).
     * - note: optional, string.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addExpense(Request $request)
    {
        // Validate incoming request.
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,id',
            'title'          => 'required|string|max:255',
            'category'       => 'required|string|max:255',
            'amount'         => 'required|numeric',
            'invoice_number' => 'nullable|string|max:100',
            'expense_date'   => 'required|date',
            'note'           => 'nullable|string',
            'attachment'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            Log::error('Expense validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                // Generate a unique file name.
                $uniqueFileName = uniqid('expense_') . '.' . $file->getClientOriginalExtension();
                // Save file to public/assets/expense_attachment directory.
                $destinationPath = public_path('assets/expense_attachment');
                $file->move($destinationPath, $uniqueFileName);
                $attachmentPath = 'assets/expense_attachment/' . $uniqueFileName;
            }

            // Insert expense record into expenses table.
            $expenseId = DB::table('expenses')->insertGetId([
                'institution_id' => $request->institution_id,
                'title'          => $request->title,
                'category'       => $request->category,
                'amount'         => $request->amount,
                'invoice_number' => $request->invoice_number,
                'expense_date'   => $request->expense_date,
                'attachment'     => $attachmentPath,
                'note'           => $request->note,
                'created_at'     => now()->format('Y-m-d H:i:s'),
                'updated_at'     => now()->format('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'status'     => 'success',
                'message'    => 'Expense added successfully.',
                'expense_id' => $expenseId,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error adding expense.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while adding the expense.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * View expenses for a given institution.
     *
     * Expected request parameter:
     * - institution_id: required, must exist in the institutions table.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewExpenses(Request $request)
    {
        // Validate that institution_id is provided.
        $validator = Validator::make($request->all(), [
            'institution_id' => 'required|string|exists:institutions,id'
        ]);

        if ($validator->fails()) {
            Log::error('View expenses validation failed.', $validator->errors()->toArray());
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $expenses = DB::table('expenses')
                ->where('institution_id', $request->institution_id)
                ->orderBy('expense_date', 'desc')
                ->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Expenses retrieved successfully.',
                'data'    => $expenses
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving expenses.', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while fetching expenses.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
