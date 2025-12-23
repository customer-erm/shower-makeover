/**
 * Shower Makeover Pro - Premium JavaScript
 * Handles all interactions, API calls, and conversion optimization
 */

(function($) {
    'use strict';

    // State
    let uploadedFile = null;
    let originalImageData = null;
    let imageWidth = 0;
    let imageHeight = 0;

    // Initialize
    function init() {
        wireSelections();
        wireUpload();
        wireForm();
        wireTips();
        wireResults();
        updateProgress();
    }

    // ==========================================
    // SELECTIONS
    // ==========================================
    function wireSelections() {
        // Base type (tub vs floor)
        $(document).on('change', '.sm-base-option input', function() {
            $('.sm-base-option').removeClass('selected');
            $(this).closest('.sm-base-option').addClass('selected');
            updateProgress();
        });

        // Cards (layout, glass)
        $(document).on('change', '.sm-card input', function() {
            const name = $(this).attr('name');
            $(`.sm-card input[name="${name}"]`).closest('.sm-card').removeClass('selected');
            $(this).closest('.sm-card').addClass('selected');
            updateProgress();
        });

        // Swatches (hardware)
        $(document).on('change', '.sm-swatch input', function() {
            $('.sm-swatch').removeClass('selected');
            $(this).closest('.sm-swatch').addClass('selected');
            updateProgress();
        });

        // Pills (handle)
        $(document).on('change', '.sm-pill input', function() {
            $('.sm-pill').removeClass('selected');
            $(this).closest('.sm-pill').addClass('selected');
            updateProgress();
        });

        // Upgrades
        $(document).on('change', '.sm-upgrade input', function() {
            updateProgress();
        });
    }

    // ==========================================
    // PROGRESS TRACKING
    // ==========================================
    function updateProgress() {
        let steps = 0;
        
        if ($('input[name="base_type"]:checked').length) steps++;
        if ($('input[name="layout"]:checked').length) steps++;
        if ($('input[name="glass"]:checked').length) steps++;
        if ($('input[name="hardware"]:checked').length) steps++;
        if ($('input[name="handle"]:checked').length) steps++;
        // Step 6 (upgrades) always counts
        steps++;
        if (uploadedFile) steps++;
        
        const pct = Math.round((steps / 7) * 100);
        $('#sm-progress-fill').css('width', pct + '%');
        $('#sm-progress-pct').text(pct);
    }

    // ==========================================
    // FILE UPLOAD
    // ==========================================
    function wireUpload() {
        const $zone = $('#sm-upload-zone');
        const $input = $('#sm-file-input');
        const $prompt = $('#sm-upload-prompt');
        const $preview = $('#sm-upload-preview');
        const $previewImg = $('#sm-preview-img');

        // Drag events
        $zone.on('dragover dragenter', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        }).on('dragleave dragend drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        }).on('drop', function(e) {
            const files = e.originalEvent.dataTransfer.files;
            if (files.length) handleFile(files[0]);
        });

        // Input change
        $input.on('change', function() {
            if (this.files.length) handleFile(this.files[0]);
        });

        // Remove
        $('#sm-preview-remove').on('click', function(e) {
            e.stopPropagation();
            clearUpload();
        });

        function handleFile(file) {
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Please upload a JPEG, PNG, or WebP image.');
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                alert('Image must be under 10MB.');
                return;
            }

            uploadedFile = file;

            const reader = new FileReader();
            reader.onload = function(e) {
                originalImageData = e.target.result;
                
                // Get dimensions
                const img = new Image();
                img.onload = function() {
                    imageWidth = img.naturalWidth;
                    imageHeight = img.naturalHeight;
                };
                img.src = e.target.result;
                
                $previewImg.attr('src', e.target.result);
                $prompt.hide();
                $preview.addClass('show');
                updateProgress();
            };
            reader.readAsDataURL(file);
        }

        function clearUpload() {
            uploadedFile = null;
            originalImageData = null;
            imageWidth = 0;
            imageHeight = 0;
            $input.val('');
            $previewImg.attr('src', '');
            $preview.removeClass('show');
            $prompt.show();
            updateProgress();
        }

        window.smClearUpload = clearUpload;
    }

    // ==========================================
    // TIPS TOGGLE
    // ==========================================
    function wireTips() {
        $('#sm-tips-toggle').on('click', function() {
            $(this).toggleClass('active');
            $('#sm-tips-content').toggleClass('show');
        });
    }

    // ==========================================
    // FORM SUBMISSION
    // ==========================================
    function wireForm() {
        $('#sm-form').on('submit', function(e) {
            e.preventDefault();

            if (!uploadedFile) {
                alert('Please upload a photo of your bathroom first.');
                // Scroll to upload on mobile
                if ($(window).width() < 1024) {
                    $('html, body').animate({
                        scrollTop: $('.sm-step-upload').offset().top - 20
                    }, 500);
                }
                return;
            }

            const formData = new FormData();
            formData.append('action', 'sm_transform_image');
            formData.append('nonce', smConfig.nonce);
            formData.append('image', uploadedFile);
            formData.append('image_width', imageWidth);
            formData.append('image_height', imageHeight);
            formData.append('base_type', $('input[name="base_type"]:checked').val());
            formData.append('layout', $('input[name="layout"]:checked').val());
            formData.append('glass', $('input[name="glass"]:checked').val());
            formData.append('hardware', $('input[name="hardware"]:checked').val());
            formData.append('handle', $('input[name="handle"]:checked').val());

            $('input[name="upgrades[]"]:checked').each(function() {
                formData.append('upgrades[]', $(this).val());
            });

            showLoading();

            $.ajax({
                url: smConfig.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 120000,
                success: function(response) {
                    if (response.success) {
                        showResults(response.data);
                    } else {
                        showError(response.data?.message || 'Something went wrong. Please try again.');
                    }
                },
                error: function(xhr, status) {
                    let msg = 'Connection error. Please try again.';
                    if (status === 'timeout') msg = 'Request timed out. Try a smaller image.';
                    showError(msg);
                }
            });
        });
    }

    // ==========================================
    // UI STATES
    // ==========================================
    function showLoading() {
        $('#sm-placeholder').hide();
        $('#sm-results').removeClass('show');
        $('#sm-error').removeClass('show');
        $('#sm-loading').addClass('show');
        
        $('#sm-submit-btn').addClass('loading').prop('disabled', true);
        
        // Scroll to results panel on mobile
        if ($(window).width() < 1024) {
            $('html, body').animate({
                scrollTop: $('#sm-results-panel').offset().top - 20
            }, 500);
        }
    }

    function hideLoading() {
        $('#sm-loading').removeClass('show');
        $('#sm-submit-btn').removeClass('loading').prop('disabled', false);
    }

    function showError(message) {
        hideLoading();
        $('#sm-error-msg').text(message);
        $('#sm-error').addClass('show');
    }

    function showResults(data) {
        hideLoading();
        
        // Set images
        $('#sm-before-img').attr('src', originalImageData);
        $('#sm-after-img').attr('src', data.image);
        
        // Parse and display dimensions from AI response
        if (data.aiRecommendation) {
            parseDimensions(data.aiRecommendation);
            formatAIResponse(data.aiRecommendation);
        } else {
            $('#sm-dims-value').html('<span class="sm-dims-loading">Dimensions not available</span>');
            $('#sm-ai-content').html('<p>Your custom shower design is ready! Schedule a consultation for professional measurements and an exact quote.</p>');
        }
        
        // Build summary tags
        const $summary = $('#sm-summary-bar').empty();
        if (data.selections) {
            if (data.selections.baseType) {
                $summary.append(`<span class="sm-summary-tag">${data.selections.baseType}</span>`);
            }
            $summary.append(`<span class="sm-summary-tag">${data.selections.layout}</span>`);
            $summary.append(`<span class="sm-summary-tag">${data.selections.glass} Glass</span>`);
            $summary.append(`<span class="sm-summary-tag">${data.selections.hardware}</span>`);
            $summary.append(`<span class="sm-summary-tag">${data.selections.handle}</span>`);
            if (data.selections.upgrades && data.selections.upgrades.length) {
                data.selections.upgrades.forEach(u => {
                    $summary.append(`<span class="sm-summary-tag">${u}</span>`);
                });
            }
        }
        
        // Show results
        $('#sm-results').addClass('show');
        
        // Initialize comparison slider
        initComparison();
        
        // Scroll on mobile
        if ($(window).width() < 1024) {
            $('html, body').animate({
                scrollTop: $('#sm-results-panel').offset().top - 20
            }, 500);
        }
    }
    
    // Parse dimensions from AI response
    function parseDimensions(text) {
        const $dimsValue = $('#sm-dims-value');
        
        // Try the new DIMENSIONS: format first
        const newFormatMatch = text.match(/DIMENSIONS:\s*(\d+)[""]?\s*W\s*x\s*(\d+)[""]?\s*D\s*x\s*(\d+)[""]?\s*H/i);
        if (newFormatMatch) {
            const width = newFormatMatch[1];
            const depth = newFormatMatch[2];
            const height = newFormatMatch[3];
            
            $dimsValue.html(`
                <div class="sm-dims-main">${width}" W × ${depth}" D × ${height}" H</div>
                <div class="sm-dims-breakdown">
                    <span><strong>${width}"</strong> width</span>
                    <span><strong>${depth}"</strong> depth</span>
                    <span><strong>${height}"</strong> height</span>
                </div>
            `);
            return;
        }
        
        // Try 2-dimension format (width x height only, no depth for alcoves)
        const twoDimMatch = text.match(/DIMENSIONS:\s*(\d+)[""]?\s*W\s*x\s*(\d+)[""]?\s*H/i);
        if (twoDimMatch) {
            const width = twoDimMatch[1];
            const height = twoDimMatch[2];
            
            $dimsValue.html(`
                <div class="sm-dims-main">${width}" W × ${height}" H</div>
                <div class="sm-dims-breakdown">
                    <span><strong>${width}"</strong> width</span>
                    <span><strong>${height}"</strong> height</span>
                </div>
            `);
            return;
        }
        
        // Fallback patterns
        const patterns = [
            /(\d+)[""]?\s*x\s*(\d+)[""]?\s*x\s*(\d+)[""]?/i,
            /(\d+)["']\s*(?:wide|width|W)\s*x\s*(\d+)["']\s*(?:deep|depth|D)\s*x\s*(\d+)["']\s*(?:tall|height|H)/i,
            /width:?\s*(\d+)[""]?.*depth:?\s*(\d+)[""]?.*height:?\s*(\d+)[""]?/i,
            /Estimated dimensions?:?\s*(\d+)[""]?\s*x\s*(\d+)[""]?\s*x\s*(\d+)[""]?/i
        ];
        
        let match = null;
        for (const pattern of patterns) {
            match = text.match(pattern);
            if (match) break;
        }
        
        if (match) {
            const width = match[1];
            const depth = match[2];
            const height = match[3];
            
            $dimsValue.html(`
                <div class="sm-dims-main">${width}" × ${depth}" × ${height}"</div>
                <div class="sm-dims-breakdown">
                    <span><strong>${width}"</strong> width</span>
                    <span><strong>${depth}"</strong> depth</span>
                    <span><strong>${height}"</strong> height</span>
                </div>
            `);
        } else {
            // Try to find any 2-dimension pattern
            const simpleMatch = text.match(/(\d+)[""]?\s*x\s*(\d+)[""]?/i);
            if (simpleMatch) {
                $dimsValue.html(`
                    <div class="sm-dims-main">${simpleMatch[1]}" × ${simpleMatch[2]}"</div>
                    <div class="sm-dims-breakdown">
                        <span>Opening dimensions</span>
                    </div>
                `);
            } else {
                // This should rarely happen with the new prompt
                $dimsValue.html(`
                    <div class="sm-dims-main">Measurement Required</div>
                    <div class="sm-dims-breakdown">
                        <span>Professional measurement needed</span>
                    </div>
                `);
            }
        }
    }
    
    // Format AI response for display
    function formatAIResponse(text) {
        // Remove dimension lines from display text
        let cleaned = text
            .replace(/---[\s\S]*?---/g, '') // Remove the dimension block
            .replace(/DIMENSIONS:[^\n]*/gi, '')
            .replace(/Estimated dimensions?:?[^\n]*/gi, '')
            .replace(/TASK \d[^\n]*/gi, '')
            .replace(/OUTPUT:?[^\n]*/gi, '')
            .replace(/CRITICAL[^\n]*/gi, '')
            .replace(/MANDATORY[^\n]*/gi, '')
            .replace(/REQUIRED FORMAT[^\n]*/gi, '')
            .replace(/Example:[^\n]*/gi, '')
            .trim();
        
        // Split into paragraphs and format
        const paragraphs = cleaned
            .split(/\n\n+/)
            .map(p => p.trim())
            .filter(p => p.length > 20)
            .map(p => {
                // Convert **text** to <strong>text</strong>
                p = p.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
                // Clean up any remaining artifacts
                p = p.replace(/\n/g, ' ').replace(/\s+/g, ' ');
                return '<p>' + p + '</p>';
            })
            .slice(0, 4);
        
        if (paragraphs.length > 0) {
            $('#sm-ai-content').html(paragraphs.join(''));
        } else {
            $('#sm-ai-content').html('<p>Your custom shower visualization is complete! This design shows how a frameless glass enclosure would look in your space. Schedule a free consultation to get exact measurements and finalize your project.</p>');
        }
    }

    // ==========================================
    // COMPARISON SLIDER
    // ==========================================
    function initComparison() {
        const $comp = $('#sm-comparison');
        const $before = $('.sm-comp-before');
        const $beforeImg = $('#sm-before-img');
        const $handle = $('#sm-comp-handle');
        
        let isDragging = false;

        function updateSlider(pct) {
            pct = Math.max(5, Math.min(95, pct));
            
            // Update before width
            $before.css('width', pct + '%');
            
            // Scale before image to match
            const compWidth = $comp.width();
            $beforeImg.css('width', compWidth + 'px');
            
            // Move handle
            $handle.css('left', pct + '%');
        }

        function getPctFromX(x) {
            const rect = $comp[0].getBoundingClientRect();
            return ((x - rect.left) / rect.width) * 100;
        }

        // Mouse
        $handle.on('mousedown', function(e) {
            e.preventDefault();
            isDragging = true;
            $('body').css('cursor', 'ew-resize');
        });

        $(document).on('mousemove', function(e) {
            if (isDragging) updateSlider(getPctFromX(e.clientX));
        }).on('mouseup', function() {
            if (isDragging) {
                isDragging = false;
                $('body').css('cursor', '');
            }
        });

        // Touch
        $handle.on('touchstart', function(e) {
            e.preventDefault();
            isDragging = true;
        });

        $(document).on('touchmove', function(e) {
            if (isDragging && e.originalEvent.touches.length) {
                updateSlider(getPctFromX(e.originalEvent.touches[0].clientX));
            }
        }).on('touchend', function() {
            isDragging = false;
        });

        // Click
        $comp.on('click', function(e) {
            if (!$(e.target).closest('.sm-comp-handle').length) {
                updateSlider(getPctFromX(e.clientX));
            }
        });

        // Initialize
        updateSlider(50);
        
        // Recalculate on resize
        $(window).on('resize', function() {
            const compWidth = $comp.width();
            $beforeImg.css('width', compWidth + 'px');
        });
    }

    // ==========================================
    // RESULTS INTERACTIONS
    // ==========================================
    function wireResults() {
        $('#sm-error-retry, #sm-restart').on('click', function() {
            if (typeof window.smClearUpload === 'function') {
                window.smClearUpload();
            }
            
            $('#sm-results').removeClass('show');
            $('#sm-error').removeClass('show');
            $('#sm-placeholder').show();
            $('#sm-dims-card').show();
            
            if ($(window).width() < 1024) {
                $('html, body').animate({
                    scrollTop: $('#sm-app').offset().top
                }, 500);
            }
        });
    }

    // Initialize on ready
    $(document).ready(init);

})(jQuery);
