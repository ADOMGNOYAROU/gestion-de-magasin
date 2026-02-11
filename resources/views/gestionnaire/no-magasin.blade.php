<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Aucun magasin assigné</h1>
                <p class="text-gray-600 mb-6">Vous n'êtes pas encore assigné à un magasin. Veuillez contacter l'administrateur.</p>
                <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                    Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
