<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
</head>
<body>
<h1>Mon Profil</h1>
<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label>Nom:</label>
    <input type="text" name="name" value="{{ auth()->user()->name }}">

    <label>Email:</label>
    <input type="email" name="email" value="{{ auth()->user()->email }}">

    <label>Description:</label>
    <textarea name="description">{{ auth()->user()->description }}</textarea>

    <label>Photo de profil:</label>
    <input type="file" name="photo">

    <button type="submit">Mettre à jour</button>
</form>
</body>
</html>
