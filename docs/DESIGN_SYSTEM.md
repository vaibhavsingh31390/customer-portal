# MAWAI Customer Portal — Design System

Single source of truth for the UI revamp. All new work should follow these conventions.

## Stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 12 (Blade `<x-component>` supported) |
| CSS | Bootstrap 4 + CSS custom properties |
| JS | jQuery + vanilla JS + Lucide (CDN) |
| Icons | **Lucide** (primary); Feather/LineIcons/icofont legacy during transition |
| Assets | Static files in `public/assets/` |

## Stylesheet load order

1. `public/assets/css/tokens.css`
2. Bootstrap + Plab vendor CSS
3. `public/assets/css/style_child.css` (legacy hooks, being migrated)
4. `public/assets/css/app.css` (**wins** on equal specificity)

**Rule:** Do not use `!important` to override Plab. Flag unmappable rules for scoping instead.

## Color tokens

See `tokens.css` for the full light/dark sets. Key tokens:

| Token | Light | Usage |
|-------|-------|-------|
| `--color-primary` | `#2563EB` | Actions, links, active nav |
| `--color-text` | `#0F172A` | Headings, body |
| `--color-text-muted` | `#475569` | Secondary text |
| `--color-surface` | `#FFFFFF` | Cards, inputs |
| `--color-bg` | `#F8FAFC` | Page background |
| `--color-border` | `#E2E8F0` | Borders |

Semantic: `--color-success`, `--color-error`, `--color-warning`, `--color-info` (+ `-bg` variants).

**60 / 30 / 10:** neutrals dominate, primary supports, accent (`--color-accent`) rarely.

## Dark mode

- Tokens under `[data-theme="dark"]` and `prefers-color-scheme: dark` (when not forced light)
- `theme-init.js` restores `localStorage.theme` before paint
- Toggle: `#theme-toggle` in top nav (`ui.js`)
- All screens must be verified in both themes (WCAG AA)

## Spacing & radius

| Token | Value |
|-------|-------|
| `--space-1` … `--space-6` | 4px – 48px (8px scale) |
| `--radius-sm` | 8px — buttons, inputs |
| `--radius-md` | 12px — cards |
| `--radius-lg` | 16px — panels, auth card |

## Shadows

- `--shadow-resting` — cards at rest
- `--shadow-raised` — hover / elevated

## Typography

- **Font:** Inter (+ system fallback)
- **Scale:** `--font-size-xs` (12px) → `--font-size-3xl` (30px)
- **Body line-height:** 1.5

## Blade components

Located in `resources/views/components/`:

| Component | Usage |
|-----------|-------|
| `<x-page-header>` | Title + subtitle + `actions` slot |
| `<x-card>` | Surface card with optional `title` |
| `<x-button>` | `variant`: primary, secondary, ghost; optional `icon`, `href` |
| `<x-stat-card>` | Dashboard metric tile |
| `<x-badge>` | Status pill (`success`, `error`, `warning`, `pending`, …) |
| `<x-alert>` | Inline alert banner |
| `<x-empty-state>` | Zero-data placeholder |
| `<x-skeleton-table>` | Shimmer loader for tables |
| `<x-icon>` | Lucide icon (`name`, `size`) |

### Examples

```blade
<x-page-header title="Dashboard" subtitle="Overview">
    <x-slot:actions>
        <x-button variant="primary" :href="route('show.create.complaint')" icon="plus">
            Create Complaint
        </x-button>
    </x-slot:actions>
</x-page-header>

<x-stat-card label="Total" :value="42" variant="primary" icon="file-text" />

<x-badge variant="success">Complete</x-badge>
```

## Shared partials

| Partial | Purpose |
|---------|---------|
| `partials/layout/head.blade.php` | `<head>` assets |
| `partials/layout/scripts.blade.php` | JS stack + `showToast()` |
| `include/dataTable.blade.php` | AJAX table shell (`#listForm`, `#form_detail`, `#pagination`) |
| `include/formHeader.blade.php` | Create/edit page header + Save/Cancel |
| `include/sidebarNav.blade.php` | Parameterized sidebar links |

## JS hooks (do not rename)

| Hook | Purpose |
|------|---------|
| `#listForm`, `#form_detail`, `#pagination` | AJAX tables |
| `.loader-wrapper`, `.loader-table` | Table loading |
| `#complaintForm`, `#submitComplaint`, `#backButton` | Complaint forms |
| `#executeComplaintRegister`, `.loader-btn` | Register search |
| `showToast(message, type)` | Notifications |
| `.burger-menu`, `.sidemenu-area`, `.main-content` | Sidebar toggle |

## Motion (`ui.js`)

- NProgress-style top bar on AJAX
- Skeleton shimmer during table load
- Button loading (`is-loading`, "Saving…")
- Page fade-in on `#wrapper`
- `prefers-reduced-motion` respected

## Toasts

`showToast()` reads `--color-success-bg` / `--color-error-bg` and matching text tokens at runtime.

## Error pages

- `resources/views/errors/404.blade.php`
- `resources/views/errors/500.blade.php`

## Changelog

### Phase 0
Tokens, typography, base layout DRY, Apex trim, toast recolor, favicon, test-mode gate.

### Phase 1
Blade components + `dataTable` / `formHeader` partials.

### Phase 2
Top nav, sidebar, footer, Lucide, theme toggle, `ui.js`.

### Phase 3
Login, dashboard, lists, forms, register, error pages revamped.

### Phase 4
Skeleton loaders, NProgress, button loading, card hover, reduced motion.
