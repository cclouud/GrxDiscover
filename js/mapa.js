// Comprobar si la configuración de Supabase está disponible
if (!window.SUPABASE_CONFIG || !window.SUPABASE_CONFIG.url || !window.SUPABASE_CONFIG.key) {
    alert("Error crítico: La configuración de Supabase no se ha cargado. La aplicación no puede funcionar.");
    throw new Error("Supabase configuration is missing from window object.");
}

// Inicialización de Supabase usando la configuración global
const supabaseClient = supabase.createClient(window.SUPABASE_CONFIG.url, window.SUPABASE_CONFIG.key);

// --- FUNCIONES DE AUTENTICACIÓN Y PERFIL ---
async function getProfileId() {
    const { data: { user } } = await supabaseClient.auth.getUser();
    return user ? user.id : null;
}

// --- FAVORITOS ---
async function getFavorites() {
    try {
        const profileId = await getProfileId();
        if (!profileId) return [];
        const { data: favorites, error } = await supabaseClient
            .from('favorites')
            .select('place_id')
            .eq('profile_id', profileId);
        if (error) throw error;
        return favorites.map(fav => fav.place_id);
    } catch (error) {
        console.error('Error obteniendo favoritos:', error);
        return [];
    }
}

async function toggleFavorite(id) {
    try {
        const profileId = await getProfileId();
        if (!profileId) {
            alert('Debes iniciar sesión para añadir favoritos');
            window.location.href = 'login.php';
            return;
        }
        const favs = await getFavorites();
        if (favs.includes(id)) {
            // Eliminar favorito
            const { error } = await supabaseClient
                .from('favorites')
                .delete()
                .eq('place_id', id)
                .eq('profile_id', profileId);
            if (error) throw error;
        } else {
            // Añadir favorito
            const { error } = await supabaseClient
                .from('favorites')
                .insert([{ place_id: id, profile_id: profileId }]);
            if (error) throw error;
        }
        loadPlaces(currentCategory, searchInput.value.trim(), showOnlyFavorites);
    } catch (error) {
        console.error('Error al gestionar favorito:', error);
        alert('Error al gestionar favorito. Por favor, intenta de nuevo.');
    }
}
let showOnlyFavorites = false;
function toggleShowFavorites() {
    showOnlyFavorites = !showOnlyFavorites;
    document.getElementById('show-favs-btn').classList.toggle('active', showOnlyFavorites);
    loadPlaces(currentCategory, searchInput.value.trim(), showOnlyFavorites);
}

// --- RESEÑAS ---
async function submitReview(placeId) {
    const container = isMobile() ? document.getElementById('bottom-sheet') : document.getElementById('place-details');
    const textarea = container.querySelector('textarea');
    const text = textarea.value.trim();
    if (!text) return;
    try {
        const profileId = await getProfileId();
        if (!profileId) {
            alert('Debes iniciar sesión para escribir reseñas');
            window.location.href = 'login.php';
            return;
        }
        const { error } = await supabaseClient
            .from('reviews')
            .insert([{ place_id: placeId, profile_id: profileId, text }]);
        if (error) throw error;
        const reviewsList = isMobile() ? document.getElementById('mobile-reviews-list') : document.getElementById('reviews-list');
        await loadReviews(placeId, reviewsList);
        textarea.value = '';
        container.querySelector('.review-form').classList.remove('active');
    } catch (error) {
        console.error('Error al guardar la reseña:', error);
        alert('Error al guardar la reseña. Por favor, intenta de nuevo.');
    }
}
async function loadReviews(placeId, container) {
    try {
        const { data: reviews, error } = await supabaseClient
            .from('reviews')
            .select('*, profiles(username)')
            .eq('place_id', placeId)
            .order('created_at', { ascending: false });
        if (error) throw error;
        if (!reviews || reviews.length === 0) {
            container.innerHTML = '<p style="color:var(--secondary-text);padding:1rem;">No hay reseñas todavía.</p>';
            return;
        }
        container.innerHTML = reviews.map(review => `
            <div class="review-item">
                <div class="review-meta">
                    <span>${review.profiles?.username || 'Usuario'}</span>
                    <span>${new Date(review.created_at).toLocaleDateString()}</span>
                </div>
                <div class="review-text">${review.text}</div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error al cargar las reseñas:', error);
        container.innerHTML = '<p style="color:var(--secondary-text);padding:1rem;">Error al cargar las reseñas.</p>';
    }
}

// --- MAPA Y LÓGICA DE UI ---
// Inicialización del mapa y lógica de la app (Leaflet, filtros, detalles, etc.)
// (El resto del código JS de tu mapa.php migrado aquí, usando las funciones de arriba)

// ...
// Si necesitas que migre funciones concretas (loadPlaces, createMarker, etc.), dímelo y lo hago paso a paso.
