class TopListApp {
    constructor() {
        this.countries = [];
        this.topList = null;
        this.selectedCountry = localStorage.getItem('selected_country') || 'auto';
        this.init();
    }

    async init() {
        this.setupEvents();
        await this.loadCountries();
        await this.loadTopList();
        this.updateCountryDisplay();
    }

    setupEvents() {
        const countryBtn = document.getElementById('countrySelector');
        const modal = document.getElementById('countryModal');
        const closeBtn = document.getElementById('closeModal');
        const retryBtn = document.getElementById('retryBtn');

        if (countryBtn) {
            countryBtn.onclick = () => this.showModal();
        }

        if (closeBtn) {
            closeBtn.onclick = () => this.hideModal();
        }

        if (modal) {
            modal.onclick = (e) => {
                if (e.target === modal) this.hideModal();
            };
        }

        if (retryBtn) {
            retryBtn.onclick = () => this.loadTopList();
        }

        document.onkeydown = (e) => {
            if (e.key === 'Escape') this.hideModal();
        };
    }

    async loadCountries() {
        try {
            const response = await api.getCountries();
            if (response.data) {
                this.countries = response.data;
                this.renderCountryList();
            }
        } catch (error) {
            console.log('Countries load failed:', error);
        }
    }

    async loadTopList() {
        this.showLoading();
        try {
            console.log('Loading toplist for country:', this.selectedCountry);
            const response = await api.getTopList();
            console.log('API response:', response);

            if (response.data) {
                this.topList = response.data;
                console.log('TopList data:', this.topList);
                this.renderTopList();
                this.showContent();
            } else {
                throw new Error('No data');
            }
        } catch (error) {
            console.log('TopList load failed:', error);
            this.showError(error.message);
        }
    }

    renderTopList() {
        const grid = document.getElementById('toplistGrid');
        const count = document.getElementById('brandCount');

        if (!grid) return;

        this.updateCountryInfo();

        const entries = this.topList.entries || [];
        if (count) count.textContent = entries.length;

        grid.innerHTML = '';

        if (entries.length === 0) {
            grid.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h3>No brands found</h3>
                    <p>No brands configured for this region.</p>
                </div>
            `;
            return;
        }

        entries.forEach((entry, index) => {
            const brand = entry.brand;
            const position = entry.position || (index + 1);
            const card = this.createBrandCard(brand, position);
            grid.appendChild(card);
        });
    }

    updateCountryInfo() {
        const name = document.getElementById('countryName');
        const desc = document.getElementById('countryDescription');

        if (this.topList.country) {
            const country = this.topList.country;
            if (name) name.textContent = country.name;
            if (desc) desc.textContent = `Showing top brands for ${country.name}`;
        } else {
            if (name) name.textContent = 'Global';
            if (desc) desc.textContent = 'Showing top brands worldwide';
        }
    }

    createBrandCard(brand, position) {
        const card = document.createElement('div');
        card.className = 'brand-card';

        const rating = brand.rating || 0;
        const stars = this.getStars(rating);

        card.innerHTML = `
            <div class="brand-position">${position}</div>
            <img src="${brand.brand_image || this.getPlaceholder()}"
                 alt="${brand.brand_name}"
                 class="brand-image"
                 onerror="this.src='${this.getPlaceholder()}'">
            <h3 class="brand-name">${brand.brand_name}</h3>
            <div class="brand-rating">
                <span class="rating-stars">${stars}</span>
                <span class="rating-value">${rating}/100</span>
            </div>
        `;

        return card;
    }

    getPlaceholder() {
        const svg = `data:image/svg+xml;base64,${btoa(`
            <svg width="80" height="80" xmlns="http://www.w3.org/2000/svg">
                <rect width="80" height="80" fill="#f1f5f9"/>
                <text x="40" y="45" text-anchor="middle" fill="#6b7280" font-family="Arial" font-size="12">Brand</text>
            </svg>
        `)}`;
        return svg;
    }

    renderCountryList() {
        const list = document.getElementById('countryList');
        if (!list) return;

        list.innerHTML = '';

        this.countries.forEach(country => {
            const option = document.createElement('div');
            option.className = 'country-option';
            option.dataset.country = country.iso_code;

            option.innerHTML = `
                <span class="country-name">${country.name}</span>
            `;

            option.onclick = () => this.selectCountry(country.iso_code, country.name);
            list.appendChild(option);
        });
    }

    selectCountry(code, name) {
        this.selectedCountry = code;
        localStorage.setItem('selected_country', code);
        this.updateCountryDisplay();
        this.hideModal();
        this.loadTopList();
        showToast(`Switched to ${name}`, 'success');
    }

    updateCountryDisplay() {
        const current = document.getElementById('currentCountry');
        if (!current) return;

        if (this.selectedCountry === 'auto') {
            current.innerHTML = 'Auto';
        } else {
            const country = this.countries.find(c => c.iso_code === this.selectedCountry);
            if (country) {
                current.innerHTML = country.name;
            }
        }
    }

    showModal() {
        const modal = document.getElementById('countryModal');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    hideModal() {
        const modal = document.getElementById('countryModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    showLoading() {
        this.hideAll();
        const loading = document.getElementById('loadingState');
        if (loading) loading.style.display = 'block';
    }

    showError(msg) {
        this.hideAll();
        const error = document.getElementById('errorState');
        const message = document.getElementById('errorMessage');
        if (error) error.style.display = 'block';
        if (message) message.textContent = msg;
    }

    showContent() {
        this.hideAll();
        const content = document.getElementById('mainContent');
        if (content) content.style.display = 'block';
    }

    hideAll() {
        const states = ['loadingState', 'errorState', 'mainContent'];
        states.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
    }



    getStars(rating) {
        const max = 5;
        const percent = (rating / 100) * max;
        const full = Math.floor(percent);
        const half = percent % 1 >= 0.5;

        let stars = '★'.repeat(full);
        if (half) stars += '☆';
        const empty = max - full - (half ? 1 : 0);
        stars += '☆'.repeat(empty);
        return stars;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new TopListApp();
});

document.addEventListener('click', (e) => {
    if (e.target.closest('[data-country="auto"]')) {
        localStorage.setItem('selected_country', 'auto');
        location.reload();
    }
});
