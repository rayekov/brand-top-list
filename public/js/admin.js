/**
 * Admin Panel 
 */

class AdminApp {
    constructor() {
        this.currentTab = 'brands';
        this.brands = [];
        this.countries = [];
        this.toplistEntries = [];
        
        this.init();
    }

    async init() {
        // Check authentication
        if (!api.isAuthenticated()) {
            this.showLoginScreen();
        } else {
            await this.showDashboard();
        }
        
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Login form
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        }

        // Logout button
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => this.handleLogout());
        }

        // Navigation tabs
        const navTabs = document.querySelectorAll('.nav-tab');
        navTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabName = tab.dataset.tab;
                this.switchTab(tabName);
            });
        });



        // Search inputs
        const brandSearch = document.getElementById('brandSearch');
        const countrySearch = document.getElementById('countrySearch');

        if (brandSearch) {
            brandSearch.addEventListener('input', (e) => this.filterBrands(e.target.value));
        }
        if (countrySearch) {
            countrySearch.addEventListener('input', (e) => this.filterCountries(e.target.value));
        }

        // Filters
        const countryFilter = document.getElementById('countryFilter');
        const statusFilter = document.getElementById('statusFilter');

        if (countryFilter) {
            countryFilter.addEventListener('change', () => this.filterToplist());
        }
        if (statusFilter) {
            statusFilter.addEventListener('change', () => this.filterToplist());
        }
    }

    async handleLogin(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const loginBtn = document.querySelector('.login-btn');
        const loginBtnText = document.getElementById('loginBtnText');
        const loginSpinner = document.getElementById('loginSpinner');
        const loginError = document.getElementById('loginError');

        // Show loading state
        loginBtn.disabled = true;
        loginBtnText.style.display = 'none';
        loginSpinner.style.display = 'block';
        loginError.style.display = 'none';

        try {
            await api.login(username, password);
            showToast('Login successful!', 'success');
            await this.showDashboard();
        } catch (error) {
            console.error('Login failed:', error);
            loginError.textContent = 'Invalid username or password';
            loginError.style.display = 'block';
        } finally {
            // Reset button state
            loginBtn.disabled = false;
            loginBtnText.style.display = 'block';
            loginSpinner.style.display = 'none';
        }
    }

    handleLogout() {
        api.logout();
        showToast('Logged out successfully', 'info');
        this.showLoginScreen();
    }

    showLoginScreen() {
        const loginScreen = document.getElementById('loginScreen');
        const adminDashboard = document.getElementById('adminDashboard');
        
        if (loginScreen) loginScreen.style.display = 'block';
        if (adminDashboard) adminDashboard.style.display = 'none';
    }

    async showDashboard() {
        const loginScreen = document.getElementById('loginScreen');
        const adminDashboard = document.getElementById('adminDashboard');

        if (loginScreen) loginScreen.style.display = 'none';
        if (adminDashboard) adminDashboard.style.display = 'block';

        // Setup dashboard event listeners
        this.setupDashboardEvents();

        // Load initial data
        await this.loadAllData();
        this.switchTab(this.currentTab);
    }

    setupDashboardEvents() {
        // Add buttons
        const addBrandBtn = document.getElementById('addBrandBtn');
        const addCountryBtn = document.getElementById('addCountryBtn');
        const addToplistBtn = document.getElementById('addToplistBtn');

        if (addBrandBtn) {
            addBrandBtn.onclick = () => {
                console.log('Add brand button clicked');
                this.showBrandModal();
            };
        }
        if (addCountryBtn) {
            addCountryBtn.onclick = () => {
                console.log('Add country button clicked');
                this.showCountryModal();
            };
        }
        if (addToplistBtn) {
            addToplistBtn.onclick = () => {
                console.log('Add toplist button clicked');
                this.showToplistModal();
            };
        }

        // Filter event handlers
        const countryFilter = document.getElementById('countryFilter');

        if (countryFilter) {
            countryFilter.onchange = () => {
                console.log('Country filter changed:', countryFilter.value);
                this.filterToplist();
            };
        }
    }

    async loadAllData() {
        try {
            const [brandsResponse, countriesResponse] = await Promise.all([
                api.getBrands(),
                api.getCountries()
            ]);

            this.brands = brandsResponse.data || [];
            this.countries = countriesResponse.data || [];
            
            this.populateCountryFilter();
        } catch (error) {
            console.error('Failed to load data:', error);
            showToast('Failed to load data', 'error');
        }
    }

    switchTab(tabName) {
        this.currentTab = tabName;

        // Update nav tabs
        document.querySelectorAll('.nav-tab').forEach(tab => {
            tab.classList.toggle('active', tab.dataset.tab === tabName);
        });

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.toggle('active', content.id === `${tabName}Tab`);
        });

        // Load tab-specific data
        switch (tabName) {
            case 'brands':
                this.renderBrands();
                break;
            case 'countries':
                this.renderCountries();
                break;
            case 'toplist':
                this.populateCountryFilter();
                this.loadToplistEntries();
                break;
        }
    }

    renderBrands(filteredBrands = null) {
        const brandsGrid = document.getElementById('brandsGrid');
        if (!brandsGrid) return;

        const brands = filteredBrands || this.brands;
        brandsGrid.innerHTML = '';

        if (brands.length === 0) {
            brandsGrid.innerHTML = '<div class="empty-state">No brands found</div>';
            return;
        }

        brands.forEach(brand => {
            const card = this.createBrandCard(brand);
            brandsGrid.appendChild(card);
        });
    }

    createBrandCard(brand) {
        const card = document.createElement('div');
        card.className = 'admin-card';
        
        card.innerHTML = `
            <div class="admin-card-header">
                <div>
                    <h3>${brand.brand_name}</h3>
                    <p>Rating: ${brand.rating}/100</p>
                </div>
                <div class="admin-card-actions">
                    <button class="action-btn edit-btn" onclick="adminApp.editBrand('${brand.uuid}')">
                        Edit
                    </button>
                    <button class="action-btn delete-btn" onclick="adminApp.deleteBrand('${brand.uuid}')">
                        Delete
                    </button>
                </div>
            </div>
            <div class="brand-details">
                <img src="${brand.brand_image}" alt="${brand.brand_name}" 
                     style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;">
                <p><small>Created: ${formatDate(brand.created_at)}</small></p>
            </div>
        `;

        return card;
    }

    renderCountries(filteredCountries = null) {
        const countriesGrid = document.getElementById('countriesGrid');
        if (!countriesGrid) return;

        const countries = filteredCountries || this.countries;
        countriesGrid.innerHTML = '';

        if (countries.length === 0) {
            countriesGrid.innerHTML = '<div class="empty-state">No countries found</div>';
            return;
        }

        countries.forEach(country => {
            const card = this.createCountryCard(country);
            countriesGrid.appendChild(card);
        });
    }

    createCountryCard(country) {
        const card = document.createElement('div');
        card.className = 'admin-card';
        
        card.innerHTML = `
            <div class="admin-card-header">
                <div>
                    <h3>${country.name}</h3>
                    <p>ISO Code: ${country.iso_code}</p>
                </div>
                <div class="admin-card-actions">
                    <button class="action-btn edit-btn" onclick="adminApp.editCountry('${country.uuid}')">
                        Edit
                    </button>
                    <button class="action-btn delete-btn" onclick="adminApp.deleteCountry('${country.uuid}')">
                        Delete
                    </button>
                </div>
            </div>
            <div class="country-details">
                <p><small>Created: ${formatDate(country.created_at)}</small></p>
            </div>
        `;

        return card;
    }

    async loadToplistEntries() {
        const toplistGrid = document.getElementById('toplistGrid');
        if (!toplistGrid) return;

        try {
            // Load toplist for each country to show all entries
            this.toplistEntries = [];
            for (const country of this.countries) {
                try {
                    const response = await api.request(`/toplist?country=${country.iso_code}`);
                    if (response.data && response.data.entries) {
                        response.data.entries.forEach(entry => {
                            this.toplistEntries.push({
                                ...entry,
                                country: country
                            });
                        });
                    }
                } catch (error) {
                    console.log(`No toplist for ${country.name}`);
                }
            }
            this.renderToplistEntries();
        } catch (error) {
            console.error('Failed to load toplist entries:', error);
            toplistGrid.innerHTML = `
                <div class="empty-state">
                    <h3>Error Loading Toplists</h3>
                    <p>Unable to load toplist entries. Please try again.</p>
                </div>
            `;
        }
    }

    renderToplistEntries() {
        this.renderFilteredToplist(this.toplistEntries);
    }

    createToplistCard(entry) {
        const card = document.createElement('div');
        card.className = 'admin-card';

        card.innerHTML = `
            <div class="admin-card-header">
                <div>
                    <h3>${entry.brand.brand_name}</h3>
                    <p>${entry.country.name} - Position ${entry.position}</p>
                </div>
                <div class="admin-card-actions">
                    <button class="action-btn edit-btn" onclick="adminApp.editToplistEntry('${entry.uuid}')">
                        Edit
                    </button>
                    <button class="action-btn delete-btn" onclick="adminApp.deleteToplistEntry('${entry.uuid}')">
                        Delete
                    </button>
                </div>
            </div>
            <div class="toplist-details">
                <span class="status-badge ${entry.is_active ? 'status-active' : 'status-inactive'}">
                    ${entry.is_active ? 'Active' : 'Inactive'}
                </span>
                <p><small>Rating: ${entry.brand.rating}/100</small></p>
            </div>
        `;

        return card;
    }

    populateCountryFilter() {
        const countryFilter = document.getElementById('countryFilter');
        if (!countryFilter) {
            console.log('Country filter element not found');
            return;
        }

        console.log('Populating country filter with', this.countries.length, 'countries');
        countryFilter.innerHTML = '<option value="">All Countries</option>';

        this.countries.forEach(country => {
            const option = document.createElement('option');
            option.value = country.uuid;
            option.textContent = country.name;
            countryFilter.appendChild(option);
            console.log('Added country option:', country.name, country.uuid);
        });
    }

    filterBrands(searchTerm) {
        const filtered = this.brands.filter(brand =>
            brand.brand_name.toLowerCase().includes(searchTerm.toLowerCase())
        );
        this.renderBrands(filtered);
    }

    filterCountries(searchTerm) {
        const filtered = this.countries.filter(country =>
            country.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            country.iso_code.toLowerCase().includes(searchTerm.toLowerCase())
        );
        this.renderCountries(filtered);
    }

    filterToplist() {
        const countryFilter = document.getElementById('countryFilter');

        if (!countryFilter) return;

        const selectedCountry = countryFilter.value;

        console.log('Filtering toplist by country:', { selectedCountry, totalEntries: this.toplistEntries.length });

        let filtered = [...this.toplistEntries];

        // Filter by country
        if (selectedCountry) {
            filtered = filtered.filter(entry => {
                console.log('Checking entry:', entry.country.uuid, 'vs selected:', selectedCountry);
                return entry.country.uuid === selectedCountry;
            });
            console.log('After country filter:', filtered.length);
        }

        this.renderFilteredToplist(filtered);
    }

    renderFilteredToplist(entries) {
        const toplistGrid = document.getElementById('toplistGrid');
        if (!toplistGrid) return;

        if (entries.length === 0) {
            toplistGrid.innerHTML = `
                <div class="empty-state">
                    <h3>No Entries Found</h3>
                    <p>No toplist entries match the selected filters.</p>
                </div>
            `;
            return;
        }

        toplistGrid.innerHTML = '';
        entries.forEach(entry => {
            const card = this.createToplistCard(entry);
            toplistGrid.appendChild(card);
        });
    }

    showBrandModal(brand = null) {
        console.log('showBrandModal called with:', brand);
        const isEdit = !!brand;
        const modal = this.createModal();

        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>${isEdit ? 'Edit' : 'Add'} Brand</h3>
                    <button class="close-btn" onclick="this.closest('.modal').remove()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="brandForm">
                        <div class="form-group">
                            <label>Brand Name</label>
                            <input type="text" id="brandName" value="${brand?.brand_name || ''}" required>
                        </div>
                        <div class="form-group">
                            <label>Brand Image</label>
                            <div class="image-upload-container">
                                <input type="file" id="brandImageFile" accept="image/*" style="display: none;">
                                <input type="url" id="brandImageUrl" placeholder="Enter image URL" value="${brand?.brand_image || ''}">
                                <button type="button" id="uploadImageBtn" class="upload-btn">Upload</button>
                            </div>
                            <div id="imagePreview" class="image-preview">
                                ${brand?.brand_image ? `<img src="${brand.brand_image}" alt="Preview">` : ''}
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Rating (0-100)</label>
                            <input type="number" id="brandRating" min="0" max="100" value="${brand?.rating || 0}" required>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="this.closest('.modal').remove()">Cancel</button>
                            <button type="submit" class="btn-primary">${isEdit ? 'Update' : 'Create'}</button>
                        </div>
                    </form>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        modal.style.display = 'block';

        // Setup image upload handlers
        this.setupImageUpload();

        document.getElementById('brandForm').onsubmit = async (e) => {
            e.preventDefault();
            await this.saveBrand(brand?.uuid, isEdit);
        };
    }

    showCountryModal(country = null) {
        const isEdit = !!country;
        const modal = this.createModal();

        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>${isEdit ? 'Edit' : 'Add'} Country</h3>
                    <button class="close-btn" onclick="this.closest('.modal').remove()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="countryForm">
                        <div class="form-group">
                            <label>Country Name</label>
                            <input type="text" id="countryName" value="${country?.name || ''}" required>
                        </div>
                        <div class="form-group">
                            <label>ISO Code (2 letters)</label>
                            <input type="text" id="countryIso" value="${country?.iso_code || ''}" maxlength="2" required>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="this.closest('.modal').remove()">Cancel</button>
                            <button type="submit" class="btn-primary">${isEdit ? 'Update' : 'Create'}</button>
                        </div>
                    </form>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        modal.style.display = 'block';

        document.getElementById('countryForm').onsubmit = async (e) => {
            e.preventDefault();
            await this.saveCountry(country?.uuid, isEdit);
        };
    }

    showToplistModal(entry = null) {
        const isEdit = !!entry;
        const modal = this.createModal();

        const brandOptions = this.brands.map(brand =>
            `<option value="${brand.uuid}" ${entry?.brand?.uuid === brand.uuid ? 'selected' : ''}>${brand.brand_name}</option>`
        ).join('');

        const countryOptions = this.countries.map(country =>
            `<option value="${country.uuid}" ${entry?.country?.uuid === country.uuid ? 'selected' : ''}>${country.name}</option>`
        ).join('');

        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>${isEdit ? 'Edit' : 'Add'} Toplist Entry</h3>
                    <button class="close-btn" onclick="this.closest('.modal').remove()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="toplistForm">
                        <div class="form-group">
                            <label>Brand</label>
                            <select id="toplistBrand" required>
                                <option value="">Select Brand</option>
                                ${brandOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <select id="toplistCountry" required>
                                <option value="">Select Country</option>
                                ${countryOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Position</label>
                            <input type="number" id="toplistPosition" min="1" value="${entry?.position || 1}" required>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="toplistActive" ${entry?.is_active !== false ? 'checked' : ''}>
                                Active
                            </label>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="this.closest('.modal').remove()">Cancel</button>
                            <button type="submit" class="btn-primary">${isEdit ? 'Update' : 'Create'}</button>
                        </div>
                    </form>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        modal.style.display = 'block';

        document.getElementById('toplistForm').onsubmit = async (e) => {
            e.preventDefault();
            await this.saveToplistEntry(entry?.uuid, isEdit);
        };
    }

    createModal() {
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };
        return modal;
    }

    async saveBrand(uuid, isEdit) {
        const name = document.getElementById('brandName').value;
        const image = document.getElementById('brandImageUrl').value;
        const rating = parseInt(document.getElementById('brandRating').value);

        const data = {
            brand_name: name,
            brand_image: image,
            rating: rating
        };

        try {
            if (isEdit) {
                await api.updateBrand(uuid, data);
                showToast('Brand updated successfully', 'success');
            } else {
                await api.createBrand(data);
                showToast('Brand created successfully', 'success');
            }

            document.querySelector('.modal').remove();
            await this.loadAllData();
            this.renderBrands();
        } catch (error) {
            console.error('Save brand failed:', error);
            showToast('Failed to save brand', 'error');
        }
    }

    async saveCountry(uuid, isEdit) {
        const name = document.getElementById('countryName').value;
        const iso = document.getElementById('countryIso').value.toUpperCase();

        const data = {
            name: name,
            iso_code: iso
        };

        try {
            if (isEdit) {
                await api.updateCountry(uuid, data);
                showToast('Country updated successfully', 'success');
            } else {
                await api.createCountry(data);
                showToast('Country created successfully', 'success');
            }

            document.querySelector('.modal').remove();
            await this.loadAllData();
            this.renderCountries();
        } catch (error) {
            console.error('Save country failed:', error);
            showToast('Failed to save country', 'error');
        }
    }

    async saveToplistEntry(uuid, isEdit) {
        const brandUuid = document.getElementById('toplistBrand').value;
        const countryUuid = document.getElementById('toplistCountry').value;
        const position = parseInt(document.getElementById('toplistPosition').value);
        const isActive = document.getElementById('toplistActive').checked;

        const data = {
            brand_uuid: brandUuid,
            country_uuid: countryUuid,
            position: position,
            is_active: isActive
        };

        try {
            if (isEdit) {
                await api.updateTopListEntry(uuid, data);
                showToast('Toplist entry updated successfully', 'success');
            } else {
                await api.createTopListEntry(data);
                showToast('Toplist entry created successfully', 'success');
            }

            document.querySelector('.modal').remove();
            this.loadToplistEntries();
        } catch (error) {
            console.error('Save toplist entry failed:', error);
            showToast('Failed to save toplist entry', 'error');
        }
    }

    // CRUD methods
    async editBrand(uuid) {
        const brand = this.brands.find(b => b.uuid === uuid);
        this.showBrandModal(brand);
    }

    async deleteBrand(uuid) {
        if (!confirm('Are you sure you want to delete this brand?')) return;

        try {
            await api.deleteBrand(uuid);
            showToast('Brand deleted successfully', 'success');
            await this.loadAllData();
            this.renderBrands();
        } catch (error) {
            console.error('Failed to delete brand:', error);
            showToast('Failed to delete brand', 'error');
        }
    }

    async editCountry(uuid) {
        const country = this.countries.find(c => c.uuid === uuid);
        this.showCountryModal(country);
    }

    async deleteCountry(uuid) {
        if (!confirm('Are you sure you want to delete this country?')) return;

        try {
            await api.deleteCountry(uuid);
            showToast('Country deleted successfully', 'success');
            await this.loadAllData();
            this.renderCountries();
        } catch (error) {
            console.error('Failed to delete country:', error);
            showToast('Failed to delete country', 'error');
        }
    }

    async editToplistEntry(uuid) {
        const entry = this.toplistEntries.find(e => e.uuid === uuid);
        this.showToplistModal(entry);
    }

    async deleteToplistEntry(uuid) {
        if (!confirm('Are you sure you want to delete this toplist entry?')) return;

        try {
            await api.deleteTopListEntry(uuid);
            showToast('Toplist entry deleted successfully', 'success');
            this.loadToplistEntries();
        } catch (error) {
            console.error('Failed to delete toplist entry:', error);
            showToast('Failed to delete toplist entry', 'error');
        }
    }

    setupImageUpload() {
        const uploadBtn = document.getElementById('uploadImageBtn');
        const fileInput = document.getElementById('brandImageFile');
        const urlInput = document.getElementById('brandImageUrl');
        const preview = document.getElementById('imagePreview');

        if (uploadBtn && fileInput) {
            uploadBtn.onclick = () => fileInput.click();

            fileInput.onchange = (e) => {
                const file = e.target.files[0];
                if (file) {
                    this.handleImageUpload(file, urlInput, preview);
                }
            };
        }

        if (urlInput) {
            urlInput.onchange = () => {
                const url = urlInput.value;
                if (url) {
                    this.updateImagePreview(url, preview);
                }
            };
        }
    }

    handleImageUpload(file, urlInput, preview) {
        // For demo purposes, we'll convert to base64
        // In production, you'd upload to a server
        const reader = new FileReader();
        reader.onload = (e) => {
            const dataUrl = e.target.result;
            urlInput.value = dataUrl;
            this.updateImagePreview(dataUrl, preview);
            showToast('Image uploaded successfully', 'success');
        };
        reader.readAsDataURL(file);
    }

    updateImagePreview(url, preview) {
        if (preview) {
            preview.innerHTML = `<img src="${url}" alt="Preview" onerror="this.style.display='none'">`;
        }
    }


}

// Initialize admin app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.adminApp = new AdminApp();
});
