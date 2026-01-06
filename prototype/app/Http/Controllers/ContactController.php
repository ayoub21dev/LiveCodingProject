<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Contact;
use App\Services\ContactService;

class ContactController extends Controller
{
    // Inject ContactService for business logic
    public function __construct(protected ContactService $service)
    {
    }

    /**
     * Display the contact list with filtering and search.
     * Also handles automatic CSV import if the database is empty.
     */
    public function index(Request $request)
    {
        // Check if there are no contacts and a CSV exists to import initial data
        if (Contact::count() == 0 && file_exists($path = public_path('contacts.csv'))) {
            $this->service->import($path);
        }

        return view('index', [
            'contacts' => $this->service->getContacts($request),
            'cities' => City::all()
        ]);
    }

    /**
     * Store a newly created contact.
     */
    public function store(Request $request)
    {
        $this->service->save($request->all());
        return back();
    }

    /**
     * Update an existing contact.
     */
    public function update(Request $request, $id)
    {
        $this->service->save($request->all(), $id);
        return back();
    }

    /**
     * Delete a contact.
     */
    public function destroy($id)
    {
        Contact::destroy($id);
        return back();
    }
}