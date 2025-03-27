<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CESI Rencontre connexion</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="/images/logo.png" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
<div class="container w-50 px-lg-5 mt-5">
    <h2>Connexion</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email :</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe :</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-lg btn-primary mt-5">Se connecter</button>
    </form>

    <div id="error-message" class="mt-3 text-danger"></div>
</div>

<script>
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault(); // Empêche la soumission classique du formulaire
        const formData = new FormData(e.target);

        try {
            // Envoyer la requête de connexion
            const loginResponse = await fetch('{{ route('login') }}', {
                method: 'POST',
                body: formData,
            });
            const loginData = await loginResponse.json();

            console.log('Réponse login:', loginData); // Pour déboguer

            if (loginData.token) {
                // Stocker le token dans localStorage
                sessionStorage.setItem('token', loginData.token);
                window.location.href='/app'
            } else {
                document.getElementById('error-message').innerText = loginData.message || 'Erreur inconnue';
            }
        } catch (error) {
            console.error('Erreur générale:', error);
            document.getElementById('error-message').innerText = 'Erreur inattendue : ' + error.message;
        }
    });
</script>
</body>
</html>
