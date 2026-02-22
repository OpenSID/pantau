@push('css')
<style>
    /* Boundary layer control styles */
    .boundary-layer-control {
        background: rgba(255, 255, 255, 0.9);
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 1px 5px rgba(0,0,0,0.4);
        margin: 10px;
        max-width: 250px;
    }
    
    .boundary-layer-control h6 {
        margin: 0 0 10px 0;
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }
    
    .boundary-layer-control .form-group {
        margin-bottom: 10px;
    }
    
    .boundary-layer-control label {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 3px;
        display: block;
    }
    
    .boundary-layer-control .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .boundary-layer-control .checkbox-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }
    
    .boundary-layer-control .checkbox-item input[type="checkbox"] {
        margin: 0;
    }
    
    .boundary-legend {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #ddd;
    }
    
    .boundary-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 5px;
        font-size: 12px;
    }
    
    .boundary-legend-color {
        width: 20px;
        height: 3px;
        display: inline-block;
    }
    
    .loading-boundaries {
        text-align: center;
        padding: 10px;
        color: #666;
        font-size: 12px;
    }
    
    .spinner-boundaries {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@push('js')
<script>
/**
 * Wilayah Boundaries Layer Manager
 * Handles loading and display of administrative boundaries on Leaflet map
 */
class BoundariesLayerManager {
    constructor(map, options = {}) {
        this.map = map;
        this.options = {
            apiUrl: options.apiUrl || '/api/boundaries',
            cacheEnabled: options.cacheEnabled !== false,
            maxZoom: options.maxZoom || 14,
            styles: {
                prov: {
                    color: '#FF5722',
                    weight: 3,
                    opacity: 0.8,
                    fillOpacity: 0.1,
                    dashArray: '10, 10'
                },
                kab: {
                    color: '#2196F3',
                    weight: 2,
                    opacity: 0.7,
                    fillOpacity: 0.15
                },
                kec: {
                    color: '#4CAF50',
                    weight: 2,
                    opacity: 0.6,
                    fillOpacity: 0.2
                },
                kel: {
                    color: '#9C27B0',
                    weight: 1,
                    opacity: 0.5,
                    fillOpacity: 0.25
                }
            },
            ...options
        };
        
        this.layers = {};
        this.currentZoomLevel = map.getZoom();
        this.visibleLevels = new Set();
        this.cache = new Map();
        
        // Start with all layers hidden (do not auto-load)
        // this.init(); // Removed - user must manually select
    }
    
    init() {
        // Track zoom changes
        this.map.on('zoomend', () => this.onZoomChange());
        
        // Initialize with current zoom
        this.updateVisibleLayers();
    }
    
    /**
     * Create control panel for boundary layers
     */
    createControl() {
        const control = L.control({ position: 'topright' });
        
        control.onAdd = () => {
            const div = L.DomUtil.create('div', 'boundary-layer-control');
            div.innerHTML = `
                <h6><i class="fas fa-map"></i> Batas Wilayah</h6>
                <div class="form-group">
                    <div class="checkbox-group">
                        <label class="checkbox-item">
                            <input type="checkbox" id="boundary-prov" value="prov">
                            <span>Provinsi</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" id="boundary-kab" value="kab">
                            <span>Kabupaten</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" id="boundary-kec" value="kec">
                            <span>Kecamatan</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" id="boundary-kel" value="kel">
                            <span>Kelurahan/Desa</span>
                        </label>
                    </div>
                </div>
                <div class="boundary-legend">
                    <div class="boundary-legend-item">
                        <span class="boundary-legend-color" style="background: #FF5722;"></span>
                        <span>Provinsi</span>
                    </div>
                    <div class="boundary-legend-item">
                        <span class="boundary-legend-color" style="background: #2196F3;"></span>
                        <span>Kabupaten</span>
                    </div>
                    <div class="boundary-legend-item">
                        <span class="boundary-legend-color" style="background: #4CAF50;"></span>
                        <span>Kecamatan</span>
                    </div>
                    <div class="boundary-legend-item">
                        <span class="boundary-legend-color" style="background: #9C27B0;"></span>
                        <span>Kelurahan</span>
                    </div>
                </div>
            `;
            
            // Prevent map clicks from propagating
            L.DomEvent.disableClickPropagation(div);
            
            // Add event listeners
            div.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    if (e.target.checked) {
                        this.showLayer(e.target.value);
                    } else {
                        this.hideLayer(e.target.value);
                    }
                });
            });
            
            return div;
        };
        
        control.addTo(this.map);
        this.control = control;
        
        return control;
    }
    
    /**
     * Load boundary data from API
     */
    async loadLevel(level) {
        if (this.layers[level]) {
            return this.layers[level];
        }
        
        // Check cache
        if (this.options.cacheEnabled && this.cache.has(level)) {
            const cached = this.cache.get(level);
            if (cached.timestamp > Date.now() - 3600000) { // 1 hour cache
                return this.addLayerToMap(cached.data, level);
            }
        }
        
        // Show loading indicator
        this.showLoading(level);
        
        try {
            const response = await fetch(`${this.options.apiUrl}/geojson/${level}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                // Cache the data
                if (this.options.cacheEnabled) {
                    this.cache.set(level, {
                        data: result.data,
                        timestamp: Date.now()
                    });
                }
                
                return this.addLayerToMap(result.data, level);
            } else {
                console.error('Failed to load boundaries:', result.message);
                return null;
            }
        } catch (error) {
            console.error('Error loading boundaries:', error);
            return null;
        } finally {
            this.hideLoading(level);
        }
    }
    
    /**
     * Add GeoJSON layer to map
     */
    addLayerToMap(geojson, level) {
        const style = this.options.styles[level] || {};
        
        const layer = L.geoJSON(geojson, {
            style: style,
            onEachFeature: (feature, layer) => {
                const props = feature.properties;
                const popupContent = `
                    <div style="min-width: 200px;">
                        <h6 style="margin: 0 0 10px 0; color: #333;">
                            ${props.name || props.new_name || 'Wilayah ' + level.toUpperCase()}
                        </h6>
                        <table style="width: 100%; font-size: 12px;">
                            <tr>
                                <td style="font-weight: bold;">Kode:</td>
                                <td>${props.kode || '-'}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Level:</td>
                                <td>${this.getLevelName(level)}</td>
                            </tr>
                            ${props.lat && props.lng ? `
                            <tr>
                                <td style="font-weight: bold;">Koordinat:</td>
                                <td>${props.lat.toFixed(4)}, ${props.lng.toFixed(4)}</td>
                            </tr>
                            ` : ''}
                        </table>
                    </div>
                `;
                layer.bindPopup(popupContent);
                
                // Hover effect
                layer.on('mouseover', function() {
                    this.setStyle({
                        fillOpacity: style.fillOpacity ? Math.min(style.fillOpacity + 0.1, 0.5) : 0.3,
                        weight: style.weight ? style.weight + 1 : 2
                    });
                });
                
                layer.on('mouseout', function() {
                    this.setStyle({
                        fillOpacity: style.fillOpacity || 0.2,
                        weight: style.weight || 2
                    });
                });
            }
        });
        
        this.layers[level] = layer;
        this.visibleLevels.add(level);
        
        return layer;
    }
    
    /**
     * Show boundary layer
     */
    async showLayer(level) {
        if (!this.layers[level]) {
            await this.loadLevel(level);
        }
        
        if (this.layers[level] && !this.map.hasLayer(this.layers[level])) {
            this.map.addLayer(this.layers[level]);
            this.visibleLevels.add(level);
        }
    }
    
    /**
     * Hide boundary layer
     */
    hideLayer(level) {
        if (this.layers[level] && this.map.hasLayer(this.layers[level])) {
            this.map.removeLayer(this.layers[level]);
            this.visibleLevels.delete(level);
        }
    }
    
    /**
     * Toggle boundary layer
     */
    toggleLayer(level) {
        if (this.visibleLevels.has(level)) {
            this.hideLayer(level);
        } else {
            this.showLayer(level);
        }
    }
    
    /**
     * Update visible layers based on zoom level
     */
    updateVisibleLayers() {
        const zoom = this.map.getZoom();
        
        // Auto-show/hide based on zoom
        if (zoom <= 6) {
            // Show only provinsi at low zoom
            if (!this.visibleLevels.has('prov')) this.showLayer('prov');
            ['kab', 'kec', 'kel'].forEach(l => this.hideLayer(l));
        } else if (zoom <= 10) {
            // Show provinsi and kabupaten
            if (!this.visibleLevels.has('prov')) this.showLayer('prov');
            if (!this.visibleLevels.has('kab')) this.showLayer('kab');
            ['kec', 'kel'].forEach(l => this.hideLayer(l));
        } else if (zoom <= 13) {
            // Show up to kecamatan
            ['prov', 'kab'].forEach(l => {
                if (!this.visibleLevels.has(l)) this.showLayer(l);
            });
            if (!this.visibleLevels.has('kec')) this.showLayer('kec');
            this.hideLayer('kel');
        } else {
            // Show all at high zoom
            ['prov', 'kab', 'kec', 'kel'].forEach(l => {
                if (!this.visibleLevels.has(l)) this.showLayer(l);
            });
        }
        
        // Update checkboxes
        this.updateCheckboxes();
    }
    
    /**
     * Handle zoom change event
     */
    onZoomChange() {
        const newZoom = this.map.getZoom();
        
        // Debounce heavy operations
        if (Math.abs(newZoom - this.currentZoomLevel) >= 2) {
            this.updateVisibleLayers();
            this.currentZoomLevel = newZoom;
        }
    }
    
    /**
     * Update checkbox states
     */
    updateCheckboxes() {
        ['prov', 'kab', 'kec', 'kel'].forEach(level => {
            const checkbox = document.getElementById(`boundary-${level}`);
            if (checkbox) {
                checkbox.checked = this.visibleLevels.has(level);
            }
        });
    }
    
    /**
     * Show loading indicator
     */
    showLoading(level) {
        // Could implement a loading indicator here
        console.log(`Loading ${level} boundaries...`);
    }
    
    /**
     * Hide loading indicator
     */
    hideLoading(level) {
        console.log(`${level} boundaries loaded`);
    }
    
    /**
     * Get human-readable level name
     */
    getLevelName(level) {
        const names = {
            prov: 'Provinsi',
            kab: 'Kabupaten/Kota',
            kec: 'Kecamatan',
            kel: 'Kelurahan/Desa'
        };
        return names[level] || level;
    }
    
    /**
     * Clear all layers
     */
    clearAll() {
        ['prov', 'kab', 'kec', 'kel'].forEach(level => {
            this.hideLayer(level);
        });
    }
    
    /**
     * Remove all layers from map
     */
    destroy() {
        this.clearAll();
        if (this.control) {
            this.map.removeControl(this.control);
        }
        this.layers = {};
        this.cache.clear();
    }
}

// Make available globally
window.BoundariesLayerManager = BoundariesLayerManager;
</script>
@endpush
