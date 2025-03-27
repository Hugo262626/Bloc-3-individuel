<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App rencontre CESI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>
<nav class="navbar bg-dark p-0">
    <div class="container-fluid p-0">
        <a class="navbar-brand p-0" href="#">
            <img src="/images/logo.png" alt="Logo" style="max-height: 90px;">
        </a>

        <div class="nav-item dropdown">
            <a href="#" class="nav-link d-flex lh-5 p-0 px-5" data-bs-toggle="dropdown" aria-label="Open user menu">
                <span id="user-avatar" class="avatar avatar-sm" style="background-image: url(./static/avatars/000m.jpg)"></span>
                <div class="d-none d-xl-block ps-2">
                    <div id="user-name">Utilisateur</div>
                    <div id="user-description" class="mt-1 small text-secondary">UI Designer</div>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profile-modal">Profil</button>
                <div class="dropdown-divider"></div>
                <form id="logout-form" style="display: inline;">
                    <button type="submit" class="dropdown-item">Déconnexion</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="page">
    <header class="navbar navbar-expand-md navbar-light">
        <div class="container-xl">
            <h1 class="navbar-brand">App CESI rencontre</h1>
        </div>
    </header>

    <div class="page-wrapper">
        <div class="container-xl">
            <ul class="nav nav-tabs" id="menuTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="list-tab" data-bs-toggle="tab" data-bs-target="#liste-tab-pane" type="button" role="tab" aria-controls="liste-tab-pane" aria-selected="true">Liste</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#carte-tab-pane" type="button" role="tab" aria-controls="carte-tab-pane" aria-selected="false">Carte</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="liste-tab-pane" role="tabpanel" aria-labelledby="liste-tab" tabindex="0">
                    <div class="row row-cards" id="users-list">
                        <!-- Liste des utilisateurs sera remplie via JS -->
                    </div>
                    <div class="d-flex mt-4">
                        <ul class="pagination ms-auto">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                        <path d="M15 6l-6 6l6 6"></path>
                                    </svg>
                                    prev
                                </a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">4</a></li>
                            <li class="page-item"><a class="page-link" href="#">5</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    next
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                        <path d="M9 6l6 6l-6 6"></path>
                                    </svg>
                                </a>
                            </li>
                            <!-- TODO: Implémenter une pagination dynamique en modifiant loadUsers() pour accepter un paramètre de page -->
                        </ul>
                    </div>
                </div>

                <div class="tab-pane fade" id="carte-tab-pane" role="tabpanel" aria-labelledby="carte-tab" tabindex="0">
                    <div class="card">
                        <div class="card-body">
                            <div id="users-map" style="height: 500px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" id="profile-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mon profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container w-50 px-lg-5 mt-5">
                    <h2>Mon Profil</h2>

                    <div id="profile-info" class="mb-4">
                        <!-- Les données seront chargées ici -->
                    </div>

                    <form id="profile-form" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom :</label>
                            <input type="text" name="name" id="name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email :</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Présentation :</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="birth" class="form-label">Date de naissance :</label>
                            <input type="date" name="birth" id="birth" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo de profil :</label>
                            <input type="file" name="photo" id="photo" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe (optionnel) :</label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe :</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>
                    </form>

                    <div id="message" class="mt-3"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="profile-save">Sauvegarder</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js"></script>
<script>
    const token = sessionStorage.getItem('token');
    console.log('Token initial:', token);
    if (!token) {
        console.log('Aucun token, redirection vers /login');
        window.location.href = '/login';
    }
try{
            // Charger les données
            console.log('Chargement des données utilisateur, utilisateurs et carte');
            loadUserData();
            loadUsers();
            initMap();
        } catch (error) {
            console.error('Erreur lors du chargement initial:', error);
            window.location.href = '/login';
        }

    // Charger les données de l'utilisateur pour la navbar
    async function loadUserData() {
        try {
            console.log('Requête vers /profile/me');
            const response = await fetch('/profile/me', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                },
            });

            console.log('Statut /profile/me:', response.status, response.statusText);

            if (!response.ok) {
               // throw new Error(`Erreur ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('Données utilisateur:', data);
            document.getElementById('name').value = data.name || '';
            document.getElementById('email').value = data.email || '';
            document.getElementById('description').value = data.description || '';
            document.getElementById('birth').value = data.birth || '';
            document.getElementById('profile-info').innerHTML = `
                    <p><strong>Nom :</strong> ${data.name || 'Non défini'}</p>
                    <p><strong>Email :</strong> ${data.email || 'Non défini'}</p>
                    <p><strong>Présentation :</strong> ${data.description || 'Non définie'}</p>
                    <p><strong>Date de naissance :</strong> ${data.birth || 'Non définie'}</p>
                    ${data.photo ? `<img src="${data.photo}" alt="Photo de profil" class="img-thumbnail" style="max-width: 200px;">` : ''}
                `;
            document.getElementById('user-name').textContent = data.name || 'Utilisateur';
            document.getElementById('user-description').textContent = data.description || 'UI Designer';
            document.getElementById('user-avatar').style.backgroundImage = `url(${data.photo || './static/avatars/000m.jpg'})`;
        } catch (error) {
            console.error('Erreur chargement utilisateur:', error);
        }
    }
document.getElementById('profile-save').addEventListener('click',(e)=>{
    console.log("sauvegarde du profil")
    fetch('/profile/me',{
        method: "PATCH",
        headers: {
            'Authorization': 'Bearer ' + token,
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            name:document.getElementById('name').value || '',
            description:document.getElementById('description').value || '',
            birth:document.getElementById('birth').value || '',
        })
    });
})

    // Charger les utilisateurs
    async function loadUsers() {
        try {
            console.log('Requête vers /users');
            const response = await fetch('/users', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                },
            });

            console.log('Statut /users:', response.status, response.statusText);

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(`Erreur ${response.status}: ${errorData.message || response.statusText}`);
            }

            const data = await response.json();
            console.log('Données utilisateurs:', data);

            const userList = document.getElementById('users-list');
            userList.innerHTML = ''; // Vider la liste avant de la remplir

            data.forEach(user => {
                const userCol = document.createElement('div');
                userCol.classList.add('col-md-6', 'col-lg-3');

                const card = document.createElement('div');
                card.classList.add('card');
                userCol.appendChild(card);

                const cardBody = document.createElement('div');
                cardBody.classList.add('card-body', 'p-4', 'text-center');

                const avatar = document.createElement('span');
                avatar.classList.add('avatar', 'avatar-xl', 'mb-3', 'rounded');
                avatar.style.backgroundImage = `url(${user.photo || './static/avatars/000m.jpg'})`;

                const userName = document.createElement('h3');
                userName.classList.add('m-0', 'mb-1');
                const userLink = document.createElement('a');
                userLink.href = '#';
                userLink.textContent = user.name || 'Nom inconnu';
                userName.appendChild(userLink);

                const userPost = document.createElement('div');
                userPost.classList.add('text-secondary');
                userPost.textContent = user.description || 'Position inconnue';

                const badgeContainer = document.createElement('div');
                badgeContainer.classList.add('mt-3');
                const badge = document.createElement('span');
                badge.classList.add('badge', 'bg-purple-lt');
                badge.textContent = user.role || 'Rôle inconnu';
                badgeContainer.appendChild(badge);

                cardBody.appendChild(avatar);
                cardBody.appendChild(userName);
                cardBody.appendChild(userPost);
                cardBody.appendChild(badgeContainer);
                card.appendChild(cardBody);

                const actionContainer = document.createElement('div');
                actionContainer.classList.add('d-flex');

                const messageLink = document.createElement('a');
                messageLink.href = '#';
                messageLink.classList.add('card-btn');
                messageLink.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2 text-muted icon-3">
                            <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path>
                            <path d="M3 7l9 6l9 -6"></path>
                        </svg>
                        Message
                    `;
                actionContainer.appendChild(messageLink);

                const callLink = document.createElement('a');
                callLink.href = '#';
                callLink.classList.add('card-btn');
                callLink.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2 text-muted icon-3">
                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path>
                        </svg>
                        Call
                    `;
                actionContainer.appendChild(callLink);

                card.appendChild(actionContainer);
                userList.appendChild(userCol);
            });
        } catch (err) {
            console.error('Erreur lors de la récupération des utilisateurs:', err);
            document.getElementById('users-list').innerHTML = '<p class="text-danger">Erreur lors du chargement des utilisateurs.</p>';
        }
    }

    // Initialiser la carte Leaflet
    function initMap() {
        console.log('Initialisation de la carte Leaflet');
        var map = L.map('users-map').setView([48.8566, 2.3522], 13); // Paris par défaut
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                console.log('Position géolocalisée:', lat, lon);
                map.setView([lat, lon], 15);
                L.marker([lat, lon]).addTo(map)
                    .bindPopup("Vous êtes ici !")
                    .openPopup();
            }, function(error) {
                console.error("Erreur de géolocalisation : ", error);
            });
        } else {
            console.error("La géolocalisation n’est pas supportée par ce navigateur.");
        }
    }

    async function loadusers(){
        console.log('Requête vers /users');
        try{
        const response = await fetch('/users', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
            },
        });

        console.log('Statut /users:', response.status, response.statusText);
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(`Erreur ${response.status}: ${errorData.message || response.statusText}`);
            }

            const data = await response.json();
            console.log('Données utilisateurs:', data);

        } catch (error) {
            console.error('Erreur réseau:', error);
            window.location.href = '/login';
        }
    }


    // Gestion de la déconnexion
    document.getElementById('logout-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        console.log('Tentative de déconnexion');
        try {
            const response = await fetch('/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}" // Ajout du token CSRF
                },
            });

            console.log('Statut /logout:', response.status, response.statusText);

            if (response.ok) {
                const data = await response.json();
                console.log('Réponse déconnexion:', data);
                sessionStorage.removeItem('token');
                window.location.href = data.redirect || '/';
            } else {
                const errorData = await response.json();
                console.error('Erreur lors de la déconnexion:', errorData);
                alert('Erreur lors de la déconnexion');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur inattendue lors de la déconnexion');
        }
    });
</script>
</body>
</html>
