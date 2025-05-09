import { createClient } from 'https://esm.sh/@supabase/supabase-js';

const supabaseUrl = 'https://hoojcjagwvrelqytwgdg.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imhvb2pjamFnd3ZyZWxxeXR3Z2RnIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDY3ODY2MjEsImV4cCI6MjA2MjM2MjYyMX0.gEqnLIAWSNIjUcM2KndKI5VfEn5jPCA9sQ-MPl5Of6U';

const options = {
    auth: {
        autoRefreshToken: true,
        persistSession: true,
        detectSessionInUrl: true,
        flowType: 'pkce',
        debug: true,
        redirectTo: window.location.origin + '/GrxDiscover/php/session_handler.php?action=oauth_callback',
        storageKey: 'grxdiscover-auth',
        storage: window.localStorage
    }
};

// Log configuration for debugging
console.log('Supabase Configuration:', {
    url: supabaseUrl,
    options: options,
    redirectUrl: options.auth.redirectTo
});

// Create Supabase client
const supabase = createClient(supabaseUrl, supabaseKey, options);

// Export the client
export const initSupabase = () => supabase;
