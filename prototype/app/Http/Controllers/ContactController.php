<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Contact; // زيد هادي
use App\Services\ContactService;

class ContactController extends Controller
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index(Request $request)
    {
        // Import automatic if empty
        if (Contact::count() == 0 && file_exists(public_path('contacts.csv'))) {
            $this->contactService->import(public_path('contacts.csv'));
        }

        $contacts = $this->contactService->getContacts($request);
        $cities = City::all();

        return view('index', compact('contacts', 'cities'));
    }

    public function store(Request $request)
    {
        $this->contactService->storeContact($request->all());
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $this->contactService->updateContact($id, $request->all());
        return redirect()->back();
    }

    public function destroy($id)
    {
        $this->contactService->deleteContact($id);
        return redirect()->back();
    }
}