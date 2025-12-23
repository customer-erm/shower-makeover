<?php
/**
 * Shower Makeover Pro - Ultra Premium Designer Template
 */
$sm = ShowerMakeoverPro::get_instance();
$layouts = $sm->get_layouts();
$glass_types = $sm->get_glass_types();
$hardware = $sm->get_hardware_finishes();
$handles = $sm->get_handle_styles();
$upgrades = $sm->get_upgrades();
$settings = get_option('sm_settings', []);
?>

<div class="sm-app" id="sm-app">
    <!-- Ambient Background -->
    <div class="sm-ambient">
        <div class="sm-orb sm-orb-1"></div>
        <div class="sm-orb sm-orb-2"></div>
        <div class="sm-orb sm-orb-3"></div>
    </div>

    <!-- Main Container -->
    <div class="sm-container" id="sm-container">
        
        <!-- Design Panel (Left) -->
        <div class="sm-design-panel" id="sm-design-panel">
            <div class="sm-panel-header">
                <h1>Design Your <span class="sm-gradient-text">Dream Shower</span></h1>
                <p>See your custom frameless glass shower in your actual bathroom</p>
            </div>

            <!-- Progress Indicator -->
            <div class="sm-progress">
                <div class="sm-progress-bar">
                    <div class="sm-progress-fill" id="sm-progress-fill"></div>
                </div>
                <span class="sm-progress-text"><span id="sm-progress-pct">0</span>% Complete</span>
            </div>

            <form id="sm-form" enctype="multipart/form-data">
                
                <!-- Step 1: Base Type -->
                <section class="sm-step" data-step="1">
                    <div class="sm-step-header">
                        <span class="sm-step-badge">1</span>
                        <div>
                            <h2>What's Your Starting Point?</h2>
                            <p>Tell us about your current setup</p>
                        </div>
                    </div>
                    <div class="sm-base-options">
                        <label class="sm-base-option selected">
                            <input type="radio" name="base_type" value="tub" checked>
                            <div class="sm-base-icon">
                                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="4" y="20" width="40" height="16" rx="2"/>
                                    <path d="M8 20V12a4 4 0 0 1 4-4h4"/>
                                    <circle cx="16" cy="8" r="2"/>
                                    <path d="M40 28v8"/>
                                </svg>
                            </div>
                            <div class="sm-base-content">
                                <strong>Keep My Tub</strong>
                                <span>Glass enclosure sits on existing tub</span>
                            </div>
                        </label>
                        <label class="sm-base-option">
                            <input type="radio" name="base_type" value="floor">
                            <div class="sm-base-icon">
                                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="8" y="8" width="24" height="32" rx="1"/>
                                    <line x1="8" y1="40" x2="40" y2="40"/>
                                    <circle cx="28" cy="24" r="2"/>
                                    <path d="M16 12v4M20 12v4"/>
                                </svg>
                            </div>
                            <div class="sm-base-content">
                                <strong>Glass to Floor</strong>
                                <span>Full shower conversion with tile base</span>
                            </div>
                        </label>
                    </div>
                </section>

                <!-- Step 2: Layout -->
                <section class="sm-step" data-step="2">
                    <div class="sm-step-header">
                        <span class="sm-step-badge">2</span>
                        <div>
                            <h2>Choose Your Layout</h2>
                            <p>Select the style that fits your space</p>
                        </div>
                    </div>
                    <div class="sm-card-grid sm-layout-grid">
                        <?php foreach ($layouts as $i => $l): ?>
                        <label class="sm-card <?php echo $i === 2 ? 'selected' : ''; ?>">
                            <input type="radio" name="layout" value="<?php echo esc_attr($l['id']); ?>" <?php echo $i === 2 ? 'checked' : ''; ?>>
                            <div class="sm-card-visual">
                                <div class="sm-layout-icon" data-layout="<?php echo esc_attr($l['id']); ?>"></div>
                            </div>
                            <div class="sm-card-content">
                                <strong><?php echo esc_html($l['name']); ?></strong>
                                <span><?php echo esc_html($l['desc']); ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Step 3: Glass -->
                <section class="sm-step" data-step="3">
                    <div class="sm-step-header">
                        <span class="sm-step-badge">3</span>
                        <div>
                            <h2>Select Glass Type</h2>
                            <p>Premium 3/8" tempered safety glass</p>
                        </div>
                    </div>
                    <div class="sm-card-grid sm-glass-grid">
                        <?php foreach ($glass_types as $i => $g): ?>
                        <label class="sm-card sm-card-compact <?php echo $i === 0 ? 'selected' : ''; ?>">
                            <input type="radio" name="glass" value="<?php echo esc_attr($g['id']); ?>" <?php echo $i === 0 ? 'checked' : ''; ?>>
                            <div class="sm-glass-swatch" data-glass="<?php echo esc_attr($g['id']); ?>"></div>
                            <strong><?php echo esc_html($g['name']); ?></strong>
                            <span><?php echo esc_html($g['desc']); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Step 4: Hardware -->
                <section class="sm-step" data-step="4">
                    <div class="sm-step-header">
                        <span class="sm-step-badge">4</span>
                        <div>
                            <h2>Hardware Finish</h2>
                            <p>Solid brass construction with lifetime warranty</p>
                        </div>
                    </div>
                    <div class="sm-swatch-row">
                        <?php foreach ($hardware as $i => $h): ?>
                        <label class="sm-swatch <?php echo $i === 0 ? 'selected' : ''; ?>" title="<?php echo esc_attr($h['name']); ?>">
                            <input type="radio" name="hardware" value="<?php echo esc_attr($h['id']); ?>" <?php echo $i === 0 ? 'checked' : ''; ?>>
                            <span class="sm-swatch-color" style="background: <?php echo esc_attr($h['color']); ?>;"></span>
                            <span class="sm-swatch-name"><?php echo esc_html($h['name']); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Step 5: Handle -->
                <section class="sm-step" data-step="5">
                    <div class="sm-step-header">
                        <span class="sm-step-badge">5</span>
                        <div>
                            <h2>Handle Style</h2>
                        </div>
                    </div>
                    <div class="sm-pill-row">
                        <?php foreach ($handles as $i => $h): ?>
                        <label class="sm-pill <?php echo $i === 0 ? 'selected' : ''; ?>">
                            <input type="radio" name="handle" value="<?php echo esc_attr($h['id']); ?>" <?php echo $i === 0 ? 'checked' : ''; ?>>
                            <span><?php echo esc_html($h['name']); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Step 6: Upgrades -->
                <section class="sm-step" data-step="6">
                    <div class="sm-step-header">
                        <span class="sm-step-badge">6</span>
                        <div>
                            <h2>Premium Upgrades</h2>
                            <p>Enhance your investment</p>
                        </div>
                    </div>
                    <div class="sm-upgrade-list">
                        <?php foreach ($upgrades as $u): ?>
                        <label class="sm-upgrade">
                            <input type="checkbox" name="upgrades[]" value="<?php echo esc_attr($u['id']); ?>">
                            <span class="sm-upgrade-check"></span>
                            <div class="sm-upgrade-info">
                                <strong><?php echo esc_html($u['name']); ?></strong>
                                <span><?php echo esc_html($u['desc']); ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Step 7: Upload -->
                <section class="sm-step sm-step-upload" data-step="7">
                    <div class="sm-step-header">
                        <span class="sm-step-badge">7</span>
                        <div>
                            <h2>Upload Your Bathroom</h2>
                            <p>Our AI will show your new shower in seconds</p>
                        </div>
                    </div>

                    <!-- Photo Tips -->
                    <div class="sm-tips-bar" id="sm-tips-toggle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                        <span>Photo tips for best results</span>
                        <svg class="sm-tips-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <div class="sm-tips-content" id="sm-tips-content">
                        <div class="sm-tip"><span>📐</span><p><strong>Face the shower directly</strong> — straight-on angle works best</p></div>
                        <div class="sm-tip"><span>📏</span><p><strong>Show floor to ceiling</strong> — capture the full space</p></div>
                        <div class="sm-tip"><span>💡</span><p><strong>Good lighting</strong> — turn on lights, open blinds</p></div>
                        <div class="sm-tip"><span>🧹</span><p><strong>Clear the clutter</strong> — remove bottles and towels</p></div>
                    </div>

                    <div class="sm-upload-zone" id="sm-upload-zone">
                        <input type="file" id="sm-file-input" name="image" accept="image/jpeg,image/png,image/webp">
                        <div class="sm-upload-prompt" id="sm-upload-prompt">
                            <div class="sm-upload-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                                    <path d="M12 12v9"/><path d="m16 16-4-4-4 4"/>
                                </svg>
                            </div>
                            <p><strong>Drop your photo here</strong> or click to browse</p>
                            <span>JPEG, PNG or WebP • Max 10MB</span>
                        </div>
                        <div class="sm-upload-preview" id="sm-upload-preview">
                            <img id="sm-preview-img" src="" alt="">
                            <button type="button" class="sm-preview-remove" id="sm-preview-remove">×</button>
                        </div>
                    </div>
                </section>

                <button type="submit" class="sm-submit-btn" id="sm-submit-btn">
                    <span class="sm-btn-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 3l14 9-14 9V3z"/></svg>
                        <span>See Your New Shower</span>
                    </span>
                    <span class="sm-btn-loading">
                        <span class="sm-spinner"></span>
                        <span>Creating your vision...</span>
                    </span>
                </button>

                <p class="sm-guarantee">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-7v2h2v-2h-2zm0-8v6h2V7h-2z"/></svg>
                    Free consultation • No obligation • Professional installation available
                </p>
            </form>
        </div>

        <!-- Results Panel (Right) -->
        <div class="sm-results-panel" id="sm-results-panel">
            
            <!-- Placeholder State -->
            <div class="sm-placeholder" id="sm-placeholder">
                <div class="sm-placeholder-visual">
                    <div class="sm-shower-illustration">
                        <div class="sm-shower-frame"></div>
                        <div class="sm-shower-glass"></div>
                        <div class="sm-shower-drops"><span></span><span></span><span></span></div>
                    </div>
                </div>
                <h3>Your Visualization Awaits</h3>
                <p>Complete your selections and upload a photo to see your beautiful new shower</p>
            </div>

            <!-- Loading State -->
            <div class="sm-loading" id="sm-loading">
                <div class="sm-loading-visual">
                    <div class="sm-loading-frame"></div>
                    <div class="sm-loading-door"></div>
                    <div class="sm-loading-drops">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <h3>Creating Your Vision</h3>
                <p>Our AI is designing your perfect shower...</p>
                <div class="sm-loading-bar"><div class="sm-loading-fill"></div></div>
                <span class="sm-loading-time">Usually takes 15-30 seconds</span>
            </div>

            <!-- Error State -->
            <div class="sm-error" id="sm-error">
                <div class="sm-error-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                </div>
                <h3>Something went wrong</h3>
                <p id="sm-error-msg">Please try again with a different photo.</p>
                <button type="button" class="sm-error-retry" id="sm-error-retry">Try Again</button>
            </div>

            <!-- Results State -->
            <div class="sm-results" id="sm-results">
                
                <!-- Comparison Slider -->
                <div class="sm-comparison" id="sm-comparison">
                    <div class="sm-comp-after"><img id="sm-after-img" src="" alt="After"></div>
                    <div class="sm-comp-before"><img id="sm-before-img" src="" alt="Before"></div>
                    <div class="sm-comp-labels">
                        <span class="sm-label-before">Your Current Space</span>
                        <span class="sm-label-after">Your New Shower</span>
                    </div>
                    <div class="sm-comp-handle" id="sm-comp-handle">
                        <div class="sm-handle-line"></div>
                        <div class="sm-handle-grip">
                            <svg viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                            <svg viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Info Cards Grid -->
                <div class="sm-info-grid">
                    
                    <!-- Estimated Dimensions -->
                    <div class="sm-dims-card" id="sm-dims-card">
                        <div class="sm-dims-header">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 21H3V3"/><path d="M21 3v18"/><path d="M3 21h18"/>
                                <path d="M7 17V7h10"/><path d="M7 7l10 10"/>
                            </svg>
                            <div>
                                <h4>Estimated Dimensions</h4>
                                <p>Based on AI analysis of your photo</p>
                            </div>
                        </div>
                        <div class="sm-dims-display" id="sm-dims-value">
                            <span class="sm-dims-loading">Calculating...</span>
                        </div>
                        <p class="sm-dims-disclaimer">*Final dimensions confirmed during in-home measurement</p>
                    </div>

                    <!-- Your Selections -->
                    <div class="sm-selections-card">
                        <h4>Your Design Selections</h4>
                        <div class="sm-summary-bar" id="sm-summary-bar"></div>
                    </div>
                </div>

                <!-- AI Consultation Card -->
                <div class="sm-ai-card" id="sm-ai-card">
                    <div class="sm-ai-header">
                        <div class="sm-ai-avatar">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                        </div>
                        <div>
                            <strong>Your Design Consultation</strong>
                            <span>AI-Powered Analysis</span>
                        </div>
                    </div>
                    <div class="sm-ai-content" id="sm-ai-content">
                        <p>Analyzing your space...</p>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="sm-next-steps">
                    <h4>What Happens Next?</h4>
                    <div class="sm-steps-list">
                        <div class="sm-next-step">
                            <span class="sm-step-num">1</span>
                            <div>
                                <strong>Schedule Your Free Consultation</strong>
                                <p>A glass specialist will contact you to schedule an in-home measurement at your convenience.</p>
                            </div>
                        </div>
                        <div class="sm-next-step">
                            <span class="sm-step-num">2</span>
                            <div>
                                <strong>Professional Measurement</strong>
                                <p>We'll take precise measurements and review glass options, hardware finishes, and layout details in person.</p>
                            </div>
                        </div>
                        <div class="sm-next-step">
                            <span class="sm-step-num">3</span>
                            <div>
                                <strong>Receive Your Custom Quote</strong>
                                <p>You'll get a detailed quote with exact pricing. Most installations are completed within 2-3 weeks.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="sm-cta-card">
                    <h3>Ready to Make It Real?</h3>
                    <p>Get an exact quote with a free professional measurement</p>
                    <a href="<?php echo esc_url($settings['cta_url'] ?? '#'); ?>" class="sm-cta-btn" id="sm-cta-btn">
                        Schedule Free Consultation
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <?php if (!empty($settings['company_phone'])): ?>
                    <a href="tel:<?php echo preg_replace('/[^0-9]/', '', $settings['company_phone']); ?>" class="sm-cta-phone">
                        Or call us: <strong><?php echo esc_html($settings['company_phone']); ?></strong>
                    </a>
                    <?php endif; ?>
                </div>

                <button type="button" class="sm-restart" id="sm-restart">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    Design Another Shower
                </button>
            </div>
        </div>
    </div>
</div>
