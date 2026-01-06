<?php
namespace App\Services;
use App\Models\Contact;
use App\Models\City;

class ContactService
{
    /**
     * Retrieve contacts with optional search (first/last name) and city filtering.
     */
    public function getContacts($request)
    {
        return Contact::with('cities')
            ->when($request->search, fn($q) => $q->where('first_name', 'like', "%$request->search%")->orWhere('last_name', 'like', "%$request->search%"))
            ->when($request->city_id, fn($q) => $q->whereHas('cities', fn($c) => $c->where('id', $request->city_id)))
            ->get();
    }

    /**
     * Create or update a contact record.
     * Handles synchronizing city relationships and photo uploads.
     */
    public function save($data, $id = null)
    {
        $contact = $id ? Contact::findOrFail($id) : new Contact;
        $contact->fill($data)->save();

        if (isset($data['cities']))
            $contact->cities()->sync($data['cities']);
        if (isset($data['photo']))
            $contact->update(['photo' => $data['photo']->store('uploads', 'public')]);
    }

    /**
     * Import contacts and their cities from a CSV file.
     */
    public function import($path)
    {
        if (!file_exists($path) || !($file = fopen($path, 'r')))
            return;

        fgetcsv($file); // Skip header

        while (($row = fgetcsv($file)) !== FALSE) {
            $city = City::firstOrCreate(['name' => $row[4]]);
            Contact::create(['first_name' => $row[0], 'last_name' => $row[1], 'email' => $row[2], 'phone' => $row[3]])
                ->cities()->attach($city->id);
        }
    }
}