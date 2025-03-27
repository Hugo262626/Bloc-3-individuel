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
                <a href="#" class="dropdown-item" id="profile-link">Profil</a>
                <div class="dropdown-divider"></div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
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

<script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js"></script>
<script>
    const token = localStorage.getItem('token');
    console.log('Token initial:', token); // Débogage : Vérifier si le token est présent

    // Vérification au chargement de la page
    window.addEventListener('load', async () => {
        console.log('Événement load déclenché'); // Débogage
        if (!token) {
            console.log('Aucun token, redirection vers /login');
            window.location.href = '/login';
            return;
        }

        try {
            console.log('Requête vers /app'); // Débogage
            const response = await fetch('/app', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'text/html',
                },
            });

            console.log('Statut /app:', response.status, response.statusText); // Débogage

            if (!response.ok) {
                console.error('Erreur lors du rechargement:', response.status, response.statusText);
                window.location.href = '/login';
                return;
            }

            // Charger les données
            console.log('Chargement des données utilisateur, utilisateurs et carte');
            loadUserData();
            loadUsers();
            initMap();
        } catch (error) {
            console.error('Erreur lors du chargement initial:', error);
            window.location.href = '/login';
        }
    });

    // Charger les données de l'utilisateur pour la navbar
    async function loadUserData() {
        try {
            console.log('Requête vers /api/profile'); // Débogage
            const response = await fetch('/api/profile', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                },
            });

            console.log('Statut /api/profile:', response.status, response.statusText); // Débogage

            if (!response.ok) {
                throw new Error(`Erreur ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('Données utilisateur:', data); // Débogage

            document.getElementById('user-name').textContent = data.name || 'Utilisateur';
            document.getElementById('user-description').textContent = data.description || 'UI Designer';
            document.getElementById('user-avatar').style.backgroundImage = `url(${data.photo || './static/avatars/000m.jpg'})`;
        } catch (error) {
            console.error('Erreur chargement utilisateur:', error);
        }
    }

    // Charger les utilisateurs
    async function loadUsers() {
        try {
            console.log('Requête vers /api/users'); // Débogage
            const response = await fetch('/api/users', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                },
            });

            console.log('Statut /api/users:', response.status, response.statusText); // Débogage

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(`Erreur ${response.status}: ${errorData.message || response.statusText}`);
            }

            const data = await response.json();
            console.log('Données utilisateurs:', data); // Débogage

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
        console.log('Initialisation de la carte Leaflet'); // Débogage
        var map = L.map('users-map').setView([48.8566, 2.3522], 13); // Paris par défaut
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                console.log('Position géolocalisée:', lat, lon); // Débogage
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

    // Gestion du lien Profil
    document.getElementById('profile-link').addEventListener('click', async (e) => {
        e.preventDefault();
        console.log('Clic sur le lien Profil'); // Débogage
        try {
            const response = await fetch('/profile', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'text/html',
                },
            });

            console.log('Statut /profile:', response.status, response.statusText); // Débogage

            if (response.ok) {
                const html = await response.text();
                document.open();
                document.write(html);
                document.close();
                window.history.pushState({}, document.title, '/profile');
            } else {
                console.error('Erreur lors du chargement de /profile:', response.status, response.statusText);
                alert('Erreur d’accès au profil : ' + response.statusText);
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur inattendue');
        }
    });
</script>
</body>
</html>
