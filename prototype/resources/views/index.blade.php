<!-- Style للمودال -->
<style>
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #00000080
    }

    .modal-body {
        background: #fff;
        margin: 10% auto;
        padding: 20px;
        width: 300px
    }
</style>

<!-- 1. Form de Recherche & Filter -->
<form method="GET">
    <!-- Task: Barre de recherche -->
    <input name="search" placeholder="Rechercher nom..." value="{{ request('search') }}">

    <!-- Task: Liste déroulante (Ville) -->
    <select name="city_id" onchange="this.form.submit()">
        <option value="">Toutes les villes</option>
        @foreach($cities as $c)
            <option value="{{ $c->id }}" {{ request('city_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
    </select>
</form>

<!-- Task: Bouton Ajouter (ouvre la modale) -->
<button onclick="document.getElementById('m').style.display='block'">Ajouter Contact</button>

<!-- Table -->
<table border="1" width="100%">
    <tr>
        <th>Nom</th>
        <th>Ville</th>
        <th>Actions</th>
    </tr>
    @foreach($contacts as $c)
        <tr>
            <td>{{ $c->first_name }} {{ $c->last_name }}</td>
            <td>@foreach($c->cities as $city) {{ $city->name }} @endforeach</td>
            <td>
                <!-- Modifier -->
                <button onclick="editContact({{ json_encode($c) }})">Modifier</button>

                <!-- Supprimer -->
                <form action="{{ route('contacts.destroy', $c->id) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Sûr ?')">Supprimer</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>

<!-- Task: Modale Ajouter -->
<div id="m" class="modal">
    <div class="modal-body">
        <form action="{{ route('contacts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <h3>Ajouter Contact</h3>
            <input name="first_name" placeholder="Prénom" required><br>
            <input name="last_name" placeholder="Nom" required><br>
            <input name="email" placeholder="Email"><br>

            <select name="cities[]" multiple>
                @foreach($cities as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
            </select><br>

            <input type="file" name="photo"><br><br>

            <button>Sauvegarder</button>
            <button type="button" onclick="document.getElementById('m').style.display='none'">Fermer</button>
        </form>
    </div>
</div>

<!-- Task: Modale Modifier -->
<div id="editModal" class="modal">
    <div class="modal-body">
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <h3>Modifier Contact</h3>
            <input name="first_name" id="edit_first_name" placeholder="Prénom" required><br>
            <input name="last_name" id="edit_last_name" placeholder="Nom" required><br>
            <input name="email" id="edit_email" placeholder="Email"><br>

            <select name="cities[]" id="edit_cities" multiple>
                @foreach($cities as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
            </select><br>

            <input type="file" name="photo"><br><br>

            <button>Mettre à jour</button>
            <button type="button" onclick="document.getElementById('editModal').style.display='none'">Fermer</button>
        </form>
    </div>
</div>

<script>
    function editContact(contact) {
        document.getElementById('editForm').action = "/contacts/" + contact.id;
        document.getElementById('edit_first_name').value = contact.first_name;
        document.getElementById('edit_last_name').value = contact.last_name;
        document.getElementById('edit_email').value = contact.email;

        // Select cities
        let citySelect = document.getElementById('edit_cities');
        Array.from(citySelect.options).forEach(option => {
            option.selected = contact.cities.some(c => c.id == option.value);
        });

        document.getElementById('editModal').style.display = 'block';
    }
</script>