/**
 * API Utility Functions for Brand Top List Application
 */

class ApiClient {
    constructor() {
        this.baseUrl = this.getBaseUrl() + '/api';
        this.token = localStorage.getItem('admin_token');
    }

    getBaseUrl() {
        // Check if we're on a specific port or domain
        if (window.location.port && window.location.port !== '80' && window.location.port !== '443') {
            return `${window.location.protocol}//${window.location.hostname}:${window.location.port}`;
        }
        return `${window.location.protocol}//${window.location.hostname}`;
    }

    /**
     * Make HTTP request with proper headers
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        };

        // Add authorization header if token exists
        if (this.token && !endpoint.includes('/auth/login')) {
            config.headers['Authorization'] = `Bearer ${this.token}`;
        }

        // Add simulated CF-IPCountry header for testing
        const selectedCountry = localStorage.getItem('selected_country');
        if (selectedCountry && selectedCountry !== 'auto') {
            config.headers['CF-IPCountry'] = selectedCountry;
            console.log('Setting CF-IPCountry header:', selectedCountry);
        } else {
            console.log('No CF-IPCountry header set (auto mode)');
        }

        console.log('API request:', url, config.headers);

        try {
            const response = await fetch(url, config);
            
            if (!response.ok) {
                if (response.status === 401) {
                    this.clearToken();
                    throw new Error('Authentication required');
                }
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('API Request failed:', error);
            throw error;
        }
    }

    /**
     * Set authentication token
     */
    setToken(token) {
        this.token = token;
        localStorage.setItem('admin_token', token);
    }

    /**
     * Clear authentication token
     */
    clearToken() {
        this.token = null;
        localStorage.removeItem('admin_token');
    }

    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        return !!this.token;
    }

    // ===== PUBLIC ENDPOINTS =====

    /**
     * Get toplist based on geolocation
     */
    async getTopList() {
        return this.request('/toplist');
    }

    /**
     * Get all brands
     */
    async getBrands() {
        return this.request('/brands');
    }

    /**
     * Get brand by UUID
     */
    async getBrand(uuid) {
        return this.request(`/brands/${uuid}`);
    }

    /**
     * Get all countries
     */
    async getCountries() {
        return this.request('/countries');
    }

    /**
     * Get country by UUID
     */
    async getCountry(uuid) {
        return this.request(`/countries/${uuid}`);
    }

    /**
     * Get country by ISO code
     */
    async getCountryByIso(isoCode) {
        return this.request(`/countries/iso/${isoCode}`);
    }

    // ===== AUTHENTICATION =====

    /**
     * Admin login
     */
    async login(username, password) {
        const response = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ username, password })
        });

        if (response.token) {
            this.setToken(response.token);
        }

        return response;
    }

    /**
     * Admin logout
     */
    logout() {
        this.clearToken();
    }

    // ===== ADMIN ENDPOINTS =====

    /**
     * Create new brand
     */
    async createBrand(brandData) {
        return this.request('/admin/brands', {
            method: 'POST',
            body: JSON.stringify(brandData)
        });
    }

    /**
     * Update brand
     */
    async updateBrand(uuid, brandData) {
        return this.request(`/admin/brands/${uuid}`, {
            method: 'PUT',
            body: JSON.stringify(brandData)
        });
    }

    /**
     * Delete brand
     */
    async deleteBrand(uuid) {
        return this.request(`/admin/brands/${uuid}`, {
            method: 'DELETE'
        });
    }

    /**
     * Create new country
     */
    async createCountry(countryData) {
        return this.request('/admin/countries', {
            method: 'POST',
            body: JSON.stringify(countryData)
        });
    }

    /**
     * Update country
     */
    async updateCountry(uuid, countryData) {
        return this.request(`/admin/countries/${uuid}`, {
            method: 'PUT',
            body: JSON.stringify(countryData)
        });
    }

    /**
     * Delete country
     */
    async deleteCountry(uuid) {
        return this.request(`/admin/countries/${uuid}`, {
            method: 'DELETE'
        });
    }

    /**
     * Create toplist entry
     */
    async createTopListEntry(entryData) {
        return this.request('/admin/toplist', {
            method: 'POST',
            body: JSON.stringify(entryData)
        });
    }

    /**
     * Update toplist entry
     */
    async updateTopListEntry(uuid, entryData) {
        return this.request(`/admin/toplist/${uuid}`, {
            method: 'PUT',
            body: JSON.stringify(entryData)
        });
    }

    /**
     * Delete toplist entry
     */
    async deleteTopListEntry(uuid) {
        return this.request(`/admin/toplist/${uuid}`, {
            method: 'DELETE'
        });
    }
}

// ===== UTILITY FUNCTIONS =====

/**
 * Format rating as stars
 */
function formatRatingStars(rating) {
    const maxStars = 5;
    const starPercentage = (rating / 100) * maxStars;
    const fullStars = Math.floor(starPercentage);
    const hasHalfStar = starPercentage % 1 >= 0.5;
    
    let stars = '★'.repeat(fullStars);
    if (hasHalfStar) stars += '☆';
    
    const emptyStars = maxStars - fullStars - (hasHalfStar ? 1 : 0);
    stars += '☆'.repeat(emptyStars);
    
    return stars;
}



/**
 * Format date for display
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => toast.remove());

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    // Add toast styles
    Object.assign(toast.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '15px 20px',
        borderRadius: '10px',
        color: 'white',
        fontWeight: '500',
        zIndex: '10000',
        maxWidth: '300px',
        boxShadow: '0 4px 20px rgba(0, 0, 0, 0.15)',
        transform: 'translateX(100%)',
        transition: 'transform 0.3s ease'
    });

    // Set background color based on type
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    toast.style.backgroundColor = colors[type] || colors.info;

    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Create global API client instance
const api = new ApiClient();
