import { createClient } from 'https://esm.sh/@supabase/supabase-js';

const supabaseUrl = 'https://hoojcjagwvrelqytwgdg.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imhvb2pjamFnd3ZyZWxxeXR3Z2RnIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDY3ODY2MjEsImV4cCI6MjA2MjM2MjYyMX0.gEqnLIAWSNIjUcM2KndKI5VfEn5jPCA9sQ-MPl5Of6U';

const options = {
    auth: {
        autoRefreshToken: true,
        persistSession: true,
        detectSessionInUrl: true,
        flowType: 'pkce',
        redirectTo: window.location.origin + '/GrxDiscover/php/oauth_callback.php',
        storageKey: 'grxdiscover-auth',
        storage: window.localStorage,
        onAuthStateChange: async (event, session) => {
            if (event === 'SIGNED_IN' && session) {
                try {
                    const response = await fetch('/GrxDiscover/php/session_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            action: 'login',
                            user: {
                                id: session.user.id,
                                email: session.user.email,
                                name: session.user.user_metadata.name || session.user.email
                            },
                            session: session
                        })
                    });
                    
                    if (!response.ok) {
                        throw new Error('Failed to store session');
                    }
                    
                    // Verificar la sesión después de iniciar sesión
                    await checkSession();
                } catch (error) {
                    console.error('Error storing session:', error);
                }
            } else if (event === 'SIGNED_OUT') {
                try {
                    await fetch('/GrxDiscover/php/session_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            action: 'logout'
                        })
                    });
                } catch (error) {
                    console.error('Error clearing session:', error);
                }
            }
        }
    }
};

// Create Supabase client
const supabase = createClient(supabaseUrl, supabaseKey, options);

// Initialize session check
async function checkSession() {
    try {
        const { data: { session }, error } = await supabase.auth.getSession();
        
        if (error) return;
        
        if (session) {
            // Verificar la sesión en PHP
            const response = await fetch('/GrxDiscover/php/session_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    action: 'check'
                })
            });
            
            const data = await response.json();
            if (!data.success || !data.is_logged_in) {
                // Si la sesión PHP no es válida, intentar restaurarla
                await fetch('/GrxDiscover/php/session_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        action: 'login',
                        user: {
                            id: session.user.id,
                            email: session.user.email,
                            name: session.user.user_metadata.name || session.user.email
                        },
                        session: session
                    })
                });
            }
        }
    } catch (error) {
        // Silently handle errors
    }
}

// Check session on load and every 5 minutes
checkSession();
setInterval(checkSession, 5 * 60 * 1000);

// Export the client
export const initSupabase = () => supabase;
