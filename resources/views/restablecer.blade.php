<!DOCTYPE html>
<html lang="es" class="h-full bg-indigo-50">
<head>
  <meta charset="UTF-8">
  <title>Restablecer Contraseña</title>
  <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="h-full flex items-center justify-center px-4 py-12">
  <!-- Card -->
  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
    <!-- Header -->
    <div class="bg-gray-30 px-6 py-8 flex flex-col items-center">
      <img class="h-14 w-auto mb-4" src="/storage/photos/logo.png" alt="Logo">
      <h2 class="text-2xl font-bold text-gray-800">Restablecer Contraseña</h2>
    </div>

    <!-- Form -->
    <div class="px-6 py-8">
      <form method="POST" action="{{ route('restablecer.password') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
          <div class="mt-1">
            <input
              type="password"
              name="password"
              id="password"
              required
              class="block w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
            >
          </div>
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
          <div class="mt-1">
            <input
              type="password"
              name="password_confirmation"
              id="password_confirmation"
              required
              class="block w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
            >
          </div>
        </div>

        <div>
          <button
            type="submit"
            class="w-full flex justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Restablecer Contraseña
          </button>
        </div>
      </form>

      
    </div>
  </div>
</body>
</html>
