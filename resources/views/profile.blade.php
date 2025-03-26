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
        console.log('Token:', token); // Débogage
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
                console.error('Erreur lors du chargement de /profile:', response.status, response.statusText);
                window.location.href = '/login';
                return;
            }

            // Charger les données du profil
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

            console.log('Réponse /api/profile:', response); // Débogage

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const data = await response.json();
            console.log('Données profil:', data); // Débogage

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
            console.error('Erreur lors du chargement du profil:', error);
            document.getElementById('message').innerText = 'Erreur lors du chargement du profil';
            document.getElementById('message').className = 'text-danger';
        }
    }

    // Mettre à jour le profil
    document.getElementById('profile-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const response = await fetch('/api/profile', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                },
                body: formData,
            });

            const data = await response.json();
            console.log('Réponse mise à jour:', data); // Débogage

            if (response.ok) {
                document.getElementById('message').innerText = data.message || 'Profil mis à jour';
                document.getElementById('message').className = 'text-success';
                loadProfile(); // Recharger les données
            } else {
                document.getElementById('message').innerText = data.message || 'Erreur lors de la mise à jour';
                document.getElementById('message').className = 'text-danger';
            }
        } catch (error) {
            console.error('Erreur lors de la mise à jour:', error);
            document.getElementById('message').innerText = 'Erreur inattendue';
            document.getElementById('message').className = 'text-danger';
        }
    });
</script>
</body>
</html>
