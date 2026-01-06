<?php
namespace App\Services;
use App\Models\Contact;
use App\Models\City;

class ContactService {
    
    // 1. هادي كتجمع البحث والفلتر بجوج
    public function getContacts($request) {
        return Contact::with('cities')
            ->when($request->search, function($q) use ($request){
                $q->where('first_name', 'like', '%'.$request->search.'%')
                  ->orWhere('last_name', 'like', '%'.$request->search.'%');
            })
            ->when($request->city_id, function($q) use ($request){
                $q->whereHas('cities', fn($c) => $c->where('id', $request->city_id));
            })
            ->get();
    }

    // 2. هادي ديال الإضافة (Modal)
    public function storeContact($data) {
        $contact = Contact::create($data); // سجل المعلومات
        
        if(isset($data['cities'])) {
            $contact->cities()->attach($data['cities']); // ربط المدن
        }
        
        // كود التصويرة (اختياري إلا بقا الوقت)
        if(isset($data['photo'])) {
            $path = $data['photo']->store('uploads', 'public');
            $contact->update(['photo' => $path]);
        }
    }

    // 3. هادي Import CSV (ديرها باش تعمر الداتا)
    public function import($path) {
        if(!file_exists($path)) return;
        $file = fopen($path, 'r'); fgetcsv($file); // Skip Header
        while (($row = fgetcsv($file)) !== FALSE) {
            $city = City::firstOrCreate(['name' => $row[4]]);
            $contact = Contact::create(['first_name'=>$row[0], 'last_name'=>$row[1], 'email'=>$row[2], 'phone'=>$row[3]]);
            $contact->cities()->attach($city->id);
        }
    }
}