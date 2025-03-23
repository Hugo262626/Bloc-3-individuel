<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CESI Rencontre Inscription</title>
    <link rel='preconnect' href='https://cdn.jsdelivr.net'>
    <link href="/images/logo.png" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
<h2>Inscription</h2>

@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('register') }}" method="POST">
    @csrf
    <div class="container w-50 px-lg-5">
        <label for="name" class="form-label">Nom :</label>
        <input type="text" name="name" class="form-control" required>

        <label for="description" class="form-label">Présentation :</label>
        <textarea name="description" class="form-control" required></textarea>

        <label for="email" class="form-label">Email :</label>
        <input type="email" name="email" class="form-control" required>

        <label for="birth" class="form-label">Date de naissance :</label>
        <input type="date" name="birth" class="form-control" required>

        <label for="password" class="form-label">Mot de passe :</label>
        <input type="password" name="password" class="form-control" required>

        <label for="password_confirmation" class="form-label">Confirmer le mot de passe :</label>
        <input type="password" name="password_confirmation" class="form-control" required>

        <button type="submit" class="btn btn-lg btn-primary mt-5">S'inscrire</button>

    </div>
</form>
</body>
</html>
