# HiCaliber Laravel Application

A modern Laravel application with real-time features, property management, and Vue.js frontend.

## Quick Start

### 1. Add to Hosts File

Add these entries to your hosts file (`/etc/hosts` on macOS/Linux or `C:\Windows\System32\drivers\etc\hosts` on Windows):

```
127.0.0.1 example.space
127.0.0.1 vite.example.space  
127.0.0.1 reverb.example.space
```

### 2. Copy .env and start Docker Containers

```bash
cp .env.example .env
```

```bash
docker compose up -d
```

### 3. Run Database Migrations

Once the containers are running, execute the migrations:

```bash
docker compose exec app php artisan migrate
```

### 4. Browser Setup

**Important**: You'll need to allow untrusted certificates for the local domains:

1. Open your browser and navigate to:
   - https://example.space
   - https://vite.example.space

2. When you see certificate warnings, click "Advanced" and "Proceed to example.space (unsafe)" or similar

3. This is required because the local development setup uses self-signed certificates for HTTPS

## Application Access

Create an account

- **Main Application**: https://example.space
