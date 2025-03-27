<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CESI Rencontre Inscription</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="/images/logo.png" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
<div class="container w-50 px-lg-5 mt-5">
    <h2>Inscription</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" id="register-form">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nom :</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Présentation :</label>
            <textarea name="description" id="description" class="form-control" required>{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email :</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label for="birth" class="form-label">Date de naissance :</label>
            <input type="date" name="birth" id="birth" class="form-control" value="{{ old('birth') }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe :</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmer le mot de passe :</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-lg btn-primary mt-5">S'inscrire</button>
    </form>

    <div id="error-message" class="mt-3 text-danger"></div>
</div>

<script>
    document.getElementById('register-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            // Étape 1 : Envoyer les données d’inscription
            const registerResponse = await fetch('{{ route('register') }}', {
                method: 'POST',
                body: formData,
            });
            const registerData = await registerResponse.json();

            console.log('Réponse register:', registerData);

            if (registerData.token) {
                // Stocker le token
                sessionStorage.setItem('token', registerData.token);
                window.location.href='/app';

            } else {
                document.getElementById('error-message').innerText = registerData.message || 'Erreur inconnue';
            }
        } catch (error) {
            console.error('Erreur générale:', error);
            document.getElementById('error-message').innerText = 'Erreur inattendue : ' + error.message;
        }
    });
</script>
</body>
</html>
