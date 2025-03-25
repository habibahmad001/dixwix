<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Customer;
use App\Models\User; // Assuming you have a User model

class QuickBooksController extends Controller
{
    public function addAccount(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'number' => 'required|string|min:16|max:16',
            'exp_month' => 'required|string|min:2|max:2',
            'exp_year' => 'required|string|min:4|max:4',
            'cvc' => 'required|string|min:3|max:4',
        ]);

        // Configure DataService
        $dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => env('QUICKBOOKS_CLIENT_ID'),
            'ClientSecret' => env('QUICKBOOKS_CLIENT_SECRET'),
            'accessTokenKey' => env('QUICKBOOKS_ACCESS_TOKEN'),
            'refreshTokenKey' => env('QUICKBOOKS_REFRESH_TOKEN'),
            'QBORealmID' => env('QUICKBOOKS_REALM_ID'),
            'baseUrl' => env('QUICKBOOKS_ENVIRONMENT') === 'Development' ? 'Development' : 'Production',
        ]);

        // Check if user already has a QuickBooks Customer ID
        $user = auth()->user(); // Assuming the user is logged in
        if ($user->quickbooks_customer_id) {
            return back()->with('error', 'This user is already added to QuickBooks.');
        }

        try {
            // Check if the customer already exists in QuickBooks
            $query = "SELECT * FROM Customer WHERE DisplayName = '{$validated['title']}'";
            $existingCustomer = $dataService->Query($query);

            if ($existingCustomer) {
                $customerId = $existingCustomer[0]->Id;
                $user->update(['quickbooks_customer_id' => $customerId]); // Update local record
                return back()->with('info', 'Customer already exists in QuickBooks.');
            }

            // Create a new customer in QuickBooks
            $customerObj = Customer::create([
                "DisplayName" => $validated['title'],
                "PrimaryEmailAddr" => [
                    "Address" => "user@example.com",
                ],
                "PrimaryPhone" => [
                    "FreeFormNumber" => "123-456-7890",
                ],
            ]);

            $resultingCustomer = $dataService->Add($customerObj);

            if (!$resultingCustomer) {
                $error = $dataService->getLastError();
                logger()->error('QuickBooks Error', ['error' => $error->getResponseBody()]);
                return back()->with('error', 'Failed to add customer to QuickBooks.');
            }

            // Save QuickBooks Customer ID to the user record
            $user->update(['quickbooks_customer_id' => $resultingCustomer->Id]);

            return back()->with('success', 'Customer successfully added to QuickBooks!');
        } catch (\Exception $e) {
            logger()->error('Exception Occurred', ['message' => $e->getMessage()]);
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
