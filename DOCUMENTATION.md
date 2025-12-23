# Shower Makeover Pro - Plugin Documentation

**Version:** 2.0.0
**Author:** Elite Results Marketing
**License:** GPL v2 or later

---

## Table of Contents

1. [Overview](#overview)
2. [Installation & Setup](#installation--setup)
3. [User Guide](#user-guide)
4. [Developer Reference](#developer-reference)
5. [LLM Context for Development](#llm-context-for-development)

---

## Overview

### What This Plugin Does

Shower Makeover Pro is an AI-powered bathroom visualization plugin that enables homeowners to see their dream frameless glass shower enclosure installed in their actual bathroom space. Using Google's Gemini AI vision model, it generates photorealistic renderings based on user selections and uploaded bathroom photos.

### Key Features

- **AI-Powered Visualization** - Google Gemini 2.5 Flash generates photorealistic shower images
- **7-Step Design Wizard** - Intuitive customization process
- **Before/After Comparison** - Interactive slider to compare original and visualization
- **Automated Dimension Estimation** - AI analyzes photos to estimate shower dimensions
- **Drag-and-Drop Upload** - Easy image upload interface
- **Mobile Responsive** - Works on all devices

### Design Options

**Base Types:** Keep My Tub, Glass to Floor

**6 Layouts:** Single Door, Door+Panel, 90° Corner, Neo Angle, Walk-In, Sliding

**5 Glass Types:** Clear, Starphire, Frosted, Rain Glass, Gray Tint

**6 Hardware Finishes:** Matte Black, Brushed Nickel, Polished Chrome, Brushed Gold, Satin Brass, Oil Rubbed Bronze

**4 Handle Styles:** Ladder Pull, Square Ladder, C-Pull, Towel Bar

**3 Premium Upgrades:** ShieldGuard Coating, Steam Ready, Grid Pattern

---

## Installation & Setup

### Requirements

- WordPress 5.0+
- PHP 7.0+
- Google Gemini API key (required)
- Modern browser

### Installation Steps

1. **Upload Plugin**
   - Upload `shower-makeover` folder to `/wp-content/plugins/`
   - OR upload as ZIP via Plugins → Add New → Upload

2. **Activate**
   - Go to Plugins in WordPress admin
   - Click "Activate" on Shower Makeover Pro

3. **Configure API Key**

**Step 1: Get Google Gemini API Key**
1. Go to https://aistudio.google.com/apikey
2. Create a new API key
3. Copy the key

**Step 2: Configure Plugin**
1. Go to WordPress Admin → Shower Makeover
2. Paste API key in "Google Gemini API Key" field
3. Select model:
   - **Nano Banana (Fast):** `gemini-2.5-flash-image`
   - **Nano Banana Pro (Best):** `gemini-3-pro-image-preview`
4. Click "Test API" to verify connection
5. Add company information:
   - Company Name
   - Phone Number
   - CTA Button URL (consultation form link)
6. Save Settings

### Adding to Your Site

**Shortcode:**
```
[shower_makeover]
```

Place on any page or post where you want the visualization tool.

---

## User Guide

### The 7-Step Design Process

**Step 1: Base Type**
- **Keep My Tub** - Glass enclosure sits on existing bathtub
- **Glass to Floor** - Full shower conversion with tile floor

**Step 2: Layout Selection**
| Layout | Description | Upgrade |
|--------|-------------|---------|
| Single Door | Classic alcove with hinged door | $0 |
| Door + Panel | Wide opening, modern look | $200 |
| 90° Corner | L-shaped corner enclosure | $350 |
| Neo Angle | Diamond corner design | $450 |
| Walk-In | Doorless open entry | $300 |
| Sliding Door | Space-saving barn style | $250 |

**Step 3: Glass Type**
| Glass | Description | Upgrade |
|-------|-------------|---------|
| Clear | Crystal clarity | $0 |
| Starphire | Ultra-clear premium | $150 |
| Frosted | Maximum privacy | $100 |
| Rain Glass | Textured elegance | $125 |
| Gray Tint | Modern sophistication | $75 |

**Step 4: Hardware Finish**
- Matte Black, Brushed Nickel, Polished Chrome ($0)
- Brushed Gold, Satin Brass ($100)
- Oil Rubbed Bronze ($75)

**Step 5: Handle Style**
- Ladder Pull ($0), Square Ladder ($25)
- C-Pull ($35), Towel Bar ($50)

**Step 6: Premium Upgrades**
- ShieldGuard Coating - Lifetime easy-clean
- Steam Ready - Fully sealed enclosure
- Grid Pattern - Decorative grid lines

**Step 7: Photo Upload**
- Upload bathroom photo (JPEG, PNG, WebP)
- Max size: 10MB
- Drag-and-drop or click to browse

### Results Display

After processing, users see:
1. **Before/After Slider** - Interactive comparison
2. **Estimated Dimensions** - AI-extracted measurements
3. **Design Selections** - Summary of all choices
4. **AI Consultation** - Analysis and recommendations
5. **Next Steps** - How to proceed to consultation
6. **CTA Button** - Links to contact form

### Photo Tips for Best Results

- Capture full floor-to-ceiling view
- Include reference objects (toilet, vanity, tiles)
- Good lighting, minimal clutter
- 72-80° angle showing the shower space
- Clear view of existing shower/tub area

---

## Developer Reference

### File Structure

```
shower-makeover/
├── shower-makeover.php           # Main plugin file
├── assets/
│   ├── css/
│   │   ├── shower-makeover.css   # Main frontend styles
│   │   ├── sm-styles.min.css     # Minified alternative
│   │   └── admin.css             # Admin panel styles
│   └── js/
│       └── shower-makeover.js    # Frontend JavaScript
└── templates/
    └── designer-form.php         # Main UI template
```

### Main Class: ShowerMakeoverPro

**Singleton Pattern** with static instance.

**Key Methods:**

| Method | Purpose |
|--------|---------|
| `add_admin_menu()` | Registers admin menu page |
| `register_settings()` | Registers WordPress settings |
| `sanitize_settings($input)` | Sanitizes user input |
| `render_admin_page()` | Renders settings page |
| `enqueue_frontend_assets()` | Loads CSS, JS, fonts |
| `render_shortcode($atts)` | Renders [shower_makeover] |
| `handle_transform()` | AJAX handler for AI generation |
| `handle_test_connection()` | Tests Gemini API |
| `get_layouts()` | Returns layout options array |
| `get_glass_types()` | Returns glass options array |
| `get_hardware_finishes()` | Returns hardware options |
| `get_handle_styles()` | Returns handle options |
| `get_upgrades()` | Returns upgrade options |
| `build_transformation_prompt()` | Creates AI system prompt |

### WordPress Options

**Option Name:** `sm_settings`

```php
[
    'gemini_api_key'  => string,  // API key
    'gemini_model'    => string,  // Model version
    'company_name'    => string,  // Business name
    'company_phone'   => string,  // Contact phone
    'cta_url'         => string,  // Consultation page URL
]
```

### AJAX Endpoints

| Action | Purpose | Auth |
|--------|---------|------|
| `sm_transform_image` | Generate visualization | Public |
| `sm_test_connection` | Test API key | Admin |

### JavaScript Functions

| Function | Purpose |
|----------|---------|
| `init()` | Initialize all handlers |
| `wireSelections()` | Handle form option changes |
| `updateProgress()` | Calculate completion % |
| `wireUpload()` | Manage file upload |
| `wireForm()` | Handle form submission |
| `showResults(data)` | Render results panel |
| `parseDimensions(text)` | Extract dimensions from AI |
| `formatAIResponse(text)` | Clean AI text for display |
| `initComparison()` | Setup before/after slider |

### Google Gemini API

**Endpoint:**
```
POST https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent
```

**Headers:**
```
Content-Type: application/json
x-goog-api-key: {API_KEY}
```

**Request Body:**
```json
{
  "contents": [{
    "parts": [
      { "text": "{detailed_prompt}" },
      { "inline_data": {
          "mime_type": "image/jpeg",
          "data": "{base64_image}"
        }
      }
    ]
  }],
  "generationConfig": {
    "responseModalities": ["TEXT", "IMAGE"],
    "imageConfig": {
      "aspectRatio": "{calculated_ratio}"
    }
  }
}
```

**Timeouts:**
- Test connection: 30 seconds
- Image generation: 120 seconds

### CSS Variables

```css
--sm-primary: #0284c7;
--sm-primary-light: #e0f2fe;
--sm-primary-dark: #0369a1;
--sm-accent: #7c3aed;
--sm-success: #059669;
--sm-warning: #d97706;
--sm-dark: #1e293b;
```

### Responsive Breakpoints

- **1100px:** Container adjustment
- **900px:** Single column stack
- **640px:** Mobile optimization

---

## LLM Context for Development

### Quick Reference for AI Assistants

**Architecture:**
- Single-file plugin with singleton class
- Templates in `/templates/`
- All frontend in one JS file
- Uses Google Gemini API (not OpenAI)

**Key Files to Edit:**

| Task | File(s) |
|------|---------|
| Add new layout | `shower-makeover.php` (get_layouts), `templates/designer-form.php` |
| Add glass type | `shower-makeover.php` (get_glass_types), template |
| Modify AI prompt | `shower-makeover.php` (build_transformation_prompt) |
| Change results display | `assets/js/shower-makeover.js` (showResults) |
| Modify admin settings | `shower-makeover.php` (render_admin_page) |
| Update styling | `assets/css/shower-makeover.css` |

**Data Flow:**
1. User makes selections in 7-step form
2. Uploads bathroom photo
3. Form submitted via AJAX to `sm_transform_image`
4. PHP builds detailed prompt with all selections
5. Image converted to base64, sent to Gemini API
6. API returns text analysis + generated image
7. JS parses response and renders results panel

**Common Modifications:**

*Adding a new design option:*
1. Add to appropriate `get_*()` method in main class
2. Add HTML in `templates/designer-form.php`
3. Handle selection in JS `wireSelections()`
4. Include in prompt building

*Modifying the AI prompt:*
1. Edit `build_transformation_prompt()` method
2. Add instructions for new features
3. Update dimension extraction format if needed
4. Test with various photos

*Changing results layout:*
1. Edit `showResults()` function in JS
2. Update CSS for new elements
3. Modify dimension parsing if format changes

**Security Patterns:**
- Nonces: `sm_nonce` (frontend), `sm_admin_nonce` (admin)
- `check_ajax_referer()` on all AJAX
- `sanitize_text_field()`, `esc_url_raw()` for inputs
- File type validation (JPEG, PNG, WebP only)
- 10MB file size limit

**Testing:**
1. Configure API key in admin
2. Test connection button
3. Place shortcode on test page
4. Complete all 7 steps
5. Upload test bathroom photo
6. Wait for AI processing (15-30 seconds)
7. Verify results display and slider
8. Test on mobile

### Prompt Template for Development Tasks

```
I need to modify the Shower Makeover Pro WordPress plugin.

TASK: [describe what you want to change]

CONTEXT:
- Main plugin: shower-makeover.php (ShowerMakeoverPro singleton)
- Template: templates/designer-form.php
- Frontend JS: assets/js/shower-makeover.js
- CSS: assets/css/shower-makeover.css

Settings stored in wp_options as 'sm_settings':
- gemini_api_key, gemini_model, company_name, company_phone, cta_url

Uses Google Gemini API (NOT OpenAI) for image generation.
Shortcode: [shower_makeover]

AJAX endpoints:
- sm_transform_image (public) - generates visualization
- sm_test_connection (admin) - tests API

Data arrays defined by:
- get_layouts(), get_glass_types(), get_hardware_finishes()
- get_handle_styles(), get_upgrades()

Please provide the specific code changes needed.
```

### AI Prompt Structure

The transformation prompt includes:
1. Role definition (shower glass consultant)
2. Installation type instructions (tub vs floor)
3. Reference objects for measurement (toilet, door, tiles)
4. Mandatory dimension format requirement
5. Installation accuracy rules (hinge placement, door swing)
6. Hardware styling requirements (metallic reflections)

**Required Response Format:**
```
DIMENSIONS: [W]" W x [D]" D x [H]" H
```

JavaScript parses this with regex to extract dimensions for display.

---

## Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| "No API key" error | Configure key in admin settings |
| API test fails | Verify key format, check Google AI Studio |
| Upload fails | Check file size (<10MB), format (JPEG/PNG/WebP) |
| Long processing time | Normal (15-30s), try smaller image |
| No image generated | Improve photo quality, lighting, angle |
| Dimensions not parsed | AI response format varied, check console |

### Debug Tips

- Check browser console for AJAX errors
- Check WordPress debug.log for PHP errors
- Verify API key in Settings → Shower Makeover
- Test API connection from admin panel

---

## Support

**Author:** Elite Results Marketing
**Website:** https://www.eliteresultsmarketing.com
