<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CESI Rencontre connexion</title>
    <link rel='preconnect' href='https://cdn.jsdelivr.net'>
    <link href="/images/logo.png" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
<h2>Connexion</h2>

@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('login') }}" method="POST">
    @csrf
    <div class="container w-50 px-lg-5">
    <label for="email" class="form-label">Email :</label>
    <input type="email" name="email" class="form-control" required>

    <label for="password" class="form-label">Mot de passe :</label>
    <input type="password" name="password" class="form-control" required>

    <button type="submit" class="btn btn-lg btn-primary mt-5">Se connecter</button>
</form>
</body>
</html>
