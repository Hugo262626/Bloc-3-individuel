<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - CESI Rencontre</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="/images/logo.png" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
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
        <button type="submit" class="btn btn-lg btn-primary mt-3">Mettre à jour</button>
    </form>

    <div id="message" class="mt-3"></div>
</div>

<script>
    const token = localStorage.getItem('token');

    // Vérification au chargement
    window.addEventListener('load', async () => {
        console.log('Token au chargement:', token);
        if (!token) {
            console.error('Aucun token trouvé');
            window.location.href = '/login';
            return;
        }

        try {
            const response = await fetch('/profile', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'text/html',
                },
            });

            if (!response.ok) {
                console.error('Erreur /profile:', response.status, response.statusText);
                window.location.href = '/login';
                return;
            }

            loadProfile();
        } catch (error) {
            console.error('Erreur réseau:', error);
            window.location.href = '/login';
        }
    });

    // Charger les données du profil
    async function loadProfile() {
        try {
            const response = await fetch('/api/profile', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                },
            });

            console.log('Réponse /api/profile:', response.status, response.statusText);

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const data = await response.json();
            console.log('Données profil:', data);

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
        } catch (error) {
            console.error('Erreur chargement profil:', error);
            document.getElementById('message').innerText = 'Erreur lors du chargement du profil';
            document.getElementById('message').className = 'text-danger';
        }
    }

    // Mettre à jour le profil
    document.getElementById('profile-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        // Débogage : Vérifier les données envoyées
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        try {
            const response = await fetch('/api/profile', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                body: formData,
            });

            console.log('Statut réponse mise à jour:', response.status, response.statusText);

            const data = await response.json();
            console.log('Réponse mise à jour:', data);

            if (response.ok) {
                document.getElementById('message').innerText = data.message || 'Profil mis à jour avec succès';
                document.getElementById('message').className = 'text-success';
                loadProfile(); // Recharger les données mises à jour
            } else {
                document.getElementById('message').innerText = data.message || 'Erreur lors de la mise à jour';
                document.getElementById('message').className = 'text-danger';
            }
        } catch (error) {
            console.error('Erreur mise à jour:', error);
            document.getElementById('message').innerText = 'Erreur inattendue lors de la mise à jour';
            document.getElementById('message').className = 'text-danger';
        }
    });

    // Mettre à jour le profil
    document.getElementById('profile-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        try {
            const response = await fetch('/api/profile', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                body: formData,
            });

            console.log('Statut réponse mise à jour:', response.status, response.statusText);

            const data = await response.json();
            console.log('Réponse mise à jour:', data);

            if (response.ok) {
                document.getElementById('message').innerText = data.message || 'Profil mis à jour avec succès';
                document.getElementById('message').className = 'text-success';
                // Rediriger vers /app pour rafraîchir les données
                const appResponse = await fetch('/app', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'text/html',
                    },
                });
                if (appResponse.ok) {
                    const html = await appResponse.text();
                    document.open();
                    document.write(html);
                    document.close();
                    window.history.pushState({}, document.title, '/app');
                }
            } else {
                document.getElementById('message').innerText = data.message || 'Erreur lors de la mise à jour';
                document.getElementById('message').className = 'text-danger';
            }
        } catch (error) {
            console.error('Erreur mise à jour:', error);
            document.getElementById('message').innerText = 'Erreur inattendue lors de la mise à jour';
            document.getElementById('message').className = 'text-danger';
        }
    });
</script>
</body>
</html>
